@extends('manager.layouts.app')

@section('title', '회원관리')
@section('page-title', '회원관리')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0"><i class="fas fa-users"></i> 회원 목록</h6>
        <form method="GET" class="ms-auto d-flex" action="{{ route('manager.users.index') }}">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="이름/이메일 검색">
            <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        </form>
        <a href="{{ route('manager.users.create') }}" class="btn btn-sm btn-primary ms-2"><i class="fas fa-user-plus"></i> 추가</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>이름</th>
                        <th>이메일</th>
                        <th>SNS</th>
                        <th>가입일</th>
                        <th class="text-end">관리</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td class="d-flex align-items-center gap-2">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="avatar" width="28" height="28" class="rounded-circle">
                            @endif
                            {{ $user->name }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->provider)
                                <span class="badge bg-primary text-uppercase">{{ $user->provider }}</span>
                            @else
                                <span class="text-muted">일반</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('manager.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> 수정</a>
                            <form action="{{ route('manager.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> 삭제</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">회원이 없습니다.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
