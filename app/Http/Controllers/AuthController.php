<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ==========================================
    // WEB SESSION AUTHENTICATION
    // ==========================================

    /**
     * Show the web login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a web login request with 90-day persistence support.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Default remember to true for 90-day persistent session unless unchecked
        $remember = $request->boolean('remember', true);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('status', 'Selamat datang kembali di Dompetify!');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Show the web registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a web registration request and auto-provision starter wallets.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-provision starter wallets (BCA, BRImo, GoPay, DANA, Cash)
        $user->provisionDefaultData();

        // Log user in with persistent 90-day session
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('status', 'Akun berhasil dibuat! 5 dompet starter Anda telah siap digunakan.');
    }

    /**
     * Handle web logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')
            ->with('status', 'Anda telah berhasil keluar.');
    }

    // ==========================================
    // API SANCTUM TOKEN AUTHENTICATION
    // ==========================================

    /**
     * Handle API user registration.
     */
    public function apiRegister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-provision starter wallets
        $user->provisionDefaultData();

        $deviceName = $validated['device_name'] ?? $request->userAgent() ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Dompet starter telah otomatis disiapkan.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
        ], 201);
    }

    /**
     * Handle API user login.
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi email atau kata sandi salah.',
            ], 401);
        }

        $deviceName = $validated['device_name'] ?? $request->userAgent() ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Handle API user logout by revoking active Sanctum token.
     */
    public function apiLogout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Token sesi berhasil dicabut (Logout berhasil).',
        ]);
    }

    /**
     * Get authenticated user profile and financial summary stats.
     */
    public function apiMe(Request $request): JsonResponse
    {
        $user = $request->user();

        $wallets = $user->wallets()->active()->get();
        $totalNetWorth = (float) $wallets->sum('balance');
        $walletsCount = $wallets->count();
        $transactionsCount = $user->transactions()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'stats' => [
                    'total_net_worth' => $totalNetWorth,
                    'active_wallets_count' => $walletsCount,
                    'total_transactions_count' => $transactionsCount,
                ],
            ],
        ]);
    }
}
