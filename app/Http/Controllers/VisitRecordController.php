<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use App\Models\VisitRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisitRecordController extends Controller
{
    public function index(Request $request)
    {
        $visitRecords = Auth::user()->visitRecords()
            ->with('castle')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // API 요청 처리
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'data' => $visitRecords->items(),
                'pagination' => [
                    'current_page' => $visitRecords->currentPage(),
                    'last_page' => $visitRecords->lastPage(),
                    'per_page' => $visitRecords->perPage(),
                    'total' => $visitRecords->total(),
                ]
            ]);
        }

        return view('visit-records.index', compact('visitRecords'));
    }

    public function create(Castle $castle)
    {
        // 이미 방문 기록이 있는지 확인
        $existingRecord = Auth::user()->visitRecords()
            ->where('castle_id', $castle->id)
            ->first();

        if ($existingRecord) {
            return redirect()->route('visit-records.show', $existingRecord)
                ->with('error', '이미 이 성에 대한 방문 기록이 있습니다.');
        }

        return view('visit-records.create', compact('castle'));
    }

    public function store(Request $request, Castle $castle)
    {
        $request->validate([
            'visit_date' => 'required|date|before_or_equal:today',
            'gps_latitude' => 'required|numeric|between:-90,90',
            'gps_longitude' => 'required|numeric|between:-180,180',
            'photos' => 'required|array|min:3',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stamp_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visit_notes' => 'nullable|string|max:1000',
            'device_timestamp' => 'required|integer',
            'gps_accuracy' => 'nullable|numeric|min:0',
            'gps_speed' => 'nullable|numeric|min:0',
            'gps_heading' => 'nullable|numeric|between:0,360'
        ]);

        // 서버 시간과 디바이스 시간 차이 검증 (5분 이내)
        $deviceTime = (int) $request->device_timestamp;
        $serverTime = time();
        if (abs($serverTime - $deviceTime) > 300) {
            return back()->withErrors(['device_timestamp' => '디바이스 시간이 서버 시간과 너무 차이납니다.']);
        }

        // GPS 정확도 검증 (Mock GPS 방지)
        $gpsAccuracy = $request->input('gps_accuracy');
        if ($gpsAccuracy !== null && $gpsAccuracy > 50) {
            return back()->withErrors(['gps' => 'GPS 정확도가 너무 낮습니다. 더 정확한 위치에서 다시 시도해주세요.']);
        }

        // GPS 위치 검증 (더 엄격한 검증)
        $distance = $castle->getDistanceFromUser($request->gps_latitude, $request->gps_longitude);
        if ($distance > 100) {
            return back()->withErrors(['gps' => "성에서 {$distance}m 떨어져 있습니다. 100m 이내에서만 인증할 수 있습니다."]);
        }

        // 동일한 위치에서의 중복 인증 방지 (10m 이내 기존 기록 확인)
        $recentRecord = Auth::user()->visitRecords()
            ->where('castle_id', $castle->id)
            ->where('created_at', '>=', now()->subHours(1))
            ->first();

        if ($recentRecord) {
            $recentDistance = $castle->getDistanceFromUser($recentRecord->gps_latitude, $recentRecord->gps_longitude);
            $currentDistance = $castle->getDistanceFromUser($request->gps_latitude, $request->gps_longitude);

            if (abs($recentDistance - $currentDistance) < 10) {
                return back()->withErrors(['gps' => '최근 1시간 내 동일한 위치에서 이미 인증 시도가 있었습니다.']);
            }
        }

        // 사진 업로드 처리 (validation에서 이미 3장 이상 확인됨)
        $photoPaths = [];
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('castle-photos', 'public');
            $photoPaths[] = $path;
        }

        $stampPhotoPath = null;
        if ($request->hasFile('stamp_photo')) {
            $stampPhotoPath = $request->file('stamp_photo')->store('stamp-photos', 'public');
        }

        // 방문 기록 생성 (검증 대기 상태로)
        $visitRecord = VisitRecord::create([
            'user_id' => Auth::id(),
            'castle_id' => $castle->id,
            'visit_date' => $request->visit_date,
            'gps_latitude' => $request->gps_latitude,
            'gps_longitude' => $request->gps_longitude,
            'photo_paths' => $photoPaths,
            'stamp_photo_path' => $stampPhotoPath,
            'visit_notes' => $request->visit_notes,
            'verification_status' => 'pending' // 관리자 검증 대기
        ]);

        // 승인된 방문 기록만 배지 확인
        if ($visitRecord->verification_status === 'approved') {
            Auth::user()->checkAndAwardBadges();
        }

        return redirect()->route('visit-records.show', $visitRecord)
            ->with('success', '방문 기록이 등록되었습니다. 관리자 검증 후 배지가 부여됩니다.');
    }

    public function show(VisitRecord $visitRecord)
    {
        // 본인의 기록만 볼 수 있도록 체크
        if ($visitRecord->user_id !== Auth::id()) {
            abort(403);
        }

        return view('visit-records.show', compact('visitRecord'));
    }
}