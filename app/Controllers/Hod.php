<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\LeaveRequestModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Email\Email;

class Hod extends BaseController
{
    public function dashboard()
    {
        $leaveModel = new LeaveRequestModel();
        $hodId = session()->get('id');

        $data = [
            'title'        => 'HOD Dashboard',
            'pendingLeaves' => $leaveModel->getLeavesForHod($hodId),
            'stats' => [
                'pending'  => $leaveModel->where('hod_id', $hodId)->where('status', 'Pending')->countAllResults(),
                'approved' => $leaveModel->where('hod_id', $hodId)->where('status', 'Approved')->countAllResults(),
                'rejected' => $leaveModel->where('hod_id', $hodId)->where('status', 'Rejected')->countAllResults(),
            ]
        ];

        return view('layouts/header', $data) . view('hod/dashboard', $data) . view('layouts/footer');
    }

    public function manageLeave()
    {
        $leaveModel = new LeaveRequestModel();
        $userModel  = new User_model();

        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status'); // Approved or Rejected
        $remark = $this->request->getPost('hod_remark') ?? '';

        $leave = $leaveModel->find($id);
        if (!$leave || $leave['hod_id'] != session()->get('id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized or request not found.']);
        }

        if ($leave['status'] !== 'Pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'This request is already finalized.']);
        }

        // Update leave status and remark
        $leaveModel->update($id, [
            'status'     => $status,
            'hod_remark' => $remark
        ]);

        // Send Email Notification to Faculty
        $faculty = $userModel->find($leave['faculty_id']);
        if ($faculty && !empty($faculty['email'])) {
            $this->sendLeaveEmail($faculty, $status, $remark, $leave);
        }

        (new ActivityLogModel())->logActivity(session()->get('id'), 'MANAGE_LEAVE', "HOD {$status} leave ID: {$id}. Remark: {$remark}");

        return $this->response->setJSON([
            'success' => true,
            'message' => "Leave request has been " . strtolower($status) . " successfully."
        ]);
    }

    private function sendLeaveEmail(array $faculty, string $status, string $remark, array $leave): void
    {
        try {
            $email = \Config\Services::email();
            $email->setFrom('kumarsasi9081@gmail.com', 'CampusPro ERP');
            $email->setTo($faculty['email']);

            $icon   = $status === 'Approved' ? '✅' : '❌';
            $color  = $status === 'Approved' ? '#22c55e' : '#ef4444';
            $subject = "{$icon} Leave Request {$status} — CampusPro";

            $remarkHtml = !empty($remark)
                ? "<p style='margin:1rem 0;background:#f8fafc;padding:1rem;border-left:4px solid {$color};border-radius:4px;'><strong>HOD Remark:</strong> {$remark}</p>"
                : '';

            $body = "
            <div style='font-family:Inter,sans-serif;max-width:520px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)'>
                <div style='background:{$color};padding:2rem;text-align:center'>
                    <h1 style='color:#fff;margin:0;font-size:1.5rem'>{$icon} Leave {$status}</h1>
                </div>
                <div style='padding:2rem'>
                    <p>Hi <strong>{$faculty['name']}</strong>,</p>
                    <p>Your leave request has been <strong style='color:{$color}'>{$status}</strong> by your HOD.</p>
                    <table style='width:100%;border-collapse:collapse;margin:1rem 0'>
                        <tr><td style='padding:.5rem;color:#64748b;font-size:.85rem'>Leave Type</td><td style='font-weight:600'>{$leave['leave_type']}</td></tr>
                        <tr style='background:#f8fafc'><td style='padding:.5rem;color:#64748b;font-size:.85rem'>Period</td><td style='font-weight:600'>" . date('M d, Y', strtotime($leave['start_date'])) . " – " . date('M d, Y', strtotime($leave['end_date'])) . "</td></tr>
                        <tr><td style='padding:.5rem;color:#64748b;font-size:.85rem'>Days</td><td style='font-weight:600'>{$leave['num_days']} day(s)</td></tr>
                    </table>
                    {$remarkHtml}
                    <p style='color:#64748b;font-size:.8rem;margin-top:2rem'>This is an automated notification from CampusPro ERP. Please do not reply.</p>
                </div>
            </div>";

            $email->setSubject($subject);
            $email->setMessage($body);
            $email->setMailType('html');
            $email->send();
        } catch (\Throwable $e) {
            // Silently fail — don't break the request if email fails
            log_message('error', 'Leave email failed: ' . $e->getMessage());
        }
    }
}
