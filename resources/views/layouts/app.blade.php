<!DOCTYPE html>
<html lang="en" x-data="appLayout()" :class="{ 'dark': darkMode }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ship Inspection Portal')</title>

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
                        navy: {
                            50:  '#EEF4FC',
                            100: '#CDDFF7',
                            200: '#9DC0EF',
                            300: '#6AAEE8',
                            400: '#3D8ED6',
                            500: '#2B7DD4',
                            600: '#1A63B5',
                            700: '#125094',
                            800: '#0C3D73',
                            900: '#071F3D',
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar scroll ── */
        .sidebar-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; }

        /* ── Nav link ── */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: #4b5563;
            transition: all 0.15s ease;
            position: relative;
            white-space: nowrap;
            text-decoration: none;
            margin: 1px 0;
        }
        .dark .nav-link { color: #9ca3af; }
        .nav-link:hover { background: #f1f5f9; color: #1e293b; }
        .dark .nav-link:hover { background: rgba(255,255,255,0.06); color: #f1f5f9; }

        /* Active state */
        .nav-link.active {
            background: linear-gradient(135deg, #EEF4FC, #dbeafe88);
            color: #1A63B5;
            font-weight: 600;
        }
        .dark .nav-link.active {
            background: rgba(26,99,181,0.18);
            color: #93c5fd;
        }
        /* Active left bar */
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: #1A63B5;
            border-radius: 0 3px 3px 0;
        }
        .dark .nav-link.active::before { background: #60a5fa; }

        /* ── Nav icon box ── */
        .nav-icon {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(26,99,181,0.07);
            font-size: 16px;
            color: #1A63B5;
            transition: background 0.15s;
        }
        .dark .nav-icon { background: rgba(96,165,250,0.1); color: #93c5fd; }
        .nav-link:hover .nav-icon { background: rgba(26,99,181,0.12); }
        .nav-link.active .nav-icon { background: rgba(26,99,181,0.14); }

        /* ── Section label ── */
        .nav-section {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 14px 12px 6px;
        }

        /* ── Divider ── */
        .nav-hr {
            height: 1px;
            background: #e2e8f0;
            margin: 8px 4px;
        }
        .dark .nav-hr { background: #334155; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; transition: all 0.15s; cursor: pointer; border: none; text-decoration: none; min-height: 38px; }
        .btn-primary { background: linear-gradient(135deg,#1A63B5,#125094); color: #fff; box-shadow: 0 1px 4px rgba(26,99,181,0.3); }
        .btn-primary:hover { background: linear-gradient(135deg,#2B7DD4,#1A63B5); box-shadow: 0 3px 10px rgba(26,99,181,0.35); }
        .btn-outline { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .dark .btn-outline { background: #1e293b; color: #d1d5db; border-color: #475569; }
        .btn-outline:hover { background: #f9fafb; }
        .dark .btn-outline:hover { background: #334155; }
        .btn-excel { background: #15803d; color: #fff; }
        .btn-excel:hover { background: #166534; }
        .btn-pdf { background: #b91c1c; color: #fff; }
        .btn-pdf:hover { background: #991b1b; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }

        /* ── Badge ── */
        .badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 500; }

        /* ── Form ── */
        .form-input { width: 100%; padding: 9px 14px; border-radius: 10px; border: 1px solid #d1d5db; background: #fff; color: #111827; font-size: 13.5px; transition: border-color 0.15s, box-shadow 0.15s; outline: none; min-height: 40px; }
        .dark .form-input { background: #1e293b; border-color: #475569; color: #f1f5f9; }
        .form-input:focus { border-color: #1A63B5; box-shadow: 0 0 0 3px rgba(26,99,181,0.12); }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 5px; }
        .dark .form-label { color: #d1d5db; }

        /* ── Table ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 11px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; text-align: left; color: #6b7280; background: #f8fafc; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
        .dark .data-table th { background: rgba(255,255,255,0.03); color: #9ca3af; border-color: #334155; }
        .data-table td { padding: 11px 14px; font-size: 13.5px; color: #374151; border-bottom: 1px solid #f1f5f9; }
        .dark .data-table td { color: #d1d5db; border-color: #1e293b; }
        .data-table tbody tr:hover td { background: #f8fafc; }
        .dark .data-table tbody tr:hover td { background: rgba(255,255,255,0.025); }

        /* ── Stat card ── */
        .stat-card { background: #fff; border-radius: 14px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: transform 0.15s, box-shadow 0.15s; }
        .dark .stat-card { background: #1e293b; border-color: #334155; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }

        /* ── Mobile overlay ── */
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(3px); z-index: 40; }
        @media (min-width: 768px) { .overlay { display: none !important; } }

        /* ── Animations ── */
        @keyframes fadeSlideDown { from { opacity:0; transform: translateY(-6px); } to { opacity:1; transform: translateY(0); } }
        .anim-fade { animation: fadeSlideDown 0.25s ease; }
    </style>
    @yield('styles')
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200"
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

    <!-- ░░░░░░░░░░░░░░░░░░░░░░
         SIDEBAR
    ░░░░░░░░░░░░░░░░░░░░░░░░ -->
    <aside class="fixed md:sticky top-0 left-0 h-screen z-50 md:z-auto
                  w-64 flex-shrink-0 flex flex-col
                  bg-white dark:bg-gray-800
                  border-r border-gray-200 dark:border-gray-700
                  transition-transform duration-300 ease-out
                  sidebar-scroll overflow-y-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- ── Brand ── -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="ti ti-ship text-white" style="font-size:18px;"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-gray-900 dark:text-white text-sm leading-tight truncate">Ship Inspection</p>
                    <p class="mono text-gray-400 leading-tight" style="font-size:10px;">Portal v2.0</p>
                </div>
            </div>
            <button @click="sidebarOpen = false"
                    class="md:hidden flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors ml-1">
                <i class="ti ti-x" style="font-size:16px;"></i>
            </button>
        </div>

        <!-- ── Navigation ── -->
        <nav class="flex-1 px-3 py-3">

            <!-- MAIN -->
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
                <span>BIQ</span>
            </a>

            <a href="{{ route('vessels.index') }}"
               class="nav-link {{ request()->routeIs('vessels*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-ship"></i></span>
                <span>Vessels</span>
            </a>

            <!-- MANAGEMENT -->
            <div class="nav-hr"></div>
            <p class="nav-section">Management</p>

            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}"
               @click="if(!isDesktop) sidebarOpen = false">
                <span class="nav-icon"><i class="ti ti-users"></i></span>
                <span>Users</span>
            </a>

        </nav>

        <!-- ── User + Logout ── -->
        <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 p-3 space-y-1">

            <!-- User info -->
            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                <div class="w-8 h-8 rounded-lg bg-navy-100 dark:bg-navy-900/50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-user text-navy-600 dark:text-navy-300" style="font-size:15px;"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate leading-tight">
                        {{ session('user_name', auth()->user()->username ?? 'User') }}
                    </p>
                    <p class="text-gray-400 truncate leading-tight" style="font-size:11px;">
                        {{ session('user_email', auth()->user()->email ?? 'Logged in') }}
                    </p>
                </div>
                <!-- Online dot -->
                <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="nav-link w-full text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                        style="color:#ef4444;">
                    <span class="nav-icon" style="background:rgba(239,68,68,0.08);">
                        <i class="ti ti-logout" style="color:#ef4444;"></i>
                    </span>
                    <span>Logout</span>
                </button>
            </form>

        </div>
    </aside>

    <!-- ░░░░░░░░░░░░░░░░░░░░░░
         MAIN AREA
    ░░░░░░░░░░░░░░░░░░░░░░░░ -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- ── Top Bar ── -->
        <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-30">

            <!-- Left: hamburger + page title -->
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all flex-shrink-0">
                    <i class="ti ti-menu-2 text-xl"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="font-bold text-gray-900 dark:text-white text-base md:text-lg leading-tight truncate">
                        @yield('page-title', 'Dashboard')
                    </h1>
                   
                </div>
            </div>

            <!-- Right: API status + dark mode -->
            <div class="flex items-center gap-2 flex-shrink-0">

                <!-- API pill -->
                {{-- <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-50 dark:bg-gray-700/60 rounded-lg">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="mono text-gray-500 dark:text-gray-400" style="font-size:11px;">
                        {{ config('app.external_api_url', '192.168.1.50:8001') }}
                    </span>
                </div> --}}

                <!-- Dark mode toggle -->
                <button @click="toggleDark()"
                        class="relative w-11 h-6 rounded-full transition-colors duration-300 focus:outline-none"
                        :class="darkMode ? 'bg-navy-600' : 'bg-gray-300'"
                        :title="darkMode ? 'Light mode' : 'Dark mode'">
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 flex items-center justify-center"
                          :class="darkMode ? 'translate-x-5' : 'translate-x-0'">
                        <i :class="darkMode ? 'ti ti-moon text-navy-600' : 'ti ti-sun text-yellow-500'"
                           style="font-size:11px;"></i>
                    </span>
                </button>

            </div>
        </header>

        <!-- ── Flash Messages ── -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="anim-fade mx-4 md:mx-6 mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between gap-3 text-sm text-green-700 dark:text-green-300">
            <span class="flex items-center gap-2"><i class="ti ti-check-circle text-lg"></i> {{ session('success') }}</span>
            <button @click="show=false" class="text-green-400 hover:text-green-600 flex-shrink-0"><i class="ti ti-x"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="anim-fade mx-4 md:mx-6 mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-center justify-between gap-3 text-sm text-red-700 dark:text-red-300">
            <span class="flex items-center gap-2"><i class="ti ti-alert-circle text-lg"></i> {{ session('error') }}</span>
            <button @click="show=false" class="text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-x"></i></button>
        </div>
        @endif

        <!-- ── Page Content ── -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @yield('content')
        </main>

    </div><!-- /main area -->
</div><!-- /flex wrapper -->

<script>
function appLayout() {
    return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: window.innerWidth >= 768,
        isDesktop: window.innerWidth >= 768,
        init() {
            this.darkMode = localStorage.getItem('darkMode') === 'true';
            this.isDesktop = window.innerWidth >= 768;
            this.sidebarOpen = this.isDesktop;
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 768;
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

@yield('scripts')
</body>
</html>