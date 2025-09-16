@extends('manager.layouts.app')

@section('title', '관리자 관리')
@section('page-title', '관리자 관리')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users-cog"></i> 관리자 목록</h5>
        <a href="{{ route('manager.managers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 관리자 추가
        </a>
    </div>
    
    <div class="card-body">
        @if($managers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>아이디</th>
                            <th>이름</th>
                            <th>이메일</th>
                            <th>전화번호</th>
                            <th>권한</th>
                            <th>상태</th>
                            <th>최근 로그인</th>
                            <th>등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managers as $manager)
                        <tr>
                            <td>
                                <input type="checkbox" class="item-checkbox" value="{{ $manager->id }}">
                            </td>
                            <td>
                                <strong>{{ $manager->username }}</strong>
                                @if($manager->id === auth('manager')->id())
                                    <span class="badge bg-info">본인</span>
                                @endif
                            </td>
                            <td>{{ $manager->name }}</td>
                            <td>{{ $manager->email }}</td>
                            <td>{{ $manager->phone ?? '-' }}</td>
                            <td>
                                @if($manager->role === 'super_admin')
                                    <span class="badge bg-danger">최고관리자</span>
                                @elseif($manager->role === 'admin')
                                    <span class="badge bg-warning">관리자</span>
                                @else
                                    <span class="badge bg-secondary">매니저</span>
                                @endif
                            </td>
                            <td>
                                @if($manager->status === 'active')
                                    <span class="badge bg-success">활성</span>
                                @else
                                    <span class="badge bg-secondary">비활성</span>
                                @endif
                            </td>
                            <td>
                                {{ $manager->last_login_at ? $manager->last_login_at->format('Y-m-d H:i') : '없음' }}
                            </td>
                            <td>{{ $manager->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manager.managers.show', $manager) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="상세보기">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('manager.managers.edit', $manager) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="수정">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($manager->id !== auth('manager')->id())
                                        <form action="{{ route('manager.managers.destroy', $manager) }}" 
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
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- 페이지네이션 -->
            <div class="d-flex justify-content-center">
                {{ $managers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">등록된 관리자가 없습니다.</h5>
                <a href="{{ route('manager.managers.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> 첫 번째 관리자 추가
                </a>
            </div>
        @endif
    </div>
</div>

@if($managers->count() > 0)
<!-- 일괄 작업 버튼 -->
<div class="mt-3">
    <button type="button" 
            class="btn btn-danger" 
            id="bulkDelete" 
            data-url="{{ route('manager.managers.index') }}">
        <i class="fas fa-trash"></i> 선택 삭제
    </button>
</div>
@endif
@endsection