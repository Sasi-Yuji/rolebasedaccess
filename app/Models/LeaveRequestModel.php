<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $table = 'leave_requests';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'faculty_id',
        'hod_id',
        'leave_type',
        'start_date',
        'end_date',
        'num_days',
        'reason',
        'status',
        'hod_remark'
    ];

    public function getMonthlyUsage($facultyId, $month, $year)
    {
        return $this->where('faculty_id', $facultyId)
                    ->where('MONTH(start_date)', $month)
                    ->where('YEAR(start_date)', $year)
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->where('leave_type', 'Casual')
                    ->countAllResults();
    }

    public function getTotalYearlyUsage($facultyId, $year)
    {
        return $this->where('faculty_id', $facultyId)
                    ->where('YEAR(start_date)', $year)
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->where('leave_type', 'Casual')
                    ->countAllResults();
    }

    public function getLeavesForFaculty($facultyId)
    {
        return $this->select('leave_requests.*, users.name as hod_name')
                    ->join('users', 'users.id = leave_requests.hod_id')
                    ->where('faculty_id', $facultyId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getLeavesForHod($hodId)
    {
        return $this->select('leave_requests.*, users.name as faculty_name, users.department')
                    ->join('users', 'users.id = leave_requests.faculty_id')
                    ->where('hod_id', $hodId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
