<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Castle;
use App\Models\User;
use App\Models\Badge;

class CacheService
{
    // 캐시 키 상수
    const CASTLES_LIST_KEY = 'castles:list';
    const BADGES_LIST_KEY = 'badges:list';
    const USER_STATS_KEY = 'user:stats:';
    const CASTLE_STATS_KEY = 'castle:stats:';
    const DASHBOARD_STATS_KEY = 'dashboard:stats';

    // 캐시 유효 시간 (초)
    const CASTLES_TTL = 3600; // 1시간
    const BADGES_TTL = 3600; // 1시간
    const USER_STATS_TTL = 600; // 10분
    const CASTLE_STATS_TTL = 1800; // 30분
    const DASHBOARD_TTL = 300; // 5분

    /**
     * 모든 성 목록을 캐시에서 가져옵니다.
     */
    public function getCastlesList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::CASTLES_LIST_KEY, self::CASTLES_TTL, function () {
            return Castle::orderBy('name_korean')->get();
        });
    }

    /**
     * 모든 배지 목록을 캐시에서 가져옵니다.
     */
    public function getBadgesList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::BADGES_LIST_KEY, self::BADGES_TTL, function () {
            return Badge::orderBy('required_visits')->get();
        });
    }

    /**
     * 사용자 통계를 캐시에서 가져옵니다.
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
                'friends_count' => $user->friends()->count(),
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