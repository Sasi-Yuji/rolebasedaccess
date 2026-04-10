<div class="card" style="margin-bottom: 2rem; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-weight: 800; color: var(--dark);">My Leave History</h2>
        <button onclick="toggleModal(true)" class="btn btn-primary">
            <i class="fas fa-plus"></i> Apply Leave
        </button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Faculty Advisor</th>
                <th>Type</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leaves as $lv): ?>
            <tr>
                <td><?= $lv['faculty_name'] ?></td>
                <td><span style="font-weight: 600;"><?= $lv['leave_type'] ?></span></td>
                <td><?= date('M d', strtotime($lv['start_date'])) ?> to <?= date('M d, Y', strtotime($lv['end_date'])) ?></td>
                <td>
                    <?php
                    $color = $lv['status'] == 'Approved' ? '#22c55e' : ($lv['status'] == 'Rejected' ? '#ef4444' : '#eab308');
                    ?>
                    <span style="color: <?= $color ?>; font-weight: 700;"><?= $lv['status'] ?></span>
                </td>
                <td style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($lv['faculty_remark'] ?: '—') ?></td>
                <td>
                    <?php if ($lv['status'] == 'Pending'): ?>
                        <a href="<?= base_url('student/leave/delete/' . $lv['id']) ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Cancel</a>
                    <?php else: ?>
                        <i class="fas fa-lock" style="color: #cbd5e1;"></i>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Apply Modal -->
<div id="leaveModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 500px; text-align: left;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 700;">Apply for Leave</h3>
        <form action="<?= base_url('student/leave/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Select Faculty Advisor</label>
                <select name="faculty_id" class="form-control" required>
                    <?php foreach ($facultyList as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= $f['name'] ?> (<?= $f['department'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Leave Type</label>
                <select name="leave_type" class="form-control" required>
                    <option value="Casual">Casual Leave</option>
                    <option value="Medical">Medical Leave</option>
                    <option value="On-Duty">On-Duty</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="form-control" required rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="button" onclick="toggleModal(false)" class="btn btn-secondary" style="flex: 1; justify-content: center;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(show) {
        $('#leaveModal').css('display', show ? 'flex' : 'none');
    }

    $(document).ready(function() {
        $('.data-table').DataTable({
            order: [[2, 'desc']]
        });
    });
</script>
