<div class="row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Pending -->
    <div class="card" style="border-left: 4px solid #eab308; background: #fffdf2; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="color: #64748b; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin: 0;">Pending Applications</h5>
            <h2 style="font-size: 1.75rem; margin: 0.25rem 0; font-weight: 900; color: #1e293b;"><?= $stats['pending'] ?></h2>
            <p style="font-size: 0.65rem; color: #a16207; margin: 0;">Awaiting Action</p>
        </div>
        <div style="text-align: right;">
            <div style="opacity: .15; font-size: 2.25rem; color: #a16207;"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>

    <!-- Approved -->
    <div class="card" style="border-left: 4px solid #22c55e; background: #f0fdf4; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="color: #64748b; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin: 0;">Approved Records</h5>
            <h2 style="font-size: 1.75rem; margin: 0.25rem 0; font-weight: 900; color: #1e293b;"><?= $stats['approved'] ?></h2>
            <p style="font-size: 0.65rem; color: #15803d; margin: 0;">Finalized</p>
        </div>
        <div style="text-align: right;">
            <div style="opacity: .15; font-size: 2.25rem; color: #15803d;"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="card" style="border-left: 4px solid #ef4444; background: #fef2f2; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="color: #64748b; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin: 0;">Rejected Submissions</h5>
            <h2 style="font-size: 1.75rem; margin: 0.25rem 0; font-weight: 900; color: #1e293b;"><?= $stats['rejected'] ?></h2>
            <p style="font-size: 0.65rem; color: #b91c1c; margin: 0;">Closed</p>
        </div>
        <div style="text-align: right;">
            <div style="opacity: .15; font-size: 2.25rem; color: #b91c1c;"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
</div>

<div class="card" style="padding: 1.5rem;">
    <h3 style="margin-bottom: 1.5rem; font-weight: 600;">All Leave Requests</h3>
    
    <table id="hodLeaves" class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="padding: 1.25rem 1rem;">Faculty Member</th>
                <th style="padding: 1.25rem 1rem;">Category</th>
                <th style="padding: 1.25rem 1rem;">Date Range</th>
                <th style="padding: 1.25rem 1rem; text-align: center;">Days</th>
                <th style="padding: 1.25rem 1rem; text-align: center;">Action / Status</th>
            </tr>
        </thead>
        <tbody id="leaveTableBody">
            <?php foreach ($pendingLeaves as $lv): ?>
            <tr id="leave-row-<?= $lv['id'] ?>" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                <td style="padding: 1.25rem 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; background: #e0e7ff; color: #4338ca; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                            <?= substr($lv['faculty_name'], 0, 1) ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1e293b; font-size: 0.875rem;"><?= $lv['faculty_name'] ?></div>
                            <div style="font-size: 0.7rem; color: #64748b;"><?= $lv['department'] ?? 'General' ?></div>
                        </div>
                    </div>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    <span style="font-weight: 600; color: #334155; font-size: 0.85rem;"><?= $lv['leave_type'] ?></span>
                    <div style="font-size: 0.7rem; color: #94a3b8; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($lv['reason']) ?>">
                        <?= htmlspecialchars($lv['reason'] ?: 'No details') ?>
                    </div>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-size: 0.85rem; font-weight: 500; color: #475569;">
                        <?= date('d M', strtotime($lv['start_date'])) ?> - <?= date('d M, Y', strtotime($lv['end_date'])) ?>
                    </div>
                </td>
                <td style="padding: 1.25rem 1rem; text-align: center;">
                    <span style="background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; padding: 0.3rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem;">
                        <?= $lv['num_days'] ?>
                    </span>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    <?php if ($lv['status'] === 'Pending'): ?>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <button onclick="resolveLeave(<?= $lv['id'] ?>, 'Approved')" class="btn-action btn-approve" style="background: #22c55e; border: none; color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: transform 0.2s;">
                                Approve
                            </button>
                            <button onclick="resolveLeave(<?= $lv['id'] ?>, 'Rejected')" class="btn-action btn-reject" style="background: #ef4444; border: none; color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: transform 0.2s;">
                                Reject
                            </button>
                        </div>
                    <?php else:
                        $bColor = $lv['status'] === 'Approved' ? '#dcfce7' : '#fee2e2';
                        $tColor = $lv['status'] === 'Approved' ? '#166534' : '#991b1b';
                        $icon   = $lv['status'] === 'Approved' ? 'fa-check-circle' : 'fa-times-circle';
                    ?>
                        <div style="text-align: center;">
                            <span style="background: <?= $bColor ?>; color: <?= $tColor ?>; padding: .35rem .75rem; border-radius: 99px; font-size: .72rem; font-weight: 700; display: inline-flex; align-items: center; gap: .35rem;">
                                <i class="fas <?= $icon ?>"></i> <?= $lv['status'] ?>
                            </span>
                            <?php if (!empty($lv['hod_remark'])): ?>
                                <div style="font-size: .65rem; color: #94a3b8; margin-top: .3rem; font-style: italic; max-width: 140px; margin-inline: auto;">"<?= htmlspecialchars($lv['hod_remark']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pendingLeaves)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    No leave requests found.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function resolveLeave(id, status) {
        const isReject = status === 'Rejected';

        if (isReject) {
            Swal.fire({
                title: '❌ Reject Leave',
                html: `<p style="color:#64748b;font-size:.9rem;margin-bottom:.75rem">Provide a reason for rejection (shown to faculty).</p>
                       <textarea id="hodRemark" rows="3" class="swal2-input" style="width:90%;resize:none;font-size:.85rem;" placeholder="e.g. Already on duty during this period…"></textarea>`,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Reject & Notify',
                preConfirm: () => document.getElementById('hodRemark').value.trim()
            }).then(result => {
                if (result.isConfirmed) {
                    submitResolve(id, status, result.value);
                }
            });
        } else {
            Swal.fire({
                title: '✅ Approve Leave?',
                text: 'This will notify the faculty and finalize the request.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Approve'
            }).then(result => {
                if (result.isConfirmed) submitResolve(id, status, '');
            });
        }
    }

    function submitResolve(id, status, remark) {
        // Show loading spinner immediately
        Swal.fire({
            title: status === 'Approved' ? 'Approving…' : 'Rejecting…',
            html: '<p style="color:#64748b;font-size:.9rem;">Please wait while we process and notify the faculty.</p>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.post('<?= base_url('hod/leave/manage') ?>', {
            id, status,
            hod_remark: remark,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.success) {
                Swal.fire('Done!', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        });
    }
</script>

<style>
    .btn-action:hover { transform: translateY(-1px); filter: brightness(1.1); }
    .data-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        padding: 1.25rem 1rem;
    }
    /* Blur only for centered modal popups — swal2-toast-shown is on body, NOT the container */
    body:not(.swal2-toast-shown) .swal2-backdrop-show {
        backdrop-filter: blur(8px);
        background: rgba(15, 23, 42, 0.4) !important;
    }
</style>
