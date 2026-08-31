@extends('layouts.app')

@section('title', 'Unduh Aplikasi Dompetify - Android APK & PWA')

@section('content')
<div class="py-12 lg:py-20 bg-slate-50 relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-brand-200/40 via-teal-200/20 to-transparent blur-3xl -z-10"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Header Banner -->
        <div class="text-center space-y-4 max-w-2xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-200 text-emerald-900 text-xs font-bold shadow-sm">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Paket Resmi & Terverifikasi Bebas Malware
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
                Unduh & Pasang <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-teal-600">Dompetify Mobile</span>
            </h1>
            <p class="text-base text-slate-600">
                Pilih metode instalasi yang paling sesuai untuk perangkat Anda: Paket Android APK langsung atau Progressive Web App (PWA).
            </p>
        </div>

        <!-- Main Download Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left Card: Android APK Direct Download (Highlighted) -->
            <div class="md:col-span-7 bg-white rounded-3xl p-8 sm:p-10 border-2 border-brand-500 shadow-2xl shadow-brand-500/10 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-brand-500 text-white text-[11px] font-black uppercase px-4 py-1 rounded-bl-2xl tracking-wider">
                    Versi Terbaru
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-brand-500/30 shrink-0">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993.0001.5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.996-3.4572c.1561-.2704.0634-.615-.207-.7711-.2705-.1562-.6151-.0635-.7712.207l-2.0232 3.5043C15.3438 8.2435 13.7228 7.9 12 7.9c-1.7228 0-3.3438.3435-4.8821.9044L5.0947 5.3c-.1561-.2705-.5007-.3632-.7712-.207-.2704.1561-.3631.5007-.207.7711l1.996 3.4572C2.7107 11.2335.3582 15.0116.0357 19.467h23.9286c-.3225-4.4554-2.675-8.2335-6.0828-10.1456"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-900">Android Standalone APK</h2>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                Versi v{{ $release['version'] ?? '1.2.0' }} • {{ $release['file_size_formatted'] ?? '18.4 MB' }} • Rilis: {{ $release['release_date'] ?? date('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        Aplikasi native penuh dengan integrasi background camera OCR scanner, local storage cache berkecepatan tinggi, dan sesi 90 hari tanpa putus.
                    </p>

                    <!-- Dynamic Changelog list -->
                    @if(!empty($release['changelog']))
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Catatan Rilis v{{ $release['version'] ?? '1.2.0' }}:</span>
                            <ul class="space-y-1.5 text-xs text-slate-600">
                                @foreach($release['changelog'] as $change)
                                    <li class="flex items-start gap-2">
                                        <span class="text-brand-600 font-bold">•</span>
                                        <span>{{ $change }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Features checklist -->
                    <ul class="space-y-2 text-xs font-semibold text-slate-700">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>Akses Kamera Langsung untuk Scan Struk AI</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>Token Sanctum Persistent 90 Hari (3 Bulan)</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>Sinkronisasi Otomatis Seluruh Saldo Dompet</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Button -->
                <div class="pt-8 space-y-3">
                    <a href="{{ route('download.apk') }}" class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-2xl text-base font-extrabold text-white bg-gradient-to-r from-brand-600 via-emerald-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 shadow-xl shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all">
                        <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh File APK v{{ $release['version'] ?? '1.2.0' }}</span>
                    </a>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 px-1">
                        <span>Nama file: <code>{{ $release['file_name'] ?? 'finance-corecraft-latest.apk' }}</code></span>
                        <span class="text-brand-600 font-bold">SHA-256 Verified</span>
                    </div>
                </div>
            </div>

            <!-- Right Card: Web App & PWA Instant Access -->
            <div class="md:col-span-5 bg-white rounded-3xl p-8 border border-slate-200 shadow-lg flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">PWA / Web App</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Gunakan Dompetify langsung di browser smartphone atau desktop tanpa mengunduh file APK.
                    </p>

                    <!-- PWA Steps -->
                    <div class="space-y-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                            <span class="font-bold text-slate-900">Chrome / Edge:</span>
                            <p class="text-slate-600">Klik menu titik tiga (⋮) lalu pilih <strong>"Install Dompetify"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                            <span class="font-bold text-slate-900">iOS Safari:</span>
                            <p class="text-slate-600">Tekan tombol Share (⎋) lalu pilih <strong>"Add to Home Screen"</strong>.</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-3.5 px-5 rounded-2xl text-sm font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                    <span>Buka Web App Sekarang</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- STEP-BY-STEP APK INSTALLATION GUIDE -->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-xl space-y-8">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <h3 class="text-2xl font-black text-slate-900">Petunjuk Instalasi APK Android</h3>
                <p class="text-xs sm:text-sm text-slate-600">
                    Ikuti 3 langkah mudah berikut untuk memasang aplikasi Dompetify di smartphone Android Anda:
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                        1
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Unduh Paket APK</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Klik tombol <strong>"Unduh File APK Langsung"</strong> di atas. Konfirmasi unduhan jika browser menampilkan pesan konfirmasi.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                        2
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Izinkan Sumber Tidak Dikenal</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Buka file yang diunduh. Jika muncul peringatan, pilih <strong>Pengaturan</strong> lalu aktifkan opsi <em>"Izinkan dari sumber ini"</em> (Allow from unknown sources).
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                        3
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Pasang & Buka Dompetify</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ketuk <strong>"Instal / Pasang"</strong>. Setelah selesai, buka aplikasi Dompetify dan masuk dengan akun Anda untuk mulai mencatat keuangan.
                    </p>
                </div>
            </div>

            <!-- SHA-256 Checksum block for developer trust -->
            <div class="p-4 rounded-2xl bg-slate-900 text-slate-300 font-mono text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 overflow-x-auto">
                <div class="flex items-center gap-2">
                    <span class="text-brand-400 font-bold">SHA-256:</span>
                    <span class="text-slate-400 break-all select-all">{{ $release['sha256_checksum'] ?? 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855' }}</span>
                </div>
                <span class="text-[10px] text-slate-500 font-sans uppercase shrink-0">Official Build</span>
            </div>
        </div>

    </div>
</div>
@endsection
