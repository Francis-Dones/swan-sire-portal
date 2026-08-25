<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sign in · SWAN Maritime Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: {
                        navy: { 50:'#EFF4FB',100:'#D6E3F3',200:'#B9D0EC',300:'#8FB2DE',400:'#4E7FBC',500:'#24589C',600:'#1E4E8C',700:'#183E70',800:'#12305B',900:'#0B1F3A',950:'#071627' },
                        cyan: { 300:'#67E8F9',400:'#22D3EE',500:'#06B6D4',600:'#0891B2' }
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
    <style>
        * { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        [x-cloak] { display: none !important; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .floaty { animation: floaty 6s ease-in-out infinite; }
        @keyframes wave { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .wave { animation: wave 14s linear infinite; }
    </style>
</head>
<body x-data="themeManager()" x-init="initTheme()"
      class="min-h-screen bg-slate-50 dark:bg-navy-950 text-slate-800 dark:text-slate-100 transition-colors">

<div class="min-h-screen grid lg:grid-cols-2">

    <!-- ░░ BRAND PANEL ░░ -->
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950 p-12 text-white">
        <!-- glows -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute top-1/3 -left-24 w-80 h-80 rounded-full bg-navy-500/30 blur-3xl"></div>
        <!-- wave -->
        <svg class="wave absolute bottom-0 left-0 w-[200%] h-40 text-cyan-500/10" viewBox="0 0 1440 200" preserveAspectRatio="none" fill="currentColor">
            <path d="M0,80 C240,160 480,0 720,80 C960,160 1200,0 1440,80 L1440,200 L0,200 Z"></path>
        </svg>

        <!-- brand -->
        <div class="relative flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center shadow-lg shadow-cyan-500/30 floaty">
                <i class="ti ti-anchor text-white text-2xl"></i>
            </div>
            <div>
                <p class="font-extrabold text-xl tracking-tight leading-none">SWAN Portal</p>
                <p class="mono text-cyan-300/80 text-xs mt-1">Maritime Inspection Suite</p>
            </div>
        </div>

        <!-- headline -->
        <div class="relative max-w-md">
            <h1 class="text-4xl font-extrabold leading-tight tracking-tight">
                Ship inspection,<br>
                <span class="bg-gradient-to-r from-cyan-300 to-cyan-500 bg-clip-text text-transparent">reimagined.</span>
            </h1>
            <p class="mt-4 text-navy-100/80 text-[15px] leading-relaxed">
                Review VIQ questionnaires, track your fleet and manage inspection evidence — all in one calm, premium workspace.
            </p>
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-sm text-navy-100/90">
                    <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"><i class="ti ti-clipboard-check text-cyan-300"></i></span>
                    Structured VIQ review &amp; approvals
                </div>
                <div class="flex items-center gap-3 text-sm text-navy-100/90">
                    <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"><i class="ti ti-photo text-cyan-300"></i></span>
                    Vessel photo evidence library
                </div>
                <div class="flex items-center gap-3 text-sm text-navy-100/90">
                    <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"><i class="ti ti-chart-line text-cyan-300"></i></span>
                    Fleet-wide compliance insight
                </div>
            </div>
        </div>

        <!-- footer -->
        <div class="relative flex items-center justify-between text-xs text-navy-100/60">
            <span>&copy; {{ date('Y') }} SWAN Maritime</span>
            <span class="mono">v2.0</span>
        </div>
    </div>

    <!-- ░░ FORM PANEL ░░ -->
    <div class="flex items-center justify-center p-6 sm:p-10 relative">
        <!-- theme toggle -->
        <button @click="toggleTheme()"
                class="absolute top-5 right-5 w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-800 hover:text-navy-600 dark:hover:text-cyan-300 transition-all shadow-sm"
                :title="isDark ? 'Light mode' : 'Dark mode'">
            <i :class="isDark ? 'ti ti-sun' : 'ti ti-moon'" class="text-lg"></i>
        </button>

        <div class="w-full max-w-sm">
            <!-- mobile brand -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-navy-600 to-cyan-600 flex items-center justify-center shadow-lg">
                    <i class="ti ti-anchor text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-extrabold text-lg tracking-tight leading-none text-slate-900 dark:text-white">SWAN Portal</p>
                    <p class="mono text-slate-400 text-[11px] mt-0.5">Maritime Inspection</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome back</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5 mb-7">Sign in to continue to your workspace.</p>

            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl text-sm flex items-center gap-2.5 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                    <i class="ti ti-alert-circle text-lg flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl text-sm flex items-center gap-2.5 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                    <i class="ti ti-exclamation-circle text-lg flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" x-data="{ loading: false, showPass: false }" @submit="loading = true">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1.5 text-slate-700 dark:text-slate-300">Username or email</label>
                    <div class="relative">
                        <i class="ti ti-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                        <input type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                               class="w-full py-3 pl-11 pr-3 rounded-xl border bg-white dark:bg-navy-900 border-slate-200 dark:border-navy-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-4 focus:ring-navy-500/12 focus:border-navy-500 outline-none transition"
                               placeholder="your username">
                    </div>
                </div>

                <div class="mb-5">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                    </div>
                    <div class="relative">
                        <i class="ti ti-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                        <input :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="w-full py-3 pl-11 pr-11 rounded-xl border bg-white dark:bg-navy-900 border-slate-200 dark:border-navy-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-4 focus:ring-navy-500/12 focus:border-navy-500 outline-none transition"
                               placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <i :class="showPass ? 'ti ti-eye-off' : 'ti ti-eye'" class="text-lg"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-navy-600 to-cyan-600 hover:from-navy-500 hover:to-cyan-500 transition-all shadow-lg shadow-navy-600/25 hover:shadow-cyan-500/30 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99] flex items-center justify-center gap-2 disabled:opacity-70">
                    <span x-show="!loading" class="flex items-center gap-2">
                        <i class="ti ti-login-2 text-lg"></i> Sign in
                    </span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Signing in…
                    </span>
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-slate-400 dark:text-slate-500">
                Secured maritime access · Authorised personnel only
            </p>
        </div>
    </div>
</div>

<script>
    function themeManager() {
        return {
            isDark: true,
            initTheme() {
                const saved = localStorage.getItem('darkMode');
                if (saved === 'true') this.isDark = true;
                else if (saved === 'false') this.isDark = false;
                else this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                this.apply();
            },
            toggleTheme() {
                this.isDark = !this.isDark;
                localStorage.setItem('darkMode', this.isDark ? 'true' : 'false');
                this.apply();
            },
            apply() {
                document.documentElement.classList.toggle('dark', this.isDark);
            }
        }
    }
</script>
</body>
</html>
