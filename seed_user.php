<?php

// Load CodeIgniter bootstrapper
require_once 'c:/xampp/htdocs/rolebasesaccess/public/index.php';

use App\Models\User_model;

$userModel = new User_model();

// First, check if the user exists and delete it to reset
$existingUser = $userModel->where('email', 'superadmin@erp.com')->first();
if ($existingUser) {
    $userModel->delete($existingUser['id']);
}

// Insert the user with a fresh hash
// The User_model has a beforeInsert hook that will hash 'admin123'
$data = [
    'name'     => 'Super Admin',
    'email'    => 'superadmin@erp.com',
    'password' => 'admin123',
    'role'     => 'superadmin'
];

if ($userModel->insert($data)) {
    echo "User superadmin@erp.com seeded successfully with password 'admin123'.\n";
} else {
    echo "Failed to seed user.\n";
    print_r($userModel->errors());
}
