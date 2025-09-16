<?php

namespace App\Http\Controllers\front;

use App\Models\Board;
use App\Models\BoardPost;
use App\Models\SystemSetting;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 공지사항 게시판 찾기
        $noticeBoard = Board::where('board_code', 'notice')
            ->where('is_active', true)
            ->first();

        // 공지사항 가져오기
        $notices = collect();
        if ($noticeBoard) {
            $notices = BoardPost::where('board_id', $noticeBoard->id)
                ->where('is_notice', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // 최근 게시글 (전체 게시판에서)
        $recentPosts = BoardPost::with('board')
            ->whereHas('board', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_notice', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 활성 게시판 목록
        $boards = Board::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('board_name')
            ->get();

        // 사이트 설정
        $siteSettings = [
            'site_name' => SystemSetting::get('site_name', config('app.name')),
            'site_description' => SystemSetting::get('site_description', '웹사이트 설명'),
        ];

        // 메인 배너 (활성 + 기간 조건 충족)
        $banners = Banner::active()->get();

        return view('front.home', compact('notices', 'recentPosts', 'boards', 'siteSettings', 'banners'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        // 게시글 검색
        $posts = BoardPost::with('board')
            ->whereHas('board', function($q) {
                $q->where('is_active', true);
            })
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('author_name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('front.search', compact('posts', 'query'));
    }
}
