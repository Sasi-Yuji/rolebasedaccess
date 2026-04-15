<!-- Modern Command Center -->
<div class="mb-8 p-8 bg-white border border-slate-200 rounded-3xl shadow-sm">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Instructor Command Center</h2>
            <p class="text-slate-500 font-medium mt-1">Welcome back, <?= session()->get('name') ?>. Here's your real-time academic overview.</p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100/50">
            <span class="block text-[10px] uppercase font-bold text-indigo-400 tracking-wider mb-0.5">Academic Session</span>
            <span class="text-sm font-bold text-indigo-700">Spring 2026 Semester</span>
        </div>
    </div>

    <!-- Stats Row (High Density Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Active Courses -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">
                    Full-Term
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Active Courses</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['subjects'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span> Assigned for current session
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300 text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-users"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">
                    Active
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Students</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['students'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span> Total enrollment across classes
                </div>
            </div>
        </div>

        <!-- Grades Submitted -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-rose-200 hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300 text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-rose-100 text-rose-600 rounded-xl group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg">
                    Entries
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Graded Items</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['grades_total'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span> Internal marks submitted
                </div>
            </div>
        </div>

        <!-- Efficiency -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-violet-200 hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-300 text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-violet-100 text-violet-600 rounded-xl group-hover:bg-violet-600 group-hover:text-white transition-colors">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-violet-500 bg-violet-50 px-2 py-1 rounded-lg">
                    Rate
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Grading Efficiency</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1">
                    <?= $stats['grades_total'] > 0 ? floor(($stats['grades_total'] / ($stats['students'] * $stats['subjects'] ?: 1)) * 100) : 0 ?>%
                </div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-violet-400 rounded-full"></span> Progress towards completion
                </div>
            </div>
        </div>

        <!-- Leave Status Stats -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300 text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-amber-100 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-lg">
                    Active
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pending Leaves</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['pending_leaves'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Submissions awaiting HOD
                </div>
            </div>
        </div>
        <!-- Student Requests -->
        <a href="<?= base_url('faculty/leave') ?>" class="group p-5 bg-violet-50/50 border border-violet-100 rounded-2xl hover:bg-white hover:border-violet-300 hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-300 text-left block no-underline">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-violet-100 text-violet-600 rounded-xl group-hover:bg-violet-600 group-hover:text-white transition-colors">
                    <i class="fas fa-user-clock"></i>
                </div>
                <?php if($stats['student_pending'] > 0): ?>
                    <div class="animate-pulse flex items-center gap-1 text-[10px] font-bold text-white bg-rose-500 px-2 py-1 rounded-lg">
                        Action Required
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Student Leave Requests</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['student_pending'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-violet-400 rounded-full"></span> Awaiting your approval
                </div>
            </div>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column (Main Body) -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- My Courses list style from screenshot -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark);">My Courses</h3>
                <a href="<?= base_url('faculty/subjects') ?>" style="font-size: 0.875rem; color: var(--primary); text-decoration: none; font-weight: 500;">View All &rarr;</a>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                <?php foreach ($assignedSubjects as $sub): ?>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: var(--radius); padding: 1.5rem; transition: transform 0.2s, background 0.2s;" class="course-card-hover">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="background: #e2e8f0; color: #475569; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-book" style="color: #64748b;"></i> Code: #<?= $sub['id'] ?>
                        </span>
                    </div>
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--dark); margin-bottom: 1.5rem; min-height: 2.5rem;"><?= $sub['subject_name'] ?></h4>
                    <a href="<?= base_url('faculty/marks/upload/' . $sub['id']) ?>" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        Grade Students <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($assignedSubjects)): ?>
                    <div style="grid-column: 1/-1; padding: 3rem; text-align: center; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 20px;">
                        <div style="width: 56px; height: 56px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #94a3b8; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <i class="fas fa-book-open" style="font-size: 1.5rem;"></i>
                        </div>
                        <h4 style="font-weight: 700; color: #475569; margin-bottom: 0.5rem;">No Assigned Subjects Yet</h4>
                        <p style="font-size: 0.85rem; color: #64748b; max-width: 300px; margin: 0 auto 1.5rem;">Once the administrator assigns subjects to your profile, they will appear here for management and grading.</p>
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); background: #eef2ff; padding: 0.4rem 1rem; border-radius: 50px; text-transform: uppercase;">Awaiting Curriculum Sync</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Timeline Activity -->
        <div>
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin-bottom: 1.5rem;">Recent Grading Activity</h3>
            <div class="card" style="padding: 1.5rem;">
                <div style="position: relative; padding-left: 1.5rem;">
                    <div style="position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: #e2e8f0;"></div>
                    
                    <?php if(empty($recentGrades)): ?>
                        <div style="padding: 2rem 1rem; text-align: center;">
                            <p style="color: #64748b; font-size: 0.875rem; font-weight: 500;">No grading activity recorded yet.</p>
                            <p style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.5rem;">When you submit internal marks for your students, your recent actions will be tracked here.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($recentGrades as $index => $rg): ?>
                        <div style="position: relative; margin-bottom: <?= $index !== count($recentGrades) - 1 ? '1.5rem' : '0' ?>; display: flex; justify-content: space-between; align-items: center;">
                            <div style="position: absolute; left: -1.5rem; top: 0.25rem;">
                                <div style="width: 14px; height: 14px; background: white; border: 3px solid var(--primary); border-radius: 50%;"></div>
                            </div>
                            
                            <div>
                                <div style="font-size: 0.95rem; font-weight: 600; color: var(--dark); margin-bottom: 0.25rem;"><?= $rg['student_name'] ?></div>
                                <div style="font-size: 0.8rem; color: var(--secondary);">Recorded grade for <?= $rg['subject_name'] ?></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1rem; font-weight: 600; color: var(--success); margin-bottom: 0.2rem;"><?= $rg['marks'] ?> / 100</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);"><?= date('h:i A', strtotime($rg['updated_at'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column Profile & Action Requirements -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Profile Banner Card -->
        <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
            <div style="height: 80px; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);"></div>
            <div style="padding: 0 1.5rem 1.5rem; text-align: center;">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=EEF2FF&color=4F46E5&size=80" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-top: -40px;">
                <h4 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin-top: 1rem;"><?= session()->get('name') ?></h4>
                <div style="font-size: 0.7rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.5rem;">Department Head</div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                        <i class="fas fa-envelope" style="color: var(--secondary); width: 16px;"></i>
                        <span style="font-size: 0.8rem; font-weight: 500; color: var(--dark); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= session()->get('email') ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                        <i class="fas fa-id-badge" style="color: var(--secondary); width: 16px;"></i>
                        <span style="font-size: 0.8rem; font-weight: 500; color: var(--dark);">#FAC-<?= session()->get('id') ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Required Card -->
        <div class="card" style="background: #1e293b; color: white; padding: 1.5rem; margin-bottom: 0; border: none;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <i class="fas fa-bolt" style="color: #fbbf24;"></i>
                <h4 style="font-weight: 600; font-size: 0.95rem;">Action Required</h4>
            </div>
            <p style="font-size: 0.75rem; color: #94a3b8; line-height: 1.5; margin-bottom: 1.5rem;">
                Please finalize your internal marks for this semester before **April 15th**. All submissions are final.
            </p>
            <button class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 0.7rem; border: none;">Review Policy</button>
        </div>

        <!-- Leave Status Widget -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <i class="fas fa-calendar-day" style="color: var(--primary);"></i>
                <h4 style="font-weight: 600; font-size: 0.95rem;">Service Status</h4>
            </div>
            <?php if($recentLeave): ?>
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">Recent Leave</span>
                        <?php 
                        $lColor = $recentLeave['status'] == 'Approved' ? '#22c55e' : ($recentLeave['status'] == 'Rejected' ? '#ef4444' : '#eab308');
                        ?>
                        <span style="color: <?= $lColor ?>; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem;">
                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i> <?= $recentLeave['status'] ?>
                        </span>
                    </div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: var(--dark); margin-bottom: 0.25rem;"><?= htmlspecialchars($recentLeave['reason']) ?></div>
                    <div style="font-size: 0.7rem; color: #94a3b8;"><?= date('M d', strtotime($recentLeave['start_date'])) ?> - <?= date('M d, Y', strtotime($recentLeave['end_date'])) ?></div>
                </div>
            <?php else: ?>
                <p style="font-size: 0.75rem; color: #64748b; text-align: center; padding: 1rem;">No recent leave activity found.</p>
            <?php endif; ?>
            <a href="<?= base_url('faculty/leave') ?>" class="btn btn-secondary" style="width: 100%; margin-top: 1rem; font-size: 0.8rem; justify-content: center; padding: 0.6rem;">Managed Leaves</a>
        </div>
        
    </div>
</div>

<style>
/* Subtle interaction for flat cards */
.course-card-hover:hover {
    background: #ffffff !important;
    border-color: var(--primary) !important;
    transform: translateY(-2px);
}
</style>
