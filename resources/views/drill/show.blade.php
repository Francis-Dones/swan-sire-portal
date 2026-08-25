@extends('layouts.app')

@section('title', 'Drill Result')
@section('page-title', 'Drill Result')
@section('page-subtitle', 'Full per-question answers')

@section('content')
@php
    if (!function_exists('drillFormatDate')) {
        function drillFormatDate($date) {
            if (empty($date)) return 'N/A';
            $ts = strtotime($date);
            return $ts === false ? $date : date('M d, Y · H:i', $ts);
        }
    }
    $score = (int) ($submission['score'] ?? 0);
    $total = (int) ($submission['total'] ?? count($submission['answers'] ?? []));
    $pct = isset($submission['percentage']) && is_numeric($submission['percentage'])
        ? (int) round($submission['percentage'])
        : ($total > 0 ? (int) round(($score / $total) * 100) : 0);
    $pill = $pct >= 80 ? 'pill-approved' : ($pct >= 50 ? 'pill-pending' : 'pill-rejected');
@endphp

<div class="max-w-3xl mx-auto space-y-5">

    <a href="{{ route('drill.results') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-navy-600 dark:text-navy-300 hover:underline">
        <i class="ti ti-arrow-left"></i> Back to results
    </a>

    {{-- ══ SUMMARY ══ --}}
    <div class="card p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center flex-shrink-0 shadow-md"><i class="ti ti-ship text-white"></i></span>
                    <div class="min-w-0">
                        <h2 class="font-bold text-slate-900 dark:text-white text-lg leading-tight truncate">{{ $submission['vessel_name'] ?? 'N/A' }}</h2>
                        <p class="text-sm text-slate-400">{{ $submission['rank'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white tnum">{{ $score }}<span class="text-slate-300 dark:text-slate-600">/</span>{{ $total }}</p>
                <span class="pill {{ $pill }} mt-1">{{ $pct }}%</span>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-navy-800 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <i class="ti ti-user-check text-slate-400"></i> Submitted by
                <span class="font-medium text-slate-800 dark:text-slate-100">{{ $submission['submitted_by'] ?? 'Unknown' }}</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <i class="ti ti-calendar text-slate-400"></i> Submitted
                <span class="font-medium text-slate-800 dark:text-slate-100">{{ drillFormatDate($submission['submitted_date'] ?? null) }}</span>
            </div>
            @if(!empty($submission['email']))
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <i class="ti ti-mail text-slate-400"></i> Email
                <span class="font-medium text-slate-800 dark:text-slate-100 truncate">{{ $submission['email'] }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ ANSWERS ══ --}}
    @php $answers = $submission['answers'] ?? []; @endphp
    @if(count($answers) > 0)
    <div class="space-y-3">
        <h3 class="font-bold text-slate-800 dark:text-slate-100 px-1">Answers ({{ count($answers) }})</h3>
        @foreach($answers as $i => $a)
        @php
            $ok = (bool) ($a['is_correct'] ?? false);
            $type = ($a['question_type'] ?? '') === 'requirement' ? 'requirement' : 'topic';
        @endphp
        <div class="card p-4 {{ $ok ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1.5
                        {{ $type === 'requirement' ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300' : 'bg-navy-100 text-navy-700 dark:bg-navy-900/40 dark:text-navy-300' }}">
                        {{ $type === 'requirement' ? 'Verify the requirement' : 'Identify the topic' }}
                    </span>
                    <p class="text-xs text-slate-400 mono">Q{{ $a['question_id'] ?? ($i + 1) }}</p>
                </div>
                <span class="pill {{ $ok ? 'pill-approved' : 'pill-rejected' }} flex-shrink-0">{{ $ok ? 'Correct' : 'Wrong' }}</span>
            </div>
            <div class="mt-3 space-y-1.5 text-sm">
                <p class="flex items-start gap-2">
                    <span class="text-slate-400 dark:text-slate-500 flex-shrink-0">Chosen:</span>
                    <span class="{{ $ok ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-rose-700 dark:text-rose-400 font-medium' }}">{{ $a['chosen_text'] ?? '— (unanswered)' }}</span>
                </p>
                @unless($ok)
                <p class="flex items-start gap-2">
                    <span class="text-slate-400 dark:text-slate-500 flex-shrink-0">Correct:</span>
                    <span class="text-emerald-700 dark:text-emerald-400 font-medium">{{ $a['correct_text'] ?? 'N/A' }}</span>
                </p>
                @endunless
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card p-6 text-center text-slate-500 dark:text-slate-400">
        <i class="ti ti-clipboard-off text-3xl text-slate-300 dark:text-slate-600"></i>
        <p class="mt-2 text-sm">No per-question answers were recorded for this submission.</p>
    </div>
    @endif

</div>
@endsection
