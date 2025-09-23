@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>👥 친구 관리</h2>
            <div>
                <a href="{{ route('social.feed') }}" class="btn btn-outline-primary">
                    🤝 소셜 피드
                </a>
            </div>
        </div>

        <!-- 탭 네비게이션 -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'friends' ? 'active' : '' }}"
                   href="{{ route('social.friends', ['tab' => 'friends']) }}">
                    친구 목록
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'received' ? 'active' : '' }}"
                   href="{{ route('social.friends', ['tab' => 'received']) }}">
                    받은 요청
                    @if(isset($receivedRequests) && $receivedRequests->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $receivedRequests->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'sent' ? 'active' : '' }}"
                   href="{{ route('social.friends', ['tab' => 'sent']) }}">
                    보낸 요청
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'suggestions' ? 'active' : '' }}"
                   href="{{ route('social.friends', ['tab' => 'suggestions']) }}">
                    추천 친구
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'search' ? 'active' : '' }}"
                   href="{{ route('social.friends', ['tab' => 'search']) }}">
                    친구 찾기
                </a>
            </li>
        </ul>

        @if($tab === 'search')
            <!-- 검색 폼 -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('social.friends') }}">
                        <input type="hidden" name="tab" value="search">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                       placeholder="사용자명으로 검색..." value="{{ $search }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">검색</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($searchResults))
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">검색 결과</h5>
                    </div>
                    <div class="card-body">
                        @if($searchResults->count() > 0)
                            @foreach($searchResults as $user)
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="font-size: 2rem;">
                                            {{ $user->isAdmin() ? '👑' : '👤' }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('social.profile', $user) }}" class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                                @if($user->isAdmin())
                                                    <span class="badge bg-warning text-dark">관리자</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                방문한 성: {{ number_format($user->visit_records_count) }}개
                                            </small>
                                        </div>
                                    </div>

                                    @php $status = auth()->user()->getFriendshipStatus($user); @endphp
                                    @if($status === 'friends')
                                        <span class="badge bg-success">친구</span>
                                    @elseif($status === 'request_sent')
                                        <span class="badge bg-warning">요청 보냄</span>
                                    @elseif($status === 'request_received')
                                        <span class="badge bg-info">요청 받음</span>
                                    @else
                                        <button class="btn btn-primary btn-sm friend-request-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}">
                                            친구 요청
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted py-4">검색 결과가 없습니다.</p>
                        @endif
                    </div>
                </div>
            @endif

        @elseif($tab === 'friends' && isset($friends))
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">내 친구들 ({{ $friends->total() }}명)</h5>
                </div>
                <div class="card-body">
                    @if($friends->count() > 0)
                        @foreach($friends as $friend)
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="font-size: 2rem;">
                                        {{ $friend->isAdmin() ? '👑' : '👤' }}
                                    </div>
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('social.profile', $friend) }}" class="text-decoration-none">
                                                {{ $friend->name }}
                                            </a>
                                            @if($friend->isAdmin())
                                                <span class="badge bg-warning text-dark">관리자</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            방문한 성: {{ number_format($friend->visit_records_count) }}개
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('social.profile', $friend) }}" class="btn btn-outline-primary btn-sm">
                                        프로필
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-center mt-4">
                            {{ $friends->appends(['tab' => 'friends'])->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div style="font-size: 3rem; color: #6c757d;">👥</div>
                            <h5 class="text-muted mt-3">아직 친구가 없습니다</h5>
                            <p class="text-muted">친구를 찾아서 연결해보세요!</p>
                            <a href="{{ route('social.friends', ['tab' => 'search']) }}" class="btn btn-primary">
                                친구 찾기
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($tab === 'suggestions' && isset($suggestions))
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">추천 친구 (공통 친구 기반)</h5>
                </div>
                <div class="card-body">
                    @if($suggestions->count() > 0)
                        @foreach($suggestions as $suggestion)
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="font-size: 2rem;">
                                        {{ $suggestion->isAdmin() ? '👑' : '👤' }}
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $suggestion->name }}</h6>
                                        <small class="text-muted">
                                            @if($suggestion->common_friends_count > 0)
                                                공통 친구 {{ $suggestion->common_friends_count }}명
                                            @else
                                                새로운 사용자
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary btn-sm send-request-btn"
                                            data-user-id="{{ $suggestion->id }}"
                                            data-user-name="{{ $suggestion->name }}">
                                        친구 요청
                                    </button>
                                    <a href="{{ route('social.profile', $suggestion) }}" class="btn btn-outline-primary btn-sm">
                                        프로필
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div style="font-size: 3rem; color: #6c757d;">💡</div>
                            <h5 class="text-muted mt-3">추천 친구가 없습니다</h5>
                            <p class="text-muted">
                                더 많은 친구를 만들어 추천 시스템을 활용해보세요!
                            </p>
                            <a href="{{ route('social.friends', ['tab' => 'search']) }}" class="btn btn-primary">
                                직접 친구 찾기
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($tab === 'received' && isset($receivedRequests))
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">받은 친구 요청 ({{ $receivedRequests->total() }}개)</h5>
                </div>
                <div class="card-body">
                    @if($receivedRequests->count() > 0)
                        @foreach($receivedRequests as $request)
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="font-size: 2rem;">
                                        {{ $request->user->isAdmin() ? '👑' : '👤' }}
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $request->user->name }}</h6>
                                        <small class="text-muted">
                                            {{ $request->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-success btn-sm me-2 accept-request-btn"
                                            data-user-id="{{ $request->user->id }}"
                                            data-user-name="{{ $request->user->name }}">
                                        수락
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm reject-request-btn"
                                            data-user-id="{{ $request->user->id }}"
                                            data-user-name="{{ $request->user->name }}">
                                        거부
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div style="font-size: 3rem; color: #6c757d;">📬</div>
                            <h5 class="text-muted mt-3">받은 친구 요청이 없습니다</h5>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($tab === 'sent' && isset($sentRequests))
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">보낸 친구 요청 ({{ $sentRequests->total() }}개)</h5>
                </div>
                <div class="card-body">
                    @if($sentRequests->count() > 0)
                        @foreach($sentRequests as $request)
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="font-size: 2rem;">
                                        {{ $request->friend->isAdmin() ? '👑' : '👤' }}
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $request->friend->name }}</h6>
                                        <small class="text-muted">
                                            {{ $request->created_at->diffForHumans() }} 요청
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-warning">대기 중</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div style="font-size: 3rem; color: #6c757d;">📤</div>
                            <h5 class="text-muted mt-3">보낸 친구 요청이 없습니다</h5>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 친구 요청 보내기
    document.querySelectorAll('.friend-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            if (!confirm(`${userName}님에게 친구 요청을 보내시겠습니까?`)) {
                return;
            }

            this.disabled = true;
            this.textContent = '처리 중...';

            fetch(`/social/friend-request/${userId}`, {
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
                    this.textContent = '요청 보냄';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-warning');
                } else {
                    alert(data.message || '오류가 발생했습니다.');
                    this.disabled = false;
                    this.textContent = '친구 요청';
                }
            });
        });
    });

    // 친구 요청 수락
    document.querySelectorAll('.accept-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;

            this.disabled = true;
            this.textContent = '처리 중...';

            fetch(`/social/friend-request/${userId}/accept`, {
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
                    location.reload();
                } else {
                    alert(data.message || '오류가 발생했습니다.');
                }
            });
        });
    });

    // 친구 요청 거부
    document.querySelectorAll('.reject-request-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;

            this.disabled = true;
            this.textContent = '처리 중...';

            fetch(`/social/friend-request/${userId}/reject`, {
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
                    location.reload();
                } else {
                    alert(data.message || '오류가 발생했습니다.');
                }
            });
        });
    });
});
</script>
@endsection