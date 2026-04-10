<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileRequestModel extends Model
{
    protected $table = 'profile_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['student_id', 'faculty_id', 'request_type', 'reason', 'status', 'rejection_reason'];
    protected $useTimestamps = true;

    public function getPendingForFaculty($facultyId)
    {
        return $this->select('profile_requests.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = profile_requests.student_id')
                    ->where('faculty_id', $facultyId)
                    ->where('status', 'pending')
                    ->orderBy('profile_requests.updated_at', 'DESC')
                    ->findAll();
    }
}
