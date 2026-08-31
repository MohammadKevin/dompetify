@extends('layouts.app')

@section('title', 'Dompetify - Smart Multi-Wallet Finance & Cashflow Tracking')

@section('content')
<div class="relative overflow-hidden">
    <!-- Background Decor Elements -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[600px] overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-40 left-1/4 w-[500px] h-[500px] rounded-full bg-gradient-to-tr from-brand-300/30 to-cyan-300/30 blur-3xl"></div>
        <div class="absolute top-20 right-1/4 w-[450px] h-[450px] rounded-full bg-gradient-to-br from-emerald-300/25 to-teal-300/25 blur-3xl"></div>
    </div>

    <!-- ========================================== -->
    <!-- HERO SECTION -->
    <!-- ========================================== -->
    <section class="pt-12 pb-20 lg:pt-20 lg:pb-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Copy & CTAs -->
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-50 border border-brand-200/80 text-brand-800 text-xs font-bold shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-brand-500 animate-pulse"></span>
                        ✨ Rilis Terbaru: Dompetify Android APK v1.2.0 Tersedia!
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.12]">
                        Kelola Seluruh <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-emerald-500 to-teal-600">Dompet & Rekening</span> dalam Satu Genggaman.
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Pantau saldo bank (<strong>BCA, BRImo</strong>), dompet digital (<strong>GoPay, OVO, DANA</strong>), hingga kas tunai secara real-time. Dilengkapi AI scan struk dan pencatatan arus kas otomatis.
                    </p>

                    <!-- Primary Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <!-- Primary CTA pointing directly to /download/apps -->
                        <a href="{{ route('download.apps') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-brand-600 via-emerald-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 shadow-xl shadow-brand-500/30 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all duration-200 group">
                            <svg class="w-6 h-6 text-brand-200 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Download / Install App</span>
                        </a>

                        <!-- Secondary CTA -->
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-base font-bold text-slate-800 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm hover:shadow hover:-translate-y-0.5 transition-all duration-200">
                            <span>Daftar Akun Gratis</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Micro Feature Highlights -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0">
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-black text-slate-900">90 Hari</p>
                            <p class="text-xs font-semibold text-slate-500">Persistent Session</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-black text-slate-900">100%</p>
                            <p class="text-xs font-semibold text-slate-500">Privasi Multi-User</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-black text-slate-900">AI OCR</p>
                            <p class="text-xs font-semibold text-slate-500">Scan Struk Instan</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive UI Showcase Mockup -->
                <div class="lg:col-span-5 relative">
                    <!-- Glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-500 to-cyan-500 rounded-3xl blur-2xl opacity-20 transform -rotate-1"></div>
                    
                    <div class="relative bg-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-800 space-y-6">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Net Worth</span>
                                <div class="text-3xl font-black tracking-tight text-white mt-0.5">Rp 8.750.000<span class="text-slate-400 text-lg font-normal">,00</span></div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                </svg>
                                +14.2% Bulan Ini
                            </span>
                        </div>

                        <!-- Multi-Account Carousel / Grid Cards -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                                <span>DOMPET TERHUBUNG</span>
                                <span class="text-brand-400">5 Akun Aktif</span>
                            </div>

                            <!-- BCA Wallet Card -->
                            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#00529C] text-white flex items-center justify-center font-black text-xs shadow-md">
                                        BCA
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">Bank BCA</h4>
                                        <p class="text-[11px] font-mono text-slate-400">1234-5678-90</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-emerald-400">Rp 5.000.000</div>
                                    <span class="text-[10px] text-slate-400">Primary Bank</span>
                                </div>
                            </div>

                            <!-- BRImo Wallet Card -->
                            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#005596] text-white flex items-center justify-center font-black text-xs shadow-md">
                                        BRI
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">BRImo</h4>
                                        <p class="text-[11px] font-mono text-slate-400">0987-6543-21</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-emerald-400">Rp 2.500.000</div>
                                    <span class="text-[10px] text-slate-400">Savings</span>
                                </div>
                            </div>

                            <!-- GoPay & DANA Mini Cards -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#00AED6] text-white flex items-center justify-center font-bold text-[10px]">
                                        GP
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-white truncate">GoPay</p>
                                        <p class="text-xs font-extrabold text-emerald-400">Rp 350.000</p>
                                    </div>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#118EEA] text-white flex items-center justify-center font-bold text-[10px]">
                                        DN
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-white truncate">DANA</p>
                                        <p class="text-xs font-extrabold text-emerald-400">Rp 150.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transaction Sample -->
                        <div class="pt-2 border-t border-slate-800/80">
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                                <span class="font-bold">TRANSAKSI TERAKHIR</span>
                                <span>Hari ini</span>
                            </div>
                            <div class="flex items-center justify-between text-xs bg-slate-900/50 p-2.5 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <span class="p-1.5 rounded-lg bg-rose-500/20 text-rose-400">☕</span>
                                    <span class="font-medium text-slate-200">Kopi Kenangan & Roti</span>
                                </div>
                                <span class="font-bold text-rose-400">-Rp 44.500</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- FEATURE SHOWCASE SECTION -->
    <!-- ========================================== -->
    <section id="features" class="py-20 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-extrabold uppercase tracking-widest text-brand-600">FITUR KELAS ENTERPRISE</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Segala Kebutuhan Finansial Terintegrasi Penuh
                </h3>
                <p class="text-base text-slate-600">
                    Dirancang untuk fleksibilitas maksimal, mendukung pencatatan multi-dompet, OCR vision scan struk, dan keamanan sesi tanpa interupsi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: Multi-Account Wallets -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-brand-500/10 text-brand-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">Multi-Account Wallet Tracking</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kelola rekening BCA, BRImo, e-wallet (GoPay, OVO, DANA), hingga kas tunai. Lacak perpindahan saldo dan biaya admin transfer otomatis.
                    </p>
                </div>

                <!-- Feature 2: Cash Flow & Net Worth Analytics -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">Live Cashflow & Net Worth</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Ringkasan otomatis total kekayaan bersih, aggregate pengeluaran per kategori bulanan, dan audit mutasi transaksi secara real-time.
                    </p>
                </div>

                <!-- Feature 3: 90-Day Persistent Login -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">Sesi Login 90 Hari (3 Bulan)</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Nikmati kemudahan penggunaan tanpa harus login berulang kali. Token Sanctum dan sesi web tetap aktif aman selama 90 hari penuh.
                    </p>
                </div>

                <!-- Feature 4: Vision AI Receipt OCR -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">AI Vision Smart Receipt Scan</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Cukup foto struk belanjaan Anda. AI secara otomatis mengekstrak nominal, toko, tanggal, hingga rincian daftar barang dan harga.
                    </p>
                </div>

                <!-- Feature 5: Multi-Tenant Data Isolation -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">Multi-User SaaS Architecture</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Isolasi data row-level yang ketat. Setiap akun memiliki dompet, transaksi, dan riwayat mandiri tanpa risiko tercampur.
                    </p>
                </div>

                <!-- Feature 6: Android Notification Hook -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/5 transition duration-300 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900">SMS & Notification Webhook</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Integrasikan notifikasi transaksi bank dan e-wallet otomatis via webhook backend untuk pencatatan instan tanpa input manual.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- HOW IT WORKS & STARTER PACK SECTION -->
    <!-- ========================================== -->
    <section id="wallets" class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-brand-400">STARTER WALLETS OUT-OF-THE-BOX</span>
                    <h3 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                        Daftar dan Langsung Dapatkan 5 Dompet Siap Pakai
                    </h3>
                    <p class="text-slate-400 text-base leading-relaxed">
                        Saat mendaftar akun baru di Dompetify, sistem secara otomatis menginisialisasi 5 starter wallet untuk Anda:
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/80 border border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">BCA</span>
                            <span class="text-sm font-semibold">Bank Central Asia (Saldo Awal Rp 5.000.000)</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/80 border border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-cyan-600 text-white flex items-center justify-center font-bold text-xs">BRI</span>
                            <span class="text-sm font-semibold">BRImo / Bank BRI (Saldo Awal Rp 2.500.000)</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/80 border border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">GP</span>
                            <span class="text-sm font-semibold">GoPay & DANA E-Wallets</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/80 border border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-xs">CASH</span>
                            <span class="text-sm font-semibold">Dompet Tunai Fisik (Rp 500.000)</span>
                        </div>
                    </div>
                </div>

                <!-- Right Installation CTA Box -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-700 space-y-6 text-center shadow-2xl">
                    <div class="w-16 h-16 rounded-2xl bg-brand-500/20 text-brand-400 mx-auto flex items-center justify-center shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold text-white">Instal Dompetify di Smartphone Anda</h4>
                    <p class="text-sm text-slate-400 max-w-md mx-auto">
                        Unduh paket APK Android atau akses versi Progressive Web App (PWA) langsung dari browser tanpa instalasi rumit.
                    </p>
                    <a href="{{ route('download.apps') }}" class="inline-flex items-center justify-center gap-3 w-full py-4 px-6 rounded-2xl text-base font-bold text-slate-950 bg-brand-400 hover:bg-brand-300 shadow-lg shadow-brand-500/20 transition-transform hover:scale-[1.02]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Buka Halaman Download APK</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- BOTTOM CALL TO ACTION -->
    <!-- ========================================== -->
    <section class="py-20 bg-gradient-to-b from-slate-50 to-brand-50/50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <h3 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
                Mulai Kontrol Penuh Keuangan Anda Sekarang
            </h3>
            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto">
                Bergabunglah dan nikmati kemudahan mencatat keuangan multi-akun dengan sesi login 90 hari tanpa ribet.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('download.apps') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xl shadow-brand-500/20 hover:-translate-y-0.5 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Aplikasi (.apk)</span>
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-base font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 shadow-sm transition">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
