<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\Subject_model;
use App\Models\ActivityLogModel;

class Admin extends BaseController
{
    public function dashboard()
    {
        $userModel = new User_model();
        $subModel = new Subject_model();
        
        $data = [
            'title' => 'Admin Dashboard',
            'stats' => [
                'students' => $userModel->where('role', 'student')->countAllResults(),
                'faculty'  => $userModel->where('role', 'faculty')->countAllResults(),
                'hods'     => $userModel->where('role', 'hod')->countAllResults(),
                'subjects' => $subModel->countAll(),
            ],
            'recentStudents' => $userModel->where('role', 'student')->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'totalAssignments' => $subModel->db->table('faculty_subjects')->countAllResults(),
            'recentLeaves' => (new \App\Models\LeaveRequestModel())->select('leave_requests.*, users.name as faculty_name')
                ->join('users', 'users.id = leave_requests.faculty_id')
                ->orderBy('leave_requests.created_at', 'DESC')
                ->limit(5)->findAll()
        ];
        return view('layouts/header', $data) . view('admin/dashboard', $data) . view('layouts/footer');
    }

    public function manageStudents()
    {
        $userModel = new User_model();
        $data = [
            'title' => 'Manage Students',
            'students' => $userModel->where('role', 'student')->findAll()
        ];
        return view('layouts/header', $data) . view('admin/students', $data) . view('layouts/footer');
    }

    public function storeStudent()
    {
        $userModel = new User_model();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'student'
        ];
        $userModel->insert($data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'CREATE_STUDENT', "Created student account for {$data['email']}");
        return redirect()->back()->with('success', 'Student added successfully.');
    }

    public function manageFaculty()
    {
        $userModel = new User_model();
        $data = [
            'title' => 'Manage Faculty',
            'faculty' => $userModel->where('role', 'faculty')->findAll()
        ];
        return view('layouts/header', $data) . view('admin/faculty', $data) . view('layouts/footer');
    }

    public function storeFaculty()
    {
        $userModel = new User_model();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'faculty',
            'department' => $this->request->getPost('department')
        ];
        $userModel->insert($data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'CREATE_FACULTY', "Created faculty account for {$data['email']} ({$data['department']})");
        return redirect()->back()->with('success', 'Faculty added successfully.');
    }

    public function updateStudent()
    {
        $userModel = new User_model();
        $id = $this->request->getPost('id');
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $userModel->update($id, $data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_STUDENT', "Updated student: {$data['email']}");
        return redirect()->back()->with('success', 'Student updated successfully.');
    }

    public function deleteStudent($id)
    {
        $userModel = new User_model();
        $student = $userModel->find($id);

        if ($student && $student['role'] === 'student') {
            $userModel->delete($id);
            (new ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_STUDENT', "Removed student: {$student['email']}");
            return redirect()->back()->with('success', 'Student deleted successfully.');
        }
        return redirect()->back()->with('error', 'Student not found.');
    }

    public function updateFaculty()
    {
        $userModel = new User_model();
        $id = $this->request->getPost('id');
        $data = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'department' => $this->request->getPost('department')
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $userModel->update($id, $data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_FACULTY', "Updated faculty: {$data['email']}");
        return redirect()->back()->with('success', 'Faculty updated successfully.');
    }

    public function deleteFaculty($id)
    {
        $userModel = new User_model();
        $faculty = $userModel->find($id);

        if ($faculty && $faculty['role'] === 'faculty') {
            $userModel->delete($id);
            (new ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_FACULTY', "Removed faculty: {$faculty['email']}");
            return redirect()->back()->with('success', 'Faculty deleted successfully.');
        }
        return redirect()->back()->with('error', 'Faculty not found.');
    }

    public function manageHods()
    {
        $userModel = new User_model();
        $data = [
            'title' => 'Manage HODs',
            'hods' => $userModel->where('role', 'hod')->findAll()
        ];
        return view('layouts/header', $data) . view('admin/hods', $data) . view('layouts/footer');
    }

    public function storeHod()
    {
        $userModel = new User_model();
        $department = $this->request->getPost('department');
        
        // Check if HOD already exists for this department
        $existingHod = $userModel->where('role', 'hod')->where('department', $department)->first();
        if ($existingHod) {
            return redirect()->back()->with('error', "An HOD already exists for the {$department} department.");
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'hod',
            'department' => $department
        ];
        $userModel->insert($data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'CREATE_HOD', "Created HOD account for {$data['email']} ({$data['department']})");
        return redirect()->back()->with('success', 'HOD added successfully.');
    }

    public function updateHod()
    {
        $userModel = new User_model();
        $id = $this->request->getPost('id');
        $department = $this->request->getPost('department');

        // Check if another HOD already exists for this department
        $existingHod = $userModel->where('role', 'hod')
                                 ->where('department', $department)
                                 ->where('id !=', $id)
                                 ->first();
        if ($existingHod) {
            return redirect()->back()->with('error', "An HOD already exists for the {$department} department.");
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'department' => $department
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $userModel->update($id, $data);
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_HOD', "Updated HOD: {$data['email']}");
        return redirect()->back()->with('success', 'HOD updated successfully.');
    }

    public function deleteHod($id)
    {
        $userModel = new User_model();
        $hod = $userModel->find($id);

        if ($hod && $hod['role'] === 'hod') {
            $userModel->delete($id);
            (new ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_HOD', "Removed HOD: {$hod['email']}");
            return redirect()->back()->with('success', 'HOD deleted successfully.');
        }
        return redirect()->back()->with('error', 'HOD not found.');
    }

    public function toggleFacultyAccess($id, $status)
    {
        $userModel = new User_model();
        $userModel->update($id, ['can_add_students' => $status]);
        
        $msg = $status ? 'Access granted to add students.' : 'Access revoked for adding students.';
        (new ActivityLogModel())->logActivity(session()->get('id'), 'TOGGLE_FACULTY_PERMISSION', "Set faculty ID {$id} student access to {$status}");

        return $this->response->setJSON([
            'success' => true,
            'message' => $msg
        ]);
    }

    public function manageSubjects()
    {
        $subModel = new Subject_model();
        $userModel = new User_model();
        $data = [
            'title' => 'Manage Subjects',
            'subjects' => $subModel->findAll(),
            'facultyMembers' => $userModel->where('role', 'faculty')->findAll()
        ];
        return view('layouts/header', $data) . view('admin/subjects', $data) . view('layouts/footer');
    }

    public function storeSubject()
    {
        $subModel = new Subject_model();
        $subModel->insert(['subject_name' => $this->request->getPost('subject_name')]);
        return redirect()->back()->with('success', 'Subject created successfully.');
    }

    public function assignSubject()
    {
        $subModel = new Subject_model();
        $faculty_id = $this->request->getPost('faculty_id');
        $subject_id = $this->request->getPost('subject_id');
        $subModel->assignToFaculty($faculty_id, $subject_id);
        
        return redirect()->back()->with('success', 'Subject assigned to faculty successfully.');
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
        
        (new ActivityLogModel())->logActivity(session()->get('id'), 'UPDATE_PERMISSIONS', "Updated individual Faculty Permissions.");
        
        return redirect()->back()->with('success', 'Faculty permissions updated successfully.');
    }
}
