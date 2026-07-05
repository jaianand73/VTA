<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientReferrer extends Model
{
    protected $fillable = ['patient_id', 'name', 'role', 'company_name', 'address', 'email', 'phone', 'special_instructions'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
