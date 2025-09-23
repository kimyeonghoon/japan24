<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use App\Models\Badge;
use App\Models\VisitRecord;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 캐시된 통계 데이터 가져오기
        $userStats = $this->cacheService->getUserStats($user->id);
        $castles = $this->cacheService->getCastlesList();
        $badges = $this->cacheService->getBadgesList();

        $totalCastles = $castles->count();
        $visitedCastles = $userStats['approved_visits'] ?? 0;
        $pendingVisits = $userStats['pending_visits'] ?? 0;
        $userBadges = $userStats['badge_count'] ?? 0;
        $totalBadges = $badges->count();

        // 최근 방문 기록은 실시간으로 가져오기 (사용자별 개인 데이터)
        $recentVisits = $user->visitRecords()
            ->with('castle')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $progressPercentage = $userStats['completion_rate'] ?? 0;

        return view('dashboard', compact(
            'totalCastles',
            'visitedCastles',
            'pendingVisits',
            'userBadges',
            'totalBadges',
            'recentVisits',
            'progressPercentage'
        ));
    }
}