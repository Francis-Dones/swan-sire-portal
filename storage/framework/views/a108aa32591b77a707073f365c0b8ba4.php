<?php $__env->startSection('title', 'Inspection Images'); ?>
<?php $__env->startSection('page-title', 'Inspection Images'); ?>
<?php $__env->startSection('page-subtitle', 'Browse & manage inspection photos'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4" x-data="{ 
    modalOpen: false, 
    modalImg: '', 
    modalName: '',
    modalMime: '',
    modalVessel: '',
    modalInspectionId: '',
    modalInspectionDate: '',
    modalInspectionLoc: '',
    modalInspector: '',
    modalRemarks: '',
    viewMode: 'table',
    downloadImage(base64Data, fileName) {
        // Simple download using data URL
        const link = document.createElement('a');
        link.href = 'data:image/jpeg;base64,' + base64Data;
        link.download = fileName.endsWith('.jpg') ? fileName : fileName + '.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}">

    <?php if(isset($apiError) && $apiError): ?>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300 rounded-xl p-4">
        <i class="ti ti-alert-triangle"></i> <?php echo e($apiError); ?>

    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl p-4">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-xl p-4">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <!-- TOOLBAR -->
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-4">
            <p class="text-sm text-gray-500"><?php echo e(number_format($total ?? 0)); ?> image(s)</p>
            <!-- View Mode Toggle -->
            <div class="flex border rounded-lg overflow-hidden">
                <button @click="viewMode = 'table'" 
                        :class="{'bg-blue-500 text-white': viewMode === 'table', 'bg-gray-100 dark:bg-gray-700': viewMode !== 'table'}"
                        class="px-3 py-1 text-sm transition">
                    📋 Table
                </button>
                <button @click="viewMode = 'grid'" 
                        :class="{'bg-blue-500 text-white': viewMode === 'grid', 'bg-gray-100 dark:bg-gray-700': viewMode !== 'grid'}"
                        class="px-3 py-1 text-sm transition">
                    🖼️ Grid
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <?php if(($images ?? []) && count($images) > 0): ?>
            <a href="<?php echo e(route('images.export.excel')); ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                Export Excel
            </a>
            <a href="<?php echo e(route('images.export.pdf')); ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                Export PDF
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($images) && count($images) > 0): ?>
    <?php
        // Group images by vessel name
        $groupedImages = [];
        foreach($images as $img) {
            $vesselKey = $img['vessel_name'] ?? $img['vessel_id'] ?? 'Unknown Vessel';
            if (!isset($groupedImages[$vesselKey])) {
                $groupedImages[$vesselKey] = [];
            }
            $groupedImages[$vesselKey][] = $img;
        }
        
        // Helper function to format date
        function formatDate($date) {
            if (empty($date)) return 'N/A';
            try {
                // Try to parse various date formats
                $timestamp = strtotime($date);
                if ($timestamp === false) return $date;
                return date('M d, Y', $timestamp); // Example: Jan 15, 2026
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        // Helper function to get full date with time
        function formatFullDate($date) {
            if (empty($date)) return 'N/A';
            try {
                $timestamp = strtotime($date);
                if ($timestamp === false) return $date;
                return date('F d, Y', $timestamp); // Example: January 15, 2026
            } catch (\Exception $e) {
                return $date;
            }
        }
    ?>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" x-cloak>
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg border shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vessel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspection ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspector</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__currentLoopData = $groupedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vesselName => $vesselImages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $rowSpan = count($vesselImages);
                        ?>
                        <?php $__currentLoopData = $vesselImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $imgId = $img['id'] ?? $img['image_id'] ?? $index;
                                $imgName = $img['image_name'] ?? $img['name'] ?? 'Image';
                                $inspectionId = $img['inspection_id'] ?? $img['inspectionId'] ?? 'N/A';
                                $inspectionDateRaw = $img['inspection_date'] ?? $img['inspectionDate'] ?? '';
                                $inspectionDate = !empty($inspectionDateRaw) ? formatDate($inspectionDateRaw) : 'N/A';
                                $inspectionDateFull = !empty($inspectionDateRaw) ? formatFullDate($inspectionDateRaw) : 'N/A';
                                $inspectionLoc = $img['inspection_loc'] ?? $img['inspectionLoc'] ?? $img['location'] ?? '';
                                $inspectorName = $img['inspector_name'] ?? $img['inspectorName'] ?? '';
                                $inspectionType = $img['inspection_type'] ?? $img['inspectionType'] ?? '';
                                $remarks = $img['remarks'] ?? '';
                                
                                // Make sure we have a display value for location
                                $displayLocation = !empty($inspectionLoc) ? $inspectionLoc : 'N/A';
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <!-- Vessel Name with rowspan -->
                                <?php if($index === 0): ?>
                                <td rowspan="<?php echo e($rowSpan); ?>" class="px-4 py-3 align-middle font-medium">
                                    <div class="flex items-center gap-2">
                                        <i class="ti ti-ship text-blue-500 text-lg"></i>
                                        <span class="font-semibold text-sm"><?php echo e($vesselName); ?></span>
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded"><?php echo e($rowSpan); ?></span>
                                    </div>
                                </td>
                                <?php endif; ?>
                                <!-- Thumbnail -->
                                <td class="px-4 py-3">
                                    <div x-data="{ loaded: false, imgData: '' }"
                                         x-init="() => {
                                             fetch('<?php echo e(route("images.data", $imgId)); ?>')
                                                 .then(res => res.json())
                                                 .then(data => {
                                                     if (data.success) {
                                                         imgData = data.image_data;
                                                         loaded = true;
                                                     }
                                                 });
                                         }"
                                         class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                                         @click="if(loaded && imgData) { 
                                             modalOpen=true; 
                                             modalImg=imgData; 
                                             modalName='<?php echo e(addslashes($imgName)); ?>'; 
                                             modalMime='<?php echo e($img['image_mime_type'] ?? 'image/jpeg'); ?>';
                                             modalVessel='<?php echo e(addslashes($vesselName)); ?>';
                                             modalInspectionId='<?php echo e(addslashes($inspectionId)); ?>';
                                             modalInspectionDate='<?php echo e(addslashes($inspectionDateFull)); ?>';
                                             modalInspectionLoc='<?php echo e(addslashes($displayLocation)); ?>';
                                             modalInspector='<?php echo e(addslashes($inspectorName)); ?>';
                                             modalRemarks='<?php echo e(addslashes($remarks)); ?>';
                                         }">
                                        <template x-if="loaded && imgData">
                                            <img :src="'data:image/jpeg;base64,' + imgData" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!loaded">
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="animate-spin h-6 w-6" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm"><?php echo e($inspectionId); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="ti ti-calendar text-gray-400 text-xs"></i>
                                        <?php echo e($inspectionDate); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="ti ti-map-pin text-gray-400 text-xs"></i>
                                        <?php echo e($displayLocation); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm"><?php echo e($inspectorName ?: 'N/A'); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($inspectionType): ?>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded"><?php echo e($inspectionType); ?></span>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <a href="<?php echo e(route('images.show', $imgId)); ?>" 
                                           class="text-blue-500 hover:text-blue-700" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <form action="<?php echo e(route('images.destroy', $imgId)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-500 hover:text-red-700" 
                                                    onclick="return confirm('Delete this image?')" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div x-show="viewMode === 'grid'" x-cloak>
        <?php $__currentLoopData = $groupedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vesselName => $vesselImages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">
                    <i class="ti ti-ship text-blue-500"></i> <?php echo e($vesselName); ?>

                </h3>
                <span class="text-sm bg-blue-100 text-blue-700 px-2 py-0.5 rounded"><?php echo e(count($vesselImages)); ?> images</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php $__currentLoopData = $vesselImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $imgId = $img['id'] ?? $img['image_id'] ?? $index;
                    $imgName = $img['image_name'] ?? $img['name'] ?? 'Image';
                    $inspectionId = $img['inspection_id'] ?? $img['inspectionId'] ?? 'N/A';
                    $inspectionType = $img['inspection_type'] ?? $img['inspectionType'] ?? '';
                    $inspectionDateRaw = $img['inspection_date'] ?? $img['inspectionDate'] ?? '';
                    $inspectionDate = !empty($inspectionDateRaw) ? formatDate($inspectionDateRaw) : 'N/A';
                    $inspectionDateFull = !empty($inspectionDateRaw) ? formatFullDate($inspectionDateRaw) : 'N/A';
                    $inspectionLoc = $img['inspection_loc'] ?? $img['inspectionLoc'] ?? $img['location'] ?? '';
                    $inspectorName = $img['inspector_name'] ?? $img['inspectorName'] ?? '';
                    $remarks = $img['remarks'] ?? '';
                    $displayLocation = !empty($inspectionLoc) ? $inspectionLoc : 'N/A';
                ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg border shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition"
                     x-data="{ loaded: false, imgData: '' }"
                     x-init="() => {
                         const observer = new IntersectionObserver((entries) => {
                             if (entries[0].isIntersecting && !loaded) {
                                 fetch('<?php echo e(route("images.data", $imgId)); ?>')
                                     .then(res => res.json())
                                     .then(data => {
                                         if (data.success) {
                                             imgData = data.image_data;
                                             loaded = true;
                                         }
                                     });
                                 observer.disconnect();
                             }
                         });
                         observer.observe($el);
                     }"
                     @click="if(loaded && imgData) { 
                         modalOpen=true; 
                         modalImg=imgData; 
                         modalName='<?php echo e(addslashes($imgName)); ?>'; 
                         modalMime='<?php echo e($img['image_mime_type'] ?? 'image/jpeg'); ?>';
                         modalVessel='<?php echo e(addslashes($vesselName)); ?>';
                         modalInspectionId='<?php echo e(addslashes($inspectionId)); ?>';
                         modalInspectionDate='<?php echo e(addslashes($inspectionDateFull)); ?>';
                         modalInspectionLoc='<?php echo e(addslashes($displayLocation)); ?>';
                         modalInspector='<?php echo e(addslashes($inspectorName)); ?>';
                         modalRemarks='<?php echo e(addslashes($remarks)); ?>';
                     }">
                    
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <template x-if="loaded && imgData">
                            <img :src="'data:image/jpeg;base64,' + imgData" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!loaded">
                            <div class="text-gray-400 text-center">
                                <svg class="animate-spin h-8 w-8 mx-auto" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <p class="text-xs mt-1">Loading...</p>
                            </div>
                        </template>
                    </div>
                    
                    <div class="p-3">
                        <p class="text-sm font-medium truncate"><?php echo e($imgName); ?></p>
                        <p class="text-xs text-gray-500">📋 <?php echo e($inspectionId); ?></p>
                        <?php if($inspectionDate && $inspectionDate !== 'N/A'): ?>
                        <p class="text-xs text-gray-500">📅 <?php echo e($inspectionDate); ?></p>
                        <?php endif; ?>
                        <?php if($displayLocation && $displayLocation !== 'N/A'): ?>
                        <p class="text-xs text-gray-500">📍 <?php echo e($displayLocation); ?></p>
                        <?php endif; ?>
                        <?php if($inspectionType): ?>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded mt-1 inline-block"><?php echo e($inspectionType); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- PAGINATION -->
    <?php if(isset($lastPage) && $lastPage > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for($i = 1; $i <= $lastPage; $i++): ?>
            <a href="<?php echo e(route('images.index', ['page' => $i])); ?>" 
               class="px-3 py-1 rounded <?php echo e($currentPage == $i ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'); ?>">
                <?php echo e($i); ?>

            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <i class="ti ti-photo-off text-5xl text-gray-400"></i>
        <p class="mt-2 text-gray-500">No images found</p>
    </div>
    <?php endif; ?>

    <!-- MODAL WITH DOWNLOAD BUTTON -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" 
         @keydown.escape.window="modalOpen=false">
        <div class="absolute inset-0 bg-black/80" @click="modalOpen=false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 z-10 flex justify-between items-center p-4 border-b">
                <h3 class="font-semibold truncate" x-text="modalName"></h3>
                <div class="flex items-center gap-2">
                    <!-- DOWNLOAD BUTTON -->
                    <button @click="downloadImage(modalImg, modalName)" 
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm flex items-center gap-1 transition">
                        <i class="ti ti-download"></i> Download JPG
                    </button>
                    <button @click="modalOpen=false" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
                </div>
            </div>
            <div class="p-4">
                <!-- Image Display -->
                <div class="flex justify-center mb-4">
                    <img :src="'data:image/jpeg;base64,' + modalImg" class="max-w-full max-h-[50vh] object-contain" id="modalImage">
                </div>
                
                <!-- Image Details -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h4 class="font-semibold mb-3 text-sm">Image Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Vessel:</span>
                            <span class="font-medium" x-text="modalVessel"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Inspection ID:</span>
                            <span class="font-medium" x-text="modalInspectionId"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Inspection Date:</span>
                            <span class="font-medium" x-text="modalInspectionDate"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Inspection Location:</span>
                            <span class="font-medium" x-text="modalInspectionLoc || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Inspector:</span>
                            <span class="font-medium" x-text="modalInspector || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Remarks:</span>
                            <span class="font-medium" x-text="modalRemarks || 'N/A'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
[x-cloak] { display: none !important; }
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ship-inspection-portal\resources\views/images/index.blade.php ENDPATH**/ ?>