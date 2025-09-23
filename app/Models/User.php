<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // 친구 관계 캐시
    private $friendshipCache = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function visitRecords()
    {
        return $this->hasMany(VisitRecord::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // 소셜 기능 관련 관계
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    public function friends()
    {
        // 단순한 방법으로 친구 관계 조회
        $friendIds = collect();

        // 내가 보낸 친구 요청이 수락된 경우
        $sentFriends = Friendship::where('user_id', $this->id)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('friend_id');

        // 내가 받은 친구 요청이 수락된 경우
        $receivedFriends = Friendship::where('friend_id', $this->id)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('user_id');

        $allFriendIds = $sentFriends->merge($receivedFriends)->unique();

        return User::whereIn('id', $allFriendIds);
    }

    public function friendsWithRelation()
    {
        return $this->hasMany(Friendship::class, 'user_id')
            ->where('status', Friendship::STATUS_ACCEPTED);
    }

    public function friendsAsReceiverWithRelation()
    {
        return $this->hasMany(Friendship::class, 'friend_id')
            ->where('status', Friendship::STATUS_ACCEPTED);
    }

    public function visitRecordLikes()
    {
        return $this->hasMany(VisitRecordLike::class);
    }

    public function getVerifiedVisitsCount()
    {
        return $this->visitRecords()->where('verification_status', VisitRecord::VERIFICATION_APPROVED)->count();
    }

    public function hasBadge($badgeId)
    {
        return $this->badges()->where('badge_id', $badgeId)->exists();
    }

    public function checkAndAwardBadges()
    {
        $verifiedVisitsCount = $this->getVerifiedVisitsCount();

        $availableBadges = Badge::where('required_visits', '<=', $verifiedVisitsCount)->get();

        $newBadges = [];
        foreach ($availableBadges as $badge) {
            if (!$this->hasBadge($badge->id)) {
                $this->badges()->attach($badge->id, ['earned_at' => now()]);
                $newBadges[] = $badge;
            }
        }

        // 새로 획득한 배지에 대해 알림 생성
        if (!empty($newBadges)) {
            $notificationService = app(\App\Services\NotificationService::class);
            foreach ($newBadges as $badge) {
                $notificationService->createBadgeEarnedNotification($this, $badge);
            }
        }
    }

    // 관리자 관련 메서드
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function makeAdmin(): void
    {
        $this->update(['is_admin' => true]);
    }

    public function removeAdmin(): void
    {
        $this->update(['is_admin' => false]);
    }

    // 친구 관계 캐시 초기화
    private function initializeFriendshipCache(): void
    {
        if ($this->friendshipCache === null) {
            $this->friendshipCache = collect();

            // 모든 친구 관계를 한 번에 가져와서 캐시
            $friendships = Friendship::where('status', Friendship::STATUS_ACCEPTED)
                ->where(function($query) {
                    $query->where('user_id', $this->id)
                          ->orWhere('friend_id', $this->id);
                })
                ->get();

            foreach ($friendships as $friendship) {
                if ($friendship->user_id == $this->id) {
                    $this->friendshipCache->push($friendship->friend_id);
                } else {
                    $this->friendshipCache->push($friendship->user_id);
                }
            }
        }
    }

    // 소셜 기능 헬퍼 메서드
    public function isFriendWith(User $user): bool
    {
        // 관계가 이미 로드되어 있다면 메모리에서 확인
        if ($this->relationLoaded('friendsWithRelation')) {
            $hasFriend = $this->friendsWithRelation->contains(function($friendship) use ($user) {
                return $friendship->friend_id == $user->id;
            });
            if ($hasFriend) {
                return true;
            }
        }

        if ($this->relationLoaded('friendsAsReceiverWithRelation')) {
            $hasFriend = $this->friendsAsReceiverWithRelation->contains(function($friendship) use ($user) {
                return $friendship->user_id == $user->id;
            });
            if ($hasFriend) {
                return true;
            }
        }

        // friends 관계가 로드되어 있다면 그것도 확인
        if ($this->relationLoaded('friends')) {
            return $this->friends->contains('id', $user->id);
        }

        // 캐시를 사용한 확인
        $this->initializeFriendshipCache();
        return $this->friendshipCache->contains($user->id);
    }

    // 친구 추천 (공통 친구 기반)
    public function getFriendSuggestions($limit = 10)
    {
        // 현재 사용자의 친구 ID 수집
        $sentFriends = Friendship::where('user_id', $this->id)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('friend_id')->toArray();

        $receivedFriends = Friendship::where('friend_id', $this->id)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('user_id')->toArray();

        $myFriendIds = array_unique(array_merge($sentFriends, $receivedFriends));

        // 제외할 사용자 ID 수집
        $excludedUserIds = $myFriendIds;
        $excludedUserIds[] = $this->id; // 본인 제외

        // 보낸/받은 친구 요청 사용자들도 제외
        $pendingRequestUserIds = $this->sentFriendRequests()
            ->where('status', Friendship::STATUS_PENDING)
            ->pluck('friend_id')->toArray();

        $receivedRequestUserIds = $this->receivedFriendRequests()
            ->where('status', Friendship::STATUS_PENDING)
            ->pluck('user_id')->toArray();

        $excludedUserIds = array_merge($excludedUserIds, $pendingRequestUserIds, $receivedRequestUserIds);
        $excludedUserIds = array_unique($excludedUserIds);

        $suggestions = collect();

        // 1단계: 공통 친구가 있는 사용자 찾기
        if (count($myFriendIds) > 0) {
            // 내 친구들의 친구를 찾기 (양방향)
            $commonFriendCandidates = collect();

            // 내 친구가 보낸 친구 요청으로부터
            $candidates1 = Friendship::whereIn('user_id', $myFriendIds)
                ->where('status', Friendship::STATUS_ACCEPTED)
                ->whereNotIn('friend_id', $excludedUserIds)
                ->selectRaw('friend_id as candidate_id, COUNT(*) as common_count')
                ->groupBy('friend_id')
                ->get();

            // 내 친구가 받은 친구 요청으로부터
            $candidates2 = Friendship::whereIn('friend_id', $myFriendIds)
                ->where('status', Friendship::STATUS_ACCEPTED)
                ->whereNotIn('user_id', $excludedUserIds)
                ->selectRaw('user_id as candidate_id, COUNT(*) as common_count')
                ->groupBy('user_id')
                ->get();

            // 두 결과 합치기
            $allCandidates = $candidates1->concat($candidates2)
                ->groupBy('candidate_id')
                ->map(function ($group) {
                    return $group->sum('common_count');
                })
                ->sortDesc();

            foreach ($allCandidates->take($limit) as $candidateId => $commonCount) {
                $user = User::find($candidateId);
                if ($user && !in_array($user->id, $excludedUserIds)) {
                    $user->common_friends_count = $commonCount;
                    $suggestions->push($user);
                }
            }
        }

        // 2단계: 부족하면 새로운 사용자로 채우기
        if ($suggestions->count() < $limit) {
            $newUserIds = $suggestions->pluck('id')->toArray();
            $finalExcludedIds = array_merge($excludedUserIds, $newUserIds);

            $newUsers = User::whereNotIn('id', $finalExcludedIds)
                ->orderBy('created_at', 'desc')
                ->limit($limit - $suggestions->count())
                ->get();

            foreach ($newUsers as $user) {
                $user->common_friends_count = 0;
                $suggestions->push($user);
            }
        }

        return $suggestions->take($limit);
    }

    public function hasSentFriendRequestTo(User $user): bool
    {
        return $this->sentFriendRequests()
            ->where('friend_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->exists();
    }

    public function hasReceivedFriendRequestFrom(User $user): bool
    {
        return $this->receivedFriendRequests()
            ->where('user_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->exists();
    }

    public function sendFriendRequest(User $user): ?Friendship
    {
        if ($this->id === $user->id) {
            return null; // 자기 자신에게는 친구 요청 불가
        }

        if ($this->isFriendWith($user) || $this->hasSentFriendRequestTo($user)) {
            return null; // 이미 친구이거나 요청이 있음
        }

        return Friendship::create([
            'user_id' => $this->id,
            'friend_id' => $user->id,
            'status' => Friendship::STATUS_PENDING
        ]);
    }

    public function acceptFriendRequest(User $user): bool
    {
        $friendship = $this->receivedFriendRequests()
            ->where('user_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->first();

        if ($friendship) {
            $friendship->update(['status' => Friendship::STATUS_ACCEPTED]);
            return true;
        }

        return false;
    }

    public function rejectFriendRequest(User $user): bool
    {
        return $this->receivedFriendRequests()
            ->where('user_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->delete() > 0;
    }

    public function unfriend(User $user): bool
    {
        $deleted = Friendship::where(function($query) use ($user) {
            $query->where('user_id', $this->id)->where('friend_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('user_id', $user->id)->where('friend_id', $this->id);
        })->where('status', Friendship::STATUS_ACCEPTED)->delete();

        return $deleted > 0;
    }

    public function getFriendshipStatus(User $user): string
    {
        if ($this->isFriendWith($user)) {
            return 'friends';
        }

        if ($this->hasSentFriendRequestTo($user)) {
            return 'request_sent';
        }

        if ($this->hasReceivedFriendRequestFrom($user)) {
            return 'request_received';
        }

        return 'none';
    }
}
