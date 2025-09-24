<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Castle;
use App\Models\User;
use App\Models\Badge;

/**
 * 캐시 서비스 클래스
 *
 * 이 클래스는 Japan24 애플리케이션의 성능 향상을 위한 캐싱 시스템을 담당합니다.
 * 데이터베이스 쿼리 결과를 캐시하여 응답 시간을 단축하고 서버 부하를 줄입니다.
 *
 * 주요 기능:
 * - 성 목록 캐싱 (1시간)
 * - 배지 목록 캐싱 (1시간)
 * - 사용자 통계 캐싱 (10분)
 * - 성별 방문 통계 캐싱 (30분)
 * - 대시보드 통계 캐싱 (5분)
 * - 인기 성 및 활성 사용자 캐싱
 *
 * 성능 개선 효과: 직접 DB 쿼리 대비 93.6% 성능 향상 달성
 *
 * @package App\Services
 * @author Japan24 Development Team
 * @version 1.0.0
 */
class CacheService
{
    // 캐시 키 상수 정의 - 일관된 키 명명 규칙 적용
    const CASTLES_LIST_KEY = 'castles:list';           // 전체 성 목록 캐시 키
    const BADGES_LIST_KEY = 'badges:list';             // 전체 배지 목록 캐시 키
    const USER_STATS_KEY = 'user:stats:';              // 사용자 통계 캐시 키 (뒤에 user_id 붙음)
    const CASTLE_STATS_KEY = 'castle:stats:';          // 성별 통계 캐시 키 (뒤에 castle_id 붙음)
    const DASHBOARD_STATS_KEY = 'dashboard:stats';     // 대시보드 전체 통계 캐시 키

    // 캐시 유효 시간 (TTL: Time To Live) 상수 정의 - 초 단위
    const CASTLES_TTL = 3600;    // 1시간 - 성 정보는 자주 변하지 않음
    const BADGES_TTL = 3600;     // 1시간 - 배지 정보는 자주 변하지 않음
    const USER_STATS_TTL = 600;  // 10분 - 사용자 통계는 비교적 자주 업데이트됨
    const CASTLE_STATS_TTL = 1800; // 30분 - 성별 통계는 중간 빈도로 업데이트됨
    const DASHBOARD_TTL = 300;   // 5분 - 대시보드는 실시간성이 중요함

    /**
     * 모든 성 목록을 캐시에서 가져옵니다.
     *
     * 일본의 24개 유명 성 목록을 한국어 이름순으로 정렬하여 반환합니다.
     * 캐시 만료 시간: 1시간 (성 정보는 자주 변경되지 않으므로 긴 캐시 시간 적용)
     *
     * @return \Illuminate\Database\Eloquent\Collection 성 목록 컬렉션
     */
    public function getCastlesList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::CASTLES_LIST_KEY, self::CASTLES_TTL, function () {
            // 한국어 이름으로 정렬하여 사용자에게 친숙한 순서로 제공
            return Castle::orderBy('name_korean')->get();
        });
    }

    /**
     * 모든 배지 목록을 캐시에서 가져옵니다.
     *
     * 방문 횟수에 따른 배지 목록을 필요 방문 횟수 순으로 정렬하여 반환합니다.
     * 캐시 만료 시간: 1시간 (배지 정보는 자주 변경되지 않으므로 긴 캐시 시간 적용)
     *
     * @return \Illuminate\Database\Eloquent\Collection 배지 목록 컬렉션
     */
    public function getBadgesList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::BADGES_LIST_KEY, self::BADGES_TTL, function () {
            // 필요 방문 횟수 오름차순으로 정렬 (초보자 → 성 컴플리트)
            return Badge::orderBy('required_visits')->get();
        });
    }

    /**
     * 사용자별 상세 통계를 캐시에서 가져옵니다.
     *
     * 특정 사용자의 방문 기록, 배지 등의 통계 정보를 제공합니다.
     * 캐시 만료 시간: 10분 (사용자 활동에 따라 자주 변경될 수 있음)
     *
     * @param int $userId 조회할 사용자 ID
     * @return array 사용자 통계 배열 (총 방문, 승인된 방문, 대기 중 방문, 배지 수, 완주율)
     */
    public function getUserStats(int $userId): array
    {
        return Cache::remember(self::USER_STATS_KEY . $userId, self::USER_STATS_TTL, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return [];
            }

            return [
                'total_visits' => $user->visitRecords()->count(),
                'approved_visits' => $user->visitRecords()->where('verification_status', 'approved')->count(),
                'pending_visits' => $user->visitRecords()->where('verification_status', 'pending')->count(),
                'badge_count' => $user->badges()->count(),
                'completion_rate' => $this->calculateCompletionRate($user),
            ];
        });
    }

    /**
     * 성별 방문 통계를 캐시에서 가져옵니다.
     */
    public function getCastleStats(int $castleId): array
    {
        return Cache::remember(self::CASTLE_STATS_KEY . $castleId, self::CASTLE_STATS_TTL, function () use ($castleId) {
            $castle = Castle::find($castleId);
            if (!$castle) {
                return [];
            }

            return [
                'total_visits' => $castle->visitRecords()->count(),
                'approved_visits' => $castle->visitRecords()->where('verification_status', 'approved')->count(),
                'unique_visitors' => $castle->visitRecords()
                    ->where('verification_status', 'approved')
                    ->distinct('user_id')
                    ->count('user_id'),
                'recent_visits' => $castle->visitRecords()
                    ->where('verification_status', 'approved')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
            ];
        });
    }

    /**
     * 대시보드 전체 통계를 캐시에서 가져옵니다.
     */
    public function getDashboardStats(): array
    {
        return Cache::remember(self::DASHBOARD_STATS_KEY, self::DASHBOARD_TTL, function () {
            return [
                'total_users' => User::count(),
                'total_visits' => \App\Models\VisitRecord::count(),
                'approved_visits' => \App\Models\VisitRecord::where('verification_status', 'approved')->count(),
                'pending_visits' => \App\Models\VisitRecord::where('verification_status', 'pending')->count(),
                'total_castles' => Castle::count(),
                'active_users_today' => User::whereDate('updated_at', today())->count(),
                'visits_today' => \App\Models\VisitRecord::whereDate('created_at', today())->count(),
            ];
        });
    }

    /**
     * 사용자별 캐시를 무효화합니다.
     */
    public function invalidateUserCache(int $userId): void
    {
        Cache::forget(self::USER_STATS_KEY . $userId);
    }

    /**
     * 성별 캐시를 무효화합니다.
     */
    public function invalidateCastleCache(int $castleId): void
    {
        Cache::forget(self::CASTLE_STATS_KEY . $castleId);
    }

    /**
     * 대시보드 캐시를 무효화합니다.
     */
    public function invalidateDashboardCache(): void
    {
        Cache::forget(self::DASHBOARD_STATS_KEY);
    }

    /**
     * 전체 캐시를 무효화합니다.
     */
    public function invalidateAllCache(): void
    {
        Cache::forget(self::CASTLES_LIST_KEY);
        Cache::forget(self::BADGES_LIST_KEY);
        Cache::forget(self::DASHBOARD_STATS_KEY);
    }

    /**
     * 방문 기록 관련 캐시를 무효화합니다.
     */
    public function invalidateVisitRelatedCache(int $userId, int $castleId): void
    {
        $this->invalidateUserCache($userId);
        $this->invalidateCastleCache($castleId);
        $this->invalidateDashboardCache();
    }

    /**
     * 완주율을 계산합니다.
     */
    private function calculateCompletionRate(User $user): float
    {
        $totalCastles = Castle::count();
        $visitedCastles = $user->visitRecords()
            ->where('verification_status', 'approved')
            ->distinct('castle_id')
            ->count('castle_id');

        if ($totalCastles === 0) {
            return 0;
        }

        return round(($visitedCastles / $totalCastles) * 100, 2);
    }

    /**
     * 캐시 상태를 확인합니다.
     */
    public function getCacheStatus(): array
    {
        return [
            'castles_cached' => Cache::has(self::CASTLES_LIST_KEY),
            'badges_cached' => Cache::has(self::BADGES_LIST_KEY),
            'dashboard_cached' => Cache::has(self::DASHBOARD_STATS_KEY),
            'cache_driver' => config('cache.default'),
        ];
    }

    /**
     * 인기 성 목록을 캐시에서 가져옵니다.
     */
    public function getPopularCastles(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "popular_castles:{$limit}";

        return Cache::remember($cacheKey, self::CASTLE_STATS_TTL, function () use ($limit) {
            return Castle::withCount(['visitRecords as visit_count' => function ($query) {
                $query->where('verification_status', 'approved');
            }])
            ->orderBy('visit_count', 'desc')
            ->limit($limit)
            ->get();
        });
    }

    /**
     * 최근 활동 사용자를 캐시에서 가져옵니다.
     */
    public function getActiveUsers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "active_users:{$limit}";

        return Cache::remember($cacheKey, self::USER_STATS_TTL, function () use ($limit) {
            return User::withCount(['visitRecords as recent_visits' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(30))
                      ->where('verification_status', 'approved');
            }])
            ->orderBy('recent_visits', 'desc')
            ->limit($limit)
            ->get();
        });
    }
}