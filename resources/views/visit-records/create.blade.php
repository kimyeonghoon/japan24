@extends('layouts.simple')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ $castle->name_korean }} 방문 인증</h4>
                <small class="text-muted">{{ $castle->name }}</small>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('visit-records.store', $castle) }}" enctype="multipart/form-data" id="visitForm">
                    @csrf

                    <!-- 방문 날짜 -->
                    <div class="mb-3">
                        <label for="visit_date" class="form-label">방문 날짜</label>
                        <input type="date" class="form-control" id="visit_date" name="visit_date"
                               value="{{ old('visit_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- GPS 위치 정보 -->
                    <div class="mb-3">
                        <label class="form-label">현재 위치</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" class="form-control" id="gps_latitude" name="gps_latitude"
                                       placeholder="위도" step="any" readonly required>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" id="gps_longitude" name="gps_longitude"
                                       placeholder="경도" step="any" readonly required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-sm mt-2" onclick="getCurrentLocation()">
                            현재 위치 가져오기
                        </button>
                        <div id="location-status" class="small text-muted mt-1"></div>
                    </div>

                    <!-- 성 사진 업로드 (3장 이상) -->
                    <div class="mb-3">
                        <label for="photos" class="form-label">성 사진 (최소 3장 필수)</label>
                        <input type="file" class="form-control" id="photos" name="photos[]"
                               accept="image/*" multiple required>
                        <div class="form-text">JPEG, PNG, JPG, GIF 형식 지원 (각 파일 최대 2MB)</div>
                        <div id="photo-preview" class="mt-2"></div>
                    </div>

                    <!-- 스탬프 사진 (선택사항) -->
                    <div class="mb-3">
                        <label for="stamp_photo" class="form-label">스탬프 사진 (선택사항)</label>
                        <input type="file" class="form-control" id="stamp_photo" name="stamp_photo"
                               accept="image/*">
                        <div class="form-text">스탬프 북이나 기념품의 사진을 업로드하세요</div>
                        <div id="stamp-preview" class="mt-2"></div>
                    </div>

                    <!-- 방문 메모 -->
                    <div class="mb-3">
                        <label for="visit_notes" class="form-label">방문 메모 (선택사항)</label>
                        <textarea class="form-control" id="visit_notes" name="visit_notes"
                                  rows="3" maxlength="1000" placeholder="방문 소감이나 특별한 경험을 기록해보세요">{{ old('visit_notes') }}</textarea>
                        <div class="form-text">최대 1000자</div>
                    </div>

                    <!-- 숨겨진 필드들 -->
                    <input type="hidden" name="device_timestamp" id="device_timestamp">
                    <input type="hidden" name="gps_accuracy" id="gps_accuracy">
                    <input type="hidden" name="gps_speed" id="gps_speed">
                    <input type="hidden" name="gps_heading" id="gps_heading">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('castles.index') }}" class="btn btn-secondary">취소</a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            방문 기록 등록
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 성 정보 카드 -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">성 정보</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>위치:</strong> {{ $castle->prefecture }}</p>
                        <p><strong>스탬프 위치:</strong> {{ $castle->official_stamp_location }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>GPS 좌표:</strong> {{ $castle->latitude }}, {{ $castle->longitude }}</p>
                    </div>
                </div>
                @if($castle->description)
                    <p class="mt-2"><strong>설명:</strong> {{ $castle->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
let deviceTimestamp = Math.floor(Date.now() / 1000);
document.getElementById('device_timestamp').value = deviceTimestamp;

function getCurrentLocation() {
    const statusDiv = document.getElementById('location-status');
    const submitBtn = document.getElementById('submit-btn');

    statusDiv.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>위치 정보를 가져오는 중...';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                const speed = position.coords.speed;
                const heading = position.coords.heading;

                document.getElementById('gps_latitude').value = lat;
                document.getElementById('gps_longitude').value = lng;
                document.getElementById('gps_accuracy').value = accuracy || '';
                document.getElementById('gps_speed').value = speed || '';
                document.getElementById('gps_heading').value = heading || '';

                // 성 위치와의 거리 계산
                const castleLat = {{ $castle->latitude }};
                const castleLng = {{ $castle->longitude }};
                const distance = calculateDistance(lat, lng, castleLat, castleLng);

                // GPS 정확도 확인
                let statusText = '';
                let statusClass = '';
                let canSubmit = false;

                if (accuracy && accuracy > 50) {
                    statusText = `⚠️ GPS 정확도가 낮습니다 (정확도: ${Math.round(accuracy)}m)`;
                    statusClass = 'text-warning';
                } else if (distance <= 100) {
                    statusText = `✓ 위치 확인 완료 (${Math.round(distance)}m, 정확도: ${Math.round(accuracy || 0)}m)`;
                    statusClass = 'text-success';
                    canSubmit = true;
                } else {
                    statusText = `✗ 성에서 너무 멀리 떨어져 있습니다 (${Math.round(distance)}m)`;
                    statusClass = 'text-danger';
                }

                statusDiv.innerHTML = `<span class="${statusClass}">${statusText}</span>`;
                submitBtn.disabled = !canSubmit;
            },
            function(error) {
                statusDiv.innerHTML = '<span class="text-danger">위치 정보를 가져올 수 없습니다</span>';
                console.error(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        statusDiv.innerHTML = '<span class="text-danger">이 브라우저는 위치 서비스를 지원하지 않습니다</span>';
    }
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371e3; // 지구 반지름 (미터)
    const φ1 = lat1 * Math.PI/180;
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lng2-lng1) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c;
}

// 사진 미리보기 (향상된 기능)
document.getElementById('photos').addEventListener('change', function(e) {
    const previewDiv = document.getElementById('photo-preview');
    previewDiv.innerHTML = '';

    const files = Array.from(e.target.files);
    let totalSize = 0;
    let validFiles = 0;

    // 파일 검증 및 크기 확인
    files.forEach((file, index) => {
        totalSize += file.size;

        if (!file.type.startsWith('image/')) {
            previewDiv.innerHTML += '<div class="alert alert-danger">이미지 파일만 업로드 가능합니다.</div>';
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB 초과
            previewDiv.innerHTML += `<div class="alert alert-danger">${file.name} 파일이 너무 큽니다 (최대 2MB)</div>`;
            return;
        }

        validFiles++;
    });

    // 총 파일 크기 확인 (10MB 제한)
    if (totalSize > 10 * 1024 * 1024) {
        previewDiv.innerHTML += '<div class="alert alert-danger">전체 파일 크기가 10MB를 초과할 수 없습니다.</div>';
        return;
    }

    if (validFiles < 3) {
        previewDiv.innerHTML += '<div class="alert alert-warning">최소 3장의 유효한 사진이 필요합니다</div>';
        return;
    }

    // 미리보기 생성
    files.forEach((file, index) => {
        if (file.type.startsWith('image/') && file.size <= 2 * 1024 * 1024) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.createElement('div');
                container.className = 'position-relative d-inline-block me-2 mb-2';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.title = `${file.name} (${(file.size/1024).toFixed(1)}KB)`;

                // 파일 정보 오버레이
                const overlay = document.createElement('div');
                overlay.className = 'position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-1';
                overlay.style.fontSize = '0.7rem';
                overlay.innerHTML = `${index + 1}. ${(file.size/1024).toFixed(0)}KB`;

                container.appendChild(img);
                container.appendChild(overlay);
                previewDiv.appendChild(container);
            };
            reader.readAsDataURL(file);
        }
    });

    // 업로드 상태 표시
    const statusInfo = document.createElement('div');
    statusInfo.className = 'mt-2 small text-muted';
    statusInfo.innerHTML = `총 ${validFiles}장 선택됨 (${(totalSize/1024).toFixed(1)}KB)`;
    previewDiv.appendChild(statusInfo);
});

document.getElementById('stamp_photo').addEventListener('change', function(e) {
    const previewDiv = document.getElementById('stamp-preview');
    previewDiv.innerHTML = '';

    const file = e.target.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            previewDiv.innerHTML = '<div class="alert alert-danger">이미지 파일만 업로드 가능합니다.</div>';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            previewDiv.innerHTML = '<div class="alert alert-danger">파일 크기가 너무 큽니다 (최대 2MB)</div>';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.createElement('div');
            container.className = 'position-relative d-inline-block';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail';
            img.style.width = '150px';
            img.style.height = '150px';
            img.style.objectFit = 'cover';
            img.title = `${file.name} (${(file.size/1024).toFixed(1)}KB)`;

            // 파일 정보 오버레이
            const overlay = document.createElement('div');
            overlay.className = 'position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-1 text-center';
            overlay.style.fontSize = '0.7rem';
            overlay.innerHTML = `스탬프 (${(file.size/1024).toFixed(0)}KB)`;

            container.appendChild(img);
            container.appendChild(overlay);
            previewDiv.appendChild(container);
        };
        reader.readAsDataURL(file);
    }
});

// 페이지 로드 시 자동으로 위치 가져오기 시도
window.addEventListener('load', function() {
    getCurrentLocation();
});
</script>
@endsection