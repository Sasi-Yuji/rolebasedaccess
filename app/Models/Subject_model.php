<?php

namespace App\Models;

use CodeIgniter\Model;

class Subject_model extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['subject_name'];

    public function getAssignedSubjects($faculty_id)
    {
        return $this->db->table('faculty_subjects')
            ->select('subjects.*')
            ->join('subjects', 'subjects.id = faculty_subjects.subject_id')
            ->where('faculty_id', $faculty_id)
            ->get()->getResultArray();
    }

    public function assignToFaculty($faculty_id, $subject_id)
    {
        return $this->db->table('faculty_subjects')->insert([
            'faculty_id' => $faculty_id,
            'subject_id' => $subject_id
        ]);
    }
}
