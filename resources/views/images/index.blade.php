@extends('layouts.app')

@section('title', 'Inspection Images')
@section('page-title', 'Inspection Images')
@section('page-subtitle', 'Browse & manage inspection photos')

@section('content')
<div class="space-y-5" x-data="{
    modalOpen: false,
    modalImgSrc: '',
    modalName: '',
    modalMime: '',
    modalVessel: '',
    modalInspectionId: '',
    modalInspectionDate: '',
    modalInspectionLoc: '',
    modalInspector: '',
    modalRemarks: '',
    modalDownloadUrl: '',
    viewMode: 'grid'
}">

    @if(isset($apiError) && $apiError)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300 rounded-2xl p-4 flex items-center gap-2 text-sm">
        <i class="ti ti-alert-triangle text-lg"></i> {{ $apiError }}
    </div>
    @endif

    <!-- VESSEL SELECTOR -->
    <form method="GET" action="{{ route('images.index') }}"
          class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300 flex items-center gap-2 shrink-0">
            <span class="w-8 h-8 rounded-lg bg-navy-100 dark:bg-navy-900/40 flex items-center justify-center">
                <i class="ti ti-ship text-navy-600 dark:text-navy-300"></i>
            </span>
            Vessel
        </label>
        <select name="vessel_name" onchange="this.form.submit()"
                class="flex-1 min-h-[44px] px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-navy-500/20 focus:border-navy-500 transition-all dark:text-white cursor-pointer">
            @if(empty($vessels ?? []))
            <option value="">No vessels found</option>
            @endif
            @foreach(($vessels ?? []) as $v)
            <option value="{{ $v['name'] }}" {{ ($selectedVesselName ?? '') === $v['name'] ? 'selected' : '' }}>{{ $v['name'] }}</option>
            @endforeach
        </select>
    </form>

    @if(empty($vessels ?? []) || empty($selectedVesselName ?? null))
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-inner">
            <i class="ti ti-ship text-4xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <p class="mt-4 font-semibold text-gray-600 dark:text-gray-400">Select a vessel to view its inspection images.</p>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 rounded-2xl p-4 flex items-center gap-2 text-sm">
        <i class="ti ti-check-circle text-lg"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 text-rose-700 dark:text-rose-300 rounded-2xl p-4 flex items-center gap-2 text-sm">
        <i class="ti ti-alert-circle text-lg"></i> {{ session('error') }}
    </div>
    @endif

    @if(!empty($selectedVesselName ?? null))
    <!-- TOOLBAR -->
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                    <div class="absolute inset-0 w-2.5 h-2.5 bg-green-500 rounded-full animate-ping opacity-75"></div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($total ?? 0) }}</span> image(s)
                </p>
            </div>
            <!-- View Mode Toggle -->
            <div class="flex bg-gray-100 dark:bg-gray-700/60 rounded-xl p-1">
                <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-800 shadow-sm text-navy-600 dark:text-navy-300' : 'text-gray-500 dark:text-gray-400'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all">
                    <i class="ti ti-layout-grid"></i> Grid
                </button>
                <button @click="viewMode = 'table'"
                        :class="viewMode === 'table' ? 'bg-white dark:bg-gray-800 shadow-sm text-navy-600 dark:text-navy-300' : 'text-gray-500 dark:text-gray-400'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all">
                    <i class="ti ti-list"></i> Table
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            @if(($images ?? []) && count($images) > 0)
            <a href="{{ route('images.export.excel') }}"
               class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                <i class="ti ti-file-spreadsheet"></i> <span class="hidden sm:inline">Excel</span>
            </a>
            <a href="{{ route('images.export.pdf') }}"
               class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                <i class="ti ti-file-type-pdf"></i> <span class="hidden sm:inline">PDF</span>
            </a>
            @endif
        </div>
    </div>

    @if(isset($images) && count($images) > 0)
    @php
        // Group images by vessel name (identity is vessel_name; vessel_id is a dead column
        // kept only as a defensive fallback so a missing name never yields an empty key).
        $groupedImages = [];
        foreach($images as $img) {
            $vesselKey = $img['vessel_name'] ?? $img['vessel_id'] ?? 'Unknown Vessel';
            if (!isset($groupedImages[$vesselKey])) {
                $groupedImages[$vesselKey] = [];
            }
            $groupedImages[$vesselKey][] = $img;
        }

        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (empty($date)) return 'N/A';
                $timestamp = strtotime($date);
                return $timestamp === false ? $date : date('M d, Y', $timestamp);
            }
        }
        if (!function_exists('formatFullDate')) {
            function formatFullDate($date) {
                if (empty($date)) return 'N/A';
                $timestamp = strtotime($date);
                return $timestamp === false ? $date : date('F d, Y', $timestamp);
            }
        }
    @endphp

    <!-- GRID VIEW -->
    <div x-show="viewMode === 'grid'" x-cloak class="space-y-6">
        @foreach($groupedImages as $vesselName => $vesselImages)
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-4 sm:px-5 py-3.5 bg-gradient-to-r from-navy-50 to-indigo-50 dark:from-navy-900/30 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center shadow-md flex-shrink-0">
                    <i class="ti ti-ship text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white text-base sm:text-lg leading-tight truncate">{{ $vesselName }}</h3>
                <span class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/70 dark:bg-gray-900/40 text-navy-700 dark:text-navy-200 border border-navy-100 dark:border-navy-800">
                    <i class="ti ti-photo"></i> {{ count($vesselImages) }}
                </span>
            </div>
            <div class="p-3 sm:p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @foreach($vesselImages as $index => $img)
                @php
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
                @endphp
                <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:shadow-md hover:border-navy-300 dark:hover:border-navy-600 transition-all"
                     @click="modalOpen=true;
                         modalImgSrc='{{ route('images.raw', $imgId) }}';
                         modalName='{{ addslashes($imgName) }}';
                         modalMime='{{ addslashes($img['image_mime_type'] ?? 'image/jpeg') }}';
                         modalVessel='{{ addslashes($vesselName) }}';
                         modalInspectionId='{{ addslashes($inspectionId) }}';
                         modalInspectionDate='{{ addslashes($inspectionDateFull) }}';
                         modalInspectionLoc='{{ addslashes($displayLocation) }}';
                         modalInspector='{{ addslashes($inspectorName) }}';
                         modalRemarks='{{ addslashes($remarks) }}';
                         modalDownloadUrl='{{ route('images.download', $imgId) }}';">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-900 overflow-hidden">
                        <img src="{{ route('images.raw', $imgId) }}" loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-3 space-y-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $imgName }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate">
                            <i class="ti ti-clipboard-list text-gray-400"></i> {{ $inspectionId }}
                        </p>
                        @if($inspectionDate && $inspectionDate !== 'N/A')
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate">
                            <i class="ti ti-calendar text-gray-400"></i> {{ $inspectionDate }}
                        </p>
                        @endif
                        @if($displayLocation && $displayLocation !== 'N/A')
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate">
                            <i class="ti ti-map-pin text-gray-400"></i> {{ $displayLocation }}
                        </p>
                        @endif
                        @if($inspectionType)
                        <span class="inline-block text-[11px] font-medium bg-navy-100 dark:bg-navy-900/40 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded-full mt-1">{{ $inspectionType }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endforeach
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vessel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inspection ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inspector</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($groupedImages as $vesselName => $vesselImages)
                            @php $rowSpan = count($vesselImages); @endphp
                            @foreach($vesselImages as $index => $img)
                                @php
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
                                    $displayLocation = !empty($inspectionLoc) ? $inspectionLoc : 'N/A';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    @if($index === 0)
                                    <td rowspan="{{ $rowSpan }}" class="px-4 py-3 align-top font-medium border-r border-gray-100 dark:border-gray-700/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-lg bg-navy-100 dark:bg-navy-900/40 flex items-center justify-center flex-shrink-0">
                                                <i class="ti ti-ship text-navy-600 dark:text-navy-300"></i>
                                            </span>
                                            <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $vesselName }}</span>
                                            <span class="text-[11px] font-bold bg-navy-100 dark:bg-navy-900/40 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded-full">{{ $rowSpan }}</span>
                                        </div>
                                    </td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden cursor-pointer"
                                             @click="modalOpen=true;
                                                 modalImgSrc='{{ route('images.raw', $imgId) }}';
                                                 modalName='{{ addslashes($imgName) }}';
                                                 modalMime='{{ addslashes($img['image_mime_type'] ?? 'image/jpeg') }}';
                                                 modalVessel='{{ addslashes($vesselName) }}';
                                                 modalInspectionId='{{ addslashes($inspectionId) }}';
                                                 modalInspectionDate='{{ addslashes($inspectionDateFull) }}';
                                                 modalInspectionLoc='{{ addslashes($displayLocation) }}';
                                                 modalInspector='{{ addslashes($inspectorName) }}';
                                                 modalRemarks='{{ addslashes($remarks) }}';
                                                 modalDownloadUrl='{{ route('images.download', $imgId) }}';">
                                            <img src="{{ route('images.raw', $imgId) }}" loading="lazy" class="w-full h-full object-cover">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $inspectionId }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center gap-1"><i class="ti ti-calendar text-gray-400 text-xs"></i>{{ $inspectionDate }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center gap-1"><i class="ti ti-map-pin text-gray-400 text-xs"></i>{{ $displayLocation }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $inspectorName ?: 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @if($inspectionType)
                                        <span class="text-[11px] font-medium bg-navy-100 dark:bg-navy-900/40 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded-full">{{ $inspectionType }}</span>
                                        @else
                                        <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('images.show', $imgId) }}"
                                               class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-navy-600 dark:text-navy-300 hover:bg-navy-50 dark:hover:bg-navy-900/40 transition-colors" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <form action="{{ route('images.destroy', $imgId) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                                                        onclick="return confirm('Delete this image?')" title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINATION -->
    @if(isset($lastPage) && $lastPage > 1)
    <div class="flex justify-center flex-wrap gap-2 mt-2">
        @for($i = 1; $i <= $lastPage; $i++)
            <a href="{{ route('images.index', ['page' => $i, 'vessel_name' => $selectedVesselName]) }}"
               class="min-w-[40px] min-h-[40px] inline-flex items-center justify-center px-3 py-1.5 text-sm rounded-lg transition-all
                      {{ $currentPage == $i
                         ? 'bg-gradient-to-r from-navy-600 to-navy-800 text-white shadow-md font-semibold'
                         : 'bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600' }}">
                {{ $i }}
            </a>
        @endfor
    </div>
    @endif

    @else
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-inner">
            <i class="ti ti-photo-off text-4xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <p class="mt-4 font-semibold text-gray-600 dark:text-gray-400">No images found for this vessel.</p>
    </div>
    @endif
    @endif

    <!-- MODAL WITH DOWNLOAD BUTTON -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="modalOpen=false">
        <div class="absolute inset-0 bg-black/80" @click="modalOpen=false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-auto shadow-2xl">
            <div class="sticky top-0 bg-white dark:bg-gray-800 z-10 flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white truncate" x-text="modalName"></h3>
                <div class="flex items-center gap-2">
                    <a :href="modalDownloadUrl"
                       class="min-h-[40px] inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-lg transition-all">
                        <i class="ti ti-download"></i> Download JPG
                    </a>
                    <button @click="modalOpen=false"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="flex justify-center mb-4">
                    <img :src="modalImgSrc" class="max-w-full max-h-[50vh] object-contain rounded-lg" id="modalImage">
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 text-sm">Image Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-500 dark:text-gray-400">Vessel:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalVessel"></span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Inspection ID:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalInspectionId"></span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Inspection Date:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalInspectionDate"></span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Inspection Location:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalInspectionLoc || 'N/A'"></span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Inspector:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalInspector || 'N/A'"></span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Remarks:</span> <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modalRemarks || 'N/A'"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
