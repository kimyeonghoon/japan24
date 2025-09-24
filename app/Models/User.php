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

    /**
     * 관리자 여부 확인
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * 방문 기록과의 관계
     */
    public function visitRecords()
    {
        return $this->hasMany(VisitRecord::class);
    }

    /**
     * 사용자 배지와의 관계
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * 방문한 성들
     */
    public function visitedCastles()
    {
        return $this->belongsToMany(Castle::class, 'visit_records')
                   ->where('visit_records.verification_status', 'approved');
    }

    /**
     * 획득한 배지들
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges', 'user_id', 'badge_id')
                   ->withTimestamps();
    }

    /**
     * 방문 진행률 계산
     */
    public function getVisitProgressPercentage(): float
    {
        $totalCastles = Castle::count();
        $visitedCastles = $this->visitedCastles()->count();

        return $totalCastles > 0 ? round(($visitedCastles / $totalCastles) * 100, 1) : 0;
    }

    /**
     * 새 배지 획득 시 처리
     */
    public function checkAndAwardBadges(): void
    {
        $visitCount = $this->visitedCastles()->count();

        // 방문 횟수에 따른 배지 조건
        $badgeConditions = [
            1 => 1,   // 초보자
            2 => 5,   // 성 순례 입문
            3 => 10,  // 성 애호가
            4 => 15,  // 성 마스터
            5 => 20,  // 성 박사
            6 => 24,  // 성 컴플리트
        ];

        foreach ($badgeConditions as $badgeId => $requiredVisits) {
            if ($visitCount >= $requiredVisits) {
                // 이미 획득한 배지인지 확인
                if (!$this->userBadges()->where('badge_id', $badgeId)->exists()) {
                    $badge = Badge::find($badgeId);
                    if ($badge) {
                        $this->userBadges()->create([
                            'badge_id' => $badgeId,
                            'earned_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}