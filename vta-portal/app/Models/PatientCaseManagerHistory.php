<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientCaseManagerHistory extends Model
{
    public $timestamps = false;

    protected $casts = [
        'change_date' => 'date',
    ];

    protected $fillable = [
        'patient_id', 'previous_case_manager_id', 'new_case_manager_id',
        'previous_company_id', 'new_company_id', 'change_date', 'reason', 'changed_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function previousCaseManager()
    {
        return $this->belongsTo(CaseManager::class, 'previous_case_manager_id');
    }

    public function newCaseManager()
    {
        return $this->belongsTo(CaseManager::class, 'new_case_manager_id');
    }

    public function previousCompany()
    {
        return $this->belongsTo(Company::class, 'previous_company_id');
    }

    public function newCompany()
    {
        return $this->belongsTo(Company::class, 'new_company_id');
    }
}
