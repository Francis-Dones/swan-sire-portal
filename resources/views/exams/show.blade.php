@extends('layouts.app')
@section('title', 'Exam Detail')
@section('page-title', 'Exam Detail')
@section('page-subtitle', $exam['exam_id'] ?? 'View Record')

@section('content')
<div class="max-w-7xl mx-auto space-y-4" x-data="{
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
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('exams.index') }}" class="btn-outline text-xs py-1.5 px-3">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>

    @php
        $reviewStatus = $exam['review_status'] ?? 'pending';
        $reviewPillClass = match ($reviewStatus) {
            'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        };
        $batchId = $exam['upload_batch_id'] ?? null;
    @endphp

    <!-- REVIEW BANNER -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold {{ $reviewPillClass }}">
                    <i class="ti ti-clipboard-check"></i> {{ ucfirst($reviewStatus) }}
                </span>
                @if($exam['reviewed_by'] ?? null)
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    by <span class="font-medium text-gray-800 dark:text-gray-200">{{ $exam['reviewed_by'] }}</span>
                    @if($exam['reviewed_at'] ?? null)
                        on {{ date('M d, Y H:i', strtotime($exam['reviewed_at'])) }}
                    @endif
                </span>
                @endif
                @if($batchId)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-800/50 text-indigo-700 dark:text-indigo-300">
                    <i class="ti ti-stack-2 text-xs"></i> Part of an upload batch
                </span>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('exams.approve', $exam['id']) }}" onsubmit="return confirm('Approve this exam?')">
                    @csrf
                    <button type="submit" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all">
                        <i class="ti ti-check"></i> Approve
                    </button>
                </form>
                <button type="button"
                        data-reject-url="{{ route('exams.reject', $exam['id']) }}"
                        data-reject-label="Exam {{ $exam['exam_id'] ?? $exam['id'] }}"
                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-x"></i> Reject
                </button>
                @if($batchId)
                <form method="POST" action="{{ route('exams.batch.approve', $batchId) }}" onsubmit="return confirm('Approve every exam in this upload?')">
                    @csrf
                    <button type="submit" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-sm font-semibold rounded-xl transition-all">
                        <i class="ti ti-stack-2"></i> Approve Entire Upload
                    </button>
                </form>
                <button type="button"
                        data-reject-url="{{ route('exams.batch.reject', $batchId) }}"
                        data-reject-label="the entire upload batch"
                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-stack-2"></i> Reject Entire Upload
                </button>
                @endif
            </div>
        </div>

        @if($exam['review_remarks'] ?? null)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
            <i class="ti ti-message-circle text-gray-400 mt-0.5"></i>
            <span>{{ $exam['review_remarks'] }}</span>
        </div>
        @endif
    </div>

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

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $exam['vessel_name'] ?? '—' }}</h2>
                <span class="mono text-xs text-blue-600 dark:text-blue-400">{{ $exam['exam_id'] ?? '—' }}</span>
            </div>
            <span class="badge bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 text-sm px-3 py-1">
                Completed
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            @foreach(['person_in_charge' => 'Person In Charge', 'submitted_by' => 'Submitted By', 'email' => 'Email', 'submitted_date' => 'Submitted Date', 'created_at' => 'Created At'] as $key => $label)
            <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ isset($exam[$key]) ? (in_array($key, ['submitted_date', 'created_at']) ? date('M d, Y H:i', strtotime($exam[$key])) : $exam[$key]) : '—' }}
                </p>
            </div>
            @endforeach
        </div>

        <!-- Statistics Summary -->
        @if(isset($exam['statistics']))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-3 text-center border border-emerald-200 dark:border-emerald-800">
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $exam['statistics']['yes_count'] }}</p>
                <p class="text-xs text-emerald-700 dark:text-emerald-300">YES Answers</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center border border-red-200 dark:border-red-800">
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $exam['statistics']['no_count'] }}</p>
                <p class="text-xs text-red-700 dark:text-red-300">NO Answers</p>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center border border-amber-200 dark:border-amber-800">
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $exam['statistics']['not_answered_count'] }}</p>
                <p class="text-xs text-amber-700 dark:text-amber-300">Not Answered</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-200 dark:border-blue-800">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $exam['statistics']['completion_rate'] }}%</p>
                <p class="text-xs text-blue-700 dark:text-blue-300">Completion Rate</p>
            </div>
        </div>
        @endif

        <!-- Inspection Answers -->
        @if(!empty($exam['processed_answers']))
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="ti ti-list-check text-blue-500"></i> Inspection Answers
                <span class="badge bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ count($exam['processed_answers']) }} items
                </span>
            </h3>
            
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700/60 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-24">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Question</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-32">Category</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase w-28">Answer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-72">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($exam['processed_answers'] as $answer)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="mono text-xs font-mono text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">
                                        {{ $answer['key'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $answer['question_text'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 px-2 py-1 rounded">
                                        {{ $answer['category'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {!! $answer['status_class'] !!}">
                                        {!! $answer['status_badge'] !!}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    @if($answer['remarks'])
                                        <div class="flex items-start gap-2">
                                            <i class="ti ti-message-circle text-gray-400 text-sm mt-0.5"></i>
                                            <span>{{ $answer['remarks'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-400">
            <i class="ti ti-clipboard-off text-4xl block mb-2"></i>
            <p>No answers recorded for this inspection</p>
        </div>
        @endif
    </div>
</div>
@endsection