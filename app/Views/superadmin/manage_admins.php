<div style="display: flex; gap: 2rem;">
    <!-- List of Admins -->
    <div class="card" style="flex: 2;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Active Administrators</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= $admin['name'] ?></td>
                        <td><?= $admin['email'] ?></td>
                        <td><?= date('M d, Y', strtotime($admin['created_at'])) ?></td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="javascript:void(0)" onclick="editAdmin(<?= htmlspecialchars(json_encode($admin)) ?>)" class="btn btn-warning" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="showConfirm('Delete Admin', 'Are you sure you want to remove <?= $admin['name'] ?>?', '<?= base_url('superadmin/admins/delete/'.$admin['id']) ?>')" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Create New Admin -->
    <div class="card" style="flex: 1; height: fit-content;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Add Administrator</h3>
        <form id="adminForm" action="<?= base_url('superadmin/admins/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="admin_id">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="John Doe" maxlength="20">
                <div id="nameError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="admin@college.edu" maxlength="35">
                <div id="emailError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Initial Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" maxlength="15" style="padding-right: 40px;">
                    <span id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="passError" class="error-message"></div>
            </div>
            <div style="text-align: right; margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" id="cancelEdit" class="btn btn-secondary" style="padding: 0.6rem 1.5rem; display: none;" onclick="resetForm()">Cancel</button>
                <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 0.6rem 2rem;">Create Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        const validator = new FormValidator();

        // Lock field constraints (physically prevent typing disallowed characters)
        validator.lockNameField('#name');
        validator.lockEmailField('#email');
        validator.lockPasswordField('#password');
        validator.initPasswordToggle('#password', '#togglePassword');

        // Real-time validation
        $('#name').on('input blur', function() {
            validator.validateName($(this).val(), '#nameError');
        });

        $('#email').on('input blur', function() {
            validator.validateEmail($(this).val(), '#emailError');
        });

        // Form submission
        $('#adminForm').on('submit', function(e) {
            const isNameValid = validator.validateName($('#name').val(), '#nameError');
            const isEmailValid = validator.validateEmail($('#email').val(), '#emailError');
            
            // Password is only required for new admins
            const isNew = $('#admin_id').val() === '';
            const passVal = $('#password').val();
            if (isNew && !passVal) {
                $('#passError').text('Password is required').show();
                e.preventDefault();
                return;
            }

            if (!isNameValid || !isEmailValid) {
                e.preventDefault();
            }
        });
    });

    function editAdmin(admin) {
        $('#admin_id').val(admin.id);
        $('#name').val(admin.name);
        $('#email').val(admin.email);
        $('#password').val('').attr('required', false);
        
        $('#adminForm').attr('action', '<?= base_url('superadmin/admins/update') ?>');
        $('h3:last').text('Edit Administrator');
        $('#submitBtn').text('Update Account');
        $('#cancelEdit').show();
        
        // Trigger validation clear
        $('.error-message').text('').hide();
    }

    function resetForm() {
        $('#admin_id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#password').val('').attr('required', true);
        
        $('#adminForm').attr('action', '<?= base_url('superadmin/admins/store') ?>');
        $('h3:last').text('Add Administrator');
        $('#submitBtn').text('Create Account');
        $('#cancelEdit').hide();
        $('.error-message').text('').hide();
    }
</script>
