<?php
$remaining    = 12 - $yearlyUsage;
$donutPct     = max(0, min(100, ($remaining / 12) * 100));
$donutColor   = $remaining >= 6 ? '#22c55e' : ($remaining >= 3 ? '#eab308' : '#ef4444');
$donutDash    = round(2 * 3.14159 * 36 * $donutPct / 100);
$donutGap     = 226 - $donutDash;

// Build calendar event data for JS
$calEvents = [];
foreach ($leaves as $lv) {
    $calEvents[] = [
        'start'  => $lv['start_date'],
        'end'    => $lv['end_date'],
        'type'   => $lv['leave_type'],
        'status' => $lv['status'],
    ];
}
?>

<!-- ── TOP STATS ROW ─────────────────────────────── -->
<div style="display:grid;grid-template-columns:repeat(3, 1fr) 180px 240px;gap:1.25rem;margin-bottom:1.75rem;padding:0 1rem;align-items:stretch;">

    <!-- Monthly CL Usage -->
    <div class="card" style="border-left:4px solid #eab308;background:#fffdf2;padding:1rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <div style="font-size:0.65rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.05em;">This Month (CL)</div>
            <div style="font-size:1.75rem;font-weight:900;color:#1e293b;margin:.25rem 0;"><?= $monthlyUsage ?><span style="font-size:0.85rem;font-weight:500;color:#94a3b8;">/1</span></div>
            <?php if($monthlyUsage >= 1): ?>
                <div style="font-size:0.65rem;color:#991b1b;font-weight:700;"><i class="fas fa-exclamation-triangle"></i> LOP Applies</div>
            <?php else: ?>
                <div style="font-size:0.65rem;color:#854d0e;">Available</div>
            <?php endif; ?>
        </div>
        <div style="opacity:0.15;font-size:2.5rem;color:#92400e;"><i class="fas fa-calendar-check"></i></div>
    </div>

    <!-- Yearly Usage -->
    <div class="card" style="border-left:4px solid #6366f1;background:#f5f3ff;padding:1rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <div style="font-size:0.65rem;font-weight:700;color:#4338ca;text-transform:uppercase;letter-spacing:.05em;">Total Taken</div>
            <div style="font-size:1.75rem;font-weight:900;color:#1e293b;margin:.25rem 0;"><?= $yearlyUsage ?><span style="font-size:0.85rem;font-weight:500;color:#94a3b8;">/12</span></div>
            <div style="font-size:0.65rem;color:#6366f1;">Taken so far</div>
        </div>
        <div style="opacity:0.1;font-size:2.5rem;color:#4338ca;"><i class="fas fa-chart-pie"></i></div>
    </div>

    <!-- All Requests -->
    <div class="card" style="border-left:4px solid #0ea5e9;background:#f0f9ff;padding:1rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <div style="font-size:0.65rem;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:.05em;">History</div>
            <div style="font-size:1.75rem;font-weight:900;color:#1e293b;margin:.25rem 0;"><?= count($leaves) ?></div>
            <div style="font-size:0.65rem;color:#0369a1;">Total requests</div>
        </div>
        <div style="text-align:right;">
            <?php 
                $appr = array_filter($leaves, fn($l) => $l['status'] == 'Approved');
                $pend = array_filter($leaves, fn($l) => $l['status'] == 'Pending');
            ?>
            <div style="font-size:0.6rem;font-weight:700;color:#16a34a;"><?= count($appr) ?> Approved</div>
            <div style="font-size:0.6rem;font-weight:700;color:#ca8a04;"><?= count($pend) ?> Pending</div>
        </div>
    </div>

    <!-- Progress Donut -->
    <div class="card" style="padding:.75rem;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;">
        <svg width="64" height="64" viewBox="0 0 88 88">
            <circle cx="44" cy="44" r="36" fill="none" stroke="#f1f5f9" stroke-width="10"/>
            <circle cx="44" cy="44" r="36" fill="none" stroke="<?= $donutColor ?>" stroke-width="10"
                stroke-dasharray="<?= $donutDash ?> <?= $donutGap ?>"
                stroke-linecap="round"
                transform="rotate(-90 44 44)"/>
            <text x="44" y="48" text-anchor="middle" font-size="18" font-weight="900" fill="#1e293b"><?= $remaining ?></text>
        </svg>
        <div style="font-size:0.6rem;font-weight:700;color:#64748b;text-transform:uppercase;">CL Left</div>
    </div>

    <!-- RELATABLE CONTENT: Next Holidays -->
    <div class="card" style="padding:1rem;background:linear-gradient(to bottom right, #ffffff, #f8fafc);border-bottom:3px solid #cbd5e1;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
            <div style="font-size:0.65rem;font-weight:800;color:#64748b;text-transform:uppercase;">Next Holidays</div>
            <i class="fas fa-umbrella-beach" style="color:#94a3b8;font-size:.8rem;"></i>
        </div>
        <div style="display:flex;flex-direction:column;gap:.4rem;">
            <div style="display:flex;justify-content:space-between;font-size:.7rem;">
                <span style="font-weight:600;color:#1e293b;">EID AL-FITR</span>
                <span style="color:#64748b;font-weight:700;">10 APR</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.7rem;">
                <span style="font-weight:600;color:#1e293b;">VISHU</span>
                <span style="color:#64748b;font-weight:700;">14 APR</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.7rem;">
                <span style="font-weight:600;color:#1e293b;">BENGALI NEW YEAR</span>
                <span style="color:#64748b;font-weight:700;">15 APR</span>
            </div>
        </div>
    </div>
</div>

<!-- ── MAIN GRID: Calendar + Leave History ──────── -->
<div style="display:flex;gap:1.5rem;padding:0 1rem;margin-bottom:1.75rem;">

    <!-- Mini Calendar -->
    <div class="card" style="width:300px;flex-shrink:0;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <button onclick="calPrev()" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:1rem;"><i class="fas fa-chevron-left"></i></button>
            <span id="calTitle" style="font-weight:700;font-size:0.9rem;color:#1e293b;"></span>
            <button onclick="calNext()" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:1rem;"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div id="calGrid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;font-size:0.7rem;text-align:center;"></div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:1rem;font-size:0.65rem;">
            <span style="display:inline-flex;align-items:center;gap:3px;"><span style="width:10px;height:10px;background:#fef9c3;border-radius:2px;display:inline-block;"></span>Pending</span>
            <span style="display:inline-flex;align-items:center;gap:3px;"><span style="width:10px;height:10px;background:#dcfce7;border-radius:2px;display:inline-block;"></span>Approved</span>
            <span style="display:inline-flex;align-items:center;gap:3px;"><span style="width:10px;height:10px;background:#fee2e2;border-radius:2px;display:inline-block;"></span>Rejected</span>
        </div>
    </div>

    <!-- Student Leave Requests (New Section) -->
    <div class="card" style="flex:1;padding:1.5rem;overflow:auto;display:flex;flex-direction:column;gap:1.25rem;background:#fcfaff;border-top:4px solid #7c3aed;">
        <h3 style="margin:0;font-weight:800;font-size:1.15rem;color:#1e293b;text-align:center;">Students Awaiting Approval</h3>
        <?php if(empty($studentLeaves)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:0.9rem;">
                <i class="fas fa-check-circle" style="font-size:2rem;display:block;margin-bottom:1rem;opacity:0.3;"></i>
                All student requests handled.
            </div>
        <?php else: ?>
            <table class="data-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Type & Dates</th>
                        <th>Reason</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($studentLeaves as $sl): ?>
                        <tr>
                            <td>
                                <div style="font-weight:700;color:#1e293b;"><?= $sl['student_name'] ?></div>
                                <div style="font-size:0.7rem;color:#64748b;">#STU-<?= $sl['student_id'] ?></div>
                            </td>
                            <td>
                                <span style="font-weight:600;font-size:0.8rem;"><?= $sl['leave_type'] ?></span>
                                <div style="font-size:0.75rem;color:#475569;"><?= date('M d', strtotime($sl['start_date'])) ?> - <?= date('M d', strtotime($sl['end_date'])) ?></div>
                            </td>
                            <td style="font-size:0.8rem;color:#334155;max-width:200px;"><?= htmlspecialchars($sl['reason']) ?></td>
                            <td style="text-align:right;">
                                <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                                    <button onclick="manageStudentLeave(<?= $sl['id'] ?>, 'Approved')" class="btn btn-primary" style="padding:0.4rem 0.8rem;font-size:0.75rem;background:#22c55e;">Approve</button>
                                    <button onclick="manageStudentLeave(<?= $sl['id'] ?>, 'Rejected')" class="btn btn-danger" style="padding:0.4rem 0.8rem;font-size:0.75rem;">Reject</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Leave History Table -->
    <div class="card" style="flex:1;padding:1.5rem;overflow:auto;display:flex;flex-direction:column;gap:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div style="flex:1;"></div>
            <h3 style="margin:0;font-weight:800;font-size:1.15rem;color:#1e293b;text-align:center;flex:2;">My Leave History</h3>
            <div style="flex:1;display:flex;justify-content:flex-end;">
                <button id="fabBtn" onclick="openDrawer()"
                    style="padding:0.6rem 1.25rem;border-radius:12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;font-size:0.85rem;font-weight:800;cursor:pointer;box-shadow:0 4px 12px rgba(79,70,229,.2);display:flex;align-items:center;gap:0.6rem;transition:transform .2s,box-shadow .2s;">
                    <i class="fas fa-plus" style="font-size: 0.9rem;"></i>
                    <span>Apply Leave</span>
                </button>
            </div>
        </div>
        <table class="data-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="padding:.75rem;">Category</th>
                    <th style="padding:.75rem;">Duration</th>
                    <th style="padding:.75rem;text-align:center;">Days</th>
                    <th style="padding:.75rem;">Status / Remark</th>
                    <th style="padding:.75rem;width:80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaves as $lv): ?>
                <tr>
                    <td style="padding:.9rem .75rem;">
                        <span style="font-weight:700;color:#1e293b;"><?= $lv['leave_type'] ?></span>
                        <div style="font-size:0.7rem;color:#94a3b8;"><?= htmlspecialchars($lv['reason'] ?: '—') ?></div>
                    </td>
                    <td style="padding:.9rem .75rem;font-size:.82rem;font-weight:500;">
                        <?= date('d M', strtotime($lv['start_date'])) ?> → <?= date('d M, Y', strtotime($lv['end_date'])) ?>
                    </td>
                    <td style="padding:.9rem .75rem;text-align:center;">
                        <span style="background:#f1f5f9;padding:.25rem .55rem;border-radius:6px;font-weight:800;font-size:.8rem;"><?= $lv['num_days'] ?></span>
                    </td>
                    <td style="padding:.9rem .75rem;">
                        <?php
                        $bColor = $lv['status']=='Approved' ? '#dcfce7' : ($lv['status']=='Rejected' ? '#fee2e2' : '#fef9c3');
                        $tColor = $lv['status']=='Approved' ? '#166534' : ($lv['status']=='Rejected' ? '#991b1b' : '#854d0e');
                        ?>
                        <span style="background:<?= $bColor ?>;color:<?= $tColor ?>;padding:.25rem .6rem;border-radius:99px;font-size:.7rem;font-weight:700;"><?= $lv['status'] ?></span>
                        <?php if (!empty($lv['hod_remark'])): ?>
                            <div style="font-size:.68rem;color:#64748b;margin-top:.25rem;font-style:italic;">"<?= htmlspecialchars($lv['hod_remark']) ?>"</div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:.9rem .75rem;">
                        <?php if ($lv['status'] == 'Pending'): ?>
                        <div style="display:flex;gap:.3rem;">
                            <button onclick="openEditDrawer(<?= htmlspecialchars(json_encode($lv)) ?>)"
                                style="background:#f59e0b;color:#fff;border:none;padding:.3rem .5rem;border-radius:4px;cursor:pointer;font-size:.75rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="showConfirm('Cancel Leave','Cancel this request?','<?= base_url('faculty/leave/delete/'.$lv['id']) ?>')"
                                style="background:#ef4444;color:#fff;border:none;padding:.3rem .5rem;border-radius:4px;cursor:pointer;font-size:.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <?php else: ?>
                            <i class="fas fa-lock" style="color:#cbd5e1;" title="Locked"></i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- ── SIDE DRAWER ──────────────────────────────── -->
<div id="drawerOverlay" onclick="closeDrawer()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1001;backdrop-filter:blur(4px);"></div>

<div id="leaveDrawer"
    style="position:fixed;top:0;right:-440px;width:420px;height:100vh;background:#fff;z-index:1002;box-shadow:-8px 0 40px rgba(0,0,0,.15);transition:right .35s cubic-bezier(.4,0,.2,1);overflow-y:auto;padding:2rem;display:flex;flex-direction:column;gap:1rem;">

    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h3 id="drawerTitle" style="font-weight:800;font-size:1.15rem;color:#1e293b;">Apply Leave</h3>
        <button onclick="closeDrawer()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#64748b;"><i class="fas fa-times"></i></button>
    </div>

    <form id="leaveForm" action="<?= base_url('faculty/leave/store') ?>" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
        <?= csrf_field() ?>
        <input type="hidden" name="leave_id" id="leave_id">
        <input type="hidden" name="skip_weekends" id="skip_weekends_hidden" value="0">

        <div>
            <label style="font-weight:600;font-size:.85rem;">Leave Category</label>
            <select name="leave_type" id="leave_type" class="form-control" style="width:100%;margin-top:.4rem;" required>
                <option value="Casual">Casual Leave (CL)</option>
                <option value="Sick">Sick Leave (SL)</option>
                <option value="On Duty">On Duty (OD)</option>
            </select>
        </div>

        <div style="display:flex;gap:.75rem;">
            <div style="flex:1;">
                <label style="font-weight:600;font-size:.85rem;">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" style="width:100%;margin-top:.4rem;" required>
            </div>
            <div style="flex:1;">
                <label style="font-weight:600;font-size:.85rem;">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" style="width:100%;margin-top:.4rem;" required>
            </div>
        </div>

        <!-- Weekend Skip Toggle -->
        <div style="display:flex;align-items:center;gap:.75rem;background:#f8fafc;border-radius:10px;padding:.75rem 1rem;border:1px solid #e2e8f0;">
            <label class="switch" style="position:relative;display:inline-block;width:38px;height:20px;flex-shrink:0;">
                <input type="checkbox" id="skipWeekends" style="opacity:0;width:0;height:0;">
                <span style="position:absolute;cursor:pointer;inset:0;background:#cbd5e1;border-radius:34px;transition:.3s;"></span>
            </label>
            <div>
                <div style="font-size:.82rem;font-weight:600;color:#334155;">Skip Weekends</div>
                <div style="font-size:.7rem;color:#94a3b8;">Auto-exclude Sat &amp; Sun from count</div>
            </div>
            <span id="daysCounter" style="margin-left:auto;background:#4f46e5;color:#fff;padding:.3rem .7rem;border-radius:99px;font-size:.72rem;font-weight:700;display:none;"></span>
        </div>

        <div>
            <label style="font-weight:600;font-size:.85rem;">Note / Description</label>
            <input type="text" name="reason" id="reason" class="form-control" style="width:100%;margin-top:.4rem;" placeholder="Optional details…">
        </div>

        <div style="display:flex;gap:.5rem;margin-top:.5rem;">
            <button type="button" id="cancelEdit" onclick="resetDrawer()" style="display:none;flex:1;background:#f1f5f9;color:#475569;border:none;padding:.75rem;border-radius:8px;font-weight:600;cursor:pointer;">Cancel</button>
            <button type="submit" id="submitBtn" style="flex:1;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;padding:.75rem;border-radius:8px;font-weight:700;cursor:pointer;font-size:.95rem;">Apply Now</button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ─── DataTables ───────────────────────────────────
$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none'; // Fix: Disable annoying alert messages
    $('.data-table').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        "pageLength": 2,
        "lengthMenu": [2, 5, 10, 25]
    });
});

// ─── Drawer ───────────────────────────────────────
function openDrawer() {
    document.getElementById('leaveDrawer').style.right = '0';
    document.getElementById('drawerOverlay').style.display = 'block';
    document.getElementById('drawerTitle').innerText = 'Apply Leave';
}
function closeDrawer() {
    document.getElementById('leaveDrawer').style.right = '-440px';
    document.getElementById('drawerOverlay').style.display = 'none';
}
function openEditDrawer(data) {
    openDrawer();
    document.getElementById('drawerTitle').innerText = 'Edit Leave';
    document.getElementById('leave_id').value   = data.id;
    document.getElementById('leave_type').value = data.leave_type;
    document.getElementById('start_date').value = data.start_date;
    document.getElementById('end_date').value   = data.end_date;
    document.getElementById('reason').value     = data.reason || '';
    document.getElementById('cancelEdit').style.display = 'block';
    document.getElementById('submitBtn').innerText = 'Update';
    calculateDays();
}
function resetDrawer() {
    document.getElementById('leaveForm').reset();
    document.getElementById('leave_id').value = '';
    document.getElementById('cancelEdit').style.display = 'none';
    document.getElementById('submitBtn').innerText = 'Apply Now';
    document.getElementById('daysCounter').style.display = 'none';
}

// ─── Days Counter + Weekend Skip ─────────────────
const startIn = document.getElementById('start_date');
const endIn   = document.getElementById('end_date');
const skipChk = document.getElementById('skipWeekends');
const counter = document.getElementById('daysCounter');

// Toggle slider CSS via JS (reuse existing switch CSS if available)
skipChk.addEventListener('change', function() {
    const span = this.nextElementSibling;
    span.style.background = this.checked ? '#4f46e5' : '#cbd5e1';
    document.getElementById('skip_weekends_hidden').value = this.checked ? '1' : '0';
    calculateDays();
});

function countWorkdays(start, end) {
    let count = 0, curr = new Date(start);
    while (curr <= end) {
        const d = curr.getDay();
        if (d !== 0 && d !== 6) count++;
        curr.setDate(curr.getDate() + 1);
    }
    return count;
}

function calculateDays() {
    if (!startIn.value || !endIn.value) { counter.style.display='none'; return; }
    const s = new Date(startIn.value), e = new Date(endIn.value);
    if (e < s) { counter.style.display='none'; return; }
    const days = skipChk.checked
        ? countWorkdays(s, e)
        : Math.floor((e - s) / 86400000) + 1;
    counter.innerText = days + (days === 1 ? ' Day' : ' Days');
    counter.style.display = 'block';
}

startIn.addEventListener('change', function() {
    endIn.setAttribute('min', this.value);
    if (endIn.value && endIn.value < this.value) endIn.value = this.value;
    calculateDays();
});
endIn.addEventListener('change', calculateDays);

// Min date = today
const today = new Date().toISOString().split('T')[0];
startIn.setAttribute('min', today);
endIn.setAttribute('min', today);

// ─── Confirm Dialog ───────────────────────────────
function showConfirm(title, text, url) {
    Swal.fire({ title, text, icon:'warning', showCancelButton:true,
        confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280', confirmButtonText:'Yes, proceed'
    }).then(r => { if (r.isConfirmed) window.location.href = url; });
}

// ─── Mini Calendar ────────────────────────────────
const calEvents = <?= json_encode($calEvents) ?>;
let calYear = new Date().getFullYear();
let calMonth = new Date().getMonth();

const statusColors = {
    Pending:  { bg: '#fef9c3', border: '#ca8a04' },
    Approved: { bg: '#dcfce7', border: '#16a34a' },
    Rejected: { bg: '#fee2e2', border: '#dc2626' },
};

function renderCal() {
    const days = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    document.getElementById('calTitle').innerText = `${months[calMonth]} ${calYear}`;

    const grid = document.getElementById('calGrid');
    grid.innerHTML = '';

    // Day headers
    days.forEach(d => {
        const el = document.createElement('div');
        el.innerText = d;
        el.style.cssText = 'font-weight:700;color:#94a3b8;padding:4px 0;font-size:.65rem;';
        grid.appendChild(el);
    });

    const first = new Date(calYear, calMonth, 1).getDay();
    const total = new Date(calYear, calMonth + 1, 0).getDate();

    for (let i = 0; i < first; i++) {
        grid.appendChild(document.createElement('div'));
    }
    for (let d = 1; d <= total; d++) {
        const dateStr = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const el = document.createElement('div');
        el.innerText = d;
        el.style.cssText = 'padding:4px 2px;border-radius:4px;cursor:default;font-size:.7rem;';

        // Prioritize: Rejected > Approved > Pending
        const dayEvts = calEvents.filter(e => dateStr >= e.start && dateStr <= e.end);
        if (dayEvts.length > 0) {
            let evt = dayEvts.find(e => e.status === 'Rejected') || 
                      dayEvts.find(e => e.status === 'Approved') || 
                      dayEvts[0];
            
            const c = statusColors[evt.status] || {};
            el.style.background = c.bg;
            el.style.outline = `1.5px solid ${c.border}`;
            el.style.fontWeight = '700';
            el.title = evt.type + ' — ' + evt.status;
        }

        // today highlight
        if (dateStr === today) {
            el.style.outline = '2px solid #4f46e5';
            el.style.fontWeight = '900';
        }
        grid.appendChild(el);
    }
}
function calPrev() { calMonth--; if(calMonth<0){calMonth=11;calYear--;} renderCal(); }
function calNext() { calMonth++; if(calMonth>11){calMonth=0;calYear++;} renderCal(); }
renderCal();

async function manageStudentLeave(id, status) {
    const { value: remark } = await Swal.fire({
        title: `Confirm ${status}`,
        input: 'text',
        inputLabel: 'Staff Remark (Optional)',
        inputPlaceholder: 'Enter any comments here...',
        showCancelButton: true,
        confirmButtonColor: status === 'Approved' ? '#22c55e' : '#ef4444',
    });

    if (remark !== undefined) {
        $.post('<?= base_url('faculty/leave/manage_student') ?>', {
            id: id,
            status: status,
            remark: remark,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            Swal.fire('Success', `Student leave has been ${status.toLowerCase()}. Email sent.`, 'success')
                .then(() => location.reload());
        });
    }
}
</script>

<style>
#fabBtn:hover { transform:scale(1.05); box-shadow:0 12px 28px rgba(79,70,229,.55); }
.switch input:checked + span { background:#4f46e5 !important; }
/* Blur only for centered modal popups — swal2-toast-shown is on body, NOT the container */
body:not(.swal2-toast-shown) .swal2-backdrop-show {
    backdrop-filter: blur(6px);
    background: rgba(15, 23, 42, 0.35) !important;
}
</style>

<?php if (session()->getFlashdata('leave_swal')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '<?= session()->getFlashdata('leave_swal') ?>',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#f0fdf4',
        color: '#166534'
    });
});
</script>
<?php endif; ?>
