<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $casts = [
        'first_contact_date' => 'date',
    ];

    protected $fillable = [
        'name', 'type', 'address', 'city', 'postcode', 'phone', 'email',
        'website', 'status', 'first_contact_date', 'notes', 'created_by'
    ];

    public function caseManagers()
    {
        return $this->hasMany(CaseManager::class);
    }

    public function enquirySelections()
    {
        return $this->hasMany(Enquiry::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
