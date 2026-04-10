<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\ActivityLogModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url(session()->get('role') . '/dashboard'));
        }
        return view('auth/login');
    }

    public function loginProcess()
    {
        $userModel = new User_model();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'id'         => $user['id'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ];
            session()->set($sessionData);

            // Log activity
            $logModel = new ActivityLogModel();
            $logModel->logActivity($user['id'], 'LOGIN', 'User logged into the system.');

            return redirect()->to(base_url($user['role'] . '/dashboard'));
        } else {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
    }

    public function logout()
    {
        // Log logout before destroying session
        if (session()->get('isLoggedIn')) {
            try {
                $logModel = new ActivityLogModel();
                $logModel->logActivity(session()->get('id'), 'LOGOUT', 'User logged out.');
            } catch (\Exception $e) {
                // Ignore log errors on logout (e.g. if user ID no longer exists after DB reset)
            }
        }
        
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
