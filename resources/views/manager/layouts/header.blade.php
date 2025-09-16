<div class="top-header">
    <div class="d-flex justify-content-between align-items-center">
        <div class="header-left">
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0 ms-3">@yield('page-title', '대시보드')</h5>
        </div>
        
        <div class="header-right">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <span class="ms-2">{{ auth('manager')->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user"></i> 프로필
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('manager.settings.index') }}">
                            <i class="fas fa-cog"></i> 설정
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('manager.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> 로그아웃
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>