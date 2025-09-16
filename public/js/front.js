// 프론트 페이지 공통 JavaScript

$(document).ready(function() {
    // 부드러운 스크롤
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // 네비게이션 스크롤 효과
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.main-header').addClass('scrolled');
        } else {
            $('.main-header').removeClass('scrolled');
        }
    });

    // 카드 호버 효과
    $('.hover-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // 이미지 지연 로딩
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // 툴팁 초기화
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 알림 자동 숨김
    $('.alert').each(function() {
        var alert = $(this);
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    });

    // 검색 폼 개선
    $('.search-form input').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    // 파일 업로드 미리보기
    $('input[type="file"]').change(function() {
        const files = this.files;
        const preview = $(this).siblings('.file-preview');

        if (preview.length && files.length > 0) {
            preview.empty();

            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = $('<img>').attr('src', e.target.result)
                            .addClass('img-thumbnail me-2 mb-2')
                            .css('max-width', '100px');
                        preview.append(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const fileInfo = $('<div>').addClass('file-info p-2 border rounded me-2 mb-2')
                        .html(`<i class="fas fa-file me-2"></i>${file.name}`);
                    preview.append(fileInfo);
                }
            });
        }
    });

    // 댓글 토글
    $('.comment-toggle').click(function() {
        const commentId = $(this).data('comment-id');
        $(`#comment-replies-${commentId}`).toggle();

        const icon = $(this).find('i');
        if (icon.hasClass('fa-chevron-down')) {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });

    // 댓글 답글 폼 토글
    $('.reply-toggle').click(function() {
        const commentId = $(this).data('comment-id');
        $(`#reply-form-${commentId}`).toggle();
    });

    // 좋아요 버튼
    $('.like-btn').click(function() {
        const btn = $(this);
        const postId = btn.data('post-id');

        $.ajax({
            url: `/posts/${postId}/like`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // 카운트/상태 업데이트
                if (typeof response.like_count !== 'undefined') {
                    btn.find('.like-count').text(response.like_count);
                }
                if (response.liked) {
                    btn.addClass('liked');
                } else {
                    btn.removeClass('liked');
                }
                // 메시지 표시 (이미 좋아요, 기타 안내)
                if (response.message) {
                    alert(response.message);
                }
            },
            error: function(jqXHR) {
                try {
                    const data = jqXHR.responseJSON || JSON.parse(jqXHR.responseText);
                    if (jqXHR.status === 401) {
                        alert(data && data.message ? data.message : '로그인이 필요합니다.');
                    } else {
                        alert(data && data.message ? data.message : '오류가 발생했습니다.');
                    }
                } catch (e) {
                    alert('오류가 발생했습니다.');
                }
            }
        });
    });

    // 갤러리 모달
    $('.gallery-item img').click(function() {
        const src = $(this).attr('src');
        const title = $(this).closest('.gallery-item').find('.gallery-title').text();

        const modal = $(`
            <div class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${src}" class="img-fluid" alt="${title}">
                        </div>
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
        modal.modal('show');

        modal.on('hidden.bs.modal', function() {
            modal.remove();
        });
    });

    // 무한 스크롤 (선택사항)
    let loading = false;
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!loading && $('.load-more-btn').length) {
                loading = true;
                $('.load-more-btn').click();
            }
        }
    });

    // 폼 유효성 검사 개선
    $('form').submit(function() {
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>처리중...');

        // 3초 후 버튼 복원 (실패 시를 위해)
        setTimeout(function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(submitBtn.data('original-text') || '전송');
        }, 3000);
    });

    // 뒤로가기 버튼
    $('.btn-back').click(function() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    });
});

// 페이지 로드 애니메이션
window.addEventListener('load', function() {
    document.body.classList.add('loaded');

    // 요소들을 순차적으로 나타나게 하기
    const elements = document.querySelectorAll('.fade-in-up');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// 유틸리티 함수들
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('ko-KR', options);
}

function formatNumber(num) {
    return new Intl.NumberFormat('ko-KR').format(num);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('클립보드에 복사되었습니다.');
    });
}

// 다크모드 토글 (선택사항)
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// 다크모드 설정 복원
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}
