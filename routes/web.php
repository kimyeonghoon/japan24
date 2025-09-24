<?php

use App\Http\Controllers\CastleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitRecordController;
use Illuminate\Support\Facades\Route;

// 홈페이지
Route::get('/', function () {
    return response('<h1>24명성 인증 앱</h1><p>환영합니다!</p><a href="/login">로그인</a> | <a href="/register">회원가입</a>');
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
});

// 기본 인증 라우트들 (Laravel UI 또는 Breeze가 없으므로 수동으로 추가)
require __DIR__.'/auth.php';
