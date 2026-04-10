<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\ActivityLogModel;

class SuperAdmin extends BaseController
{
    public function dashboard()
    {
        $userModel = new User_model();
        $logModel = new ActivityLogModel();
        
        $data = [
            'title' => 'Super Admin Dashboard',
            'stats' => [
                'total_users' => $userModel->countAll(),
                'admins'      => $userModel->where('role', 'admin')->countAllResults(),
                'faculty'     => $userModel->where('role', 'faculty')->countAllResults(),
                'students'    => $userModel->where('role', 'student')->countAllResults(),
            ],
            'recentLogs' => $logModel->getRecentLogs(5)
        ];

        return view('layouts/header', $data) . view('superadmin/dashboard', $data) . view('layouts/footer');
    }

    public function manageAdmins()
    {
        $userModel = new User_model();
        $data = [
            'title' => 'Manage Admins',
            'admins' => $userModel->where('role', 'admin')->findAll()
        ];
        return view('layouts/header', $data) . view('superadmin/manage_admins', $data) . view('layouts/footer');
    }

    public function storeAdmin()
    {
        $userModel = new User_model();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'admin'
        ];
        
        $userModel->insert($data);
        
        // Log action
        $logModel = new ActivityLogModel();
        $logModel->logActivity(session()->get('id'), 'CREATE_ADMIN', 'Created a new admin account: ' . $data['email']);
        
        return redirect()->to('superadmin/admins')->with('success', 'Admin created successfully.');
    }

    public function deleteAdmin($id)
    {
        $userModel = new User_model();
        $admin = $userModel->find($id);

        if ($admin && $admin['role'] === 'admin') {
            $userModel->delete($id);
            (new ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_ADMIN', "Removed admin account: {$admin['email']}");
            return redirect()->to('superadmin/admins')->with('success', 'Admin deleted successfully.');
        }

        return redirect()->to('superadmin/admins')->with('error', 'Admin not found or invalid.');
    }

    public function updateAdmin()
    {
        $userModel = new User_model();
        $id = $this->request->getPost('id');
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        // Only update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $userModel->update($id, $data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_ADMIN', "Updated admin account details for: {$data['email']}");

        return redirect()->to('superadmin/admins')->with('success', 'Admin updated successfully.');
    }

    public function systemLogs()
    {
        $logModel = new ActivityLogModel();
        $data = [
            'title' => 'System Activity Logs',
            'logs'  => $logModel->getRecentLogs(50)
        ];
        return view('layouts/header', $data) . view('superadmin/system_logs', $data) . view('layouts/footer');
    }

    public function permissions()
    {
        $permModel = new \App\Models\UserPermissionModel();
        $userModel = new User_model();
        $data = [
            'title' => 'Faculty Permissions',
            'facultyMembers' => $userModel->where('role', 'faculty')->findAll(),
            'matrix' => $permModel->getPermissionsByUser()
        ];
        return view('layouts/header', $data) . view('admin/permissions', $data) . view('layouts/footer');
    }

    public function savePermissions()
    {
        $permModel = new \App\Models\UserPermissionModel();
        $permissions = $this->request->getPost('permissions') ?? [];
        
        $permModel->savePermissions($permissions);
        
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_PERMISSIONS', "Super Admin updated individual Faculty Permissions.");
        
        return redirect()->back()->with('success', 'Faculty permissions updated successfully.');
    }
}
