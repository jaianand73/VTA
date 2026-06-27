<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Patient;
use App\Models\CaseManager;
use App\Models\DocumentType;
use App\Models\Enquiry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function store(UploadedFile $file, array $data): Document
    {
        $patient = isset($data['patient_id']) ? Patient::with('caseManager.company')->find($data['patient_id']) : null;
        $caseManager = isset($data['case_manager_id']) ? CaseManager::with('company')->find($data['case_manager_id']) : null;
        $enquiry = isset($data['enquiry_id']) ? Enquiry::with('selectedCaseManager.company', 'selectedCompany')->find($data['enquiry_id']) : null;
        $docType = isset($data['document_type_id']) ? DocumentType::find($data['document_type_id']) : null;

        $companyName = $caseManager?->company?->name
            ?? $patient?->caseManager?->company?->name
            ?? $enquiry?->selectedCaseManager?->company?->name
            ?? $enquiry?->selectedCompany?->name;
        $cmName = $caseManager
            ? $caseManager->first_name . '-' . $caseManager->last_name
            : ($patient?->caseManager
                ? $patient->caseManager->first_name . '-' . $patient->caseManager->last_name
                : ($enquiry?->selectedCaseManager
                    ? $enquiry->selectedCaseManager->first_name . '-' . $enquiry->selectedCaseManager->last_name
                    : null));

        $companySlug = $companyName ? Str::slug($companyName) : 'unknown-company';
        $cmSlug = $cmName ? Str::slug($cmName) : ($enquiry ? 'enquiry-' . $enquiry->id : 'unknown-cm');
        $patientSlug = $patient ? Str::slug($patient->first_name . '-' . $patient->last_name . '-' . $patient->id) : 'unknown-patient';
        $docTypeSlug = $docType ? Str::slug($docType->name) : 'unknown-type';
        $uuid = Str::uuid();
        $ext = $file->extension();

        $relativePath = "{$companySlug}/{$cmSlug}/{$patientSlug}/{$docTypeSlug}/{$uuid}.{$ext}";

        Storage::disk('vta-documents')->put($relativePath, file_get_contents($file));

        return Document::create([
            'document_type_id' => $data['document_type_id'] ?? null,
            'patient_id' => $data['patient_id'] ?? null,
            'case_manager_id' => $data['case_manager_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'enquiry_id' => $data['enquiry_id'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'stored_file_name' => "{$uuid}.{$ext}",
            'file_path' => $relativePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'is_password_protected' => $data['is_password_protected'] ?? false,
            'report_password' => isset($data['report_password']) ? encrypt($data['report_password']) : null,
            'password_shared_date' => $data['password_shared_date'] ?? null,
            'password_shared_via' => $data['password_shared_via'] ?? null,
            'uploaded_by' => $data['uploaded_by'],
        ]);
    }
}
