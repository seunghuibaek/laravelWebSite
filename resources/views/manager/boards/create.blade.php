@extends('manager.layouts.app')

@section('title', '게시판 추가')
@section('page-title', '게시판 추가')

@push('styles')
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> 새 게시판 등록</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('manager.boards.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="board_code" class="form-label">게시판 코드 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('board_code') is-invalid @enderror"
                                   id="board_code"
                                   name="board_code"
                                   value="{{ old('board_code') }}"
                                   placeholder="영문, 숫자, 언더스코어만 사용"
                                   required>
                            @error('board_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">URL에 사용되는 고유 식별자입니다.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="board_name" class="form-label">게시판명 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('board_name') is-invalid @enderror"
                                   id="board_name"
                                   name="board_name"
                                   value="{{ old('board_name') }}"
                                   required>
                            @error('board_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="board_type" class="form-label">게시판 타입 <span class="text-danger">*</span></label>
                            <select class="form-select @error('board_type') is-invalid @enderror"
                                    id="board_type"
                                    name="board_type"
                                    required>
                                <option value="">타입을 선택하세요</option>
                                <option value="normal" {{ old('board_type') === 'normal' ? 'selected' : '' }}>일반 게시판</option>
                                <option value="gallery" {{ old('board_type') === 'gallery' ? 'selected' : '' }}>갤러리 게시판</option>
                            </select>
                            @error('board_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="upload_folder" class="form-label">파일 업로드 폴더명</label>
                            <input type="text"
                                   class="form-control @error('upload_folder') is-invalid @enderror"
                                   id="upload_folder"
                                   name="upload_folder"
                                   value="{{ old('upload_folder') }}"
                                   placeholder="비워두면 게시판 코드로 자동 설정">
                            @error('upload_folder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_file_count" class="form-label">파일 업로드 개수 <span class="text-danger">*</span></label>
                            <select class="form-select @error('max_file_count') is-invalid @enderror"
                                    id="max_file_count"
                                    name="max_file_count"
                                    required>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('max_file_count', 1) == $i ? 'selected' : '' }}>{{ $i }}개</option>
                                @endfor
                            </select>
                            @error('max_file_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_file_size" class="form-label">업로드 파일 크기 제한 (KB) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('max_file_size') is-invalid @enderror"
                                   id="max_file_size"
                                   name="max_file_size"
                                   value="{{ old('max_file_size', 10240) }}"
                                   min="1"
                                   max="102400"
                                   required>
                            @error('max_file_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">1KB ~ 100MB (102400KB)</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">정렬 순서 <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0"
                                   required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">숫자가 작을수록 먼저 표시됩니다.</small>
                        </div>
                    </div>

                    <!-- 기능 설정 -->
                    <div class="mb-4">
                        <h6><i class="fas fa-cog"></i> 기능 설정</h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="use_notice"
                                           name="use_notice"
                                           value="1"
                                           {{ old('use_notice') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_notice">
                                        공지사항 사용
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="use_file_upload"
                                           name="use_file_upload"
                                           value="1"
                                           {{ old('use_file_upload') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_file_upload">
                                        파일 업로드 사용
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="use_editor"
                                           name="use_editor"
                                           value="1"
                                           {{ old('use_editor') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_editor">
                                        에디터 사용 (CKEditor 5)
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="use_comment"
                                           name="use_comment"
                                           value="1"
                                           {{ old('use_comment') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_comment">
                                        댓글 사용
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="allow_user_write"
                                           name="allow_user_write"
                                           value="1"
                                           {{ old('allow_user_write', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_user_write">
                                        사용자 글등록 허용
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                게시판 활성화
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="memo" class="form-label">메모</label>
                        <textarea class="form-control @error('memo') is-invalid @enderror"
                                  id="memo"
                                  name="memo"
                                  rows="3"
                                  placeholder="게시판에 대한 설명이나 메모를 입력하세요">{{ old('memo') }}</textarea>
                        @error('memo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.boards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> 취소
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 등록
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
$(document).ready(function() {
    // 게시판 코드 자동 생성
    $('#board_name').on('input', function() {
        var boardName = $(this).val();
        var boardCode = boardName.toLowerCase()
            .replace(/[^a-z0-9가-힣]/g, '_')
            .replace(/_{2,}/g, '_')
            .replace(/^_|_$/g, '');

        if ($('#board_code').val() === '') {
            $('#board_code').val(boardCode);
        }
    });

    // 에디터 미리보기
    $('#use_editor').change(function() {
        if ($(this).is(':checked')) {
            if (!$('#editor-preview').length) {
                $('#memo').after('<div id="editor-preview" class="mt-2"><small class="text-info"><i class="fas fa-info-circle"></i> CKEditor 5가 적용됩니다.</small></div>');
            }
        } else {
            $('#editor-preview').remove();
        }
    });
});
</script>
@endpush
