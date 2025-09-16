<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Board;
use App\Models\BoardPost;
use App\Models\Comment;
use App\Models\Inquiry;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7'); // 기본 7일
        $startDate = now()->subDays($period);

        // 기본 통계
        $stats = [
            'total_boards' => Board::count(),
            'active_boards' => Board::where('is_active', true)->count(),
            'total_posts' => BoardPost::count(),
            'total_comments' => Comment::count(),
            'total_inquiries' => Inquiry::count(),
            'pending_inquiries' => Inquiry::where('status', 'pending')->count(),
            'total_managers' => Manager::count(),
            'active_managers' => Manager::where('status', 'active')->count(),
        ];

        // 기간별 통계
        $periodStats = [
            'posts' => BoardPost::where('created_at', '>=', $startDate)->count(),
            'comments' => Comment::where('created_at', '>=', $startDate)->count(),
            'inquiries' => Inquiry::where('created_at', '>=', $startDate)->count(),
        ];

        // 일별 게시글 통계 (차트용)
        $dailyPosts = BoardPost::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 일별 댓글 통계
        $dailyComments = Comment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 게시판별 게시글 수
        $boardStats = Board::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();

        // 최근 활동
        $recentPosts = BoardPost::with('board')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentComments = Comment::with('post.board')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentInquiries = Inquiry::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 인기 게시글 (조회수 기준)
        $popularPosts = BoardPost::with('board')
            ->where('created_at', '>=', $startDate)
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        return view('manager.statistics.index', compact(
            'stats',
            'periodStats',
            'dailyPosts',
            'dailyComments',
            'boardStats',
            'recentPosts',
            'recentComments',
            'recentInquiries',
            'popularPosts',
            'period'
        ));
    }
}
