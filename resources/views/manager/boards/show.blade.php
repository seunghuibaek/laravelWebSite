@extends('manager.layouts.app')

@section('title', '게시판 상세보기')
@section('page-title', '게시판 상세보기')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> {{ $board->board_name }}</h5>
                <div>
                    <a href="{{ route('manager.boards.edit', $board) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> 수정
                    </a>
                    <a href="{{ route('manager.boards.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> 목록
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">게시판 코드:</th>
                                <td><code>{{ $board->board_code }}</code></td>
                            </tr>
                            <tr>
                                <th>게시판명:</th>
                                <td>{{ $board->board_name }}</td>
                            </tr>
                            <tr>
                                <th>게시판 타입:</th>
                                <td>
                                    @if($board->board_type === 'gallery')
                                        <span class="badge bg-info">갤러리</span>
                                    @else
                                        <span class="badge bg-secondary">일반</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>업로드 폴더:</th>
                                <td><code>{{ $board->upload_folder }}</code></td>
                            </tr>
                            <tr>
                                <th>파일 업로드 개수:</th>
                                <td>{{ $board->max_file_count }}개</td>
                            </tr>
                            <tr>
                                <th>파일 크기 제한:</th>
                                <td>{{ number_format($board->max_file_size) }}KB</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">정렬 순서:</th>
                                <td>{{ $board->sort_order }}</td>
                            </tr>
                            <tr>
                                <th>상태:</th>
                                <td>
                                    @if($board->is_active)
                                        <span class="badge bg-success">활성</span>
                                    @else
                                        <span class="badge bg-secondary">비활성</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>등록일:</th>
                                <td>{{ $board->created_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                            <tr>
                                <th>수정일:</th>
                                <td>{{ $board->updated_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                            <tr>
                                <th>게시글 수:</th>
                                <td>
                                    <span class="badge bg-primary">{{ $board->posts()->count() }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- 기능 설정 -->
                <div class="mt-4">
                    <h6><i class="fas fa-cog"></i> 기능 설정</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                @if($board->use_notice)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>공지사항 사용</span>
                                @else
                                    <i class="fas fa-times-circle text-muted me-2"></i>
                                    <span class="text-muted">공지사항 미사용</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                @if($board->use_file_upload)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>파일 업로드 사용</span>
                                @else
                                    <i class="fas fa-times-circle text-muted me-2"></i>
                                    <span class="text-muted">파일 업로드 미사용</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                @if($board->use_editor)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>에디터 사용</span>
                                @else
                                    <i class="fas fa-times-circle text-muted me-2"></i>
                                    <span class="text-muted">에디터 미사용</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                @if($board->use_comment)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>댓글 사용</span>
                                @else
                                    <i class="fas fa-times-circle text-muted me-2"></i>
                                    <span class="text-muted">댓글 미사용</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                @if($board->allow_user_write)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>사용자 글등록 허용</span>
                                @else
                                    <i class="fas fa-times-circle text-muted me-2"></i>
                                    <span class="text-muted">사용자 글등록 제한</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($board->memo)
                <div class="mt-4">
                    <h6><i class="fas fa-sticky-note"></i> 메모</h6>
                    <div class="alert alert-light">
                        {{ $board->memo }}
                    </div>
                </div>
                @endif
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($board->posts()->count() === 0)
                        <form action="{{ route('manager.boards.destroy', $board) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i> 삭제
                            </button>
                        </form>
                        @else
                        <button type="button"
                                class="btn btn-danger btn-sm"
                                disabled
                                data-bs-toggle="tooltip"
                                title="게시글이 있어 삭제할 수 없습니다">
                            <i class="fas fa-trash"></i> 삭제
                        </button>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('manager.boards.edit', $board) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> 수정
                        </a>
                        <a href="{{ route('manager.boards.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> 목록
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 최근 게시글 -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-file-alt"></i> 최근 게시글</h6>
            </div>
            <div class="card-body">
                @if($board->posts->count() > 0)
                    @foreach($board->posts as $post)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ Str::limit($post->title, 25) }}</h6>
                            <small class="text-muted">{{ $post->author_name }}</small>
                            <br>
                            <small class="text-muted">{{ $post->created_at->format('m-d H:i') }}</small>
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
                    @endforeach
                @else
                    <p class="text-muted text-center py-4">게시글이 없습니다.</p>
                @endif
            </div>
        </div>

        <!-- 통계 -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> 게시판 통계</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $board->posts()->count() }}</h4>
                            <small class="text-muted">총 게시글</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $board->posts()->where('created_at', '>=', now()->subDays(7))->count() }}</h4>
                        <small class="text-muted">최근 7일</small>
                    </div>
                </div>

                @if($board->use_comment)
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info">{{ $board->posts()->withCount('comments')->get()->sum('comments_count') }}</h4>
                            <small class="text-muted">총 댓글</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $board->posts()->sum('view_count') }}</h4>
                        <small class="text-muted">총 조회수</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
