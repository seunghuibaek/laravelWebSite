@extends('front.layouts.app')

@section('title', '홈')
@section('description', $siteSettings['site_description'])

@section('content')
<!-- Hero/Banner Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">{{ $siteSettings['site_name'] }}</h1>
                <p class="lead mb-4">{{ $siteSettings['site_description'] }}</p>
                <div class="d-flex gap-3">
                    @if($boards->count() > 0)
                        <a href="{{ route('board.index', $boards->first()->board_code) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-clipboard-list me-2"></i>게시판 보기
                        </a>
                    @endif
                    <a href="{{ route('inquiry.create') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>문의하기
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                @if(isset($banners) && $banners->count() > 0)
                    <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded shadow">
                            @foreach($banners as $idx => $banner)
                                <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                    @if($banner->link_url)
                                        <a href="{{ $banner->link_url }}" target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/' . $banner->image_path) }}" class="d-block w-100" alt="{{ $banner->title }}">
                                        </a>
                                    @else
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" class="d-block w-100" alt="{{ $banner->title }}">
                                    @endif
                                    @if($banner->title)
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5 class="bg-dark bg-opacity-50 d-inline-block px-2 py-1 rounded">{{ $banner->title }}</h5>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                @else
                    <div class="hero-image text-center">
                        <i class="fas fa-globe fa-10x text-primary opacity-25"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Notice Section -->
@if($notices->count() > 0)
<section class="notice-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">
                    <i class="fas fa-bullhorn text-warning me-2"></i>공지사항
                </h2>

                <div class="row">
                    @foreach($notices as $notice)
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-warning text-dark">공지</span>
                                    <small class="text-muted">{{ $notice->created_at->format('Y-m-d') }}</small>
                                </div>
                                <h5 class="card-title">
                                    <a href="{{ route('board.show', [$notice->board->board_code, $notice->id]) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $notice->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit(strip_tags($notice->content), 100) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $notice->author_name }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ number_format($notice->view_count) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($notices->count() >= 4)
                <div class="text-center mt-4">
                    <a href="{{ route('board.index', 'notice') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>더 많은 공지사항 보기
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

<!-- Boards Section -->
@if($boards->count() > 0)
<section class="boards-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>게시판
                </h2>

                <div class="row">
                    @foreach($boards as $board)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if($board->board_type === 'gallery')
                                        <i class="fas fa-images fa-3x text-info"></i>
                                    @else
                                        <i class="fas fa-list fa-3x text-primary"></i>
                                    @endif
                                </div>
                                <h5 class="card-title">{{ $board->board_name }}</h5>
                                @if($board->memo)
                                    <p class="card-text text-muted">{{ Str::limit($board->memo, 80) }}</p>
                                @endif
                                <div class="mt-3">
                                    <span class="badge bg-primary">{{ $board->posts()->count() }}개 게시글</span>
                                    @if($board->board_type === 'gallery')
                                        <span class="badge bg-info">갤러리</span>
                                    @endif
                                </div>
                                <a href="{{ route('board.index', $board->board_code) }}"
                                   class="btn btn-outline-primary mt-3">
                                    <i class="fas fa-arrow-right me-2"></i>게시판 보기
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Recent Posts Section -->
@if($recentPosts->count() > 0)
<section class="recent-posts-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">
                    <i class="fas fa-clock text-success me-2"></i>최근 게시글
                </h2>

                <div class="row">
                    @foreach($recentPosts->take(6) as $post)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-secondary">{{ $post->board->board_name }}</span>
                                    <small class="text-muted">{{ $post->created_at->format('m-d') }}</small>
                                </div>
                                <h6 class="card-title">
                                    <a href="{{ route('board.show', [$post->board->board_code, $post->id]) }}"
                                       class="text-decoration-none text-dark">
                                        {{ Str::limit($post->title, 40) }}
                                    </a>
                                </h6>
                                <p class="card-text text-muted small">
                                    {{ Str::limit(strip_tags($post->content), 80) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $post->author_name }}</small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ $post->view_count }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="mb-4">궁금한 점이 있으신가요?</h2>
                <p class="lead mb-4">언제든지 문의해 주세요. 빠르게 답변드리겠습니다.</p>
                <a href="{{ route('inquiry.create') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-envelope me-2"></i>문의하기
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
