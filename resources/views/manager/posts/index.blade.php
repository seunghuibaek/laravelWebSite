@extends('manager.layouts.app')

@section('title', $board->board_name . ' 게시글 관리')
@section('page-title', $board->board_name . ' 게시글 관리')

@section('content')
<!-- 검색 필터 -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-search"></i> 검색 필터</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('manager.posts.index', $board->board_code) }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">검색어</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="제목, 내용, 작성자">
                </div>

                <div class="col-md-2 mb-3">
                    <label for="is_notice" class="form-label">공지사항</label>
                    <select class="form-select" id="is_notice" name="is_notice">
                        <option value="">전체</option>
                        <option value="1" {{ request('is_notice') === '1' ? 'selected' : '' }}>공지사항</option>
                        <option value="0" {{ request('is_notice') === '0' ? 'selected' : '' }}>일반글</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="is_secret" class="form-label">비밀글</label>
                    <select class="form-select" id="is_secret" name="is_secret">
                        <option value="">전체</option>
                        <option value="1" {{ request('is_secret') === '1' ? 'selected' : '' }}>비밀글</option>
                        <option value="0" {{ request('is_secret') === '0' ? 'selected' : '' }}>공개글</option>
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

                <div class="col-md-1 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('manager.posts.index', $board->board_code) }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-file-alt"></i> {{ $board->board_name }} 게시글 목록
            <span class="badge bg-primary">{{ $posts->total() }}</span>
        </h5>
        <div>
            @if($posts->count() > 0)
            <button type="button"
                    class="btn btn-danger btn-sm me-2"
                    id="bulkDelete"
                    data-url="{{ route('manager.posts.bulk-delete', $board->board_code) }}">
                <i class="fas fa-trash"></i> 선택 삭제
            </button>
            @endif
            <a href="{{ route('manager.posts.create', $board->board_code) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> 게시글 추가
            </a>
        </div>
    </div>

    <div class="card-body">
        @if($posts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>번호</th>
                            <th>제목</th>
                            <th>작성자</th>
                            <th>조회수</th>
                            <th>작성일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $index => $post)
                        <tr>
                            <td>
                                <input type="checkbox" class="item-checkbox" value="{{ $post->id }}">
                            </td>
                            <td>
                                @if($post->is_notice)
                                    <i class="fas fa-bullhorn text-warning"></i>
                                @else
                                    {{ $posts->total() - $posts->firstItem() - $index + 1 }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('manager.posts.show', [$board->board_code, $post]) }}" class="text-decoration-none">
                                    {{ $post->title }}
                                </a>
                                @if($post->is_notice)
                                    <span class="badge bg-warning text-dark ms-1">공지</span>
                                @endif
                                @if($post->is_secret)
                                    <span class="badge bg-secondary ms-1" title="비밀">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">비밀</span>
                                    </span>
                                @endif
                                @if($post->files->count() > 0)
                                    <i class="fas fa-paperclip text-muted ms-1"></i>
                                @endif
                                @if($post->comments->count() > 0)
                                    <span class="badge bg-info ms-1">{{ $post->comments->count() }}</span>
                                @endif
                            </td>
                            <td>{{ $post->author_name }}</td>
                            <td>{{ number_format($post->view_count) }}</td>
                            <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manager.posts.show', [$board->board_code, $post]) }}"
                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="상세보기">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('manager.posts.edit', [$board->board_code, $post]) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="수정">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('manager.posts.destroy', [$board->board_code, $post]) }}"
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
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="d-flex justify-content-center">
                {{ $posts->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    @if(request()->hasAny(['search', 'is_notice', 'is_secret', 'date_from', 'date_to']))
                        검색 조건에 맞는 게시글이 없습니다.
                    @else
                        등록된 게시글이 없습니다.
                    @endif
                </h5>
                @if(request()->hasAny(['search', 'is_notice', 'is_secret', 'date_from', 'date_to']))
                    <a href="{{ route('manager.posts.index', $board->board_code) }}" class="btn btn-primary mt-3">
                        <i class="fas fa-redo"></i> 전체 게시글 보기
                    </a>
                @else
                    <a href="{{ route('manager.posts.create', $board->board_code) }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> 첫 번째 게시글 추가
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- 게시판 정보 -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary">{{ $posts->total() }}</h4>
                <small class="text-muted">총 게시글</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning">{{ $posts->where('is_notice', true)->count() }}</h4>
                <small class="text-muted">공지사항</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success">{{ \App\Models\BoardPost::where('board_id', $board->id)->whereDate('created_at', today())->count() }}</h4>
                <small class="text-muted">오늘 게시글</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info">{{ \App\Models\Comment::whereHas('post', function($q) use ($board) { $q->where('board_id', $board->id); })->count() }}</h4>
                <small class="text-muted">총 댓글</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 일괄 삭제
    $('#bulkDelete').click(function() {
        var selected = $('.item-checkbox:checked');
        if (selected.length === 0) {
            alert('삭제할 게시글을 선택해주세요.');
            return;
        }

        if (confirm(selected.length + '개 게시글을 삭제하시겠습니까?')) {
            var ids = [];
            selected.each(function() {
                ids.push($(this).val());
            });

            var form = $('<form>', {
                'method': 'POST',
                'action': $(this).data('url')
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
});
</script>
@endpush
