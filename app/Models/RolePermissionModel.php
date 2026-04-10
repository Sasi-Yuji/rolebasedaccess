<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role', 'permission'];

    public function getPermissionsByRole()
    {
        $records = $this->findAll();
        $matrix = [
            'superadmin' => [],
            'admin'      => [],
            'faculty'    => [],
            'student'    => []
        ];
        foreach ($records as $record) {
            $matrix[$record['role']][] = $record['permission'];
        }
        return $matrix;
    }

    public function savePermissions($matrixData)
    {
        // $matrixData should be like ['student' => ['view_marks', 'view_profile'], 'faculty' => [...]]
        // To be safe, delete everything and reinsert
        $this->db->table($this->table)->truncate();
        
        $insertData = [];
        foreach ($matrixData as $role => $permissions) {
            if (is_array($permissions)) {
                foreach ($permissions as $perm) {
                    $insertData[] = [
                        'role' => $role,
                        'permission' => $perm
                    ];
                }
            }
        }
        
        if (!empty($insertData)) {
            $this->insertBatch($insertData);
        }
    }
}
