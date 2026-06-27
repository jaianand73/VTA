<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $casts = [
        'enquiry_date' => 'date',
        'first_response_date' => 'date',
        'converted_date' => 'date',
    ];

    protected $fillable = [
        'enquirer_name', 'company_name', 'company_id', 'case_manager_id', 'email', 'phone', 'source', 'reason',
        'enquiry_date', 'first_response_date', 'status', 'converted_to_company_id',
        'converted_to_case_manager_id', 'converted_date', 'notes', 'created_by'
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
}
