<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardPost;
use App\Models\BoardFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardApiController extends Controller
{
    // 게시판 JSON 글등록
    public function store(Request $request, string $board_code)
    {
        $board = Board::where('board_code', $board_code)
            ->where('is_active', true)
            ->first();

        if (!$board) {
            return response()->json(['message' => '존재하지 않거나 비활성화된 게시판입니다.'], 404);
        }

        if (!$board->allow_user_write) {
            return response()->json(['message' => '이 게시판에는 글을 작성할 수 없습니다.'], 403);
        }

        // 로그인 사용자만 허용 (Sanctum 토큰 인증 전제)
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => '인증이 필요합니다.'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_secret' => 'sometimes|boolean',
            'files.*' => 'file|max:' . ($board->max_file_size * 1024),
        ]);

        $post = BoardPost::create([
            'board_id' => $board->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_name' => $user->name,
            'author_email' => $user->email,
            'user_id' => $user->id,
            'is_secret' => (bool)($validated['is_secret'] ?? false),
            'ip_address' => $request->ip(),
        ]);

        if ($board->use_file_upload && $request->hasFile('files')) {
            $this->handleFileUploads($request, $post, $board);
        }

        return response()->json([
            'message' => '게시글이 등록되었습니다.',
            'post' => [
                'id' => $post->id,
                'board_code' => $board->board_code,
                'title' => $post->title,
                'content' => $post->content,
                'author_name' => $post->author_name,
                'created_at' => $post->created_at,
                'url' => route('board.show', [$board->board_code, $post->id]),
            ],
        ], 201);
    }

    private function handleFileUploads(Request $request, BoardPost $post, Board $board): void
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
