<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Board;
use App\Models\BoardPost;
use App\Models\Comment;
use App\Models\Inquiry;
use App\Models\Manager;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_managers' => Manager::count(),
            'total_boards' => Board::count(),
            'total_posts' => BoardPost::count(),
            'total_comments' => Comment::count(),
            'pending_inquiries' => Inquiry::pending()->count(),
            'recent_posts' => BoardPost::with('board')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'recent_inquiries' => Inquiry::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return view('manager.dashboard', compact('stats'));
    }
}
