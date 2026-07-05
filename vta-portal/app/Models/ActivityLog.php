<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'subject_type', 'subject_id',
        'patient_id', 'associate_id',
        'action', 'description', 'metadata', 'occurred_at',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Action → icon + colour
    public static function actionMeta(string $action): array
    {
        return match (true) {
            str_contains($action, 'created')      => ['icon' => 'fa-plus-circle',        'color' => '#16a34a'],
            str_contains($action, 'updated')      => ['icon' => 'fa-pen',                'color' => '#2563eb'],
            str_contains($action, 'status')       => ['icon' => 'fa-arrow-right-arrow-left', 'color' => '#7c3aed'],
            str_contains($action, 'signed_off')   => ['icon' => 'fa-circle-check',       'color' => '#16a34a'],
            str_contains($action, 'uploaded')     => ['icon' => 'fa-upload',             'color' => '#0891b2'],
            str_contains($action, 'submitted')    => ['icon' => 'fa-paper-plane',        'color' => '#ea580c'],
            str_contains($action, 'paid')         => ['icon' => 'fa-sterling-sign',      'color' => '#16a34a'],
            str_contains($action, 'deleted')      => ['icon' => 'fa-trash',              'color' => '#dc2626'],
            str_contains($action, 'qualified')    => ['icon' => 'fa-user-check',         'color' => '#0092b4'],
            str_contains($action, 'converted')    => ['icon' => 'fa-rotate',             'color' => '#7c3aed'],
            str_contains($action, 'assigned')     => ['icon' => 'fa-user-plus',          'color' => '#0092b4'],
            str_contains($action, 'feedback')     => ['icon' => 'fa-comment-dots',       'color' => '#d97706'],
            str_contains($action, 'invoice')      => ['icon' => 'fa-file-invoice',       'color' => '#0892b2'],
            default                               => ['icon' => 'fa-circle-dot',         'color' => '#6b7280'],
        };
    }
}
