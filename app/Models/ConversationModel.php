<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table = 'chat_conversations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['type', 'name', 'description', 'avatar', 'created_by', 'is_active', 'last_message_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // last_message_at is used manually

    public function getConversationsForUser($userId)
    {
        return $this->db->table('chat_participants cp')
            ->select('cc.*, cp.chat_role, cp.unread_count, cp.is_muted')
            ->join('chat_conversations cc', 'cc.id = cp.conversation_id')
            ->where('cp.user_id', $userId)
            ->where('cc.is_active', 1)
            ->orderBy('cc.last_message_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function findOrCreateDirect($user1, $user2)
    {
        // Check if a direct conversation already exists between these two
        $existing = $this->db->table('chat_participants cp1')
            ->select('cp1.conversation_id as id')
            ->join('chat_participants cp2', 'cp1.conversation_id = cp2.conversation_id')
            ->join('chat_conversations cc', 'cc.id = cp1.conversation_id')
            ->where('cc.type', 'direct')
            ->where('cp1.user_id', $user1)
            ->where('cp2.user_id', $user2)
            ->get()
            ->getRowArray();

        if ($existing) {
            return $existing['id'];
        }

        // Create new direct conversation
        $this->db->transStart();
        
        $convId = $this->insert([
            'type' => 'direct',
            'created_by' => $user1,
            'last_message_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->table('chat_participants')->insertBatch([
            ['conversation_id' => $convId, 'user_id' => $user1, 'chat_role' => 'admin'],
            ['conversation_id' => $convId, 'user_id' => $user2, 'chat_role' => 'admin']
        ]);

        $this->db->transComplete();

        return $convId;
    }

    public function createGroup($data, $participantIds, $creatorId)
    {
        $this->db->transStart();

        $convId = $this->insert([
            'type' => 'group',
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'avatar' => $data['avatar'] ?? null,
            'created_by' => $creatorId,
            'last_message_at' => date('Y-m-d H:i:s')
        ]);

        $participants = [];
        // Add creator as admin
        $participants[] = [
            'conversation_id' => $convId,
            'user_id' => $creatorId,
            'chat_role' => 'admin'
        ];

        // Add others as members
        foreach ($participantIds as $pid) {
            if ($pid != $creatorId) {
                $participants[] = [
                    'conversation_id' => $convId,
                    'user_id' => $pid,
                    'chat_role' => 'member'
                ];
            }
        }

        $this->db->table('chat_participants')->insertBatch($participants);

        $this->db->transComplete();

        return $convId;
    }

    public function isParticipant($convId, $userId)
    {
        return $this->db->table('chat_participants')
            ->where('conversation_id', $convId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }

    public function isAdmin($convId, $userId)
    {
        return $this->db->table('chat_participants')
            ->where('conversation_id', $convId)
            ->where('user_id', $userId)
            ->where('chat_role', 'admin')
            ->countAllResults() > 0;
    }

    public function incrementUnreadCount($convId, $excludeUserId)
    {
        return $this->db->table('chat_participants')
            ->where('conversation_id', $convId)
            ->where('user_id !=', $excludeUserId)
            ->set('unread_count', 'unread_count + 1', false)
            ->update();
    }

    public function markAsRead($convId, $userId)
    {
        return $this->db->table('chat_participants')
            ->where('conversation_id', $convId)
            ->where('user_id', $userId)
            ->update(['unread_count' => 0]);
    }

    public function updateLastMessage($convId)
    {
        return $this->update($convId, ['last_message_at' => date('Y-m-d H:i:s')]);
    }
}
