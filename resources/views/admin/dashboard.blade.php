@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛠️ 관리자 대시보드</h2>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">사용자 대시보드</a>
                <span class="badge bg-danger ms-2">관리자</span>
            </div>
        </div>

        <!-- 통계 카드들 -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">총 사용자</h6>
                                <h2 class="mb-0">{{ number_format($totalUsers) }}</h2>
                            </div>
                            <div style="font-size: 2rem;">👥</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">총 방문 기록</h6>
                                <h2 class="mb-0">{{ number_format($totalVisitRecords) }}</h2>
                            </div>
                            <div style="font-size: 2rem;">📝</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">검토 대기</h6>
                                <h2 class="mb-0">{{ number_format($pendingRecords) }}</h2>
                            </div>
                            <div style="font-size: 2rem;">⏳</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">승인됨</h6>
                                <h2 class="mb-0">{{ number_format($approvedRecords) }}</h2>
                            </div>
                            <div style="font-size: 2rem;">✅</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 빠른 액션 -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">빠른 액션</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.visit-records') }}" class="btn btn-warning w-100">
                                    ⏳ 검토 대기 기록 ({{ $pendingRecords }})
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.users') }}" class="btn btn-info w-100">
                                    👥 사용자 관리
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.statistics') }}" class="btn btn-secondary w-100">
                                    📊 상세 통계
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('castles.map') }}" class="btn btn-outline-primary w-100">
                                    🗺️ 지도 보기
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 최근 검토 대기 중인 방문 기록 -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🔍 최근 검토 대기 기록</h5>
                        <a href="{{ route('admin.visit-records') }}" class="btn btn-sm btn-outline-primary">전체 보기</a>
                    </div>
                    <div class="card-body">
                        @if($recentPendingRecords->count() > 0)
                            @foreach($recentPendingRecords as $record)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <strong>{{ $record->user->name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $record->castle->name_korean }}
                                            ({{ $record->created_at->format('m/d H:i') }})
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.visit-records') }}?status=pending"
                                           class="btn btn-sm btn-warning">검토</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">검토 대기 중인 기록이 없습니다.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 최근 가입한 사용자 -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">👋 최근 가입 사용자</h5>
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">전체 보기</a>
                    </div>
                    <div class="card-body">
                        @if($recentUsers->count() > 0)
                            @foreach($recentUsers as $user)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->isAdmin())
                                            <span class="badge bg-danger">관리자</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            {{ $user->email }}
                                            ({{ $user->created_at->format('m/d H:i') }})
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users') }}"
                                           class="btn btn-sm btn-info">관리</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">사용자가 없습니다.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 인기 성 통계 -->
        @if($castleStats->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🏰 인기 성 TOP 10</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($castleStats as $index => $castle)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                            <strong>{{ $castle->name_korean }}</strong>
                                            <small class="text-muted">({{ $castle->name }})</small>
                                        </div>
                                        <span class="badge bg-primary">{{ number_format($castle->visit_count) }}회</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection