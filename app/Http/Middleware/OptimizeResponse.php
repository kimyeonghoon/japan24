<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * 응답 최적화 및 성능 모니터링 미들웨어
 *
 * 이 미들웨어는 Japan24 애플리케이션의 모든 HTTP 요청에 대해
 * 성능을 모니터링하고 응답을 최적화하는 기능을 제공합니다.
 *
 * 주요 기능:
 * - 요청 처리 시간 측정 (밀리초 단위)
 * - 메모리 사용량 추적 (MB 단위)
 * - 느린 요청 자동 로깅 (1초 이상)
 * - 개발 모드에서 성능 헤더 추가
 * - 보안 헤더 자동 추가
 * - 정적 콘텐츠 캐시 헤더 설정
 *
 * 성능 임계값:
 * - 느린 요청 기준: 1초 (1000ms) 이상
 * - 정적 파일 캐시: 1년 (31536000초)
 *
 * @package App\Http\Middleware
 * @author Japan24 Development Team
 * @version 1.0.0
 */
class OptimizeResponse
{
    /**
     * 들어오는 요청을 처리하고 응답을 최적화합니다.
     *
     * 요청 처리 전후의 시간과 메모리 사용량을 측정하여
     * 성능 지표를 수집하고, 응답에 최적화 헤더를 추가합니다.
     *
     * @param Request $request HTTP 요청 객체
     * @param Closure $next 다음 미들웨어 또는 컨트롤러로 요청을 전달하는 클로저
     * @return Response 최적화된 HTTP 응답 객체
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 성능 측정 시작 - 요청 처리 시작 시점의 시간과 메모리 기록
        $startTime = microtime(true);        // 마이크로초 단위의 정확한 시간
        $startMemory = memory_get_usage(true); // 실제 할당된 메모리 (바이트)

        // 다음 미들웨어 또는 컨트롤러로 요청 전달하여 응답 생성
        $response = $next($request);

        // 성능 측정 종료 - 요청 처리 완료 시점의 시간과 메모리 기록
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        // 성능 지표 계산
        $executionTime = round(($endTime - $startTime) * 1000, 2); // 밀리초 단위로 변환
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB 단위로 변환
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2); // 최대 메모리 사용량

        // 느린 요청에 대한 성능 로깅 (1초 이상 소요된 요청)
        if ($executionTime > 1000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => $executionTime,
                'memory_usage_mb' => $memoryUsage,
                'peak_memory_mb' => $peakMemory,
                'status_code' => $response->getStatusCode(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Add performance headers for debugging
        if (config('app.debug')) {
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Usage', $memoryUsage . 'MB');
            $response->headers->set('X-Peak-Memory', $peakMemory . 'MB');
        }

        // Add security and optimization headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Cache headers for static content
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
        }

        return $response;
    }
}