<?php

use App\Http\Controllers\CastleController;
use App\Http\Controllers\VisitRecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 인증이 필요한 API 라우트
Route::middleware('auth:sanctum')->group(function () {
    // 사용자 정보
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    // 방문 기록 API
    Route::apiResource('visit-records', VisitRecordController::class);

    // 사용자 대시보드 정보
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        $visitRecords = $user->visitRecords()->with('castle')->get();
        $badges = $user->userBadges()->with('badge')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'visit_records' => $visitRecords,
                'badges' => $badges,
                'statistics' => [
                    'total_visits' => $visitRecords->count(),
                    'approved_visits' => $visitRecords->where('verification_status', 'approved')->count(),
                    'pending_visits' => $visitRecords->where('verification_status', 'pending')->count(),
                    'badges_count' => $badges->count(),
                ]
            ]
        ]);
    });
});

// 공개 API 라우트 (인증 불필요)
Route::get('/castles', [CastleController::class, 'index']);
Route::get('/castles/{castle}', [CastleController::class, 'show']);
Route::get('/castles/map/data', [CastleController::class, 'map']);

// API 상태 확인
Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => '24명성 인증 앱 API가 정상 작동 중입니다.',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString()
    ]);
});