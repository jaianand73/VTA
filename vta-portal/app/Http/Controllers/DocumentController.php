<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Patient;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, DocumentService $documentService)
    {
        $data = $request->validate([
            'document_type_id' => 'nullable|exists:document_types,id',
            'patient_id' => 'nullable|exists:patients,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,jpg,png,gif',
            'is_password_protected' => 'boolean',
            'report_password' => 'nullable|string|max:255',
            'password_shared_date' => 'nullable|date',
            'password_shared_via' => 'nullable|string|max:50',
        ]);

        $patient = isset($data['patient_id']) ? Patient::find($data['patient_id']) : null;

        $this->authorize('create', [Document::class, $patient]);

        $data['uploaded_by'] = Auth::id();

        $documentService->store($request->file('file'), $data);

        return redirect()->back();
    }

    public function download(Document $document)
    {
        $this->authorize('download', $document);

        if (!Storage::disk('vta-documents')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('vta-documents')->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        if (Storage::disk('vta-documents')->exists($document->file_path)) {
            Storage::disk('vta-documents')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back();
    }
}
