<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VisitRecord;
use App\Models\Castle;
use App\Models\Badge;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 전체 통계
        $totalUsers = User::count();
        $totalVisitRecords = VisitRecord::count();
        $pendingRecords = VisitRecord::where('verification_status', 'pending')->count();
        $approvedRecords = VisitRecord::where('verification_status', 'approved')->count();
        $rejectedRecords = VisitRecord::where('verification_status', 'rejected')->count();

        // 최근 방문 기록 (검토 대기 중)
        $recentPendingRecords = VisitRecord::with(['user', 'castle'])
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 최근 가입한 사용자
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 성별 방문 통계 (상위 10개)
        $castleStats = DB::table('visit_records')
            ->join('castles', 'visit_records.castle_id', '=', 'castles.id')
            ->select('castles.name_korean', 'castles.name', DB::raw('count(*) as visit_count'))
            ->groupBy('castles.id', 'castles.name_korean', 'castles.name')
            ->orderBy('visit_count', 'desc')
            ->limit(10)
            ->get();

        // 월별 방문 기록 통계 (최근 6개월)
        $monthlyStats = DB::table('visit_records')
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalVisitRecords',
            'pendingRecords',
            'approvedRecords',
            'rejectedRecords',
            'recentPendingRecords',
            'recentUsers',
            'castleStats',
            'monthlyStats'
        ));
    }

    public function visitRecords(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = VisitRecord::with(['user', 'castle'])
            ->where('verification_status', $status);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('castle', function ($q) use ($search) {
                $q->where('name_korean', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $visitRecords = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.visit-records', compact('visitRecords', 'status', 'search'));
    }

    public function approveVisitRecord(VisitRecord $visitRecord)
    {
        $visitRecord->update(['verification_status' => 'approved']);

        // 캐시 무효화
        $cacheService = app(\App\Services\CacheService::class);
        $cacheService->invalidateVisitRelatedCache($visitRecord->user_id, $visitRecord->castle_id);

        // 배지 확인 및 부여
        $visitRecord->user->checkAndAwardBadges();

        // TODO: 승인 알림 생성 시스템 구현 예정
        // $notificationService = app(\App\Services\NotificationService::class);
        // $notificationService->createVisitApprovedNotification($visitRecord);

        return redirect()->back()->with('success',
            $visitRecord->user->name . '님의 ' . $visitRecord->castle->name_korean . ' 방문이 승인되었습니다.');
    }

    public function rejectVisitRecord(VisitRecord $visitRecord)
    {
        $visitRecord->update(['verification_status' => 'rejected']);

        // 캐시 무효화
        $cacheService = app(\App\Services\CacheService::class);
        $cacheService->invalidateVisitRelatedCache($visitRecord->user_id, $visitRecord->castle_id);

        // TODO: 거부 알림 생성 시스템 구현 예정
        // $notificationService = app(\App\Services\NotificationService::class);
        // $notificationService->createVisitRejectedNotification($visitRecord);

        return redirect()->back()->with('success',
            $visitRecord->user->name . '님의 ' . $visitRecord->castle->name_korean . ' 방문이 거부되었습니다.');
    }

    public function users(Request $request)
    {
        $search = $request->get('search');

        $query = User::withCount(['visitRecords', 'badges']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users', 'search'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->isAdmin()) {
            $user->removeAdmin();
            $message = $user->name . '님의 관리자 권한이 해제되었습니다.';
        } else {
            $user->makeAdmin();
            $message = $user->name . '님이 관리자로 승격되었습니다.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function statistics()
    {
        // 상세 통계 페이지
        $totalStats = [
            'users' => User::count(),
            'visit_records' => VisitRecord::count(),
            'castles' => Castle::count(),
            'badges' => Badge::count(),
        ];

        // 상태별 방문 기록
        $statusStats = [
            'pending' => VisitRecord::where('verification_status', 'pending')->count(),
            'approved' => VisitRecord::where('verification_status', 'approved')->count(),
            'rejected' => VisitRecord::where('verification_status', 'rejected')->count(),
        ];

        // 인기 성 TOP 10
        $popularCastles = DB::table('visit_records')
            ->join('castles', 'visit_records.castle_id', '=', 'castles.id')
            ->select('castles.name_korean', 'castles.name', DB::raw('count(*) as visit_count'))
            ->groupBy('castles.id', 'castles.name_korean', 'castles.name')
            ->orderBy('visit_count', 'desc')
            ->limit(10)
            ->get();

        // 활성 사용자 TOP 10
        $activeUsers = User::withCount('visitRecords')
            ->orderBy('visit_records_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.statistics', compact(
            'totalStats',
            'statusStats',
            'popularCastles',
            'activeUsers'
        ));
    }

    /**
     * 시스템 설정 페이지
     */
    public function settings()
    {
        $registrationEnabled = SystemSetting::isRegistrationEnabled();

        return view('admin.settings', compact('registrationEnabled'));
    }

    /**
     * 시스템 설정 업데이트
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'registration_enabled' => 'boolean',
        ]);

        // 회원가입 허용/차단 설정
        SystemSetting::setRegistrationEnabled($request->boolean('registration_enabled'));

        return redirect()->back()->with('success', '시스템 설정이 업데이트되었습니다.');
    }

    /**
     * 보안 모니터링 페이지
     */
    public function security()
    {
        // 차단된 IP 목록
        $blockedIPs = collect();
        // SQLite 캐시 환경과 호환되는 방식으로 차단된 IP 조회
        $cachedIPs = DB::table('cache')
            ->where('key', 'LIKE', '%blocked_ip:%')
            ->get();

        foreach ($cachedIPs as $cacheItem) {
            try {
                $data = unserialize($cacheItem->value);
                $ip = str_replace('blocked_ip:', '', $cacheItem->key);
                $blockedIPs->push([
                    'ip' => $ip,
                    'reason' => $data['reason'] ?? 'Unknown',
                    'blocked_at' => $data['blocked_at'] ?? null,
                    'expires_at' => $data['expires_at'] ?? null,
                ]);
            } catch (\Exception $e) {
                // 역직렬화 실패 시 로그 남기고 건너뛰기
                Log::warning('Failed to unserialize blocked IP data', [
                    'key' => $cacheItem->key,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // 최근 로그인 실패 시도 (로그 파일에서 읽기)
        $recentFailedAttempts = $this->getRecentFailedAttempts();

        return view('admin.security', compact('blockedIPs', 'recentFailedAttempts'));
    }

    /**
     * IP 차단 해제
     */
    public function unblockIP(Request $request)
    {
        $ip = $request->input('ip');
        Cache::forget("blocked_ip:{$ip}");

        Log::info('관리자가 IP 차단 해제', [
            'admin' => auth()->user()->email,
            'unblocked_ip' => $ip
        ]);

        return redirect()->back()->with('success', "IP {$ip}의 차단이 해제되었습니다.");
    }

    /**
     * IP 수동 차단
     */
    public function blockIP(Request $request)
    {
        $request->validate([
            'ip' => ['required', 'ip'],
            'reason' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:1', 'max:1440'] // 최대 24시간
        ]);

        $ip = $request->input('ip');
        $reason = $request->input('reason');
        $minutes = $request->input('duration');

        Cache::put("blocked_ip:{$ip}", [
            'reason' => "관리자 차단: {$reason}",
            'blocked_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'blocked_by' => auth()->user()->email
        ], now()->addMinutes($minutes));

        Log::warning('관리자가 IP 수동 차단', [
            'admin' => auth()->user()->email,
            'blocked_ip' => $ip,
            'reason' => $reason,
            'duration_minutes' => $minutes
        ]);

        return redirect()->back()->with('success', "IP {$ip}가 {$minutes}분간 차단되었습니다.");
    }

    /**
     * 최근 로그인 실패 시도 조회
     */
    private function getRecentFailedAttempts(): array
    {
        $attempts = [];
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            $lines = array_slice(file($logPath), -100); // 최근 100줄

            foreach (array_reverse($lines) as $line) {
                if (strpos($line, '로그인 실패') !== false) {
                    $attempts[] = $line;
                    if (count($attempts) >= 20) break; // 최대 20개
                }
            }
        }

        return $attempts;
    }
}