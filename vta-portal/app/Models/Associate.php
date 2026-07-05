<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Communication;

class Associate extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'region', 'speciality',
        'qualifications', 'session_rate', 'travel_rate_per_mile', 'is_active', 'notes', 'cv_path', 'hourly_rate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_associates')
            ->withPivot(['role', 'start_date', 'end_date', 'is_primary']);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'associate_id');
    }

    public function invoices()
    {
        return $this->hasMany(AssociateInvoice::class, 'associate_id');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class, 'associate_id')->orderBy('communication_date', 'desc');
    }

    public function complianceDocuments()
    {
        return $this->hasMany(AssociateComplianceDocument::class)->orderBy('document_type');
    }
}
