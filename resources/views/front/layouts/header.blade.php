<header class="header">
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-envelope me-2"></i>
                        {{ \App\Models\SystemSetting::get('admin_email', 'admin@example.com') }}
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('manager.login') }}" class="text-white text-decoration-none">
                        <i class="fas fa-user-cog me-1"></i> 관리자
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header bg-white shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                    {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> 홈
                            </a>
                        </li>

                        <!-- 게시판 메뉴 -->
                        @php
                            $boards = \App\Models\Board::where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('board_name')
                                ->get();
                        @endphp

                        @if($boards->count() > 0)
                            @if($boards->count() <= 5)
                                <!-- 게시판이 5개 이하일 때는 개별 메뉴로 표시 -->
                                @foreach($boards as $board)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('board.*') && request()->route('board_code') === $board->board_code ? 'active' : '' }}"
                                       href="{{ route('board.index', $board->board_code) }}">
                                        @if($board->board_type === 'gallery')
                                            <i class="fas fa-images"></i>
                                        @else
                                            <i class="fas fa-list"></i>
                                        @endif
                                        {{ $board->board_name }}
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <!-- 게시판이 5개 초과일 때는 드롭다운 메뉴로 표시 -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('board.*') ? 'active' : '' }}"
                                       href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-clipboard-list"></i> 게시판
                                    </a>
                                    <ul class="dropdown-menu">
                                        @foreach($boards as $board)
                                        <li>
                                            <a class="dropdown-item {{ request()->routeIs('board.*') && request()->route('board_code') === $board->board_code ? 'active' : '' }}"
                                               href="{{ route('board.index', $board->board_code) }}">
                                                @if($board->board_type === 'gallery')
                                                    <i class="fas fa-images me-2"></i>
                                                @else
                                                    <i class="fas fa-list me-2"></i>
                                                @endif
                                                {{ $board->board_name }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('inquiry.*') ? 'active' : '' }}" href="{{ route('inquiry.create') }}">
                                <i class="fas fa-envelope"></i> 문의하기
                            </a>
                        </li>
                    </ul>

                    <!-- 검색 -->
                    <form class="d-flex me-3" action="{{ route('search') }}" method="GET">
                        <input class="form-control me-2" type="search" name="q" placeholder="검색..." value="{{ request('q') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <!-- 사용자 메뉴 -->
                    <ul class="navbar-nav">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="fas fa-user-edit me-2"></i>마이페이지
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>로그아웃
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> 로그인
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> 회원가입
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>
