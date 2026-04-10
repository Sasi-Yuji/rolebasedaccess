<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.5s ease-out forwards; opacity: 0; }
.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }
.delay-400 { animation-delay: 400ms; }
.delay-500 { animation-delay: 500ms; }
</style>

<div class="mb-3 p-4 bg-white border border-slate-200 rounded-2xl shadow-sm animate-slide-up">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h2 class="text-[1.1rem] font-extrabold text-slate-800 tracking-tight">Course Analytics Hub</h2>
            <p class="text-[10px] text-slate-500 font-medium mt-0.5">Real-time curriculum oversight and enrollment trends.</p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100/50">
            <span class="block text-[10px] uppercase font-bold text-indigo-400 tracking-wider mb-0.5">Academic Year</span>
            <span class="flex items-center gap-2 text-sm font-bold text-indigo-700">
                <i class="fas fa-calendar-check text-indigo-500"></i>
                2026 - 2027
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <!-- Total Courses -->
        <div class="group p-4 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-white hover:border-indigo-200 hover:shadow-md hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1 animate-slide-up delay-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors text-sm">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Catalogue</div>
                <div class="text-lg font-black text-slate-800 tracking-tight mb-1"><span class="animated-counter" data-target="<?= $stats['total'] ?>">0</span> Courses</div>
                <div class="text-[9px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span> All semesters
                </div>
            </div>
        </div>

        <!-- Active Courses -->
        <div class="group p-4 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-white hover:border-emerald-200 hover:shadow-md hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 animate-slide-up delay-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors text-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex items-center gap-1 text-[9px] font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-md">
                    <?= $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0 ?>%
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Active Now</div>
                <div class="text-lg font-black text-slate-800 tracking-tight mb-1"><span class="animated-counter" data-target="<?= $stats['active'] ?>">0</span> Courses</div>
                <div class="text-[9px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span> Ready for enrollment
                </div>
            </div>
        </div>

        <!-- Inactive Courses -->
        <div class="group p-4 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-white hover:border-amber-200 hover:shadow-md hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1 animate-slide-up delay-300">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 flex items-center justify-center bg-amber-100 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors text-sm">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Archived/Draft</div>
                <div class="text-lg font-black text-slate-800 tracking-tight mb-1"><span class="animated-counter" data-target="<?= $stats['inactive'] ?>">0</span> Courses</div>
                <div class="text-[9px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Hidden from students
                </div>
            </div>
        </div>

        <!-- Unique Departments -->
        <div class="group p-4 bg-slate-50/50 border border-slate-100 rounded-xl hover:bg-white hover:border-purple-200 hover:shadow-md hover:shadow-purple-500/10 transition-all duration-300 hover:-translate-y-1 animate-slide-up delay-400">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 flex items-center justify-center bg-purple-100 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition-colors text-sm">
                    <i class="fas fa-university"></i>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Departments</div>
                <div class="text-lg font-black text-slate-800 tracking-tight mb-1"><span class="animated-counter" data-target="<?= count($stats['by_dept']) ?>">0</span> Units</div>
                <div class="text-[9px] font-medium text-slate-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-purple-400 rounded-full"></span> Multi-disciplinary
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 animate-slide-up delay-500">
    <!-- Distribution by Department (Bar Chart) -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm transition-all duration-300 hover:shadow-md hover:border-indigo-200 cursor-pointer" id="deptChartContainer" title="Click a bar to filter the catalogue">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[15px] font-bold text-slate-800 tracking-tight">Curriculum by Department</h3>
            <span class="p-1.5 bg-slate-50 rounded-lg text-slate-400 cursor-help transition-colors hover:text-indigo-500 hover:bg-indigo-50" title="Courses assigned per department">
                <i class="fas fa-info-circle text-xs"></i>
            </span>
        </div>
        <div style="height: 180px; position: relative;" class="w-full">
            <canvas id="deptChart"></canvas>
        </div>
    </div>

    <!-- Active Status Distribution (Donut Chart) -->
    <div class="bg-white border border-slate-200 p-4 rounded-xl text-slate-800 shadow-sm relative overflow-hidden group transition-all duration-300 hover:shadow-md hover:border-indigo-200 hover:-translate-y-1">
        <div class="absolute -right-16 -top-16 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 group-hover:scale-110 transition-all duration-500"></div>
        <div class="flex items-center justify-between mb-4 relative z-10">
            <h3 class="text-[15px] font-bold text-slate-800 tracking-tight">Lifecycle Status</h3>
            <div class="px-2 py-1 bg-indigo-50 rounded-md text-[9px] font-bold text-indigo-500 uppercase tracking-widest border border-indigo-100">Global Status</div>
        </div>
        <div style="height: 180px; position: relative;" class="relative z-10 w-full transition-transform duration-500 group-hover:scale-[1.02]">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Course Load by Semester (Grouped Table) -->
    <div class="lg:col-span-2 bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-3 animate-slide-up delay-300 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[15px] font-bold text-slate-800 tracking-tight">Semester Workload Distribution</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Number of courses offered across current academic semesters.</p>
            </div>
            <a href="<?= base_url('admin/courses') ?>" class="text-[10px] font-bold text-indigo-600 hover:text-white flex items-center gap-1 bg-indigo-50 hover:bg-indigo-600 px-3 py-1.5 rounded-lg transition-all duration-300 shadow-sm hover:shadow-indigo-500/20">
                Access List View <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <?php $loopIndex = 1; foreach ($stats['by_sem'] as $sem): ?>
            <div class="relative flex-1 min-w-[120px] p-4 bg-slate-50 border border-slate-100 rounded-xl shadow-sm hover:shadow-lg hover:border-indigo-300 transition-all duration-300 group hover:-translate-y-1 cursor-default overflow-hidden animate-slide-up" style="animation-delay: <?= 500 + ($loopIndex++ * 100) ?>ms;">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10 flex flex-col justify-center">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-calendar-alt text-indigo-400 text-xs"></i>
                        <span class="text-[10px] font-bold text-slate-500 group-hover:text-indigo-600 transition-colors uppercase tracking-wider">Semester <?= $sem['semester'] ?></span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-800 tracking-tight group-hover:text-indigo-700 transition-colors animated-counter" data-target="<?= $sem['count'] ?>">0</span>
                        <span class="text-[10px] font-medium text-slate-400 group-hover:text-slate-600 transition-colors">Courses</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($stats['by_sem'])): ?>
            <div class="w-full py-8 flex flex-col items-center justify-center text-slate-300">
                <i class="fas fa-layer-group text-3xl mb-3 hover:animate-bounce"></i>
                <p class="text-xs font-medium">No courses available for semester mapping.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Animated Counters Slightly Faster
    $('.animated-counter').each(function() {
        var $this = $(this), countTo = $this.attr('data-target');
        $({ countNum: $this.text()}).animate({
            countNum: countTo
        }, {
            duration: 1200,
            easing: 'swing',
            step: function() { $this.text(Math.floor(this.countNum)); },
            complete: function() { $this.text(this.countNum); }
        });
    });

    // Department Distribution Chart
    const deptCtx = document.getElementById('deptChart').getContext('2d');
    const deptData = <?= json_encode($stats['by_dept']) ?>;
    
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: deptData.map(d => d.department),
            datasets: [{
                label: 'Course Count',
                data: deptData.map(d => d.count),
                backgroundColor: '#4F46E5',
                borderRadius: 8,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const dataIndex = elements[0].index;
                    const department = deptData[dataIndex].department;
                    window.location.href = '<?= base_url("admin/courses") ?>?department=' + encodeURIComponent(department);
                }
            },
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                data: [<?= $stats['active'] ?>, <?= $stats['inactive'] ?>],
                backgroundColor: ['#10B981', '#F59E0B'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            }
        }
    });
});
</script>
