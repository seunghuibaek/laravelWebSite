@php use App\Models\SystemSetting; @endphp
<footer class="footer bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>{{ SystemSetting::get('site_name', config('app.name')) }}</h5>
                <p class="text-light">{{ SystemSetting::get('site_description', '웹사이트 설명') }}</p>

                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 mb-4">
                <h6>빠른 링크</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-light text-decoration-none">홈</a></li>
                    <li><a href="{{ route('inquiry.create') }}" class="text-light text-decoration-none">문의하기</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-4">
                <h6>게시판</h6>
                <ul class="list-unstyled">
                    @php
                        $footerBoards = \App\Models\Board::where('is_active', true)
                            ->orderBy('sort_order')
                            ->limit(5)
                            ->get();
                    @endphp
                    @foreach($footerBoards as $board)
                        <li>
                            <a href="{{ route('board.index', $board->board_code) }}"
                               class="text-light text-decoration-none">
                                {{ $board->board_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3 mb-4">
                <h6>연락처</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        {{ SystemSetting::get('admin_email', 'admin@example.com') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        {{ SystemSetting::get('contact_phone', '02-1234-5678') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ SystemSetting::get('contact_address', '서울시 강남구') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom bg-black py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ SystemSetting::get('site_name', config('app.name')) }}.
                        All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">Powered by Laravel</small>
                </div>
            </div>
        </div>
    </div>
</footer>
