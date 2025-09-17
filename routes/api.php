<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BoardApiController;
use App\Http\Controllers\API\AuthController;

// Public: login to get a Sanctum token
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    // 자유게시판 글등록 (JSON)
    Route::post('/board/{board_code}/posts', [BoardApiController::class, 'store'])
        ->name('api.board.posts.store');

    // Optional: logout (revoke current token)
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});
