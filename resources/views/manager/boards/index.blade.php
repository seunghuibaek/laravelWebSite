@extends('manager.layouts.app')

@section('title', '게시판 관리')
@section('page-title', '게시판 관리')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> 게시판 목록</h5>
        <a href="{{ route('manager.boards.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 게시판 추가
        </a>
    </div>
    
    <div class="card-body">
        @if($boards->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>순서</th>
                            <th>게시판 코드</th>
                            <th>게시판명</th>
                            <th>타입</th>
                            <th>게시글 수</th>
                            <th>기능</th>
                            <th>상태</th>
                            <th>등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boards as $board)
                        <tr>
                            <td>
                                <input type="checkbox" class="item-checkbox" value="{{ $board->id }}">
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $board->sort_order }}</span>
                            </td>
                            <td>
                                <code>{{ $board->board_code }}</code>
                            </td>
                            <td>
                                <strong>{{ $board->board_name }}</strong>
                                @if($board->memo)
                                    <br><small class="text-muted">{{ Str::limit($board->memo, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($board->board_type === 'gallery')
                                    <span class="badge bg-info">갤러리</span>
                                @else
                                    <span class="badge bg-secondary">일반</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $board->posts()->count() }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($board->use_notice)
                                        <span class="badge bg-warning text-dark" title="공지사항">공지</span>
                                    @endif
                                    @if($board->use_file_upload)
                                        <span class="badge bg-success" title="파일업로드">파일</span>
                                    @endif
                                    @if($board->use_editor)
                                        <span class="badge bg-info" title="에디터">에디터</span>
                                    @endif
                                    @if($board->use_comment)
                                        <span class="badge bg-primary" title="댓글">댓글</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($board->is_active)
                                    <span class="badge bg-success">활성</span>
                                @else
                                    <span class="badge bg-secondary">비활성</span>
                                @endif
                            </td>
                            <td>{{ $board->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manager.boards.show', $board) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="상세보기">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('manager.boards.edit', $board) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="수정">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('manager.boards.destroy', $board) }}" 
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
                {{ $boards->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">등록된 게시판이 없습니다.</h5>
                <a href="{{ route('manager.boards.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> 첫 번째 게시판 추가
                </a>
            </div>
        @endif
    </div>
</div>

@if($boards->count() > 0)
<!-- 일괄 작업 버튼 -->
<div class="mt-3">
    <button type="button" 
            class="btn btn-danger" 
            id="bulkDelete" 
            data-url="{{ route('manager.boards.index') }}">
        <i class="fas fa-trash"></i> 선택 삭제
    </button>
</div>
@endif
@endsection