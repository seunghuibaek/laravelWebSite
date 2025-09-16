@extends('front.layouts.app')

@section('title', '글쓰기 - ' . $board->board_name)

@push('styles')
@if($board->use_editor)
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
@endif
@endpush

@section('content')
<!-- Board Header -->
<section class="board-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">홈</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('board.index', $board->board_code) }}" class="text-white">{{ $board->board_name }}</a></li>
                        <li class="breadcrumb-item active text-white">글쓰기</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold">
                    <i class="fas fa-pen me-3"></i>글쓰기
                </h1>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>새 글 작성
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('board.store', $board->board_code) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="author_name" class="form-label">작성자 <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('author_name') is-invalid @enderror"
                                           id="author_name"
                                           name="author_name"
                                           value="{{ old('author_name', auth()->check() ? auth()->user()->name : '') }}"
                                           {{ auth()->check() ? 'readonly' : '' }}
                                           required>
                                    @error('author_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @auth
                                        <div class="form-text">로그인된 사용자 정보가 자동으로 입력됩니다.</div>
                                    @endauth
                                </div>

                                <div class="col-md-6">
                                    <label for="author_email" class="form-label">이메일</label>
                                    <input type="email"
                                           class="form-control @error('author_email') is-invalid @enderror"
                                           id="author_email"
                                           name="author_email"
                                           value="{{ old('author_email', auth()->check() ? auth()->user()->email : '') }}"
                                           {{ auth()->check() ? 'readonly' : '' }}>
                                    @error('author_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @auth
                                        <div class="form-text">로그인된 사용자 정보가 자동으로 입력됩니다.</div>
                                    @endauth
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-{{ auth()->check() ? '12' : '8' }}">
                                    <label for="title" class="form-label">제목 <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title') }}"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @guest
                                <div class="col-md-4">
                                    <label for="password" class="form-label">비밀번호 <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="수정/삭제 시 필요"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endguest
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">내용 <span class="text-danger">*</span></label>
                                @if($board->use_editor)
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                              id="content"
                                              name="content"
                                              rows="15">{{ old('content') }}</textarea>
                                @else
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                              id="content"
                                              name="content"
                                              rows="15"
                                              placeholder="내용을 입력하세요..."
                                              required>{{ old('content') }}</textarea>
                                @endif
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($board->use_file_upload)
                            <div class="mb-3">
                                <label for="files" class="form-label">
                                    파일 첨부
                                    <small class="text-muted">
                                        (최대 {{ $board->max_file_count }}개, 개당 {{ number_format($board->max_file_size) }}KB 이하)
                                    </small>
                                </label>
                                <input type="file"
                                       class="form-control @error('files.*') is-invalid @enderror"
                                       id="files"
                                       name="files[]"
                                       multiple
                                       accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                @error('files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="file-preview mt-2"></div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_secret"
                                           id="is_secret"
                                           value="1"
                                           {{ old('is_secret') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_secret">
                                        비밀글로 설정
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('board.index', $board->board_code) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>취소
                                </a>
                                <button type="submit" class="btn btn-primary" data-original-text="등록">
                                    <i class="fas fa-save me-2"></i>등록
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if($board->use_editor)
<!-- Froala Editor JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/languages/ko.js"></script>

<script>
$(document).ready(function() {
    // Froala Editor 초기화
    new FroalaEditor('#content', {
        language: 'ko',
        height: 400,
        toolbarButtons: {
            'moreText': {
                'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']
            },
            'moreParagraph': {
                'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']
            },
            'moreRich': {
                'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']
            },
            'moreMisc': {
                'buttons': ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help']
            }
        },
        imageUploadURL: '/upload-image',
        imageUploadParams: {
            _token: $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>
@endif
@endpush
