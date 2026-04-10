<!-- Operations Control Center (Admin Header) -->
<div class="mb-8 p-8 bg-white border border-slate-200 rounded-3xl shadow-sm">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Operations Control Center</h2>
            <p class="text-slate-500 font-medium mt-1">Administrator Overview. Manage students, faculty, and academic assets.</p>
        </div>
        <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100/50">
            <span class="block text-[10px] uppercase font-bold text-blue-400 tracking-wider mb-0.5">System Status</span>
            <span class="flex items-center gap-2 text-sm font-bold text-blue-700">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                Standard Admin
            </span>
        </div>
    </div>

    <!-- Main Stats Grid (Updated to 4 cols for better width/density) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Total Students -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">
                    <i class="fas fa-arrow-up"></i> 12%
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Students</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['students'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> 5 New this week
                </div>
            </div>
        </div>

        <!-- Total Faculty -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-purple-200 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-purple-100 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-purple-500 bg-purple-50 px-2 py-1 rounded-lg">
                    Active
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Faculty</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['faculty'] ?></div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-purple-400 rounded-full"></span> 2 Departments
                </div>
            </div>
        </div>

        <!-- Total Subjects -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">
                    Core
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Curriculum</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1"><?= $stats['subjects'] ?> Subjects</div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span> Updated Today
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="group p-5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-amber-100 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fas fa-server"></i>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-lg">
                    Cloud
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">System Load</div>
                <div class="text-2xl font-black text-slate-800 tracking-tight mb-1">Optimal</div>
                <div class="text-[10px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> 100% Uptime
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Actions & Recent Activity -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- System Quick Actions -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i> System Quick Actions
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="<?= base_url('admin/students') ?>" class="group flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-indigo-600 transition-all duration-300">
                    <div class="w-12 h-12 bg-white flex items-center justify-center rounded-xl shadow-sm text-indigo-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-white">Add Student</span>
                </a>
                
                <a href="<?= base_url('admin/faculty') ?>" class="group flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-purple-600 transition-all duration-300">
                    <div class="w-12 h-12 bg-white flex items-center justify-center rounded-xl shadow-sm text-purple-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-tie text-lg"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-white">Add Faculty</span>
                </a>

                <a href="<?= base_url('admin/subjects') ?>" class="group flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-emerald-600 transition-all duration-300">
                    <div class="w-12 h-12 bg-white flex items-center justify-center rounded-xl shadow-sm text-emerald-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-white">Manage Subjects</span>
                </a>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Recently Registered Students</h3>
                <a href="<?= base_url('admin/students') ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">View All</a>
            </div>
            
            <div class="space-y-3">
                <?php if (empty($recentStudents)): ?>
                    <div class="flex flex-col items-center justify-center py-10 grayscale opacity-50">
                        <i class="fas fa-folder-open text-4xl mb-4"></i>
                        <p class="text-sm font-medium text-slate-500">No recent students found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentStudents as $s): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:border-slate-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-200 to-slate-300 flex items-center justify-center text-slate-600 font-bold text-xs ring-2 ring-white">
                                <?= strtoupper(substr($s['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800 leading-none mb-1 text-ellipsis overflow-hidden whitespace-nowrap max-w-[150px]"><?= $s['name'] ?></div>
                                <div class="text-[11px] font-medium text-slate-500 text-ellipsis overflow-hidden whitespace-nowrap max-w-[150px]"><?= $s['email'] ?></div>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                            <?= date('M d, Y', strtotime($s['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Leave Requests (Global Oversight) -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Recent Leave Requests</h3>
                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">Global Monitoring</span>
            </div>
            
            <div class="space-y-3">
                <?php if (empty($recentLeaves)): ?>
                    <p class="text-sm text-slate-500 py-10 text-center">No recent leave activity.</p>
                <?php else: ?>
                    <?php foreach ($recentLeaves as $rl): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50/50 border border-slate-100 rounded-2xl">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800 leading-none mb-1"><?= $rl['faculty_name'] ?></div>
                                <div class="text-[11px] font-medium text-slate-500"><?= $rl['leave_type'] ?> • <?= $rl['num_days'] ?> <?= $rl['num_days'] == 1 ? 'day' : 'days' ?></div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <?php 
                                $statusColor = ($rl['status'] == 'Approved') ? 'text-emerald-600' : (($rl['status'] == 'Rejected') ? 'text-rose-600' : 'text-amber-600');
                                $statusBg = ($rl['status'] == 'Approved') ? 'bg-emerald-50' : (($rl['status'] == 'Rejected') ? 'bg-rose-50' : 'bg-amber-50');
                            ?>
                            <span class="px-2 py-0.5 <?= $statusBg ?> <?= $statusColor ?> rounded text-[9px] font-black uppercase tracking-widest">
                                <?= $rl['status'] ?>
                            </span>
                            <div class="text-[10px] text-slate-400 font-medium">
                                <?= date('M d', strtotime($rl['start_date'])) ?> - <?= date('M d', strtotime($rl['end_date'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: System Insights -->
    <div class="space-y-8">
        <div class="bg-slate-900 p-8 rounded-[2rem] text-white shadow-2xl shadow-slate-500/20 relative overflow-hidden">
            <!-- Glass Decorative Element -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
            
            <h3 class="text-lg font-bold text-white mb-8 relative z-10">Academic Insights</h3>
            
            <div class="space-y-6 relative z-10">
                <div class="p-5 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                    <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Total Assignments</div>
                    <div class="text-3xl font-black text-indigo-400 tracking-tight"><?= $totalAssignments ?></div>
                </div>
                
                <div class="p-5 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                    <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Student/Faculty Ratio</div>
                    <?php 
                        $ratio = $stats['faculty'] > 0 ? round($stats['students'] / $stats['faculty'], 1) : $stats['students'];
                    ?>
                    <div class="text-3xl font-black text-emerald-400 tracking-tight"><?= $ratio ?> : 1</div>
                </div>

                <div class="p-5 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                    <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Session Status</div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="w-5 h-5 flex items-center justify-center bg-indigo-500/20 text-indigo-400 rounded-md">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                        </span>
                        <span class="text-sm font-bold text-slate-200">Spring 2026 Admin</span>
                    </div>
                </div>
            </div>

            <div class="mt-10 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                    <p class="text-[11px] font-medium text-slate-400 leading-relaxed">
                        <strong class="text-amber-500">Quick Tip:</strong> Keep subject assignments up to date ensure faculty members can start their grading cycles.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
