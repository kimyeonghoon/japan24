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

<!-- 카카오맵 API -->
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_KAKAO_API_KEY"></script>

<script>
let map;
let markers = [];

// 카카오맵 초기화
function initMap() {
    // 일본 중심부 좌표 (도쿄 근처)
    const mapContainer = document.getElementById('map');
    const mapOption = {
        center: new kakao.maps.LatLng(35.6762, 139.6503), // 도쿄
        level: 6
    };

    map = new kakao.maps.Map(mapContainer, mapOption);

    // 모든 성 마커 추가
    @foreach($castles as $castle)
        addCastleMarker({{ $castle->latitude }}, {{ $castle->longitude }},
                       "{{ $castle->name_korean }}", "{{ $castle->name }}",
                       "{{ route('visit-records.create', $castle) }}", {{ $castle->id }});
    @endforeach

    // 모든 마커가 보이도록 지도 범위 조정
    if (markers.length > 0) {
        const bounds = new kakao.maps.LatLngBounds();
        markers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        map.setBounds(bounds);
    }
}

// 성 마커 추가
function addCastleMarker(lat, lng, koreanName, japaneseName, visitUrl, castleId) {
    const position = new kakao.maps.LatLng(lat, lng);

    // 커스텀 마커 이미지 (성 아이콘)
    const imageSrc = 'data:image/svg+xml;base64,' + btoa(`
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
            <circle cx="12" cy="12" r="10" fill="#007bff" stroke="#fff" stroke-width="2"/>
            <text x="12" y="16" text-anchor="middle" fill="white" font-size="12" font-family="Arial">🏰</text>
        </svg>
    `);

    const imageSize = new kakao.maps.Size(32, 32);
    const imageOption = { offset: new kakao.maps.Point(16, 32) };
    const markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imageOption);

    const marker = new kakao.maps.Marker({
        position: position,
        image: markerImage
    });

    marker.setMap(map);
    markers.push(marker);

    // 인포윈도우 내용
    const infowindowContent = `
        <div style="padding:10px; min-width:200px;">
            <h6 style="margin:0 0 5px 0; color:#007bff;">${koreanName}</h6>
            <p style="margin:0 0 10px 0; font-size:12px; color:#666;">${japaneseName}</p>
            <div style="text-align:center;">
                <a href="${visitUrl}" class="btn btn-sm btn-success" style="text-decoration:none;
                   background:#28a745; color:white; padding:5px 10px; border-radius:4px; font-size:12px;">
                    방문 인증하기
                </a>
            </div>
        </div>
    `;

    const infowindow = new kakao.maps.InfoWindow({
        content: infowindowContent,
        removable: true
    });

    // 마커 클릭 이벤트
    kakao.maps.event.addListener(marker, 'click', function() {
        // 다른 인포윈도우 닫기
        markers.forEach(m => {
            if (m.infowindow) {
                m.infowindow.close();
            }
        });

        infowindow.open(map, marker);
        marker.infowindow = infowindow;

        // 해당 성 카드 하이라이트
        highlightCastleCard(castleId);
    });
}

// 특정 성에 지도 포커스
function focusOnCastle(lat, lng) {
    const moveLatLon = new kakao.maps.LatLng(lat, lng);
    map.setCenter(moveLatLon);
    map.setLevel(3); // 확대
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
    // 카카오맵 API가 로드되었는지 확인
    if (typeof kakao !== 'undefined' && kakao.maps) {
        initMap();
    } else {
        // API 키가 없거나 로드 실패 시 구글맵으로 대체
        initGoogleMap();
    }
});

// 구글맵 대체 함수 (카카오맵 API 사용 불가시)
function initGoogleMap() {
    const mapDiv = document.getElementById('map');
    mapDiv.innerHTML = `
        <div class="alert alert-info text-center" role="alert">
            <h5>지도 서비스 준비 중</h5>
            <p>카카오맵 API 키 설정이 필요합니다. 현재는 성 목록으로 위치를 확인해주세요.</p>
            <div class="mt-3">
                <small class="text-muted">
                    각 성의 "지도에서 보기" 버튼을 클릭하면 구글맵이나 카카오맵에서 위치를 확인할 수 있습니다.
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
                window.open(`https://maps.google.com?q=${lat},${lng}`, '_blank');
            };
            btn.innerHTML = '구글맵에서 보기';
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