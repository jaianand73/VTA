<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $action,
        string $description,
        Model  $subject,
        ?int   $patientId   = null,
        ?int   $associateId = null,
        array  $metadata    = []
    ): void {
        try {
            ActivityLog::create([
                'user_id'      => Auth::id(),
                'subject_type' => class_basename($subject),
                'subject_id'   => $subject->getKey(),
                'patient_id'   => $patientId,
                'associate_id' => $associateId,
                'action'       => $action,
                'description'  => $description,
                'metadata'     => $metadata ?: null,
                'occurred_at'  => now(),
            ]);
        } catch (\Throwable) {
            // Never let logging break the main request
        }
    }
}
