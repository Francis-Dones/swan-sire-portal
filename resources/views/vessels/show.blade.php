@extends('layouts.app')
@section('title', 'Vessel: ' . ($vesselName ?? 'Details'))
@section('page-title', $vesselName ?? 'Vessel Details')
@section('page-subtitle', 'Inspection History & Vessel Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('vessels.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-navy-800 dark:hover:bg-navy-800 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
            <i class="ti ti-arrow-left text-base"></i>
            <span>Back to Vessels</span>
        </a>
    </div>

    <!-- VESSEL SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <!-- Vessel Info Card -->
        <div class="md:col-span-2 bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-900/40 dark:to-navy-800/40 flex items-center justify-center shadow-inner">
                    <i class="ti ti-ship text-navy-600 dark:text-navy-400 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $vesselName ?? 'Unknown Vessel' }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300">
                            <i class="ti ti-id-badge text-xs"></i>
                            ID: {{ $vessel['vessel_id'] ?? 'N/A' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                        <i class="ti ti-clipboard-list text-xs"></i>
                        {{ count($exams ?? []) }} inspection record(s)
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <i class="ti ti-calendar text-sm"></i>
                            <span>Registered: {{ $vessel['registered_date'] ?? 'N/A' }}</span>
                        </div>
                        @if(isset($vessel['last_inspection']) && $vessel['last_inspection'])
                        <div class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400">
                            <i class="ti ti-calendar-check text-sm"></i>
                            <span>Last Inspection: {{ date('M d, Y', strtotime($vessel['last_inspection'])) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Persons In Charge Card -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100 dark:border-navy-800">
                <div class="p-1.5 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg">
                    <i class="ti ti-users text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Persons In Charge</h3>
                <span class="text-xs text-gray-400 ml-auto">{{ count($vessel['persons'] ?? []) }} person(s)</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @php $persons = $vessel['persons'] ?? []; @endphp
                @forelse($persons as $person)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-gray-100 dark:bg-navy-800 text-gray-700 dark:text-gray-300">
                    <i class="ti ti-user-circle text-sm"></i>
                    {{ $person }}
                </span>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">— No person assigned —</p>
                @endforelse
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100 dark:border-navy-800">
                <div class="p-1.5 bg-gradient-to-br from-navy-500 to-cyan-600 rounded-lg">
                    <i class="ti ti-chart-bar text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Statistics</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Exams</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($exams ?? []) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Answers</span>
                    <span class="text-2xl font-bold text-navy-600 dark:text-navy-400">
                        {{ collect($exams ?? [])->sum(fn($e) => is_array($e['answers'] ?? null) ? count($e['answers']) : 0) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unique Inspectors</span>
                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">
                        {{ collect($exams ?? [])->pluck('submitted_by')->unique()->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- INSPECTION HISTORY TABLE -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden transition-all duration-300">
        <div class="flex items-center justify-between p-5 pb-3 border-b border-gray-100 dark:border-navy-800">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-cyan-500 to-navy-600 rounded-lg">
                    <i class="ti ti-history text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Inspection History</h3>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($exams ?? []) }} records</span>
            </div>
        </div>
        
        <div class="overflow-x-auto overflow-y-auto" style="max-height: 500px;">
            <table class="w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-navy-800/80 dark:to-navy-900/80 border-b-2 border-gray-200 dark:border-navy-800 backdrop-blur-sm">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Exam ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Person In Charge</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Submitted Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Answers</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-800/50">
                    @forelse($exams ?? [] as $exam)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-navy-800/30 transition-all duration-200 group cursor-pointer" 
                        onclick="window.location='{{ route('exams.show', $exam['id'] ?? 0) }}'">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 border border-navy-100 dark:border-navy-800">
                                <i class="ti ti-id text-xs"></i>
                                {{ $exam['exam_id'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-user-circle text-gray-400 dark:text-gray-500 text-sm"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $exam['person_in_charge'] ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <i class="ti ti-user-check text-green-600 dark:text-green-400 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $exam['submitted_by'] ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-calendar text-gray-400 dark:text-gray-500 text-sm"></i>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ isset($exam['submitted_date']) ? date('M d, Y', strtotime($exam['submitted_date'])) : '—' }}
                                    </span>
                                    @if(isset($exam['submitted_date']))
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">
                                        {{ date('H:i', strtotime($exam['submitted_date'])) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php $ansCount = is_array($exam['answers'] ?? null) ? count($exam['answers']) : 0; @endphp
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $ansCount > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-navy-800 text-gray-500 dark:text-gray-400' }}">
                                <i class="ti ti-checklist text-xs"></i>
                                <span>{{ $ansCount }}</span>
                                <span class="text-[10px] opacity-75">items</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('exams.show', $exam['id'] ?? 0) }}" 
                               class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-navy-500 to-cyan-500 hover:from-navy-600 hover:to-cyan-600 text-white text-xs font-medium rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                               onclick="event.stopPropagation()">
                                <i class="ti ti-eye text-sm"></i>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-navy-800 dark:to-navy-900 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-clipboard-off text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No inspection records</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">This vessel has no exam records yet</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection