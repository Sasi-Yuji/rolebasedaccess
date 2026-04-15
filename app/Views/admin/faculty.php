<style>
    /* Force Toggle Switch UI */
    .switch {
        position: relative !important;
        display: inline-block !important;
        width: 44px !important;
        height: 22px !important;
        vertical-align: middle;
    }
    .switch input { 
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
    }
    .slider {
        position: absolute !important;
        cursor: pointer !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: #cbd5e1 !important;
        transition: .4s !important;
        border-radius: 34px !important;
    }
    .slider:before {
        position: absolute !important;
        content: "" !important;
        height: 16px !important;
        width: 16px !important;
        left: 3px !important;
        bottom: 3px !important;
        background-color: white !important;
        transition: .4s !important;
        border-radius: 50% !important;
    }
    .switch input:checked + .slider {
        background-color: #4f46e5 !important;
    }
    .switch input:checked + .slider:before {
        transform: translateX(22px) !important;
    }
</style>
<div style="display: flex; gap: 1.5rem;">
    <div class="card" style="flex: 2;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;">Faculty Members</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Students Access</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faculty as $f): ?>
                <tr>
                    <td>#F-<?= $f['id'] ?></td>
                    <td><?= $f['name'] ?></td>
                    <td><?= $f['email'] ?></td>
                    <td><span class="badge badge-faculty"><?= $f['department'] ?? 'Not Assigned' ?></span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" onchange="toggleStudentAccess(<?= $f['id'] ?>, this.checked)" <?= $f['can_add_students'] ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="javascript:void(0)" onclick="editFaculty(<?= htmlspecialchars(json_encode($f)) ?>)" class="btn btn-warning" style="padding: 0.4rem 0.6rem; font-size: 0.7rem; background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="showConfirm('Delete Faculty', 'Are you sure you want to remove <?= $f['name'] ?>?', '<?= base_url('admin/faculty/delete/'.$f['id']) ?>')" class="btn btn-danger" style="padding: 0.4rem 0.6rem; font-size: 0.7rem;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="flex: 1; height: fit-content;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;">Add Faculty</h3>
        <form id="facultyForm" action="<?= base_url('admin/faculty/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="faculty_id">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="name" class="form-control" maxlength="20" required>
                <div id="nameError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control" maxlength="35" required>
                <div id="emailError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department" id="department" class="form-control" required>
                    <option value="">Select Department</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Business">Business</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" maxlength="15" required style="padding-right: 40px;">
                    <span id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="passError" class="error-message"></div>
            </div>
            <div style="text-align: right; margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" id="cancelEdit" class="btn btn-secondary" style="padding: 0.6rem 1.5rem; display: none;" onclick="resetForm()">Cancel</button>
                <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 0.6rem 2rem;">Add Faculty Member</button>
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

        $('#name').on('input blur', function() {
            validator.validateName($(this).val(), '#nameError');
        });

        $('#email').on('input blur', function() {
            validator.validateEmail($(this).val(), '#emailError');
        });

        $('#facultyForm').on('submit', function(e) {
            const isNameValid = validator.validateName($('#name').val(), '#nameError');
            const isEmailValid = validator.validateEmail($('#email').val(), '#emailError');
            
            const isNew = $('#faculty_id').val() === '';
            if (isNew && !$('#password').val()) {
                $('#passError').text('Password is required').show();
                e.preventDefault();
                return;
            }

            if (!isNameValid || !isEmailValid) {
                e.preventDefault();
            }
        });
    });

    function editFaculty(f) {
        $('#faculty_id').val(f.id);
        $('#name').val(f.name);
        $('#email').val(f.email);
        $('#department').val(f.department);
        $('#password').val('').attr('required', false);
        
        $('#facultyForm').attr('action', '<?= base_url('admin/faculty/update') ?>');
        $('h3:last').text('Edit Faculty');
        $('#submitBtn').text('Update Faculty');
        $('#cancelEdit').show();
        $('.error-message').text('').hide();
    }

    function resetForm() {
        $('#faculty_id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#department').val('');
        $('#password').val('').attr('required', true);
        
        $('#facultyForm').attr('action', '<?= base_url('admin/faculty/store') ?>');
        $('h3:last').text('Add Faculty');
        $('#submitBtn').text('Add Faculty Member');
        $('#cancelEdit').hide();
        $('.error-message').text('').hide();
    }

    function toggleStudentAccess(facultyId, isChecked) {
        const status = isChecked ? 1 : 0;
        fetch('<?= base_url('admin/faculty/toggle-access/') ?>' + facultyId + '/' + status)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessToast(data.message);
                } else {
                    showAlert('Error', 'Unable to update faculty status. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('System Error', 'An unexpected error occurred. Check console for details.', 'error');
            });
    }

    function showSuccessToast(message) {
        const toast = $('<div class="success-toast">' +
            '<i class="fas fa-check-circle"></i>' +
            '<span>' + message + '</span>' +
            '</div>');
        $('body').append(toast);
        toast.fadeIn().delay(3000).fadeOut(function() {
            $(this).remove();
        });
    }
</script>
