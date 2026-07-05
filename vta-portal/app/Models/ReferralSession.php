<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityType;

class ReferralSession extends Model
{
    protected $fillable = [
        'referral_id', 'session_date', 'activity_type_id', 'scheduled_at',
        'duration_minutes', 'location', 'notes', 'document_path', 'created_by',
    ];

    protected $casts = [
        'session_date' => 'date',
        'scheduled_at' => 'datetime',
    ];

    public function referral()      { return $this->belongsTo(Referral::class); }
    public function activityType()  { return $this->belongsTo(ActivityType::class); }
    public function createdBy()     { return $this->belongsTo(User::class, 'created_by'); }
}
