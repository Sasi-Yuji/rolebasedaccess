<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'conversation_id', 'sender_id', 'message_type', 'content', 
        'reply_to_id', 'is_edited', 'edited_at', 'is_deleted', 
        'deleted_at', 'deleted_by'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // edited_at is used manually

    public function getMessages($convId, $limit = 50, $offset = 0)
    {
        $messages = $this->select('chat_messages.*, users.name as sender_name')
            ->join('users', 'users.id = chat_messages.sender_id')
            ->where('conversation_id', $convId)
            ->where('is_deleted', 0)
            ->orderBy('chat_messages.created_at', 'ASC')
            ->findAll($limit, $offset);

        foreach ($messages as &$msg) {
            $msg['attachments'] = $this->db->table('chat_attachments')
                ->where('message_id', $msg['id'])
                ->get()
                ->getResultArray();
        }

        return $messages;
    }

    public function getNewMessagesSince($convId, $lastMsgId)
    {
        $messages = $this->select('chat_messages.*, users.name as sender_name')
            ->join('users', 'users.id = chat_messages.sender_id')
            ->where('conversation_id', $convId)
            ->where('chat_messages.id >', $lastMsgId)
            ->where('is_deleted', 0)
            ->orderBy('chat_messages.created_at', 'ASC')
            ->findAll();

        foreach ($messages as &$msg) {
            $msg['attachments'] = $this->db->table('chat_attachments')
                ->where('message_id', $msg['id'])
                ->get()
                ->getResultArray();
        }

        return $messages;
    }

    public function sendMessage($data)
    {
        $this->db->transStart();

        $msgId = $this->insert([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'message_type' => $data['message_type'] ?? 'text',
            'content' => $data['content'] ?? null,
            'reply_to_id' => $data['reply_to_id'] ?? null
        ]);

        if (!empty($data['attachments'])) {
            $attachments = [];
            foreach ($data['attachments'] as $att) {
                $attachments[] = [
                    'message_id' => $msgId,
                    'file_url' => $att['file_url'],
                    'file_name' => $att['file_name'],
                    'file_type' => $att['file_type'],
                    'file_size' => $att['file_size']
                ];
            }
            $this->db->table('chat_attachments')->insertBatch($attachments);
        }

        // Update conversation last message time
        $convModel = new \App\Models\ConversationModel();
        $convModel->updateLastMessage($data['conversation_id']);
        $convModel->incrementUnreadCount($data['conversation_id'], $data['sender_id']);

        $this->db->transComplete();

        return $msgId;
    }

    public function getMessageWithSender($msgId)
    {
        $msg = $this->select('chat_messages.*, users.name as sender_name')
            ->join('users', 'users.id = chat_messages.sender_id')
            ->where('chat_messages.id', $msgId)
            ->first();

        if ($msg) {
            $msg['attachments'] = $this->db->table('chat_attachments')
                ->where('message_id', $msgId)
                ->get()
                ->getResultArray();
        }

        return $msg;
    }

    public function getReactions($msgId)
    {
        return $this->db->table('chat_reactions r')
            ->select('r.*, u.name as user_name')
            ->join('users u', 'u.id = r.user_id')
            ->where('r.message_id', $msgId)
            ->get()
            ->getResultArray();
    }

    public function addReaction($msgId, $userId, $emoji)
    {
        // Toggle behavior: if already exists, remove it. Otherwise add.
        $existing = $this->db->table('chat_reactions')
            ->where('message_id', $msgId)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->get()
            ->getRowArray();

        if ($existing) {
            return $this->db->table('chat_reactions')
                ->where('id', $existing['id'])
                ->delete();
        } else {
            return $this->db->table('chat_reactions')->insert([
                'message_id' => $msgId,
                'user_id' => $userId,
                'emoji' => $emoji
            ]);
        }
    }

    public function softDelete($msgId, $userId)
    {
        return $this->update($msgId, [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $userId
        ]);
    }

    public function getAttachmentsByConversation($convId)
    {
        return $this->db->table('chat_attachments')
            ->select('chat_attachments.*, chat_messages.sender_id, chat_messages.created_at as sent_at')
            ->join('chat_messages', 'chat_messages.id = chat_attachments.message_id')
            ->where('chat_messages.conversation_id', $convId)
            ->orderBy('chat_attachments.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getConversationStats($convId)
    {
        $totalMessages = $this->where('conversation_id', $convId)->countAllResults();
        $totalMedia = $this->db->table('chat_attachments')
            ->join('chat_messages', 'chat_messages.id = chat_attachments.message_id')
            ->where('chat_messages.conversation_id', $convId)
            ->countAllResults();
            
        return [
            'total_messages' => $totalMessages,
            'total_media' => $totalMedia
        ];
    }
}
