@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'System Users')
@section('page-subtitle', 'Registered portal accounts')

@section('content')
<div class="space-y-6">
    <!-- SUCCESS/ERROR MESSAGES -->
    @if(session('success'))
    <div class="p-4 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded-lg shadow-sm">
        <div class="flex items-center gap-2">
            <i class="ti ti-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 rounded-lg shadow-sm">
        <div class="flex items-center gap-2">
            <i class="ti ti-alert-circle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- FILTERS SECTION -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none p-5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg">
                    <i class="ti ti-users text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Filter Users</h3>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">{{ $users->total() ?? count($users ?? []) }} total</span>
        </div>
        
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 flex items-center gap-1 mb-1.5">
                    <i class="ti ti-search text-xs"></i> Search User
                </label>
                <div class="relative group">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-500 transition-colors text-sm"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all dark:text-white"
                           placeholder="Search username, email, vessel name...">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    <i class="ti ti-search text-base"></i>
                    <span>Search</span>
                </button>
                <a href="{{ route('users.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
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
                <span class="font-bold text-gray-900 dark:text-white">{{ $users->total() ?? count($users ?? []) }}</span> user(s) found
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <!-- CREATE USER BUTTON -->
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-user-plus text-base"></i>
                <span>Create User</span>
            </a>
            
            <a href="{{ route('users.export.excel') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-spreadsheet text-base"></i>
                <span class="hidden sm:inline">Excel</span>
            </a>
            
            <a href="{{ route('users.export.pdf') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                <i class="ti ti-file-type-pdf text-base"></i>
                <span class="hidden sm:inline">PDF</span>
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto overflow-y-auto" style="max-height: 600px;">
            <table class="w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/80 dark:to-gray-800/80 border-b-2 border-gray-200 dark:border-gray-700 backdrop-blur-sm">
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Username</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Address</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Age</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Vessel Name</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Token Type</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Created Date</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($users ?? [] as $index => $user)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-all duration-200 group">
                        <td class="px-4 py-4 text-sm text-gray-400 dark:text-gray-500 font-mono font-medium">
                            {{ isset($users->currentPage) ? ($users->currentPage() - 1) * $users->perPage() + $index + 1 : $index + 1 }}
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/40 dark:to-purple-800/40 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-user-circle text-purple-600 dark:text-purple-400 text-base"></i>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $user->username ?? $user['username'] ?? 'N/A' }}</span>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">
                                        ID: {{ $user->id ?? $user['id'] ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-mail text-gray-400 dark:text-gray-500 text-sm"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email ?? $user['email'] ?? '—' }}</span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-map-pin text-gray-400 dark:text-gray-500 text-sm"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $user->address ?? $user['address'] ?? '—' }}</span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            @if(($user->age ?? $user['age'] ?? null))
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                <i class="ti ti-cake text-xs"></i>
                                <span>{{ $user->age ?? $user['age'] }}</span>
                                <span class="text-[10px] opacity-75">yrs</span>
                            </div>
                            @else
                            <span class="text-gray-400 dark:text-gray-500 text-sm">—</span>
                            @endif
                        </td>
                        
                        <!-- VESSEL NAME COLUMN -->
                        <td class="px-4 py-4">
                            @if(($user->vessel_name ?? $user['vessel_name'] ?? null))
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300">
                                <i class="ti ti-ship text-xs"></i>
                                <span>{{ $user->vessel_name ?? $user['vessel_name'] }}</span>
                            </div>
                            @else
                            <span class="text-gray-400 dark:text-gray-500 text-sm">—</span>
                            @endif
                        </td>
                        
                        <td class="px-4 py-4">
                            @if($user->token_type ?? $user['token_type'] ?? null)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                                <i class="ti ti-key text-xs"></i>
                                {{ $user->token_type ?? $user['token_type'] }}
                            </span>
                            @else
                            <span class="text-gray-400 dark:text-gray-500 text-sm">—</span>
                            @endif
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-calendar text-gray-400 dark:text-gray-500 text-sm"></i>
                                <div>
                                    @php
                                        $createdAt = $user->created_at ?? $user['created_at'] ?? null;
                                    @endphp
                                    <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ $createdAt ? date('M d, Y', strtotime($createdAt)) : '—' }}
                                    </span>
                                    @if($createdAt)
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">
                                        {{ date('H:i', strtotime($createdAt)) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- ACTIONS COLUMN -->
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <!-- Edit Button -->
                                <a href="{{ route('users.edit', $user->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-lg transition-all hover:scale-105">
                                    <i class="ti ti-edit text-xs"></i>
                                    Edit
                                </a>
                                
                                <!-- Change Password Button -->
                                <a href="{{ route('users.edit-password', $user->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-lg transition-all hover:scale-105">
                                    <i class="ti ti-key text-xs"></i>
                                    Password
                                </a>
                                
                                <!-- Delete Button (Hide for current logged-in user) -->
                                @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete user: {{ $user->username }}? This action cannot be undone!')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-xs font-medium rounded-lg transition-all hover:scale-105">
                                        <i class="ti ti-trash text-xs"></i>
                                        Delete
                                    </button>
                                </form>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                    <i class="ti ti-lock text-xs"></i>
                                    Current
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-20">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center shadow-inner">
                                    <i class="ti ti-users-off text-4xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-600 dark:text-gray-400 text-lg">No users found</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your search or add new users</p>
                                </div>
                                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm rounded-xl transition-all">
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
        
        @if(isset($users) && method_exists($users, 'hasPages') && $users->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: rgb(243 244 246);
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgb(156 163 175);
        border-radius: 10px;
    }
    .dark .overflow-x-auto::-webkit-scrollbar-track {
        background: rgb(31 41 55);
    }
    .dark .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgb(75 85 99);
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: rgb(107 114 128);
    }
    * {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    .bg-purple-100 { background-color: #f3e8ff; }
    .bg-purple-200 { background-color: #e9d5ff; }
    .bg-purple-500 { background-color: #a855f7; }
    .bg-purple-600 { background-color: #9333ea; }
    .text-purple-600 { color: #9333ea; }
    .text-purple-400 { color: #c084fc; }
    .dark .bg-purple-900\/40 { background-color: rgba(88, 28, 135, 0.4); }
    .dark .bg-purple-800\/40 { background-color: rgba(107, 33, 168, 0.4); }
    .dark .text-purple-400 { color: #c084fc; }
    .from-purple-500 { --tw-gradient-from: #a855f7; }
    .from-purple-600 { --tw-gradient-from: #9333ea; }
    .to-pink-600 { --tw-gradient-to: #db2777; }
    .from-emerald-600 { --tw-gradient-from: #059669; }
    .to-green-600 { --tw-gradient-to: #16a34a; }
    .from-rose-600 { --tw-gradient-from: #e11d48; }
    .to-red-600 { --tw-gradient-to: #dc2626; }
    .bg-blue-100 { background-color: #dbeafe; }
    .bg-blue-200 { background-color: #bfdbfe; }
    .text-blue-700 { color: #1d4ed8; }
    .dark .bg-blue-900\/30 { background-color: rgba(30, 58, 138, 0.3); }
    .dark .bg-blue-900\/50 { background-color: rgba(30, 58, 138, 0.5); }
    .dark .text-blue-300 { color: #93c5fd; }
    .bg-amber-100 { background-color: #fef3c7; }
    .bg-amber-200 { background-color: #fde68a; }
    .text-amber-700 { color: #b45309; }
    .dark .bg-amber-900\/30 { background-color: rgba(146, 64, 14, 0.3); }
    .dark .bg-amber-900\/50 { background-color: rgba(146, 64, 14, 0.5); }
    .dark .text-amber-300 { color: #fcd34d; }
    .bg-red-100 { background-color: #fee2e2; }
    .bg-red-200 { background-color: #fecaca; }
    .text-red-700 { color: #dc2626; }
    .dark .bg-red-900\/30 { background-color: rgba(127, 29, 29, 0.3); }
    .dark .bg-red-900\/50 { background-color: rgba(127, 29, 29, 0.5); }
    .dark .text-red-300 { color: #fca5a5; }
    .bg-green-100 { background-color: #dcfce7; }
    .text-green-700 { color: #16a34a; }
    .dark .bg-green-900\/30 { background-color: rgba(22, 101, 52, 0.3); }
    .dark .text-green-400 { color: #4ade80; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .dark .bg-gray-700 { background-color: #374151; }
    .dark .text-gray-400 { color: #9ca3af; }
    .bg-cyan-100 { background-color: #cffafe; }
    .text-cyan-700 { color: #0e7490; }
    .dark .bg-cyan-900\/30 { background-color: rgba(22, 78, 99, 0.3); }
    .dark .text-cyan-300 { color: #67e8f9; }
</style>
@endpush
@endsection