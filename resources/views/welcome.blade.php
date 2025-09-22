@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">24명성 인증 앱</h1>
                <p class="lead mb-4">일본의 아름다운 성들을 방문하고 디지털 스탬프를 수집하세요!</p>

                @guest
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg me-md-2">
                            <i class="bi bi-person-plus me-2"></i>시작하기
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>로그인
                        </a>
                    </div>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-speedometer2 me-2"></i>대시보드로 이동
                    </a>
                @endguest
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card bg-white bg-opacity-20 border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-building display-4 mb-3"></i>
                                <h5>24개의 성</h5>
                                <p class="small">일본 전국의 명성들</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-white bg-opacity-20 border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-geo-alt display-4 mb-3"></i>
                                <h5>GPS 인증</h5>
                                <p class="small">정확한 위치 확인</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-white bg-opacity-20 border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-camera display-4 mb-3"></i>
                                <h5>사진 인증</h5>
                                <p class="small">3장의 성 사진</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-white bg-opacity-20 border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-award display-4 mb-3"></i>
                                <h5>배지 시스템</h5>
                                <p class="small">성취도에 따른 보상</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="fw-bold">앱의 주요 기능</h2>
                <p class="text-muted">일본 성 순례를 더욱 즐겁고 체계적으로 즐길 수 있습니다</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center h-100">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-geo-alt text-white fs-4"></i>
                    </div>
                    <h5>GPS 위치 인증</h5>
                    <p class="text-muted">성 근처 200m 이내에서만 인증이 가능하여 정확한 방문을 보장합니다.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="text-center h-100">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-camera text-white fs-4"></i>
                    </div>
                    <h5>사진 인증</h5>
                    <p class="text-muted">성의 아름다운 모습을 3장의 사진으로 기록하고 인증을 받으세요.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="text-center h-100">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-image text-white fs-4"></i>
                    </div>
                    <h5>스탬프 수첩</h5>
                    <p class="text-muted">오프라인 도장과 함께 디지털 스탬프도 수집할 수 있습니다.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="text-center h-100">
                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-award text-white fs-4"></i>
                    </div>
                    <h5>배지 시스템</h5>
                    <p class="text-muted">방문한 성의 개수에 따라 다양한 배지를 획득할 수 있습니다.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="h2 fw-bold text-primary">24</div>
                <p class="text-muted mb-0">일본 명성</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="h2 fw-bold text-success">6</div>
                <p class="text-muted mb-0">배지 종류</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="h2 fw-bold text-warning">3</div>
                <p class="text-muted mb-0">필수 사진</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="h2 fw-bold text-info">200m</div>
                <p class="text-muted mb-0">인증 범위</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@guest
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h3 class="mb-4">지금 바로 일본 성 순례를 시작하세요!</h3>
        <p class="lead mb-4">무료로 가입하고 24개의 아름다운 성을 탐험해보세요</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
            <i class="bi bi-person-plus me-2"></i>무료 회원가입
        </a>
    </div>
</section>
@endguest
@endsection