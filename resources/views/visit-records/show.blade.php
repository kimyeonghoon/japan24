@extends('layouts.simple')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>방문 기록 상세</h2>
            <div>
                <a href="{{ route('visit-records.index') }}" class="btn btn-secondary">목록으로</a>
                <a href="{{ route('castles.index') }}" class="btn btn-outline-primary">성 목록</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- 방문 기록 정보 -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $visitRecord->castle->name_korean }}</h4>
                        <small class="text-muted">{{ $visitRecord->castle->name }}</small>
                    </div>
                    <span class="badge fs-6
                        @if($visitRecord->verification_status === 'approved') bg-success
                        @elseif($visitRecord->verification_status === 'rejected') bg-danger
                        @else bg-warning text-dark
                        @endif">
                        @if($visitRecord->verification_status === 'approved')
                            ✓ 승인됨
                        @elseif($visitRecord->verification_status === 'rejected')
                            ✗ 거부됨
                        @else
                            ⏳ 검토중
                        @endif
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>방문 정보</h6>
                        <ul class="list-unstyled">
                            <li><strong>방문일:</strong> {{ $visitRecord->visit_date->format('Y년 m월 d일') }}</li>
                            <li><strong>등록일:</strong> {{ $visitRecord->created_at->format('Y-m-d H:i:s') }}</li>
                            <li><strong>GPS 좌표:</strong> {{ $visitRecord->gps_latitude }}, {{ $visitRecord->gps_longitude }}</li>
                            <li><strong>성과의 거리:</strong> {{ number_format($visitRecord->castle->getDistanceFromUser($visitRecord->gps_latitude, $visitRecord->gps_longitude)) }}m</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>성 정보</h6>
                        <ul class="list-unstyled">
                            <li><strong>위치:</strong> {{ $visitRecord->castle->prefecture }}</li>
                            <li><strong>방문 시간:</strong> {{ $visitRecord->castle->visiting_hours }}</li>
                            <li><strong>입장료:</strong> {{ $visitRecord->castle->entrance_fee }}</li>
                            <li><strong>스탬프 위치:</strong> {{ $visitRecord->castle->official_stamp_location }}</li>
                        </ul>
                    </div>
                </div>

                @if($visitRecord->visit_notes)
                    <div class="mt-3">
                        <h6>방문 메모</h6>
                        <p class="text-muted">{{ $visitRecord->visit_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 업로드된 사진들 -->
        @if($visitRecord->photo_paths && count($visitRecord->photo_paths) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">업로드된 사진 ({{ count($visitRecord->photo_paths) }}장)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($visitRecord->photo_paths as $index => $photoPath)
                            <div class="col-md-4 mb-3">
                                <div class="position-relative">
                                    <img src="{{ Storage::url($photoPath) }}"
                                         class="img-fluid rounded shadow-sm"
                                         alt="방문 사진 {{ $index + 1 }}"
                                         style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal"
                                         data-bs-target="#photoModal{{ $index }}">
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-dark">{{ $index + 1 }}</span>
                                    </div>
                                </div>

                                <!-- 사진 확대 모달 -->
                                <div class="modal fade" id="photoModal{{ $index }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">방문 사진 {{ $index + 1 }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ Storage::url($photoPath) }}"
                                                     class="img-fluid"
                                                     alt="방문 사진 {{ $index + 1 }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- 스탬프 사진 -->
        @if($visitRecord->stamp_photo_path)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">스탬프 사진</h5>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <img src="{{ Storage::url($visitRecord->stamp_photo_path) }}"
                                 class="img-fluid rounded shadow-sm"
                                 alt="스탬프 사진"
                                 style="width: 100%; height: 300px; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#stampPhotoModal">
                        </div>
                    </div>

                    <!-- 스탬프 사진 확대 모달 -->
                    <div class="modal fade" id="stampPhotoModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">스탬프 사진</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ Storage::url($visitRecord->stamp_photo_path) }}"
                                         class="img-fluid"
                                         alt="스탬프 사진">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 검증 상태에 따른 메시지 -->
        <div class="card">
            <div class="card-body">
                @if($visitRecord->verification_status === 'approved')
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            <strong>방문이 승인되었습니다!</strong><br>
                            이 방문 기록이 배지 획득에 반영되었습니다.
                        </div>
                    </div>
                @elseif($visitRecord->verification_status === 'rejected')
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        <div>
                            <strong>방문이 거부되었습니다.</strong><br>
                            관리자 검토 결과 방문 인증 요건을 만족하지 않습니다.
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-clock-fill me-2"></i>
                        <div>
                            <strong>관리자 검토 대기 중입니다.</strong><br>
                            방문 기록이 검토되면 배지 획득 여부가 결정됩니다.
                        </div>
                    </div>
                @endif

                <!-- 지도 표시 (간단한 링크) -->
                <div class="mt-3">
                    <h6>지도에서 보기</h6>
                    <a href="https://maps.google.com?q={{ $visitRecord->gps_latitude }},{{ $visitRecord->gps_longitude }}"
                       target="_blank" class="btn btn-outline-primary btn-sm">
                        Google Maps에서 열기
                    </a>
                    <a href="https://map.kakao.com/link/map/{{ $visitRecord->castle->name_korean }},{{ $visitRecord->gps_latitude }},{{ $visitRecord->gps_longitude }}"
                       target="_blank" class="btn btn-outline-warning btn-sm">
                        카카오맵에서 열기
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
@endsection