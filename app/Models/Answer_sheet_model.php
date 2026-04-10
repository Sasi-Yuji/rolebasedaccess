<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class Answer_sheet_model extends Model
{
    protected $table = 'answer_sheets';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['student_id', 'subject_id', 'image_path', 'page_number', 'pdf_path', 'status'];
 
    public function getStudentSubmission($student_id, $subject_id)
    {
        return $this->where('student_id', $student_id)
                    ->where('subject_id', $subject_id)
                    ->orderBy('page_number', 'ASC')
                    ->findAll();
    }
}
