<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Associate extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'region', 'speciality',
        'qualifications', 'session_rate', 'travel_rate_per_mile', 'is_active', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_associates')
            ->withPivot(['role', 'start_date', 'end_date', 'is_primary'])
            ->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'associate_id');
    }
}
