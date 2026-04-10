<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Landing & Auth
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login/process', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'PasswordResetController::forgotPassword');
$routes->post('forgot-password', 'PasswordResetController::sendOTP');
$routes->get('verify-otp', 'PasswordResetController::verifyOTPView');
$routes->post('verify-otp', 'PasswordResetController::verifyOTP');
$routes->get('reset-password', 'PasswordResetController::resetPasswordView');
$routes->post('reset-password', 'PasswordResetController::resetPassword');

// Super Admin Group
$routes->group('superadmin', ['filter' => 'auth:superadmin'], function($routes) {
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    $routes->get('admins', 'SuperAdmin::manageAdmins');
    $routes->post('admins/store', 'SuperAdmin::storeAdmin');
    $routes->post('admins/update', 'SuperAdmin::updateAdmin');
    $routes->get('admins/delete/(:num)', 'SuperAdmin::deleteAdmin/$1');
    $routes->get('logs', 'SuperAdmin::systemLogs');
    $routes->get('permissions', 'SuperAdmin::permissions');
    $routes->post('permissions/save', 'SuperAdmin::savePermissions');
    
    // Chat
    $routes->get('chat', 'Chat::index');
});

// Admin Group
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('students', 'Admin::manageStudents');
    $routes->post('students/store', 'Admin::storeStudent');
    $routes->post('students/update', 'Admin::updateStudent');
    $routes->get('students/delete/(:num)', 'Admin::deleteStudent/$1');
    $routes->get('faculty', 'Admin::manageFaculty');
    $routes->post('faculty/store', 'Admin::storeFaculty');
    $routes->post('faculty/update', 'Admin::updateFaculty');
    $routes->get('faculty/delete/(:num)', 'Admin::deleteFaculty/$1');
    $routes->get('faculty/toggle-access/(:num)/(:num)', 'Admin::toggleFacultyAccess/$1/$2');
    $routes->get('subjects', 'Admin::manageSubjects');
    $routes->post('subjects/store', 'Admin::storeSubject');
    $routes->post('subjects/assign', 'Admin::assignSubject');
    $routes->get('permissions', 'Admin::permissions');
    $routes->post('permissions/save', 'Admin::savePermissions');
    
    // HODs
    $routes->get('hods', 'Admin::manageHods');
    $routes->post('hods/store', 'Admin::storeHod');
    $routes->post('hods/update', 'Admin::updateHod');
    $routes->get('hods/delete/(:num)', 'Admin::deleteHod/$1');
    
    // Bus Routes
    $routes->get('bus', 'BusController::adminBus');
    $routes->post('bus/store', 'BusController::storeRoute');
    $routes->post('bus/update-status', 'BusController::updateStatus');
    $routes->post('bus/add-stop', 'BusController::addStop');
    $routes->get('bus/delete/(:num)', 'BusController::deleteRoute/$1');

    // Course Management
    $routes->get('courses', 'Courses::index');
    $routes->get('courses/dashboard', 'Courses::dashboard');
    $routes->get('courses/create', 'Courses::create');
    $routes->post('courses/store', 'Courses::store');
    $routes->get('courses/edit/(:num)', 'Courses::edit/$1');
    $routes->post('courses/update/(:num)', 'Courses::update/$1');
    $routes->get('courses/delete/(:num)', 'Courses::delete/$1');

    // Chat
    $routes->get('chat', 'Chat::index');
});

// Faculty Group
$routes->group('faculty', ['filter' => 'auth:faculty'], function($routes) {
    $routes->get('dashboard', 'Faculty::dashboard');
    $routes->get('subjects', 'Faculty::assignedSubjects');
    $routes->get('students', 'Faculty::manageStudents');
    $routes->post('students/store', 'Faculty::storeStudent');
    $routes->get('students/documents/(:num)', 'Faculty::getStudentDocs/$1');
    $routes->post('students/documents/update-status', 'Faculty::updateDocumentStatus');
    $routes->get('profile-requests', 'Faculty::viewProfileRequests');
    $routes->post('profile-requests/manage', 'Faculty::manageProfileRequest');
    $routes->get('marks/upload/(:num)', 'Faculty::uploadMarks/$1');
    $routes->post('marks/store', 'Faculty::storeMarks');
    $routes->get('marks/delete/(:num)', 'Faculty::deleteMarks/$1');
    $routes->get('marks/submission/(:num)/(:num)', 'Faculty::getSubmissionImages/$1/$2');
    
    // Leaves
    $routes->get('leave', 'Faculty::leave');
    $routes->post('leave/store', 'Faculty::storeLeave');
    $routes->get('leave/delete/(:num)', 'Faculty::deleteLeave/$1');
    $routes->post('leave/manage_student', 'Faculty::manageStudentLeave');

    // Courses (View Only)
    $routes->get('courses', 'Courses::index');

    // Chat
    $routes->get('chat', 'Chat::index');
});

// HOD Group
$routes->group('hod', ['filter' => 'auth:hod'], function($routes) {
    $routes->get('dashboard', 'Hod::dashboard');
    $routes->post('leave/manage', 'Hod::manageLeave');
    
    // Courses (View Only)
    $routes->get('courses', 'Courses::index');

    // Chat
    $routes->get('chat', 'Chat::index');
});

// Student Group
$routes->group('student', ['filter' => 'auth:student'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('marks', 'Student::viewMarks');
    $routes->get('profile', 'Student::profile');
    $routes->post('profile/request', 'Student::submitEditRequest');
    $routes->post('profile/update', 'Student::updateProfile');
    $routes->post('profile/update-password', 'Student::updatePassword');
    
    // Bus Tracking
    $routes->get('bus', 'BusController::studentBus');

    // Courses (Catalogue)
    $routes->get('courses', 'Courses::index');

    // Chat
    $routes->get('chat', 'Chat::index');

    // Leave
    $routes->get('leave', 'Student::leave');
    $routes->post('leave/store', 'Student::storeLeave');
    $routes->get('leave/delete/(:num)', 'Student::deleteLeave/$1');

    // Document Uploads
    $routes->get('upload/answers', 'Student::viewAnswerUpload');
    $routes->post('upload/answers/store', 'Student::storeAnswerSheet');
    $routes->get('upload/marksheet', 'Student::viewMarksheetUpload');
    $routes->post('upload/marksheet/store', 'Student::storeMarksheet');
});

// Common Chat API Group
$routes->group('chat/api', ['filter' => 'auth:admin,student,faculty,hod,superadmin'], function($routes) {
    $routes->get('conversations', 'ChatAPI::getConversations');
    $routes->post('conversations', 'ChatAPI::createConversation');
    $routes->get('conversations/(:num)/messages', 'ChatAPI::getMessages/$1');
    $routes->get('conversations/(:num)/attachments', 'ChatAPI::getConversationAttachments/$1');
    $routes->get('conversations/(:num)/stats', 'ChatAPI::getConversationStats/$1');
    $routes->post('conversations/(:num)/messages/send', 'ChatAPI::sendMessage/$1');
    $routes->get('conversations/(:num)/messages/poll', 'ChatAPI::pollNewMessages/$1');
    $routes->post('conversations/(:num)/deliver', 'ChatAPI::markAsDelivered/$1');
    $routes->post('conversations/(:num)/clear', 'ChatAPI::clearConversation/$1');
    $routes->post('conversations/(:num)/delete', 'ChatAPI::deleteConversation/$1');
    $routes->get('conversations/(:num)/members', 'ChatAPI::getMembers/$1');
    $routes->post('conversations/(:num)/members/role', 'ChatAPI::updateMemberRole/$1');
    $routes->post('conversations/(:num)/members/remove', 'ChatAPI::removeMember/$1');
    $routes->post('conversations/(:num)/members/add', 'ChatAPI::addMembers/$1');
    $routes->post('upload', 'ChatAPI::uploadFile');
    $routes->post('users/heartbeat', 'ChatAPI::updateHeartbeat');
    $routes->post('messages/(:num)/react', 'ChatAPI::addReaction/$1');
    $routes->post('messages/(:num)/delete', 'ChatAPI::deleteMessage/$1');
});
