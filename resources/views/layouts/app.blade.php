<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dompetify - Aplikasi Manajemen Keuangan Multi-Akun & Smart Cashflow Tracker. Pantau rekening bank (BCA, BRImo), e-wallet (GoPay, DANA, OVO), dan transaksi harian dalam satu aplikasi aman.">
    <title>@yield('title', 'Dompetify - Smart Personal Finance & Multi-Wallet Manager')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN Fallback for rich standalone styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        dark: {
                            900: '#090d16',
                            800: '#0f172a',
                            700: '#1e293b',
                            600: '#334155',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glow-emerald {
            box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.3);
        }
        .glow-cyan {
            box-shadow: 0 0 50px -10px rgba(6, 182, 212, 0.3);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .glass-card-dark {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .grid-pattern {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-cyan-400 p-[2px] shadow-lg shadow-brand-500/20 group-hover:scale-105 transition-transform duration-200">
                        <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-1.5">
                            Dompetify
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800 uppercase tracking-wider">v1.2</span>
                        </span>
                        <p class="text-[11px] font-medium text-slate-400 -mt-1 hidden sm:block">Smart Multi-Wallet Finance</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('landing') }}#features" class="hover:text-brand-600 transition-colors">Fitur Unggulan</a>
                    <a href="{{ route('landing') }}#wallets" class="hover:text-brand-600 transition-colors">Multi-Dompet</a>
                    <a href="{{ route('landing') }}#security" class="hover:text-brand-600 transition-colors">Keamanan 90 Hari</a>
                    <a href="{{ route('download.apps') }}" class="hover:text-brand-600 transition-colors flex items-center gap-1.5 text-brand-600 font-bold">
                        <svg class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download App
                    </a>
                </nav>

                <!-- Auth / CTA Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition">
                            <svg class="w-4 h-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Dashboard ({{ auth()->user()->name }})
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2.5 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Logout">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition">
                            Sign Up
                        </a>
                        <a href="{{ route('download.apps') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-500 hover:to-emerald-400 shadow-md shadow-brand-500/20 transition-all hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Install App</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alert Messages -->
    @if(session('status'))
        <div class="bg-brand-50 border-b border-brand-200 py-3 px-4 text-center text-sm font-semibold text-brand-900 flex items-center justify-center gap-2">
            <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-b border-rose-200 py-3 px-4 text-center text-sm font-semibold text-rose-900 flex items-center justify-center gap-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Body Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-cyan-400 p-[2px]">
                            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-2xl font-extrabold text-white tracking-tight">Dompetify</span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        Platform pembukuan dan pemantau kekayaan multi-akun otomatis. Integrasikan rekening bank BCA, BRImo, e-wallet GoPay, DANA, OVO, dan dompet kas Anda dengan keamanan tingkat tinggi.
                    </p>
                    <div class="flex items-center gap-3 text-xs font-semibold text-emerald-400 pt-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Sesi Login Aktif 90 Hari (3 Bulan) Tanpa Ribet
                    </div>
                </div>

                <!-- Navigation Quick Links -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Navigasi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('landing') }}" class="hover:text-brand-400 transition">Beranda</a></li>
                        <li><a href="{{ route('landing') }}#features" class="hover:text-brand-400 transition">Fitur Utama</a></li>
                        <li><a href="{{ route('download.apps') }}" class="hover:text-brand-400 transition font-bold text-brand-400">Download APK Android</a></li>
                        <li><a href="{{ url('/api/health') }}" target="_blank" class="hover:text-brand-400 transition">Status Server API</a></li>
                    </ul>
                </div>

                <!-- User & Auth -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Akses Akun</h4>
                    <ul class="space-y-2.5 text-sm">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-brand-400 transition">Dashboard Keuangan</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand-400 transition">Masuk (Sign In)</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand-400 transition">Daftar Akun Baru (Sign Up)</a></li>
                        @endauth
                        <li><a href="{{ route('download.apps') }}" class="hover:text-brand-400 transition">Petunjuk Instalasi APK</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Dompetify Finance App. All rights reserved. Multi-tenant SaaS architecture.</p>
                <div class="flex items-center gap-6">
                    <span class="inline-flex items-center gap-1.5 text-slate-400">
                        <svg class="w-4 h-4 text-brand-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Row-Level Data Encryption
                    </span>
                    <span class="text-slate-400">Bearer Sanctum API</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
