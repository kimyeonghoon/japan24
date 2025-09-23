<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        /* Additional custom styles */
    </style>

    <style>
        .castle-card {
            transition: transform 0.2s;
        }
        .castle-card:hover {
            transform: translateY(-5px);
        }
        .badge-earned {
            opacity: 1;
        }
        .badge-locked {
            opacity: 0.3;
            filter: grayscale(100%);
        }
        .progress-circle {
            position: relative;
            width: 120px;
            height: 120px;
        }
        .navbar-brand {
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-building me-2"></i>24명성 인증 앱
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'fw-bold' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>대시보드
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('castles.*') && !request()->routeIs('castles.map') ? 'fw-bold' : '' }}" href="{{ route('castles.index') }}">
                                <i class="bi bi-list me-1"></i>성 목록
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('castles.map') ? 'fw-bold' : '' }}" href="{{ route('castles.map') }}">
                                <i class="bi bi-geo-alt me-1"></i>지도
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('visit-records.*') ? 'fw-bold' : '' }}" href="{{ route('visit-records.index') }}">
                                <i class="bi bi-journal-check me-1"></i>방문 기록
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('social.*') ? 'fw-bold' : '' }}" href="{{ route('social.feed') }}">
                                <i class="bi bi-people me-1"></i>소셜
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative {{ request()->routeIs('notifications.*') ? 'fw-bold' : '' }}" href="{{ route('notifications.index') }}" id="notificationsLink">
                                <i class="bi bi-bell me-1"></i>알림
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      id="notificationBadge" style="display: none; font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link text-warning {{ request()->routeIs('admin.*') ? 'fw-bold' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-tools me-1"></i>관리자
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-1"></i>로그아웃
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">로그인</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">회원가입</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 24명성 인증 앱. 일본 성 순례를 즐겁게!</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

    @stack('scripts')
</body>
</html>