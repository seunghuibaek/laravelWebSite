@extends('front.layouts.app')

@section('title', $board->board_name)
@section('description', $board->memo ?: $board->board_name . ' 게시판')

@section('content')
<!-- Board Header -->
<section class="board-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold mb-3">
                    @if($board->board_type === 'gallery')
                        <i class="fas fa-images me-3"></i>
                    @else
                        <i class="fas fa-list me-3"></i>
                    @endif
                    {{ $board->board_name }}
                </h1>
                @if($board->memo)
                    <p class="lead">{{ $board->memo }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Board Navigation -->
<section class="board-nav">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">홈</a></li>
                        <li class="breadcrumb-item active">{{ $board->board_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                @if($board->allow_user_write)
                    <a href="{{ route('board.create', $board->board_code) }}" class="btn btn-primary">
                        <i class="fas fa-pen me-2"></i>글쓰기
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Board Content -->
<section class="py-5">
    <div class="container">
        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-6 offset-md-6">
                <form method="GET" action="{{ route('board.index', $board->board_code) }}" class="search-form">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="제목, 내용, 작성자 검색...">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($board->board_type === 'gallery')
            <!-- Gallery View -->
            @if($notices->count() > 0 || $posts->count() > 0)
                <!-- Notices -->
                @if($notices->count() > 0)
                <div class="mb-5">
                    <h5 class="mb-3"><i class="fas fa-bullhorn text-warning me-2"></i>공지사항</h5>
                    <div class="gallery-grid">
                        @foreach($notices as $notice)
                        <div class="gallery-item">
                            @php
                                $firstImage = $notice->files->where('mime_type', 'like', 'image/%')->first();
                            @endphp
                            @if($firstImage)
                                <img src="{{ Storage::url($firstImage->file_path) }}" alt="{{ $notice->title }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="gallery-overlay">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-warning text-dark">공지</span>
                                    <small>{{ $notice->created_at->format('m-d') }}</small>
                                </div>
                                <h6 class="gallery-title">
                                    <a href="{{ route('board.show', [$board->board_code, $notice]) }}" class="text-white text-decoration-none">
                                        {{ Str::limit($notice->title, 30) }}
                                    </a>
                                </h6>
                                <small class="text-light">{{ $notice->author_name }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Gallery Posts -->
                @if($posts->count() > 0)
                <div class="gallery-grid">
                    @foreach($posts as $post)
                    <div class="gallery-item">
                        @php
                            $firstImage = $post->files->where('mime_type', 'like', 'image/%')->first();
                        @endphp
                        @if($firstImage)
                            <img src="{{ Storage::url($firstImage->file_path) }}" alt="{{ $post->title }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="gallery-overlay">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                @if($post->is_secret)
                                    <span class="badge bg-secondary" title="비밀">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">비밀</span>
                                    </span>
                                @endif
                                <small>{{ $post->created_at->format('m-d') }}</small>
                            </div>
                            <h6 class="gallery-title">
                                <a href="{{ route('board.show', [$board->board_code, $post]) }}" class="text-white text-decoration-none">
                                    {{ Str::limit($post->title, 30) }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-light">{{ $post->author_name }}</small>
                                <small class="text-light">
                                    <i class="fas fa-eye me-1"></i>{{ $post->view_count }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">등록된 게시글이 없습니다.</h5>
                    @if($board->allow_user_write)
                        <a href="{{ route('board.create', $board->board_code) }}" class="btn btn-primary mt-3">
                            <i class="fas fa-pen me-2"></i>첫 번째 글 작성하기
                        </a>
                    @endif
                </div>
            @endif
        @else
            <!-- List View -->
            @if($notices->count() > 0 || $posts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="60">번호</th>
                                <th>제목</th>
                                <th width="120">작성자</th>
                                <th width="100">작성일</th>
                                <th width="80">조회</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Notices -->
                            @foreach($notices as $notice)
                            <tr class="table-warning">
                                <td class="text-center">
                                    <i class="fas fa-bullhorn text-warning"></i>
                                </td>
                                <td>
                                    <a href="{{ route('board.show', [$board->board_code, $notice]) }}" class="text-decoration-none text-dark fw-bold">
                                        {{ $notice->title }}
                                    </a>
                                    @if($notice->files->count() > 0)
                                        <i class="fas fa-paperclip text-muted ms-1"></i>
                                    @endif
                                    @if($board->use_comment && $notice->comments->count() > 0)
                                        <span class="badge bg-primary ms-1">{{ $notice->comments->count() }}</span>
                                    @endif
                                </td>
                                <td>{{ $notice->author_name }}</td>
                                <td>{{ $notice->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">{{ number_format($notice->view_count) }}</td>
                            </tr>
                            @endforeach

                            <!-- Regular Posts -->
                            @foreach($posts as $index => $post)
                            <tr class="post-item">
                                <td class="text-center">{{ $posts->total() - $posts->firstItem() - $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('board.show', [$board->board_code, $post]) }}" class="text-decoration-none text-dark">
                                        {{ $post->title }}
                                    </a>
                                    @if($post->is_secret)
                                        <i class="fas fa-lock text-warning ms-1"></i>
                                    @endif
                                    @if($post->files->count() > 0)
                                        <i class="fas fa-paperclip text-muted ms-1"></i>
                                    @endif
                                    @if($board->use_comment && $post->comments->count() > 0)
                                        <span class="badge bg-primary ms-1">{{ $post->comments->count() }}</span>
                                    @endif
                                    @if($post->created_at->isToday())
                                        <span class="badge bg-danger ms-1">NEW</span>
                                    @endif
                                </td>
                                <td>{{ $post->author_name }}</td>
                                <td>
                                    @if($post->created_at->isToday())
                                        {{ $post->created_at->format('H:i') }}
                                    @else
                                        {{ $post->created_at->format('m-d') }}
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($post->view_count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request('search'))
                            검색 결과가 없습니다.
                        @else
                            등록된 게시글이 없습니다.
                        @endif
                    </h5>
                    @if(request('search'))
                        <a href="{{ route('board.index', $board->board_code) }}" class="btn btn-secondary mt-3">
                            <i class="fas fa-redo me-2"></i>전체 게시글 보기
                        </a>
                    @elseif($board->allow_user_write)
                        <a href="{{ route('board.create', $board->board_code) }}" class="btn btn-primary mt-3">
                            <i class="fas fa-pen me-2"></i>첫 번째 글 작성하기
                        </a>
                    @endif
                </div>
            @endif
        @endif
    </div>
</section>
@endsection
