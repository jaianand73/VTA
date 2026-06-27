<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAssociate extends Model
{
    public $timestamps = false;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_primary' => 'boolean',
    ];

    protected $fillable = [
        'patient_id', 'associate_id', 'role', 'start_date', 'end_date',
        'is_primary', 'notes', 'assigned_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }
}
