@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6 fw-bold">
                <i class="bi bi-speedometer2 me-3"></i>대시보드
            </h1>
            <p class="lead text-muted">{{ Auth::user()->name }}님의 24명성 순례 현황</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-xl-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">방문한 성</h6>
                            <h3 class="mb-0">{{ $visitedCastles }}/{{ $totalCastles }}</h3>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <small class="text-white-75">{{ $progressPercentage }}% 완료</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">대기 중</h6>
                            <h3 class="mb-0">{{ $pendingVisits }}</h3>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                    <small class="text-white-75">인증 대기 중인 방문</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">획득 배지</h6>
                            <h3 class="mb-0">{{ $userBadges }}/{{ $totalBadges }}</h3>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-award"></i>
                        </div>
                    </div>
                    <small class="text-white-75">수집한 배지</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">다음 목표</h6>
                            <h3 class="mb-0">{{ $totalCastles - $visitedCastles }}</h3>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-flag"></i>
                        </div>
                    </div>
                    <small class="text-white-75">남은 성</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Progress Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>진행률
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="progress-circle mx-auto mb-3" style="width: 150px; height: 150px;">
                            <svg width="150" height="150" class="position-absolute">
                                <circle cx="75" cy="75" r="60" fill="none" stroke="#e9ecef" stroke-width="10"></circle>
                                <circle cx="75" cy="75" r="60" fill="none" stroke="#0d6efd" stroke-width="10"
                                        stroke-dasharray="{{ 2 * 3.14159 * 60 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 60 * (1 - $progressPercentage / 100) }}"
                                        transform="rotate(-90 75 75)"
                                        class="transition-all duration-500"></circle>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="h3 fw-bold text-primary mb-0">{{ $progressPercentage }}%</div>
                                <div class="small text-muted">완료</div>
                            </div>
                        </div>
                        <h6 class="mb-2">{{ $visitedCastles }}개 방문 완료</h6>
                        <p class="text-muted small mb-0">{{ $totalCastles - $visitedCastles }}개 남음</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Visits -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>최근 방문
                    </h5>
                    <a href="{{ route('visit-records.index') }}" class="btn btn-sm btn-outline-primary">
                        전체 보기
                    </a>
                </div>
                <div class="card-body">
                    @if($recentVisits->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentVisits as $visit)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $visit->castle->name_korean }}</h6>
                                            <p class="mb-1 text-muted small">{{ $visit->castle->name }}</p>
                                            <small class="text-muted">{{ $visit->created_at->format('Y-m-d') }}</small>
                                        </div>
                                        <div>
                                            @if($visit->verification_status === 'approved')
                                                <span class="badge bg-success">승인</span>
                                            @elseif($visit->verification_status === 'pending')
                                                <span class="badge bg-warning">대기</span>
                                            @else
                                                <span class="badge bg-danger">거절</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                            <p class="text-muted mb-3">아직 방문한 성이 없습니다.</p>
                            <a href="{{ route('castles.index') }}" class="btn btn-primary">
                                <i class="bi bi-building me-2"></i>성 목록 보기
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>빠른 작업
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('castles.index') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-list fs-4 d-block mb-2"></i>
                                성 목록
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('castles.map') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-geo-alt fs-4 d-block mb-2"></i>
                                지도 보기
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('visit-records.index') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-journal-check fs-4 d-block mb-2"></i>
                                방문 기록
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-info w-100 p-3" data-bs-toggle="modal" data-bs-target="#badgeModal">
                                <i class="bi bi-award fs-4 d-block mb-2"></i>
                                배지 현황
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Badge Modal -->
<div class="modal fade" id="badgeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-award me-2"></i>배지 현황
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    @php
                        $badges = \App\Models\Badge::all();
                        $userBadgeIds = Auth::user()->badges()->pluck('badges.id')->toArray();
                    @endphp

                    @foreach($badges as $badge)
                        <div class="col-md-6">
                            <div class="card {{ in_array($badge->id, $userBadgeIds) ? 'badge-earned' : 'badge-locked' }}">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-award-fill fs-1" style="color: {{ $badge->badge_color }}"></i>
                                    </div>
                                    <h6 class="card-title">{{ $badge->name_korean }}</h6>
                                    <p class="card-text small text-muted">{{ $badge->description }}</p>
                                    <div class="small">
                                        <strong>필요 방문:</strong> {{ $badge->required_visits }}개
                                    </div>
                                    @if(in_array($badge->id, $userBadgeIds))
                                        <span class="badge bg-success mt-2">획득 완료</span>
                                    @else
                                        <span class="badge bg-secondary mt-2">미획득</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection