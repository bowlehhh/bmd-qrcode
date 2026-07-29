<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 900;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            foreach ($this->throttleKeys($request) as $key) {
                RateLimiter::hit($key, self::LOCKOUT_SECONDS);
            }

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        foreach ($this->throttleKeys($request) as $key) {
            RateLimiter::clear($key);
        }
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'login',
            'description' => 'Berhasil login ke sistem.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'description' => 'Keluar dari sistem.',
            'ip_address' => $request->ip(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function ensureIsNotRateLimited(LoginRequest $request): void
    {
        foreach ($this->throttleKeys($request) as $key) {
            if (RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS)) {
                event(new Lockout($request));

                throw ValidationException::withMessages([
                    'email' => 'Terlalu banyak percobaan login. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
                ]);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function throttleKeys(LoginRequest $request): array
    {
        $email = Str::lower(trim((string) $request->input('email')));

        return [
            'login:email:'.hash('sha256', Str::transliterate($email)),
            'login:ip:'.hash('sha256', (string) $request->ip()),
        ];
    }
}
