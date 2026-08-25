@extends('layouts.app')
@section('title', 'Vessels')
@section('page-title', 'Vessels')
@section('page-subtitle', 'All registered vessels')

@section('content')
<div class="space-y-6">
    <!-- FILTERS SECTION -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-navy-800">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-navy-500 to-navy-600 rounded-lg">
                    <i class="ti ti-ship text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Filter Vessels</h3>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-navy-800 px-2 py-1 rounded-full">{{ count($vessels) }} total</span>
        </div>
        
        <form method="GET" action="{{ route('vessels.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1 mb-1.5">
                    <i class="ti ti-search text-xs"></i> Search Vessel
                </label>
                <div class="relative group">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-navy-500 transition-colors text-sm"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 dark:bg-navy-950/50 border border-gray-200 dark:border-navy-800 rounded-xl focus:ring-2 focus:ring-navy-500/20 focus:border-navy-500 dark:focus:border-navy-500 transition-all dark:text-white"
                           placeholder="Search vessel name...">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-navy-600 to-navy-600 hover:from-navy-700 hover:to-navy-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-search text-base"></i>
                    <span>Search</span>
                </button>
                <a href="{{ route('vessels.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-navy-800 dark:hover:bg-navy-800 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
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
                <span class="font-bold text-gray-900 dark:text-white">{{ count($vessels) }}</span> vessel(s) found
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('vessels.export.excel') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-spreadsheet text-base"></i>
                <span class="hidden sm:inline">Excel</span>
            </a>
            
            <a href="{{ route('vessels.export.pdf') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-type-pdf text-base"></i>
                <span class="hidden sm:inline">PDF</span>
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-gray-200 dark:border-navy-800 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto overflow-y-auto" style="max-height: 600px;">
            <table class="w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-navy-800/80 dark:to-navy-900/80 border-b-2 border-gray-200 dark:border-navy-800 backdrop-blur-sm">
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Vessel Name</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total Exams</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Person(s) In Charge</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Last Inspection</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-800/50">
                    @forelse($vessels as $i => $vessel)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-navy-800/30 transition-all duration-200 group cursor-pointer" 
                        onclick="window.location='{{ route('vessels.show', ['vesselName' => urlencode($vessel['vessel_name'])]) }}'">
                        <td class="px-4 py-4 text-sm text-gray-400 dark:text-gray-500 font-mono font-medium">{{ $i + 1 }}</td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-900/40 dark:to-navy-800/40 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-ship text-navy-600 dark:text-navy-400 text-base"></i>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $vessel['vessel_name'] }}</span>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">
                                        ID: {{ $vessel['vessel_id'] ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300">
                                <i class="ti ti-clipboard-list text-xs"></i>
                                <span>{{ $vessel['total_exams'] }}</span>
                                <span class="text-[10px] opacity-75">exams</span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $persons = $vessel['persons'] ?? [];
                                    $displayPersons = array_slice($persons, 0, 2);
                                    $remaining = count($persons) - 2;
                                @endphp
                                
                                @foreach($displayPersons as $person)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-gray-100 dark:bg-navy-800 text-gray-600 dark:text-gray-300">
                                    <i class="ti ti-user-circle text-[10px]"></i>
                                    {{ $person }}
                                </span>
                                @endforeach
                                
                                @if($remaining > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                    <i class="ti ti-plus text-[10px]"></i>
                                    {{ $remaining }} more
                                </span>
                                @endif
                                
                                @if(empty($persons))
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">— No person assigned —</span>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            @if(isset($vessel['last_inspection']) && $vessel['last_inspection'])
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <i class="ti ti-calendar-check text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">
                                        {{ date('M d, Y', strtotime($vessel['last_inspection'])) }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">
                                        {{ date('H:i', strtotime($vessel['last_inspection'])) }}
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-navy-800 flex items-center justify-center">
                                    <i class="ti ti-calendar-off text-gray-400 dark:text-gray-500 text-sm"></i>
                                </div>
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">No inspection yet</span>
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('vessels.show', ['vesselName' => urlencode($vessel['vessel_name'])]) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-navy-500 to-navy-500 hover:from-navy-600 hover:to-navy-600 text-white text-xs font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                               onclick="event.stopPropagation()">
                                <i class="ti ti-eye text-sm"></i>
                                <span>View Details</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-20">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-navy-800 dark:to-navy-900 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-ship-off text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No vessels found</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your search or check registered vessels</p>
                                </div>
                                <a href="{{ route('vessels.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-navy-500 hover:bg-navy-600 text-white text-sm rounded-xl transition-all">
                                    <i class="ti ti-refresh"></i>
                                    Clear Search
                                </a>
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