<?php

namespace App\Models;

use CodeIgniter\Model;

class Marks_model extends Model
{
    protected $table = 'marks';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['student_id', 'subject_id', 'marks', 'updated_by'];

    public function getStudentMarks($student_id)
    {
        return $this->select('marks.*, subjects.subject_name, users.name as teacher_name')
            ->join('subjects', 'subjects.id = marks.subject_id')
            ->join('users', 'users.id = marks.updated_by')
            ->where('student_id', $student_id)
            ->findAll();
    }

    public function getMarksBySubject($subject_id)
    {
        return $this->select('marks.*, users.name as student_name, users.email')
            ->join('users', 'users.id = marks.student_id')
            ->where('subject_id', $subject_id)
            ->orderBy('marks.updated_at', 'DESC')
            ->findAll();
    }
}
