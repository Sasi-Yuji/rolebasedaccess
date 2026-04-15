<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h3 style="font-weight: 700; color: #1e293b;">Profile Update Requests</h3>
            <p style="color: #64748b; font-size: 0.875rem;">Manage student requests to edit their official profile information.</p>
        </div>
        <div class="badge badge-faculty" style="padding: 0.5rem 1rem;"><?= count($requests) ?> Pending</div>
    </div>

    <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 4rem 2rem; background: #f8fafc; border-radius: 16px; border: 2px dashed #e2e8f0;">
            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fas fa-inbox"></i></div>
            <h4 style="color: #64748b; font-weight: 600;">No pending requests</h4>
            <p style="color: #94a3b8; font-size: 0.875rem;">Everything is up to date!</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Reason for Update</th>
                    <th>Requested At</th>
                    <th style="width: 250px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($r['student_name']) ?>&background=EEF2FF&color=4F46E5" style="width: 32px; height: 32px; border-radius: 8px;">
                            <div>
                                <div style="font-weight: 600; color: #1e293b;"><?= $r['student_name'] ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= $r['student_email'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="max-width: 300px; font-size: 0.875rem; color: #475569; line-height: 1.4;">
                            "<?= htmlspecialchars($r['reason']) ?>"
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem; color: #64748b;"><?= date('M d, Y h:i A', strtotime($r['created_at'])) ?></div>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <form action="<?= base_url('faculty/profile-requests/manage') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                <input type="hidden" name="action" value="approved">
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #10b981; border: none;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <button type="button" onclick="openRejectModal(<?= $r['id'] ?>, '<?= addslashes($r['student_name']) ?>')" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.8rem; border: none;">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter:blur(8px); align-items:center; justify-content:center; z-index:10000;">
    <div class="modal-card" style="background:white; padding:2rem; border-radius:24px; width:95%; max-width:450px;">
        <h3 style="margin-bottom: 0.5rem; color: #1e293b; font-weight: 800;">Reject Request</h3>
        <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">Rejecting request for: <strong id="rejectStudentName" style="color: #4f46e5;"></strong></p>
        
        <form action="<?= base_url('faculty/profile-requests/manage') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="request_id" id="rejectRequestId">
            <input type="hidden" name="action" value="rejected">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="3" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;" placeholder="e.g. Please provide more specific details about the required changes."></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-light" style="flex: 1; padding: 0.75rem; border-radius: 12px; font-weight: 700;">Cancel</button>
                <button type="submit" class="btn btn-danger" style="flex: 1; padding: 0.75rem; border-radius: 12px; font-weight: 700; background: #ef4444;">Reject Now</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, name) {
        document.getElementById('rejectRequestId').value = id;
        document.getElementById('rejectStudentName').innerText = name;
        document.getElementById('rejectModal').style.display = 'flex';
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
</script>
