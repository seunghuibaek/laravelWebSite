@extends('front.layouts.app')

@section('title', '문의하기')
@section('description', '궁금한 점이나 문의사항이 있으시면 언제든지 연락주세요.')

@section('content')
<!-- Header -->
<section class="board-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-envelope me-3"></i>문의하기
                </h1>
                <p class="lead">궁금한 점이나 문의사항이 있으시면 언제든지 연락주세요.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-3x text-primary"></i>
                        </div>
                        <h5>이메일</h5>
                        <p class="text-muted">{{ \App\Models\SystemSetting::get('admin_email', 'admin@example.com') }}</p>
                        <a href="mailto:{{ \App\Models\SystemSetting::get('admin_email', 'admin@example.com') }}" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane me-2"></i>메일 보내기
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-3x text-success"></i>
                        </div>
                        <h5>전화</h5>
                        <p class="text-muted">{{ \App\Models\SystemSetting::get('contact_phone', '02-1234-5678') }}</p>
                        <a href="tel:{{ \App\Models\SystemSetting::get('contact_phone', '02-1234-5678') }}" class="btn btn-outline-success">
                            <i class="fas fa-phone me-2"></i>전화 걸기
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt fa-3x text-info"></i>
                        </div>
                        <h5>주소</h5>
                        <p class="text-muted">{{ \App\Models\SystemSetting::get('contact_address', '서울시 강남구') }}</p>
                        <button type="button" class="btn btn-outline-info">
                            <i class="fas fa-map me-2"></i>지도 보기
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Inquiry Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>온라인 문의
                        </h4>
                        <p class="text-muted mb-0 mt-2">아래 양식을 작성해 주시면 빠른 시일 내에 답변드리겠습니다.</p>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('inquiry.store') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">이름 <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">이메일 <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">전화번호</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="010-1234-5678">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="subject" class="form-label">문의 제목 <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" 
                                           name="subject" 
                                           value="{{ old('subject') }}" 
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">문의 내용 <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="8" 
                                          placeholder="문의하실 내용을 자세히 작성해 주세요..."
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>개인정보 수집 및 이용 안내</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>수집목적:</strong> 문의사항 답변 및 고객 서비스 제공</li>
                                        <li><strong>수집항목:</strong> 이름, 이메일, 전화번호, 문의내용</li>
                                        <li><strong>보유기간:</strong> 문의 처리 완료 후 1년</li>
                                        <li><strong>동의거부권:</strong> 개인정보 수집에 동의하지 않을 수 있으나, 이 경우 문의 서비스 이용이 제한됩니다.</li>
                                    </ul>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy_agree" required>
                                    <label class="form-check-label" for="privacy_agree">
                                        개인정보 수집 및 이용에 동의합니다. <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5" data-original-text="문의 접수">
                                    <i class="fas fa-paper-plane me-2"></i>문의 접수
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">
                    <i class="fas fa-question-circle text-info me-2"></i>자주 묻는 질문
                </h2>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        <i class="fas fa-clock me-2"></i>문의 답변은 얼마나 걸리나요?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        일반적으로 영업일 기준 1-2일 내에 답변드리고 있습니다. 
                                        복잡한 문의의 경우 조금 더 시간이 소요될 수 있습니다.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        <i class="fas fa-envelope me-2"></i>이메일로 직접 문의해도 되나요?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        네, {{ \App\Models\SystemSetting::get('admin_email', 'admin@example.com') }}로 
                                        직접 이메일을 보내셔도 됩니다. 하지만 온라인 문의 양식을 이용하시면 
                                        더 체계적으로 관리되어 빠른 답변을 받으실 수 있습니다.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        <i class="fas fa-shield-alt me-2"></i>개인정보는 안전하게 보호되나요?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        네, 수집된 개인정보는 문의 답변 목적으로만 사용되며, 
                                        관련 법령에 따라 안전하게 보호됩니다. 
                                        문의 처리 완료 후 1년 뒤 자동으로 삭제됩니다.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        <i class="fas fa-phone me-2"></i>전화 상담도 가능한가요?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        네, {{ \App\Models\SystemSetting::get('contact_phone', '02-1234-5678') }}로 
                                        전화 주시면 상담 가능합니다. 
                                        운영시간은 평일 오전 9시부터 오후 6시까지입니다.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 전화번호 자동 포맷팅
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value.length >= 3 && value.length <= 7) {
            value = value.replace(/(\d{3})(\d{1,4})/, '$1-$2');
        } else if (value.length >= 8) {
            value = value.replace(/(\d{3})(\d{4})(\d{1,4})/, '$1-$2-$3');
        }
        $(this).val(value);
    });
    
    // 문자 수 카운터
    $('#message').on('input', function() {
        const maxLength = 1000;
        const currentLength = $(this).val().length;
        
        if (!$('#char-counter').length) {
            $(this).after(`<small id="char-counter" class="form-text text-muted"></small>`);
        }
        
        $('#char-counter').text(`${currentLength}/${maxLength}자`);
        
        if (currentLength > maxLength) {
            $(this).addClass('is-invalid');
            $('#char-counter').addClass('text-danger');
        } else {
            $(this).removeClass('is-invalid');
            $('#char-counter').removeClass('text-danger');
        }
    });
});
</script>
@endpush