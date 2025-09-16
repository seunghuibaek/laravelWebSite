@extends('manager.layouts.app')

@section('title', '사이트 통계')
@section('page-title', '사이트 통계')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<!-- 기간 선택 -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('manager.statistics.index') }}" class="d-flex align-items-center gap-3">
            <label for="period" class="form-label mb-0">통계 기간:</label>
            <select class="form-select" id="period" name="period" style="width: auto;" onchange="this.form.submit()">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>최근 7일</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>최근 30일</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>최근 90일</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>최근 1년</option>
            </select>
        </form>
    </div>
</div>

<!-- 전체 통계 카드 -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['total_boards'] }}</div>
                    <div class="stats-label">총 게시판</div>
                    <small class="text-light">활성: {{ $stats['active_boards'] }}개</small>
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
                    <div class="stats-number">{{ number_format($stats['total_posts']) }}</div>
                    <div class="stats-label">총 게시글</div>
                    <small class="text-light">최근 {{ $period }}일: {{ $periodStats['posts'] }}개</small>
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
                    <div class="stats-number">{{ number_format($stats['total_comments']) }}</div>
                    <div class="stats-label">총 댓글</div>
                    <small class="text-light">최근 {{ $period }}일: {{ $periodStats['comments'] }}개</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-comments fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stats-number">{{ $stats['total_inquiries'] }}</div>
                    <div class="stats-label">총 문의</div>
                    <small class="text-light">대기중: {{ $stats['pending_inquiries'] }}개</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-envelope fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 차트 -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line"></i> 일별 활동 통계 (최근 {{ $period }}일)</h6>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> 게시판별 게시글 수</h6>
            </div>
            <div class="card-body">
                <canvas id="boardChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 상세 통계 -->
<div class="row">
    <!-- 게시판별 통계 -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> 게시판별 상세 통계</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>게시판</th>
                                <th>게시글</th>
                                <th>상태</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boardStats as $board)
                            <tr>
                                <td>{{ $board->board_name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $board->posts_count }}</span>
                                </td>
                                <td>
                                    @if($board->is_active)
                                        <span class="badge bg-success">활성</span>
                                    @else
                                        <span class="badge bg-secondary">비활성</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 인기 게시글 -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-fire"></i> 인기 게시글 (최근 {{ $period }}일)</h6>
            </div>
            <div class="card-body">
                @if($popularPosts->count() > 0)
                    @foreach($popularPosts as $post)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ Str::limit($post->title, 30) }}</h6>
                            <small class="text-muted">{{ $post->board->board_name }} | {{ $post->author_name }}</small>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-warning text-dark">{{ number_format($post->view_count) }} 조회</div>
                            @if($post->like_count > 0)
                                <div class="badge bg-danger">{{ $post->like_count }} 좋아요</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-4">해당 기간에 게시글이 없습니다.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 최근 활동 -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-file-alt"></i> 최근 게시글</h6>
            </div>
            <div class="card-body">
                @foreach($recentPosts as $post)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ Str::limit($post->title, 25) }}</h6>
                        <small class="text-muted">{{ $post->board->board_name }} | {{ $post->author_name }}</small>
                        <br><small class="text-muted">{{ $post->created_at->format('m-d H:i') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-comments"></i> 최근 댓글</h6>
            </div>
            <div class="card-body">
                @foreach($recentComments as $comment)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ Str::limit($comment->content, 25) }}</h6>
                        <small class="text-muted">{{ $comment->post->board->board_name }} | {{ $comment->author_name }}</small>
                        <br><small class="text-muted">{{ $comment->created_at->format('m-d H:i') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-envelope"></i> 최근 문의</h6>
            </div>
            <div class="card-body">
                @foreach($recentInquiries as $inquiry)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ Str::limit($inquiry->subject, 25) }}</h6>
                        <small class="text-muted">{{ $inquiry->name }}</small>
                        <br><small class="text-muted">{{ $inquiry->created_at->format('m-d H:i') }}</small>
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 일별 활동 차트
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    const dailyPostsData = @json($dailyPosts);
    const dailyCommentsData = @json($dailyComments);
    
    // 날짜 배열 생성
    const dates = [];
    for (let i = {{ $period - 1 }}; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        dates.push(date.toISOString().split('T')[0]);
    }
    
    // 데이터 매핑
    const postsData = dates.map(date => {
        const found = dailyPostsData.find(item => item.date === date);
        return found ? found.count : 0;
    });
    
    const commentsData = dates.map(date => {
        const found = dailyCommentsData.find(item => item.date === date);
        return found ? found.count : 0;
    });
    
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return (d.getMonth() + 1) + '/' + d.getDate();
            }),
            datasets: [{
                label: '게시글',
                data: postsData,
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.1
            }, {
                label: '댓글',
                data: commentsData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // 게시판별 차트
    const boardCtx = document.getElementById('boardChart').getContext('2d');
    const boardData = @json($boardStats);
    
    new Chart(boardCtx, {
        type: 'doughnut',
        data: {
            labels: boardData.map(board => board.board_name),
            datasets: [{
                data: boardData.map(board => board.posts_count),
                backgroundColor: [
                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                    '#ffecd2', '#fcb69f'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush