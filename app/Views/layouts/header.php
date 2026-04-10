<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'College ERP' ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#6B7280',
                        dark: '#111827',
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('js/form-validations.js') ?>"></script>
    
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="app-sidebar">
            <div class="sidebar-header" style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="background: var(--primary); padding: 0.5rem; border-radius: 8px;">
                        <i class="fas fa-graduation-cap" style="color: white; font-size: 1.25rem;"></i>
                    </div>
                    <h2 class="sidebar-title" style="font-size: 1.25rem; font-weight: 700; color: var(--dark);">CampusPro</h2>
                </div>
                <button class="md:hidden text-slate-400 p-2" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
            </div>
            
            <nav class="sidebar-nav">
                <?php $role = session()->get('role'); ?>
                
                <!-- Common Dashboards -->
                <a href="<?= base_url($role . '/dashboard') ?>" class="nav-link <?= (url_is($role . '/dashboard')) ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="<?= base_url($role . '/chat') ?>" class="nav-link <?= (url_is($role . '/chat*')) ? 'active' : '' ?>" id="global-chat-link">
                    <i class="fas fa-comments"></i> Chat
                    <?php 
                        $chatNotif = new \App\Models\ChatNotificationModel();
                        $unread = $chatNotif->getUnreadCount(session()->get('id'));
                    ?>
                    <span id="global-chat-badge" style="<?= $unread > 0 ? '' : 'display:none;' ?> background: #ef4444; color: white; border-radius: 99px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 800; margin-left: auto;"><?= $unread ?></span>
                </a>

                <?php if ($role === 'superadmin'): ?>
                    <a href="<?= base_url('superadmin/admins') ?>" class="nav-link <?= (url_is('superadmin/admins*')) ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i> Manage Admins
                    </a>
                    <a href="<?= base_url('superadmin/logs') ?>" class="nav-link <?= (url_is('superadmin/logs')) ? 'active' : '' ?>">
                        <i class="fas fa-history"></i> System Logs
                    </a>
                    <a href="<?= base_url('superadmin/permissions') ?>" class="nav-link <?= (url_is('superadmin/permissions')) ? 'active' : '' ?>">
                        <i class="fas fa-shield-alt"></i> Role Permissions
                    </a>
                <?php elseif ($role === 'admin'): ?>
                    <a href="<?= base_url('admin/students') ?>" class="nav-link <?= (url_is('admin/students*')) ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i> Manage Students
                    </a>
                    <a href="<?= base_url('admin/faculty') ?>" class="nav-link <?= (url_is('admin/faculty*')) ? 'active' : '' ?>">
                        <i class="fas fa-chalkboard-teacher"></i> Manage Faculty
                    </a>
                    <a href="<?= base_url('admin/subjects') ?>" class="nav-link <?= (url_is('admin/subjects*')) ? 'active' : '' ?>">
                        <i class="fas fa-book"></i> Subjects
                    </a>
                    <a href="<?= base_url('admin/hods') ?>" class="nav-link <?= (url_is('admin/hods*')) ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i> Manage HODs
                    </a>
                    <a href="<?= base_url('admin/permissions') ?>" class="nav-link <?= (url_is('admin/permissions')) ? 'active' : '' ?>">
                        <i class="fas fa-shield-alt"></i> Role Permissions
                    </a>
                    <a href="<?= base_url('admin/bus') ?>" class="nav-link <?= (url_is('admin/bus*')) ? 'active' : '' ?>">
                        <i class="fas fa-bus"></i> Manage Transport
                    </a>
                    <a href="<?= base_url('admin/courses') ?>" class="nav-link <?= (url_is('admin/courses')) ? 'active' : '' ?>">
                        <i class="fas fa-layer-group"></i> Manage Courses
                    </a>
                    <a href="<?= base_url('admin/courses/dashboard') ?>" class="nav-link <?= (url_is('admin/courses/dashboard')) ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i> Course Analytics
                    </a>
                <?php elseif ($role === 'faculty'): ?>
                    <a href="<?= base_url('faculty/subjects') ?>" class="nav-link <?= (url_is('faculty/subjects*')) ? 'active' : '' ?>">
                        <i class="fas fa-book-open"></i> Assigned Subjects
                    </a>
                    <a href="<?= base_url('faculty/students') ?>" class="nav-link <?= (url_is('faculty/students*')) ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i> Manage Students
                    </a>
                    <a href="<?= base_url('faculty/profile-requests') ?>" class="nav-link <?= (url_is('faculty/profile-requests*')) ? 'active' : '' ?>">
                        <i class="fas fa-id-badge"></i> Profile Requests
                    </a>
                    <a href="<?= base_url('faculty/leave') ?>" class="nav-link <?= (url_is('faculty/leave*')) ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i> Apply Leave
                    </a>
                    <a href="<?= base_url('faculty/courses') ?>" class="nav-link <?= (url_is('faculty/courses*')) ? 'active' : '' ?>">
                        <i class="fas fa-layer-group"></i> Courses
                    </a>
                <?php elseif ($role === 'hod'): ?>
                    <!-- HOD Specific Links -->
                    <?php 
                        $pendingCount = (new \App\Models\LeaveRequestModel())->where('hod_id', session()->get('id'))->where('status', 'Pending')->countAllResults();
                    ?>
                    <a href="<?= base_url('hod/dashboard') ?>" class="nav-link <?= (url_is('hod/dashboard')) ? 'active' : '' ?>" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-tasks"></i> Leave Requests</span>
                        <?php if($pendingCount > 0): ?>
                            <span style="background: #ef4444; color: white; border-radius: 99px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 800; animation: pulse 2s infinite;"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('hod/courses') ?>" class="nav-link <?= (url_is('hod/courses*')) ? 'active' : '' ?>">
                        <i class="fas fa-layer-group"></i> Courses
                    </a>
                <?php elseif ($role === 'student'): ?>
                    <a href="<?= base_url('student/marks') ?>" class="nav-link <?= (url_is('student/marks')) ? 'active' : '' ?>">
                        <i class="fas fa-poll"></i> My Marks
                    </a>
                    <a href="<?= base_url('student/profile') ?>" class="nav-link <?= (url_is('student/profile')) ? 'active' : '' ?>">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a href="<?= base_url('student/bus') ?>" class="nav-link <?= (url_is('student/bus*')) ? 'active' : '' ?>">
                        <i class="fas fa-bus"></i> Bus Tracker
                    </a>
                    <a href="<?= base_url('student/courses') ?>" class="nav-link <?= (url_is('student/courses*')) ? 'active' : '' ?>">
                        <i class="fas fa-layer-group"></i> Course Catalogue
                    </a>
                    <a href="<?= base_url('student/leave') ?>" class="nav-link <?= (url_is('student/leave*')) ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i> Apply Leave
                    </a>
                    <div style="border-top: 1px solid #f1f5f9; margin: 0.5rem 0.75rem; padding-top: 0.5rem;">
                        <span style="font-size: 0.65rem; text-transform: uppercase; font-weight: 800; color: #94a3b8; padding-left: 1rem; letter-spacing: 0.1em;">Submissions</span>
                        <a href="<?= base_url('student/upload/answers') ?>" class="nav-link <?= (url_is('student/upload/answers')) ? 'active' : '' ?>">
                            <i class="fas fa-file-upload"></i> Exam Uploads
                        </a>
                        <a href="<?= base_url('student/upload/marksheet') ?>" class="nav-link <?= (url_is('student/upload/marksheet')) ? 'active' : '' ?>">
                            <i class="fas fa-id-card"></i> Document Vault
                        </a>
                    </div>
                <?php endif; ?>
            </nav>

            <div style="margin-top: auto;">
                <a href="<?= base_url('logout') ?>" class="nav-link" style="color: var(--danger);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <button id="sidebar-toggle" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 style="font-size: 1.25rem; font-weight: 600; margin: 0;"><?= $title ?? 'Dashboard' ?></h1>
                        <p style="color: #64748b; font-size: 0.75rem; margin: 0;">Welcome back, <?= session()->get('name') ?></p>
                    </div>
                </div>

                <script>
                    function toggleSidebar() {
                        const sidebar = document.getElementById('app-sidebar');
                        const overlay = document.getElementById('sidebar-overlay');
                        sidebar.classList.toggle('show');
                        overlay.classList.toggle('show');
                    }
                </script>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.875rem;"><?= session()->get('name') ?></div>
                        <div class="badge badge-<?= $role ?>" style="font-size: 0.75rem; text-transform: uppercase;"><?= $role ?></div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                         <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=random" alt="Avatar">
                    </div>
                </div>
            </header>

