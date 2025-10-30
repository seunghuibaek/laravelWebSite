// 관리자 페이지 공통 JavaScript

$(document).ready(function() {
    // 사이드바 토글
    $('#sidebarToggle').click(function() {
        $('.sidebar').toggleClass('show');
        $('.main-content').toggleClass('sidebar-open');
    });

    // 알림 자동 숨김
    $('.alert').each(function() {
        var alert = $(this);
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    });

    // 삭제 확인
    $('.btn-delete').click(function(e) {
        if (!confirm('정말 삭제하시겠습니까?')) {
            e.preventDefault();
        }
    });

    // 테이블 체크박스 전체 선택
    $('#selectAll').change(function() {
        $('.item-checkbox').prop('checked', this.checked);
    });

    // 개별 체크박스 변경 시 전체 선택 체크박스 상태 업데이트
    $('.item-checkbox').change(function() {
        var total = $('.item-checkbox').length;
        var checked = $('.item-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });

    // 일괄 삭제
    $('#bulkDelete').click(function() {
        var selected = $('.item-checkbox:checked');
        if (selected.length === 0) {
            alert('삭제할 항목을 선택해주세요.');
            return;
        }

        if (confirm(selected.length + '개 항목을 삭제하시겠습니까?')) {
            var ids = [];
            selected.each(function() {
                ids.push($(this).val());
            });

            // 일괄 삭제 폼 생성 및 제출
            var form = $('<form>', {
                'method': 'POST',
                'action': $(this).data('url')
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': $('meta[name="csrf-token"]').attr('content')
            }));

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_method',
                'value': 'DELETE'
            }));

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'ids',
                'value': ids.join(',')
            }));

            $('body').append(form);
            form.submit();
        }
    });

    // 툴팁 초기화
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
// 모달 초기화
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
});
