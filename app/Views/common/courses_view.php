<div class="glass-card" style="padding: 2.5rem; margin-top: 1rem; border-radius: 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.04); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.8);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;"><?= $title ?></h2>
            <p style="color: #64748b; font-size: 0.95rem; font-weight: 500;">
                <i class="fas fa-layer-group" style="margin-right: 0.5rem;"></i>
                <?php if ($role === 'student'): ?>
                    Explore available courses and academic modules.
                <?php else: ?>
                    Viewing active courses for department: <span style="color: var(--primary); font-weight: 700;"><?= session()->get('department') ?></span>
                <?php endif; ?>
            </p>
        </div>
        <?php if ($role !== 'admin'): ?>
        <div style="background: #eef2ff; color: #4f46e5; padding: 0.6rem 1.25rem; border-radius: 12px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.5rem; border: 1px solid #c7d2fe; letter-spacing: 0.05em;">
            <i class="fas fa-lock"></i> READ ONLY ACCESS
        </div>
        <?php endif; ?>
    </div>

    <div class="table-responsive" style="overflow-x: auto;">
        <table id="commonCoursesTable" class="modern-data-table" style="width:100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border-radius: 12px 0 0 12px;">CODE</th>
                    <th>COURSE NAME</th>
                    <th>SEM</th>
                    <th>CREDITS</th>
                    <th>STATUS</th>
                    <?php if ($role === 'faculty'): ?>
                        <th style="text-align: right; border-radius: 0 12px 12px 0;">QUICK ACTIONS</th>
                    <?php else: ?>
                        <th style="border-radius: 0 12px 12px 0;"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td>
                        <div style="font-weight: 800; color: #1e293b; background: #f1f5f9; display: inline-block; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem;"><?= $course['course_code'] ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #334155; font-size: 1rem;"><?= $course['course_name'] ?></div>
                    </td>
                    <td>
                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; border-radius: 8px;">Semester <?= $course['semester'] ?></span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #64748b;">
                            <i class="fas fa-star" style="color: #fbbf24; font-size: 0.8rem;"></i> <?= $course['credits'] ?> Units
                        </div>
                    </td>
                    <td>
                        <?php if ($course['status'] === 'Active'): ?>
                            <span class="badge" style="background: #ecfdf5; color: #10b981; font-weight: 700; border-radius: 8px;">
                                <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i> <?= $course['status'] ?>
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background: #fef2f2; color: #ef4444; font-weight: 700; border-radius: 8px;">
                                <i class="fas fa-times-circle" style="margin-right: 0.25rem;"></i> <?= $course['status'] ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <?php if ($role === 'faculty'): ?>
                        <td style="text-align: right;">
                            <?php 
                                $courseSlug = strtolower($course['course_name']);
                                $subjectId = $assignedSubjectIds[$courseSlug] ?? null;
                            ?>
                            <?php if ($subjectId): ?>
                                <a href="<?= base_url('faculty/marks/upload/' . $subjectId) ?>" class="btn-action" style="background: #4f46e5; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                                    <i class="fas fa-graduation-cap"></i> Grade Students
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic; font-weight: 500;">No Action Available</span>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .modern-data-table { border: none !important; }
    .modern-data-table thead th { 
        background: #f8fafc; 
        color: #475569; 
        font-size: 0.75rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
        padding: 1.25rem 1rem; 
        border: none;
    }
    .modern-data-table tbody td { 
        padding: 1.25rem 1rem; 
        border-bottom: 1px solid #f1f5f9; 
        vertical-align: middle;
        background: white;
    }
    .modern-data-table tbody tr:hover td { background: #fbfcfe; }
    .modern-data-table tbody tr:last-child td { border-bottom: none; }
    
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter { margin-bottom: 2rem; }
    
    .dataTables_wrapper .dataTables_filter input {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        width: 300px;
        transition: all 0.2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4f46e5;
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        background: #4338ca !important;
    }
</style>

<script>
$(document).ready(function() {
    $('#commonCoursesTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            search: "",
            searchPlaceholder: "Fast search courses...",
            lengthMenu: "Show _MENU_ per page"
        }
    });
});
</script>
