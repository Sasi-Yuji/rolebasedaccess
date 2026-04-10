        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter:blur(5px); align-items:center; justify-content:center; z-index:10000;">
        <div class="modal-card" style="background:white; padding:2rem; border-radius:16px; width:90%; max-width:400px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
            <div style="background: #fee2e2; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.5rem;"></i>
            </div>
            <h3 id="modalTitle" style="margin-bottom: 0.5rem; font-weight: 700; color: #1e293b;">Are you sure?</h3>
            <p id="modalMessage" style="color: #64748b; margin-bottom: 2rem;">This action cannot be undone.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="button" onclick="closeConfirmModal()" class="btn" style="background: #f1f5f9; color: #475569; min-width: 100px; border:none; padding: 10px; border-radius: 8px; cursor:pointer;">Cancel</button>
                <a id="modalConfirm" href="#" class="btn btn-danger" style="min-width: 100px; text-decoration:none; display:inline-block; padding: 10px; border-radius: 8px; color:white; background:#ef4444;">Confirm</a>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div id="alertModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter:blur(8px); align-items:center; justify-content:center; z-index:10100;">
        <div class="modal-card" style="background:white; padding:2.5rem; border-radius:24px; width:90%; max-width:400px; text-align:center; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
            <div style="background: #fef3c7; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-info-circle" id="alertIcon" style="color: #d97706; font-size: 1.75rem;"></i>
            </div>
            <h3 id="alertTitle" style="margin-bottom: 0.75rem; font-weight: 700; color: #1e293b; font-size: 1.25rem;">Notice</h3>
            <p id="alertMessage" style="color: #64748b; margin-bottom: 2rem; line-height: 1.5;">This is an alert message.</p>
            <div style="display: flex; justify-content: center;">
                <button type="button" onclick="closeAlertModal()" class="btn" style="background: var(--primary); color: white; min-width: 120px; border:none; padding: 12px; border-radius: 12px; cursor:pointer; font-weight: 600;">Okay</button>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Use window object to ensure global scope
        window.showConfirm = function(title, message, confirmUrl) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalConfirm').setAttribute('href', confirmUrl);
            document.getElementById('confirmModal').style.display = 'flex';
        };

        window.closeConfirmModal = function() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        window.showAlert = function(title, message, type = 'info') {
            document.getElementById('alertTitle').innerText = title;
            document.getElementById('alertMessage').innerText = message;
            const icon = document.getElementById('alertIcon');
            const bg = icon.parentElement;

            if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
                icon.style.color = '#ef4444';
                bg.style.background = '#fee2e2';
            } else if (type === 'warning') {
                icon.className = 'fas fa-exclamation-triangle';
                icon.style.color = '#d97706';
                bg.style.background = '#fef3c7';
            } else {
                icon.className = 'fas fa-info-circle';
                icon.style.color = '#4f46e5';
                bg.style.background = '#eef2ff';
            }

            document.getElementById('alertModal').style.display = 'flex';
        };

        window.closeAlertModal = function() {
            document.getElementById('alertModal').style.display = 'none';
        }

        // Global Toast Notification using SweetAlert2
        window.showToast = function(message, icon = 'success') {
            const isError = icon === 'error';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: isError ? '#fef2f2' : '#f0fdf4',
                color: isError ? '#991b1b' : '#166534',
                iconColor: isError ? '#ef4444' : '#22c55e',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        };

        $(document).ready(function() {
            // Modal Cancel handling for clicking outside the card
            $('.modal-overlay').on('click', function(e) {
                if (e.target === this) $(this).hide();
            });

            // Handle CI Flashdata with Toast
            <?php if (session()->getFlashdata('success')): ?>
                showToast("<?= session()->getFlashdata('success') ?>", 'success');
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                showToast("<?= session()->getFlashdata('error') ?>", 'error');
            <?php endif; ?>
        });
    </script>
</body>
</html>
