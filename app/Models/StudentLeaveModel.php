<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentLeaveModel extends Model
{
    protected $table = 'student_leaves';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'student_id',
        'faculty_id',
        'leave_type',
        'start_date',
        'end_date',
        'num_days',
        'reason',
        'status',
        'faculty_remark'
    ];

    public function getLeavesForStudent($studentId)
    {
        return $this->select('student_leaves.*, users.name as faculty_name')
                    ->join('users', 'users.id = student_leaves.faculty_id')
                    ->where('student_id', $studentId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getPendingForFaculty($facultyId)
    {
        return $this->select('student_leaves.*, users.name as student_name')
                    ->join('users', 'users.id = student_leaves.student_id')
                    ->where('student_leaves.faculty_id', $facultyId)
                    ->where('student_leaves.status', 'Pending')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
