@extends('front.layouts.app')

@section('title', '"' . $query . '" 검색 결과')

@section('content')
<!-- Search Header -->
<section class="board-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="display-6 fw-bold">
                    <i class="fas fa-search me-3"></i>검색 결과
                </h1>
                <p class="lead">"{{ $query }}"에 대한 검색 결과입니다.</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-5">
    <div class="container">
        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-8 offset-md-2">
                <form method="GET" action="{{ route('search') }}" class="search-form">
                    <div class="input-group input-group-lg">
                        <input type="text"
                               class="form-control"
                               name="q"
                               value="{{ $query }}"
                               placeholder="검색어를 입력하세요...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> 검색
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Count -->
        <div class="row mb-4">
            <div class="col-12">
                <p class="text-muted">
                    총 <strong>{{ number_format($posts->total()) }}</strong>개의 검색 결과가 있습니다.
                </p>
            </div>
        </div>

        <!-- Results -->
        @if($posts->count() > 0)
            <div class="row">
                @foreach($posts as $post)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">{{ $post->board->board_name }}</span>
                                <small class="text-muted">{{ $post->created_at->format('Y-m-d') }}</small>
                            </div>

                            <h5 class="card-title">
                                <a href="{{ route('board.show', [$post->board->board_code, $post]) }}"
                                   class="text-decoration-none text-dark">
                                    {!! str_ireplace($query, '<mark>' . $query . '</mark>', e($post->title)) !!}
                                </a>
                                @if($post->is_notice)
                                    <span class="badge bg-warning text-dark ms-2">공지</span>
                                @endif
                                @if($post->is_secret)
                                    <span class="badge bg-secondary ms-2" title="비밀">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">비밀</span>
                                    </span>
                                @endif
                            </h5>

                            <p class="card-text text-muted">
                                {!! str_ireplace($query, '<mark>' . $query . '</mark>', e(Str::limit(strip_tags($post->content), 200))) !!}
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="post-meta text-muted small">
                                    <span><i class="fas fa-user me-1"></i>{{ $post->author_name }}</span>
                                    <span class="ms-3"><i class="fas fa-eye me-1"></i>{{ number_format($post->view_count) }}</span>
                                    @if($post->comments->count() > 0)
                                        <span class="ms-3"><i class="fas fa-comments me-1"></i>{{ $post->comments->count() }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('board.show', [$post->board->board_code, $post]) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    자세히 보기
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $posts->appends(['q' => $query])->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">검색 결과가 없습니다.</h5>
                <p class="text-muted">다른 검색어로 다시 시도해보세요.</p>

                <div class="mt-4">
                    <h6>검색 팁:</h6>
                    <ul class="list-unstyled text-muted">
                        <li>• 검색어의 철자를 확인해보세요</li>
                        <li>• 더 간단한 검색어를 사용해보세요</li>
                        <li>• 다른 키워드로 검색해보세요</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
