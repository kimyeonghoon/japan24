@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📊 상세 통계</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">관리자 대시보드</a>
            </div>
        </div>

        <!-- 전체 통계 요약 -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem;">👥</div>
                        <h3 class="mb-0">{{ number_format($totalStats['users']) }}</h3>
                        <p class="mb-0">총 사용자</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem;">📝</div>
                        <h3 class="mb-0">{{ number_format($totalStats['visit_records']) }}</h3>
                        <p class="mb-0">총 방문 기록</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem;">🏰</div>
                        <h3 class="mb-0">{{ number_format($totalStats['castles']) }}</h3>
                        <p class="mb-0">등록된 성</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem;">🏅</div>
                        <h3 class="mb-0">{{ number_format($totalStats['badges']) }}</h3>
                        <p class="mb-0">배지 종류</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 방문 기록 상태별 통계 -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem;">⏳</div>
                        <h4 class="mb-0">{{ number_format($statusStats['pending']) }}</h4>
                        <p class="mb-0">검토 대기</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem;">✅</div>
                        <h4 class="mb-0">{{ number_format($statusStats['approved']) }}</h4>
                        <p class="mb-0">승인됨</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem;">❌</div>
                        <h4 class="mb-0">{{ number_format($statusStats['rejected']) }}</h4>
                        <p class="mb-0">거부됨</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 인기 성 TOP 10 -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">🏰 인기 성 TOP 10</h5>
                    </div>
                    <div class="card-body">
                        @if($popularCastles->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($popularCastles as $index => $castle)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center">
                                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }} me-3">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <strong>{{ $castle->name_korean }}</strong><br>
                                                <small class="text-muted">{{ $castle->name }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary fs-6">{{ number_format($castle->visit_count) }}</span><br>
                                            <small class="text-muted">방문</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div style="font-size: 3rem; color: #6c757d;">🏰</div>
                                <p class="text-muted mt-3">방문 기록이 없습니다.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 활성 사용자 TOP 10 -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">🏆 활성 사용자 TOP 10</h5>
                    </div>
                    <div class="card-body">
                        @if($activeUsers->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($activeUsers as $index => $user)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center">
                                            <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-secondary' }} me-3">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <strong>
                                                    {{ $user->isAdmin() ? '👑' : '👤' }} {{ $user->name }}
                                                </strong>
                                                @if($user->isAdmin())
                                                    <span class="badge bg-danger ms-1">관리자</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success fs-6">{{ number_format($user->visit_records_count) }}</span><br>
                                            <small class="text-muted">방문</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div style="font-size: 3rem; color: #6c757d;">👥</div>
                                <p class="text-muted mt-3">사용자가 없습니다.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 완주율 통계 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📈 24성 완주 현황</h5>
                    </div>
                    <div class="card-body">
                        @if($activeUsers->count() > 0)
                            <div class="row">
                                @php
                                    $completionRanges = [
                                        ['min' => 24, 'max' => 24, 'label' => '완주 (24성)', 'color' => 'success'],
                                        ['min' => 20, 'max' => 23, 'label' => '거의 완주 (20-23성)', 'color' => 'warning'],
                                        ['min' => 10, 'max' => 19, 'label' => '절반 이상 (10-19성)', 'color' => 'info'],
                                        ['min' => 5, 'max' => 9, 'label' => '시작 단계 (5-9성)', 'color' => 'primary'],
                                        ['min' => 1, 'max' => 4, 'label' => '입문 (1-4성)', 'color' => 'secondary'],
                                    ];
                                @endphp

                                @foreach($completionRanges as $range)
                                    @php
                                        $count = $activeUsers->filter(function($user) use ($range) {
                                            return $user->visit_records_count >= $range['min'] && $user->visit_records_count <= $range['max'];
                                        })->count();

                                        $percentage = $totalStats['users'] > 0 ? ($count / $totalStats['users']) * 100 : 0;
                                    @endphp

                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-{{ $range['color'] }}">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-{{ $range['color'] }}">{{ $range['label'] }}</h6>
                                                <h4 class="text-{{ $range['color'] }}">{{ number_format($count) }}명</h4>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $range['color'] }}"
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- 전체 평균 -->
                            <div class="mt-4 p-3 bg-light rounded">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h5 class="text-primary">{{ number_format($activeUsers->avg('visit_records_count'), 1) }}</h5>
                                        <small class="text-muted">평균 방문 수</small>
                                    </div>
                                    <div class="col-md-4">
                                        @php
                                            $completionRate = $totalStats['users'] > 0
                                                ? ($activeUsers->where('visit_records_count', 24)->count() / $totalStats['users']) * 100
                                                : 0;
                                        @endphp
                                        <h5 class="text-success">{{ number_format($completionRate, 1) }}%</h5>
                                        <small class="text-muted">완주율</small>
                                    </div>
                                    <div class="col-md-4">
                                        @php
                                            $averageCompletion = $activeUsers->avg('visit_records_count') / 24 * 100;
                                        @endphp
                                        <h5 class="text-info">{{ number_format($averageCompletion, 1) }}%</h5>
                                        <small class="text-muted">평균 진행률</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div style="font-size: 3rem; color: #6c757d;">📊</div>
                                <h5 class="text-muted mt-3">아직 통계 데이터가 없습니다.</h5>
                                <p class="text-muted">사용자들이 성 방문을 시작하면 통계가 표시됩니다.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection