@extends('manager.layouts.app')

@section('title', '새 설정 추가')
@section('page-title', '새 설정 추가')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> 새 시스템 설정 추가</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('manager.settings.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="key" class="form-label">설정 키 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('key') is-invalid @enderror"
                                   id="key"
                                   name="key"
                                   value="{{ old('key') }}"
                                   placeholder="예: site_name"
                                   required>
                            @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">영문, 숫자, 언더스코어만 사용 가능합니다.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="label" class="form-label">설정 이름 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('label') is-invalid @enderror"
                                   id="label"
                                   name="label"
                                   value="{{ old('label') }}"
                                   placeholder="예: 사이트 이름"
                                   required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">설정 타입 <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type"
                                    required>
                                <option value="">타입을 선택하세요</option>
                                <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>텍스트</option>
                                <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>숫자</option>
                                <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>참/거짓</option>
                                <option value="json" {{ old('type') === 'json' ? 'selected' : '' }}>JSON</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="group" class="form-label">설정 그룹 <span class="text-danger">*</span></label>
                            <select class="form-select @error('group') is-invalid @enderror"
                                    id="group"
                                    name="group"
                                    required>
                                <option value="">그룹을 선택하세요</option>
                                <option value="general" {{ old('group') === 'general' ? 'selected' : '' }}>일반 설정</option>
                                <option value="board" {{ old('group') === 'board' ? 'selected' : '' }}>게시판 설정</option>
                                <option value="security" {{ old('group') === 'security' ? 'selected' : '' }}>보안 설정</option>
                                <option value="mail" {{ old('group') === 'mail' ? 'selected' : '' }}>메일 설정</option>
                                <option value="custom" {{ old('group') === 'custom' ? 'selected' : '' }}>사용자 정의</option>
                            </select>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3" id="value-container">
                        <label for="value" class="form-label">기본값</label>
                        <input type="text"
                               class="form-control @error('value') is-invalid @enderror"
                               id="value"
                               name="value"
                               value="{{ old('value') }}">
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" id="value-help">설정의 기본값을 입력하세요.</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">설명</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="설정에 대한 설명을 입력하세요">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> 취소
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 추가
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
    // 타입 변경 시 입력 필드 업데이트
    $('#type').change(function() {
        const type = $(this).val();
        const container = $('#value-container');
        const helpText = $('#value-help');

        // 기존 입력 필드 제거
        container.find('input, textarea, select').remove();

        let inputHtml = '';
        let helpTextContent = '';

        switch(type) {
            case 'text':
                inputHtml = '<input type="text" class="form-control" id="value" name="value" value="{{ old("value") }}">';
                helpTextContent = '텍스트 값을 입력하세요.';
                break;
            case 'number':
                inputHtml = '<input type="number" class="form-control" id="value" name="value" value="{{ old("value") }}">';
                helpTextContent = '숫자 값을 입력하세요.';
                break;
            case 'boolean':
                inputHtml = `
                    <select class="form-select" id="value" name="value">
                        <option value="true">참 (true)</option>
                        <option value="false">거짓 (false)</option>
                    </select>
                `;
                helpTextContent = '참 또는 거짓을 선택하세요.';
                break;
            case 'json':
                inputHtml = '<textarea class="form-control" id="value" name="value" rows="4">{{ old("value") }}</textarea>';
                helpTextContent = 'JSON 형식으로 입력하세요. 예: {"key": "value"}';
                break;
            default:
                inputHtml = '<input type="text" class="form-control" id="value" name="value" value="{{ old("value") }}">';
                helpTextContent = '설정의 기본값을 입력하세요.';
        }

        // 새 입력 필드 추가
        helpText.before(inputHtml);
        helpText.text(helpTextContent);
    });

    // 키 자동 생성
    $('#label').on('input', function() {
        const label = $(this).val();
        const key = label.toLowerCase()
            .replace(/[^a-z0-9가-힣\s]/g, '')
            .replace(/\s+/g, '_')
            .replace(/_{2,}/g, '_')
            .replace(/^_|_$/g, '');
        let $key = $('key');
        if ($key.val() === '') {
            $key.val(key);
        }
    });
});
</script>
@endpush
