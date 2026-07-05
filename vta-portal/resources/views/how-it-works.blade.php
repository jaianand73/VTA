<x-app-layout>
    <x-slot name="header">Patient Lifecycle</x-slot>

    {{-- ══ INTRO ══ --}}
    <div class="mb-8 rounded-xl border border-[#0092b4]/20 bg-gradient-to-r from-[#0092b4]/5 to-white p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#0092b4] text-white">
                <i class="fa-solid fa-diagram-project text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">The VTA Patient Lifecycle</h2>
                <p class="mt-1 text-sm text-gray-600 leading-relaxed">Every patient follows the same path from first contact through to case closure. This page shows every stage, every branching decision, who is responsible, and where to go in the portal. Use this as your go-to reference.</p>
            </div>
        </div>
    </div>

    {{-- ══ STAGE TRACKER (mini map) ══ --}}
    <div class="mb-8 overflow-x-auto rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex items-center gap-1.5 min-w-max">
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#0092b4;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">1</span>
                Enquiry
            </div>
            <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#059669;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">2</span>
                Referral
            </div>
            <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#7c3aed;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">3</span>
                Patient
            </div>
            <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#15803d;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">4</span>
                Treatment
            </div>
            <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#b45309;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">5</span>
                Accounts
            </div>
            <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            <div class="flex items-center gap-2 rounded-full px-4 py-2 text-white text-xs font-semibold whitespace-nowrap" style="background:#6b7280;">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-white/25 font-bold" style="font-size:10px;">6</span>
                Closure
            </div>
        </div>
    </div>

    {{-- ══ STAGE 1 — ENQUIRY ══ --}}
    <div class="mb-6" id="stage-1">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#0092b4;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">1</span>
                <span class="text-base font-bold tracking-tight">Enquiry & Referral</span>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Starting Point</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #0092b4;">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-phone-arrow-down-left text-[#0092b4]"></i>
                        <span class="text-sm font-semibold text-gray-800">What triggers this?</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">A case manager, solicitor, or insurer contacts VTA about a potential patient. An <strong>Enquiry ID</strong> (e.g. E004) is assigned manually at this stage — it carries through to the patient record on conversion.</p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">📧 Email</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">📞 Phone</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">📄 Referral Letter</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">🌐 Website</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">💬 Word of Mouth</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background:#dff1f7;color:#005f7a;">🔗 LinkedIn</span>
                    </div>
                    <div class="mt-3 rounded-lg bg-blue-50 border border-blue-100 p-3">
                        <p class="text-xs font-semibold text-blue-800 mb-1"><i class="fa-solid fa-arrow-right mr-1"></i> Go to: <a href="{{ route('enquiries.index') }}" class="underline hover:text-blue-600">Enquiries</a></p>
                        <p class="text-xs text-blue-700">Click <strong>"Log New Enquiry"</strong> — fill in Enquiry ID, referrer details, company, and case manager.</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-list-check text-[#0092b4]"></i>
                        <span class="text-sm font-semibold text-gray-800">Steps in this stage</span>
                    </div>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">1</span>Log the enquiry — assign Enquiry ID, enter referrer name, company, condition</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">2</span>Add contacts (solicitor, insurer, case manager) as needed</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">3</span>Send initial response → log date and remarks → status <strong>In Progress</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">4</span>Review the case → decide: <strong>Qualify</strong> or <strong>Not Proceeding</strong></li>
                    </ol>
                    <div class="mt-3 flex items-center gap-1.5 flex-wrap text-xs font-semibold">
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dbeafe;color:#1e40af;">New</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#fef3c7;color:#78350f;">In Progress</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#ccfbf1;color:#065f46;">Qualified</span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-code-branch text-[#0092b4]"></i>
                        <span class="text-sm font-semibold text-gray-800">Decision point</span>
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3">
                            <p class="text-xs font-bold text-green-800 mb-1">✅ Qualified — Promote to Referral</p>
                            <p class="text-xs text-green-700">Enquiry is marked <strong>Qualified</strong>. Click <strong>"Promote to Referral"</strong> on the enquiry page — a Referral record is created with the same VTA-xxx reference. The referral then moves through its own stages (Assessment → Proposal → Approval) before a patient record is created.</p>
                        </div>
                        <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                            <p class="text-xs font-bold text-red-800 mb-1">❌ Not Proceeding</p>
                            <p class="text-xs text-red-700">Status set to <strong>Not Proceeding</strong>. Enquiry stays on record for reference. No patient is created.</p>
                            <span class="mt-1.5 inline-block rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">🔒 Case Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Arrow --}}
    <div class="flex justify-center my-1 mb-5">
        <div class="flex flex-col items-center gap-0.5">
            <div class="w-0.5 h-4 bg-gray-200"></div>
            <i class="fa-solid fa-arrow-down text-gray-300 text-lg"></i>
        </div>
    </div>

    {{-- ══ STAGE 2 — REFERRAL ══ --}}
    <div class="mb-6" id="stage-2">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#059669;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">2</span>
                <span class="text-base font-bold tracking-tight">Referral & Assessment</span>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background:#d1fae5;color:#065f46;">Enquiry Promotes to Referral</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #059669;">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-file-medical text-emerald-600"></i>
                        <span class="text-sm font-semibold text-gray-800">What happens here?</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">The qualified enquiry is promoted to a <strong>Referral</strong>. An associate is assigned, conducts an assessment, and VTA produces a proposal for the insurer. The referral passes through up to 5 statuses before it is either approved (and converted to a patient) or closed.</p>
                    <div class="mt-3 rounded-lg border p-3" style="background:#f0fdf4;border-color:#bbf7d0;">
                        <p class="text-xs font-semibold mb-1" style="color:#065f46;"><i class="fa-solid fa-arrow-right mr-1"></i> Go to: <a href="{{ route('referrals.index') }}" class="underline hover:opacity-80">Referrals</a></p>
                        <p class="text-xs" style="color:#166534;">Open the enquiry → click <strong>"Promote to Referral"</strong>. The referral is created with the same VTA-xxx reference number.</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-list-check text-emerald-600"></i>
                        <span class="text-sm font-semibold text-gray-800">Steps in this stage</span>
                    </div>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46;">1</span>Referral created → status <strong>In Progress</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46;">2</span>Case manager confirms go-ahead → assign associate → status <strong>Assessment</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46;">3</span>Associate conducts sessions, logs bills and documents from their portal</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46;">4</span>VTA sends proposal to insurer → status <strong>Proposal Submitted</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46;">5</span>Insurer approves → status <strong>Approved</strong> → ready to convert</li>
                    </ol>
                    <div class="mt-3 flex items-center gap-1 flex-wrap text-xs font-semibold">
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dbeafe;color:#1e40af;">In Progress</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#ede9fe;color:#5b21b6;">Assessment</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#fef3c7;color:#78350f;">Proposal</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#d1fae5;color:#065f46;">Approved</span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-code-branch text-emerald-600"></i>
                        <span class="text-sm font-semibold text-gray-800">Decision point</span>
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-lg p-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <p class="text-xs font-bold mb-1" style="color:#065f46;">✅ Approved — Convert to Patient</p>
                            <p class="text-xs" style="color:#166534;">Insurer approves the proposal. Click <strong>"Convert to Patient"</strong> on the referral page. A patient record is created with the same VTA-xxx ref and the referral status changes to Converted.</p>
                        </div>
                        <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                            <p class="text-xs font-bold text-red-800 mb-1">❌ Not Proceeding</p>
                            <p class="text-xs text-red-700">Insurer declined, patient withdrew, or case unsuitable. Set status to <strong>Not Proceeding</strong>. The referral remains on record. No patient is created.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Arrow --}}
    <div class="flex justify-center my-1 mb-5">
        <div class="flex flex-col items-center gap-0.5">
            <div class="w-0.5 h-4 bg-gray-200"></div>
            <i class="fa-solid fa-arrow-down text-gray-300 text-lg"></i>
        </div>
    </div>

    {{-- ══ STAGE 3 — PATIENT RECORD ══ --}}
    <div class="mb-6" id="stage-3p">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#7c3aed;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">3</span>
                <span class="text-base font-bold tracking-tight">Patient Record & Assessment</span>
            </div>
            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">Referral Converts to Patient</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #7c3aed;">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-user-plus text-violet-500"></i>
                        <span class="text-sm font-semibold text-gray-800">What happens here?</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">The qualified enquiry is converted into a <strong>Patient record</strong>. The Enquiry ID auto-copies as the Patient ID. Full patient details, next of kin, and referrers are captured. An assessment is then scheduled and conducted.</p>
                    <div class="mt-3 rounded-lg bg-violet-50 border border-violet-100 p-3">
                        <p class="text-xs font-semibold text-violet-800 mb-1"><i class="fa-solid fa-arrow-right mr-1"></i> Go to: <a href="{{ route('patients.index') }}" class="underline hover:text-violet-600">Patients</a></p>
                        <p class="text-xs text-violet-700">Open the approved referral → click <strong>"Convert to Patient"</strong>. The VTA-xxx reference, demographics, and associate carry through automatically.</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-list-check text-violet-500"></i>
                        <span class="text-sm font-semibold text-gray-800">Steps in this stage</span>
                    </div>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">1</span>Create patient — fill in personal details, NOK, referrers, condition</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">2</span>Book assessment appointment → <a href="{{ route('appointments.calendar') }}" class="text-violet-600 underline">Appointments Calendar</a></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">3</span>Status → <strong>Assessment Scheduled</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">4</span>Conduct assessment — upload report document</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">5</span>Send report to referrer → status <strong>Report Sent</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">6</span>Create <strong>Cost Estimation</strong> and send to funder → status <strong>Cost Estimation Sent</strong></li>
                    </ol>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-code-branch text-violet-500"></i>
                        <span class="text-sm font-semibold text-gray-800">Funding decision</span>
                    </div>
                    <div class="space-y-2">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3">
                            <p class="text-xs font-bold text-green-800 mb-1">✅ Funding Approved</p>
                            <p class="text-xs text-green-700">Approval letter received and uploaded. Funding Cycle created with approved amount. Proceed to Treatment.</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-3">
                            <p class="text-xs font-bold text-amber-800 mb-1">⏳ Awaiting LOI</p>
                            <p class="text-xs text-amber-700">Waiting for funder to respond. Status stays <strong>Awaiting LOI</strong> until letter arrives.</p>
                        </div>
                        <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                            <p class="text-xs font-bold text-red-800 mb-1">❌ Funding Rejected</p>
                            <p class="text-xs text-red-700">Funder declines. Patient may be discharged or funding re-applied for with revised estimate.</p>
                        </div>
                    </div>
                    <div class="mt-3 rounded-lg bg-red-50 border border-red-100 p-2.5">
                        <p class="text-xs text-red-700"><i class="fa-solid fa-ban mr-1"></i><strong>Block:</strong> Status cannot move to "Funding Approved" without the approval document uploaded.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center my-1 mb-5">
        <div class="flex flex-col items-center gap-0.5">
            <div class="w-0.5 h-4 bg-gray-200"></div>
            <i class="fa-solid fa-arrow-down text-gray-300 text-lg"></i>
        </div>
    </div>

    {{-- ══ STAGE 4 — TREATMENT (Associates & Appointments) ══ --}}
    <div class="mb-6" id="stage-4t">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#15803d;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">4</span>
                <span class="text-base font-bold tracking-tight">Treatment — Associates & Appointments</span>
            </div>
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Longest Stage — Multiple Roles</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #15803d;">
            {{-- Swimlane header --}}
            <div class="grid grid-cols-1 md:grid-cols-3 border-b border-gray-100">
                <div class="p-4 border-b md:border-b-0 md:border-r border-gray-100" style="background:#0092b4/5;">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full" style="background:#0092b4;"></span>
                        <span class="text-sm font-bold text-gray-800">Samy / Sheeba (Admin)</span>
                    </div>
                </div>
                <div class="bg-violet-50 p-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-violet-400"></span>
                        <span class="text-sm font-bold text-gray-800">Associate (Therapist)</span>
                    </div>
                </div>
                <div class="bg-pink-50 p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-pink-400"></span>
                        <span class="text-sm font-bold text-gray-800">Case Manager</span>
                    </div>
                </div>
            </div>
            {{-- Swimlane content --}}
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5 space-y-2.5">
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full shrink-0" style="background:#0092b4;"></span>Allocate Associate to the patient, approve sessions</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full shrink-0" style="background:#0092b4;"></span>Book appointments in the <a href="{{ route('appointments.calendar') }}" class="underline" style="color:#0092b4;">Calendar</a></div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full shrink-0" style="background:#0092b4;"></span>Review and sign off case notes uploaded by associates</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full shrink-0" style="background:#0092b4;"></span>Log MDT meetings and associate communications</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-amber-400 shrink-0"></span>If treatment pauses → status <strong>On Hold</strong></div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-amber-400 shrink-0"></span>If funding runs low → request new Funding Cycle</div>
                    <div class="mt-2 rounded-lg p-3 border" style="background:#dff1f7;border-color:#0092b4/30;">
                        <p class="text-xs font-semibold mb-1" style="color:#005f7a;"><i class="fa-solid fa-circle-info mr-1"></i>Appointment outcomes</p>
                        <div class="flex flex-wrap gap-1 text-xs">
                            <span class="rounded-full px-2 py-0.5 font-semibold" style="background:#dcfce7;color:#14532d;">Completed</span>
                            <span class="rounded-full px-2 py-0.5 font-semibold" style="background:#fef3c7;color:#78350f;">DNA</span>
                            <span class="rounded-full px-2 py-0.5 font-semibold" style="background:#fee2e2;color:#7f1d1d;">Cancelled</span>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-2.5">
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-violet-400 shrink-0"></span>Logs in to the <strong>Associate Portal</strong> (same URL — portal detects role)</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-violet-400 shrink-0"></span>Views their assigned patients and upcoming appointments</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-violet-400 shrink-0"></span>Uploads <strong>Case Notes</strong> after each session</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-violet-400 shrink-0"></span>Can flag a note for Clinical Head Review (Samy)</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 shrink-0"></span>Cannot see finance, other patients, or settings</div>
                    <div class="mt-2 rounded-lg bg-violet-50 border border-violet-100 p-3">
                        <p class="text-xs text-violet-700">Associate invoices (Draft → Submitted → Paid) are raised through Finance after sessions are delivered.</p>
                    </div>
                </div>
                <div class="p-5 space-y-2.5">
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-pink-400 shrink-0"></span>Logs in to the <strong>Case Manager Portal</strong></div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-pink-400 shrink-0"></span>Sees <strong>only their own company's patients</strong></div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-pink-400 shrink-0"></span>Views signed-off case notes, progress, documents</div>
                    <div class="flex items-start gap-2 text-sm text-gray-600"><span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 shrink-0"></span>Cannot see finance or other companies</div>
                    <div class="mt-2 rounded-lg bg-pink-50 border border-pink-100 p-3">
                        <p class="text-xs text-pink-700">Create case manager portal logins via <a href="{{ route('companies.index') }}" class="underline font-semibold">Companies</a> → select company → Case Managers list → <strong>"Create Portal Login"</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center my-1 mb-5">
        <div class="flex flex-col items-center gap-0.5">
            <div class="w-0.5 h-4 bg-gray-200"></div>
            <i class="fa-solid fa-arrow-down text-gray-300 text-lg"></i>
        </div>
    </div>

    {{-- ══ STAGE 4 — ACCOUNTS ══ --}}
    <div class="mb-6" id="stage-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#b45309;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">4</span>
                <span class="text-base font-bold tracking-tight">Accounts & Invoicing</span>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background:#fef3c7;color:#78350f;">Runs Throughout Treatment</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #b45309;">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-file-invoice-dollar text-amber-600"></i>
                        <span class="text-sm font-semibold text-gray-800">VTA Invoice to Funder</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-3">VTA raises invoices against the Funding Cycle. Auto-numbered <strong>VTA-YYYY-NNNN</strong>. Document must be attached before marking Sent.</p>
                    <div class="flex items-center gap-1.5 text-xs font-semibold flex-wrap">
                        <span class="rounded-full px-2.5 py-0.5" style="background:#fef3c7;color:#78350f;">Draft</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dbeafe;color:#1e40af;">Sent</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dcfce7;color:#14532d;">Paid</span>
                    </div>
                    <div class="mt-3 rounded-lg bg-amber-50 border border-amber-100 p-3">
                        <p class="text-xs text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Only <strong>Paid</strong> invoices are deducted from the funding balance. A warning appears if a new invoice would exceed the approved amount.</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-file-invoice text-amber-600"></i>
                        <span class="text-sm font-semibold text-gray-800">Associate Invoice from Therapist</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-3">Associates submit invoices to VTA. Due date auto-sets to invoice date + 28 days.</p>
                    <div class="flex items-center gap-1.5 text-xs font-semibold flex-wrap">
                        <span class="rounded-full px-2.5 py-0.5" style="background:#fef3c7;color:#78350f;">Draft</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#ede9fe;color:#4c1d95;">Submitted</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dcfce7;color:#14532d;">Paid</span>
                    </div>
                    <div class="mt-3 rounded-lg bg-violet-50 border border-violet-100 p-3">
                        <p class="text-xs text-violet-700"><i class="fa-solid fa-arrow-right mr-1"></i> Go to: <a href="{{ route('finance.index') }}" class="underline font-semibold">Finance</a> → Associate Invoices</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-gauge text-amber-600"></i>
                        <span class="text-sm font-semibold text-gray-800">Funding Balance</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">The portal tracks remaining funding automatically per Funding Cycle:</p>
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                            <span class="text-xs text-gray-600">Approved amount</span>
                            <span class="text-xs font-semibold text-gray-800">£5,000</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                            <span class="text-xs text-gray-600">Invoiced (Paid)</span>
                            <span class="text-xs font-semibold text-gray-800">£1,600</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-green-50 px-3 py-2 border border-green-100">
                            <span class="text-xs text-gray-600">Remaining balance</span>
                            <span class="text-xs font-semibold text-green-700">£3,400</span>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-amber-700"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Amber warning shown when less than 20% remains. Request a new Funding Cycle from the funder.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center my-1 mb-5">
        <div class="flex flex-col items-center gap-0.5">
            <div class="w-0.5 h-4 bg-gray-200"></div>
            <i class="fa-solid fa-arrow-down text-gray-300 text-lg"></i>
        </div>
    </div>

    {{-- ══ STAGE 5 — DISCHARGE & CLOSURE ══ --}}
    <div class="mb-8" id="stage-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-2.5 rounded-2xl px-4 py-2 text-white shadow-sm" style="background:#6b7280;">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/25 text-sm font-black">5</span>
                <span class="text-base font-bold tracking-tight">Discharge & Case Closure</span>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">End of Journey</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm" style="border-left: 4px solid #6b7280;">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-circle-check text-gray-500"></i>
                        <span class="text-sm font-semibold text-gray-800">What happens here?</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">Treatment is complete. The associate submits a final case note. Samy discharges the patient and settles all outstanding invoices before closing the case.</p>
                    <div class="mt-3 flex items-center gap-1.5 text-xs font-semibold flex-wrap">
                        <span class="rounded-full px-2.5 py-0.5" style="background:#dcfce7;color:#14532d;">Treatment Active</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#e0f2fe;color:#0369a1;">Discharged</span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded-full px-2.5 py-0.5" style="background:#f1f5f9;color:#475569;">Case Closed</span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-list-check text-gray-500"></i>
                        <span class="text-sm font-semibold text-gray-800">Closure checklist</span>
                    </div>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-xs font-bold">1</span>Associate submits final case note</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-xs font-bold">2</span>Samy signs off the final note</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-xs font-bold">3</span>Raise any remaining VTA invoices and mark all <strong>Paid</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-xs font-bold">4</span>Change patient status → <strong>Discharged</strong></li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-xs font-bold">5</span>Change patient status → <strong>Case Closed</strong></li>
                    </ol>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-shield-check text-gray-500"></i>
                        <span class="text-sm font-semibold text-gray-800">Business rules enforced</span>
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-lg bg-red-50 border border-red-100 p-3">
                            <p class="text-xs font-semibold text-red-700 mb-1"><i class="fa-solid fa-ban mr-1"></i>Block: Unpaid invoices</p>
                            <p class="text-xs text-red-600">The system will not allow <strong>Case Closed</strong> if any VTA invoice is unpaid. All invoices must be marked Paid first.</p>
                        </div>
                        <div class="rounded-lg bg-green-50 border border-green-100 p-3">
                            <p class="text-xs font-semibold text-green-700 mb-1"><i class="fa-solid fa-circle-check mr-1"></i>What "Case Closed" means</p>
                            <p class="text-xs text-green-600">The patient record stays in the system permanently — full history, timeline, and documents remain accessible for reference.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ LINK TO UNDERSTANDING EACH PAGE ══ --}}
    <div class="mb-8 rounded-xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white" style="background:#6366f1;">
                    <i class="fa-solid fa-table-columns text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Understanding Each Page</h2>
                    <p class="mt-1 text-sm text-gray-600 leading-relaxed">A block-by-block guide to every section in the portal — what it means and when to use it.</p>
                </div>
            </div>
            <a href="{{ route('understanding-each-page') }}" class="inline-flex shrink-0 items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90" style="background:#6366f1;">
                Explore Page Guide <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    {{-- ══ QUICK REFERENCE — MODULES ══ --}}
    <div class="mb-8">
        <h3 class="text-base font-bold text-gray-900 mb-4"><i class="fa-solid fa-grid-2 text-gray-400 mr-2"></i>Portal Modules at a Glance</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
            $modules = [
                ['icon'=>'fa-envelope-open-text','color'=>'text-blue-500 bg-blue-50','label'=>'Enquiries','desc'=>'Log new referrals, track responses, qualify into patients.','link'=>route('enquiries.index')],
                ['icon'=>'fa-users','color'=>'text-indigo-500 bg-indigo-50','label'=>'Patients','desc'=>'The central hub — status, timeline, documents, associates, funding.','link'=>route('patients.index')],
                ['icon'=>'fa-calendar-days','color'=>'text-teal-500 bg-teal-50','label'=>'Appointments','desc'=>'Book and manage all appointments. View the full calendar.','link'=>route('appointments.calendar')],
                ['icon'=>'fa-file-lines','color'=>'text-emerald-500 bg-emerald-50','label'=>'Case Notes','desc'=>'Review notes submitted by associates. Sign off or flag for follow-up.','link'=>route('case-notes.index')],
                ['icon'=>'fa-building','color'=>'text-pink-500 bg-pink-50','label'=>'Companies','desc'=>'Manage referrer companies and their case managers. Create portal logins.','link'=>route('companies.index')],
                ['icon'=>'fa-chart-line','color'=>'text-amber-500 bg-amber-50','label'=>'Finance','desc'=>'VTA Invoices, Associate Invoices, and financial reports.','link'=>route('finance.index')],
                ['icon'=>'fa-inbox','color'=>'text-cyan-500 bg-cyan-50','label'=>'Email Intake','desc'=>'Emails sent to VTA appear here — link them to the right patient.','link'=>route('email-intake.index')],
                ['icon'=>'fa-file-chart-column','color'=>'text-violet-500 bg-violet-50','label'=>'Reports','desc'=>'Funding balance, financial summary, patients by status, associate activity.','link'=>route('reports.index')],
                ['icon'=>'fa-gear','color'=>'text-gray-500 bg-gray-100','label'=>'Settings','desc'=>'Manage staff users, associates, document types, and activity types.','link'=>route('settings.index')],
            ];
            @endphp
            @foreach($modules as $m)
            <a href="{{ $m['link'] }}" class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 hover:border-[#0092b4]/40 hover:shadow-sm transition-all">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $m['color'] }}">
                    <i class="fa-solid {{ $m['icon'] }} text-sm"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $m['label'] }}</div>
                    <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $m['desc'] }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ══ QUICK REFERENCE — ROLES ══ --}}
    <div class="mb-8">
        <h3 class="text-base font-bold text-gray-900 mb-4"><i class="fa-solid fa-id-badge text-gray-400 mr-2"></i>Who Has Access to What</h3>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
            <table class="w-full text-sm min-w-max">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-5 py-3 text-left">Area</th>
                        <th class="px-4 py-3 text-center">Samy<br><span class="font-normal normal-case text-gray-400">Admin</span></th>
                        <th class="px-4 py-3 text-center">Sheeba<br><span class="font-normal normal-case text-gray-400">Staff</span></th>
                        <th class="px-4 py-3 text-center">Associate<br><span class="font-normal normal-case text-gray-400">Therapist</span></th>
                        <th class="px-4 py-3 text-center">Case Manager<br><span class="font-normal normal-case text-gray-400">External</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                    $access = [
                        ['area'=>'Enquiries','a'=>'full','s'=>'full','as'=>'none','cm'=>'none'],
                        ['area'=>'Qualify Enquiry → Patient','a'=>'full','s'=>'none','as'=>'none','cm'=>'none'],
                        ['area'=>'Patients (all)','a'=>'full','s'=>'full','as'=>'none','cm'=>'none'],
                        ['area'=>'Own Patients (via portal)','a'=>'—','s'=>'—','as'=>'full','cm'=>'full'],
                        ['area'=>'Appointments','a'=>'full','s'=>'full','as'=>'view','cm'=>'view'],
                        ['area'=>'Case Notes','a'=>'sign-off','s'=>'view','as'=>'upload','cm'=>'add'],
                        ['area'=>'Documents','a'=>'full','s'=>'full','as'=>'own','cm'=>'allowed types'],
                        ['area'=>'Finance / Invoices','a'=>'full','s'=>'none','as'=>'none','cm'=>'none'],
                        ['area'=>'Reports','a'=>'full','s'=>'none','as'=>'none','cm'=>'none'],
                        ['area'=>'Settings','a'=>'full','s'=>'none','as'=>'none','cm'=>'none'],
                    ];
                    $chip = fn($v) => match($v) {
                        'full'     => '<span class="inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Full</span>',
                        'view'     => '<span class="inline-block rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">View</span>',
                        'upload'   => '<span class="inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">Upload</span>',
                        'add'      => '<span class="inline-block rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-700">Add</span>',
                        'none'     => '<span class="inline-block rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-600">None</span>',
                        'sign-off' => '<span class="inline-block rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-semibold text-teal-700">Sign-off</span>',
                        'own'      => '<span class="inline-block rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Own</span>',
                        default    => '<span class="text-gray-400 text-xs">'.$v.'</span>',
                    };
                    @endphp
                    @foreach($access as $row)
                    <tr class="even:bg-gray-50/50">
                        <td class="px-5 py-2.5 font-medium text-gray-700 text-sm">{{ $row['area'] }}</td>
                        <td class="px-4 py-2.5 text-center">{!! $chip($row['a']) !!}</td>
                        <td class="px-4 py-2.5 text-center">{!! $chip($row['s']) !!}</td>
                        <td class="px-4 py-2.5 text-center">{!! $chip($row['as']) !!}</td>
                        <td class="px-4 py-2.5 text-center">{!! $chip($row['cm']) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ CTA ══ --}}
    <div class="rounded-xl border border-[#0092b4]/20 bg-gradient-to-r from-[#0092b4]/5 to-white p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-base font-bold text-gray-900">Ready to test the portal?</h4>
                <p class="mt-1 text-sm text-gray-600">Work through the UAT Testing guide — it walks you through every step above with exact instructions and expected outcomes.</p>
            </div>
            <a href="{{ route('uat-guide.show') }}" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[#0092b4] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#007a9a] transition-colors">
                <i class="fa-solid fa-clipboard-list-check"></i>
                Open UAT Testing Guide
            </a>
        </div>
    </div>

</x-app-layout>
