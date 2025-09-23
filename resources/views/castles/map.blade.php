@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>24명성 지도</h2>
            <div>
                <a href="{{ route('castles.index') }}" class="btn btn-outline-primary">목록 보기</a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">대시보드</a>
            </div>
        </div>

        <!-- 지도 컨테이너 -->
        <div class="card mb-4">
            <div class="card-body">
                <div id="map" style="height: 600px; width: 100%;"></div>
            </div>
        </div>

        <!-- 성 목록 (지도 아래) -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">성 목록 ({{ $castles->count() }}개)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($castles as $castle)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 castle-card" data-castle-id="{{ $castle->id }}"
                                 data-lat="{{ $castle->latitude }}" data-lng="{{ $castle->longitude }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $castle->name_korean }}</h6>
                                    <p class="card-text small text-muted">{{ $castle->name }}</p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $castle->prefecture }}<br>
                                            <i class="bi bi-clock"></i> {{ $castle->visiting_hours }}<br>
                                            <i class="bi bi-currency-yen"></i>
                                            @if($castle->entrance_fee > 0)
                                                {{ number_format($castle->entrance_fee) }}원
                                            @else
                                                무료
                                            @endif
                                        </small>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="focusOnCastle({{ $castle->latitude }}, {{ $castle->longitude }})">
                                            지도에서 보기
                                        </button>
                                        <a href="{{ route('visit-records.create', $castle) }}" class="btn btn-sm btn-success">
                                            방문 인증
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
let map;
let markers = [];

// Leaflet + OpenStreetMap 초기화
function initMap() {
    // 일본 중심부 좌표 (도쿄 근처)
    map = L.map('map').setView([35.6762, 139.6503], 6);

    // OpenStreetMap 타일 레이어 추가
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // 모든 성 마커 추가
    @foreach($castles as $castle)
        addCastleMarker({{ $castle->latitude }}, {{ $castle->longitude }},
                       "{{ $castle->name_korean }}", "{{ $castle->name }}",
                       "{{ route('visit-records.create', $castle) }}", {{ $castle->id }});
    @endforeach

    // 모든 마커가 보이도록 지도 범위 조정
    if (markers.length > 0) {
        const group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

// 성 마커 추가
function addCastleMarker(lat, lng, koreanName, japaneseName, visitUrl, castleId) {
    // 커스텀 성 아이콘 (성 이름 포함)
    const castleIcon = L.divIcon({
        className: 'custom-castle-marker',
        html: `
            <div style="
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            ">
                <div style="
                    background: #007bff;
                    color: white;
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 3px solid white;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                    font-size: 16px;
                ">🏰</div>
                <div style="
                    background: rgba(255,255,255,0.95);
                    border: 1px solid #007bff;
                    border-radius: 4px;
                    padding: 2px 6px;
                    font-size: 11px;
                    font-weight: bold;
                    color: #007bff;
                    margin-top: 2px;
                    white-space: nowrap;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                ">${koreanName}</div>
            </div>
        `,
        iconSize: [120, 60],
        iconAnchor: [60, 32],
        popupAnchor: [0, -32]
    });

    const marker = L.marker([lat, lng], { icon: castleIcon }).addTo(map);
    markers.push(marker);

    // 팝업 내용
    const popupContent = `
        <div style="min-width:200px; text-align:center;">
            <h6 style="margin:0 0 5px 0; color:#007bff; font-weight:bold;">${koreanName}</h6>
            <p style="margin:0 0 10px 0; font-size:12px; color:#666;">${japaneseName}</p>
            <a href="${visitUrl}"
               class="btn btn-sm btn-success"
               style="text-decoration:none; background:#28a745; color:white;
                      padding:8px 15px; border-radius:4px; font-size:12px;
                      display:inline-block; margin-top:5px;">
                🚩 방문 인증하기
            </a>
        </div>
    `;

    marker.bindPopup(popupContent);

    // 마커 클릭 이벤트
    marker.on('click', function() {
        // 해당 성 카드 하이라이트
        highlightCastleCard(castleId);
    });
}

// 특정 성에 지도 포커스
function focusOnCastle(lat, lng) {
    map.setView([lat, lng], 15); // 확대해서 보기
}

// 성 카드 하이라이트
function highlightCastleCard(castleId) {
    // 모든 카드 하이라이트 제거
    document.querySelectorAll('.castle-card').forEach(card => {
        card.classList.remove('border-primary');
        card.style.boxShadow = '';
    });

    // 해당 카드 하이라이트
    const targetCard = document.querySelector(`[data-castle-id="${castleId}"]`);
    if (targetCard) {
        targetCard.classList.add('border-primary');
        targetCard.style.boxShadow = '0 0 10px rgba(0,123,255,0.3)';
        targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// 페이지 로드 시 지도 초기화
window.addEventListener('load', function() {
    // Leaflet이 로드되었는지 확인
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        console.error('Leaflet 라이브러리 로드 실패');
        initFallbackMap();
    }
});

// 대체 함수 (Leaflet 로드 실패시)
function initFallbackMap() {
    const mapDiv = document.getElementById('map');
    mapDiv.innerHTML = `
        <div class="alert alert-info text-center" role="alert">
            <h5>📍 지도 서비스</h5>
            <p>OpenStreetMap 기반 지도를 준비 중입니다. 각 성의 위치는 아래 목록에서 확인하실 수 있습니다.</p>
            <div class="mt-3">
                <small class="text-muted">
                    "지도에서 보기" 버튼을 클릭하면 외부 지도에서 위치를 확인할 수 있습니다.
                </small>
            </div>
        </div>
    `;

    // 지도에서 보기 버튼을 외부 지도 링크로 변경
    document.querySelectorAll('.castle-card').forEach(card => {
        const lat = card.dataset.lat;
        const lng = card.dataset.lng;
        const btn = card.querySelector('.btn-outline-primary');
        if (btn) {
            btn.onclick = function() {
                window.open(`https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=15`, '_blank');
            };
            btn.innerHTML = 'OpenStreetMap에서 보기';
        }
    });
}
</script>

<style>
.castle-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.castle-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

#map {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection