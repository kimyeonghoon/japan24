<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitRecordLike extends Model
{
    protected $fillable = [
        'user_id',
        'visit_record_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitRecord(): BelongsTo
    {
        return $this->belongsTo(VisitRecord::class);
    }
}
