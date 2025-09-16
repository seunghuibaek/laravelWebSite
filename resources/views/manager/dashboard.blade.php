@extends('manager.layouts.app')

@section('title', '대시보드')
@section('page-title', '대시보드')

@section('content')
<div class="row">
    <!-- 통계 카드들 -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['total_managers'] }}</div>
                    <div class="stats-label">관리자</div>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-users-cog fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['total_boards'] }}</div>
                    <div class="stats-label">게시판</div>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['total_posts'] }}</div>
                    <div class="stats-label">게시글</div>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-file-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['pending_inquiries'] }}</div>
                    <div class="stats-label">대기 중인 문의</div>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-envelope fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 최근 게시글 -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> 최근 게시글</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_posts']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>게시판</th>
                                    <th>제목</th>
                                    <th>작성자</th>
                                    <th>작성일</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_posts'] as $post)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $post->board->board_name }}</span>
                                    </td>
                                    <td>
                                        {{ Str::limit($post->title, 30) }}
                                        @if($post->is_notice)
                                            <span class="badge bg-warning text-dark">공지</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->author_name }}</td>
                                    <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">등록된 게시글이 없습니다.</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- 최근 문의 -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-envelope"></i> 최근 문의</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_inquiries']->count() > 0)
                    @foreach($stats['recent_inquiries'] as $inquiry)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ Str::limit($inquiry->subject, 20) }}</h6>
                            <small class="text-muted">{{ $inquiry->name }}</small>
                            <br>
                            <small class="text-muted">{{ $inquiry->created_at->format('m-d H:i') }}</small>
                        </div>
                        <div>
                            @if($inquiry->status === 'pending')
                                <span class="badge bg-warning">대기</span>
                            @elseif($inquiry->status === 'processing')
                                <span class="badge bg-info">처리중</span>
                            @else
                                <span class="badge bg-success">완료</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="text-center">
                        <a href="{{ route('manager.inquiries.index') }}" class="btn btn-sm btn-outline-primary">
                            전체 보기
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center py-4">문의가 없습니다.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 빠른 액션 -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> 빠른 액션</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('manager.boards.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i><br>
                            게시판 추가
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('manager.managers.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-user-plus"></i><br>
                            관리자 추가
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('manager.settings.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-cog"></i><br>
                            시스템 설정
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('manager.statistics.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-chart-bar"></i><br>
                            통계 보기
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection