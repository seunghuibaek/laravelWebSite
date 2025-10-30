@php use App\Models\SystemSetting; @endphp
@extends('front.layouts.app')

@section('title', '로그인')

@section('content')
    <!-- Login Header -->
    <section class="board-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb text-white">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">홈</a></li>
                            <li class="breadcrumb-item active text-white">로그인</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold">
                        <i class="fas fa-sign-in-alt me-3"></i>로그인
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Form Section -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>로그인
                            </h5>
                        </div>

                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <form action="{{ route('login') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">이메일 <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">비밀번호 <span
                                            class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="remember"
                                               id="remember">
                                        <label class="form-check-label" for="remember">
                                            로그인 상태 유지
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>로그인
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="mb-0">계정이 없으신가요?
                                    <a href="{{ route('register') }}" class="text-decoration-none">회원가입</a>
                                </p>
                            </div>

                            @php
                                $snsEnabled = SystemSetting::get('sns_enabled', false);
                                $googleOn = (string)SystemSetting::get('sns_google_client_id');
                                $kakaoOn = (string)SystemSetting::get('sns_kakao_client_id');
                                $naverOn = (string)SystemSetting::get('sns_naver_client_id');
                            @endphp
                            @if($snsEnabled && ($googleOn || $kakaoOn || $naverOn))
                                <hr class="my-4">
                                <div class="text-center mb-3 text-muted">또는 SNS 계정으로 로그인</div>
                                <div class="d-grid gap-2">
                                    @if($naverOn)
                                        <a href="{{ route('auth.sns.redirect', ['provider' => 'naver']) }}"
                                           class="btn btn-success"
                                           style="background-color:#03C75A;border-color:#03C75A;">
                                            <i class="fas fa-n"></i> 네이버로 로그인
                                        </a>
                                    @endif
                                    @if($kakaoOn)
                                        <a href="{{ route('auth.sns.redirect', ['provider' => 'kakao']) }}"
                                           class="btn btn-warning text-dark"
                                           style="background-color:#FEE500;border-color:#FEE500;">
                                            <i class="fas fa-comment"></i> 카카오로 로그인
                                        </a>
                                    @endif
                                    @if($googleOn)
                                        <a href="{{ route('auth.sns.redirect', ['provider' => 'google']) }}"
                                           class="btn btn-outline-dark">
                                            <i class="fab fa-google"></i> Google로 로그인
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
