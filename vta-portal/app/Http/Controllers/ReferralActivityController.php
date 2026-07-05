<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\ReferralSession;
use App\Models\ReferralBill;
use App\Models\ReferralCommunication;
use App\Models\ReferralDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReferralActivityController extends Controller
{
    // ─── SESSIONS ────────────────────────────────────────────────────────────

    public function storeSession(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'session_date'     => 'required|date',
            'activity_type_id' => 'required|exists:activity_types,id',
            'scheduled_at'     => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location'         => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
            'document'         => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('referrals/sessions', 'public');
        }
        unset($data['document']);

        $data['created_by'] = Auth::id();
        $referral->sessions()->create($data);

        return back()->with('success', 'Session logged.');
    }

    public function destroySession(Referral $referral, ReferralSession $session)
    {
        if ($session->document_path) Storage::disk('public')->delete($session->document_path);
        $session->delete();
        return back()->with('success', 'Session removed.');
    }

    // ─── BILLS ───────────────────────────────────────────────────────────────

    public function storeBill(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'bill_date' => 'required|date',
            'amount'    => 'required|numeric|min:0',
            'status'    => 'required|in:Pending,Paid,Unpaid',
            'notes'     => 'nullable|string',
            'document'  => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('referrals/bills', 'public');
        }
        unset($data['document']);

        $data['created_by'] = Auth::id();
        $referral->bills()->create($data);

        return back()->with('success', 'Bill recorded.');
    }

    public function updateBillStatus(Request $request, Referral $referral, ReferralBill $bill)
    {
        $request->validate(['status' => 'required|in:Pending,Paid,Unpaid']);
        $bill->update(['status' => $request->status]);
        return back()->with('success', 'Bill status updated.');
    }

    public function markBillPaid(ReferralBill $bill)
    {
        $bill->update(['status' => 'Paid']);
        return redirect()->route('accounts.index', ['tab' => 'referral-bills'])
            ->with('success', 'Bill marked as paid.');
    }

    public function destroyBill(Referral $referral, ReferralBill $bill)
    {
        if ($bill->document_path) Storage::disk('public')->delete($bill->document_path);
        $bill->delete();
        return back()->with('success', 'Bill removed.');
    }

    // ─── COMMUNICATIONS ──────────────────────────────────────────────────────

    public function storeCommunication(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'communication_date' => 'required|date',
            'type'               => 'required|in:Email,Phone,WhatsApp,Video Call,In-person,Letter,Other',
            'direction'          => 'required|in:Inbound,Outbound',
            'subject'            => 'nullable|string|max:255',
            'notes'              => 'nullable|string',
            'document'           => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('referrals/communications', 'public');
        }
        unset($data['document']);

        $data['created_by'] = Auth::id();
        $referral->communications()->create($data);

        return back()->with('success', 'Communication logged.');
    }

    public function destroyCommunication(Referral $referral, ReferralCommunication $communication)
    {
        if ($communication->document_path) Storage::disk('public')->delete($communication->document_path);
        $communication->delete();
        return back()->with('success', 'Communication removed.');
    }

    // ─── DOCUMENTS ───────────────────────────────────────────────────────────

    public function storeDocument(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'title'                => 'required|string|max:255',
            'file'                 => 'required|file|mimes:pdf,doc,docx,jpg,png|max:20480',
            'visible_to_associate' => 'nullable|boolean',
        ]);

        $data['file_path']             = $request->file('file')->store('referrals/documents', 'public');
        $data['visible_to_associate']  = $request->boolean('visible_to_associate');
        $data['uploaded_by']           = Auth::id();
        unset($data['file']);

        $referral->documents()->create($data);

        return back()->with('success', 'Document uploaded.');
    }

    public function toggleVisibility(Referral $referral, ReferralDocument $document)
    {
        $document->update(['visible_to_associate' => !$document->visible_to_associate]);
        $label = $document->visible_to_associate ? 'visible' : 'hidden';
        return back()->with('success', "Document is now {$label} to the associate.");
    }

    public function requestRevision(Request $request, Referral $referral, ReferralDocument $document)
    {
        $request->validate(['revision_notes' => 'required|string|max:1000']);

        $document->update([
            'revision_requested' => true,
            'revision_notes'     => $request->revision_notes,
            'visible_to_associate' => true,
        ]);

        return back()->with('success', 'Revision requested. The associate will see this when they log in.');
    }

    public function destroyDocument(Referral $referral, ReferralDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document removed.');
    }
}
