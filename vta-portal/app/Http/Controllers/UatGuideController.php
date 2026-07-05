<?php

namespace App\Http\Controllers;

use App\Models\PortalFeedbackItem;
use App\Models\UatTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UatGuideController extends Controller
{
    public function show()
    {
        $results = UatTestResult::all()->keyBy('step_reference');

        $stats = [
            'total'       => $results->count(),
            'pass'        => $results->where('result', 'pass')->count(),
            'fail'        => $results->where('result', 'fail')->count(),
            'improvement' => $results->where('result', 'pass_with_improvement')->count(),
        ];

        return view('uat-guide.show', compact('results', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'step_reference' => 'required|string|max:10',
            'step_title'     => 'required|string|max:255',
            'result'         => 'required|in:pass,fail,pass_with_improvement',
            'comment'        => 'nullable|string|max:2000',
        ]);

        if ($data['result'] === 'fail' && empty($data['comment'])) {
            return back()->withErrors(['comment' => 'Please describe what went wrong.'])->withInput();
        }

        $feedbackItemId = null;

        // Auto-create a Feedback Board entry for fails and improvements
        if (in_array($data['result'], ['fail', 'pass_with_improvement'])) {
            $type     = $data['result'] === 'fail' ? 'bug' : 'improvement';
            $priority = $data['result'] === 'fail' ? 'high' : 'medium';
            $prefix   = $data['result'] === 'fail' ? '[UAT Fail]' : '[UAT Improvement]';

            $item = PortalFeedbackItem::create([
                'type'        => $type,
                'section'     => 'UAT',
                'priority'    => $priority,
                'title'       => $prefix . ' ' . $data['step_reference'] . ': ' . $data['step_title'],
                'description' => $data['comment'] ?? '',
                'raised_by'   => Auth::user()->name,
                'samy_status' => 'approved',
                'is_seeded'   => false,
            ]);

            $feedbackItemId = $item->id;
        }

        UatTestResult::updateOrCreate(
            ['step_reference' => $data['step_reference']],
            [
                'step_title'      => $data['step_title'],
                'result'          => $data['result'],
                'comment'         => $data['comment'] ?? null,
                'tested_by'       => Auth::user()->name,
                'tested_at'       => now(),
                'feedback_item_id' => $feedbackItemId,
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Step ' . $data['step_reference'] . ' result saved.');
    }
}
