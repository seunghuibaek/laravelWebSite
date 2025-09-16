@extends('manager.layouts.app')

@section('title', $post->title . ' 수정 - ' . $board->board_name)
@section('page-title', $board->board_name . ' 게시글 수정')

@push('styles')
@if($board->use_editor)
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
@endif
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> {{ $board->board_name }} 게시글 수정</h5>
            </div>
            
            <div class="card-body">
                <form action="{{ route('manager.posts.update', [$board->board_code, $post]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                    
                    <!-- 기존 첨부파일 -->
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
                    
                    <div class="row mb-4">
                        @if($board->use_notice)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_notice" 
                                       id="is_notice" 
                                       value="1"
                                       {{ old('is_notice', $post->is_notice) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_notice">
                                    공지사항으로 설정
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
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
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.posts.show', [$board->board_code, $post]) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> 취소
                        </a>
                        <button type="submit" class="btn btn-primary" data-original-text="수정">
                            <i class="fas fa-save"></i> 수정
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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

<script>
function deleteFile(fileId) {
    if (confirm('이 파일을 삭제하시겠습니까?')) {
        $.ajax({
            url: `/manager/files/${fileId}`,
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
</script>
@endpush