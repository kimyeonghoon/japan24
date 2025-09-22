@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="display-6 fw-bold">
                <i class="bi bi-building me-3"></i>24명성 목록
            </h1>
            <p class="lead text-muted">일본의 아름다운 성들을 탐험해보세요</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('castles.map') }}" class="btn btn-outline-primary">
                <i class="bi bi-geo-alt me-2"></i>지도에서 보기
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">지역별 필터</label>
                            <select class="form-select" id="prefectureFilter">
                                <option value="">전체 지역</option>
                                @foreach($castles->pluck('prefecture')->unique()->sort() as $prefecture)
                                    <option value="{{ $prefecture }}">{{ $prefecture }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">방문 상태</label>
                            <select class="form-select" id="visitStatusFilter">
                                <option value="">전체</option>
                                <option value="visited">방문 완료</option>
                                <option value="not-visited">미방문</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">정렬</label>
                            <select class="form-select" id="sortOrder">
                                <option value="name">이름순</option>
                                <option value="prefecture">지역순</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Castle Cards -->
    <div class="row g-4" id="castleGrid">
        @php
            $userVisitedCastleIds = Auth::user()->visitRecords()
                ->where('verification_status', 'approved')
                ->pluck('castle_id')
                ->toArray();
        @endphp

        @foreach($castles as $castle)
            <div class="col-lg-4 col-md-6 castle-item"
                 data-prefecture="{{ $castle->prefecture }}"
                 data-visited="{{ in_array($castle->id, $userVisitedCastleIds) ? 'true' : 'false' }}"
                 data-name="{{ $castle->name_korean }}">
                <div class="card castle-card h-100 {{ in_array($castle->id, $userVisitedCastleIds) ? 'border-success' : '' }}">
                    @if($castle->image_url)
                        <img src="{{ $castle->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $castle->name_korean }}">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <!-- Visit Status Badge -->
                    @if(in_array($castle->id, $userVisitedCastleIds))
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>방문 완료
                            </span>
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $castle->name_korean }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $castle->name }}</h6>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $castle->prefecture }}
                            </small>
                        </p>
                        <p class="card-text">{{ Str::limit($castle->description, 100) }}</p>

                        @if($castle->entrance_fee > 0)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-cash me-1"></i>입장료: {{ number_format($castle->entrance_fee) }}엔
                                </small>
                            </div>
                        @endif

                        @if($castle->visiting_hours)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $castle->visiting_hours }}
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('castles.show', $castle) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>자세히 보기
                            </a>
                            @if(!in_array($castle->id, $userVisitedCastleIds))
                                <a href="{{ route('visit-records.create', $castle) }}" class="btn btn-success">
                                    <i class="bi bi-camera me-2"></i>방문 인증하기
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="bi bi-search display-4 text-muted mb-3"></i>
        <h5>검색 결과가 없습니다</h5>
        <p class="text-muted">다른 조건으로 검색해보세요.</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const prefectureFilter = document.getElementById('prefectureFilter');
    const visitStatusFilter = document.getElementById('visitStatusFilter');
    const sortOrder = document.getElementById('sortOrder');
    const castleItems = document.querySelectorAll('.castle-item');
    const emptyState = document.getElementById('emptyState');
    const castleGrid = document.getElementById('castleGrid');

    function filterAndSort() {
        const prefecture = prefectureFilter.value;
        const visitStatus = visitStatusFilter.value;
        const sort = sortOrder.value;

        let visibleItems = [];

        castleItems.forEach(item => {
            let show = true;

            // Prefecture filter
            if (prefecture && item.dataset.prefecture !== prefecture) {
                show = false;
            }

            // Visit status filter
            if (visitStatus === 'visited' && item.dataset.visited !== 'true') {
                show = false;
            } else if (visitStatus === 'not-visited' && item.dataset.visited === 'true') {
                show = false;
            }

            if (show) {
                item.style.display = 'block';
                visibleItems.push(item);
            } else {
                item.style.display = 'none';
            }
        });

        // Sort visible items
        if (sort === 'name') {
            visibleItems.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
        } else if (sort === 'prefecture') {
            visibleItems.sort((a, b) => a.dataset.prefecture.localeCompare(b.dataset.prefecture));
        }

        // Reorder DOM elements
        visibleItems.forEach(item => {
            castleGrid.appendChild(item);
        });

        // Show/hide empty state
        if (visibleItems.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    prefectureFilter.addEventListener('change', filterAndSort);
    visitStatusFilter.addEventListener('change', filterAndSort);
    sortOrder.addEventListener('change', filterAndSort);
});
</script>
@endpush
@endsection