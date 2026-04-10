<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'content', 'author_id', 'target_role', 'created_at', 'updated_at'];

    public function getActiveAnnouncements($targetRole = null)
    {
        $this->select('announcements.*, users.name as author_name, users.role as author_role_name');
        $this->join('users', 'users.id = announcements.author_id', 'left');
        
        if ($targetRole) {
            $this->groupStart()
                 ->where('target_role', 'all')
                 ->orWhere('target_role', $targetRole)
                 ->groupEnd();
        }
        
        return $this->orderBy('created_at', 'DESC')->findAll(10); // Fetch top 10 latest
    }
}
