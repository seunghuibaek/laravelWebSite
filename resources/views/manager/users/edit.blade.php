@extends('manager.layouts.app')

@section('title', '회원 수정')
@section('page-title', '회원 수정')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0"><i class="fas fa-user-edit"></i> 회원 수정</h6>
        <div class="ms-auto d-flex gap-2">
            <form method="POST" action="{{ route('manager.users.destroy', $user) }}" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> 삭제</button>
            </form>
            <a href="{{ route('manager.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-list"></i> 목록
            </a>
        </div>
    </div>
    <form method="POST" action="{{ route('manager.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">이름</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">이메일</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">비밀번호 변경 (선택)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">비밀번호 확인</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">SNS 제공자</label>
                <div class="form-control-plaintext">
                    @if($user->provider)
                        <span class="badge bg-primary text-uppercase">{{ $user->provider }}</span> (ID: {{ $user->provider_id }})
                    @else
                        <span class="text-muted">일반 가입</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <div class="d-flex gap-2">
                <a href="{{ route('manager.users.index') }}" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
            </div>
        </div>
    </form>
</div>
@endsection
