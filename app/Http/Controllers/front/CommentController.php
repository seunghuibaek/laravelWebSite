<?php

namespace App\Http\Controllers\front;

use App\Models\BoardPost;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // 로그인 사용자만 허용
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => '로그인이 필요합니다.'], 401);
            }
            return back()->withErrors(['error' => '로그인이 필요합니다.'])->withInput();
        }

        $request->validate([
            'post_id' => 'required|exists:board_posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string',
        ]);

        $post = BoardPost::findOrFail($request->post_id);

        // 게시판에서 댓글 사용이 허용되는지 확인
        if (!$post->board->use_comment) {
            return back()->withErrors(['error' => '이 게시판에서는 댓글을 사용할 수 없습니다.']);
        }

        // 로그인 사용자 정보 사용
        $user = $request->user();

        Comment::create([
            'post_id' => $request->input('post_id'),
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content'),
            'author_name' => $user->name ?? '회원',
            'author_email' => $user->email ?? null,
            'password' => bcrypt(uniqid('cmt_', true)),
            'is_secret' => $request->boolean('is_secret'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', '댓글이 등록되었습니다.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        // 비밀번호 확인
        if (!password_verify($request->password, $comment->password)) {
            return back()->withErrors(['password' => '비밀번호가 일치하지 않습니다.']);
        }

        // 대댓글이 있는 경우 삭제할 수 없음
        if ($comment->replies()->count() > 0) {
            return back()->withErrors(['error' => '대댓글이 있는 댓글은 삭제할 수 없습니다.']);
        }

        $comment->delete();

        return back()->with('success', '댓글이 삭제되었습니다.');
    }
}
