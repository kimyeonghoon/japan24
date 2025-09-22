@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🤝 소셜 피드</h2>
            <div>
                <a href="{{ route('social.friends') }}" class="btn btn-outline-primary">
                    👥 친구 관리
                </a>
            </div>
        </div>

        @if($visitRecords->count() > 0)
            @foreach($visitRecords as $record)
                <div class="card mb-4 social-post">
                    <div class="card-body">
                        <!-- 사용자 헤더 -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3" style="font-size: 2rem;">
                                @if($record->user->id === auth()->id())
                                    😊
                                @else
                                    👤
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('social.profile', $record->user) }}" class="text-decoration-none">
                                                <strong>{{ $record->user->name }}</strong>
                                            </a>
                                            @if($record->user->isAdmin())
                                                <span class="badge bg-warning text-dark ms-1">관리자</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            🏰 <strong>{{ $record->castle->name_korean }}</strong> 방문
                                            • {{ $record->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 방문 내용 -->
                        @if($record->visit_notes)
                            <p class="mb-3">{{ $record->visit_notes }}</p>
                        @endif

                        <!-- 사진들 (첫 번째만 표시) -->
                        @if($record->getPhotos())
                            @php $photos = $record->getPhotos(); @endphp
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $photos[0]) }}"
                                     class="img-fluid rounded"
                                     alt="방문 사진"
                                     style="max-height: 400px; object-fit: cover; width: 100%;">

                                @if(count($photos) > 1)
                                    <small class="text-muted d-block mt-2">
                                        📸 {{ count($photos) }}장의 사진
                                        <a href="{{ route('visit-records.show', $record) }}" class="ms-2">모두 보기</a>
                                    </small>
                                @endif
                            </div>
                        @endif

                        <!-- 상호작용 버튼 -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <div class="d-flex gap-3">
                                <!-- 좋아요 버튼 -->
                                <button class="btn btn-link p-0 like-btn"
                                        data-record-id="{{ $record->id }}"
                                        data-liked="{{ $record->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                                    <span class="like-icon">
                                        {{ $record->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}
                                    </span>
                                    <span class="likes-count">{{ number_format($record->likes_count) }}</span>
                                </button>

                                <!-- 보기 버튼 -->
                                <a href="{{ route('visit-records.show', $record) }}" class="btn btn-link p-0">
                                    💬 자세히 보기
                                </a>
                            </div>

                            <!-- 성 정보 -->
                            <small class="text-muted">
                                📍 {{ $record->castle->name }}
                                @if($record->castle->prefecture)
                                    ({{ $record->castle->prefecture }})
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- 페이지네이션 -->
            <div class="d-flex justify-content-center">
                {{ $visitRecords->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div style="font-size: 4rem; color: #6c757d;">🤝</div>
                <h5 class="text-muted mt-3">아직 피드가 비어있습니다</h5>
                <p class="text-muted">
                    친구들과 연결하고 방문 기록을 공유해보세요!<br>
                    먼저 성을 방문하고 기록을 남겨보시는 건 어떨까요?
                </p>
                <div class="mt-4">
                    <a href="{{ route('social.friends') }}" class="btn btn-primary me-2">친구 찾기</a>
                    <a href="{{ route('castles.index') }}" class="btn btn-outline-primary">성 둘러보기</a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// 좋아요 기능
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const recordId = this.dataset.recordId;
            const isLiked = this.dataset.liked === 'true';

            // 버튼 비활성화
            this.disabled = true;

            fetch(`/social/visit-record/${recordId}/like`, {
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
                    // 아이콘 업데이트
                    const icon = this.querySelector('.like-icon');
                    const count = this.querySelector('.likes-count');

                    icon.textContent = data.liked ? '❤️' : '🤍';
                    count.textContent = new Intl.NumberFormat().format(data.likes_count);

                    this.dataset.liked = data.liked ? 'true' : 'false';
                } else {
                    alert(data.message || '오류가 발생했습니다.');
                }
            })
            .catch(error => {
                console.error('좋아요 처리 오류:', error);
                alert('네트워크 오류가 발생했습니다.');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});
</script>

<style>
.social-post {
    transition: box-shadow 0.2s ease;
}

.social-post:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.like-btn {
    border: none !important;
    color: #666;
    transition: color 0.2s ease;
}

.like-btn:hover {
    color: #e91e63 !important;
}

.like-btn:disabled {
    opacity: 0.6;
}
</style>
@endsection