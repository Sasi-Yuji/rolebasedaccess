<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class Student_document_model extends Model
{
    protected $table = 'student_documents';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['student_id', 'doc_name', 'image_path', 'status'];
 
    public function getStudentDocs($student_id)
    {
        return $this->where('student_id', $student_id)->findAll();
    }
}
