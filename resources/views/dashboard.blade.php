@extends('layouts.app')

@section('title', 'Dashboard Keuangan - Dompetify')

@section('content')
<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        Sesi Aktif 90 Hari
                    </span>
                    <span class="text-xs text-slate-400 font-mono">User ID: #{{ $user->id }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Halo, {{ $user->name }}! 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Berikut adalah ikhtisar saldo dompet dan arus kas Anda.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('download.apps') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition">
                    <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download APK Mobile
                </a>
            </div>
        </div>

        <!-- Net Worth Card & Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Net Worth -->
            <div class="md:col-span-2 bg-gradient-to-tr from-slate-950 via-slate-900 to-slate-800 text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
                    <span>TOTAL KEKAYAAN BERSIH (NET WORTH)</span>
                    <span class="text-brand-400">{{ $wallets->count() }} Dompet Terdaftar</span>
                </div>
                <div class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                    Rp {{ number_format($totalNetWorth, 2, ',', '.') }}
                </div>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-400 border-t border-slate-800">
                    <span class="flex items-center gap-1.5 text-emerald-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status: Akun SaaS Multi-Tenant Terisolasi
                    </span>
                </div>
            </div>

            <!-- Mobile App Connection Promo -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                        📱
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Aplikasi Android</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Gunakan aplikasi Android untuk scan struk otomatis dengan AI dan sinkronisasi instan.
                    </p>
                </div>
                <a href="{{ route('download.apk') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold text-slate-900 bg-brand-100 hover:bg-brand-200 transition">
                    <span>Unduh APK Sekarang</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Wallets Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Daftar Dompet Anda</h2>
                <span class="text-xs font-semibold text-slate-500">{{ $wallets->count() }} Dompet Aktif</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($wallets as $wallet)
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-300 transition space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl text-white flex items-center justify-center font-bold text-xs shadow" style="background-color: {{ $wallet->color_hex ?? '#00529C' }}">
                                    {{ substr($wallet->name, 0, 3) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $wallet->name }}</h4>
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $wallet->account_number ?? 'Dompet Kas' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">
                                {{ is_object($wallet->type) ? $wallet->type->value : $wallet->type }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Saldo</span>
                            <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($wallet->balance, 2, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 text-xs">
                        Belum ada dompet terdaftar.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Riwayat Transaksi Terkini</h2>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200/80 uppercase font-bold text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Tanggal</th>
                                <th class="py-3.5 px-6">Deskripsi</th>
                                <th class="py-3.5 px-6">Dompet</th>
                                <th class="py-3.5 px-6">Kategori</th>
                                <th class="py-3.5 px-6 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTransactions as $tx)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-6 text-slate-500 font-mono">{{ $tx->date ? $tx->date->format('d M Y H:i') : '-' }}</td>
                                    <td class="py-4 px-6 font-bold text-slate-900">{{ $tx->description ?? 'Transaksi' }}</td>
                                    <td class="py-4 px-6 text-slate-700">{{ $tx->wallet->name ?? '-' }}</td>
                                    <td class="py-4 px-6 text-slate-600">{{ $tx->category->name ?? '-' }}</td>
                                    <td class="py-4 px-6 text-right font-extrabold @if($tx->type == 'INCOME') text-emerald-600 @else text-rose-600 @endif">
                                        @if($tx->type == 'INCOME') + @else - @endif Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Belum ada catatan transaksi. Transaksi dari mobile app atau web akan muncul di sini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
