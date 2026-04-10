<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPermissionModel extends Model
{
    protected $table = 'user_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'permission'];

    public function getPermissionsByUser()
    {
        $records = $this->findAll();
        $matrix = [];
        foreach ($records as $record) {
            $matrix[$record['user_id']][] = $record['permission'];
        }
        return $matrix;
    }

    public function savePermissions($matrixData)
    {
        // $matrixData should be like [user_id => ['add_student', 'edit_student'], ...]
        $this->db->table($this->table)->truncate();
        
        $insertData = [];
        if (!empty($matrixData) && is_array($matrixData)) {
            foreach ($matrixData as $userId => $permissions) {
                if (is_array($permissions)) {
                    foreach ($permissions as $perm) {
                        $insertData[] = [
                            'user_id' => $userId,
                            'permission' => $perm
                        ];
                    }
                }
            }
        }
        
        if (!empty($insertData)) {
            $this->insertBatch($insertData);
        }
    }

    public function hasPermission($userId, $permission)
    {
        return $this->where('user_id', $userId)->where('permission', $permission)->countAllResults() > 0;
    }
}
