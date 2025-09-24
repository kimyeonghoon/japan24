<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Castle;
use App\Models\VisitRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /**
     * 기본 헬스체크 엔드포인트
     */
    public function health()
    {
        try {
            // 데이터베이스 연결 확인
            $dbStatus = $this->checkDatabase();

            // 캐시 시스템 확인
            $cacheStatus = $this->checkCache();

            $status = $dbStatus && $cacheStatus ? 'healthy' : 'unhealthy';
            $httpCode = $status === 'healthy' ? 200 : 503;

            return response()->json([
                'status' => $status,
                'timestamp' => now()->toISOString(),
                'checks' => [
                    'database' => $dbStatus ? 'ok' : 'failed',
                    'cache' => $cacheStatus ? 'ok' : 'failed',
                ]
            ], $httpCode);

        } catch (\Exception $e) {
            Log::error('Health check failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => 'Internal server error'
            ], 503);
        }
    }

    /**
     * Prometheus 메트릭 엔드포인트
     */
    public function metrics()
    {
        try {
            $metrics = [];

            // 애플리케이션 상태
            $metrics[] = '# HELP japan24_app_up Application status (1 = up, 0 = down)';
            $metrics[] = '# TYPE japan24_app_up gauge';
            $metrics[] = 'japan24_app_up 1';

            // 데이터베이스 메트릭
            $dbConnected = $this->checkDatabase() ? 1 : 0;
            $metrics[] = '# HELP japan24_db_connected Database connection status (1 = connected, 0 = disconnected)';
            $metrics[] = '# TYPE japan24_db_connected gauge';
            $metrics[] = "japan24_db_connected {$dbConnected}";

            // 사용자 수
            $userCount = User::count();
            $metrics[] = '# HELP japan24_users_total Total number of users';
            $metrics[] = '# TYPE japan24_users_total gauge';
            $metrics[] = "japan24_users_total {$userCount}";

            // 관리자 수
            $adminCount = User::where('is_admin', true)->count();
            $metrics[] = '# HELP japan24_admins_total Total number of admin users';
            $metrics[] = '# TYPE japan24_admins_total gauge';
            $metrics[] = "japan24_admins_total {$adminCount}";

            // 방문 기록 통계
            $totalVisits = VisitRecord::count();
            $approvedVisits = VisitRecord::where('verification_status', 'approved')->count();
            $pendingVisits = VisitRecord::where('verification_status', 'pending')->count();
            $rejectedVisits = VisitRecord::where('verification_status', 'rejected')->count();

            $metrics[] = '# HELP japan24_visits_total Total number of visit records';
            $metrics[] = '# TYPE japan24_visits_total gauge';
            $metrics[] = "japan24_visits_total {$totalVisits}";

            $metrics[] = '# HELP japan24_visits_by_status Visit records by status';
            $metrics[] = '# TYPE japan24_visits_by_status gauge';
            $metrics[] = "japan24_visits_by_status{status=\"approved\"} {$approvedVisits}";
            $metrics[] = "japan24_visits_by_status{status=\"pending\"} {$pendingVisits}";
            $metrics[] = "japan24_visits_by_status{status=\"rejected\"} {$rejectedVisits}";

            // 보안 메트릭
            $blockedIPs = $this->getBlockedIPsCount();
            $metrics[] = '# HELP japan24_security_blocked_ips Currently blocked IP addresses';
            $metrics[] = '# TYPE japan24_security_blocked_ips gauge';
            $metrics[] = "japan24_security_blocked_ips {$blockedIPs}";

            // 캐시 상태
            $cacheStatus = $this->checkCache() ? 1 : 0;
            $metrics[] = '# HELP japan24_cache_status Cache system status (1 = working, 0 = failed)';
            $metrics[] = '# TYPE japan24_cache_status gauge';
            $metrics[] = "japan24_cache_status {$cacheStatus}";

            return response(implode("\n", $metrics), 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');

        } catch (\Exception $e) {
            Log::error('Metrics endpoint failed', ['error' => $e->getMessage()]);

            return response('# Error generating metrics', 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    /**
     * 데이터베이스 연결 확인
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            // 간단한 쿼리로 실제 연결 테스트
            DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 캐시 시스템 확인
     */
    private function checkCache(): bool
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $result = Cache::get($testKey);
            Cache::forget($testKey);

            return $result === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 차단된 IP 수 확인
     */
    private function getBlockedIPsCount(): int
    {
        try {
            // 데이터베이스 캐시를 사용하므로 직접 cache 테이블에서 조회
            $blockedKeys = DB::table('cache')
                ->where('key', 'LIKE', '%blocked_ip:%')
                ->count();

            return $blockedKeys;
        } catch (\Exception $e) {
            return 0;
        }
    }
}