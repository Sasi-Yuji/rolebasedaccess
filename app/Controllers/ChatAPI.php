<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Models\User_model;
use CodeIgniter\API\ResponseTrait;

class ChatAPI extends BaseController
{
    use ResponseTrait;

    protected $convModel;
    protected $msgModel;
    protected $userModel;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->convModel = new ConversationModel();
        $this->msgModel = new MessageModel();
        $this->userModel = new User_model();
    }

    public function getConversations()
    {
        $userId = session()->get('id');
        $conversations = $this->convModel->getConversationsForUser($userId);

        foreach ($conversations as &$conv) {
            if ($conv['type'] === 'direct') {
                // Find the other participant to show their name/avatar
                $otherParticipant = $this->db->table('chat_participants cp')
                    ->select('u.name, u.last_seen')
                    ->join('users u', 'u.id = cp.user_id')
                    ->where('cp.conversation_id', $conv['id'])
                    ->where('cp.user_id !=', $userId)
                    ->get()
                    ->getRowArray();
                
                if ($otherParticipant) {
                    $conv['display_name'] = $otherParticipant['name'];
                    $conv['is_online'] = $this->isUserOnline($otherParticipant['last_seen']);
                }
            } else {
                $conv['display_name'] = $conv['name'];
            }

            // Get last message preview
            $lastMsg = $this->db->table('chat_messages')
                ->where('conversation_id', $conv['id'])
                ->where('is_deleted', 0)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
                
            if ($lastMsg) {
                $conv['last_message'] = $lastMsg;
                // Add status to last message
                if ($lastMsg['sender_id'] == $userId) {
                    $otherPart = $this->db->table('chat_participants')
                        ->where('conversation_id', $conv['id'])
                        ->where('user_id !=', $userId)
                        ->get()
                        ->getRowArray();
                    
                    if ($otherPart && $otherPart['last_read_message_id'] >= $lastMsg['id']) {
                        $conv['last_message']['status'] = 'read';
                    } elseif ($lastMsg['delivered_at']) {
                        $conv['last_message']['status'] = 'delivered';
                    } else {
                        $conv['last_message']['status'] = 'sent';
                    }
                }
            }
        }

        return $this->respond($conversations);
    }

    public function createConversation()
    {
        $userId = session()->get('id');
        $type = $this->request->getPost('type');
        $targetUserId = $this->request->getPost('user_id'); // for direct
        $participantIds = $this->request->getPost('participant_ids'); // for group
        $name = $this->request->getPost('name');

        if ($type === 'direct') {
            $convId = $this->convModel->findOrCreateDirect($userId, $targetUserId);
        } else {
            $data = ['name' => $name, 'description' => $this->request->getPost('description')];
            $convId = $this->convModel->createGroup($data, $participantIds, $userId);
        }

        return $this->respond(['id' => $convId]);
    }

    public function getMessages($id)
    {
        $userId = session()->get('id');
        if (!$this->convModel->isParticipant($id, $userId)) {
            return $this->failForbidden();
        }

        $messages = $this->msgModel->getMessages($id);
        
        // Mark these messages as delivered (if they weren't already)
        $this->db->table('chat_messages')
            ->where('conversation_id', $id)
            ->where('sender_id !=', $userId)
            ->where('delivered_at', null)
            ->update(['delivered_at' => date('Y-m-d H:i:s')]);

        // Mark as read
        $this->convModel->markAsRead($id, $userId);
        
        // Update participant's last_read_message_id
        if (!empty($messages)) {
            $lastMsg = end($messages);
            $this->db->table('chat_participants')
                ->where('conversation_id', $id)
                ->where('user_id', $userId)
                ->update(['last_read_message_id' => $lastMsg['id']]);
        }

        // Add status info for the sender to each message
        $otherPart = $this->db->table('chat_participants')
            ->where('conversation_id', $id)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();
            
        foreach ($messages as &$msg) {
            if ($msg['sender_id'] == $userId) {
                if ($otherPart && $otherPart['last_read_message_id'] >= $msg['id']) {
                    $msg['status'] = 'read';
                } elseif ($msg['delivered_at']) {
                    $msg['status'] = 'delivered';
                } else {
                    $msg['status'] = 'sent';
                }
            }
        }

        return $this->respond($messages);
    }

    public function sendMessage($id)
    {
        $userId = session()->get('id');
        if (!$this->convModel->isParticipant($id, $userId)) {
            return $this->failForbidden();
        }

        $data = [
            'conversation_id' => $id,
            'sender_id' => $userId,
            'content' => $this->request->getPost('content'),
            'message_type' => $this->request->getPost('message_type') ?? 'text',
            'reply_to_id' => $this->request->getPost('reply_to_id'),
            'attachments' => $this->request->getPost('attachments') // New
        ];

        $msgId = $this->msgModel->sendMessage($data);
        $message = $this->msgModel->getMessageWithSender($msgId);

        return $this->respond($message);
    }

    public function pollNewMessages($id)
    {
        $userId = session()->get('id');
        if (!$this->convModel->isParticipant($id, $userId)) {
            return $this->failForbidden();
        }

        $lastMsgId = $this->request->getGet('last_id');
        $newMessages = $this->msgModel->getNewMessagesSince($id, $lastMsgId);

        if (!empty($newMessages)) {
            // Mark these new messages as delivered
            $this->db->table('chat_messages')
                ->where('conversation_id', $id)
                ->where('sender_id !=', $userId)
                ->where('delivered_at', null)
                ->update(['delivered_at' => date('Y-m-d H:i:s')]);

            // Mark as read if we got new messages
            $this->convModel->markAsRead($id, $userId);
            
            $lastMsg = end($newMessages);
            $this->db->table('chat_participants')
                ->where('conversation_id', $id)
                ->where('user_id', $userId)
                ->update(['last_read_message_id' => $lastMsg['id']]);
        }
        
        // Status check for existing messages (to see if they were just read)
        $otherPart = $this->db->table('chat_participants')
            ->where('conversation_id', $id)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();
            
        foreach ($newMessages as &$msg) {
            if ($msg['sender_id'] == $userId) {
                if ($otherPart && $otherPart['last_read_message_id'] >= $msg['id']) {
                    $msg['status'] = 'read';
                } elseif ($msg['delivered_at']) {
                    $msg['status'] = 'delivered';
                } else {
                    $msg['status'] = 'sent';
                }
            }
        }

        return $this->respond([
            'new_messages' => $newMessages,
            'other_last_read_id' => $otherPart['last_read_message_id'] ?? 0
        ]);
    }

    public function getConversationAttachments($id)
    {
        $attachments = $this->msgModel->getAttachmentsByConversation($id);
        return $this->respond($attachments);
    }

    public function getConversationStats($id)
    {
        $stats = $this->msgModel->getConversationStats($id);
        return $this->respond($stats);
    }

    public function uploadFile()
    {
        $userId = session()->get('id');
        $files = $this->request->getFileMultiple('files');
        
        if (!$files) {
            return $this->fail('No files uploaded');
        }

        $uploadedFiles = [];
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/chat', $newName);
                
                $uploadedFiles[] = [
                    'file_url' => base_url('uploads/chat/' . $newName),
                    'file_name' => $file->getClientName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ];
            }
        }

        return $this->respond($uploadedFiles);
    }

    public function updateHeartbeat()
    {
        $userId = session()->get('id');
        $this->db->table('users')
            ->where('id', $userId)
            ->update(['last_seen' => date('Y-m-d H:i:s')]);
        
        return $this->respond(['status' => 'success']);
    }

    public function markAsDelivered($id)
    {
        $userId = session()->get('id');
        $this->db->table('chat_messages')
            ->where('conversation_id', $id)
            ->where('sender_id !=', $userId)
            ->where('delivered_at', null)
            ->update(['delivered_at' => date('Y-m-d H:i:s')]);
            
        return $this->respond(['status' => 'delivered']);
    }

    private function isUserOnline($lastSeen)
    {
        if (!$lastSeen) return false;
        $lastSeenTime = strtotime($lastSeen);
        return (time() - $lastSeenTime) < 60; // Online if heartbeat in last 60s
    }

    public function addReaction($id)
    {
        $userId = session()->get('id');
        $emoji = $this->request->getPost('emoji');
        $this->msgModel->addReaction($id, $userId, $emoji);
        return $this->respond(['status' => 'success']);
    }

    public function deleteMessage($id)
    {
        $userId = session()->get('id');
        $message = $this->msgModel->find($id);
        
        if (!$message || $message['sender_id'] != $userId) {
            return $this->failForbidden();
        }

        $this->msgModel->softDelete($id, $userId);
        return $this->respond(['status' => 'success']);
    }
    public function clearConversation($id)
    {
        $userId = session()->get('id');
        $this->db->table('chat_messages')
            ->where('conversation_id', $id)
            ->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $userId]);

        return $this->respond(['status' => 'success']);
    }

    public function deleteConversation($id)
    {
        $userId = session()->get('id');
        $this->db->table('chat_participants')->where('conversation_id', $id)->delete();
        $this->db->table('chat_conversations')->where('id', $id)->delete();
        
        return $this->respond(['status' => 'success']);
    }

    public function getMembers($id)
    {
        $members = $this->db->table('chat_participants p')
            ->select('p.user_id, p.chat_role, u.name, u.role as user_role')
            ->join('users u', 'u.id = p.user_id')
            ->where('p.conversation_id', $id)
            ->get()
            ->getResultArray();
        
        return $this->respond($members);
    }

    public function updateMemberRole($id)
    {
        $userId = session()->get('id');
        $targetUserId = $this->request->getPost('user_id');
        $newRole = $this->request->getPost('role');

        $requester = $this->db->table('chat_participants')
            ->where(['conversation_id' => $id, 'user_id' => $userId])
            ->get()->getRowArray();

        if (!$requester || $requester['chat_role'] !== 'admin') {
            return $this->failForbidden('Only admins can change roles');
        }

        $this->db->table('chat_participants')
            ->where(['conversation_id' => $id, 'user_id' => $targetUserId])
            ->update(['chat_role' => $newRole]);

        return $this->respond(['status' => 'success']);
    }

    public function removeMember($id)
    {
        $userId = session()->get('id');
        $targetUserId = $this->request->getPost('user_id');

        $requester = $this->db->table('chat_participants')
            ->where(['conversation_id' => $id, 'user_id' => $userId])
            ->get()->getRowArray();

        if (!$requester || $requester['chat_role'] !== 'admin') {
            return $this->failForbidden('Only admins can remove members');
        }

        $this->db->table('chat_participants')
            ->where(['conversation_id' => $id, 'user_id' => $targetUserId])
            ->delete();

        return $this->respond(['status' => 'success']);
    }

    public function addMembers($id)
    {
        $userId = session()->get('id');
        $participantIds = $this->request->getPost('participant_ids');

        $requester = $this->db->table('chat_participants')
            ->where(['conversation_id' => $id, 'user_id' => $userId])
            ->get()->getRowArray();

        if (!$requester || $requester['chat_role'] !== 'admin') {
            return $this->failForbidden('Only admins can add members');
        }

        if (!empty($participantIds)) {
            foreach ($participantIds as $pId) {
                $exists = $this->db->table('chat_participants')
                    ->where(['conversation_id' => $id, 'user_id' => $pId])
                    ->countAllResults();
                
                if (!$exists) {
                    $this->db->table('chat_participants')->insert([
                        'conversation_id' => $id,
                        'user_id' => $pId,
                        'chat_role' => 'member'
                    ]);
                }
            }
        }

        return $this->respond(['status' => 'success']);
    }
}
