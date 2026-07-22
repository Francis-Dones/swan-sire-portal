<?php $__env->startSection('title', 'Dashboard – Ship Inspection Portal'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('page-subtitle', 'Overview & Statistics'); ?>

<?php $__env->startSection('content'); ?>

<?php if(isset($apiError)): ?>
    <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-2xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                <i class="ti ti-alert-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <p class="text-red-700 dark:text-red-300"><?php echo e($apiError); ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- STAT CARDS - Only Total Inspections and Total Vessels -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <!-- Total Exams Card -->
    <div class="group relative bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/20 to-transparent rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-500/10 to-transparent rounded-full -ml-12 -mb-12"></div>
        <div class="p-6 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i class="ti ti-clipboard-list text-white text-2xl"></i>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">TOTAL</span>
            </div>
            <p class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e(number_format($totalExams)); ?></p>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">Total Inspections</p>
            <div class="mt-4 h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <!-- Total Vessels Card -->
    <div class="group relative bg-gradient-to-br from-white to-navy-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-navy-500/20 to-transparent rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-navy-500/10 to-transparent rounded-full -ml-12 -mb-12"></div>
        <div class="p-6 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-navy-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-navy-500/30">
                    <i class="ti ti-ship text-white text-2xl"></i>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-navy-100 dark:bg-navy-900/40 text-navy-700 dark:text-navy-300">FLEET</span>
            </div>
            <p class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e(number_format($totalVessels)); ?></p>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">Total Vessels</p>
            <div class="mt-4 h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-navy-500 to-indigo-600 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS SECTION - Monthly Inspections Trend & Vessel Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Bar Chart Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                            <i class="ti ti-chart-bar text-white text-sm"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Monthly Inspections Trend</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Last 12 months performance</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        Inspections
                    </span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <canvas id="monthlyChart" height="300" style="max-height: 320px; width: 100%;"></canvas>
        </div>
    </div>

    <!-- Pie Chart Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg">
                            <i class="ti ti-pie-chart text-white text-sm"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Vessel Distribution</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">By number of inspections</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <canvas id="typeChart" height="300" style="max-height: 320px; width: 100%;"></canvas>
        </div>
    </div>
</div>

<!-- RECENT EXAMS SECTION -->
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg">
                    <i class="ti ti-history text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">Recent Inspections</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Latest submitted exams (Last 30 days)</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('exams.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-sm font-medium rounded-xl transition-all hover:scale-105 shadow-md">
                    <span>View All Records</span>
                    <i class="ti ti-arrow-right text-sm"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] lg:min-w-full">
            <thead>
                <tr class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700/80 dark:to-gray-800/80 border-b-2 border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Exam ID</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Vessel</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">PIC</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">Inspector</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <?php $__empty_1 = true; $__currentLoopData = $recentExams ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/50 dark:hover:from-blue-900/10 dark:hover:to-indigo-900/10 transition-all duration-200 group cursor-pointer" onclick="window.location='<?php echo e(route('exams.show', $exam['id'] ?? 0)); ?>'">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                            <i class="ti ti-id-badge text-xs"></i>
                            <?php echo e($exam['exam_id'] ?? '—'); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-900/40 dark:to-navy-800/40 flex items-center justify-center">
                                <i class="ti ti-ship text-navy-600 dark:text-navy-400 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 text-sm"><?php echo e($exam['vessel_name'] ?? '—'); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm hidden sm:table-cell">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-user-circle text-gray-400 text-sm"></i>
                            <?php echo e($exam['person_in_charge'] ?? '—'); ?>

                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm hidden lg:table-cell">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-mail text-gray-400 text-sm"></i>
                            <?php echo e($exam['submitted_by'] ?? '—'); ?>

                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-calendar text-gray-400 text-sm"></i>
                            <span class="text-gray-600 dark:text-gray-400 text-sm whitespace-nowrap">
                                <?php echo e(isset($exam['submitted_date']) ? date('M d, Y', strtotime($exam['submitted_date'])) : '—'); ?>

                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('exams.show', $exam['id'] ?? 0)); ?>" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105"
                           onclick="event.stopPropagation()">
                            <i class="ti ti-eye text-sm"></i>
                            <span>View</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-16">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                <i class="ti ti-database-off text-3xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No Inspections Yet</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Start by creating your first inspection</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = () => document.documentElement.classList.contains('dark');
    const gridColor = () => isDark() ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = () => isDark() ? '#cbd5e1' : '#475569';

    // Monthly Bar Chart
    const monthlyData = <?php echo json_encode($monthlyData ?? [], 15, 512) ?>;
    const barCtx = document.getElementById('monthlyChart');
    
    if (barCtx && monthlyData && monthlyData.length > 0) {
        new Chart(barCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [{
                    label: 'Inspections',
                    data: monthlyData.map(d => d.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    borderRadius: 10,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: { color: textColor(), font: { size: 11, weight: 'bold' } }
                    },
                    tooltip: {
                        backgroundColor: isDark() ? '#1e293b' : '#ffffff',
                        titleColor: isDark() ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark() ? '#cbd5e1' : '#475569',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `📊 Inspections: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: gridColor(), drawBorder: true }, 
                        ticks: { color: textColor(), stepSize: 1, font: { weight: 'bold' } },
                        title: { display: true, text: 'Number of Inspections', color: textColor(), font: { size: 11 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: textColor(), font: { size: 11, weight: 'bold' } }
                    }
                }
            }
        });
    }

    // Pie Chart - Vessel Distribution
    const typeData = <?php echo json_encode($inspectionTypes ?? [], 15, 512) ?>;
    const pieCtx = document.getElementById('typeChart');
    
    if (pieCtx && Object.keys(typeData).length > 0) {
        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 15,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { color: textColor(), font: { size: 11, weight: 'bold' }, usePointStyle: true, boxWidth: 10 }
                    },
                    tooltip: {
                        backgroundColor: isDark() ? '#1e293b' : '#ffffff',
                        titleColor: isDark() ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark() ? '#cbd5e1' : '#475569',
                        borderColor: '#8b5cf6',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `⚓ ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%',
                radius: '90%'
            }
        });
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ship-inspection-portal\resources\views/dashboard/index.blade.php ENDPATH**/ ?>