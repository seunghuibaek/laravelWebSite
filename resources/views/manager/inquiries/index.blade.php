@php use App\Models\Inquiry; @endphp
@extends('manager.layouts.app')

@section('title', '문의하기 관리')
@section('page-title', '문의하기 관리')

@section('content')
    <!-- 검색 필터 -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-search"></i> 검색 필터</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('manager.inquiries.index') }}">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">상태</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">전체</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>대기</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>처리중
                            </option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>완료
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">검색어</label>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="제목, 이름, 이메일, 내용">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">시작일</label>
                        <input type="date"
                               class="form-control"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_to" class="form-label">종료일</label>
                        <input type="date"
                               class="form-control"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('manager.inquiries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-envelope"></i> 문의 목록
                <span class="badge bg-primary">{{ $inquiries->total() }}</span>
            </h5>
            @if($inquiries->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i> 일괄 작업
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <button class="dropdown-item" onclick="bulkUpdateStatus('pending')">
                                <i class="fas fa-clock"></i> 대기로 변경
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="bulkUpdateStatus('processing')">
                                <i class="fas fa-spinner"></i> 처리중으로 변경
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="bulkUpdateStatus('completed')">
                                <i class="fas fa-check"></i> 완료로 변경
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <button class="dropdown-item text-danger" onclick="bulkDelete()">
                                <i class="fas fa-trash"></i> 선택 삭제
                            </button>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="card-body">
            @if($inquiries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>상태</th>
                            <th>제목</th>
                            <th>문의자</th>
                            <th>연락처</th>
                            <th>문의일</th>
                            <th>답변일</th>
                            <th>관리</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inquiries as $inquiry)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" value="{{ $inquiry->id }}">
                                </td>
                                <td>
                                    @if($inquiry->status === 'pending')
                                        <span class="badge bg-warning">대기</span>
                                    @elseif($inquiry->status === 'processing')
                                        <span class="badge bg-info">처리중</span>
                                    @else
                                        <span class="badge bg-success">완료</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('manager.inquiries.show', $inquiry) }}"
                                       class="text-decoration-none">
                                        {{ Str::limit($inquiry->subject, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $inquiry->name }}</strong>
                                        <br><small class="text-muted">{{ $inquiry->email }}</small>
                                    </div>
                                </td>
                                <td>{{ $inquiry->phone ?? '-' }}</td>
                                <td>
                                    <div>{{ $inquiry->created_at->format('Y-m-d') }}</div>
                                    <small class="text-muted">{{ $inquiry->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($inquiry->replied_at)
                                        <div>{{ $inquiry->replied_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $inquiry->replied_at->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('manager.inquiries.show', $inquiry) }}"
                                           class="btn btn-sm btn-outline-info"
                                           data-bs-toggle="tooltip"
                                           title="상세보기">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('manager.inquiries.destroy', $inquiry) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger btn-delete"
                                                    data-bs-toggle="tooltip"
                                                    title="삭제">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- 페이지네이션 -->
                <div class="d-flex justify-content-center">
                    {{ $inquiries->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request()->hasAny(['status', 'search', 'date_from', 'date_to']))
                            검색 조건에 맞는 문의가 없습니다.
                        @else
                            등록된 문의가 없습니다.
                        @endif
                    </h5>
                    @if(request()->hasAny(['status', 'search', 'date_from', 'date_to']))
                        <a href="{{ route('manager.inquiries.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-redo"></i> 전체 문의 보기
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- 통계 정보 -->
    @if($inquiries->total() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary">{{ $inquiries->total() }}</h4>
                        <small class="text-muted">총 문의 수</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning">{{ Inquiry::where('status', 'pending')->count() }}</h4>
                        <small class="text-muted">대기 중</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">{{ Inquiry::where('status', 'processing')->count() }}</h4>
                        <small class="text-muted">처리 중</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">{{ Inquiry::where('status', 'completed')->count() }}</h4>
                        <small class="text-muted">완료</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function bulkUpdateStatus(status) {
            const selected = $('.item-checkbox:checked');
            if (selected.length === 0) {
                alert('상태를 변경할 문의를 선택해주세요.');
                return;
            }

            const statusText = {
                'pending': '대기',
                'processing': '처리중',
                'completed': '완료'
            };

            if (confirm(selected.length + '개 문의의 상태를 "' + statusText[status] + '"로 변경하시겠습니까?')) {
                const ids = [];
                selected.each(function () {
                    ids.push($(this).val());
                });

                const form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("manager.inquiries.index") }}/bulk-status'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'ids',
                    'value': ids.join(',')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'status',
                    'value': status
                }));

                $('body').append(form);
                form.submit();
            }
        }

        function bulkDelete() {
            const selected = $('.item-checkbox:checked');
            if (selected.length === 0) {
                alert('삭제할 문의를 선택해주세요.');
                return;
            }

            if (confirm(selected.length + '개 문의를 삭제하시겠습니까?')) {
                const ids = [];
                selected.each(function () {
                    ids.push($(this).val());
                });

                const form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("manager.inquiries.index") }}/bulk-delete'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'ids',
                    'value': ids.join(',')
                }));

                $('body').append(form);
                form.submit();
            }
        }
    </script>
@endpush
