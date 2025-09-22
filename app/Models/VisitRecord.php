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
        'verified_at'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'photo_paths' => 'array',
        'verified_at' => 'datetime'
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
}