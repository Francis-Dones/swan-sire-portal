<?php $__env->startSection('title', 'Exam Records'); ?>
<?php $__env->startSection('page-title', 'Exam Records'); ?>
<?php $__env->startSection('page-subtitle', 'All ship inspection exams - Sorted by latest date'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- FILTERS SECTION - Premium Design -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                    <i class="ti ti-filter text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Filter Records</h3>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                <?php echo e($exams->total()); ?> total
            </span>
        </div>
        
        <form method="GET" action="<?php echo e(route('exams.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-search text-xs"></i> Search
                </label>
                <div class="relative group">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                    <input type="text" name="search" value="<?php echo e($search); ?>" 
                           class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white"
                           placeholder="Exam ID, Vessel, Person...">
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-ship text-xs"></i> Vessel
                </label>
                <select name="vessel_name" 
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white cursor-pointer">
                    <option value="">All Vessels</option>
                    <?php $__currentLoopData = $vessels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($v); ?>" <?php echo e($vesselName == $v ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-calendar-plus text-xs"></i> Date From
                </label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" 
                       class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-calendar-minus text-xs"></i> Date To
                </label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" 
                       class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white">
            </div>
            
            <div class="flex gap-2 items-end">
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-filter text-base"></i>
                    <span>Filter</span>
                </button>
                <a href="<?php echo e(route('exams.index')); ?>" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-x text-base"></i>
                    <span class="hidden sm:inline">Clear</span>
                </a>
            </div>
        </form>
    </div>

    <!-- TOOLBAR - Premium Design -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                <div class="absolute inset-0 w-2.5 h-2.5 bg-green-500 rounded-full animate-ping opacity-75"></div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                <span class="font-bold text-gray-900 dark:text-white"><?php echo e($exams->total()); ?></span> record(s) found
                <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">
                    <i class="ti ti-arrow-down text-xs"></i> Newest first
                </span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <!-- Import Button -->
            <form method="POST" action="<?php echo e(route('exams.import')); ?>" enctype="multipart/form-data" x-data="{ loading: false }" class="relative">
                <?php echo csrf_field(); ?>
                <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-all cursor-pointer border border-gray-200 dark:border-gray-600 hover:shadow-md">
                    <i class="ti ti-upload text-base"></i>
                    <span class="hidden sm:inline">Import</span>
                    <input type="file" name="file" accept=".xlsx,.csv,.xls" class="hidden" @change="loading = true; $el.closest('form').submit()">
                </label>
            </form>
            
            <!-- Excel Button -->
            <a href="<?php echo e(route('exams.export.excel')); ?>" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-spreadsheet text-base"></i>
                <span class="hidden sm:inline">Excel</span>
            </a>
            
            <!-- PDF Button -->
            <a href="<?php echo e(route('exams.export.pdf')); ?>" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-type-pdf text-base"></i>
                <span class="hidden sm:inline">PDF</span>
            </a>
        </div>
    </div>

    <!-- TABLE - Premium Design with Responsive -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto overflow-y-auto" style="max-height: 600px;">
            <table class="w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/80 dark:to-gray-800/80 border-b-2 border-gray-200 dark:border-gray-700 backdrop-blur-sm">
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Exam ID</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Vessel</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PIC</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Answers</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-all duration-200 group cursor-pointer" onclick="window.location='<?php echo e(route('exams.show', $exam['id'] ?? 0)); ?>'">
                        <td class="px-4 py-4 text-sm text-gray-400 dark:text-gray-500 font-mono font-medium">
                            <?php echo e($exams->firstItem() + $index); ?>

                        </td>
                        
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-mono font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                <i class="ti ti-id-badge text-xs"></i>
                                <?php echo e($exam['exam_id'] ?? '—'); ?>

                            </span>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/40 dark:to-indigo-800/40 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-ship text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                </div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm"><?php echo e($exam['vessel_name'] ?? '—'); ?></span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-user-circle text-gray-400 dark:text-gray-500 text-base"></i>
                                <span class="text-gray-700 dark:text-gray-300 text-sm"><?php echo e($exam['person_in_charge'] ?? '—'); ?></span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <i class="ti ti-user-check text-green-600 dark:text-green-400 text-xs"></i>
                                </div>
                                <span class="text-gray-700 dark:text-gray-300 text-sm"><?php echo e($exam['submitted_by'] ?? '—'); ?></span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-mail text-gray-400 dark:text-gray-500 text-sm"></i>
                                <span class="text-gray-600 dark:text-gray-400 text-xs truncate max-w-[150px]"><?php echo e($exam['email'] ?? '—'); ?></span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <?php
                                $dateFormatted = '—';
                                $timeFormatted = '';
                                if (isset($exam['submitted_date'])) {
                                    try {
                                        $timestamp = is_string($exam['submitted_date']) ? strtotime($exam['submitted_date']) : $exam['submitted_date'];
                                        if ($timestamp && $timestamp > 0) {
                                            $dateFormatted = date('M d, Y', $timestamp);
                                            $timeFormatted = date('H:i', $timestamp);
                                        }
                                    } catch (\Exception $e) {
                                        $dateFormatted = '—';
                                    }
                                }
                            ?>
                            <div class="flex items-center gap-2">
                                <i class="ti ti-calendar-time text-gray-400 dark:text-gray-500 text-sm"></i>
                                <span class="text-gray-600 dark:text-gray-400 text-xs whitespace-nowrap font-mono">
                                    <?php echo e($dateFormatted); ?>

                                </span>
                            </div>
                            <?php if($timeFormatted): ?>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">
                                <?php echo e($timeFormatted); ?>

                            </div>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-4 py-4">
                            <?php $ansCount = is_array($exam['answers'] ?? null) ? count($exam['answers']) : 0; ?>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold <?php echo e($ansCount > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'); ?>">
                                <i class="ti ti-checklist text-xs"></i>
                                <span><?php echo e($ansCount); ?></span>
                                <span class="text-[10px] opacity-75">items</span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4 text-center">
                            <a href="<?php echo e(route('exams.show', $exam['id'] ?? 0)); ?>" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                               onclick="event.stopPropagation()">
                                <i class="ti ti-eye text-sm"></i>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-20">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-clipboard-off text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No exam records found</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters or check API connection</p>
                                </div>
                                <a href="<?php echo e(route('exams.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-xl transition-all">
                                    <i class="ti ti-refresh"></i>
                                    Clear Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        <?php if($exams->hasPages()): ?>
        <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($exams->firstItem()); ?></span> 
                    to <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($exams->lastItem()); ?></span> 
                    of <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($exams->total()); ?></span> results
                </div>
                
                <div class="flex items-center gap-2">
                    <?php if($exams->onFirstPage()): ?>
                        <span class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="ti ti-chevron-left text-sm"></i>
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($exams->previousPageUrl()); ?>" 
                           class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">
                            <i class="ti ti-chevron-left text-sm"></i>
                        </a>
                    <?php endif; ?>
                    
                    <div class="flex items-center gap-1">
                        <?php
                            $currentPage = $exams->currentPage();
                            $lastPage = $exams->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $exams->url(1) . '" class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">1</a>';
                                if ($start > 2) {
                                    echo '<span class="px-2 text-gray-400">…</span>';
                                }
                            }
                            
                            for ($page = $start; $page <= $end; $page++) {
                                if ($page == $currentPage) {
                                    echo '<span class="px-3 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-md font-semibold">' . $page . '</span>';
                                } else {
                                    echo '<a href="' . $exams->url($page) . '" class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">' . $page . '</a>';
                                }
                            }
                            
                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) {
                                    echo '<span class="px-2 text-gray-400">…</span>';
                                }
                                echo '<a href="' . $exams->url($lastPage) . '" class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">' . $lastPage . '</a>';
                            }
                        ?>
                    </div>
                    
                    <?php if($exams->hasMorePages()): ?>
                        <a href="<?php echo e($exams->nextPageUrl()); ?>" 
                           class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">
                            <i class="ti ti-chevron-right text-sm"></i>
                        </a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="ti ti-chevron-right text-sm"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: rgb(243 244 246);
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgb(156 163 175);
        border-radius: 10px;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-track {
        background: rgb(31 41 55);
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgb(75 85 99);
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: rgb(107 114 128);
    }
    
    * {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    tbody tr {
        cursor: pointer;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ship-inspection-portal\resources\views/exams/index.blade.php ENDPATH**/ ?>