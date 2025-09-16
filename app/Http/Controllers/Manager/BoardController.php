<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::orderBy('sort_order')->orderBy('board_name')->paginate(15);

        return view('manager.boards.index', compact('boards'));
    }

    public function create()
    {
        return view('manager.boards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'board_code' => 'required|string|max:50|unique:boards|alpha_dash',
            'board_name' => 'required|string|max:255',
            'board_type' => 'required|in:normal,gallery',
            'upload_folder' => 'nullable|string|max:255|alpha_dash',
            'use_notice' => 'boolean',
            'use_file_upload' => 'boolean',
            'max_file_count' => 'required|integer|min:1|max:5',
            'use_editor' => 'boolean',
            'use_comment' => 'boolean',
            'max_file_size' => 'required|integer|min:1|max:102400', // 최대 100MB
            'memo' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data = $request->all();

        // 체크박스 값들을 boolean으로 변환
        $data['use_notice'] = $request->boolean('use_notice');
        $data['use_file_upload'] = $request->boolean('use_file_upload');
        $data['use_editor'] = $request->boolean('use_editor');
        $data['use_comment'] = $request->boolean('use_comment');
        $data['allow_user_write'] = $request->boolean('allow_user_write');
        $data['is_active'] = $request->boolean('is_active');

        // 업로드 폴더가 비어있으면 게시판 코드로 설정
        if (empty($data['upload_folder'])) {
            $data['upload_folder'] = $data['board_code'];
        }

        Board::create($data);

        return redirect()->route('manager.boards.index')
            ->with('success', '게시판이 성공적으로 생성되었습니다.');
    }

    public function show(Board $board)
    {
        $board->load(['posts' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('manager.boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        return view('manager.boards.edit', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        $request->validate([
            'board_code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('boards')->ignore($board->id)],
            'board_name' => 'required|string|max:255',
            'board_type' => 'required|in:normal,gallery',
            'upload_folder' => 'nullable|string|max:255|alpha_dash',
            'use_notice' => 'boolean',
            'use_file_upload' => 'boolean',
            'max_file_count' => 'required|integer|min:1|max:5',
            'use_editor' => 'boolean',
            'use_comment' => 'boolean',
            'max_file_size' => 'required|integer|min:1|max:102400',
            'memo' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data = $request->all();

        // 체크박스 값들을 boolean으로 변환
        $data['use_notice'] = $request->boolean('use_notice');
        $data['use_file_upload'] = $request->boolean('use_file_upload');
        $data['use_editor'] = $request->boolean('use_editor');
        $data['use_comment'] = $request->boolean('use_comment');
        $data['allow_user_write'] = $request->boolean('allow_user_write');
        $data['is_active'] = $request->boolean('is_active');

        // 업로드 폴더가 비어있으면 게시판 코드로 설정
        if (empty($data['upload_folder'])) {
            $data['upload_folder'] = $data['board_code'];
        }

        $board->update($data);

        return redirect()->route('manager.boards.index')
            ->with('success', '게시판이 성공적으로 수정되었습니다.');
    }

    public function destroy(Board $board)
    {
        // 게시글이 있는 게시판은 삭제할 수 없음
        if ($board->posts()->count() > 0) {
            return redirect()->route('manager.boards.index')
                ->with('error', '게시글이 있는 게시판은 삭제할 수 없습니다.');
        }

        $board->delete();

        return redirect()->route('manager.boards.index')
            ->with('success', '게시판이 성공적으로 삭제되었습니다.');
    }
}
