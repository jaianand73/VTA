<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    protected $fillable = [
        'patient_id', 'associate_id', 'activity_type_id', 'scheduled_at',
        'duration_minutes', 'location', 'status', 'notes', 'travel_miles', 'created_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id');
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function caseNotes()
    {
        return $this->hasMany(CaseNote::class, 'appointment_id');
    }
}
