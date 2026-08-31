@extends('layouts.app')

@section('title', 'Masuk Akun - Dompetify')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-slate-50">
    <!-- Background Glow -->
    <div class="absolute -top-40 right-1/4 w-96 h-96 bg-brand-300/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-10 left-1/4 w-96 h-96 bg-cyan-300/30 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-2xl shadow-slate-200/50">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-emerald-500 text-white mx-auto flex items-center justify-center shadow-md shadow-brand-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-xs text-slate-500">Masuk untuk mengelola keuangan & dompet Anda</p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                <div class="relative">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 rounded-xl border @error('email') border-rose-500 bg-rose-50/50 @else border-slate-300 @enderror text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                        placeholder="nama@email.com"
                    >
                </div>
                @error('email')
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    class="w-full px-4 py-3 rounded-xl border @error('password') border-rose-500 bg-rose-50/50 @else border-slate-300 @enderror text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-sm outline-none" 
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 90-Day Persistent Session Checkbox -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        value="1" 
                        checked 
                        class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 transition"
                    >
                    <span class="text-xs font-medium text-slate-600">Ingat saya selama <strong>90 hari (3 Bulan)</strong></span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3.5 px-4 rounded-xl text-sm font-extrabold text-white bg-gradient-to-r from-brand-600 via-emerald-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200"
            >
                Masuk ke Dompetify
            </button>
        </form>

        <!-- Quick Demo Account Fill -->
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span>Ingin mencoba fitur?</span>
                <span class="font-bold text-slate-700">Demo Akun</span>
            </div>
            <button 
                type="button" 
                onclick="fillDemoAccount()" 
                class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-2"
            >
                <span>⚡ Isi Otomatis Akun Demo (admin@dompetify.com)</span>
            </button>
        </div>

        <!-- Footer link -->
        <p class="text-center text-xs text-slate-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 underline">
                Daftar Akun Baru (Gratis)
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function fillDemoAccount() {
        document.getElementById('email').value = 'admin@dompetify.com';
        document.getElementById('password').value = 'password123';
    }
</script>
@endpush
@endsection
