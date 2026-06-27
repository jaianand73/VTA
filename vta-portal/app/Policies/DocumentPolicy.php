<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\DocumentTypePermission;
use App\Models\Patient;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'staff') return true;

        if ($user->role === 'associate') {
            $associate = $user->associate;
            if (!$associate) return false;

            $hasPatient = $document->patient_id && $document->patient->patientAssociates()
                ->where('associate_id', $associate->id)
                ->whereNull('end_date')
                ->exists();

            if (!$hasPatient) return false;

            return $this->checkTypePermission($document, 'associate');
        }

        if ($user->role === 'case_manager') {
            if (!$this->belongsToCaseManager($document, $user)) return false;
            return $this->checkTypePermission($document, 'case_manager');
        }

        return false;
    }

    public function download(User $user, Document $document): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'staff') return true;

        if ($user->role === 'associate') {
            $associate = $user->associate;
            if (!$associate) return false;

            $hasPatient = $document->patient_id && $document->patient->patientAssociates()
                ->where('associate_id', $associate->id)
                ->whereNull('end_date')
                ->exists();

            if (!$hasPatient) return false;

            return $this->checkTypePermission($document, 'associate');
        }

        if ($user->role === 'case_manager') {
            if (!$this->belongsToCaseManager($document, $user)) return false;
            return $this->checkTypePermission($document, 'case_manager');
        }

        return false;
    }

    /**
     * A document belongs to a case manager either because it was attached
     * directly to them (case_manager_id), or because it belongs to one of
     * their own patients (patient_id -> patient.case_manager_id).
     */
    private function belongsToCaseManager(Document $document, User $user): bool
    {
        $caseManager = $user->caseManager;
        if (!$caseManager) return false;

        if ($document->case_manager_id === $caseManager->id) return true;

        return $document->patient_id && $document->patient?->case_manager_id === $caseManager->id;
    }

    public function create(User $user, ?Patient $patient = null): bool
    {
        if (in_array($user->role, ['admin', 'staff'])) {
            return true;
        }

        if ($user->role === 'associate') {
            // No patient context (e.g. case-manager-only document) — not allowed for associates.
            if (!$patient) {
                return false;
            }

            $associate = $user->associate;
            if (!$associate) {
                return false;
            }

            return $patient->patientAssociates()
                ->where('associate_id', $associate->id)
                ->whereNull('end_date')
                ->exists();
        }

        return false;
    }

    public function delete(User $user): bool
    {
        return $user->role === 'admin';
    }

    private function checkTypePermission(Document $document, string $role): bool
    {
        if (!$document->document_type_id) return true;

        $permission = DocumentTypePermission::where('document_type_id', $document->document_type_id)
            ->where('role', $role)
            ->first();

        return $permission?->can_view ?? false;
    }
}
