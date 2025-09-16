@extends('manager.layouts.app')

@section('title', $post->title . ' - ' . $board->board_name)
@section('page-title', $board->board_name . ' 게시글 상세')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    {{ $post->title }}
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
                <div>
                    <a href="{{ route('manager.posts.index', $board->board_code) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> 목록
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- 게시글 정보 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">작성자:</th>
                                <td>{{ $post->author_name }}</td>
                            </tr>
                            <tr>
                                <th>이메일:</th>
                                <td>{{ $post->author_email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>작성일:</th>
                                <td>{{ $post->created_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">조회수:</th>
                                <td>{{ number_format($post->view_count) }}</td>
                            </tr>
                            <tr>
                                <th>좋아요:</th>
                                <td>{{ number_format($post->like_count) }}</td>
                            </tr>
                            <tr>
                                <th>IP 주소:</th>
                                <td>{{ $post->ip_address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- 게시글 내용 -->
                <div class="mb-4">
                    <h6><i class="fas fa-file-text"></i> 게시글 내용</h6>
                    <div class="border rounded p-3 bg-light">
                        @if($board->use_editor)
                            {!! $post->content !!}
                        @else
                            {!! nl2br(e($post->content)) !!}
                        @endif
                    </div>
                </div>

                <!-- 첨부파일 -->
                @if($post->files->count() > 0)
                <div class="mb-4">
                    <h6><i class="fas fa-paperclip"></i> 첨부파일</h6>
                    <div class="file-list">
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
                                    ({{ $file->getFileSizeFormatted() }}, 다운로드: {{ $file->download_count }}회)
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 댓글 -->
                @if($board->use_comment && $post->comments->count() > 0)
                <div class="mb-4">
                    <h6><i class="fas fa-comments"></i> 댓글 ({{ $post->comments->count() }}개)</h6>
                    @foreach($post->comments as $comment)
                    <div class="comment-item p-3 mb-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $comment->author_name }}</strong>
                                @if($comment->is_secret)
                                    <span class="badge bg-warning text-dark ms-2">비밀</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                        <div class="comment-content">
                            {!! nl2br(e($comment->content)) !!}
                        </div>

                        @if($comment->replies->count() > 0)
                        <div class="mt-3">
                            @foreach($comment->replies as $reply)
                            <div class="comment-reply p-3 mt-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <i class="fas fa-reply text-muted me-2"></i>
                                        <strong>{{ $reply->author_name }}</strong>
                                        @if($reply->is_secret)
                                            <span class="badge bg-warning text-dark ms-2">비밀</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                                <div class="comment-content">
                                    {!! nl2br(e($reply->content)) !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="{{ route('manager.posts.destroy', [$board->board_code, $post]) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i> 삭제
                            </button>
                        </form>
                    </div>

                    <div>
                        <a href="{{ route('manager.posts.edit', [$board->board_code, $post]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> 수정
                        </a>
                        <a href="{{ route('manager.posts.index', $board->board_code) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> 목록
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 사이드바 -->
    <div class="col-lg-4">
        <!-- 빠른 액션 -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> 빠른 액션</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('manager.posts.create', $board->board_code) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> 새 게시글 작성
                    </a>
                    <a href="{{ route('board.show', [$board->board_code, $post]) }}"
                       class="btn btn-outline-primary btn-sm"
                       target="_blank">
                        <i class="fas fa-external-link-alt"></i> 프론트에서 보기
                    </a>
                </div>
            </div>
        </div>

        <!-- 게시판 정보 -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> 게시판 정보</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>게시판명:</th>
                        <td>{{ $board->board_name }}</td>
                    </tr>
                    <tr>
                        <th>게시판 코드:</th>
                        <td><code>{{ $board->board_code }}</code></td>
                    </tr>
                    <tr>
                        <th>타입:</th>
                        <td>
                            @if($board->board_type === 'gallery')
                                <span class="badge bg-info">갤러리</span>
                            @else
                                <span class="badge bg-secondary">일반</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>총 게시글:</th>
                        <td>{{ number_format($board->posts()->count()) }}개</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('manager.boards.show', $board) }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-cog"></i> 게시판 설정
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
