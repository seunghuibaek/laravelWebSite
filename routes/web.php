<?php

use App\Http\Controllers\front\AuthController;
use App\Http\Controllers\front\BoardController;
use App\Http\Controllers\front\HomeController;
use App\Http\Controllers\front\InquiryController;
use Illuminate\Support\Facades\Route;

// 관리자 라우트 포함
require __DIR__.'/manager.php';

// 프론트 페이지 라우트
Route::get('/', [HomeController::class, 'index'])->name('home');

// 인증 라우트
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 로그인 필요한 라우트
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// 게시판 라우트
Route::prefix('board/{board_code}')->name('board.')->group(function () {
    Route::get('/', [BoardController::class, 'index'])->name('index');
    Route::get('/create', [BoardController::class, 'create'])->name('create');
    Route::post('/', [BoardController::class, 'store'])->name('store');
    Route::get('/{post}', [BoardController::class, 'show'])->name('show');
    Route::get('/{post}/edit', [BoardController::class, 'edit'])->name('edit');
    Route::put('/{post}', [BoardController::class, 'update'])->name('update');
    Route::delete('/{post}', [BoardController::class, 'destroy'])->name('destroy');
    Route::post('/{post}/like', [BoardController::class, 'like'])->middleware('auth')->name('like');
});

// 호환용 좋아요 라우트 (/posts/{post}/like)
Route::post('/posts/{post}/like', function (\Illuminate\Http\Request $request, \App\Models\BoardPost $post) {
    // 활성 게시판에 속한 게시글만 좋아요 허용
    $board = \App\Models\Board::where('id', $post->board_id)
        ->where('is_active', true)
        ->first();
    if (!$board) {
        abort(404);
    }

    // 로그인 필요
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => '로그인이 필요합니다.'], 401);
    }

    $already = \App\Models\PostLike::where('post_id', $post->id)
        ->where('user_id', $user->id)
        ->exists();
    if ($already) {
        return response()->json([
            'like_count' => $post->like_count,
            'liked' => true,
            'message' => '이미 좋아요를 누르셨습니다.'
        ], 200);
    }

    $like = \App\Models\PostLike::firstOrCreate(['post_id' => $post->id, 'user_id' => $user->id]);
    if ($like->wasRecentlyCreated) {
        $post->incrementLikeCount();
        $post->refresh();
    }

    return response()->json([
        'like_count' => $post->like_count,
        'liked' => true,
    ]);
})->middleware('auth')->name('posts.like');

// 문의하기 라우트
Route::prefix('inquiry')->name('inquiry.')->group(function () {
    Route::get('/', [InquiryController::class, 'create'])->name('create');
    Route::post('/', [InquiryController::class, 'store'])->name('store');
});

// 검색 라우트
Route::get('/search', [HomeController::class, 'search'])->name('search');

// 댓글 라우트
Route::post('/comments', [\App\Http\Controllers\front\CommentController::class, 'store'])->middleware('auth')->name('comments.store');
Route::delete('/comments/{comment}', [\App\Http\Controllers\front\CommentController::class, 'destroy'])->name('comments.destroy');
