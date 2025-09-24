@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>⚙️ 시스템 설정</h2>
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

        <!-- 회원가입 설정 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">👥 회원가입 설정</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="registration_enabled"
                                       name="registration_enabled" value="1"
                                       {{ $registrationEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="registration_enabled">
                                    <strong>회원가입 허용</strong>
                                </label>
                            </div>
                            <small class="text-muted">
                                이 옵션을 비활성화하면 새로운 사용자가 회원가입을 할 수 없습니다.
                                기존 사용자는 계속 로그인할 수 있습니다.
                            </small>

                            <div class="mt-3">
                                <div class="alert {{ $registrationEnabled ? 'alert-success' : 'alert-warning' }}">
                                    <strong>현재 상태:</strong>
                                    {{ $registrationEnabled ? '🟢 회원가입이 허용되어 있습니다' : '🔴 회원가입이 차단되어 있습니다' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                💾 설정 저장
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 추가 설정 섹션 (미래 확장용) -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔧 기타 설정</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">추가 시스템 설정 기능은 향후 업데이트에서 제공됩니다.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h6>📊 통계</h6>
                        <ul class="list-unstyled">
                            <li><small class="text-muted">• 총 등록 사용자: {{ \App\Models\User::count() }}명</small></li>
                            <li><small class="text-muted">• 총 방문 기록: {{ \App\Models\VisitRecord::count() }}건</small></li>
                            <li><small class="text-muted">• 대기 중인 기록: {{ \App\Models\VisitRecord::where('verification_status', 'pending')->count() }}건</small></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🏰 성 정보</h6>
                        <ul class="list-unstyled">
                            <li><small class="text-muted">• 총 성 수: {{ \App\Models\Castle::count() }}개</small></li>
                            <li><small class="text-muted">• 총 배지 수: {{ \App\Models\Badge::count() }}개</small></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection