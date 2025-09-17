@extends('manager.layouts.app')

@section('title', '시스템 설정')
@section('page-title', '시스템 설정')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.settings.update') }}" method="POST">
            @csrf

            @foreach($settings as $group => $groupSettings)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog"></i>
                        {{ ucfirst($group) === 'General' ? '일반 설정' :
                           (ucfirst($group) === 'Board' ? '게시판 설정' :
                           (ucfirst($group) === 'Security' ? '보안 설정' :
                           (strtolower($group) === 'sns' ? 'SNS 설정' : ucfirst($group)))) }}
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($groupSettings as $setting)
                    <div class="mb-3">
                        <label for="setting_{{ $setting->key }}" class="form-label">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="fas fa-info-circle text-muted ms-1"
                                   data-bs-toggle="tooltip"
                                   title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="setting_{{ $setting->key }}"
                                       name="settings[{{ $setting->key }}]"
                                       value="true"
                                       {{ $setting->getCastedValue() ? 'checked' : '' }}>
                                <label class="form-check-label" for="setting_{{ $setting->key }}">
                                    {{ $setting->getCastedValue() ? '사용' : '사용 안함' }}
                                </label>
                            </div>
                        @elseif($setting->type === 'number')
                            <input type="number"
                                   class="form-control"
                                   id="setting_{{ $setting->key }}"
                                   name="settings[{{ $setting->key }}]"
                                   value="{{ $setting->value }}">
                        @elseif($setting->type === 'json')
                            <textarea class="form-control"
                                      id="setting_{{ $setting->key }}"
                                      name="settings[{{ $setting->key }}]"
                                      rows="4">{{ $setting->value }}</textarea>
                            <small class="form-text text-muted">JSON 형식으로 입력하세요.</small>
                        @else
                            <input type="text"
                                   class="form-control"
                                   id="setting_{{ $setting->key }}"
                                   name="settings[{{ $setting->key }}]"
                                   value="{{ $setting->value }}">
                        @endif

                        @if($setting->description)
                            <small class="form-text text-muted">{{ $setting->description }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 설정 저장
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- 설정 관리 -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-plus"></i> 설정 관리</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('manager.settings.create') }}" class="btn btn-success w-100 mb-3">
                    <i class="fas fa-plus"></i> 새 설정 추가
                </a>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> 설정 타입</h6>
                    <ul class="mb-0 small">
                        <li><strong>text:</strong> 일반 텍스트</li>
                        <li><strong>number:</strong> 숫자</li>
                        <li><strong>boolean:</strong> 참/거짓</li>
                        <li><strong>json:</strong> JSON 데이터</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 현재 설정 요약 -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list"></i> 설정 요약</h6>
            </div>
            <div class="card-body">
                @foreach($settings as $group => $groupSettings)
                <div class="mb-3">
                    <h6 class="text-primary">{{ ucfirst($group) }}</h6>
                    <ul class="list-unstyled small">
                        @foreach($groupSettings as $setting)
                        <li class="d-flex justify-content-between align-items-center mb-1">
                            <span>{{ $setting->label }}</span>
                            <div>
                                @if($setting->type === 'boolean')
                                    @if($setting->getCastedValue())
                                        <span class="badge bg-success">ON</span>
                                    @else
                                        <span class="badge bg-secondary">OFF</span>
                                    @endif
                                @else
                                    <span class="text-muted">{{ Str::limit($setting->value, 15) }}</span>
                                @endif

                                <form action="{{ route('manager.settings.destroy', $setting) }}"
                                      method="POST"
                                      class="d-inline ms-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger btn-delete"
                                            data-bs-toggle="tooltip"
                                            title="삭제">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 스위치 토글 시 라벨 업데이트
    $('.form-check-input[type="checkbox"]').change(function() {
        const label = $(this).siblings('.form-check-label');
        if ($(this).is(':checked')) {
            label.text('사용');
        } else {
            label.text('사용 안함');
        }
    });

    // JSON 유효성 검사
    $('textarea[name*="settings"]').each(function() {
        const settingKey = $(this).attr('name').match(/\[(.*?)\]/)[1];
        const setting = @json($settings->flatten()->keyBy('key'));

        if (setting[settingKey] && setting[settingKey].type === 'json') {
            $(this).on('blur', function() {
                try {
                    JSON.parse($(this).val());
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                } catch (e) {
                    $(this).addClass('is-invalid');
                    if (!$(this).siblings('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">유효한 JSON 형식이 아닙니다.</div>');
                    }
                }
            });
        }
    });
});
</script>
@endpush
