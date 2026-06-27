<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientCaseManagerHistory;
use App\Models\CaseManager;
use Illuminate\Support\Facades\Auth;

class PatientTransferService
{
    public function transfer(Patient $patient, int $newCaseManagerId, ?string $reason = null): void
    {
        $oldCaseManager = $patient->caseManager;
        $newCaseManager = CaseManager::findOrFail($newCaseManagerId);

        PatientCaseManagerHistory::create([
            'patient_id' => $patient->id,
            'previous_case_manager_id' => $oldCaseManager?->id,
            'new_case_manager_id' => $newCaseManager->id,
            'previous_company_id' => $oldCaseManager?->company_id,
            'new_company_id' => $newCaseManager->company_id,
            'change_date' => now(),
            'reason' => $reason,
            'changed_by' => Auth::id(),
        ]);

        $patient->update(['case_manager_id' => $newCaseManagerId]);
    }
}
