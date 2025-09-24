<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        $registrationEnabled = SystemSetting::isRegistrationEnabled();
        return view('auth.simple-login', compact('registrationEnabled'));
    }

    public function store(Request $request)
    {
        // Rate Limiting - IP별로 1분에 5번, 이메일별로 1분에 3번 시도 제한
        $ipKey = 'login-attempts-ip:' . $request->ip();
        $emailKey = 'login-attempts-email:' . strtolower($request->input('email'));

        // IP 기반 제한 확인
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);

            // 의심스러운 활동 로그
            Log::warning('브루트포스 공격 감지 (IP 제한)', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent(),
                'available_in' => $seconds
            ]);

            throw ValidationException::withMessages([
                'email' => "너무 많은 로그인 시도입니다. {$seconds}초 후에 다시 시도해주세요.",
            ]);
        }

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 이메일 기반 제한 확인
        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            $seconds = RateLimiter::availableIn($emailKey);

            Log::warning('이메일별 로그인 시도 한도 초과', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            throw ValidationException::withMessages([
                'email' => "이 계정에 대한 로그인 시도가 너무 많습니다. {$seconds}초 후에 다시 시도해주세요.",
            ]);
        }

        // 로그인 시도 증가
        RateLimiter::hit($ipKey, 60); // 1분
        RateLimiter::hit($emailKey, 60); // 1분

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // 실패 시 추가 제한 및 로그
            RateLimiter::hit($ipKey . '-failed', 300); // 5분간 실패 기록

            Log::info('로그인 실패', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 성공 시 제한 해제
        RateLimiter::clear($ipKey);
        RateLimiter::clear($emailKey);

        // 성공 로그 (관리자 계정인 경우만)
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            Log::info('관리자 로그인', [
                'admin_email' => $user->email,
                'admin_name' => $user->name,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}