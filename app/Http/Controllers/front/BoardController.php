<?php

namespace App\Http\Controllers\front;

use App\Models\Board;
use App\Models\BoardFile;
use App\Models\BoardPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    public function index(Request $request, $board_code)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        $query = BoardPost::where('board_id', $board->id);

        // 검색
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        // 공지사항과 일반 게시글 분리
        $notices = $query->clone()->where('is_notice', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $posts = $query->where('is_notice', false)
            ->orderBy('created_at', 'desc')
            ->paginate(\App\Models\SystemSetting::get('posts_per_page', 10));

        return view('front.board.index', compact('board', 'notices', 'posts'));
    }

    public function show($board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        // 조회수 증가
        $post->incrementViewCount();

        // 댓글 로드 (대댓글 포함)
        $comments = Comment::where('post_id', $post->id)
            ->with('replies')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get();

        // 이전/다음 게시글
        $prevPost = BoardPost::where('board_id', $board->id)
            ->where('id', '<', $post->id)
            ->where('is_notice', false)
            ->orderBy('id', 'desc')
            ->first();

        $nextPost = BoardPost::where('board_id', $board->id)
            ->where('id', '>', $post->id)
            ->where('is_notice', false)
            ->orderBy('id', 'asc')
            ->first();

        return view('front.board.show', compact('board', 'post', 'comments', 'prevPost', 'nextPost'));
    }

    public function create($board_code)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        // 사용자 글등록이 허용되지 않은 경우 접근 차단
        if (!$board->allow_user_write) {
            abort(403, '이 게시판에는 글을 작성할 수 없습니다.');
        }

        // 기능설정: 사용자 글등록 허용 시에도 로그인한 회원만 글쓰기 가능
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', '로그인한 회원만 글을 작성할 수 있습니다.');
        }

        return view('front.board.create', compact('board'));
    }

    public function store(Request $request, $board_code)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        // 사용자 글등록이 허용되지 않은 경우 접근 차단
        if (!$board->allow_user_write) {
            abort(403, '이 게시판에는 글을 작성할 수 없습니다.');
        }

        // 글쓰기는 로그인 회원만 가능 (요청이 비로그인 상태로 오면 로그인 페이지로 이동)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', '로그인한 회원만 글을 작성할 수 있습니다.');
        }

        // 검증 규칙 (로그인 사용자 전용)
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'files.*' => 'file|max:' . ($board->max_file_size * 1024),
        ]);

        // 데이터 저장 (로그인 사용자)
        $post = BoardPost::create([
            'board_id' => $board->id,
            'title' => $request->title,
            'content' => $request->get('content'),
            'author_name' => auth()->user()->name,
            'author_email' => auth()->user()->email,
            'user_id' => auth()->id(),
            'is_secret' => $request->boolean('is_secret'),
            'ip_address' => $request->ip(),
        ]);

        // 파일 업로드 처리
        if ($board->use_file_upload && $request->hasFile('files')) {
            $this->handleFileUploads($request, $post, $board);
        }

        return redirect()->route('board.show', [$board_code, $post])
            ->with('success', '게시글이 등록되었습니다.');
    }

    public function edit($board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        return view('front.board.edit', compact('board', 'post'));
    }

    public function update(Request $request, $board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        // 비밀번호 확인
        if (!password_verify($request->password, $post->password)) {
            return back()->withErrors(['password' => '비밀번호가 일치하지 않습니다.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_name' => 'required|string|max:100',
            'author_email' => 'nullable|email|max:255',
            'password' => 'required|string',
            'files.*' => 'file|max:' . ($board->max_file_size * 1024),
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->get('content'),
            'author_name' => $request->author_name,
            'author_email' => $request->author_email,
            'is_secret' => $request->boolean('is_secret'),
        ]);

        // 파일 업로드 처리
        if ($board->use_file_upload && $request->hasFile('files')) {
            $this->handleFileUploads($request, $post, $board);
        }

        return redirect()->route('board.show', [$board_code, $post])
            ->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy(Request $request, $board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        // 비밀번호 확인
        if (!password_verify($request->password, $post->password)) {
            return back()->withErrors(['password' => '비밀번호가 일치하지 않습니다.']);
        }

        // 파일 삭제
        foreach ($post->files as $file) {
            Storage::delete($file->file_path);
        }

        $post->delete();

        return redirect()->route('board.index', $board_code)
            ->with('success', '게시글이 삭제되었습니다.');
    }

    public function like(Request $request, $board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        // 로그인한 사용자만 가능
        if (!$request->user()) {
            return response()->json([
                'message' => '로그인이 필요합니다.'
            ], 401);
        }

        $user = $request->user();

        // 이미 좋아요 했는지 확인
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

        $like = \App\Models\PostLike::firstOrCreate([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        if ($like->wasRecentlyCreated) {
            $post->incrementLikeCount();
            $post->refresh();
        }

        return response()->json([
            'like_count' => $post->like_count,
            'liked' => true
        ]);
    }

    private function handleFileUploads(Request $request, BoardPost $post, Board $board)
    {
        $files = $request->file('files');
        $uploadedCount = 0;

        foreach ($files as $file) {
            if ($uploadedCount >= $board->max_file_count) {
                break;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $storedName = Str::random(40) . '.' . $extension;
            $filePath = 'uploads/' . $board->upload_folder . '/' . $storedName;

            $file->storeAs('uploads/' . $board->upload_folder, $storedName, 'public');

            BoardFile::create([
                'post_id' => $post->id,
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $uploadedCount++;
        }
    }
}
