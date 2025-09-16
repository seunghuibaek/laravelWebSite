@extends('manager.layouts.app')

@section('title', '관리자 상세보기')
@section('page-title', '관리자 상세보기')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user"></i> 관리자 정보</h5>
                <div>
                    <a href="{{ route('manager.managers.edit', $manager) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> 수정
                    </a>
                    <a href="{{ route('manager.managers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> 목록
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">아이디:</th>
                                <td>
                                    {{ $manager->username }}
                                    @if($manager->id === auth('manager')->id())
                                        <span class="badge bg-info ms-2">본인</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>이름:</th>
                                <td>{{ $manager->name }}</td>
                            </tr>
                            <tr>
                                <th>이메일:</th>
                                <td>{{ $manager->email }}</td>
                            </tr>
                            <tr>
                                <th>전화번호:</th>
                                <td>{{ $manager->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>권한:</th>
                                <td>
                                    @if($manager->role === 'super_admin')
                                        <span class="badge bg-danger">최고관리자</span>
                                    @elseif($manager->role === 'admin')
                                        <span class="badge bg-warning">관리자</span>
                                    @else
                                        <span class="badge bg-secondary">매니저</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>상태:</th>
                                <td>
                                    @if($manager->status === 'active')
                                        <span class="badge bg-success">활성</span>
                                    @else
                                        <span class="badge bg-secondary">비활성</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">등록일:</th>
                                <td>{{ $manager->created_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                            <tr>
                                <th>수정일:</th>
                                <td>{{ $manager->updated_at->format('Y년 m월 d일 H:i') }}</td>
                            </tr>
                            <tr>
                                <th>최근 로그인:</th>
                                <td>
                                    @if($manager->last_login_at)
                                        {{ $manager->last_login_at->format('Y년 m월 d일 H:i') }}
                                        <small class="text-muted">
                                            ({{ $manager->last_login_at->diffForHumans() }})
                                        </small>
                                    @else
                                        <span class="text-muted">로그인 기록 없음</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>이메일 인증:</th>
                                <td>
                                    @if($manager->email_verified_at)
                                        <span class="badge bg-success">인증됨</span>
                                        <small class="text-muted d-block">
                                            {{ $manager->email_verified_at->format('Y-m-d H:i') }}
                                        </small>
                                    @else
                                        <span class="badge bg-warning">미인증</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($manager->repliedInquiries()->count() > 0)
                <hr>
                <div class="mt-4">
                    <h6><i class="fas fa-envelope"></i> 답변한 문의 ({{ $manager->repliedInquiries()->count() }}건)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>문의 제목</th>
                                    <th>문의자</th>
                                    <th>답변일</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($manager->repliedInquiries()->latest()->limit(5)->get() as $inquiry)
                                <tr>
                                    <td>
                                        <a href="{{ route('manager.inquiries.show', $inquiry) }}">
                                            {{ Str::limit($inquiry->subject, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $inquiry->name }}</td>
                                    <td>{{ $inquiry->replied_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success">완료</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($manager->repliedInquiries()->count() > 5)
                    <div class="text-center">
                        <a href="{{ route('manager.inquiries.index', ['replied_by' => $manager->id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            전체 보기
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($manager->id !== auth('manager')->id())
                        <form action="{{ route('manager.managers.destroy', $manager) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i> 삭제
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <div>
                        <a href="{{ route('manager.managers.edit', $manager) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> 수정
                        </a>
                        <a href="{{ route('manager.managers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> 목록
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection