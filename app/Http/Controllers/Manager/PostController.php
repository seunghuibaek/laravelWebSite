<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Board;
use App\Models\BoardFile;
use App\Models\BoardPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request, $board_code)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        $query = BoardPost::where('board_id', $board->id)->with(['files']);

        // 검색 필터
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_notice')) {
            $query->where('is_notice', $request->boolean('is_notice'));
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

        $posts = $query->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.posts.index', compact('board', 'posts'));
    }

    public function show($board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        $post->load(['files', 'comments.replies']);

        return view('manager.posts.show', compact('board', 'post'));
    }

    public function create($board_code)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        return view('manager.posts.create', compact('board'));
    }

    public function store(Request $request, $board_code)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_name' => 'required|string|max:100',
            'author_email' => 'nullable|email|max:255',
            'files.*' => 'file|max:' . ($board->max_file_size * 1024),
        ]);

        $post = BoardPost::create([
            'board_id' => $board->id,
            'title' => $request->title,
            'content' => $request->get('content'),
            'author_name' => $request->author_name,
            'author_email' => $request->author_email,
            'password' => bcrypt('admin123'), // 관리자 게시글 기본 비밀번호
            'is_notice' => $request->boolean('is_notice'),
            'is_secret' => $request->boolean('is_secret'),
            'ip_address' => $request->ip(),
        ]);

        // 파일 업로드 처리
        if ($board->use_file_upload && $request->hasFile('files')) {
            $this->handleFileUploads($request, $post, $board);
        }

        return redirect()->route('manager.posts.index', $board_code)
            ->with('success', '게시글이 등록되었습니다.');
    }

    public function edit($board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        return view('manager.posts.edit', compact('board', 'post'));
    }

    public function update(Request $request, $board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_name' => 'required|string|max:100',
            'author_email' => 'nullable|email|max:255',
            'files.*' => 'file|max:' . ($board->max_file_size * 1024),
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->get('content'),
            'author_name' => $request->author_name,
            'author_email' => $request->author_email,
            'is_notice' => $request->boolean('is_notice'),
            'is_secret' => $request->boolean('is_secret'),
        ]);

        // 파일 업로드 처리
        if ($board->use_file_upload && $request->hasFile('files')) {
            $this->handleFileUploads($request, $post, $board);
        }

        return redirect()->route('manager.posts.show', [$board_code, $post])
            ->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy($board_code, BoardPost $post)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        if ($post->board_id !== $board->id) {
            abort(404);
        }

        // 파일 삭제
        foreach ($post->files as $file) {
            Storage::delete($file->file_path);
        }

        $post->delete();

        return redirect()->route('manager.posts.index', $board_code)
            ->with('success', '게시글이 삭제되었습니다.');
    }

    public function bulkDelete(Request $request, $board_code)
    {
        $board = Board::where('board_code', $board_code)->firstOrFail();

        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $posts = BoardPost::where('board_id', $board->id)->whereIn('id', $ids)->get();

        foreach ($posts as $post) {
            // 파일 삭제
            foreach ($post->files as $file) {
                Storage::delete($file->file_path);
            }
            $post->delete();
        }

        return redirect()->route('manager.posts.index', $board_code)
            ->with('success', count($posts) . '개의 게시글이 삭제되었습니다.');
    }

//    private function handleFileUploads(Request $request, BoardPost $post, Board $board)
//    {
//        $files = $request->file('files');
//        $uploadedCount = 0;
//
//        foreach ($files as $file) {
//            if ($uploadedCount >= $board->max_file_count) {
//                break;
//            }
//
//            $originalName = $file->getClientOriginalName();
//            $extension = $file->getClientOriginalExtension();
//            $storedName = Str::random(40) . '.' . $extension;
//            $filePath = 'uploads/' . $board->upload_folder . '/' . $storedName;
//
//            $file->storeAs('uploads/' . $board->upload_folder, $storedName, 'public');
//
//            BoardFile::create([
//                'post_id' => $post->id,
//                'original_name' => $originalName,
//                'stored_name' => $storedName,
//                'file_path' => $filePath,
//                'mime_type' => $file->getMimeType(),
//                'file_size' => $file->getSize(),
//            ]);
//
//            $uploadedCount++;
//        }
//    }
//    public function destroy(Request $request, $board_code, BoardPost $post)
//    {
//        $post->delete();
//
//        return redirect()->route('manager.posts.index', $board_code)->with('success', '게시글이 삭제되었습니다.');
//    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('file')->store('uploads/posts', 'public');

        return response()->json(['link' => Storage::url($path)]);
    }
}
