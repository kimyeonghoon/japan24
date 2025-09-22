<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use App\Models\VisitRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisitRecordController extends Controller
{
    public function index()
    {
        $visitRecords = Auth::user()->visitRecords()
            ->with('castle')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stamp_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visit_notes' => 'nullable|string|max:1000',
            'device_timestamp' => 'required|integer'
        ]);

        // 서버 시간과 디바이스 시간 차이 검증 (5분 이내)
        $deviceTime = (int) $request->device_timestamp;
        $serverTime = time();
        if (abs($serverTime - $deviceTime) > 300) {
            return back()->withErrors(['device_timestamp' => '디바이스 시간이 서버 시간과 너무 차이납니다.']);
        }

        // GPS 위치 검증 (더 엄격한 검증)
        if (!$castle->isWithinAuthenticationRange($request->gps_latitude, $request->gps_longitude, 100)) {
            return back()->withErrors(['gps' => '성에서 100m 이내에서만 인증할 수 있습니다.']);
        }

        // 사진 업로드 처리
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('castle-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // 사진이 3장 미만인 경우 에러
        if (count($photoPaths) < 3) {
            return back()->withErrors(['photos' => '성 사진을 최소 3장 업로드해야 합니다.']);
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