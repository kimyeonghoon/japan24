<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="일본 24명성 방문 인증 애플리케이션 - GPS 기반 방문 확인 및 배지 시스템">
    <meta name="keywords" content="일본성, 명성, 방문인증, 여행, 일본여행">

    <!-- DNS Prefetch for better performance -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//unpkg.com">

    <!-- Preconnect for external resources -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <title>24명성 인증 앱</title>

    <!-- Bootstrap CSS with integrity check -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-9ndCyUa6J81PMIO3UOqC3YNDPNcGR6+7dWy6LCtKF6vEU8rNGJj5d9rG3t3ZU7"
          crossorigin="anonymous">

    <!-- Custom CSS for performance optimization -->
    <style>
        /* Critical CSS - Above the fold styles */
        .navbar { will-change: transform; }
        .container { max-width: 1200px; }

        /* Image optimization */
        img {
            max-width: 100%;
            height: auto;
            loading: lazy;
        }

        /* Lazy loading placeholder */
        .lazy-image {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Reduce layout shift */
        .notification-badge {
            min-width: 20px;
            min-height: 20px;
        }
    </style>
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

    <!-- Optimized JavaScript loading -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"
            defer></script>

    @auth
    <script>
        // Performance optimized notification management
        let notificationUpdateTimer;
        let isPageVisible = true;

        // Debounced notification update function
        function updateNotificationBadge() {
            // Skip updates when page is not visible
            if (!isPageVisible) return;

            // Clear existing timer
            if (notificationUpdateTimer) {
                clearTimeout(notificationUpdateTimer);
            }

            notificationUpdateTimer = setTimeout(() => {
                fetch('{{ route("api.notifications.unread-count") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.warn('알림 개수 업데이트 실패:', error);
                });
            }, 100); // 100ms debounce
        }

        // Page visibility change handler
        function handleVisibilityChange() {
            isPageVisible = !document.hidden;
            if (isPageVisible) {
                updateNotificationBadge();
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initial update
            updateNotificationBadge();

            // Update when page becomes visible
            document.addEventListener('visibilitychange', handleVisibilityChange);

            // Periodic update (only when page is visible)
            setInterval(() => {
                if (isPageVisible) {
                    updateNotificationBadge();
                }
            }, 30000); // 30 seconds

            // Update on focus (user returns to tab)
            window.addEventListener('focus', updateNotificationBadge);
        });
    </script>
    @endauth
</body>
</html>