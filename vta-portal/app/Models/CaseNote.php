<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseNote extends Model
{
    protected $casts = [
        'session_date' => 'date',
        'signed_off_at' => 'datetime',
    ];

    protected $fillable = [
        'patient_id', 'appointment_id', 'associate_id', 'session_date',
        'note_type', 'content', 'document_path', 'is_signed_off',
        'signed_off_by', 'signed_off_at'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id');
    }

    public function signedOffBy()
    {
        return $this->belongsTo(User::class, 'signed_off_by');
    }
}
