@php use App\Models\Board;use App\Models\Inquiry; @endphp
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-cogs"></i> 관리자</h4>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}"
                   href="{{ route('manager.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>대시보드</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.managers.*') ? 'active' : '' }}"
                   href="{{ route('manager.managers.index') }}">
                    <i class="fas fa-users-cog"></i>
                    <span>관리자 관리</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.users.*') ? 'active' : '' }}"
                   href="{{ route('manager.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span>회원관리</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.boards.*') ? 'active' : '' }}"
                   href="{{ route('manager.boards.index') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>게시판 관리</span>
                </a>
            </li>

            <!-- 게시글 관리 -->
            @php
                $boards = Board::orderBy('sort_order')->orderBy('board_name')->get();
            @endphp
            @if($boards->count() > 0)
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#postsSubmenu"
                       aria-expanded="false">
                        <i class="fas fa-file-alt"></i>
                        <span>게시글 관리</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="postsSubmenu">
                        <ul class="nav flex-column ms-3">
                            @foreach($boards as $board)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.posts.*') && request()->route('board_code') === $board->board_code ? 'active' : '' }}"
                                       href="{{ route('manager.posts.index', $board->board_code) }}">
                                        <i class="fas fa-circle fa-xs me-2"></i>
                                        {{ $board->board_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.comments.*') ? 'active' : '' }}"
                   href="{{ route('manager.comments.index') }}">
                    <i class="fas fa-comments"></i>
                    <span>댓글 관리</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.inquiries.*') ? 'active' : '' }}"
                   href="{{ route('manager.inquiries.index') }}">
                    <i class="fas fa-envelope"></i>
                    <span>문의하기 관리</span>
                    @if($pendingInquiries = Inquiry::pending()->count())
                        <span class="badge bg-danger ms-2">{{ $pendingInquiries }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.statistics.*') ? 'active' : '' }}"
                   href="{{ route('manager.statistics.index') }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>사이트 통계</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.banners.*') ? 'active' : '' }}"
                   href="{{ route('manager.banners.index') }}">
                    <i class="fas fa-images"></i>
                    <span>메인 배너</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.settings.*') ? 'active' : '' }}"
                   href="{{ route('manager.settings.index') }}">
                    <i class="fas fa-cog"></i>
                    <span>시스템 설정</span>
                </a>
            </li>
        </ul>
    </div>
</div>
