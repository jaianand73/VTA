<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Patient $patient): bool
    {
        if (in_array($user->role, ['admin', 'staff'])) return true;
        if ($user->role === 'associate') {
            return $patient->patientAssociates()
                ->where('associate_id', $user->associate?->id)
                ->whereNull('end_date')
                ->exists();
        }
        if ($user->role === 'case_manager') {
            return $patient->case_manager_id === $user->caseManager?->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function changeStatus(User $user, Patient $patient): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function transfer(User $user, Patient $patient): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
