<div class="profile-page" style="max-width: 1000px; margin: 0 auto; animation: slide-up 0.5s ease-out;">
    
    <!-- Unified Profile Header Card -->
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem; border: none; box-shadow: var(--shadow-lg);">
        <div style="height: 140px; background: linear-gradient(120deg, #4f46e5 0%, #6366f1 100%);"></div>
        <div style="padding: 0 2.5rem 2rem; position: relative;">
            <div style="display: flex; align-items: flex-end; gap: 2rem; margin-top: -60px;">
                <div style="position: relative;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&size=140&background=eef2ff&color=4f46e5&font-size=0.35&bold=true" style="width: 140px; height: 140px; border-radius: 28px; border: 6px solid white; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);">
                    <div style="position: absolute; bottom: 12px; right: 12px; width: 22px; height: 22px; background: #10b981; border: 4px solid white; border-radius: 50%;"></div>
                </div>
                <div style="flex: 1; padding: 65px 0 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 2.25rem; font-weight: 800; color: #1e293b; letter-spacing: -0.02em; margin-bottom: 0.5rem;"><?= $user['name'] ?></h1>
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <span class="badge badge-student" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">Student Enrollment</span>
                            <span style="color: #64748b; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fas fa-calendar-alt" style="font-size: 0.8rem;"></i> Joined <?= date('F Y', strtotime($user['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($user['can_edit_profile']): ?>
                        <div style="padding: 0.5rem 1rem; background: #dcfce7; color: #15803d; border-radius: 12px; font-weight: 700; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-unlock"></i> Edit Access Granted
                        </div>
                    <?php elseif ($pendingRequest): ?>
                        <div style="padding: 0.5rem 1rem; background: #fff7ed; color: #ea580c; border-radius: 12px; font-weight: 700; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clock"></i> Request Pending Approval
                        </div>
                    <?php else: ?>
                        <button class="btn btn-primary" onclick="openRequestModal()" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; background: #4f46e5; box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);">
                            <i class="fas fa-edit"></i> Edit Request
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for Rejection -->
    <?php if ($lastRejected): ?>
    <div style="margin-bottom: 2rem; padding: 1.25rem; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 16px; display: flex; align-items: flex-start; gap: 1rem;">
        <div style="color: #ef4444; font-size: 1.25rem; margin-top: 2px;"><i class="fas fa-times-circle"></i></div>
        <div>
            <h4 style="color: #991b1b; font-weight: 700; margin-bottom: 0.25rem;">Previous Request Rejected</h4>
            <p style="color: #b91c1c; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Reason: <?= $lastRejected['rejection_reason'] ?: 'No reason provided.' ?></p>
            <span style="font-size: 0.75rem; color: #dc2626; opacity: 0.8;">Date: <?= date('M d, Y', strtotime($lastRejected['updated_at'])) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <?php if ($user['can_edit_profile']): ?>
        <!-- EDIT MODE FORM -->
        <div class="card" style="padding: 2.5rem; border-top: 4px solid #4f46e5; animation: slide-up 0.5s ease-out;">
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">Update Your Information</h3>
                <p style="color: #64748b; font-size: 0.875rem;">You have one-time permission to update your profile details.</p>
            </div>
            
            <form action="<?= base_url('student/profile/update') ?>" method="POST">
                <?= csrf_field() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Full Name</label>
                        <input type="text" name="name" value="<?= $user['name'] ?>" class="form-control" style="background: #f8fafc;" required>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Email Address</label>
                        <input type="email" name="email" value="<?= $user['email'] ?>" class="form-control" style="background: #f8fafc;" required>
                    </div>
                </div>
                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="location.reload()" class="btn btn-light" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; border-radius: 12px; font-weight: 700;">Save Changes</button>
                </div>
            </form>
        </div>
    <?php elseif ($user['can_change_pwd']): ?>
        <!-- PASSWORD CHANGE FORM -->
        <div class="card" style="padding: 2.5rem; border-top: 4px solid #ef4444; animation: slide-up 0.5s ease-out;">
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">Security Update</h3>
                <p style="color: #64748b; font-size: 0.875rem;">Your password change request was approved. Please set a strong new password.</p>
            </div>
            
            <form action="<?= base_url('student/profile/update-password') ?>" method="POST">
                <?= csrf_field() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">New Password</label>
                        <input type="password" name="password" id="new_password" class="form-control" placeholder="••••••••" style="background: #f8fafc;" required minlength="6">
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" style="background: #f8fafc; padding-right: 2.5rem;" required minlength="6">
                        <button type="button" onclick="togglePasswords()" style="position: absolute; right: 12px; bottom: 12px; background: none; border: none; cursor: pointer; color: #94a3b8; outline: none;">
                            <i class="fas fa-eye" id="togglePwdIcon"></i>
                        </button>
                    </div>
                </div>
                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="location.reload()" class="btn btn-light" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="padding: 0.75rem 2.5rem; border-radius: 12px; font-weight: 700; background: #ef4444;">Update Password</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- VIEW MODE GRID -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-user-shield" style="color: #4f46e5; opacity: 0.8;"></i> Personal Identification
                </h3>
                <div style="display: grid; gap: 1.5rem;">
                    <div class="info-row">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Full Legal Name</label>
                        <div style="font-size: 1rem; font-weight: 600; color: #334155;"><?= $user['name'] ?></div>
                    </div>
                    <div class="info-row">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Registered Email</label>
                        <div style="font-size: 1rem; font-weight: 600; color: #334155;"><?= $user['email'] ?></div>
                    </div>
                </div>
            </div>

            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-graduation-cap" style="color: #4f46e5; opacity: 0.8;"></i> Academic Standing
                </h3>
                <div style="display: grid; gap: 1.5rem;">
                    <div class="info-row">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Enrollment Date</label>
                        <div style="font-size: 1rem; font-weight: 600; color: #334155;"><?= date('l, d F Y', strtotime($user['created_at'])) ?></div>
                    </div>
                    <div class="info-row">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Account Status</label>
                        <div>
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 1rem; background: #dcfce7; color: #15803d; border-radius: 50px; font-size: 0.875rem; font-weight: 700;">
                                <span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span> Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- History/Audit Section -->
    <div style="margin-top: 1.5rem; padding: 1.25rem 2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; display: flex; align-items: center; gap: 1.5rem;">
        <div style="width: 48px; height: 48px; background: #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1.25rem;">
            <i class="fas fa-shield-alt"></i>
        </div>
        <p style="font-size: 0.825rem; color: #64748b; font-weight: 500; line-height: 1.5; margin: 0;">
            This profile is part of the central identity management system. Any approved changes will be logged for audit purposes.
        </p>
    </div>
</div>

<!-- Request Modal -->
<div id="requestModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter:blur(8px); align-items:center; justify-content:center; z-index:10000;">
    <div class="modal-card" style="background:white; padding:2.5rem; border-radius:24px; width:95%; max-width:500px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-weight: 800; color: #1e293b; font-size: 1.5rem;">Change Request</h3>
            <button onclick="closeRequestModal()" style="border:none; background:none; cursor:pointer; color: #94a3b8; font-size: 1.25rem;"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="<?= base_url('student/profile/request') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">What do you want to change?</label>
                <select name="request_type" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; height: 3.5rem; font-weight: 600;">
                    <option value="profile_update">Update Profile Information (Name, Email)</option>
                    <option value="password_change">Reset Account Password</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Select Approving Faculty</label>
                <select name="faculty_id" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; height: 3.5rem;">
                    <option value="">-- Choose Faculty --</option>
                    <?php foreach ($facultyList as $fac): ?>
                        <option value="<?= $fac['id'] ?>"><?= $fac['name'] ?> (<?= $fac['department'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569;">Explanation for Request</label>
                    <span id="charCount" style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">0/100</span>
                </div>
                <textarea name="reason" id="reasonInput" rows="3" maxlength="100" class="form-control" required style="width: 100%; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;" placeholder="Describe why this change is necessary..."></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeRequestModal()" class="btn btn-light" style="flex: 1; padding: 0.875rem; border-radius: 12px; font-weight: 700;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 2; padding: 0.875rem; border-radius: 12px; font-weight: 700; background: #4f46e5; border:none; color:white;">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    const reasonInput = document.getElementById('reasonInput');
    const charCount = document.getElementById('charCount');

    reasonInput.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = `${len}/100`;
        charCount.style.color = (len >= 90) ? '#ef4444' : (len >= 70 ? '#f59e0b' : '#94a3b8');
    });

    function openRequestModal() { document.getElementById('requestModal').style.display = 'flex'; }
    function closeRequestModal() { 
        document.getElementById('requestModal').style.display = 'none'; 
        reasonInput.value = '';
        charCount.textContent = '0/100';
    }

    function togglePasswords() {
        const newPwd = document.getElementById('new_password');
        const confPwd = document.getElementById('confirm_password');
        const icon = document.getElementById('togglePwdIcon');
        
        if (newPwd.type === 'password') {
            newPwd.type = 'text';
            confPwd.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            newPwd.type = 'password';
            confPwd.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .info-row { padding: 0.5rem 0; }
    .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    .badge-student { background: rgba(79, 70, 229, 0.1); color: #4f46e5; border: 1px solid rgba(79, 70, 229, 0.2); }
</style>
