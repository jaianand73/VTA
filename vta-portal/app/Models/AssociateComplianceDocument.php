<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociateComplianceDocument extends Model
{
    protected $fillable = [
        'associate_id', 'document_type', 'document_path', 'expiry_date', 'notes', 'uploaded_by',
    ];

    protected $casts = ['expiry_date' => 'date'];

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isExpiringSoon(int $days = 90): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
