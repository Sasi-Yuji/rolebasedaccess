<?php

namespace App\Controllers;

use App\Models\Subject_model;
use App\Models\Marks_model;
use App\Models\User_model;

class Faculty extends BaseController
{
    public function dashboard()
    {
        $subModel = new Subject_model();
        $userModel = new User_model();
        $marksModel = new Marks_model();
        
        $assignedSubjects = $subModel->getAssignedSubjects(session()->get('id'));
        $leaveModel = new \App\Models\LeaveRequestModel();
        
        $data = [
            'title' => 'Faculty Dashboard',
            'assignedSubjects' => $assignedSubjects,
            'stats' => [
                'subjects' => count($assignedSubjects),
                'students' => $userModel->where('role', 'student')->countAllResults(),
                'grades_total' => $marksModel->where('updated_by', session()->get('id'))->countAllResults(),
                'pending_leaves' => $leaveModel->where('faculty_id', session()->get('id'))->where('status', 'Pending')->countAllResults(),
                'student_pending' => (new \App\Models\StudentLeaveModel())->where('faculty_id', session()->get('id'))->where('status', 'Pending')->countAllResults(),
            ],
            'recentGrades' => $marksModel->select('marks.*, users.name as student_name, subjects.subject_name')
                ->join('users', 'users.id = marks.student_id')
                ->join('subjects', 'subjects.id = marks.subject_id')
                ->where('marks.updated_by', session()->get('id'))
                ->orderBy('marks.updated_at', 'DESC')
                ->limit(4)->findAll(),
            'recentLeave' => $leaveModel->where('faculty_id', session()->get('id'))->orderBy('created_at', 'DESC')->first()
        ];
        return view('layouts/header', $data) . view('faculty/dashboard', $data) . view('layouts/footer');
    }

    public function assignedSubjects()
    {
        $subModel = new Subject_model();
        $data = [
            'title' => 'My Subjects',
            'subjects' => $subModel->getAssignedSubjects(session()->get('id'))
        ];
        return view('layouts/header', $data) . view('faculty/subjects', $data) . view('layouts/footer');
    }

    public function uploadMarks($subject_id)
    {
        $subModel = new Subject_model();
        $userModel = new User_model();
        $marksModel = new Marks_model();
        
        $ansModel = new \App\Models\Answer_sheet_model();
        
        $data = [
            'title' => 'Upload Marks',
            'subject' => $subModel->find($subject_id),
            'students' => $userModel->where('role', 'student')->findAll(),
            'existingMarks' => $marksModel->getMarksBySubject($subject_id),
            'submissions' => $ansModel->where('subject_id', $subject_id)->findAll()
        ];
        return view('layouts/header', $data) . view('faculty/upload_marks', $data) . view('layouts/footer');
    }

    public function storeMarks()
    {
        $marksModel = new Marks_model();
        $data = [
            'student_id' => $this->request->getPost('student_id'),
            'subject_id' => $this->request->getPost('subject_id'),
            'marks'      => $this->request->getPost('marks'),
            'updated_by' => session()->get('id')
        ];

        // Check if marks already exist, then update or insert
        $existing = $marksModel->where([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id']
        ])->first();

        if ($existing) {
            $marksModel->update($existing['id'], $data);
            $msg = 'Marks updated successfully.';
        } else {
            $marksModel->insert($data);
            $msg = 'Marks uploaded successfully.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function manageStudents()
    {
        $userModel = new User_model();
        $faculty = $userModel->find(session()->get('id'));

        $data = [
            'title' => 'Manage Students',
            'students' => $userModel->where('role', 'student')->findAll(),
            'canAdd' => $faculty['can_add_students']
        ];

        return view('layouts/header', $data) . view('faculty/students', $data) . view('layouts/footer');
    }

    public function storeStudent()
    {
        $userModel = new User_model();
        $faculty = $userModel->find(session()->get('id'));

        // Check permission
        if (!$faculty['can_add_students']) {
            return redirect()->back()->with('error', 'Restricted: You do not have proper authority to add students.');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'student'
        ];

        $userModel->insert($data);
        (new \App\Models\ActivityLogModel())->logActivity(session()->get('id'), 'FACULTY_CREATE_STUDENT', "Faculty created student: {$data['email']}");

        return redirect()->back()->with('success', 'Student registered successfully.');
    }

    public function viewProfileRequests()
    {
        $reqModel = new \App\Models\ProfileRequestModel();
        $facultyId = session()->get('id');

        $data = [
            'title' => 'Profile Update Requests',
            'requests' => $reqModel->getPendingForFaculty($facultyId)
        ];

        return view('layouts/header', $data) . view('faculty/profile_requests', $data) . view('layouts/footer');
    }

    public function manageProfileRequest()
    {
        $reqModel = new \App\Models\ProfileRequestModel();
        $userModel = new User_model();
        
        $reqId = $this->request->getPost('request_id');
        $action = $this->request->getPost('action'); // approved or rejected
        $rejReason = $this->request->getPost('rejection_reason');

        $request = $reqModel->find($reqId);
        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        if ($action === 'approved') {
            // Grant appropriate permission based on request type
            if ($request['request_type'] === 'password_change') {
                $userModel->update($request['student_id'], ['can_change_pwd' => 1]);
                $msg = 'Password change request approved.';
            } else {
                $userModel->update($request['student_id'], ['can_edit_profile' => 1]);
                $msg = 'Profile edit request approved.';
            }
            $reqModel->update($reqId, ['status' => 'approved']);
        } else {
            $reqModel->update($reqId, [
                'status' => 'rejected',
                'rejection_reason' => $rejReason
            ]);
            $msg = 'Request rejected successfully.';
        }

        (new \App\Models\ActivityLogModel())->logActivity(session()->get('id'), 'MANAGE_PROFILE_REQUEST', "{$action} request ID: {$reqId}");
        return redirect()->back()->with('success', $msg);
    }

    public function deleteMarks($id)
    {
        $marksModel = new Marks_model();
        $mark = $marksModel->find($id);
        
        if (!$mark) {
            return redirect()->back()->with('error', 'Mark record not found.');
        }

        $marksModel->delete($id);
        (new \App\Models\ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_MARK', "Faculty deleted mark ID: {$id} for student ID: {$mark['student_id']}");

        return redirect()->back()->with('success', 'Mark removed successfully.');
    }

    public function leave()
    {
        $leaveModel = new \App\Models\LeaveRequestModel();
        $studentLeaveModel = new \App\Models\StudentLeaveModel();
        $facultyId = session()->get('id');
        $month = date('n');
        $year = date('Y');

        $data = [
            'title' => 'Leave Application',
            'leaves' => $leaveModel->getLeavesForFaculty($facultyId),
            'studentLeaves' => $studentLeaveModel->getPendingForFaculty($facultyId),
            'monthlyUsage' => $leaveModel->getMonthlyUsage($facultyId, $month, $year),
            'yearlyUsage' => $leaveModel->getTotalYearlyUsage($facultyId, $year),
        ];

        return view('layouts/header', $data) . view('faculty/leave', $data) . view('layouts/footer');
    }

    public function storeLeave()
    {
        $leaveModel = new \App\Models\LeaveRequestModel();
        $userModel = new User_model();
        $facultyId = session()->get('id');

        // Check if there's an active (Pending) leave already
        $existing = $leaveModel->where('faculty_id', $facultyId)->where('status', 'Pending')->first();
        if ($existing && empty($this->request->getPost('leave_id'))) {
            return redirect()->back()->with('error', 'You already have a Pending leave request.');
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        $skipWeekends = (bool) $this->request->getPost('skip_weekends');

        // Calculate days (with optional weekend exclusion)
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);

        if ($skipWeekends) {
            $days = 0;
            $curr = clone $start;
            while ($curr <= $end) {
                if (!in_array((int)$curr->format('N'), [6, 7])) {
                    $days++;
                }
                $curr->modify('+1 day');
            }
        } else {
            $days = $start->diff($end)->days + 1;
        }

        // Get Faculty department & find corresponding HOD
        $faculty = $userModel->find($facultyId);
        $hod = $userModel->where('role', 'hod')->where('department', $faculty['department'])->first();

        if (!$hod) {
            return redirect()->back()->with('error', 'No HOD defined for your department. Contact Admin.');
        }

        $data = [
            'faculty_id' => $facultyId,
            'hod_id'     => $hod['id'],
            'leave_type' => $this->request->getPost('leave_type'),
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'num_days'   => $days,
            'reason'     => $this->request->getPost('reason') ?: $this->request->getPost('leave_type'),
            'status'     => 'Pending'
        ];

        $leave_id = $this->request->getPost('leave_id');
        if (!empty($leave_id)) {
            $leaveModel->update($leave_id, $data);
            $msg = 'Leave application updated.';
        } else {
            $leaveModel->insert($data);
            $msg = 'Leave application submitted.';
        }

        return redirect()->back()->with('leave_swal', $msg);
    }

    public function deleteLeave($id)
    {
        $leaveModel = new \App\Models\LeaveRequestModel();
        $leave = $leaveModel->find($id);

        if ($leave && $leave['faculty_id'] == session()->get('id') && $leave['status'] == 'Pending') {
            $leaveModel->delete($id);
            (new \App\Models\ActivityLogModel())->logActivity(session()->get('id'), 'DELETE_LEAVE', 'Cancelled leave application');
            return redirect()->back()->with('leave_swal', 'Leave canceled successfully.');
        }
        return redirect()->back()->with('error', 'Unable to cancel this leave application.');
    }
    public function manageStudentLeave()
    {
        $leaveModel = new \App\Models\StudentLeaveModel();
        $userModel = new User_model();

        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $remark = $this->request->getPost('remark') ?? '';

        $leave = $leaveModel->find($id);
        if (!$leave || $leave['faculty_id'] != session()->get('id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized.']);
        }

        $leaveModel->update($id, [
            'status' => $status,
            'faculty_remark' => $remark
        ]);

        // Send Email to Student
        $student = $userModel->find($leave['student_id']);
        if ($student && !empty($student['email'])) {
            $this->sendStudentLeaveEmail($student, $status, $remark, $leave);
        }

        return $this->response->setJSON(['success' => true]);
    }

    private function sendStudentLeaveEmail($student, $status, $remark, $leave)
    {
        // ... (existing code)
    }

    public function getSubmissionImages($student_id, $subject_id)
    {
        $ansModel = new \App\Models\Answer_sheet_model();
        $images = $ansModel->where('student_id', $student_id)
                           ->where('subject_id', $subject_id)
                           ->orderBy('page_number', 'ASC')
                           ->findAll();
        
        return $this->response->setJSON(['success' => true, 'images' => $images]);
    }

    public function getStudentDocs($student_id)
    {
        $docModel = new \App\Models\Student_document_model();
        $docs = $docModel->where('student_id', $student_id)->findAll();
        
        return $this->response->setJSON(['success' => true, 'docs' => $docs]);
    }

    public function updateDocumentStatus()
    {
        $docModel = new \App\Models\Student_document_model();
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($docModel->update($id, ['status' => $status])) {
            (new \App\Models\ActivityLogModel())->logActivity(
                session()->get('id'),
                'UPDATE_DOC_STATUS',
                "Updated document ID: {$id} status to {$status}"
            );
            return $this->response->setJSON(['success' => true, 'message' => "Document has been {$status}."]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
    }
}
