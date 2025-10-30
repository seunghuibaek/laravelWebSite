<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Board;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['post.board']);

        // 검색 필터
        if ($request->filled('board_id')) {
            $query->whereHas('post', function($q) use ($request) {
                $q->where('board_id', $request->board_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%$search%")
                  ->orWhere('author_name', 'like', "%$search%")
                  ->orWhere('author_email', 'like', "%$search%");
            });
        }

        if ($request->filled('is_secret')) {
            $query->where('is_secret', $request->boolean('is_secret'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);
        $boards = Board::active()->ordered()->get();

        return view('manager.comments.index', compact('comments', 'boards'));
    }

    public function destroy(Comment $comment)
    {
        // 대댓글이 있는 경우 삭제할 수 없음
        if ($comment->replies()->count() > 0) {
            return redirect()->route('manager.comments.index')
                ->with('error', '대댓글이 있는 댓글은 삭제할 수 없습니다.');
        }

        $comment->delete();

        return redirect()->route('manager.comments.index')
            ->with('success', '댓글이 성공적으로 삭제되었습니다.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);

        // 대댓글이 있는 댓글은 제외
        $deletableComments = Comment::whereIn('id', $ids)
            ->whereDoesntHave('replies')
            ->get();

        $deletedCount = $deletableComments->count();
        $skippedCount = count($ids) - $deletedCount;

        Comment::whereIn('id', $deletableComments->pluck('id'))->delete();

        $message = "{$deletedCount}개의 댓글이 삭제되었습니다.";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount}개는 대댓글이 있어 삭제되지 않았습니다.)";
        }

        return redirect()->route('manager.comments.index')
            ->with('success', $message);
    }
}
