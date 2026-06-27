<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseManager extends Model
{
    protected $casts = [
        'nda_signed_date' => 'date',
        'materials_sent_date' => 'date',
    ];

    protected $fillable = [
        'user_id', 'company_id', 'first_name', 'last_name', 'email', 'phone',
        'job_title', 'nda_signed', 'nda_signed_date', 'materials_sent',
        'materials_sent_date', 'status', 'notes', 'created_by'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class, 'case_manager_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'case_manager_id');
    }
}
