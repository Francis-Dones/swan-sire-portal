<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login · Ship Inspection</title>
    <!-- Tailwind + Plugins -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            transition: background 0.25s ease, color 0.2s ease;
        }
        /* smooth card transitions */
        .card-transition {
            transition: background 0.25s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-style {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body x-data="themeManager()" x-init="initTheme()" :class="{ 'bg-gray-950': isDark, 'bg-sky-50': !isDark }" class="min-h-screen flex items-center justify-center p-5">

    <!-- Simple centered card -->
    <div class="w-full max-w-[400px]">
        <!-- Tiny greeting + day -->
        <div class="text-center mb-6" x-data="{ greeting: '' }" x-init="greeting = new Date().getHours() < 12 ? 'Morning' : (new Date().getHours() < 18 ? 'Afternoon' : 'Evening')">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 shadow-md mb-3">
                <i class="ti ti-anchor text-white text-2xl"></i>
            </div>
            <p class="text-sm font-medium tracking-wide" :class="isDark ? 'text-slate-300' : 'text-slate-600'">Hello! Have a</p>
            <h2 class="text-3xl font-bold tracking-tight mt-0.5" :class="isDark ? 'text-white' : 'text-slate-800'">GOOD <span x-text="greeting.toUpperCase()"></span></h2>
        </div>

        <!-- Login Card - clean, minimal -->
        <div class="rounded-2xl shadow-xl card-transition overflow-hidden"
             :class="isDark ? 'bg-gray-900/80 backdrop-blur-sm border border-gray-800' : 'bg-white/90 backdrop-blur-sm border border-white/60'">
            
            <div class="p-6 sm:p-7">
                <h3 class="text-xl font-semibold text-center mb-6" :class="isDark ? 'text-white' : 'text-gray-800'">Login</h3>
                
                <!-- Alert errors (clean) -->
                <?php if(session('error')): ?>
                    <div class="mb-4 p-3 rounded-xl text-sm flex items-center gap-2" :class="isDark ? 'bg-red-500/10 text-red-300 border border-red-500/30' : 'bg-red-50 text-red-700 border border-red-200'">
                        <i class="ti ti-alert-circle text-lg"></i>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if($errors->any()): ?>
                    <div class="mb-4 p-3 rounded-xl text-sm flex items-center gap-2" :class="isDark ? 'bg-red-500/10 text-red-300 border border-red-500/30' : 'bg-red-50 text-red-700 border border-red-200'">
                        <i class="ti ti-exclamation-circle text-lg"></i>
                        <span><?php echo e($errors->first()); ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo e(route('login.post')); ?>" x-data="{ loading: false, showPass: false }" @submit="loading = true">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Username field -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1.5" :class="isDark ? 'text-gray-300' : 'text-gray-700'">Username</label>
                        <div class="relative">
                            <i class="ti ti-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="text" name="login" value="<?php echo e(old('login')); ?>" required autofocus
                                   class="input-style w-full py-2.5 pl-10 pr-3 rounded-xl border focus:ring-2 focus:ring-blue-500/40 outline-none"
                                   :class="isDark ? 'bg-gray-800 border-gray-700 text-white placeholder:text-gray-500' : 'bg-gray-50 border-gray-200 text-gray-900 placeholder:text-gray-400'"
                                   placeholder="your username or email">
                        </div>
                    </div>
                    
                    <!-- Password field -->
                    <div class="mb-5">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-sm font-medium" :class="isDark ? 'text-gray-300' : 'text-gray-700'">Password</label>
                            <a href="#" class="text-xs font-medium transition hover:underline" :class="isDark ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-700'">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   class="input-style w-full py-2.5 pl-10 pr-10 rounded-xl border focus:ring-2 focus:ring-blue-500/40 outline-none"
                                   :class="isDark ? 'bg-gray-800 border-gray-700 text-white placeholder:text-gray-500' : 'bg-gray-50 border-gray-200 text-gray-900 placeholder:text-gray-400'"
                                   placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-500">
                                <i :class="showPass ? 'ti ti-eye-off' : 'ti ti-eye'" class="text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" 
                            class="w-full py-2.5 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                            :disabled="loading">
                        <template x-if="!loading">
                            <>
                                <i class="ti ti-login text-lg"></i>
                                <span>Login</span>
                            </>
                        </template>
                        <template x-if="loading">
                            <>
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span>Logging in...</span>
                            </>
                        </template>
                    </button>
                    
                    <!-- Signup link (clean) -->
                   
                </form>
            </div>
        </div>
        
        <!-- tiny theme toggle button (discreet) -->
        <div class="flex justify-center mt-6">
            <button @click="toggleTheme()" class="text-xs flex items-center gap-1.5 px-3 py-1.5 rounded-full transition-all"
                    :class="isDark ? 'bg-gray-800/60 text-gray-300 hover:bg-gray-700' : 'bg-white/70 text-gray-600 hover:bg-gray-100 shadow-sm'">
                <i :class="isDark ? 'ti ti-sun' : 'ti ti-moon'" class="text-sm"></i>
                <span x-text="isDark ? 'Light mode' : 'Dark mode'"></span>
            </button>
        </div>
    </div>

    <script>
        function themeManager() {
            return {
                isDark: true,
                initTheme() {
                    const saved = localStorage.getItem('theme_pref');
                    if (saved === 'light') this.isDark = false;
                    else if (saved === 'dark') this.isDark = true;
                    else this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    this.applyDocClass();
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme_pref', this.isDark ? 'dark' : 'light');
                    this.applyDocClass();
                },
                applyDocClass() {
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                        document.documentElement.classList.remove('light');
                    } else {
                        document.documentElement.classList.add('light');
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\ship-inspection-portal\resources\views/auth/login.blade.php ENDPATH**/ ?>