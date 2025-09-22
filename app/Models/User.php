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
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', Friendship::STATUS_ACCEPTED)
            ->withTimestamps();
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

    // 소셜 기능 헬퍼 메서드
    public function isFriendWith(User $user): bool
    {
        return $this->friends()->where('friend_id', $user->id)->exists() ||
               $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
                   ->wherePivot('status', Friendship::STATUS_ACCEPTED)
                   ->where('user_id', $user->id)
                   ->exists();
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
