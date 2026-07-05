<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMdtMeeting extends Model
{
    protected $fillable = [
        'patient_id',
        'meeting_date',
        'attendees',
        'discussion',
        'outcomes',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
