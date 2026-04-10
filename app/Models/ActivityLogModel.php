<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'action', 'description', 'ip_address'];

    public function logActivity($user_id, $action, $description = '')
    {
        // Verify user exists to satisfy foreign key constraint
        $db = \Config\Database::connect();
        $userExists = $db->table('users')->where('id', $user_id)->countAllResults();
        
        $data = [
            'user_id' => $userExists ? $user_id : null,
            'action'  => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        return $this->db->table($this->table)->insert($data);
    }

    public function getRecentLogs($limit = 10)
    {
        return $this->select('activity_logs.*, users.name as user_name, users.role')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
