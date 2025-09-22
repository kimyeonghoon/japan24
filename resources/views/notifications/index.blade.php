@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🔔 알림</h2>
            <div>
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            모든 알림 읽음 처리 ({{ $unreadCount }}개)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($notifications->count() > 0)
            <div class="card">
                <div class="card-body p-0">
                    @foreach($notifications as $notification)
                        <div class="notification-item d-flex align-items-start p-3 {{ $notification->isUnread() ? 'bg-light border-start border-primary border-3' : '' }} border-bottom">
                            <div class="me-3" style="font-size: 1.5rem; min-width: 40px;">
                                @if($notification->type === 'badge_earned')
                                    🏅
                                @elseif($notification->type === 'visit_approved')
                                    ✅
                                @elseif($notification->type === 'visit_rejected')
                                    ❌
                                @else
                                    🔔
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 {{ $notification->isUnread() ? 'fw-bold' : '' }}">
                                            {{ $notification->title }}
                                        </h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>

                                        @if($notification->data)
                                            <div class="mt-2">
                                                @if($notification->type === 'badge_earned')
                                                    <div class="badge bg-warning text-dark">
                                                        🏅 {{ $notification->data['badge_name'] ?? '' }}
                                                    </div>
                                                    @if(isset($notification->data['required_visits']))
                                                        <small class="text-muted d-block mt-1">
                                                            {{ $notification->data['required_visits'] }}회 방문 달성
                                                        </small>
                                                    @endif
                                                @elseif($notification->type === 'visit_approved' || $notification->type === 'visit_rejected')
                                                    <div class="badge {{ $notification->type === 'visit_approved' ? 'bg-success' : 'bg-danger' }}">
                                                        🏰 {{ $notification->data['castle_name'] ?? '' }}
                                                    </div>
                                                    @if(isset($notification->data['visited_at']))
                                                        <small class="text-muted d-block mt-1">
                                                            방문일시: {{ $notification->data['visited_at'] }}
                                                        </small>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-column align-items-end">
                                        @if($notification->isUnread())
                                            <span class="badge bg-primary mb-2" style="font-size: 0.6rem;">NEW</span>
                                        @endif
                                        @if($notification->isUnread())
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                        onclick="markAsRead({{ $notification->id }}, this); return false;">
                                                    읽음 처리
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 페이지네이션 -->
            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div style="font-size: 4rem; color: #6c757d;">🔔</div>
                <h5 class="text-muted mt-3">알림이 없습니다</h5>
                <p class="text-muted">새로운 배지를 획득하거나 방문 기록이 승인되면 알림을 받을 수 있습니다.</p>
                <div class="mt-4">
                    <a href="{{ route('castles.index') }}" class="btn btn-primary me-2">성 둘러보기</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">대시보드로</a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function markAsRead(notificationId, button) {
    // 버튼 비활성화
    button.disabled = true;
    button.textContent = '처리 중...';

    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 알림 항목에서 NEW 배지 및 강조 스타일 제거
            const notificationItem = button.closest('.notification-item');
            notificationItem.classList.remove('bg-light', 'border-start', 'border-primary', 'border-3');

            // NEW 배지 제거
            const newBadge = notificationItem.querySelector('.badge.bg-primary');
            if (newBadge) newBadge.remove();

            // 제목 굵기 제거
            const title = notificationItem.querySelector('h6');
            if (title) title.classList.remove('fw-bold');

            // 버튼 제거
            button.remove();

            // 네비게이션의 알림 배지 업데이트
            if (typeof updateNotificationBadge === 'function') {
                updateNotificationBadge();
            }
        }
    })
    .catch(error => {
        console.error('읽음 처리 실패:', error);
        button.disabled = false;
        button.textContent = '읽음 처리';
    });
}
</script>

<style>
.notification-item:hover {
    background-color: var(--bs-gray-50) !important;
}

.notification-item.bg-light:hover {
    background-color: var(--bs-gray-100) !important;
}
</style>
@endsection