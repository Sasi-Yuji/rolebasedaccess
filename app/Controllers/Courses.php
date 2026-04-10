<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\ActivityLogModel;

class Courses extends BaseController
{
    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
    }

    /**
     * List all courses
     */
    public function index()
    {
        $role = session()->get('role');
        $department = session()->get('department');

        if ($role === 'faculty' || $role === 'hod') {
            $courses = $this->courseModel->getByDepartment($department);
            $title = 'Departmental Courses';
        }
        elseif ($role === 'student') {
            // For students, show all active courses globally or filter by semester if we had it
            // Assuming for now they see all active courses in the catalogue
            $courses = $this->courseModel->where('status', 'Active')->findAll();
            $title = 'Course Catalogue';
        }

        // Context for Quick Actions if faculty
        $assignedSubjectIds = [];
        if ($role === 'faculty') {
            $subModel = new \App\Models\Subject_model();
            $assigned = $subModel->getAssignedSubjects(session()->get('id'));
            // Map names to IDs for easier lookup in the view
            foreach($assigned as $s) {
                $assignedSubjectIds[strtolower($s['subject_name'])] = $s['id'];
            }
        }
        else {
            $courses = $this->courseModel->findAll();
            $title = 'Manage Courses';
        }

        $data = [
            'title' => $title,
            'courses' => $courses,
            'role' => $role,
            'assignedSubjectIds' => $assignedSubjectIds
        ];

        $viewPath = ($role === 'admin') ? 'admin/courses/index' : 'common/courses_view';

        return view('layouts/header', $data)
            . view($viewPath, $data)
            . view('layouts/footer');
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Course',
        ];

        return view('layouts/header', $data)
            . view('admin/courses/create', $data)
            . view('layouts/footer');
    }

    /**
     * Validate + save new course
     */
    public function store()
    {
        $rules = $this->courseModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'department' => $this->request->getPost('department'),
            'semester' => $this->request->getPost('semester'),
            'credits' => $this->request->getPost('credits'),
            'status' => $this->request->getPost('status'),
        ];

        if ($this->courseModel->insert($data)) {
            (new ActivityLogModel())->logActivity(
                session()->get('id'),
                'CREATE_COURSE',
                "Created course: {$data['course_code']} - {$data['course_name']}"
            );
            return redirect()->to('/admin/courses')->with('success', 'Course added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add course.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $course = $this->courseModel->find($id);

        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
        }

        $data = [
            'title' => 'Edit Course',
            'course' => $course,
        ];

        return view('layouts/header', $data)
            . view('admin/courses/edit', $data)
            . view('layouts/footer');
    }

    /**
     * Validate + update course
     */
    public function update($id = null)
    {
        if (!$id || !$course = $this->courseModel->find($id)) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
        }

        $rules = $this->courseModel->getValidationRules();
        // Ignore current record ID in unique check
        $rules['course_code'] = str_replace('{id}', $id, $rules['course_code']);

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'department' => $this->request->getPost('department'),
            'semester' => $this->request->getPost('semester'),
            'credits' => $this->request->getPost('credits'),
            'status' => $this->request->getPost('status'),
        ];

        // Use the query builder directly to bypass any model preprocessing that strips fields
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->courseModel->builder()->where('id', $id)->update($data)) {
            (new ActivityLogModel())->logActivity(
                session()->get('id'),
                'UPDATE_COURSE',
                "Updated course ID: {$id}"
            );
            return redirect()->to('/admin/courses')->with('success', 'Course updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update course.');
    }

    /**
     * Soft delete course
     */
    public function delete($id)
    {
        $course = $this->courseModel->find($id);

        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
        }

        if ($this->courseModel->delete($id)) {
            (new ActivityLogModel())->logActivity(
                session()->get('id'),
                'DELETE_COURSE',
                "Soft deleted course: {$course['course_code']}"
            );
            return redirect()->to('/admin/courses')->with('success', 'Course deleted successfully (soft delete).');
        }

        return redirect()->to('/admin/courses')->with('error', 'Failed to delete course.');
    }

    /**
     * Course Analytics Dashboard
     */
    public function dashboard()
    {
        $data = [
            'title' => 'Course Analytics',
            'stats' => $this->courseModel->getStats(),
        ];

        return view('layouts/header', $data)
            . view('admin/courses/dashboard', $data)
            . view('layouts/footer');
    }
}
