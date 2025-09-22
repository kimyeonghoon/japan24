<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'castle_id',
        'visit_date',
        'gps_latitude',
        'gps_longitude',
        'photo_paths',
        'stamp_photo_path',
        'visit_notes',
        'verification_status',
        'verified_at',
        'is_public',
        'likes_count'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'photo_paths' => 'array',
        'verified_at' => 'datetime',
        'is_public' => 'boolean'
    ];

    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_APPROVED = 'approved';
    const VERIFICATION_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function castle()
    {
        return $this->belongsTo(Castle::class);
    }

    public function likes()
    {
        return $this->hasMany(VisitRecordLike::class);
    }

    public function isVerified()
    {
        return $this->verification_status === self::VERIFICATION_APPROVED;
    }

    public function isPending()
    {
        return $this->verification_status === self::VERIFICATION_PENDING;
    }

    public function isRejected()
    {
        return $this->verification_status === self::VERIFICATION_REJECTED;
    }

    public function getPhotos()
    {
        return $this->photo_paths ?? [];
    }

    public function hasRequiredPhotos()
    {
        return count($this->getPhotos()) >= 3;
    }

    public function hasStampPhoto()
    {
        return !empty($this->stamp_photo_path);
    }

    public function approve()
    {
        $this->verification_status = self::VERIFICATION_APPROVED;
        $this->verified_at = now();
        $this->save();
    }

    public function reject()
    {
        $this->verification_status = self::VERIFICATION_REJECTED;
        $this->save();
    }

    // 소셜 기능 관련 메서드
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(User $user): bool
    {
        $existingLike = $this->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $this->decrement('likes_count');
            return false; // Unlike
        } else {
            $this->likes()->create(['user_id' => $user->id]);
            $this->increment('likes_count');
            return true; // Like
        }
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isVisibleTo(User $user): bool
    {
        // 자신의 기록은 항상 볼 수 있음
        if ($this->user_id === $user->id) {
            return true;
        }

        // 공개 설정이고 승인된 기록만 다른 사용자에게 보임
        return $this->is_public && $this->isVerified();
    }

    // 스코프
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function($q) use ($user) {
            $q->where('user_id', $user->id) // 자신의 기록
              ->orWhere(function($q) {
                  $q->where('is_public', true) // 공개 기록 중
                    ->where('verification_status', self::VERIFICATION_APPROVED); // 승인된 것만
              });
        });
    }
}