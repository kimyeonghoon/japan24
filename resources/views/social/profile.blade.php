@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- 프로필 헤더 -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="me-4" style="font-size: 4rem;">
                                {{ $user->isAdmin() ? '👑' : '👤' }}
                            </div>
                            <div>
                                <h2 class="mb-1">
                                    {{ $user->name }}
                                    @if($user->isAdmin())
                                        <span class="badge bg-warning text-dark">관리자</span>
                                    @endif
                                </h2>
                                <p class="text-muted mb-3">{{ $user->email }}</p>

                                <!-- 친구 관계 버튼 -->
                                @if($user->id !== auth()->id())
                                    <div id="friendship-actions">
                                        @if($friendshipStatus === 'friends')
                                            <button class="btn btn-success" disabled>
                                                👥 친구
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm ms-2 unfriend-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">
                                                친구 끊기
                                            </button>
                                        @elseif($friendshipStatus === 'request_sent')
                                            <button class="btn btn-warning" disabled>
                                                ⏳ 요청 보냄
                                            </button>
                                        @elseif($friendshipStatus === 'request_received')
                                            <button class="btn btn-success me-2 accept-request-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">
                                                요청 수락
                                            </button>
                                            <button class="btn btn-outline-secondary reject-request-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">
                                                거부
                                            </button>
                                        @else
                                            <button class="btn btn-primary friend-request-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">
                                                👥 친구 요청
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-info">내 프로필</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- 통계 정보 -->
                        <div class="row text-center">
                            <div class="col-6 col-md-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary mb-0">{{ number_format($stats['total_visits']) }}</h4>
                                    <small class="text-muted">방문한 성</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-warning mb-0">{{ number_format($stats['total_badges']) }}</h4>
                                    <small class="text-muted">획득 배지</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-success mb-0">{{ number_format($stats['total_friends']) }}</h4>
                                    <small class="text-muted">친구</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-12">
                                <div class="border rounded p-3">
                                    <h4 class="text-info mb-0">{{ number_format($stats['completion_rate'], 1) }}%</h4>
                                    <small class="text-muted">완주율</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 방문 기록 -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    🏰 {{ $user->name }}님의 방문 기록
                    @if($user->id === auth()->id())
                        ({{ $visitRecords->total() }}개)
                    @else
                        (공개: {{ $visitRecords->total() }}개)
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($visitRecords->count() > 0)
                    <div class="row">
                        @foreach($visitRecords as $record)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    @if($record->getPhotos())
                                        @php $photos = $record->getPhotos(); @endphp
                                        <img src="{{ asset('storage/' . $photos[0]) }}"
                                             class="card-img-top"
                                             alt="방문 사진"
                                             style="height: 200px; object-fit: cover;">
                                    @endif

                                    <div class="card-body">
                                        <h6 class="card-title">{{ $record->castle->name_korean }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                {{ $record->created_at->format('Y.m.d') }}
                                                @if($record->likes_count > 0)
                                                    • ❤️ {{ number_format($record->likes_count) }}
                                                @endif
                                            </small>
                                        </p>

                                        @if($record->visit_notes)
                                            <p class="card-text small">
                                                {{ Str::limit($record->visit_notes, 80) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                📍 {{ $record->castle->name }}
                                            </small>

                                            @if($record->isVisibleTo(auth()->user()))
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('visit-records.show', $record) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        보기
                                                    </a>

                                                    @if($record->isPublic() && $record->user_id !== auth()->id())
                                                        <button class="btn btn-outline-danger btn-sm like-btn"
                                                                data-record-id="{{ $record->id }}"
                                                                data-liked="{{ $record->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                                                            {{ $record->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- 페이지네이션 -->
                    <div class="d-flex justify-content-center">
                        {{ $visitRecords->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 3rem; color: #6c757d;">🏰</div>
                        <h5 class="text-muted mt-3">
                            @if($user->id === auth()->id())
                                아직 방문한 성이 없습니다
                            @else
                                {{ $user->name }}님의 공개 방문 기록이 없습니다
                            @endif
                        </h5>
                        @if($user->id === auth()->id())
                            <p class="text-muted">첫 번째 성을 방문해보세요!</p>
                            <a href="{{ route('castles.index') }}" class="btn btn-primary">
                                성 둘러보기
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 친구 관련 액션들 - social/friends.blade.php와 동일한 코드
    // 친구 요청 보내기
    document.querySelectorAll('.friend-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            if (!confirm(`${userName}님에게 친구 요청을 보내시겠습니까?`)) return;

            handleFriendshipAction(`/social/friend-request/${userId}`, 'POST', this);
        });
    });

    // 친구 요청 수락
    document.querySelectorAll('.accept-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            handleFriendshipAction(`/social/friend-request/${userId}/accept`, 'POST', this);
        });
    });

    // 친구 요청 거부
    document.querySelectorAll('.reject-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            handleFriendshipAction(`/social/friend-request/${userId}/reject`, 'POST', this);
        });
    });

    // 친구 끊기
    document.querySelectorAll('.unfriend-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            if (!confirm(`${userName}님과의 친구 관계를 해제하시겠습니까?`)) return;

            handleFriendshipAction(`/social/friend/${userId}`, 'DELETE', this);
        });
    });

    // 좋아요 버튼
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const recordId = this.dataset.recordId;

            this.disabled = true;

            fetch(`/social/visit-record/${recordId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.textContent = data.liked ? '❤️' : '🤍';
                    this.dataset.liked = data.liked ? 'true' : 'false';
                }
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    function handleFriendshipAction(url, method, button) {
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = '처리 중...';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // 페이지 새로고침으로 상태 업데이트
            } else {
                alert(data.message || '오류가 발생했습니다.');
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    }

    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
               document.querySelector('input[name="_token"]')?.value;
    }
});
</script>
@endsection