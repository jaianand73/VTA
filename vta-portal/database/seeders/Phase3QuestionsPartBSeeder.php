<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalFeedbackItem;

class Phase3QuestionsPartBSeeder extends Seeder
{
    public function run(): void
    {
        if (PortalFeedbackItem::where('reference', 'P3-Q13')->exists()) {
            return;
        }

        $items = [

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q13',
                'priority'    => 'high',
                'title'       => 'Do patients fall into distinct case types with different workflows?',
                'description' => 'Looking at the patients VTA manages, they likely do not all follow the same path. For example: (1) Assessment-only — Samy does one assessment, writes a report, sends it to the referrer. No ongoing treatment, no funding cycle, case closes after the report. (2) Short-term treatment — a defined number of sessions with a clear end point (e.g. 6 sessions of physiotherapy). (3) Long-term / complex rehabilitation — open-ended treatment with multiple funding cycles, possibly multiple associates over months or years. (4) Medico-legal — the report is for legal proceedings (court case, tribunal, personal injury claim). The solicitor is driving the timeline, there are strict deadlines, and the report format may be different. (5) Occupational / return-to-work — employer or occupational health is involved, and there is a specific return-to-work target date that everything must work around. Do these case types exist at VTA? Are there others? And does each type genuinely follow a different process, or is the current single workflow flexible enough to cover all of them?',
                'dev_context' => 'If distinct case types exist, the biggest impact is on the status chain — assessment-only cases do not need a Funding Cycle step; medico-legal cases need a court/deadline date field; short-term cases might auto-close after approved sessions are reached. This could mean a case_type field on patients that conditionally shows/hides sections of the patient page, or in the more complex scenario, separate workflow configurations per type. Knowing the answer before building further screens prevents significant rework.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q14',
                'priority'    => 'high',
                'title'       => 'How does funding and payment complexity vary across patients?',
                'description' => 'Not all funding situations are the same. From a day-to-day management perspective, which of the following situations do you encounter at VTA, and how often? (A) Clean approval — formal letter received, amount agreed in writing, funder pays on time. Minimal follow-up needed. (B) Vague or informal agreement — funder said yes verbally or by email but no formal approval letter yet. You are proceeding on trust while chasing the paperwork. (C) Disputed funding — funder is questioning the number of sessions, disputing the report, or making partial payments. Requires ongoing negotiation. (D) Multi-funder — more than one party paying different parts (e.g. insurance covers sessions, solicitor covers the assessment fee, patient pays a top-up). (E) Self-pay — the patient is paying directly with no insurer or solicitor involved. Completely different invoice recipient and possibly different rate. (F) Time-constrained payment — there is a legal deadline or court date that determines when everything must be invoiced and settled. For the situations that apply, does the portal need to track where you are in the chasing process (e.g. "Approval letter requested — waiting", "Chased twice — escalated")?',
                'dev_context' => 'Self-pay is the most impactful technically — it changes the invoice recipient model entirely. Multi-funder may require splitting a single funding cycle into multiple payer records. Vague agreement and disputed funding suggest a funding_status field with more granularity than the current binary is_active. A chasing log (similar to the communications log but specifically for funder follow-ups) may be needed. The answer determines whether the current FundingCycle model is sufficient or needs extension.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

            [
                'type'        => 'question',
                'section'     => 'Pre-UAT Planning',
                'reference'   => 'P3-Q15',
                'priority'    => 'high',
                'title'       => 'Should patients have a separate attention/priority level on top of their clinical status?',
                'description' => 'Right now a patient\'s status tells you where they are in the clinical journey (Assessment Scheduled, Treatment Active, etc.) but it does not tell you how much management attention they need right now. In practice some patients are ticking along fine with no issues, while others need daily attention because a funder is disputing, an associate is overdue on a report, or there is a court deadline in two weeks. A separate attention level — for example Routine / Needs Chase / At Risk / Urgent — would let the dashboard surface the right patients immediately without Samy having to scroll through 40 patients to find the three that need action today. Questions: (1) Does this match how you think about your caseload — do you mentally group patients by urgency separate from their clinical stage? (2) Who would set this flag — only Samy, or could staff also raise a patient\'s priority? (3) Should there be a mandatory reason when setting a patient to "At Risk" or "Urgent" (e.g. "Funder disputing sessions 8–10" or "Court date 15 July")? (4) Should Urgent patients trigger a notification or just appear at the top of the dashboard?',
                'dev_context' => 'Implementation is a priority_level enum on patients (routine/needs_chase/at_risk/urgent) with an optional priority_reason text field. The dashboard Daily Actions table would sort by priority_level first, then referral_date. This replaces or extends the current needs_review boolean which is too blunt — it conflates clinical review of case notes with general case management attention. If Samy confirms this concept, needs_review could be retired in favour of priority_level.',
                'samy_status' => 'pending',
                'is_seeded'   => true,
            ],

        ];

        foreach ($items as $item) {
            PortalFeedbackItem::create($item);
        }
    }
}
