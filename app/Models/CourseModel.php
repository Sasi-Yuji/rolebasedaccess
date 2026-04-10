<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_code',
        'course_name',
        'department',
        'semester',
        'credits',
        'status',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|numeric',
        'course_code' => 'required|is_unique[courses.course_code,id,{id}]|min_length[3]|max_length[20]',
        'course_name' => 'required|min_length[3]|max_length[40]',
        'department' => 'required',
        'semester' => 'required|numeric',
        'credits' => 'required|numeric',
        'status' => 'required|in_list[Active,Inactive]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get courses by department
     */
    public function getByDepartment($department)
    {
        return $this->where('department', $department)->findAll();
    }

    /**
     * Get courses by semester
     */
    public function getBySemester($semester)
    {
        return $this->where('semester', $semester)->findAll();
    }

    /**
     * Get stats for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'Active')->countAllResults(),
            'inactive' => $this->where('status', 'Inactive')->countAllResults(),
            'by_dept' => $this->select('department, COUNT(*) as count')->groupBy('department')->findAll(),
            'by_sem' => $this->select('semester, COUNT(*) as count')->groupBy('semester')->orderBy('semester', 'ASC')->findAll(),
        ];
        return $stats;
    }
}
