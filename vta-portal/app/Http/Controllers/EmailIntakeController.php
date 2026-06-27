<?php

namespace App\Http\Controllers;

use App\Models\EmailIntakeLog;
use App\Models\Patient;
use App\Models\CaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmailIntakeController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailIntakeLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('from_email', 'like', "%{$search}%")
                  ->orWhere('from_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('processed')) {
            $query->where('processed', filter_var($request->processed, FILTER_VALIDATE_BOOLEAN));
        } elseif ($request->filter === 'unprocessed') {
            $query->where('processed', false);
        } elseif ($request->filter === 'processed') {
            $query->where('processed', true);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('received_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('received_at', '<=', $request->date_to);
        }

        $emailLogs = $query->latest('received_at')->paginate(20);

        return view('email-intake.index', compact('emailLogs'));
    }

    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'from_email'       => 'required|email',
            'from_name'        => 'nullable|string|max:255',
            'subject'          => 'required|string|max:255',
            'body'             => 'nullable|string',
            'received_at'      => 'nullable|date',
            'has_attachments'  => 'boolean',
        ]);

        $data['received_at'] = $data['received_at'] ?? Carbon::now();
        $data['has_attachments'] = $data['has_attachments'] ?? false;
        $data['processed'] = false;

        EmailIntakeLog::create($data);

        return redirect()->route('email-intake.index')
            ->with('success', 'Manual email record created.');
    }

    public function link(Request $request, $id)
    {
        $emailIntakeLog = EmailIntakeLog::findOrFail($id);

        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'case_manager_id' => 'nullable|exists:case_managers,id',
            'notes' => 'nullable|string',
        ]);

        $update = ['processed' => true, 'notes' => $data['notes'] ?? null];

        if (!empty($data['patient_id'])) {
            $update['linked_patient_id'] = $data['patient_id'];
            $update['action_taken'] = 'Linked to Patient';
        } elseif (!empty($data['case_manager_id'])) {
            $update['linked_case_manager_id'] = $data['case_manager_id'];
            $update['action_taken'] = 'Linked to Case Manager';
        }

        $emailIntakeLog->update($update);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $emailIntakeLog = EmailIntakeLog::findOrFail($id);
        $emailIntakeLog->delete();

        return redirect()->back();
    }
}
