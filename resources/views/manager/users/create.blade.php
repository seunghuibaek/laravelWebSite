@extends('manager.layouts.app')

@section('title', '회원 생성')
@section('page-title', '회원 생성')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0"><i class="fas fa-user-plus"></i> 새 회원 추가</h6>
        <a href="{{ route('manager.users.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
            <i class="fas fa-list"></i> 목록
        </a>
    </div>
    <form method="POST" action="{{ route('manager.users.store') }}">
        @csrf
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">이름</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">이메일</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">비밀번호</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">비밀번호 확인</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
        </div>
    </form>
</div>
@endsection
