<?php

use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\BoardController;
use App\Http\Controllers\Manager\CommentController;
use App\Http\Controllers\Manager\InquiryController;
use App\Http\Controllers\Manager\SystemSettingController;
use App\Http\Controllers\Manager\StatisticsController;
use App\Http\Controllers\Manager\BannerController;
use Illuminate\Support\Facades\Route;

// 관리자 인증 관련 라우트
Route::prefix('manager')->name('manager.')->group(function () {
    Route::middleware('guest:manager')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:manager', App\Http\Middleware\ManagerAuth::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // 대시보드
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index']);

        // 관리자 관리
        Route::resource('managers', ManagerController::class);

        // 게시판 관리
        Route::resource('boards', BoardController::class);

        // 게시글 관리
        Route::prefix('posts/{board_code}')->name('posts.')->group(function () {
            Route::get('/', [App\Http\Controllers\Manager\PostController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Manager\PostController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Manager\PostController::class, 'store'])->name('store');
            Route::get('/{post}', [App\Http\Controllers\Manager\PostController::class, 'show'])->name('show');
            Route::get('/{post}/edit', [App\Http\Controllers\Manager\PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [App\Http\Controllers\Manager\PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [App\Http\Controllers\Manager\PostController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [App\Http\Controllers\Manager\PostController::class, 'bulkDelete'])->name('bulk-delete');
        });

        // 댓글 관리
        Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('comments/bulk-delete', [CommentController::class, 'bulkDelete'])->name('comments.bulk-delete');

        // 문의하기 관리
        Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('inquiries/{inquiry}/reply', [InquiryController::class, 'reply'])->name('inquiries.reply');
        Route::post('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete'])->name('inquiries.bulk-delete');
        Route::post('inquiries/bulk-status', [InquiryController::class, 'bulkUpdateStatus'])->name('inquiries.bulk-status');

        // 시스템 설정
        Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SystemSettingController::class, 'update'])->name('settings.update');
        Route::get('settings/create', [SystemSettingController::class, 'create'])->name('settings.create');
        Route::post('settings/store', [SystemSettingController::class, 'store'])->name('settings.store');
        Route::delete('settings/{setting}', [SystemSettingController::class, 'destroy'])->name('settings.destroy');

        // 배너 관리
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::post('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

        // 통계
        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    });
});
