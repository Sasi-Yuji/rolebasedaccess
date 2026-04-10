<?php 
    $totalMarks = 0;
    $subjectsCount = count($marks);
    $passCount = 0;
    foreach ($marks as $m) {
        $totalMarks += $m['marks'];
        if ($m['marks'] >= 40) $passCount++;
    }
    $gpa = ($subjectsCount > 0) ? number_format(($totalMarks / ($subjectsCount * 100)) * 10, 2) : '0.00';
?>

<div class="dashboard-wrapper" style="animation: slide-up 0.5s ease-out;">
    <!-- Welcome Section -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 2.5rem; border-radius: 24px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div style="position: relative; z-index: 1; flex: 1;">
            <h2 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">Academic Overview</h2>
            <p style="opacity: 0.9; font-size: 1.1rem;">Track your progress, grades, and upcoming academic milestones.</p>
        </div>

        <!-- Dynamic Progression Content -->
        <div style="position: relative; z-index: 1; flex: 1.5; display: flex; justify-content: flex-start; gap: 4rem; padding-left: 4rem;">
            <div style="text-align: center;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.8; margin-bottom: 0.5rem; font-weight: 700;">Semester Progress</div>
                <div style="font-size: 1.75rem; font-weight: 800;">72%</div>
                <div style="width: 100px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; margin-top: 0.5rem; overflow: hidden;">
                    <div style="width: 72%; height: 100%; background: white;"></div>
                </div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.8; margin-bottom: 0.5rem; font-weight: 700;">Next Major Milestone</div>
                <div style="font-size: 1.75rem; font-weight: 800;">Final Exams</div>
                <div style="font-size: 0.75rem; opacity: 0.9; font-weight: 600; margin-top: 0.25rem;">May 15th, 2026</div>
            </div>
        </div>

        <div style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); opacity: 0.15; font-size: 10rem;">
            <i class="fas fa-graduation-cap"></i>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Primary Metrics Area -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Compact Stat Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                <!-- GPA Card -->
                <div class="card" style="padding: 1.25rem; border: none; background: white; box-shadow: var(--shadow-md); position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="background: #eef2ff; color: #4f46e5; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 style="font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase;">GPA</h4>
                        </div>
                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 700; background: #ecfdf5; padding: 0.25rem 0.5rem; border-radius: 6px;">Top 15%</div>
                    </div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #1e293b;"><?= $gpa ?></div>
                </div>

                <!-- Subjects Card -->
                <div class="card" style="padding: 1.25rem; border: none; background: white; box-shadow: var(--shadow-md); position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="background: #ecfdf5; color: #10b981; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-book-reader"></i>
                            </div>
                            <h4 style="font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase;">CLEARED</h4>
                        </div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 700; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 6px;">Progress</div>
                    </div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #1e293b;"><?= $passCount ?>/<?= $subjectsCount ?></div>
                </div>

                <!-- Standing Card -->
                <div class="card" style="padding: 1.25rem; border: none; background: white; box-shadow: var(--shadow-md); position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="background: #fff7ed; color: #f59e0b; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-award"></i>
                            </div>
                            <h4 style="font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase;">STANDING</h4>
                        </div>
                        <div style="font-size: 0.7rem; color: #f59e0b; font-weight: 700; background: #fffbeb; padding: 0.25rem 0.5rem; border-radius: 6px;">Candidate</div>
                    </div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">Excellence</div>
                </div>
            </div>
            
            <!-- Quick Actions: Document Uploads -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <!-- Upload Answer Sheet -->
                <div class="card" onclick="window.location.href='<?= base_url('student/upload/answers') ?>'" style="padding: 1.5rem; border: none; background: white; box-shadow: var(--shadow-md); cursor: pointer; transition: all 0.2s; border-left: 5px solid #4f46e5;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: #eef2ff; color: #4f46e5; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 800; color: #1e293b; margin: 0;">Upload Answer Sheet</h4>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Exam papers (A4 Fixed Crop)</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Previous Marksheet -->
                <div class="card" onclick="window.location.href='<?= base_url('student/upload/marksheet') ?>'" style="padding: 1.5rem; border: none; background: white; box-shadow: var(--shadow-md); cursor: pointer; transition: all 0.2s; border-left: 5px solid #10b981;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 800; color: #1e293b; margin: 0;">Previous Marksheet</h4>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Certificates (Free Size Crop)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Breakdown -->
            <div class="card" style="padding: 2rem; border: none; background: white; box-shadow: var(--shadow-lg);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3 style="font-weight: 800; color: #1e293b;">Subject Performance</h3>
                    <a href="<?= base_url('student/marks') ?>" style="color: #4f46e5; font-size: 0.875rem; font-weight: 600; text-decoration: none;">View Detailed Marks <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></a>
                </div>
                
                <div style="display: grid; gap: 1.5rem;">
                    <?php foreach ($marks as $m): ?>
                    <div style="padding: 1.25rem; background: #f8fafc; border-radius: 16px; border: 1px solid #f1f5f9; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(10px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <div style="font-weight: 700; color: #334155;"><?= $m['subject_name'] ?></div>
                            <div style="font-weight: 800; color: #4f46e5; font-size: 1.1rem;"><?= $m['marks'] ?>%</div>
                        </div>
                        <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                            <div style="width: <?= $m['marks'] ?>%; height: 100%; background: linear-gradient(90deg, #4f46e5, #7c3aed); border-radius: 4px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($marks)): ?>
                    <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="fas fa-database" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                        No academic data available for the current semester.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Items -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Profile Quick Link -->
            <div class="card" style="padding: 1.5rem; background: #4f46e5; color: white; border: none; box-shadow: var(--shadow-lg);">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=white&color=4f46e5&bold=true" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.2);">
                    <div>
                        <h4 style="font-weight: 700; font-size: 1.25rem;"><?= session()->get('name') ?></h4>
                        <div style="opacity: 0.8; font-size: 0.875rem;">Registered Student</div>
                    </div>
                    <a href="<?= base_url('student/profile') ?>" class="btn btn-light" style="width: 100%; padding: 0.75rem; border-radius: 12px; font-weight: 700; color: #4f46e5;">My Profile</a>
                </div>
            </div>

            <!-- Important Dates Card -->
            <div class="card" style="padding: 1.5rem; border: none; background: white; box-shadow: var(--shadow-lg);">
                <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 1.5rem;">Quick Navigation</h4>
                <div style="display: grid; gap: 1rem;">
                    <a href="<?= base_url('student/marks') ?>" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; padding: 0.75rem; border-radius: 10px; background: #f8fafc; color: #475569; font-weight: 600;">
                        <i class="fas fa-file-invoice text-primary"></i> Examination Reports
                    </a>
                    <a href="#" onclick="showAlert('Coming Soon', 'Course registration for the next semester will open shortly.', 'info')" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; padding: 0.75rem; border-radius: 10px; background: #f8fafc; color: #475569; font-weight: 600;">
                        <i class="fas fa-calendar-plus text-success"></i> Course Registration
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
