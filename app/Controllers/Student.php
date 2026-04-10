<?php

namespace App\Controllers;

use App\Models\Marks_model;
use App\Models\User_model;

class Student extends BaseController
{
    public function dashboard()
    {
        $marksModel = new Marks_model();
        $student_id = session()->get('id');
        
        $data = [
            'title' => 'Student Dashboard',
            'marks' => $marksModel->getStudentMarks($student_id)
        ];
        return view('layouts/header', $data) . view('student/dashboard', $data) . view('layouts/footer');
    }

    public function viewMarks()
    {
        $marksModel = new Marks_model();
        $student_id = session()->get('id');
        
        $data = [
            'title' => 'Academic Performance',
            'marks' => $marksModel->getStudentMarks($student_id)
        ];
        return view('layouts/header', $data) . view('student/marks', $data) . view('layouts/footer');
    }

    public function profile()
    {
        $userModel = new User_model();
        $reqModel = new \App\Models\ProfileRequestModel();
        
        $studentId = session()->get('id');
        $user = $userModel->find($studentId);

        $data = [
            'title' => 'Student Profile',
            'user' => $user,
            'facultyList' => $userModel->where('role', 'faculty')->findAll(),
            'pendingRequest' => $reqModel->where('student_id', $studentId)->where('status', 'pending')->first(),
            'lastRejected' => $reqModel->where('student_id', $studentId)->where('status', 'rejected')->orderBy('created_at', 'DESC')->first()
        ];
        return view('layouts/header', $data) . view('student/profile', $data) . view('layouts/footer');
    }

    public function submitEditRequest()
    {
        $reqModel = new \App\Models\ProfileRequestModel();
        $studentId = session()->get('id');

        // Check if already has a pending request
        if ($reqModel->where('student_id', $studentId)->where('status', 'pending')->first()) {
            return redirect()->back()->with('error', 'You already have a pending request.');
        }

        $data = [
            'student_id'   => $studentId,
            'faculty_id'   => $this->request->getPost('faculty_id'),
            'request_type' => $this->request->getPost('request_type') ?: 'profile_update',
            'reason'       => $this->request->getPost('reason'),
            'status'       => 'pending'
        ];

        $reqModel->insert($data);
        (new \App\Models\ActivityLogModel())->logActivity($studentId, "{$data['request_type']}_REQUEST", "Submitted request to faculty ID: {$data['faculty_id']}");

        return redirect()->back()->with('success', 'Your request has been submitted to the faculty for approval.');
    }

    public function updateProfile()
    {
        $userModel = new User_model();
        $studentId = session()->get('id');
        $user = $userModel->find($studentId);

        if (!$user['can_edit_profile']) {
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission to edit your profile.');
        }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        $userModel->update($studentId, $updateData);
        
        // Reset the one-time flag
        $userModel->update($studentId, ['can_edit_profile' => 0]);

        (new \App\Models\ActivityLogModel())->logActivity($studentId, 'PROFILE_UPDATED', "User updated their own profile.");

        return redirect()->to(base_url('student/profile'))->with('success', 'Profile updated successfully. Your one-time edit permission has been used.');
    }

    public function updatePassword()
    {
        $userModel = new User_model();
        $studentId = session()->get('id');
        $user = $userModel->find($studentId);

        if (!$user['can_change_pwd']) {
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission to change your password.');
        }

        $newPwd = $this->request->getPost('password');
        $confirmPwd = $this->request->getPost('confirm_password');

        if ($newPwd !== $confirmPwd) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userModel->update($studentId, ['password' => $newPwd, 'can_change_pwd' => 0]);

        (new \App\Models\ActivityLogModel())->logActivity($studentId, 'PASSWORD_UPDATED', "User updated their password after faculty approval.");

        return redirect()->to(base_url('student/profile'))->with('success', 'Password updated successfully. Your one-time permission has been used.');
    }

    public function leave()
    {
        $leaveModel = new \App\Models\StudentLeaveModel();
        $userModel = new User_model();
        $studentId = session()->get('id');

        $data = [
            'title' => 'Leave Application',
            'leaves' => $leaveModel->getLeavesForStudent($studentId),
            'facultyList' => $userModel->where('role', 'faculty')->findAll()
        ];

        return view('layouts/header', $data) . view('student/leave', $data) . view('layouts/footer');
    }

    public function storeLeave()
    {
        $leaveModel = new \App\Models\StudentLeaveModel();
        $studentId = session()->get('id');

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);
        $days  = $start->diff($end)->days + 1;

        $data = [
            'student_id' => $studentId,
            'faculty_id' => $this->request->getPost('faculty_id'),
            'leave_type' => $this->request->getPost('leave_type'),
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'num_days'   => $days,
            'reason'     => $this->request->getPost('reason'),
            'status'     => 'Pending'
        ];

        $leaveModel->insert($data);
        return redirect()->back()->with('success', 'Leave applied successfully.');
    }

    public function deleteLeave($id)
    {
        $leaveModel = new \App\Models\StudentLeaveModel();
        $leave = $leaveModel->find($id);

        if ($leave && $leave['student_id'] == session()->get('id') && $leave['status'] == 'Pending') {
            $leaveModel->delete($id);
            return redirect()->back()->with('success', 'Leave cancelled.');
        }
        return redirect()->back()->with('error', 'Unable to cancel.');
    }

    public function viewAnswerUpload()
    {
        $subModel = new \App\Models\Subject_model();
        $ansModel = new \App\Models\Answer_sheet_model();
        $studentId = session()->get('id');
        
        $pastSubmissions = $ansModel->select('answer_sheets.subject_id, answer_sheets.created_at, answer_sheets.status, subjects.subject_name, count(answer_sheets.id) as page_count')
                                    ->join('subjects', 'subjects.id = answer_sheets.subject_id')
                                    ->where('answer_sheets.student_id', $studentId)
                                    ->groupBy('answer_sheets.subject_id, answer_sheets.created_at, answer_sheets.status, subjects.subject_name')
                                    ->orderBy('answer_sheets.created_at', 'DESC')
                                    ->findAll();

        $data = [
            'title' => 'Upload Answer Sheet',
            'subjects' => $subModel->findAll(), // Ideally only subjects the student is registered for
            'submissions' => $pastSubmissions
        ];
        return view('layouts/header', $data) . view('student/upload_answers', $data) . view('layouts/footer');
    }

    public function storeAnswerSheet()
    {
        $ansModel = new \App\Models\Answer_sheet_model();
        $studentId = session()->get('id');
        $subjectId = $this->request->getPost('subject_id');
        $images = $this->request->getPost('cropped_images'); // Array of base64 strings

        if (empty($images)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No images provided.']);
        }

        $uploadPath = 'uploads/answer_sheets/';
        if (!is_dir(FCPATH . $uploadPath)) mkdir(FCPATH . $uploadPath, 0777, true);

        foreach ($images as $index => $base64) {
            $data = explode(',', $base64);
            $imgData = base64_decode($data[1]);
            $fileName = "ans_{$studentId}_{$subjectId}_{$index}_" . time() . ".jpg";
            file_put_contents(FCPATH . $uploadPath . $fileName, $imgData);

            $ansModel->insert([
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'image_path' => $uploadPath . $fileName,
                'page_number' => $index + 1,
                'status' => 'Submitted'
            ]);
        }

        return $this->response->setJSON(['success' => true, 'redirect' => base_url('student/dashboard')]);
    }

    public function viewMarksheetUpload()
    {
        $docModel = new \App\Models\Student_document_model();
        $studentId = session()->get('id');
        $documents = $docModel->where('student_id', $studentId)->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Upload Previous Marksheet',
            'documents' => $documents
        ];
        return view('layouts/header', $data) . view('student/upload_marksheet', $data) . view('layouts/footer');
    }

    public function storeMarksheet()
    {
        $docModel = new \App\Models\Student_document_model();
        $studentId = session()->get('id');
        $docName = $this->request->getPost('doc_name');
        $base64 = $this->request->getPost('cropped_image');

        if (!$base64) {
            return $this->response->setJSON(['success' => false, 'message' => 'No image provided.']);
        }

        $uploadPath = 'uploads/student_docs/';
        if (!is_dir(FCPATH . $uploadPath)) mkdir(FCPATH . $uploadPath, 0777, true);

        $data = explode(',', $base64);
        $imgData = base64_decode($data[1]);
        $fileName = "doc_{$studentId}_" . time() . ".jpg";
        file_put_contents(FCPATH . $uploadPath . $fileName, $imgData);

        $docModel->insert([
            'student_id' => $studentId,
            'doc_name' => $docName,
            'image_path' => $uploadPath . $fileName,
            'status' => 'Pending'
        ]);

        return $this->response->setJSON(['success' => true, 'redirect' => base_url('student/profile')]);
    }
}
