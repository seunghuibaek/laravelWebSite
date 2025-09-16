@extends('manager.layouts.app')

@section('title', '댓글 관리')
@section('page-title', '댓글 관리')

@section('content')
<!-- 검색 필터 -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-search"></i> 검색 필터</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('manager.comments.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="board_id" class="form-label">게시판</label>
                    <select class="form-select" id="board_id" name="board_id">
                        <option value="">전체 게시판</option>
                        @foreach($boards as $board)
                            <option value="{{ $board->id }}" {{ request('board_id') == $board->id ? 'selected' : '' }}>
                                {{ $board->board_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">검색어</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="내용, 작성자명, 이메일">
                </div>

                <div class="col-md-2 mb-3">
                    <label for="is_secret" class="form-label">비밀댓글</label>
                    <select class="form-select" id="is_secret" name="is_secret">
                        <option value="">전체</option>
                        <option value="1" {{ request('is_secret') === '1' ? 'selected' : '' }}>비밀댓글</option>
                        <option value="0" {{ request('is_secret') === '0' ? 'selected' : '' }}>공개댓글</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="date_from" class="form-label">시작일</label>
                    <input type="date"
                           class="form-control"
                           id="date_from"
                           name="date_from"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label for="date_to" class="form-label">종료일</label>
                    <input type="date"
                           class="form-control"
                           id="date_to"
                           name="date_to"
                           value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> 검색
                </button>
                <a href="{{ route('manager.comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> 초기화
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-comments"></i> 댓글 목록
            <span class="badge bg-primary">{{ $comments->total() }}</span>
        </h5>
        @if($comments->count() > 0)
        <button type="button"
                class="btn btn-danger btn-sm"
                id="bulkDelete"
                data-url="{{ route('manager.comments.index') }}">
            <i class="fas fa-trash"></i> 선택 삭제
        </button>
        @endif
    </div>

    <div class="card-body">
        @if($comments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>게시판</th>
                            <th>게시글</th>
                            <th>댓글 내용</th>
                            <th>작성자</th>
                            <th>구분</th>
                            <th>작성일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comments as $comment)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="item-checkbox"
                                       value="{{ $comment->id }}"
                                       {{ $comment->replies()->count() > 0 ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $comment->post->board->board_name }}</span>
                            </td>
                            <td>
                                <div>
                                    {{ Str::limit($comment->post->title, 30) }}
                                    @if($comment->post->is_notice)
                                        <span class="badge bg-warning text-dark">공지</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($comment->parent_id)
                                        <i class="fas fa-reply text-muted me-1"></i>
                                    @endif

                                    @if($comment->is_secret)
                                        <i class="fas fa-lock text-warning me-1" title="비밀댓글"></i>
                                    @endif

                                    {{ Str::limit(strip_tags($comment->content), 50) }}
                                </div>

                                @if($comment->replies()->count() > 0)
                                    <small class="text-muted">
                                        <i class="fas fa-comments"></i> 대댓글 {{ $comment->replies()->count() }}개
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $comment->author_name }}</strong>
                                    @if($comment->author_email)
                                        <br><small class="text-muted">{{ $comment->author_email }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($comment->parent_id)
                                    <span class="badge bg-info">대댓글</span>
                                @else
                                    <span class="badge bg-secondary">댓글</span>
                                @endif

                                @if($comment->is_secret)
                                    <br><span class="badge bg-warning text-dark" title="비밀">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">비밀</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $comment->created_at->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $comment->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($comment->replies()->count() > 0)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            disabled
                                            data-bs-toggle="tooltip"
                                            title="대댓글이 있어 삭제할 수 없습니다">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <form action="{{ route('manager.comments.destroy', $comment) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger btn-delete"
                                                data-bs-toggle="tooltip"
                                                title="삭제">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="d-flex justify-content-center">
                {{ $comments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    @if(request()->hasAny(['board_id', 'search', 'is_secret', 'date_from', 'date_to']))
                        검색 조건에 맞는 댓글이 없습니다.
                    @else
                        등록된 댓글이 없습니다.
                    @endif
                </h5>
                @if(request()->hasAny(['board_id', 'search', 'is_secret', 'date_from', 'date_to']))
                    <a href="{{ route('manager.comments.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-redo"></i> 전체 댓글 보기
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- 통계 정보 -->
@if($comments->total() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary">{{ $comments->total() }}</h4>
                <small class="text-muted">총 댓글 수</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success">{{ \App\Models\Comment::whereDate('created_at', today())->count() }}</h4>
                <small class="text-muted">오늘 댓글</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning">{{ \App\Models\Comment::where('is_secret', true)->count() }}</h4>
                <small class="text-muted">비밀 댓글</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info">{{ \App\Models\Comment::whereNotNull('parent_id')->count() }}</h4>
                <small class="text-muted">대댓글</small>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 일괄 삭제 기능 수정
    $('#bulkDelete').click(function() {
        var selected = $('.item-checkbox:checked:not(:disabled)');
        if (selected.length === 0) {
            alert('삭제할 댓글을 선택해주세요.');
            return;
        }

        if (confirm(selected.length + '개 댓글을 삭제하시겠습니까?')) {
            var ids = [];
            selected.each(function() {
                ids.push($(this).val());
            });

            // 일괄 삭제 폼 생성 및 제출
            var form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("manager.comments.index") }}/bulk-delete'
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': $('meta[name="csrf-token"]').attr('content')
            }));

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'ids',
                'value': ids.join(',')
            }));

            $('body').append(form);
            form.submit();
        }
    });

    // 전체 선택 시 비활성화된 체크박스는 제외
    $('#selectAll').change(function() {
        $('.item-checkbox:not(:disabled)').prop('checked', this.checked);
    });
});
</script>
@endpush
