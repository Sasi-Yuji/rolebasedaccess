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
        <h3 style="margin-bottom: 1rem; font-weight: 600;">HOD Members</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hods as $f): ?>
                <tr>
                    <td>#HOD-<?= $f['id'] ?></td>
                    <td><?= $f['name'] ?></td>
                    <td><?= $f['email'] ?></td>
                    <td><span class="badge badge-HOD" style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem;"><?= $f['department'] ?? 'Not Assigned' ?></span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="javascript:void(0)" onclick="editHOD(<?= htmlspecialchars(json_encode($f)) ?>)" class="btn btn-warning" style="padding: 0.4rem 0.6rem; font-size: 0.7rem; background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="showConfirm('Delete HOD', 'Are you sure you want to remove <?= $f['name'] ?>?', '<?= base_url('admin/hods/delete/'.$f['id']) ?>')" class="btn btn-danger" style="padding: 0.4rem 0.6rem; font-size: 0.7rem;">
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
        <h3 style="margin-bottom: 1rem; font-weight: 600;">Add HOD</h3>
        <form id="HODForm" action="<?= base_url('admin/hods/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="HOD_id">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="name" class="form-control" maxlength="20" required>
                <div id="nameError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control" maxlength="25" required>
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
                <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 0.6rem 2rem;">Add HOD Member</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        const validator = new FormValidator();

        // Lock field constraints
        if(typeof validator.lockNameField === 'function') {
            validator.lockNameField('#name');
            validator.lockEmailField('#email');
            validator.lockPasswordField('#password');
            validator.initPasswordToggle('#password', '#togglePassword');
        }

        $('#name').on('input blur', function() {
            if(typeof validator.validateName === 'function') validator.validateName($(this).val(), '#nameError');
        });

        $('#email').on('input blur', function() {
            if(typeof validator.validateEmail === 'function') validator.validateEmail($(this).val(), '#emailError');
        });

        $('#HODForm').on('submit', function(e) {
            let isNameValid = true;
            let isEmailValid = true;
            
            if(typeof validator.validateName === 'function') {
                isNameValid = validator.validateName($('#name').val(), '#nameError');
                isEmailValid = validator.validateEmail($('#email').val(), '#emailError');
            }
            
            const isNew = $('#HOD_id').val() === '';
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

    function editHOD(f) {
        $('#HOD_id').val(f.id);
        $('#name').val(f.name);
        $('#email').val(f.email);
        $('#department').val(f.department);
        $('#password').val('').attr('required', false);
        
        $('#HODForm').attr('action', '<?= base_url('admin/hods/update') ?>');
        $('h3:last').text('Edit HOD');
        $('#submitBtn').text('Update HOD');
        $('#cancelEdit').show();
        $('.error-message').text('').hide();
    }

    function resetForm() {
        $('#HOD_id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#department').val('');
        $('#password').val('').attr('required', true);
        
        $('#HODForm').attr('action', '<?= base_url('admin/hods/store') ?>');
        $('h3:last').text('Add HOD');
        $('#submitBtn').text('Add HOD Member');
        $('#cancelEdit').hide();
        $('.error-message').text('').hide();
    }

    function showConfirm(title, text, url) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        } else {
            if(confirm(text)) {
                window.location.href = url;
            }
        }
    }
</script>
