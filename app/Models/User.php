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

        foreach ($availableBadges as $badge) {
            if (!$this->hasBadge($badge->id)) {
                $this->badges()->attach($badge->id, ['earned_at' => now()]);
            }
        }
    }
}
