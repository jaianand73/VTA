<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    protected $casts = [
        'enquiry_date' => 'date',
        'first_response_date' => 'date',
        'converted_date' => 'date',
        'qualified_date' => 'date',
        'qualified_as_referral' => 'boolean',
    ];

    protected $fillable = [
        'enquiry_ref', 'enquirer_name', 'company_name', 'company_id', 'case_manager_id', 'email', 'phone', 'source', 'reason',
        'client_location', 'nearest_associate_id',
        'enquiry_date', 'first_response_date', 'first_response_remarks', 'status',
        'converted_to_company_id', 'converted_to_case_manager_id', 'converted_date',
        'notes', 'created_by', 'qualified_as_referral', 'qualified_date', 'qualified_remarks',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'converted_to_company_id');
    }

    public function selectedCompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function selectedCaseManager()
    {
        return $this->belongsTo(CaseManager::class, 'case_manager_id');
    }

    public function caseManager()
    {
        return $this->belongsTo(CaseManager::class, 'converted_to_case_manager_id');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function referral()
    {
        return $this->hasOne(\App\Models\Referral::class);
    }

    public function nearestAssociate()
    {
        return $this->belongsTo(Associate::class, 'nearest_associate_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(EnquiryContact::class);
    }
}
