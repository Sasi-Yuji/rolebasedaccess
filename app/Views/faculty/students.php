<div style="display: flex; gap: 1.5rem;">
    <div class="card" style="flex: 2;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-weight: 600;">Student Records</h3>
            <?php if (!$canAdd): ?>
                <span class="badge" style="background: #fee2e2; color: #991b1b; font-size: 0.75rem;">
                    <i class="fas fa-lock"></i> Adding Students Restricted
                </span>
            <?php endif; ?>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Join Date</th>
                    <th style="text-align: right;">Documents</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td>#S-<?= $s['id'] ?></td>
                    <td><strong><?= $s['name'] ?></strong></td>
                    <td style="color: #64748b;"><?= $s['email'] ?></td>
                    <td style="color: #64748b;"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                    <td style="text-align: right;">
                        <button onclick="viewDocs(<?= $s['id'] ?>, '<?= addslashes($s['name']) ?>')" class="btn-action" style="background: #ecfdf5; color: #10b981; border: none; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                            <i class="fas fa-file-contract"></i> Verify
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($canAdd): ?>
    <div class="card" style="flex: 1; height: fit-content;">
        <h3 style="margin-bottom: 1rem; font-weight: 600;">Add Student</h3>
        <form id="studentForm" action="<?= base_url('faculty/students/store') ?>" method="POST">
            <?= csrf_field() ?>
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
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" maxlength="15" required style="padding-right: 40px;">
                    <span id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="passError" class="error-message"></div>
            </div>
            <div style="text-align: right; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem;">Register Student</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="card" style="flex: 1; height: fit-content; text-align: center; padding: 3rem 1.5rem; background: #fdf2f2; border-color: #fecaca;">
        <div style="width: 64px; height: 64px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #dc2626; font-size: 1.5rem;">
            <i class="fas fa-user-slash"></i>
        </div>
        <h4 style="color: #991b1b; margin-bottom: 0.5rem;">Access Restricted</h4>
        <p style="color: #b91c1c; font-size: 0.875rem;">You do not have proper authority to add new students at this time. Please contact Admin if you believe this is an error.</p>
    </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        // Use global FormValidator if available (common in this app)
        if (typeof FormValidator !== 'undefined') {
            const validator = new FormValidator();
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

            $('#studentForm').on('submit', function(e) {
                const isNameValid = validator.validateName($('#name').val(), '#nameError');
                const isEmailValid = validator.validateEmail($('#email').val(), '#emailError');
                
                if (!isNameValid || !isEmailValid) {
                    e.preventDefault();
                }
            });
        }
    });

    function viewDocs(studentId, studentName) {
        $.ajax({
            url: `<?= base_url('faculty/students/documents') ?>/${studentId}`,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.docs.length > 0) {
                    let html = '';
                    res.docs.forEach((doc, idx) => {
                        let statusColor = doc.status === 'Approved' ? '#ecfdf5' : (doc.status === 'Pending' ? '#fffbeb' : '#fef2f2');
                        let statusTextColor = doc.status === 'Approved' ? '#065f46' : (doc.status === 'Pending' ? '#b45309' : '#991b1b');
                        
                        html += `
                        <div style="flex: 1; min-height: 0; display: flex; flex-direction: column; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; background: #fff;">
                                <div>
                                    <span style="font-weight: 700; color: #1e293b; margin-right: 0.75rem;">${doc.doc_name}</span>
                                    <span id="doc-badge-${doc.id}" class="badge" style="background: ${statusColor}; color: ${statusTextColor}; font-size: 0.75rem; padding: 0.35rem 0.8rem; border-radius: 12px; font-weight: 700;">${doc.status}</span>
                                </div>
                                <div id="actions-${doc.id}" style="display: flex; gap: 0.5rem;">
                                    <?php if (session()->get('role') === 'faculty' || session()->get('role') === 'admin'): ?>
                                        <button onclick="updateDocStatus(${doc.id}, 'Approved')" class="btn" style="background: #10b981; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.7rem; font-weight: 700; cursor: pointer; transition: all 0.2s;"><i class="fas fa-check"></i> Approve</button>
                                        <button onclick="updateDocStatus(${doc.id}, 'Rejected')" class="btn" style="background: #ef4444; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.7rem; font-weight: 700; cursor: pointer; transition: all 0.2s;"><i class="fas fa-times"></i> Reject</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="flex: 1; min-height: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; background: #f8fafc;">
                                <img src="<?= base_url() ?>${doc.image_path}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        </div>`;
                    });
                    
                    document.getElementById('docs-content').innerHTML = html;
                    document.getElementById('modal-student-name').textContent = studentName;
                    document.getElementById('docs-modal').style.display = 'flex';
                } else {
                    alert('This student has not uploaded any documents yet.');
                }
            }
        });
    }

    function closeDocs() {
        document.getElementById('docs-modal').style.display = 'none';
        document.getElementById('docs-content').innerHTML = '';
        // Optionally refresh the whole page if needed, but the live status change in UI is better
    }

    function updateDocStatus(id, newStatus) {
        const btnContainer = document.getElementById(`actions-${id}`);
        const originalHtml = btnContainer.innerHTML;
        btnContainer.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: #64748b;"></i>';

        $.ajax({
            url: '<?= base_url('faculty/students/documents/update-status') ?>',
            method: 'POST',
            data: { id: id, status: newStatus },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    // Update badge
                    const badge = document.getElementById(`doc-badge-${id}`);
                    badge.textContent = newStatus;
                    
                    if (newStatus === 'Approved') {
                        badge.style.background = '#ecfdf5';
                        badge.style.color = '#065f46';
                    } else if (newStatus === 'Pending') {
                        badge.style.background = '#fffbeb';
                        badge.style.color = '#b45309';
                    } else {
                        badge.style.background = '#fef2f2';
                        badge.style.color = '#991b1b';
                    }

                    // Success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });
                }
                btnContainer.innerHTML = originalHtml;
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Unable to communicate with the server.'
                });
                btnContainer.innerHTML = originalHtml;
            }
        });
    }
</script>

<!-- Document Verification Modal -->
<div id="docs-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div style="background: white; width: 95%; max-width: 800px; height: 85vh; border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="padding: 1.5rem 2.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <div>
                <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Marksheet Verification</h3>
                <p style="color: #10b981; font-size: 0.875rem; font-weight: 700; margin: 0;" id="modal-student-name"></p>
            </div>
            <button onclick="closeDocs()" style="width: 40px; height: 40px; border-radius: 12px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fas fa-times"></i></button>
        </div>
        <div id="docs-content" style="flex: 1; overflow: hidden; padding: 1.5rem 2.5rem; background: #f8fafc; display: flex; flex-direction: column; gap: 1rem;">
            <!-- Docs injected here -->
        </div>
        <div style="padding: 1.5rem 2.5rem; border-top: 1px solid #f1f5f9; text-align: center; background: #fff;">
            <button onclick="closeDocs()" class="btn btn-primary" style="padding: 0.75rem 3rem; border-radius: 12px; font-weight: 700; background: #10b981;">Finish Review</button>
        </div>
    </div>
</div>
