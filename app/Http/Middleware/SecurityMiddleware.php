<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // IP 차단 목록 확인
        if ($this->isBlocked($ip)) {
            Log::warning('차단된 IP 접근 시도', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);

            abort(403, '접근이 차단되었습니다.');
        }

        // 의심스러운 요청 패턴 감지
        $this->detectSuspiciousActivity($request);

        return $next($request);
    }

    /**
     * IP가 차단되어 있는지 확인
     */
    private function isBlocked(string $ip): bool
    {
        return Cache::has("blocked_ip:{$ip}");
    }

    /**
     * 의심스러운 활동 감지
     */
    private function detectSuspiciousActivity(Request $request): void
    {
        $ip = $request->ip();

        // 1시간 내 요청 횟수 추적
        $requestKey = "requests_per_hour:{$ip}";
        $requestCount = Cache::increment($requestKey, 1);

        if (Cache::missing($requestKey)) {
            Cache::put($requestKey, 1, now()->addHour());
        }

        // 1시간에 500번 이상 요청 시 의심스러운 활동으로 간주
        if ($requestCount > 500) {
            $this->blockIP($ip, '과도한 요청 횟수', 60); // 1시간 차단
        }

        // 로그인 페이지에 대한 과도한 접근
        if ($request->is('login') || $request->is('register')) {
            $loginRequestKey = "login_requests:{$ip}";
            $loginCount = Cache::increment($loginRequestKey, 1);

            if (Cache::missing($loginRequestKey)) {
                Cache::put($loginRequestKey, 1, now()->addMinutes(10));
            }

            // 10분에 50번 이상 로그인 페이지 접근 시 차단
            if ($loginCount > 50) {
                $this->blockIP($ip, '로그인 페이지 과도한 접근', 30); // 30분 차단
            }
        }
    }

    /**
     * IP 차단
     */
    private function blockIP(string $ip, string $reason, int $minutes): void
    {
        Cache::put("blocked_ip:{$ip}", [
            'reason' => $reason,
            'blocked_at' => now(),
            'expires_at' => now()->addMinutes($minutes)
        ], now()->addMinutes($minutes));

        Log::alert('IP 자동 차단', [
            'ip' => $ip,
            'reason' => $reason,
            'blocked_for_minutes' => $minutes,
            'blocked_until' => now()->addMinutes($minutes)
        ]);
    }
}