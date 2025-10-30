@php use App\Models\BoardPost; @endphp
@extends('front.layouts.app')

@section('title', $post->title . ' - ' . $board->board_name)
@section('description', Str::limit(strip_tags($post->content), 160))

@push('styles')
    @if($board->use_editor)
        <!-- Froala Editor CSS -->
        <link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet"
              type="text/css"/>
    @endif
@endpush

@section('content')
    <!-- Board Header -->
    <section class="board-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb text-white">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">홈</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('board.index', $board->board_code) }}"
                                                           class="text-white">{{ $board->board_name }}</a></li>
                            <li class="breadcrumb-item active text-white">{{ Str::limit($post->title, 30) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Post Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Post -->
                    <article class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h1 class="h3 mb-2">{{ $post->title }}</h1>
                                    <div class="post-meta text-muted">
                                        <span><i class="fas fa-user me-1"></i>{{ $post->author_name }}</span>
                                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                        <span class="ms-3"><i class="fas fa-eye me-1"></i>{{ number_format($post->view_count) }}</span>
                                        @if($post->like_count > 0)
                                            <span class="ms-3"><i class="fas fa-heart me-1"></i>{{ number_format($post->like_count) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if($post->is_notice)
                                        <span class="badge bg-warning text-dark">공지</span>
                                    @endif
                                    @if($post->is_secret)
                                        <span class="badge bg-secondary" title="비밀">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">비밀</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="post-content">
                                @if($board->use_editor)
                                    {!! $post->content !!}
                                @else
                                    {!! nl2br(e($post->content)) !!}
                                @endif
                            </div>

                            <!-- Files -->
                            @if($post->files->count() > 0)
                                <div class="file-list mt-4">
                                    <h6><i class="fas fa-paperclip me-2"></i>첨부파일</h6>
                                    @foreach($post->files as $file)
                                        <div class="file-item">
                                            <div class="file-icon">
                                                @if($file->isImage())
                                                    <i class="fas fa-image text-info"></i>
                                                @else
                                                    <i class="fas fa-file text-muted"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="{{ Storage::url($file->file_path) }}"
                                                   target="_blank"
                                                   class="text-decoration-none">
                                                    {{ $file->original_name }}
                                                </a>
                                                <small class="text-muted ms-2">
                                                    ({{ $file->getFileSizeFormatted() }},
                                                    다운로드: {{ $file->download_count }}회)
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-danger like-btn"
                                            data-post-id="{{ $post->id }}">
                                        <i class="fas fa-heart me-1"></i>
                                        좋아요 <span class="like-count">{{ $post->like_count }}</span>
                                    </button>
                                </div>
                                @if($board->allow_user_write && auth()->check())
                                    <div>
                                        <a href="{{ route('board.edit', [$board->board_code, $post]) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit me-1"></i>수정
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-1"></i>삭제
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>

                    <!-- Navigation -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            @if($prevPost)
                                <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                    <i class="fas fa-chevron-up text-muted me-2"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">이전글</small>
                                        <div>
                                            <a href="{{ route('board.show', [$board->board_code, $prevPost]) }}"
                                               class="text-decoration-none">
                                                {{ Str::limit($prevPost->title, 50) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($nextPost)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chevron-down text-muted me-2"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">다음글</small>
                                        <div>
                                            <a href="{{ route('board.show', [$board->board_code, $nextPost]) }}"
                                               class="text-decoration-none">
                                                {{ Str::limit($nextPost->title, 50) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!$prevPost && !$nextPost)
                                <div class="text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>이전글/다음글이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments -->
                    @if($board->use_comment)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-comments me-2"></i>댓글
                                    <span class="badge bg-primary">{{ $comments->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Comment List -->
                                @foreach($comments as $comment)
                                    <div class="comment-item p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $comment->author_name }}</strong>
                                                @if($comment->is_secret)
                                                    <span class="badge bg-warning text-dark ms-2" title="비밀">
                                            <i class="fas fa-lock" aria-hidden="true"></i>
                                            <span class="visually-hidden">비밀</span>
                                        </span>
                                                @endif
                                            </div>
                                            <small
                                                class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                        <div class="comment-content">
                                            @php $canView = method_exists($comment, 'canView') ? $comment->canView(auth()->user(), $post) : !$comment->is_secret; @endphp
                                            @if($canView)
                                                {!! nl2br(e($comment->content)) !!}
                                            @else
                                                <em class="text-muted"><i class="fas fa-lock me-1"></i>비밀댓글입니다.</em>
                                            @endif
                                        </div>

                                        @if($comment->replies->count() > 0)
                                            <div class="mt-3">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary comment-toggle"
                                                        data-comment-id="{{ $comment->id }}">
                                                    <i class="fas fa-chevron-down me-1"></i>
                                                    답글 {{ $comment->replies->count() }}개 보기
                                                </button>

                                                <div id="comment-replies-{{ $comment->id }}" style="display: none;">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="comment-reply p-3 mt-2">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <i class="fas fa-reply text-muted me-2"></i>
                                                                    <strong>{{ $reply->author_name }}</strong>
                                                                    @if($reply->is_secret)
                                                                        <span class="badge bg-warning text-dark ms-2"
                                                                              title="비밀">
                                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                                        <span class="visually-hidden">비밀</span>
                                                    </span>
                                                                    @endif
                                                                </div>
                                                                <small
                                                                    class="text-muted">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                                                            </div>
                                                            <div class="comment-content">
                                                                @php $canViewReply = method_exists($reply, 'canView') ? $reply->canView(auth()->user(), $post) : !$reply->is_secret; @endphp
                                                                @if($canViewReply)
                                                                    {!! nl2br(e($reply->content)) !!}
                                                                @else
                                                                    <em class="text-muted"><i
                                                                            class="fas fa-lock me-1"></i>비밀댓글입니다.</em>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if($comments->count() === 0)
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-comments fa-2x mb-3"></i>
                                        <p>첫 번째 댓글을 작성해보세요!</p>
                                    </div>
                                @endif

                                <!-- Comment Form -->
                                <div class="comment-form mt-4">
                                    <h6><i class="fas fa-edit me-2"></i>댓글 작성</h6>
                                    @auth
                                        <form action="{{ route('comments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                                            <div class="mb-3">
                                                <textarea class="form-control" name="content" rows="4"
                                                          placeholder="댓글을 입력하세요..." required></textarea>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_secret"
                                                           id="is_secret">
                                                    <label class="form-check-label" for="is_secret">
                                                        비밀댓글
                                                    </label>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane me-2"></i>댓글 등록
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-info-circle me-2"></i>
                                                댓글 작성은 로그인한 회원만 가능합니다.
                                            </div>
                                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">로그인</a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>게시판 메뉴</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('board.index', $board->board_code) }}"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>목록으로
                                </a>
                                @if($board->allow_user_write)
                                    <a href="{{ route('board.create', $board->board_code) }}" class="btn btn-primary">
                                        <i class="fas fa-pen me-2"></i>글쓰기
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    @php
                        $recentPosts = BoardPost::where('board_id', $board->id)
                            ->where('id', '!=', $post->id)
                            ->where('is_notice', false)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($recentPosts->count() > 0)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>최근 게시글</h6>
                            </div>
                            <div class="card-body">
                                @foreach($recentPosts as $recentPost)
                                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('board.show', [$board->board_code, $recentPost]) }}"
                                                   class="text-decoration-none">
                                                    {{ Str::limit($recentPost->title, 25) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                {{ $recentPost->author_name }}
                                                | {{ $recentPost->created_at->format('m-d') }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @if($board->allow_user_write && auth()->check())
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">게시글 삭제</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('board.destroy', [$board->board_code, $post]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <p>정말로 이 게시글을 삭제하시겠습니까?</p>
                            <div class="mb-3">
                                <label for="delete_password" class="form-label">비밀번호 확인</label>
                                <input type="password" class="form-control" name="password" id="delete_password"
                                       required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                            <button type="submit" class="btn btn-danger">삭제</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if($board->use_editor)
        <!-- Froala Editor JS -->
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/languages/ko.js"></script>
    @endif

    <script>
        $(document).ready(function () {
            // 좋아요 버튼 클릭 시 AJAX 처리는 front.js에서 처리됨

            // 이미지 클릭 시 모달로 크게 보기
            $('.post-content img').click(function () {
                const src = $(this).attr('src');
                const alt = $(this).attr('alt') || '이미지';

                const modal = $(`
            <div class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${alt}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${src}" class="img-fluid" alt="${alt}">
                        </div>
                    </div>
                </div>
            </div>
        `);

                $('body').append(modal);
                modal.modal('show');

                modal.on('hidden.bs.modal', function () {
                    modal.remove();
                });
            });
        });
    </script>
@endpush
