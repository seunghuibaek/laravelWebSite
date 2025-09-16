@extends('manager.layouts.app')

@section('title', '문의 상세보기')
@section('page-title', '문의 상세보기')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope"></i> {{ $inquiry->subject }}
                    @if($inquiry->status === 'pending')
                        <span class="badge bg-warning ms-2">대기</span>
                    @elseif($inquiry->status === 'processing')
                        <span class="badge bg-info ms-2">처리중</span>
                    @else
                        <span class="badge bg-success ms-2">완료</span>
                    @endif
                </h5>
                <div>
                    <a href="{{ route('manager.inquiries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> 목록
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 문의자 정보 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">문의자:</th>
                                <td>{{ $inquiry->name }}</td>
                            </tr>
                            <tr>
                                <th>이메일:</th>
                                <td>{{ $inquiry->email }}</td>
                            </tr>
                            <tr>
                                <th>전화번호:</th>
                                <td>{{ $inquiry->phone ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">문의일:</th>
                                <td>{{ $inquiry->created_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                            <tr>
                                <th>IP 주소:</th>
                                <td>{{ $inquiry->ip_address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>상태:</th>
                                <td>
                                    <form action="{{ route('manager.inquiries.update', $inquiry) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                            <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>대기</option>
                                            <option value="processing" {{ $inquiry->status === 'processing' ? 'selected' : '' }}>처리중</option>
                                            <option value="completed" {{ $inquiry->status === 'completed' ? 'selected' : '' }}>완료</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- 문의 내용 -->
                <div class="mb-4">
                    <h6><i class="fas fa-comment"></i> 문의 내용</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($inquiry->message)) !!}
                    </div>
                </div>
                
                <!-- 답변 -->
                @if($inquiry->admin_reply)
                <div class="mb-4">
                    <h6><i class="fas fa-reply"></i> 관리자 답변</h6>
                    <div class="border rounded p-3 bg-primary bg-opacity-10">
                        {!! nl2br(e($inquiry->admin_reply)) !!}
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            답변일: {{ $inquiry->replied_at->format('Y년 m월 d일 H:i') }}
                            @if($inquiry->repliedBy)
                                | 답변자: {{ $inquiry->repliedBy->name }}
                            @endif
                        </small>
                    </div>
                </div>
                @endif
                
                <!-- 답변 작성 폼 -->
                @if(!$inquiry->admin_reply || $inquiry->status !== 'completed')
                <div class="mb-4">
                    <h6><i class="fas fa-edit"></i> 답변 작성</h6>
                    <form action="{{ route('manager.inquiries.reply', $inquiry) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control @error('admin_reply') is-invalid @enderror" 
                                      name="admin_reply" 
                                      rows="6" 
                                      placeholder="답변 내용을 입력하세요..."
                                      required>{{ old('admin_reply', $inquiry->admin_reply) }}</textarea>
                            @error('admin_reply')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> 답변 전송
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="{{ route('manager.inquiries.destroy', $inquiry) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i> 삭제
                            </button>
                        </form>
                    </div>
                    
                    <div>
                        <a href="{{ route('manager.inquiries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> 목록
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 사이드바 -->
    <div class="col-lg-4">
        <!-- 빠른 액션 -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> 빠른 액션</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($inquiry->status === 'pending')
                        <form action="{{ route('manager.inquiries.update', $inquiry) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-spinner"></i> 처리중으로 변경
                            </button>
                        </form>
                    @endif
                    
                    @if($inquiry->status !== 'completed')
                        <form action="{{ route('manager.inquiries.update', $inquiry) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> 완료로 변경
                            </button>
                        </form>
                    @endif
                    
                    <a href="mailto:{{ $inquiry->email }}?subject=Re: {{ $inquiry->subject }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope"></i> 이메일 답장
                    </a>
                    
                    @if($inquiry->phone)
                        <a href="tel:{{ $inquiry->phone }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone"></i> 전화 걸기
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- 관련 문의 -->
        @php
            $relatedInquiries = \App\Models\Inquiry::where('email', $inquiry->email)
                ->where('id', '!=', $inquiry->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @if($relatedInquiries->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history"></i> 동일 이메일 문의 이력</h6>
            </div>
            <div class="card-body">
                @foreach($relatedInquiries as $related)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <a href="{{ route('manager.inquiries.show', $related) }}" class="text-decoration-none">
                                {{ Str::limit($related->subject, 25) }}
                            </a>
                        </h6>
                        <small class="text-muted">{{ $related->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                    <div>
                        @if($related->status === 'pending')
                            <span class="badge bg-warning">대기</span>
                        @elseif($related->status === 'processing')
                            <span class="badge bg-info">처리중</span>
                        @else
                            <span class="badge bg-success">완료</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection