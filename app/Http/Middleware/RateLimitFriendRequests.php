<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitFriendRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        // 사용자별 친구 요청 제한: 시간당 10개
        $cacheKey = "friend_requests_" . $user->id;
        $currentCount = Cache::get($cacheKey, 0);

        // 제한 초과 시 429 응답
        if ($currentCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => '시간당 친구 요청 한도를 초과했습니다. 잠시 후 다시 시도해주세요.'
            ], 429);
        }

        // 요청 카운트 증가 (1시간 TTL)
        Cache::put($cacheKey, $currentCount + 1, now()->addHour());

        return $next($request);
    }
}
