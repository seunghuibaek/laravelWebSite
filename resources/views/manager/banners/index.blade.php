@extends('manager.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>메인 배너 관리</h3>
        <a href="{{ route('manager.banners.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 배너 등록</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>이미지</th>
                        <th>제목</th>
                        <th>링크</th>
                        <th>노출</th>
                        <th>정렬</th>
                        <th>기간</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="" style="height:50px"/>
                            </td>
                            <td>{{ $banner->title }}</td>
                            <td>
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" target="_blank">{{ $banner->link_url }}</a>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('manager.banners.toggle', $banner) }}">
                                    @csrf
                                    <button class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $banner->is_active ? 'ON' : 'OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td>{{ $banner->sort_order }}</td>
                            <td>
                                @if($banner->starts_at || $banner->ends_at)
                                    {{ $banner->starts_at ? $banner->starts_at->format('Y-m-d H:i') : '제한없음' }}
                                    ~
                                    {{ $banner->ends_at ? $banner->ends_at->format('Y-m-d H:i') : '제한없음' }}
                                @else
                                    상시
                                @endif
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('manager.banners.edit', $banner) }}" class="btn btn-sm btn-warning">수정</a>
                                <form method="POST" action="{{ route('manager.banners.destroy', $banner) }}" onsubmit="return confirm('삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">삭제</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">등록된 배너가 없습니다.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection
