<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $casts = [
        'communication_date' => 'datetime',
        'follow_up_date' => 'date',
    ];

    protected $fillable = [
        'enquiry_id', 'case_manager_id', 'patient_id', 'patient_associate_id', 'associate_id', 'type', 'direction', 'subject',
        'summary', 'communication_date', 'follow_up_date',
        'follow_up_completed', 'created_by'
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function caseManager()
    {
        return $this->belongsTo(CaseManager::class, 'case_manager_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id');
    }

    public function patientAssociate()
    {
        return $this->belongsTo(PatientAssociate::class, 'patient_associate_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
