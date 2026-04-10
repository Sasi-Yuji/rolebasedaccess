<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatNotificationModel extends Model
{
    // Note: We'll use a simplified notifications approach. 
    // In a real app, this might be a separate table. 
    // For this implementation, we'll focus on the core chat logic.
    // If we need a table, we can add it later. 
    // For now, we'll just track unread_count in chat_participants.
    
    public function getUnreadCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('chat_participants')
            ->where('user_id', $userId)
            ->selectSum('unread_count')
            ->get()
            ->getRowArray()['unread_count'] ?? 0;
    }
}
