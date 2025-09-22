<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VisitRecord;
use App\Models\Castle;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
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

        // 배지 확인 및 부여
        $visitRecord->user->checkAndAwardBadges();

        return redirect()->back()->with('success',
            $visitRecord->user->name . '님의 ' . $visitRecord->castle->name_korean . ' 방문이 승인되었습니다.');
    }

    public function rejectVisitRecord(VisitRecord $visitRecord)
    {
        $visitRecord->update(['verification_status' => 'rejected']);

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
}