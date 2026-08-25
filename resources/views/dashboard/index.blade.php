@extends('layouts.app')

@section('title', 'Dashboard – Ship Inspection Portal')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview & Statistics')

@section('content')

@if(isset($apiError))
    <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-2xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                <i class="ti ti-alert-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <p class="text-red-700 dark:text-red-300">{{ $apiError }}</p>
        </div>
    </div>
@endif

<!-- STAT CARDS -->
@php
    $statCards = [
        ['label' => 'Total Inspections', 'value' => number_format($totalExams), 'icon' => 'ti-clipboard-list', 'grad' => 'from-navy-500 to-navy-700', 'tint' => 'bg-navy-50 dark:bg-navy-500/10 text-navy-600 dark:text-navy-300', 'sub' => 'VIQ records on file'],
        ['label' => 'Fleet Vessels', 'value' => number_format($totalVessels), 'icon' => 'ti-ship', 'grad' => 'from-cyan-500 to-cyan-700', 'tint' => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-300', 'sub' => 'Distinct vessels'],
        ['label' => 'This Month', 'value' => number_format($examsThisMonth ?? 0), 'icon' => 'ti-calendar-stats', 'grad' => 'from-navy-600 to-cyan-600', 'tint' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-300', 'sub' => 'Inspections submitted', 'trend' => $monthTrend ?? 0],
        ['label' => 'Avg. Completion', 'value' => ($avgCompletion ?? 0) . '%', 'icon' => 'ti-rosette-discount-check', 'grad' => 'from-emerald-500 to-teal-600', 'tint' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300', 'sub' => ($uniqueInspectors ?? 0) . ' inspector(s)'],
    ];
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-8">
    @foreach($statCards as $c)
    <div class="group relative bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-navy-800 shadow-sm hover:shadow-xl hover:shadow-navy-500/10 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
        <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-gradient-to-br {{ $c['grad'] }} opacity-[0.07] group-hover:opacity-[0.12] transition-opacity"></div>
        <div class="p-5 relative z-10">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl {{ $c['tint'] }} flex items-center justify-center">
                    <i class="ti {{ $c['icon'] }} text-xl"></i>
                </div>
                @isset($c['trend'])
                    @php $up = $c['trend'] >= 0; @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[11px] font-bold {{ $up ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' }}">
                        <i class="ti {{ $up ? 'ti-trending-up' : 'ti-trending-down' }} text-xs"></i>{{ abs($c['trend']) }}%
                    </span>
                @endisset
            </div>
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white tnum tracking-tight">{{ $c['value'] }}</p>
            <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 mt-1.5">{{ $c['label'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $c['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- CHARTS SECTION - Monthly Inspections Trend & Vessel Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Bar Chart Card -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-800 bg-gradient-to-r from-slate-50 to-white dark:from-navy-800/40 dark:to-navy-900">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-gradient-to-br from-navy-500 to-navy-700 rounded-lg">
                            <i class="ti ti-chart-bar text-white text-sm"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Monthly Inspections Trend</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Last 12 months performance</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        <div class="w-2 h-2 rounded-full bg-navy-500"></div>
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
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-800 bg-gradient-to-r from-slate-50 to-white dark:from-navy-800/40 dark:to-navy-900">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-gradient-to-br from-cyan-500 to-cyan-700 rounded-lg">
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
<div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-800 bg-gradient-to-r from-slate-50 to-white dark:from-navy-800/40 dark:to-navy-900">
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
                <a href="{{ route('exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-navy-600 to-cyan-600 hover:from-navy-500 hover:to-cyan-500 text-white text-sm font-medium rounded-xl transition-all hover:scale-105 shadow-md">
                    <span>View All Records</span>
                    <i class="ti ti-arrow-right text-sm"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] lg:min-w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-navy-800/60 dark:to-navy-900 border-b-2 border-gray-200 dark:border-navy-800">
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Exam ID</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Vessel</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">PIC</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">Inspector</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-navy-800/50">
                @forelse($recentExams ?? [] as $exam)
                <tr class="hover:bg-gradient-to-r hover:bg-navy-50/60 dark:hover:bg-navy-800/30 transition-all duration-200 group cursor-pointer" onclick="window.location='{{ route('exams.show', $exam['id'] ?? 0) }}'">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-navy-50 dark:bg-navy-500/15 text-navy-700 dark:text-navy-200 border border-navy-100 dark:border-navy-800">
                            <i class="ti ti-id-badge text-xs"></i>
                            {{ $exam['exam_id'] ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-800 dark:to-navy-900 flex items-center justify-center">
                                <i class="ti ti-ship text-navy-600 dark:text-navy-400 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $exam['vessel_name'] ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm hidden sm:table-cell">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-user-circle text-gray-400 text-sm"></i>
                            {{ $exam['person_in_charge'] ?? '—' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm hidden lg:table-cell">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-mail text-gray-400 text-sm"></i>
                            {{ $exam['submitted_by'] ?? '—' }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <i class="ti ti-calendar text-gray-400 text-sm"></i>
                            <span class="text-gray-600 dark:text-gray-400 text-sm whitespace-nowrap">
                                {{ isset($exam['submitted_date']) ? date('M d, Y', strtotime($exam['submitted_date'])) : '—' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('exams.show', $exam['id'] ?? 0) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-navy-600 to-cyan-600 hover:from-navy-500 hover:to-cyan-500 text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105"
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
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-navy-800 dark:to-navy-900 flex items-center justify-center">
                                <i class="ti ti-database-off text-3xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No Inspections Yet</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Start by creating your first inspection</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
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
    const monthlyData = @json($monthlyData ?? []);
    const barCtx = document.getElementById('monthlyChart');
    
    if (barCtx && monthlyData && monthlyData.length > 0) {
        new Chart(barCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [{
                    label: 'Inspections',
                    data: monthlyData.map(d => d.count),
                    backgroundColor: 'rgba(30, 78, 140, 0.85)',
                    borderColor: '#1E4E8C',
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
                        borderColor: '#1E4E8C',
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
    const typeData = @json($inspectionTypes ?? []);
    const pieCtx = document.getElementById('typeChart');
    
    if (pieCtx && Object.keys(typeData).length > 0) {
        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: ['#1E4E8C', '#06B6D4', '#24589C', '#22D3EE', '#0891B2', '#4E7FBC', '#8FB2DE'],
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

@endsection