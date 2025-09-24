<?php

use App\Http\Controllers\CastleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitRecordController;
use Illuminate\Support\Facades\Route;

// 홈페이지 - 바로 로그인 페이지로 리다이렉트
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// 공개 API (인증 불필요)
Route::prefix('api/public')->group(function () {
    Route::get('/castles', [CastleController::class, 'index'])->name('api.castles.index');
    Route::get('/castles/{castle}', [CastleController::class, 'show'])->name('api.castles.show');
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'message' => '24명성 인증 앱 API가 정상 작동 중입니다.',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString()
        ]);
    })->name('api.status');
});

// 모니터링 엔드포인트 (인증 불필요)
Route::get('/health', [App\Http\Controllers\HealthController::class, 'health'])->name('health');
Route::get('/metrics', [App\Http\Controllers\HealthController::class, 'metrics'])->name('metrics');

// 인증이 필요한 라우트들
Route::middleware(['auth'])->group(function () {
    // 대시보드
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 성 관련 라우트
    Route::get('/castles', [CastleController::class, 'index'])->name('castles.index');
    Route::get('/castles/{castle}', [CastleController::class, 'show'])->name('castles.show');
    Route::get('/map', [CastleController::class, 'map'])->name('castles.map');

    // 방문 기록 관련 라우트
    Route::get('/visit-records', [VisitRecordController::class, 'index'])->name('visit-records.index');
    Route::get('/castles/{castle}/visit', [VisitRecordController::class, 'create'])->name('visit-records.create');
    Route::post('/castles/{castle}/visit', [VisitRecordController::class, 'store'])->name('visit-records.store');
    Route::get('/visit-records/{visitRecord}', [VisitRecordController::class, 'show'])->name('visit-records.show');

});

// 관리자 전용 라우트들
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/visit-records', [App\Http\Controllers\AdminController::class, 'visitRecords'])->name('visit-records');
    Route::post('/visit-records/{visitRecord}/approve', [App\Http\Controllers\AdminController::class, 'approveVisitRecord'])->name('visit-records.approve');
    Route::post('/visit-records/{visitRecord}/reject', [App\Http\Controllers\AdminController::class, 'rejectVisitRecord'])->name('visit-records.reject');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [App\Http\Controllers\AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/statistics', [App\Http\Controllers\AdminController::class, 'statistics'])->name('statistics');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/security', [App\Http\Controllers\AdminController::class, 'security'])->name('security');
    Route::post('/security/block-ip', [App\Http\Controllers\AdminController::class, 'blockIP'])->name('security.block-ip');
    Route::post('/security/unblock-ip', [App\Http\Controllers\AdminController::class, 'unblockIP'])->name('security.unblock-ip');
});

// 기본 인증 라우트들 (Laravel UI 또는 Breeze가 없으므로 수동으로 추가)
require __DIR__.'/auth.php';
