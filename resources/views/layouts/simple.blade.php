<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>24명성 인증 앱</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">24명성 인증 앱</a>

            <div class="navbar-nav ms-auto">
                @auth
                    <a class="nav-link" href="/dashboard">대시보드</a>
                    <a class="nav-link" href="{{ route('castles.index') }}">성 목록</a>
                    <a class="nav-link" href="{{ route('castles.map') }}">지도</a>
                    <a class="nav-link" href="{{ route('visit-records.index') }}">내 기록</a>
                    <a class="nav-link" href="{{ route('social.feed') }}">🤝 소셜</a>
                    <a class="nav-link position-relative" href="{{ route('notifications.index') }}" id="notificationsLink">
                        🔔 알림
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              id="notificationBadge" style="display: none; font-size: 0.6rem;">
                            0
                        </span>
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a class="nav-link text-warning" href="{{ route('admin.dashboard') }}">
                            🛠️ 관리자
                        </a>
                    @endif
                    <form method="POST" action="/logout" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">로그아웃</button>
                    </form>
                @else
                    <a class="nav-link" href="/login">로그인</a>
                    <a class="nav-link" href="/register">회원가입</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
    <script>
        // 알림 개수 업데이트 함수
        function updateNotificationBadge() {
            fetch('{{ route("api.notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.log('알림 개수 업데이트 실패:', error));
        }

        // 페이지 로드 시 알림 개수 업데이트
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationBadge();

            // 30초마다 알림 개수 업데이트 (선택적)
            setInterval(updateNotificationBadge, 30000);
        });
    </script>
    @endauth
</body>
</html>