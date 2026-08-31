@extends('layouts.app')

@section('title', 'Daftar Akun Baru - Dompetify')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-slate-50">
    <!-- Background Glow -->
    <div class="absolute -top-40 left-1/4 w-96 h-96 bg-brand-300/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-teal-300/30 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-md w-full space-y-7 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-2xl shadow-slate-200/50">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-cyan-500 text-white mx-auto flex items-center justify-center shadow-md shadow-brand-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Buat Akun Dompetify</h2>
            <p class="text-xs text-slate-500">Mulai kelola seluruh dompet & arus kas Anda dengan mudah</p>
        </div>

        <!-- Bonus Perk Alert -->
        <div class="p-3.5 rounded-2xl bg-brand-50 border border-brand-200 text-xs text-brand-900 flex items-start gap-2.5">
            <span class="text-base">🎁</span>
            <div class="leading-relaxed">
                <strong>Starter Portfolio Otomatis:</strong> Akun baru langsung mendapatkan 5 dompet aktif (BCA, BRImo, GoPay, DANA, & Kas Tunai).
            </div>
        </div>

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Name Input -->
            <div class="space-y-1">
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    class="w-full px-4 py-3 rounded-xl border @error('name') border-rose-500 bg-rose-50/50 @else border-slate-300 @enderror text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                    placeholder="Contoh: Kevin Wijaya"
                >
                @error('name')
                    <p class="text-xs font-semibold text-rose-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Input -->
            <div class="space-y-1">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl border @error('email') border-rose-500 bg-rose-50/50 @else border-slate-300 @enderror text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                    placeholder="nama@email.com"
                >
                @error('email')
                    <p class="text-xs font-semibold text-rose-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-1">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    class="w-full px-4 py-3 rounded-xl border @error('password') border-rose-500 bg-rose-50/50 @else border-slate-300 @enderror text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                    placeholder="Minimal 8 karakter"
                >
                @error('password')
                    <p class="text-xs font-semibold text-rose-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="space-y-1">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required 
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                    placeholder="Ulangi kata sandi"
                >
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full py-3.5 px-4 rounded-xl text-sm font-extrabold text-white bg-gradient-to-r from-brand-600 via-emerald-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200"
                >
                    Daftar & Buat Dompet Starter
                </button>
            </div>
        </form>

        <!-- Footer link -->
        <p class="text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 underline">
                Masuk di sini
            </a>
        </p>
    </div>
</div>
@endsection
