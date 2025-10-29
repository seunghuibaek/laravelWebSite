@extends('front.layouts.app')

@section('title', '글 수정 - ' . $board->board_name)

@push('styles')
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
                        <li class="breadcrumb-item"><a href="{{ route('board.show', [$board->board_code, $post]) }}" class="text-white">{{ Str::limit($post->title, 20) }}</a></li>
                        <li class="breadcrumb-item active text-white">수정</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold">
                    <i class="fas fa-edit me-3"></i>글 수정
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
                            <i class="fas fa-edit me-2"></i>글 수정
                        </h5>
                    </div>

                    <div class="card-body">
                        <!-- Password Check Form -->
                        <div id="passwordCheck" class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-lock me-2"></i>
                                글을 수정하려면 작성 시 입력한 비밀번호를 입력해주세요.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="checkPassword" placeholder="비밀번호 입력">
                                        <button type="button" class="btn btn-primary" onclick="checkPassword()">
                                            <i class="fas fa-check me-2"></i>확인
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form (Initially Hidden) -->
                        <div id="editForm" style="display: none;">
                            <form action="{{ route('board.update', [$board->board_code, $post]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="password" id="confirmedPassword">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="author_name" class="form-label">작성자 <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('author_name') is-invalid @enderror"
                                               id="author_name"
                                               name="author_name"
                                               value="{{ old('author_name', $post->author_name) }}"
                                               required>
                                        @error('author_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="author_email" class="form-label">이메일</label>
                                        <input type="email"
                                               class="form-control @error('author_email') is-invalid @enderror"
                                               id="author_email"
                                               name="author_email"
                                               value="{{ old('author_email', $post->author_email) }}">
                                        @error('author_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">제목 <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title', $post->title) }}"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">내용 <span class="text-danger">*</span></label>
                                    @if($board->use_editor)
                                        <textarea class="form-control @error('content') is-invalid @enderror"
                                                  id="content"
                                                  name="content"
                                                  rows="15">{{ old('content', $post->content) }}</textarea>
                                    @else
                                        <textarea class="form-control @error('content') is-invalid @enderror"
                                                  id="content"
                                                  name="content"
                                                  rows="15"
                                                  placeholder="내용을 입력하세요..."
                                                  required>{{ old('content', $post->content) }}</textarea>
                                    @endif
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Existing Files -->
                                @if($post->files->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">기존 첨부파일</label>
                                    <div class="file-list">
                                        @foreach($post->files as $file)
                                        <div class="file-item">
                                            <div class="file-icon">
                                                @if($file->isImage())
                                                    <i class="fas fa-image text-info"></i>
                                                @else
                                                    <i class="fas fa-file text-muted"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="{{ Storage::url($file->file_path) }}"
                                                   target="_blank"
                                                   class="text-decoration-none">
                                                    {{ $file->original_name }}
                                                </a>
                                                <small class="text-muted ms-2">
                                                    ({{ $file->getFileSizeFormatted() }})
                                                </small>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFile({{ $file->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($board->use_file_upload)
                                <div class="mb-3">
                                    <label for="files" class="form-label">
                                        새 파일 첨부
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
                                               {{ old('is_secret', $post->is_secret) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_secret">
                                            비밀글로 설정
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('board.show', [$board->board_code, $post]) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>취소
                                    </a>
                                    <button type="submit" class="btn btn-primary" data-original-text="수정">
                                        <i class="fas fa-save me-2"></i>수정
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if($board->use_editor)
<!-- CKEditor 5 Classic -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.querySelector('#content');
        if (el) {
            ClassicEditor.create(el, {
                language: 'ko'
            }).catch(function (error) {
                console.error(error);
            });
        }
    });
</script>
@endif

<script>
let ckeditorInstance = null;

function checkPassword() {
    const password = $('#checkPassword').val();

    if (!password) {
        alert('비밀번호를 입력해주세요.');
        return;
    }

    // 실제로는 서버에서 비밀번호를 확인해야 하지만,
    // 여기서는 간단히 클라이언트에서 처리
    $.ajax({
        url: '{{ route("board.check-password", [$board->board_code, $post]) }}',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            password: password
        },
        success: function(response) {
            if (response.success) {
                $('#passwordCheck').hide();
                $('#editForm').show();
                $('#confirmedPassword').val(password);

                @if($board->use_editor)
                ClassicEditor.create(document.querySelector('#content'), { language: 'ko' })
                    .then(editor => { ckeditorInstance = editor; })
                    .catch(error => console.error(error));
                @endif
            } else {
                alert('비밀번호가 일치하지 않습니다.');
            }
        },
        error: function() {
            alert('오류가 발생했습니다.');
        }
    });
}

function deleteFile(fileId) {
    if (confirm('이 파일을 삭제하시겠습니까?')) {
        $.ajax({
            url: `/files/${fileId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('파일 삭제에 실패했습니다.');
                }
            },
            error: function() {
                alert('오류가 발생했습니다.');
            }
        });
    }
}

$(document).ready(function() {
    // 엔터키로 비밀번호 확인
    $('#checkPassword').keypress(function(e) {
        if (e.which === 13) {
            checkPassword();
        }
    });

    // 에러가 있으면 폼을 바로 표시
    @if($errors->any())
        $('#passwordCheck').hide();
        $('#editForm').show();
        $('#confirmedPassword').val('{{ old("password") }}');

        @if($board->use_editor)
        // Froala Editor 초기화
        froalaEditor = new FroalaEditor('#content', {
            language: 'ko',
            height: 400,
            // ... 설정은 위와 동일
        });
        @endif
    @endif
});
</script>
@endpush
