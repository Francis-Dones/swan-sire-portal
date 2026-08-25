<!DOCTYPE html>
<html lang="en" x-data="appLayout()" :class="{ 'dark': darkMode }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SWAN · Maritime Inspection Portal')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        /* ── Brand navy (Modern Maritime SaaS) ── */
                        navy: {
                            50:  '#EFF4FB',
                            100: '#D6E3F3',
                            200: '#B9D0EC',
                            300: '#8FB2DE',
                            400: '#4E7FBC',
                            500: '#24589C',
                            600: '#1E4E8C',
                            700: '#183E70',
                            800: '#12305B',
                            900: '#0B1F3A',
                            950: '#071627',
                        },
                        /* ── Accent cyan ── */
                        cyan: {
                            50:  '#ECFEFF',
                            100: '#CFFAFE',
                            200: '#A5F3FC',
                            300: '#67E8F9',
                            400: '#22D3EE',
                            500: '#06B6D4',
                            600: '#0891B2',
                            700: '#0E7490',
                        }
                    },
                    boxShadow: {
                        'glow-navy': '0 8px 30px -8px rgba(30,78,140,0.45)',
                        'glow-cyan': '0 8px 30px -8px rgba(6,182,212,0.45)',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .tnum { font-variant-numeric: tabular-nums; }
        [x-cloak] { display: none !important; }

        /* ── Scrollbars ── */
        .nice-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .nice-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .nice-scroll::-webkit-scrollbar-track { background: transparent; }
        .nice-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .dark .nice-scroll { scrollbar-color: #1E4E8C transparent; }
        .dark .nice-scroll::-webkit-scrollbar-thumb { background: #12305B; }
        .sidebar-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #183E70; }

        /* ── Nav link ── */
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 11px;
            font-size: 13.5px; font-weight: 500; color: #475569;
            transition: all .15s ease; position: relative;
            white-space: nowrap; text-decoration: none; margin: 2px 0;
        }
        .dark .nav-link { color: #94a3b8; }
        .nav-link:hover { background: #f1f5f9; color: #0f172a; }
        .dark .nav-link:hover { background: rgba(255,255,255,.05); color: #f1f5f9; }
        .nav-link.active {
            background: linear-gradient(135deg, #EFF4FB, rgba(6,182,212,.10));
            color: #1E4E8C; font-weight: 600;
        }
        .dark .nav-link.active {
            background: linear-gradient(135deg, rgba(30,78,140,.28), rgba(6,182,212,.12));
            color: #8FB2DE;
        }
        .nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 18%; bottom: 18%;
            width: 3px; background: linear-gradient(#1E4E8C,#06B6D4); border-radius: 0 3px 3px 0;
        }
        .dark .nav-link.active::before { background: linear-gradient(#4E7FBC,#22D3EE); }

        /* ── Nav icon box ── */
        .nav-icon {
            flex-shrink: 0; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9px; background: rgba(30,78,140,.08);
            font-size: 16px; color: #1E4E8C; transition: all .15s;
        }
        .dark .nav-icon { background: rgba(78,127,188,.12); color: #8FB2DE; }
        .nav-link.active .nav-icon { background: linear-gradient(135deg,#1E4E8C,#06B6D4); color: #fff; }

        /* ── Section label ── */
        .nav-section {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #94a3b8; padding: 16px 12px 6px;
        }
        .nav-hr { height: 1px; background: #e2e8f0; margin: 8px 4px; }
        .dark .nav-hr { background: #12305B; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 9px 16px; border-radius: 11px; font-size: 13.5px; font-weight: 600; transition: all .15s ease; cursor: pointer; border: none; text-decoration: none; min-height: 40px; }
        .btn-primary { background: linear-gradient(135deg,#1E4E8C,#0891B2); color: #fff; box-shadow: 0 2px 8px rgba(30,78,140,.30); }
        .btn-primary:hover { background: linear-gradient(135deg,#24589C,#06B6D4); box-shadow: 0 6px 18px rgba(6,182,212,.35); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0) scale(.98); }
        .btn-outline { background: #fff; color: #334155; border: 1px solid #e2e8f0; }
        .dark .btn-outline { background: #0B1F3A; color: #cbd5e1; border-color: #12305B; }
        .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
        .dark .btn-outline:hover { background: #12305B; }
        .btn-excel { background: #15803d; color: #fff; }
        .btn-excel:hover { background: #166534; }
        .btn-pdf { background: #b91c1c; color: #fff; }
        .btn-pdf:hover { background: #991b1b; }
        .btn-danger { background: #f43f5e; color: #fff; }
        .btn-danger:hover { background: #e11d48; }

        /* ── Pills ── */
        .pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; line-height: 1.4; }
        .pill-approved { background: #d1fae5; color: #047857; }
        .dark .pill-approved { background: rgba(16,185,129,.16); color: #6ee7b7; }
        .pill-pending  { background: #fef3c7; color: #b45309; }
        .dark .pill-pending  { background: rgba(245,158,11,.16); color: #fcd34d; }
        .pill-rejected { background: #ffe4e6; color: #be123c; }
        .dark .pill-rejected { background: rgba(244,63,94,.16); color: #fda4af; }
        .pill-info     { background: #e0f2fe; color: #0369a1; }
        .dark .pill-info     { background: rgba(14,165,233,.16); color: #7dd3fc; }

        /* ── Card ── */
        .card { background: #fff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15,23,42,.05); }
        .dark .card { background: #0B1F3A; border-color: #12305B; box-shadow: none; }

        /* ── Form ── */
        .form-input { width: 100%; padding: 10px 14px; border-radius: 11px; border: 1px solid #e2e8f0; background: #fff; color: #0f172a; font-size: 13.5px; transition: border-color .15s, box-shadow .15s; outline: none; min-height: 42px; }
        .dark .form-input { background: #071627; border-color: #12305B; color: #f1f5f9; }
        .form-input:focus { border-color: #24589C; box-shadow: 0 0 0 3px rgba(36,88,156,.14); }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #334155; margin-bottom: 6px; }
        .dark .form-label { color: #cbd5e1; }

        /* ── Mobile overlay ── */
        .overlay { position: fixed; inset: 0; background: rgba(7,22,39,.55); backdrop-filter: blur(3px); z-index: 40; }
        @media (min-width: 1024px) { .overlay { display: none !important; } }

        @keyframes fadeSlideDown { from { opacity:0; transform: translateY(-6px); } to { opacity:1; transform: translateY(0); } }
        .anim-fade { animation: fadeSlideDown .25s ease; }
    </style>
    @stack('styles')
</head>

<body class="h-full bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-slate-100 transition-colors duration-200"
      x-init="init()">

<div class="flex h-full">

    <!-- ░░ MOBILE OVERLAY ░░ -->
    <div class="overlay"
         x-show="sidebarOpen && !isDesktop"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         style="display:none;">
    </div>

    <!-- ░░░░ SIDEBAR ░░░░ -->
    <!-- On lg+ the rail is ALWAYS visible (lg:translate-x-0 overrides the mobile toggle);
         below lg it is an off-canvas drawer controlled by sidebarOpen. -->
    <aside class="fixed lg:sticky top-0 left-0 h-screen z-50 lg:z-auto
                  w-64 flex-shrink-0 flex flex-col
                  bg-white dark:bg-navy-900
                  border-r border-slate-200 dark:border-navy-800
                  transition-transform duration-300 ease-out
                  lg:translate-x-0
                  sidebar-scroll overflow-y-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Brand -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-slate-200 dark:border-navy-800 flex-shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-600 to-cyan-600 flex items-center justify-center flex-shrink-0 shadow-glow-navy">
                    <i class="ti ti-anchor text-white" style="font-size:19px;"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-extrabold text-slate-900 dark:text-white text-sm leading-tight truncate tracking-tight">SWAN Portal</p>
                    <p class="mono text-slate-400 leading-tight" style="font-size:10px;">Maritime Inspection</p>
                </div>
            </div>
            <button @click="sidebarOpen = false"
                    class="lg:hidden flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors ml-1">
                <i class="ti ti-x" style="font-size:16px;"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-2">
            <p class="nav-section">Main</p>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-layout-dashboard"></i></span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('images.index') }}"
               class="nav-link {{ request()->routeIs('images*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-photo"></i></span>
                <span>Vessel Photos</span>
            </a>

            <a href="{{ route('exams.index') }}"
               class="nav-link {{ request()->routeIs('exams*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-clipboard-list"></i></span>
                <span>VIQ Records</span>
            </a>

            <a href="{{ route('vessels.index') }}"
               class="nav-link {{ request()->routeIs('vessels*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-ship"></i></span>
                <span>Vessels</span>
            </a>

            <div x-data="{ open: {{ request()->routeIs('drill*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                        class="nav-link w-full {{ request()->routeIs('drill*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="ti ti-clipboard-check"></i></span>
                    <span>SIRE Drill</span>
                    <i class="ti ti-chevron-down text-slate-400 text-sm ml-auto transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak class="ml-4 pl-3 border-l border-slate-200 dark:border-navy-800 mt-1 space-y-0.5">
                    <a href="{{ route('drill.take') }}"
                       class="nav-link {{ request()->routeIs('drill.take') ? 'active' : '' }}"
                       @click="if(!isDesktop) sidebarOpen = false">
                        <span class="nav-icon"><i class="ti ti-pencil-question"></i></span>
                        <span>Take Drill</span>
                    </a>
                    <a href="{{ route('drill.results') }}"
                       class="nav-link {{ request()->routeIs('drill.results') || request()->routeIs('drill.show') ? 'active' : '' }}"
                       @click="if(!isDesktop) sidebarOpen = false">
                        <span class="nav-icon"><i class="ti ti-checklist"></i></span>
                        <span>Results</span>
                    </a>
                </div>
            </div>

            <div class="nav-hr"></div>
            <p class="nav-section">Management</p>

            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-users"></i></span>
                <span>Users</span>
            </a>
        </nav>

        <!-- Sidebar footer (user + logout now live in the header menu) -->
        <div class="flex-shrink-0 border-t border-slate-200 dark:border-navy-800 px-4 py-3.5 flex items-center justify-between">
            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">SWAN Maritime</span>
            <span class="mono text-[10px] text-slate-400 dark:text-slate-500">v2.0</span>
        </div>
    </aside>

    <!-- ░░░░ MAIN AREA ░░░░ -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top Bar -->
        <header class="flex-shrink-0 bg-white/90 dark:bg-navy-900/90 backdrop-blur-md border-b border-slate-200 dark:border-navy-800 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-navy-800 transition-all flex-shrink-0">
                    <i class="ti ti-menu-2 text-xl"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="font-bold text-slate-900 dark:text-white text-base md:text-lg leading-tight truncate">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-tight truncate hidden sm:block">
                        @yield('page-subtitle', 'Ship Inspection Portal')
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                @hasSection('topbar-actions')
                    @yield('topbar-actions')
                @endif

                <!-- Theme toggle -->
                <button @click="toggleDark()"
                        class="relative w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none flex-shrink-0"
                        :class="darkMode ? 'bg-navy-600' : 'bg-slate-300'"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 flex items-center justify-center"
                          :class="darkMode ? 'translate-x-6' : 'translate-x-0'">
                        <i :class="darkMode ? 'ti ti-moon text-navy-600' : 'ti ti-sun text-amber-500'"
                           style="font-size:11px;"></i>
                    </span>
                </button>

                <div class="w-px h-6 bg-slate-200 dark:bg-navy-800 hidden sm:block"></div>

                <!-- User menu -->
                @php
                    $displayName  = session('user_name', auth()->user()->username ?? 'User');
                    $displayEmail = session('user_email', auth()->user()->email ?? 'Logged in');
                    $initial = strtoupper(substr($displayName, 0, 1));
                @endphp
                <div class="relative" x-data="{ userMenu: false }" @keydown.escape.window="userMenu=false">
                    <button @click="userMenu = !userMenu"
                            class="flex items-center gap-2 pl-1.5 pr-1 sm:pr-2.5 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors"
                            :class="userMenu ? 'bg-slate-100 dark:bg-navy-800' : ''">
                        <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-navy-500 to-cyan-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ $initial }}</span>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700 dark:text-slate-200 max-w-[9rem] truncate leading-tight">{{ $displayName }}</span>
                        <i class="ti ti-chevron-down text-slate-400 text-sm transition-transform hidden sm:block" :class="userMenu ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="userMenu" x-cloak @click.outside="userMenu=false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                         class="absolute right-0 mt-2 w-64 rounded-xl bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-800 shadow-xl shadow-navy-900/10 overflow-hidden z-50">
                        <div class="flex items-center gap-3 p-3.5 border-b border-slate-100 dark:border-navy-800">
                            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-500 to-cyan-600 flex items-center justify-center text-white font-bold flex-shrink-0">{{ $initial }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate leading-tight">{{ $displayName }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 truncate leading-tight mt-0.5">{{ $displayEmail }}</p>
                            </div>
                        </div>
                        <div class="p-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <i class="ti ti-logout text-lg"></i> Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash toasts -->
        <div class="fixed top-16 right-4 sm:right-6 z-40 w-[calc(100%-2rem)] sm:w-96 space-y-2 pointer-events-none">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl bg-white dark:bg-navy-900 border border-emerald-200 dark:border-emerald-800/60 shadow-lg shadow-emerald-500/10">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </span>
                <p class="flex-1 text-sm text-slate-700 dark:text-slate-200 pt-1.5">{{ session('success') }}</p>
                <button @click="show=false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 flex-shrink-0 pt-1"><i class="ti ti-x"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl bg-white dark:bg-navy-900 border border-rose-200 dark:border-rose-800/60 shadow-lg shadow-rose-500/10">
                <span class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-circle text-rose-600 dark:text-rose-400 text-lg"></i>
                </span>
                <p class="flex-1 text-sm text-slate-700 dark:text-slate-200 pt-1.5">{{ session('error') }}</p>
                <button @click="show=false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 flex-shrink-0 pt-1"><i class="ti ti-x"></i></button>
            </div>
            @endif

            @if(session('warning'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl bg-white dark:bg-navy-900 border border-amber-200 dark:border-amber-800/60 shadow-lg shadow-amber-500/10">
                <span class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-triangle text-amber-600 dark:text-amber-400 text-lg"></i>
                </span>
                <p class="flex-1 text-sm text-slate-700 dark:text-slate-200 pt-1.5">{{ session('warning') }}</p>
                <button @click="show=false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 flex-shrink-0 pt-1"><i class="ti ti-x"></i></button>
            </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto nice-scroll p-4 md:p-6">
            @yield('content')
        </main>

    </div>
</div>

<script>
function appLayout() {
    return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        // Desktop rail is CSS-forced visible at lg (>=1024); this flag only governs
        // the mobile/tablet off-canvas drawer + its backdrop.
        sidebarOpen: window.innerWidth >= 1024,
        isDesktop: window.innerWidth >= 1024,
        init() {
            this.darkMode = localStorage.getItem('darkMode') === 'true';
            this.isDesktop = window.innerWidth >= 1024;
            this.sidebarOpen = this.isDesktop;
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 1024;
                if (this.isDesktop) this.sidebarOpen = true;
            });
        },
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        }
    }
}
</script>

@stack('scripts')
</body>
</html>
