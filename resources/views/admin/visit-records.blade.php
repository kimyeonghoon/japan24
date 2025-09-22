@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📝 방문 기록 관리</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">관리자 대시보드</a>
            </div>
        </div>

        <!-- 필터 및 검색 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🔍 필터 및 검색</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.visit-records') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">상태</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>검토 대기</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>승인됨</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>거부됨</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">검색 (사용자명, 이메일, 성 이름)</label>
                        <input type="text" name="search" id="search" class="form-control"
                               value="{{ $search }}" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">검색</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 방문 기록 목록 -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if($status === 'pending')
                        ⏳ 검토 대기 중인 방문 기록
                    @elseif($status === 'approved')
                        ✅ 승인된 방문 기록
                    @else
                        ❌ 거부된 방문 기록
                    @endif
                    ({{ $visitRecords->total() }}개)
                </h5>
            </div>
            <div class="card-body">
                @if($visitRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>사용자</th>
                                    <th>성 이름</th>
                                    <th>방문일시</th>
                                    <th>GPS 좌표</th>
                                    <th>사진</th>
                                    <th>상태</th>
                                    <th>액션</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visitRecords as $record)
                                    <tr>
                                        <td>
                                            <strong>{{ $record->user->name }}</strong><br>
                                            <small class="text-muted">{{ $record->user->email }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $record->castle->name_korean }}</strong><br>
                                            <small class="text-muted">{{ $record->castle->name }}</small>
                                        </td>
                                        <td>
                                            {{ $record->created_at->format('Y-m-d H:i') }}<br>
                                            <small class="text-muted">{{ $record->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ number_format($record->latitude, 6) }},<br>
                                                {{ number_format($record->longitude, 6) }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($record->photos)
                                                @php $photos = json_decode($record->photos, true); @endphp
                                                <span class="badge bg-info">{{ count($photos) }}장</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#photosModal{{ $record->id }}">
                                                    보기
                                                </button>
                                            @else
                                                <span class="text-muted">없음</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->verification_status === 'pending')
                                                <span class="badge bg-warning">검토 대기</span>
                                            @elseif($record->verification_status === 'approved')
                                                <span class="badge bg-success">승인됨</span>
                                            @else
                                                <span class="badge bg-danger">거부됨</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->verification_status === 'pending')
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="{{ route('admin.visit-records.approve', $record) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                                onclick="return confirm('이 방문 기록을 승인하시겠습니까?')">
                                                            승인
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.visit-records.reject', $record) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('이 방문 기록을 거부하시겠습니까?')">
                                                            거부
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <small class="text-muted">처리 완료</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 페이지네이션 -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $visitRecords->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 3rem; color: #6c757d;">📝</div>
                        <h5 class="text-muted mt-3">
                            @if($status === 'pending')
                                검토 대기 중인 방문 기록이 없습니다.
                            @elseif($status === 'approved')
                                승인된 방문 기록이 없습니다.
                            @else
                                거부된 방문 기록이 없습니다.
                            @endif
                        </h5>
                        @if($search)
                            <p class="text-muted">검색어: "{{ $search }}"에 대한 결과가 없습니다.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 사진 모달 -->
@foreach($visitRecords as $record)
    @if($record->photos)
        @php $photos = json_decode($record->photos, true); @endphp
        <div class="modal fade" id="photosModal{{ $record->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $record->user->name }}님의 {{ $record->castle->name_korean }} 방문 사진
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @foreach($photos as $index => $photo)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ asset('storage/' . $photo) }}"
                                         class="img-fluid rounded"
                                         alt="방문 사진 {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection