@extends('layouts.app')
@section('title', 'VIQ Records')
@section('page-title', 'VIQ Records')
@section('page-subtitle', 'Ship inspection questionnaires grouped by vessel & upload')

@section('content')
<div class="space-y-6" x-data="{
    rejectOpen: false,
    rejectUrl: '',
    rejectLabel: '',
    remarks: '',
    openReject(url, label) {
        this.rejectUrl = url;
        this.rejectLabel = label;
        this.remarks = '';
        this.rejectOpen = true;
    }
}">
    <!-- FILTERS SECTION -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                    <i class="ti ti-filter text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Filter Records</h3>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                {{ $exams->total() }} total
            </span>
        </div>

        <form method="GET" action="{{ route('exams.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-search text-xs"></i> Search
                </label>
                <div class="relative group">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                    <input type="text" name="search" value="{{ $search }}"
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
                    @foreach($vessels as $v)
                    <option value="{{ $v }}" {{ $vesselName == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-calendar-plus text-xs"></i> Date From
                </label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-calendar-minus text-xs"></i> Date To
                </label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i class="ti ti-clipboard-check text-xs"></i> Review Status
                </label>
                <select name="review_status"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all dark:text-white cursor-pointer">
                    <option value="">All</option>
                    <option value="pending" {{ $reviewStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $reviewStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $reviewStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit"
                        class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-filter text-base"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('exams.index') }}"
                   class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-x text-base"></i>
                    <span class="hidden sm:inline">Clear</span>
                </a>
            </div>
        </form>
    </div>

    <!-- TOOLBAR -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                <div class="absolute inset-0 w-2.5 h-2.5 bg-green-500 rounded-full animate-ping opacity-75"></div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                <span class="font-bold text-gray-900 dark:text-white">{{ $exams->total() }}</span> record(s) found
                <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">
                    <i class="ti ti-arrow-down text-xs"></i> Newest first
                </span>
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <!-- Import Button -->
            <form method="POST" action="{{ route('exams.import') }}" enctype="multipart/form-data" x-data="{ loading: false }" class="relative">
                @csrf
                <label class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-all cursor-pointer border border-gray-200 dark:border-gray-600 hover:shadow-md">
                    <i class="ti ti-upload text-base"></i>
                    <span class="hidden sm:inline">Import</span>
                    <input type="file" name="file" accept=".xlsx,.csv,.xls" class="hidden" @change="loading = true; $el.closest('form').submit()">
                </label>
            </form>

            <!-- Excel Button -->
            <a href="{{ route('exams.export.excel') }}"
               class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-spreadsheet text-base"></i>
                <span class="hidden sm:inline">Excel</span>
            </a>

            <!-- PDF Button -->
            <a href="{{ route('exams.export.pdf') }}"
               class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-type-pdf text-base"></i>
                <span class="hidden sm:inline">PDF</span>
            </a>
        </div>
    </div>

    @php
        // Batch-level review status -> [pill classes, label, icon]. 'mixed' is a batch that
        // is fully decided but split between approved and rejected PICs.
        $reviewPill = function ($status) {
            return match ($status) {
                'approved' => ['bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300', 'Approved', 'ti-circle-check'],
                'rejected' => ['bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300', 'Rejected', 'ti-circle-x'],
                'mixed'    => ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300', 'Partial', 'ti-progress-check'],
                default    => ['bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300', 'Pending', 'ti-clock'],
            };
        };
        // Per-PIC status dot colour.
        $picDot = function ($status) {
            return match ($status) {
                'approved' => 'bg-emerald-500',
                'rejected' => 'bg-rose-500',
                default    => 'bg-amber-500',
            };
        };
    @endphp

    <!-- VESSEL > UPLOAD BATCH > PIC -->
    <div class="space-y-5">
        @forelse($vesselGroups as $vessel)
        <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <!-- Vessel header: the vessel name appears ONCE per section -->
            <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-3.5 bg-gradient-to-r from-navy-50 to-indigo-50 dark:from-navy-900/30 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center shadow-md flex-shrink-0">
                        <i class="ti ti-ship text-white text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900 dark:text-white text-base sm:text-lg leading-tight truncate">{{ $vessel['vessel_name'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ count($vessel['batches']) }} {{ \Illuminate\Support\Str::plural('upload', count($vessel['batches'])) }}
                            · {{ $vessel['pic_count'] }} PIC
                        </p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/70 dark:bg-gray-900/40 text-navy-700 dark:text-navy-200 border border-navy-100 dark:border-navy-800">
                    <i class="ti ti-clipboard-list"></i> {{ $vessel['pic_count'] }} VIQ
                </span>
            </div>

            <!-- Upload batches -->
            <div class="p-3 sm:p-4 space-y-3">
                @foreach($vessel['batches'] as $batch)
                @php [$pillClass, $pillLabel, $pillIcon] = $reviewPill($batch['status']); @endphp
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30">
                    <!-- Batch header: date, submitter, status pill, PIC count, batch id, actions -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-3 sm:p-4 border-b border-gray-100 dark:border-gray-700/60">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                                <i class="ti {{ $batch['is_legacy'] ? 'ti-file-text' : 'ti-stack-2' }} text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $pillClass }}">
                                        <i class="ti {{ $pillIcon }} text-xs"></i> {{ $pillLabel }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200/70 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        <i class="ti ti-users text-xs"></i> {{ $batch['pic_count'] }} PIC
                                    </span>
                                    @if($batch['is_legacy'])
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 italic">single submission</span>
                                    @endif
                                </div>
                                <div class="mt-1.5 flex items-center gap-x-3 gap-y-0.5 flex-wrap text-[11px] text-gray-500 dark:text-gray-400">
                                    @php $bts = !empty($batch['submitted_date']) ? strtotime($batch['submitted_date']) : false; @endphp
                                    <span class="inline-flex items-center gap-1"><i class="ti ti-calendar-time"></i>{{ $bts ? date('M d, Y', $bts) : '—' }}</span>
                                    <span class="inline-flex items-center gap-1"><i class="ti ti-user-check"></i>{{ $batch['submitted_by'] ?? '—' }}</span>
                                    @if(!$batch['is_legacy'])
                                    <span class="inline-flex items-center gap-1 font-mono" title="Upload batch ID"><i class="ti ti-hash"></i>{{ \Illuminate\Support\Str::limit($batch['batch_id'], 14) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Batch-level actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($batch['is_legacy'])
                                @php $one = $batch['items'][0]; @endphp
                                <form method="POST" action="{{ route('exams.approve', $one['id'] ?? 0) }}" class="flex-1 md:flex-none" onsubmit="return confirm('Approve this submission?')">
                                    @csrf
                                    <button type="submit" class="w-full min-h-[44px] inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all">
                                        <i class="ti ti-check"></i> Approve
                                    </button>
                                </form>
                                <button type="button"
                                        data-reject-url="{{ route('exams.reject', $one['id'] ?? 0) }}"
                                        data-reject-label="{{ $vessel['vessel_name'] }} · {{ $one['person_in_charge'] ?? ('Exam ' . ($one['exam_id'] ?? $one['id'] ?? '')) }}"
                                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                                        class="flex-1 md:flex-none min-h-[44px] inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg transition-all">
                                    <i class="ti ti-x"></i> Reject
                                </button>
                            @else
                                <form method="POST" action="{{ route('exams.batch.approve', $batch['batch_id']) }}" class="flex-1 md:flex-none" onsubmit="return confirm('Approve all {{ $batch['pic_count'] }} PIC in this upload?')">
                                    @csrf
                                    <button type="submit" class="w-full min-h-[44px] inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all">
                                        <i class="ti ti-check"></i> <span class="whitespace-nowrap">Approve Upload</span>
                                    </button>
                                </form>
                                <button type="button"
                                        data-reject-url="{{ route('exams.batch.reject', $batch['batch_id']) }}"
                                        data-reject-label="{{ $vessel['vessel_name'] }} upload ({{ $batch['pic_count'] }} PIC)"
                                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                                        class="flex-1 md:flex-none min-h-[44px] inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg transition-all">
                                    <i class="ti ti-x"></i> <span class="whitespace-nowrap">Reject Upload</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- PICs: compact chips, each links to its exam detail — no vessel repeated -->
                    <div class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                        @foreach($batch['items'] as $exam)
                        @php $ps = $exam['review_status'] ?? 'pending'; @endphp
                        <a href="{{ route('exams.show', $exam['id'] ?? 0) }}"
                           class="group flex items-center gap-2.5 min-h-[44px] px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-sm transition-all">
                            <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $picDot($ps) }}" title="{{ ucfirst($ps) }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $exam['person_in_charge'] ?? '—' }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono truncate">{{ $exam['exam_id'] ?? ('#' . ($exam['id'] ?? '')) }}</p>
                            </div>
                            <i class="ti ti-chevron-right text-gray-300 dark:text-gray-600 group-hover:text-indigo-500 flex-shrink-0"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-inner">
                <i class="ti ti-clipboard-off text-4xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <p class="font-bold text-gray-600 dark:text-gray-400 text-lg mt-4">No VIQ records found</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters or check the API connection.</p>
            <a href="{{ route('exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 mt-4 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-xl transition-all">
                <i class="ti ti-refresh"></i> Clear Filters
            </a>
        </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    @if($exams->hasPages())
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-4 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $exams->firstItem() }}</span>
                    to <span class="font-semibold text-gray-900 dark:text-white">{{ $exams->lastItem() }}</span>
                    of <span class="font-semibold text-gray-900 dark:text-white">{{ $exams->total() }}</span> PIC row(s)
                </div>

                <div class="flex items-center gap-2">
                    @if($exams->onFirstPage())
                        <span class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="ti ti-chevron-left text-sm"></i>
                        </span>
                    @else
                        <a href="{{ $exams->previousPageUrl() }}"
                           class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">
                            <i class="ti ti-chevron-left text-sm"></i>
                        </a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
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
                        @endphp
                    </div>

                    @if($exams->hasMorePages())
                        <a href="{{ $exams->nextPageUrl() }}"
                           class="px-3 py-1.5 text-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-all hover:shadow-md">
                            <i class="ti ti-chevron-right text-sm"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="ti ti-chevron-right text-sm"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- REJECT MODAL -->
    <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="rejectOpen=false">
        <div class="absolute inset-0 bg-black/60" @click="rejectOpen=false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md p-5 shadow-xl">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                <i class="ti ti-x-circle text-rose-500"></i> Reject <span x-text="rejectLabel"></span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">A remark is required when rejecting.</p>
            <form method="POST" :action="rejectUrl">
                @csrf
                <textarea name="remarks" x-model="remarks" required rows="4"
                          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all dark:text-white"
                          placeholder="Reason for rejection..."></textarea>
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="rejectOpen=false"
                            class="flex-1 min-h-[44px] px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!remarks.trim()"
                            class="flex-1 min-h-[44px] px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-all">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
