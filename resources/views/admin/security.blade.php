@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🔐 보안 모니터링</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">대시보드로</a>
                <span class="badge bg-danger ms-2">관리자</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- IP 수동 차단 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🚫 IP 수동 차단</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security.block-ip') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ip" class="form-label">IP 주소</label>
                                        <input type="text" class="form-control" id="ip" name="ip" required
                                               placeholder="192.168.1.1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">차단 시간 (분)</label>
                                        <select class="form-select" id="duration" name="duration" required>
                                            <option value="30">30분</option>
                                            <option value="60">1시간</option>
                                            <option value="180">3시간</option>
                                            <option value="360">6시간</option>
                                            <option value="1440">24시간</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">차단 사유</label>
                                <input type="text" class="form-control" id="reason" name="reason" required
                                       placeholder="악의적인 활동, 스팸 등">
                            </div>
                            <button type="submit" class="btn btn-danger">IP 차단하기</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 보안 통계 -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📊 보안 통계</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><strong>현재 차단된 IP:</strong> {{ $blockedIPs->count() }}개</li>
                            <li><strong>최근 로그인 실패:</strong> {{ count($recentFailedAttempts) }}건</li>
                            <li><strong>보안 모니터링:</strong> <span class="badge bg-success">활성화됨</span></li>
                            <li><strong>Rate Limiting:</strong> <span class="badge bg-success">활성화됨</span></li>
                        </ul>

                        <h6 class="mt-3">보안 설정</h6>
                        <ul class="list-unstyled small text-muted">
                            <li>• IP별 로그인 시도: 1분에 5회 제한</li>
                            <li>• 이메일별 로그인 시도: 1분에 3회 제한</li>
                            <li>• 과도한 요청: 1시간에 500회 시 차단</li>
                            <li>• 로그인 페이지 접근: 10분에 50회 시 차단</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 차단된 IP 목록 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🚫 차단된 IP 목록</h5>
            </div>
            <div class="card-body">
                @if($blockedIPs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>IP 주소</th>
                                    <th>차단 사유</th>
                                    <th>차단 시간</th>
                                    <th>만료 시간</th>
                                    <th>작업</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blockedIPs as $blocked)
                                <tr>
                                    <td><code>{{ $blocked['ip'] }}</code></td>
                                    <td>{{ $blocked['reason'] }}</td>
                                    <td>
                                        @if($blocked['blocked_at'])
                                            {{ \Carbon\Carbon::parse($blocked['blocked_at'])->format('m/d H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($blocked['expires_at'])
                                            {{ \Carbon\Carbon::parse($blocked['expires_at'])->format('m/d H:i') }}
                                            @if(\Carbon\Carbon::parse($blocked['expires_at'])->isPast())
                                                <span class="badge bg-success">만료됨</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.security.unblock-ip') }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="ip" value="{{ $blocked['ip'] }}">
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('이 IP의 차단을 해제하시겠습니까?')">
                                                차단 해제
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">현재 차단된 IP가 없습니다.</p>
                @endif
            </div>
        </div>

        <!-- 최근 로그인 실패 기록 -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚠️ 최근 로그인 실패 기록</h5>
            </div>
            <div class="card-body">
                @if(count($recentFailedAttempts) > 0)
                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($recentFailedAttempts as $attempt)
                            <div class="mb-2 p-2 bg-light rounded">
                                <code class="small">{{ trim($attempt) }}</code>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">최근 로그인 실패 기록이 없습니다.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection