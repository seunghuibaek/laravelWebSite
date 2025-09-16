@extends('manager.layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">배너 등록</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('manager.banners.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">제목</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">이미지</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <div class="form-text">권장 크기: 1600x600 등 메인 배너 비율</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">링크 URL</label>
                    <input type="url" name="link_url" class="form-control" value="{{ old('link_url') }}" placeholder="https://example.com">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">노출여부</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>ON</option>
                            <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>OFF</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">정렬 순서</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">시작일시</label>
                        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">종료일시</label>
                        <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">저장</button>
                    <a href="{{ route('manager.banners.index') }}" class="btn btn-secondary">목록</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
