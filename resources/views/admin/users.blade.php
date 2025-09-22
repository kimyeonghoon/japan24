@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>👥 사용자 관리</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">관리자 대시보드</a>
            </div>
        </div>

        <!-- 검색 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🔍 사용자 검색</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control"
                               value="{{ $search }}" placeholder="사용자명 또는 이메일을 입력하세요">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">검색</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 사용자 목록 -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">전체 사용자 목록 ({{ $users->total() }}명)</h5>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>사용자 정보</th>
                                    <th>가입일</th>
                                    <th>방문 기록</th>
                                    <th>획득 배지</th>
                                    <th>권한</th>
                                    <th>액션</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="font-size: 2rem;">
                                                    {{ $user->isAdmin() ? '👑' : '👤' }}
                                                </div>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->isAdmin())
                                                        <span class="badge bg-danger ms-1">관리자</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $user->created_at->format('Y-m-d') }}<br>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <h5 class="mb-0 text-primary">{{ number_format($user->visit_records_count) }}</h5>
                                                <small class="text-muted">방문</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <h5 class="mb-0 text-warning">{{ number_format($user->badges_count) }}</h5>
                                                <small class="text-muted">배지</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->isAdmin())
                                                <span class="badge bg-danger">관리자</span>
                                            @else
                                                <span class="badge bg-secondary">일반 사용자</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#userModal{{ $user->id }}">
                                                    상세
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $user->isAdmin() ? 'btn-warning' : 'btn-success' }}"
                                                                onclick="return confirm('{{ $user->isAdmin() ? '관리자 권한을 해제하시겠습니까?' : '관리자 권한을 부여하시겠습니까?' }}')">
                                                            {{ $user->isAdmin() ? '권한해제' : '관리자승격' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 페이지네이션 -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 3rem; color: #6c757d;">👥</div>
                        <h5 class="text-muted mt-3">사용자가 없습니다.</h5>
                        @if($search)
                            <p class="text-muted">검색어: "{{ $search }}"에 대한 결과가 없습니다.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 사용자 상세 정보 모달 -->
@foreach($users as $user)
    <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $user->isAdmin() ? '👑' : '👤' }} {{ $user->name }}님의 상세 정보
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">기본 정보</h6>
                                    <p class="mb-1"><strong>이름:</strong> {{ $user->name }}</p>
                                    <p class="mb-1"><strong>이메일:</strong> {{ $user->email }}</p>
                                    <p class="mb-1"><strong>가입일:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
                                    <p class="mb-0">
                                        <strong>권한:</strong>
                                        @if($user->isAdmin())
                                            <span class="badge bg-danger">관리자</span>
                                        @else
                                            <span class="badge bg-secondary">일반 사용자</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">활동 통계</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="text-primary mb-0">{{ number_format($user->visit_records_count) }}</h4>
                                            <small class="text-muted">방문 기록</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-warning mb-0">{{ number_format($user->badges_count) }}</h4>
                                            <small class="text-muted">획득 배지</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $progress = min(($user->visit_records_count / 24) * 100, 100);
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ $progress }}%">
                                            {{ number_format($progress, 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">24성 완주 진행률</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 최근 활동 내역은 실제 데이터가 있는 경우에만 표시 -->
                    @if($user->visit_records_count > 0)
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">📈 최근 활동 내역</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted text-center">
                                    최근 방문 기록: {{ $user->visit_records_count }}건<br>
                                    획득 배지: {{ $user->badges_count }}개
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $user->isAdmin() ? 'btn-warning' : 'btn-success' }}"
                                    onclick="return confirm('{{ $user->isAdmin() ? '관리자 권한을 해제하시겠습니까?' : '관리자 권한을 부여하시겠습니까?' }}')">
                                {{ $user->isAdmin() ? '👑 권한 해제' : '👑 관리자 승격' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection