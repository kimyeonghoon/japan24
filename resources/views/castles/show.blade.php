@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header with Back Button -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">대시보드</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('castles.index') }}">성 목록</a></li>
                    <li class="breadcrumb-item active">{{ $castle->name_korean }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Castle Image and Basic Info -->
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($castle->image_url)
                    <img src="{{ $castle->image_url }}" class="card-img-top" style="height: 400px; object-fit: cover;" alt="{{ $castle->name_korean }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                        <i class="bi bi-building text-muted" style="font-size: 6rem;"></i>
                    </div>
                @endif

                <div class="card-body">
                    <h1 class="card-title h2">{{ $castle->name_korean }}</h1>
                    <h2 class="card-subtitle h5 text-muted mb-3">{{ $castle->name }}</h2>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">
                                <i class="bi bi-geo-alt me-2"></i>소재지
                            </h6>
                            <p>{{ $castle->prefecture }}</p>

                            @if($castle->address)
                                <h6 class="text-muted">
                                    <i class="bi bi-house me-2"></i>주소
                                </h6>
                                <p>{{ $castle->address }}</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            @if($castle->visiting_hours)
                                <h6 class="text-muted">
                                    <i class="bi bi-clock me-2"></i>관람 시간
                                </h6>
                                <p>{{ $castle->visiting_hours }}</p>
                            @endif

                            <h6 class="text-muted">
                                <i class="bi bi-cash me-2"></i>입장료
                            </h6>
                            <p>
                                @if($castle->entrance_fee > 0)
                                    {{ number_format($castle->entrance_fee) }}엔
                                @else
                                    무료
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($castle->description)
                        <h5>성 소개</h5>
                        <p class="lead">{{ $castle->description }}</p>
                    @endif

                    @if($castle->historical_info)
                        <h5>역사 정보</h5>
                        <p>{{ $castle->historical_info }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>위치 정보
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>위도:</strong> {{ $castle->latitude }}
                    </p>
                    <p class="mb-3">
                        <strong>경도:</strong> {{ $castle->longitude }}
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('castles.map') }}?focus={{ $castle->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-map me-2"></i>지도에서 보기
                        </a>

                        @php
                            $userVisited = auth()->user()->visitRecords()
                                ->where('castle_id', $castle->id)
                                ->where('verification_status', 'approved')
                                ->exists();
                        @endphp

                        @if($userVisited)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle me-2"></i>이미 방문 인증 완료!
                            </div>
                        @else
                            <a href="{{ route('visit-records.create', $castle) }}" class="btn btn-success">
                                <i class="bi bi-camera me-2"></i>방문 인증하기
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            @if($castle->official_stamp_location || $castle->access_method || $castle->official_website)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>상세 정보
                    </h5>
                </div>
                <div class="card-body">
                    @if($castle->official_stamp_location)
                        <h6 class="text-muted">
                            <i class="bi bi-award me-2"></i>스탬프 위치
                        </h6>
                        <p class="mb-3">{{ $castle->official_stamp_location }}</p>
                    @endif

                    @if($castle->access_method)
                        <h6 class="text-muted">
                            <i class="bi bi-signpost me-2"></i>접근 방법
                        </h6>
                        <p class="mb-3">{{ $castle->access_method }}</p>
                    @endif

                    @if($castle->official_website)
                        <h6 class="text-muted">
                            <i class="bi bi-globe me-2"></i>공식 웹사이트
                        </h6>
                        <p class="mb-0">
                            <a href="{{ $castle->official_website }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>웹사이트 방문
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Visit Records -->
    @if($userVisited)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-check me-2"></i>내 방문 기록
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $visitRecord = auth()->user()->visitRecords()
                            ->where('castle_id', $castle->id)
                            ->where('verification_status', 'approved')
                            ->first();
                    @endphp

                    @if($visitRecord)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>방문일:</strong> {{ $visitRecord->visited_at->format('Y년 m월 d일') }}</p>
                                <p><strong>인증일:</strong> {{ $visitRecord->created_at->format('Y년 m월 d일') }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($visitRecord->photos && count($visitRecord->photos) > 0)
                                    <p><strong>방문 인증 사진:</strong></p>
                                    <div class="row g-2">
                                        @foreach(array_slice($visitRecord->photos, 0, 3) as $photo)
                                            <div class="col-4">
                                                <img src="{{ Storage::url($photo) }}" class="img-thumbnail" alt="방문 사진">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('visit-records.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list me-1"></i>전체 방문 기록 보기
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection