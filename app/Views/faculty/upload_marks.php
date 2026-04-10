<div style="display: flex; gap: 2rem;">
    <!-- Record Marks -->
    <div class="card" style="flex: 2;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0;">Student Performance | <span style="color: var(--primary);"><?= $subject['subject_name'] ?></span></h3>
            <span style="font-size: 0.75rem; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 0.4rem 0.8rem; border-radius: 20px;"><?= count($existingMarks) ?> Records Found</span>
        </div>

            <table id="marks-table" class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email Address</th>
                        <th>Submitted Date</th>
                        <th>Result / 100</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
            </thead>
            <tbody>
                <?php 
                $mergedList = [];
                // 1. Students with existing marks
                foreach ($existingMarks as $m) {
                    $sSub = array_filter($submissions, function($s) use ($m) { return $s['student_id'] == $m['student_id']; });
                    $subDate = (!empty($sSub)) ? array_values($sSub)[0]['created_at'] : 'N/A';
                    
                    $mergedList[$m['student_id']] = [
                        'student_id' => $m['student_id'],
                        'student_name' => $m['student_name'],
                        'email' => $m['email'],
                        'marks' => $m['marks'],
                        'id' => $m['id'],
                        'submitted_date' => $subDate
                    ];
                }
                // 2. Students with submissions but NO marks
                foreach ($submissions as $sub) {
                    if (!isset($mergedList[$sub['student_id']])) {
                        $stu = array_filter($students, function($s) use ($sub) { return $s['id'] == $sub['student_id']; });
                        if (!empty($stu)) {
                            $stu = array_values($stu)[0];
                            $mergedList[$sub['student_id']] = [
                                'student_id' => $stu['id'],
                                'student_name' => $stu['name'],
                                'email' => $stu['email'],
                                'marks' => null, // indicates pending
                                'id' => null,
                                'submitted_date' => $sub['created_at']
                            ];
                        }
                    }
                }
                ?>
                <?php foreach ($mergedList as $m): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?= $m['student_name'] ?></div>
                    </td>
                    <td><span style="font-size: 0.8rem; color: #64748b;"><?= $m['email'] ?></span></td>
                    <td>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 500; white-space: nowrap;">
                            <?= ($m['submitted_date'] !== 'N/A') ? '<i class="far fa-calendar-alt"></i> ' . date('d M Y, h:i A', strtotime($m['submitted_date'])) : '<span style="color: #cbd5e1;">No Submission</span>' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($m['marks'] !== null): ?>
                            <span class="badge badge-student" style="font-size: 0.9rem; padding: 0.4rem 1rem; font-weight: 700; background: #eef2ff; color: #4f46e5;"><?= $m['marks'] ?></span>
                        <?php else: ?>
                            <span class="badge" style="font-size: 0.8rem; padding: 0.3rem 0.8rem; font-weight: 700; background: #fef3c7; color: #d97706; border-radius: 12px;"><i class="fas fa-clock"></i> Pending Grade</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            <?php 
                                $studentSub = array_filter($submissions, function($s) use ($m) {
                                    return $s['student_id'] == $m['student_id'];
                                });
                            ?>
                            <?php if (!empty($studentSub)): ?>
                                <button onclick="viewSubmission(<?= $m['student_id'] ?>, '<?= addslashes($m['student_name']) ?>')" class="btn-action" title="View Answer Sheet" style="background: #eef2ff; color: #4f46e5; height: 32px; padding: 0 0.75rem; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fas fa-eye"></i> View Answers (<?= count($studentSub) ?>)
                                </button>
                            <?php endif; ?>

                            <?php if ($m['marks'] !== null): ?>
                                <button onclick="editMark(<?= $m['student_id'] ?>, <?= $m['marks'] ?>)" class="btn-action btn-edit" title="Edit Mark" style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;">
                                    <i class="fas fa-edit" style="font-size: 0.85rem;"></i>
                                </button>
                                <button onclick="window.showConfirm('Delete Marks', 'Are you sure you want to remove the marks for <?= addslashes($m['student_name']) ?>? This action is permanent.', '<?= base_url('faculty/marks/delete/' . $m['id']) ?>')" class="btn-action btn-delete" title="Delete Mark" style="background: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;">
                                    <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="editMark(<?= $m['student_id'] ?>, '')" class="btn-action" title="Enter Grade" style="background: #ecfdf5; color: #10b981; height: 32px; padding: 0 0.75rem; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; transition: all 0.2s;">
                                    <i class="fas fa-pen"></i> Add Grade
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($mergedList)): ?>
                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 4rem 2rem;">
                        <i class="fas fa-folder-open" style="font-size: 2.5rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                        No submissions or marks recorded for this subject yet.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Upload/Update Form -->
    <div class="card" style="flex: 1; height: fit-content; border: 1px solid #e2e8f0; background: #f8fafc;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 700; color: #1e293b;">Grade Student</h3>
        <form id="marksForm" action="<?= base_url('faculty/marks/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
            
            <div class="form-group">
                <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Select Student</label>
                <select name="student_id" id="studentSelect" class="form-control" required style="border-radius: 12px; height: 3rem;">
                    <option value="">Select...</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?> (<?= $s['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-top: 1.5rem;">
                <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Marks (0-100)</label>
                <input type="number" name="marks" id="marksInput" class="form-control no-spinners" min="0" max="100" required placeholder="0-100" style="border-radius: 12px; height: 3rem; font-size: 1.25rem; font-weight: 700; text-align: center;">
            </div>
            
            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">Save & Update Record</button>
            </div>
        </form>
    </div>
</div>

<style>
    .badge { display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 700; height: fit-content; }
    .btn-edit:hover { background: #e2e8f0 !important; color: #1e293b !important; transform: translateY(-2px); }
    .btn-delete:hover { background: #fecaca !important; transform: translateY(-2px); }
    .data-table th { background: #f8fafc; color: #475569; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid #f1f5f9; text-align: left; }
    .data-table td { border-bottom: 1px solid #f1f5f9; padding: 1rem; vertical-align: middle; }
    .btn-action { flex-shrink: 0; white-space: nowrap; }
    .dataTables_wrapper .dataTables_filter input { border-radius: 8px; border: 1px solid #e2e8f0; padding: 0.5rem; margin-left: 0.5rem; }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    function editMark(studentId, marks) {
        document.getElementById('studentSelect').value = studentId;
        document.getElementById('marksInput').value = marks;
        document.getElementById('marksInput').focus();
        
        // Visual feedback
        const form = document.querySelector('#marksForm').parentElement;
        form.style.borderColor = 'var(--primary)';
        form.style.background = '#eef2ff';
        setTimeout(() => {
            form.style.borderColor = '#e2e8f0';
            form.style.background = '#f8fafc';
        }, 1000);
    }

    $(document).ready(function() {
        const validator = new FormValidator();
        validator.lockMarksField('#marksInput');

        $('#marks-table').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            language: {
                search: "",
                searchPlaceholder: "Search students...",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });

    function viewSubmission(studentId, studentName) {
        $.ajax({
            url: `<?= base_url('faculty/marks/submission') ?>/${studentId}/<?= $subject['id'] ?>`,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.images.length > 0) {
                    let html = '';
                    res.images.forEach((img, idx) => {
                        html += `
                        <div style="margin-bottom: 2rem; border: 1px solid #eee; border-radius: 12px; overflow: hidden;">
                            <div style="background: #f8fafc; padding: 0.5rem 1rem; border-bottom: 1px solid #eee; font-weight: 700; font-size: 0.75rem; color: #64748b;">PAGE ${img.page_number}</div>
                            <img src="<?= base_url() ?>${img.image_path}" style="width: 100%; display: block;">
                        </div>`;
                    });
                    
                    document.getElementById('submission-content').innerHTML = html;
                    document.getElementById('modal-student-name').textContent = studentName;
                    document.getElementById('viewer-modal').style.display = 'flex';
                } else {
                    alert('No images found for this submission.');
                }
            }
        });
    }

    function closeViewer() {
        document.getElementById('viewer-modal').style.display = 'none';
        document.getElementById('submission-content').innerHTML = '';
    }

    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        
        const images = document.querySelectorAll('#submission-content img');
        if (images.length === 0) return;
        
        const btn = document.getElementById('pdf-btn');
        const origText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.disabled = true;

        // Small timeout to allow UI update
        await new Promise(r => setTimeout(r, 100));

        try {
            for (let i = 0; i < images.length; i++) {
                if (i > 0) pdf.addPage();
                
                const img = images[i];
                const canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth || img.width;
                canvas.height = img.naturalHeight || img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                
                const imgData = canvas.toDataURL('image/jpeg', 0.9);
                pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pageHeight);
            }
            
            const studentName = document.getElementById('modal-student-name').textContent;
            pdf.save(`${studentName.replace(/[^a-zA-Z0-9]/g, '_')}_AnswerSheet.pdf`);
        } catch (error) {
            console.error('PDF Generation Failed', error);
            alert('Failed to generate PDF. Check console.');
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }
</script>

<!-- Submission Viewer Modal -->
<div id="viewer-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div style="background: white; width: 95%; max-width: 900px; height: 90vh; border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="padding: 1.5rem 2.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <div>
                <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Answer Sheet Submission</h3>
                <p style="color: #4f46e5; font-size: 0.875rem; font-weight: 700; margin: 0;" id="modal-student-name"></p>
            </div>
            <button onclick="closeViewer()" style="width: 40px; height: 40px; border-radius: 12px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-times"></i></button>
        </div>
        <div id="submission-content" style="flex: 1; overflow-y: auto; padding: 2.5rem; background: #f8fafc;">
            <!-- Images injected here -->
        </div>
        <div style="padding: 1.5rem 2.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: center; gap: 1rem; background: #fff;">
            <button id="pdf-btn" onclick="downloadPDF()" class="btn" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; background: #ecfdf5; color: #10b981; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;"><i class="fas fa-file-pdf"></i> Download as PDF</button>
            <button onclick="closeViewer()" class="btn btn-primary" style="padding: 0.75rem 3rem; border-radius: 12px; font-weight: 700;">Close Viewer</button>
        </div>
    </div>
</div>
