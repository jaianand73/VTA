<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNextOfKin extends Model
{
    protected $table = 'patient_next_of_kin';

    protected $fillable = ['patient_id', 'name', 'relationship', 'email', 'phone'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
