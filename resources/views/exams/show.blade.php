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
    },
    approveOpen: false,
    approveUrl: '',
    approveLabel: '',
    approveMsg: '',
    openApprove(url, label, msg) {
        this.approveUrl = url;
        this.approveLabel = label;
        this.approveMsg = msg;
        this.approveOpen = true;
    }
}">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('exams.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium text-navy-700 dark:text-navy-200 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800 hover:border-navy-300 dark:hover:border-navy-700 transition-all shadow-sm">
            <i class="ti ti-arrow-left text-base"></i> Back to VIQ
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
    <div class="bg-white dark:bg-navy-900 rounded-xl border border-gray-200 dark:border-navy-800 shadow-sm p-4 sm:p-5">
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
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300">
                    <i class="ti ti-stack-2 text-xs"></i> Part of an upload batch
                </span>
                @endif
            </div>

            {{-- Review actions are shown ONLY while this exam is still pending; once approved/rejected the decision is final. --}}
            @if($reviewStatus === 'pending')
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-approve-url="{{ route('exams.approve', $exam['id']) }}"
                        data-approve-label="Exam {{ $exam['exam_id'] ?? $exam['id'] }}"
                        @click="openApprove($el.dataset.approveUrl, $el.dataset.approveLabel, 'Approve this questionnaire?')"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-check"></i> Approve
                </button>
                <button type="button"
                        data-reject-url="{{ route('exams.reject', $exam['id']) }}"
                        data-reject-label="Exam {{ $exam['exam_id'] ?? $exam['id'] }}"
                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-x"></i> Reject
                </button>
                @if($batchId)
                <button type="button"
                        data-approve-url="{{ route('exams.batch.approve', $batchId) }}"
                        data-approve-label="the entire upload batch"
                        @click="openApprove($el.dataset.approveUrl, $el.dataset.approveLabel, 'Approve every pending exam in this upload?')"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-stack-2"></i> Approve Entire Upload
                </button>
                <button type="button"
                        data-reject-url="{{ route('exams.batch.reject', $batchId) }}"
                        data-reject-label="the entire upload batch"
                        @click="openReject($el.dataset.rejectUrl, $el.dataset.rejectLabel)"
                        class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 text-sm font-semibold rounded-xl transition-all">
                    <i class="ti ti-stack-2"></i> Reject Entire Upload
                </button>
                @endif
            </div>
            @else
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-navy-800 self-start">
                <i class="ti ti-lock-check"></i> Review finalised — no further action
            </span>
            @endif
        </div>

        @if($exam['review_remarks'] ?? null)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-800 flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
            <i class="ti ti-message-circle text-gray-400 mt-0.5"></i>
            <span>{{ $exam['review_remarks'] }}</span>
        </div>
        @endif
    </div>

    <!-- APPROVE MODAL -->
    <div x-show="approveOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="approveOpen=false">
        <div class="absolute inset-0 bg-black/60" x-show="approveOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="approveOpen=false"></div>
        <div class="relative bg-white dark:bg-navy-900 rounded-2xl w-full max-w-md p-5 shadow-xl border border-slate-200 dark:border-navy-800"
             x-show="approveOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-circle-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                </span>
                <h3 class="font-semibold text-gray-900 dark:text-white">Confirm approval</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1" x-text="approveMsg"></p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4"><span x-text="approveLabel"></span></p>
            <form method="POST" :action="approveUrl">
                @csrf
                <div class="flex gap-2">
                    <button type="button" @click="approveOpen=false"
                            class="flex-1 min-h-[44px] px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-navy-800 dark:hover:bg-navy-800 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 min-h-[44px] px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all">
                        <i class="ti ti-check"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="rejectOpen=false">
        <div class="absolute inset-0 bg-black/60" x-show="rejectOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="rejectOpen=false"></div>
        <div class="relative bg-white dark:bg-navy-900 rounded-2xl w-full max-w-md p-5 shadow-xl border border-slate-200 dark:border-navy-800"
             x-show="rejectOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                <i class="ti ti-x-circle text-rose-500"></i> Reject <span x-text="rejectLabel"></span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">A remark is required when rejecting.</p>
            <form method="POST" :action="rejectUrl">
                @csrf
                <textarea name="remarks" x-model="remarks" required rows="4"
                          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-navy-950/50 border border-gray-200 dark:border-navy-800 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all dark:text-white"
                          placeholder="Reason for rejection..."></textarea>
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="rejectOpen=false"
                            class="flex-1 min-h-[44px] px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-navy-800 dark:hover:bg-navy-800 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-all">
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

    <div class="bg-white dark:bg-navy-900 rounded-xl border border-gray-200 dark:border-navy-800 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $exam['vessel_name'] ?? '—' }}</h2>
                <span class="mono text-xs text-cyan-600 dark:text-cyan-400">{{ $exam['exam_id'] ?? '—' }}</span>
            </div>
            <span class="badge bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 text-sm px-3 py-1">
                Completed
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            @foreach(['person_in_charge' => 'Person In Charge', 'submitted_by' => 'Submitted By', 'email' => 'Email', 'submitted_date' => 'Submitted Date', 'created_at' => 'Created At'] as $key => $label)
            <div class="bg-gray-50 dark:bg-navy-800/40 rounded-lg p-3">
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
            <div class="bg-navy-50 dark:bg-navy-500/10 rounded-lg p-3 text-center border border-navy-200 dark:border-navy-800">
                <p class="text-2xl font-bold text-navy-600 dark:text-navy-300">{{ $exam['statistics']['completion_rate'] }}%</p>
                <p class="text-xs text-navy-700 dark:text-navy-300">Completion Rate</p>
            </div>
        </div>
        @endif

        <!-- Inspection Answers -->
        @if(!empty($exam['processed_answers']))
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="ti ti-list-check text-navy-500 dark:text-cyan-400"></i> Inspection Answers
                <span class="badge bg-navy-100 text-navy-700 dark:bg-navy-500/15 dark:text-navy-200">
                    {{ count($exam['processed_answers']) }} items
                </span>
            </h3>
            
            <div class="bg-gray-50 dark:bg-navy-950/50 rounded-lg border border-gray-200 dark:border-navy-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-navy-800/60 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-24">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Question</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-32">Category</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase w-28">Answer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-72">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-navy-800">
                            @foreach($exam['processed_answers'] as $answer)
                            @php
                                $ansUpper = strtoupper(trim((string)($answer['answer'] ?? '')));
                                $rowAccent = match ($ansUpper) {
                                    'YES' => 'border-l-4 border-emerald-400 dark:border-emerald-500/60',
                                    'NO'  => 'border-l-4 border-rose-400 dark:border-rose-500/60',
                                    default => 'border-l-4 border-amber-400 dark:border-amber-500/60',
                                };
                                $rowHover = match ($ansUpper) {
                                    'YES' => 'hover:bg-emerald-50/60 dark:hover:bg-emerald-900/10',
                                    'NO'  => 'hover:bg-rose-50/60 dark:hover:bg-rose-900/10',
                                    default => 'hover:bg-amber-50/60 dark:hover:bg-amber-900/10',
                                };
                            @endphp
                            <tr class="{{ $rowAccent }} {{ $rowHover }} transition-colors">
                                <td class="px-4 py-3">
                                    <span class="mono text-xs font-mono text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-navy-800 px-2 py-1 rounded">
                                        {{ $answer['key'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $answer['question_text'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 px-2 py-1 rounded">
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