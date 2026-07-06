<?php

namespace App\Http\Controllers;

use App\Models\PortalFeedbackItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'questions');

        $changes      = PortalFeedbackItem::changes()->orderBy('section')->orderBy('reference')->get();
        $questions    = PortalFeedbackItem::questions()->orderBy('id')->get();
        $improvements = PortalFeedbackItem::improvements()->orderBy('reference')->get();
        $bugs         = PortalFeedbackItem::bugs()->latest()->get();

        $stats = [
            'changes_pending'   => $changes->where('samy_status', 'pending')->count(),
            'changes_approved'  => $changes->where('samy_status', 'approved')->count(),
            'questions_pending' => $questions->whereNull('samy_response')->count(),
            'questions_done'    => $questions->whereNotNull('samy_response')->count(),
            'bugs_open'         => $bugs->whereIn('samy_status', ['pending', 'approved'])->count(),
            'items_in_progress' => PortalFeedbackItem::where('dev_status', 'in_progress')->count(),
            'items_done'        => PortalFeedbackItem::where('dev_status', 'done')->count(),
        ];

        return view('portal-feedback.index', compact(
            'tab', 'changes', 'questions', 'improvements', 'bugs', 'stats'
        ));
    }

    public function respond(Request $request, PortalFeedbackItem $item)
    {
        $data = $request->validate([
            'samy_status'   => 'nullable|in:pending,approved,hold,rejected',
            'samy_response' => 'nullable|string|max:5000',
            'dev_status'    => 'nullable|in:not_started,in_progress,done',
            'dev_notes'      => 'nullable|string|max:5000',
            'client_notes'   => 'nullable|string|max:5000',
            'dev_follow_up'  => 'nullable|string|max:5000',
            'title'          => 'nullable|string|max:500',
            'description'    => 'nullable|string',
            'severity'       => 'nullable|in:critical,high,medium,low',
        ]);

        $updates = [];

        if (isset($data['samy_status']) && $data['samy_status'] !== $item->samy_status) {
            $updates['samy_status'] = $data['samy_status'];
        }

        if (array_key_exists('samy_response', $data)) {
            $updates['samy_response'] = $data['samy_response'];
            $updates['samy_responded_at'] = now();
        }

        if (isset($data['dev_status'])) {
            $updates['dev_status'] = $data['dev_status'];
        }

        if (array_key_exists('dev_notes', $data)) {
            $updates['dev_notes'] = $data['dev_notes'];
        }

        if (array_key_exists('client_notes', $data)) {
            $updates['client_notes'] = $data['client_notes'];
        }

        if (array_key_exists('dev_follow_up', $data)) {
            $updates['dev_follow_up'] = $data['dev_follow_up'];
        }

        if (!empty($data['title'])) {
            $updates['title'] = $data['title'];
        }

        if (!empty($data['description'])) {
            $updates['description'] = $data['description'];
        }

        if (!empty($data['severity'])) {
            $updates['severity'] = $data['severity'];
            $updates['priority'] = $data['severity'];
        }

        if (!empty($updates)) {
            $item->update($updates);
        }

        return back()->with('success', 'Response saved.');
    }

    public function storeBug(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:500',
            'description'   => 'required|string',
            'severity'      => 'required|in:critical,high,medium,low',
            'screenshots'   => 'nullable|array|max:5',
            'screenshots.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        $paths = [];
        foreach ($request->file('screenshots', []) as $file) {
            $paths[] = $file->store('feedback-screenshots', 'public');
        }

        PortalFeedbackItem::create([
            'type'        => 'bug',
            'priority'    => $data['severity'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'severity'    => $data['severity'],
            'raised_by'   => Auth::user()->name,
            'samy_status' => 'approved',
            'is_seeded'   => false,
            'screenshots' => $paths ?: null,
        ]);

        return back()->with('success', 'Correction logged successfully.')->with('tab', 'bugs');
    }

    public function storeImprovement(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:500',
            'description' => 'required|string',
        ]);

        PortalFeedbackItem::create([
            'type'        => 'improvement',
            'priority'    => 'medium',
            'title'       => $data['title'],
            'description' => $data['description'],
            'raised_by'   => Auth::user()->name,
            'samy_status' => 'approved',
            'is_seeded'   => false,
        ]);

        return back()->with('success', 'Improvement suggestion saved.')->with('tab', 'improvements');
    }
}
