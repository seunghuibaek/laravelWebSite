<?php

use App\Http\Controllers\Manager\PostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BoardApiController;
use App\Http\Controllers\API\AuthController;

// Public: login to get a Sanctum token
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// 에디터 파일 업로드
Route::post('/upload-image', [PostController::class, 'uploadImage'])->name('posts.uploadImage');

Route::middleware('auth:sanctum')->group(function () {
    // 자유게시판 글등록 (JSON)
    Route::post('/board/{board_code}/posts', [BoardApiController::class, 'store'])
        ->name('api.board.posts.store');

    // Optional: logout (revoke current token)
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});

Route::post('/auth/token', function (\Illuminate\Http\Request $request) {
    $request->validate(['email'=>'required|email','password'=>'required']);
    $user = User::where('email',$request->email)->first();
    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['message'=>'Invalid credentials'], 401);
    }
    return response()->json(['token'=>$user->createToken('talend')->plainTextToken]);
});
