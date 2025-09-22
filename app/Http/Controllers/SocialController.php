<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VisitRecord;
use App\Models\Friendship;

class SocialController extends Controller
{
    // 소셜 피드 (친구들의 공개 방문 기록)
    public function feed(Request $request)
    {
        $user = auth()->user();

        // 친구들의 공개 방문 기록 + 자신의 기록
        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $friendIds[] = $user->id; // 자신 포함

        $visitRecords = VisitRecord::with(['user', 'castle', 'likes'])
            ->whereIn('user_id', $friendIds)
            ->where('verification_status', VisitRecord::VERIFICATION_APPROVED)
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('social.feed', compact('visitRecords'));
    }

    // 친구 목록
    public function friends(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'friends');

        $data = [];

        switch ($tab) {
            case 'friends':
                $data['friends'] = $user->friends()->withCount('visitRecords')->paginate(20);
                break;
            case 'sent':
                $data['sentRequests'] = $user->sentFriendRequests()
                    ->with('friend')
                    ->where('status', Friendship::STATUS_PENDING)
                    ->paginate(20);
                break;
            case 'received':
                $data['receivedRequests'] = $user->receivedFriendRequests()
                    ->with('user')
                    ->where('status', Friendship::STATUS_PENDING)
                    ->paginate(20);
                break;
            case 'search':
                $search = $request->get('search');
                if ($search) {
                    $data['searchResults'] = User::where('name', 'like', "%{$search}%")
                        ->where('id', '!=', $user->id)
                        ->withCount('visitRecords')
                        ->paginate(20);
                }
                break;
        }

        $data['tab'] = $tab;
        $data['search'] = $request->get('search');

        return view('social.friends', $data);
    }

    // 사용자 프로필
    public function profile(User $user)
    {
        $currentUser = auth()->user();

        // 기본 정보
        $friendshipStatus = $currentUser->getFriendshipStatus($user);
        $visitRecords = VisitRecord::with(['castle'])
            ->visibleTo($currentUser)
            ->where('user_id', $user->id)
            ->where('verification_status', VisitRecord::VERIFICATION_APPROVED)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // 통계
        $stats = [
            'total_visits' => $user->visitRecords()->where('verification_status', VisitRecord::VERIFICATION_APPROVED)->count(),
            'total_badges' => $user->badges()->count(),
            'total_friends' => $user->friends()->count(),
            'completion_rate' => min(($user->visitRecords()->where('verification_status', VisitRecord::VERIFICATION_APPROVED)->count() / 24) * 100, 100)
        ];

        return view('social.profile', compact('user', 'friendshipStatus', 'visitRecords', 'stats'));
    }

    // 친구 요청 보내기
    public function sendFriendRequest(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $friendship = $currentUser->sendFriendRequest($user);

        if ($friendship) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name}님에게 친구 요청을 보냈습니다.",
                'status' => 'request_sent'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '친구 요청을 보낼 수 없습니다.'
        ]);
    }

    // 친구 요청 수락
    public function acceptFriendRequest(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $success = $currentUser->acceptFriendRequest($user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name}님과 친구가 되었습니다!",
                'status' => 'friends'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '친구 요청을 수락할 수 없습니다.'
        ]);
    }

    // 친구 요청 거부
    public function rejectFriendRequest(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $success = $currentUser->rejectFriendRequest($user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "친구 요청을 거부했습니다.",
                'status' => 'none'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '친구 요청을 거부할 수 없습니다.'
        ]);
    }

    // 친구 끊기
    public function unfriend(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $success = $currentUser->unfriend($user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name}님과의 친구 관계를 해제했습니다.",
                'status' => 'none'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '친구 관계를 해제할 수 없습니다.'
        ]);
    }

    // 방문 기록 좋아요 토글
    public function toggleLike(Request $request, VisitRecord $visitRecord)
    {
        $user = auth()->user();

        if (!$visitRecord->isVisibleTo($user)) {
            return response()->json([
                'success' => false,
                'message' => '접근 권한이 없습니다.'
            ], 403);
        }

        $liked = $visitRecord->toggleLike($user);

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $visitRecord->likes_count,
            'message' => $liked ? '좋아요!' : '좋아요 취소'
        ]);
    }
}
