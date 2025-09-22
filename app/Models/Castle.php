<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Castle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_korean',
        'prefecture',
        'latitude',
        'longitude',
        'description',
        'historical_info',
        'image_url',
        'official_stamp_location',
        'visiting_hours',
        'entrance_fee'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'entrance_fee' => 'integer'
    ];

    public function visitRecords()
    {
        return $this->hasMany(VisitRecord::class);
    }

    public function getDistanceFromUser($userLatitude, $userLongitude)
    {
        $earthRadius = 6371000; // 지구 반지름 (미터)

        $latFrom = deg2rad($userLatitude);
        $lonFrom = deg2rad($userLongitude);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // 미터 단위 거리
    }

    public function isWithinAuthenticationRange($userLatitude, $userLongitude, $rangeMeters = 200)
    {
        return $this->getDistanceFromUser($userLatitude, $userLongitude) <= $rangeMeters;
    }
}