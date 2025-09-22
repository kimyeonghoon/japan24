<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use App\Models\Badge;
use App\Models\VisitRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $totalCastles = Castle::count();
        $visitedCastles = $user->visitRecords()->where('verification_status', 'approved')->count();
        $pendingVisits = $user->visitRecords()->where('verification_status', 'pending')->count();
        $userBadges = $user->badges()->count();
        $totalBadges = Badge::count();

        $recentVisits = $user->visitRecords()
            ->with('castle')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $progressPercentage = $totalCastles > 0 ? round(($visitedCastles / $totalCastles) * 100, 1) : 0;

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