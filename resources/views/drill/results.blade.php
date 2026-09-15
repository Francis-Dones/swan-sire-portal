@extends('layouts.app')

@section('title', 'Drill Results')
@section('page-title', 'SIRE Drill Results')
@section('page-subtitle', 'Submitted readiness drills from crew & shore')

@section('content')
@php
    if (!function_exists('drillFormatDate')) {
        function drillFormatDate($date) {
            if (empty($date)) return 'N/A';
            $ts = strtotime($date);
            return $ts === false ? $date : date('M d, Y · H:i', $ts);
        }
    }
    if (!function_exists('drillPct')) {
        function drillPct($s) {
            $score = (int) ($s['score'] ?? 0);
            $total = (int) ($s['total'] ?? 0);
            if (isset($s['percentage']) && is_numeric($s['percentage'])) return (int) round($s['percentage']);
            return $total > 0 ? (int) round(($score / $total) * 100) : 0;
        }
    }
    if (!function_exists('drillPill')) {
        function drillPill($pct) {
            return $pct >= 80 ? 'pill-approved' : ($pct >= 50 ? 'pill-pending' : 'pill-rejected');
        }
    }
@endphp

<div class="space-y-6">

    {{-- ══ FILTERS ══ --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-navy-800">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-navy-500 to-cyan-600 rounded-lg">
                    <i class="ti ti-filter text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-slate-700 dark:text-slate-300">Filter Results</h3>
            </div>
            <span class="text-xs text-slate-400 bg-slate-100 dark:bg-navy-800 px-2 py-1 rounded-full tnum">
                {{ $submissions->total() }} total
            </span>
        </div>

        <form method="GET" action="{{ route('drill.results') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 flex items-center gap-1"><i class="ti ti-ship text-xs"></i> Vessel</label>
                <select name="vessel_name" class="form-input cursor-pointer">
                    <option value="">All Vessels</option>
                    @foreach($vessels as $v)
                        @php
                            // Safely extract the vessel name if it's an array
                            $vName = is_array($v) ? ($v['name'] ?? $v['vessel_name'] ?? reset($v)) : $v;
                        @endphp
                        <option value="{{ $vName }}" {{ $vesselName === $vName ? 'selected' : '' }}>{{ $vName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 flex items-center gap-1"><i class="ti ti-user text-xs"></i> Rank</label>
                <select name="rank" class="form-input cursor-pointer">
                    <option value="">All Ranks</option>
                    @foreach($ranks as $r)
                    <option value="{{ $r['key'] }}" {{ $rank === $r['key'] ? 'selected' : '' }}>{{ $r['key'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 flex items-center gap-1"><i class="ti ti-user-check text-xs"></i> Submitted By</label>
                <input type="text" name="submitted_by" value="{{ $submittedBy }}" placeholder="Name" class="form-input">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 flex items-center gap-1"><i class="ti ti-calendar-plus text-xs"></i> Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 flex items-center gap-1"><i class="ti ti-calendar-minus text-xs"></i> Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex gap-2 pt-1">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Apply</button>
                <a href="{{ route('drill.results') }}" class="btn btn-outline"><i class="ti ti-x"></i> Clear</a>
            </div>
        </form>
    </div>

    @if($submissions->count() > 0)
    {{-- ══ TABLE (sm+) ══ --}}
    <div class="card overflow-hidden hidden sm:block">
        <div class="overflow-x-auto nice-scroll">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-navy-800/50 border-b border-slate-200 dark:border-navy-800">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vessel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rank</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-navy-800/50">
                    @foreach($submissions as $s)
                    @php $pct = drillPct($s); @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-800/40 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1"><i class="ti ti-calendar text-slate-400 text-xs"></i>{{ drillFormatDate($s['submitted_date'] ?? null) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $s['submitted_by'] ?? 'Unknown' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg bg-navy-100 dark:bg-navy-900/40 flex items-center justify-center flex-shrink-0"><i class="ti ti-ship text-navy-600 dark:text-navy-300 text-xs"></i></span>
                                {{ $s['vessel_name'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $s['rank'] ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="pill {{ drillPill($pct) }}">{{ (int)($s['score'] ?? 0) }}/{{ (int)($s['total'] ?? 0) }} · {{ $pct }}%</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('drill.show', $s['id'] ?? 0) }}"
                               class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-navy-600 dark:text-navy-300 hover:bg-navy-50 dark:hover:bg-navy-900/40 transition-colors" title="View answers">
                                <i class="ti ti-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ CARDS (mobile) ══ --}}
    <div class="space-y-3 sm:hidden">
        @foreach($submissions as $s)
        @php $pct = drillPct($s); @endphp
        <a href="{{ route('drill.show', $s['id'] ?? 0) }}" class="card p-4 block active:scale-[.99] transition-transform">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $s['vessel_name'] ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $s['rank'] ?? 'N/A' }}</p>
                </div>
                <span class="pill {{ drillPill($pct) }} flex-shrink-0">{{ (int)($s['score'] ?? 0) }}/{{ (int)($s['total'] ?? 0) }} · {{ $pct }}%</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-navy-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="inline-flex items-center gap-1"><i class="ti ti-user"></i> {{ $s['submitted_by'] ?? 'Unknown' }}</span>
                <span class="inline-flex items-center gap-1"><i class="ti ti-calendar"></i> {{ drillFormatDate($s['submitted_date'] ?? null) }}</span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ══ PAGINATION ══ --}}
    @if($submissions->hasPages())
    <div class="card px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-slate-600 dark:text-slate-400">
            Showing <span class="font-semibold text-slate-900 dark:text-white">{{ $submissions->firstItem() }}</span>
            to <span class="font-semibold text-slate-900 dark:text-white">{{ $submissions->lastItem() }}</span>
            of <span class="font-semibold text-slate-900 dark:text-white">{{ $submissions->total() }}</span> result(s)
        </div>
        <div class="flex items-center gap-1">
            @if($submissions->onFirstPage())
                <span class="px-3 py-1.5 text-sm bg-slate-100 dark:bg-navy-800 text-slate-400 rounded-lg cursor-not-allowed"><i class="ti ti-chevron-left text-sm"></i></span>
            @else
                <a href="{{ $submissions->previousPageUrl() }}" class="px-3 py-1.5 text-sm bg-white dark:bg-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-navy-800 rounded-lg transition-all"><i class="ti ti-chevron-left text-sm"></i></a>
            @endif
            @php
                $cp = $submissions->currentPage();
                $lp = $submissions->lastPage();
                $start = max(1, $cp - 2);
                $end = min($lp, $cp + 2);
            @endphp
            @for($p = $start; $p <= $end; $p++)
                @if($p == $cp)
                    <span class="px-3 py-1.5 text-sm bg-gradient-to-r from-navy-600 to-cyan-600 text-white rounded-lg shadow-md font-semibold tnum">{{ $p }}</span>
                @else
                    <a href="{{ $submissions->url($p) }}" class="px-3 py-1.5 text-sm bg-white dark:bg-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-navy-800 rounded-lg transition-all tnum">{{ $p }}</a>
                @endif
            @endfor
            @if($submissions->hasMorePages())
                <a href="{{ $submissions->nextPageUrl() }}" class="px-3 py-1.5 text-sm bg-white dark:bg-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-navy-800 rounded-lg transition-all"><i class="ti ti-chevron-right text-sm"></i></a>
            @else
                <span class="px-3 py-1.5 text-sm bg-slate-100 dark:bg-navy-800 text-slate-400 rounded-lg cursor-not-allowed"><i class="ti ti-chevron-right text-sm"></i></span>
            @endif
        </div>
    </div>
    @endif

    @else
    <div class="text-center py-16 card">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-navy-800 dark:to-navy-900 flex items-center justify-center shadow-inner">
            <i class="ti ti-clipboard-off text-4xl text-slate-400 dark:text-slate-500"></i>
        </div>
        <p class="mt-4 font-semibold text-slate-600 dark:text-slate-400">No drill results found.</p>
        <a href="{{ route('drill.take') }}" class="btn btn-primary mt-4"><i class="ti ti-player-play"></i> Take a Drill</a>
    </div>
    @endif

</div>
@endsection