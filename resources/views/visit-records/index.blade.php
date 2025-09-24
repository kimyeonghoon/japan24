@extends('layouts.simple')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-4">
            <h2>나의 방문 기록</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($visitRecords->count() > 0)
            <div class="row">
                @foreach($visitRecords as $record)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ $record->castle->name_korean }}</h5>
                                        <small class="text-muted">{{ $record->castle->name }}</small>
                                    </div>
                                    <span class="badge
                                        @if($record->verification_status === 'approved') bg-success
                                        @elseif($record->verification_status === 'rejected') bg-danger
                                        @else bg-warning text-dark
                                        @endif">
                                        @if($record->verification_status === 'approved')
                                            승인됨
                                        @elseif($record->verification_status === 'rejected')
                                            거부됨
                                        @else
                                            검토중
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="card-text">
                                    <strong>방문일:</strong> {{ $record->visit_date->format('Y년 m월 d일') }}<br>
                                    <strong>등록일:</strong> {{ $record->created_at->format('Y-m-d H:i') }}<br>
                                    <strong>위치:</strong> {{ $record->castle->prefecture }}
                                </p>

                                @if($record->visit_notes)
                                    <p class="card-text">
                                        <strong>메모:</strong> {{ Str::limit($record->visit_notes, 100) }}
                                    </p>
                                @endif

                                <!-- 사진 미리보기 -->
                                @if($record->photo_paths && count($record->photo_paths) > 0)
                                    <div class="mb-2">
                                        <small class="text-muted">업로드된 사진 ({{ count($record->photo_paths) }}장)</small>
                                        <div class="d-flex flex-wrap mt-1">
                                            @foreach(array_slice($record->photo_paths, 0, 3) as $photoPath)
                                                <img src="{{ Storage::url($photoPath) }}"
                                                     class="img-thumbnail me-1 mb-1"
                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                     alt="방문 사진">
                                            @endforeach
                                            @if(count($record->photo_paths) > 3)
                                                <div class="d-flex align-items-center justify-content-center me-1 mb-1 bg-light border rounded"
                                                     style="width: 50px; height: 50px;">
                                                    <small class="text-muted">+{{ count($record->photo_paths) - 3 }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        거리: {{ number_format($record->castle->getDistanceFromUser($record->gps_latitude, $record->gps_longitude)) }}m
                                    </small>
                                    <a href="{{ route('visit-records.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                        자세히 보기
                                    </a>
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
                <div class="mb-4">
                    <i class="bi bi-journal-x" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h4 class="text-muted">아직 방문 기록이 없습니다</h4>
                <p class="text-muted mb-4">24개의 일본 명성을 방문하고 인증해보세요!</p>
                <a href="{{ route('castles.index') }}" class="btn btn-primary">
                    성 목록 보기
                </a>
            </div>
        @endif
    </div>
</div>
@endsection