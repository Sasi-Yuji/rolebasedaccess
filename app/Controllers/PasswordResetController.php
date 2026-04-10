<?php

namespace App\Controllers;

use App\Models\User_model;
use CodeIgniter\Controller;

class PasswordResetController extends Controller
{
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function sendOTP()
    {
        $email = $this->request->getPost('email');
        $userModel = new User_model();
        $user = $userModel->getUserByEmail($email);

        if (!$user) {
            return redirect()->back()->with('error', 'Email not found.');
        }

        // Restrict roles
        $allowedRoles = ['staff', 'faculty', 'hod', 'admin', 'superadmin'];
        if (!in_array(strtolower($user['role']), $allowedRoles)) {
            return redirect()->back()->with('error', 'Password reset is not available for students via this method.');
        }

        // Generate OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Store OTP
        $db = \Config\Database::connect();
        $db->table('password_resets')->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send Email
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset OTP - CampusPro');
        $emailService->setMessage("Your OTP for password reset is: $otp. It is valid for 5 minutes.");

        if ($emailService->send()) {
            session()->set('reset_email', $email);
            return redirect()->to('verify-otp')->with('success', 'OTP has been sent to your email.');
        } else {
            return redirect()->back()->with('error', 'Failed to send email. Please try again later.');
        }
    }

    public function verifyOTPView()
    {
        if (!session()->has('reset_email')) {
            return redirect()->to('forgot-password');
        }
        return view('auth/verify_otp');
    }

    public function verifyOTP()
    {
        $otp = $this->request->getPost('otp');
        $email = session()->get('reset_email');

        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->where('is_verified', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($reset) {
            $db->table('password_resets')->where('id', $reset['id'])->update(['is_verified' => 1]);
            session()->set('otp_verified', true);
            return redirect()->to('reset-password')->with('success', 'OTP verified. You can now reset your password.');
        } else {
            return redirect()->back()->with('error', 'Invalid or expired OTP.');
        }
    }

    public function resetPasswordView()
    {
        if (!session()->get('otp_verified')) {
            return redirect()->to('verify-otp');
        }
        return view('auth/reset_password');
    }

    public function resetPassword()
    {
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');
        $email = session()->get('reset_email');

        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userModel = new User_model();
        $user = $userModel->getUserByEmail($email);

        if ($user) {
            $userModel->update($user['id'], ['password' => $password]);
            
            // Cleanup
            session()->remove(['reset_email', 'otp_verified']);
            
            return redirect()->to('login')->with('success', 'Password reset successful. Please login with your new password.');
        }

        return redirect()->to('forgot-password')->with('error', 'Something went wrong.');
    }
}   
