<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-800">{{ $referral->patient_full_name }}</h2>
                    @if($referral->referral_ref)
                        <span class="rounded-full bg-gray-100 px-3 py-0.5 text-xs font-mono text-gray-500">{{ $referral->referral_ref }}</span>
                    @endif
                    @php
                    $sc = [
                        'New'               => ['bg'=>'#eff6ff','txt'=>'#1d4ed8','border'=>'#bfdbfe'],
                        'In Progress'       => ['bg'=>'#fefce8','txt'=>'#854d0e','border'=>'#fde68a'],
                        'Awaiting Go-ahead' => ['bg'=>'#fff7ed','txt'=>'#c2410c','border'=>'#fed7aa'],
                        'Assessment'        => ['bg'=>'#f0fdf4','txt'=>'#15803d','border'=>'#bbf7d0'],
                        'Proposal Submitted'=> ['bg'=>'#fdf4ff','txt'=>'#7e22ce','border'=>'#e9d5ff'],
                        'Approved'          => ['bg'=>'#f0fdfa','txt'=>'#0f766e','border'=>'#99f6e4'],
                        'Not Proceeding'    => ['bg'=>'#fef2f2','txt'=>'#b91c1c','border'=>'#fecaca'],
                    ][$referral->status] ?? ['bg'=>'#f3f4f6','txt'=>'#374151','border'=>'#d1d5db'];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-semibold"
                        style="background:{{ $sc['bg'] }};color:{{ $sc['txt'] }};border:1px solid {{ $sc['border'] }};">
                        {{ $referral->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Referral — Stage 2</p>
            </div>
            <div class="flex gap-2">
                @if(!$referral->patient && $referral->status === 'Approved')
                    <a href="{{ route('referrals.convertToPatient', $referral) }}"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white"
                        style="background:#0f766e;">
                        <i class="fa-solid fa-user-plus"></i> Convert to Patient
                    </a>
                @elseif($referral->patient)
                    <a href="{{ route('patients.show', $referral->patient) }}"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white"
                        style="background:#0f766e;">
                        <i class="fa-solid fa-user-injured"></i> View Patient Record
                    </a>
                @endif
                <a href="{{ route('referrals.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i> All Referrals
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm text-green-800">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-800">
            <i class="fa-solid fa-circle-xmark mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div x-data="{ editing: false }" class="space-y-5">

        {{-- Source enquiry link --}}
        @if($referral->enquiry)
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm text-blue-800 flex items-center gap-3">
            <i class="fa-solid fa-link"></i>
            Promoted from Enquiry:
            <a href="{{ route('enquiries.show', $referral->enquiry) }}" class="font-semibold underline">
                {{ $referral->enquiry->enquiry_ref ?? '#'.$referral->enquiry_id }} — {{ $referral->enquiry->enquirer_name }}
            </a>
        </div>
        @endif

        {{-- Edit form --}}
        <div x-show="editing">
            <form method="POST" action="{{ route('referrals.update', $referral) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Reference & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referral ID</label>
                            <input type="text" name="referral_ref" value="{{ old('referral_ref', $referral->referral_ref) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                @foreach(['New','In Progress','Awaiting Go-ahead','Assessment','Proposal Submitted','Approved','Not Proceeding'] as $s)
                                    <option value="{{ $s }}" @selected($referral->status === $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Patient Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="patient_first_name" value="{{ old('patient_first_name', $referral->patient_first_name) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="patient_last_name" value="{{ old('patient_last_name', $referral->patient_last_name) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="patient_dob" value="{{ old('patient_dob', $referral->patient_dob?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                            <input type="text" name="patient_postcode" value="{{ old('patient_postcode', $referral->patient_postcode) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="patient_address" rows="2"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('patient_address', $referral->patient_address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="patient_phone" value="{{ old('patient_phone', $referral->patient_phone) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="patient_email" value="{{ old('patient_email', $referral->patient_email) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Case Management & Associate</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <select name="company_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">— None —</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}" @selected($referral->company_id == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Case Manager</label>
                            <select name="case_manager_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">— None —</option>
                                @foreach($caseManagers as $cm)
                                    <option value="{{ $cm->id }}" @selected($referral->case_manager_id == $cm->id)>
                                        {{ $cm->first_name }} {{ $cm->last_name }} — {{ $cm->company?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Associate</label>
                            <select name="associate_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">— None —</option>
                                @foreach($associates as $a)
                                    <option value="{{ $a->id }}" @selected($referral->associate_id == $a->id)>{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Special Instructions</h3>
                    <textarea name="special_instructions" rows="4"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('special_instructions', $referral->special_instructions) }}</textarea>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Notes</h3>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $referral->notes) }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg px-6 py-2 text-sm font-semibold text-white" style="background:#0092b4;">Save Changes</button>
                    <button type="button" @click="editing=false" class="rounded-lg px-6 py-2 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>

        {{-- Read view --}}
        <div x-show="!editing">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Left: Patient & Case Management --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Patient Details --}}
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                            <h3 class="font-semibold text-gray-700 text-sm">Patient Details</h3>
                            <button @click="editing=true" class="text-xs font-medium" style="color:#0092b4;">
                                <i class="fa-solid fa-pen mr-1"></i>Edit
                            </button>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Full Name</dt>
                                <dd class="font-medium text-gray-900">{{ $referral->patient_full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Date of Birth</dt>
                                <dd class="text-gray-700">{{ $referral->patient_dob?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Postcode</dt>
                                <dd class="text-gray-700">{{ $referral->patient_postcode ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Phone</dt>
                                <dd class="text-gray-700">{{ $referral->patient_phone ?? '—' }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Address</dt>
                                <dd class="text-gray-700">{{ $referral->patient_address ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Email</dt>
                                <dd class="text-gray-700">{{ $referral->patient_email ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Case Management --}}
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                            <h3 class="font-semibold text-gray-700 text-sm">Case Management</h3>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Company</dt>
                                <dd class="font-medium text-gray-900">{{ $referral->company?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Case Manager</dt>
                                <dd class="text-gray-700">
                                    @if($referral->caseManager)
                                        {{ $referral->caseManager->first_name }} {{ $referral->caseManager->last_name }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Associate</dt>
                                <dd class="text-gray-700">{{ $referral->associate?->name ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Special Instructions --}}
                    @if($referral->special_instructions)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-amber-200">
                            <h3 class="font-semibold text-amber-800 text-sm"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Special Instructions</h3>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-amber-900 leading-relaxed" style="white-space:pre-wrap;">{{ $referral->special_instructions }}</p>
                        </div>
                    </div>
                    @else
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                            <h3 class="font-semibold text-gray-700 text-sm">Special Instructions</h3>
                        </div>
                        <div class="px-6 py-5 text-sm text-gray-400 italic">None recorded.</div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    @if($referral->notes)
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                            <h3 class="font-semibold text-gray-700 text-sm">Notes</h3>
                        </div>
                        <div class="px-6 py-5 text-sm text-gray-700 leading-relaxed" style="white-space:pre-wrap;">{{ $referral->notes }}</div>
                    </div>
                    @endif

                </div>

                {{-- Right: Progression panel --}}
                <div class="space-y-4">

                    {{-- Go-ahead to Visit --}}
                    <div class="rounded-xl border shadow-sm overflow-hidden
                        @if($referral->hasVisitApproval()) border-green-200 bg-green-50 @else border-orange-200 bg-orange-50 @endif">
                        <div class="px-5 py-4 border-b @if($referral->hasVisitApproval()) border-green-200 @else border-orange-200 @endif">
                            <h3 class="font-semibold text-sm @if($referral->hasVisitApproval()) text-green-800 @else text-orange-800 @endif">
                                <i class="fa-solid @if($referral->hasVisitApproval()) fa-circle-check @else fa-clock @endif mr-2"></i>
                                Go-ahead to Visit
                            </h3>
                        </div>
                        @if($referral->hasVisitApproval())
                            <div class="px-5 py-4 text-sm text-green-800 space-y-1">
                                <p><span class="font-medium">Approved:</span> {{ $referral->visit_approved_date->format('d M Y') }}</p>
                                @if($referral->associate)
                                    <p><span class="font-medium">Assigned associate:</span> {{ $referral->associate->name }}</p>
                                @endif
                                @if($referral->visit_approved_document)
                                    <p><a href="{{ Storage::url($referral->visit_approved_document) }}" target="_blank" class="underline">View document</a></p>
                                @endif
                            </div>
                        @else
                            <div class="px-5 py-4">
                                <p class="text-xs text-orange-700 mb-3">Not yet received. Record it when the case manager confirms.</p>
                                <form method="POST" action="{{ route('referrals.approveVisit', $referral) }}" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <div>
                                        <label class="block text-xs text-orange-700 mb-1 font-medium">Go-ahead date <span class="text-red-500">*</span></label>
                                        <input type="date" name="visit_approved_date" required
                                            class="w-full rounded-lg border border-orange-300 px-3 py-1.5 text-sm focus:border-orange-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-orange-700 mb-1 font-medium">Assign associate for assessment <span class="text-red-500">*</span></label>
                                        <select name="associate_id" required
                                            class="w-full rounded-lg border border-orange-300 px-3 py-1.5 text-sm focus:border-orange-500 focus:outline-none">
                                            <option value="">— Select associate —</option>
                                            @foreach($associates as $a)
                                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-orange-700 mb-1">Confirmation document (optional)</label>
                                        <input type="file" name="visit_approved_document" accept=".pdf,.doc,.docx,.jpg,.png"
                                            class="w-full text-xs text-gray-600">
                                    </div>
                                    <button type="submit" class="w-full rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#ea580c;">
                                        Record Go-ahead &amp; Assign Associate
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- Proposal --}}
                    <div class="rounded-xl border shadow-sm overflow-hidden
                        @if($referral->proposal_submitted_date) border-purple-200 bg-purple-50 @else border-gray-200 bg-white @endif">
                        <div class="px-5 py-4 border-b @if($referral->proposal_submitted_date) border-purple-200 @else border-gray-100 bg-gray-50/60 @endif">
                            <h3 class="font-semibold text-sm @if($referral->proposal_submitted_date) text-purple-800 @else text-gray-700 @endif">
                                <i class="fa-solid fa-file-contract mr-2"></i>Proposal
                            </h3>
                        </div>
                        @if($referral->proposal_submitted_date)
                            <div class="px-5 py-4 text-sm text-purple-800 space-y-2">
                                <p><span class="font-medium">Submitted:</span> {{ $referral->proposal_submitted_date->format('d M Y') }}</p>
                                @if($referral->proposal_document)
                                    <p><a href="{{ Storage::url($referral->proposal_document) }}" target="_blank" class="underline">View proposal</a></p>
                                @endif
                                @if($referral->proposal_approved_date)
                                    <p class="text-green-700 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i>Approved: {{ $referral->proposal_approved_date->format('d M Y') }}</p>
                                @else
                                    <div class="border-t border-purple-200 pt-3 mt-2">
                                        <p class="text-xs text-purple-600 mb-2">Waiting for funder approval. Record it when confirmed.</p>
                                        <form method="POST" action="{{ route('referrals.approveProposal', $referral) }}" class="space-y-2">
                                            @csrf
                                            <input type="date" name="proposal_approved_date" required
                                                class="w-full rounded-lg border border-purple-300 px-3 py-1.5 text-sm focus:border-purple-500 focus:outline-none">
                                            <button type="submit" class="w-full rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#7c3aed;">
                                                <i class="fa-solid fa-circle-check mr-1"></i>Record Proposal Approval
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="px-5 py-4">
                                <p class="text-xs text-gray-500 mb-3">Not yet submitted.</p>
                                @if($referral->hasVisitApproval())
                                <form method="POST" action="{{ route('referrals.submitProposal', $referral) }}" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="date" name="proposal_submitted_date" required
                                        class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-[#0092b4] focus:outline-none">
                                    <input type="file" name="proposal_document" accept=".pdf,.doc,.docx"
                                        class="w-full text-xs text-gray-600">
                                    <button type="submit" class="w-full rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#7c3aed;">
                                        Record Proposal Submission
                                    </button>
                                </form>
                                @else
                                <p class="text-xs text-gray-400 italic">Available after Go-ahead to Visit is recorded.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Delete --}}
                    @if(!$referral->patient)
                    <form method="POST" action="{{ route('referrals.destroy', $referral) }}"
                        onsubmit="return confirm('Delete this referral? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-trash mr-1"></i>Delete Referral
                        </button>
                    </form>
                    @endif

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             ACTIVITY — Sessions, Communications, Documents, Bills
             Visible to Samy; associate logs via their own portal
        ═══════════════════════════════════════════════════════════════════ --}}

        {{-- Sessions --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" x-data="{ addSession: false }">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-calendar-check mr-2 text-green-600"></i>Assessment Sessions</h3>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ $referral->sessions->count() }} logged</span>
                    <button @click="addSession = !addSession" type="button"
                        class="text-xs px-3 py-1 rounded-lg font-medium text-white" style="background:#16a34a;">
                        <i class="fa-solid fa-plus mr-1"></i>Log Session
                    </button>
                </div>
            </div>

            {{-- Add session form --}}
            <div x-show="addSession" x-cloak class="border-b border-gray-100 bg-green-50/40 px-6 py-5">
                <form method="POST" action="{{ route('referrals.sessions.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="session_date" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Activity Type <span class="text-red-500">*</span></label>
                        <select name="activity_type_id" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                            <option value="">Select…</option>
                            @foreach($activityTypes as $at)
                                <option value="{{ $at->id }}">{{ $at->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_at"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Duration (min)</label>
                        <input type="number" name="duration_minutes" min="15" max="480" placeholder="60"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g. Patient's home"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Document</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.png"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#16a34a;">Save Session</button>
                        <button type="button" @click="addSession = false" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600" style="background:#f3f4f6;">Cancel</button>
                    </div>
                </form>
            </div>

            @if($referral->sessions->isEmpty())
                <div class="px-6 py-5 text-sm text-gray-400 italic">No sessions logged yet.</div>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($referral->sessions as $session)
                <div class="flex items-start gap-4 px-6 py-4 text-sm">
                    <div class="mt-0.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-700 whitespace-nowrap">
                        {{ $session->session_date->format('d M Y') }}
                    </div>
                    <div class="flex-1">
                        <span class="font-medium text-gray-700">{{ $session->activityType?->name ?? '—' }}</span>
                        @if($session->scheduled_at)
                            <span class="ml-2 text-xs text-gray-400">@ {{ $session->scheduled_at->format('H:i') }}</span>
                        @endif
                        @if($session->duration_minutes)
                            <span class="ml-1 text-xs text-gray-400">({{ $session->duration_minutes }} min)</span>
                        @endif
                        @if($session->location)
                            <span class="ml-2 text-xs text-gray-500">— {{ $session->location }}</span>
                        @endif
                        <span class="ml-2 text-xs text-gray-400">by {{ $session->createdBy?->name }}</span>
                        @if($session->notes)<p class="text-gray-500 mt-0.5">{{ $session->notes }}</p>@endif
                        @if($session->document_path)
                            <a href="{{ Storage::url($session->document_path) }}" target="_blank"
                                class="mt-1 inline-block text-xs underline" style="color:#0092b4;">View document</a>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('referrals.sessions.destroy', [$referral, $session]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600"
                            onclick="return confirm('Remove this session?')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Communications --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-comments mr-2 text-blue-500"></i>Communications</h3>
                <span class="text-xs text-gray-400">{{ $referral->communications->count() }} logged</span>
            </div>
            @if($referral->communications->isEmpty())
                <div class="px-6 py-5 text-sm text-gray-400 italic">No communications logged yet.</div>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($referral->communications as $comm)
                <div class="flex items-start gap-4 px-6 py-4 text-sm">
                    <div class="mt-0.5 text-xs text-gray-400 whitespace-nowrap">{{ $comm->communication_date->format('d M Y') }}</div>
                    <div class="flex-1">
                        <span class="font-medium text-gray-700">{{ $comm->type }}</span>
                        <span class="ml-2 text-xs px-1.5 py-0.5 rounded" style="background:#f0f9ff;color:#0369a1;">{{ $comm->direction }}</span>
                        @if($comm->subject)<span class="ml-2 text-gray-600">— {{ $comm->subject }}</span>@endif
                        <span class="ml-2 text-xs text-gray-400">by {{ $comm->createdBy?->name }}</span>
                        @if($comm->notes)<p class="text-gray-500 mt-0.5">{{ $comm->notes }}</p>@endif
                        @if($comm->document_path)
                            <a href="{{ Storage::url($comm->document_path) }}" target="_blank"
                                class="mt-1 inline-block text-xs underline" style="color:#0092b4;">Attachment</a>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('referrals.communications.destroy', [$referral, $comm]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600"
                            onclick="return confirm('Remove this communication?')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Documents (Samy's view — upload + toggle visibility) --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-folder-open mr-2 text-purple-500"></i>Documents</h3>
                <p class="text-xs text-gray-400 mt-0.5">Toggle "Visible to associate" to share a document with the assigned associate.</p>
            </div>
            <div class="p-6 space-y-4">
                <form method="POST" action="{{ route('referrals.documents.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border border-gray-200 rounded-xl p-4 bg-gray-50/60">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.png"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div class="flex items-center gap-2 pt-4">
                        <input type="checkbox" name="visible_to_associate" value="1" id="vis_new" class="rounded">
                        <label for="vis_new" class="text-xs text-gray-600">Visible to associate</label>
                    </div>
                    <div>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#7c3aed;">
                            Upload
                        </button>
                    </div>
                </form>

                @if($referral->documents->isEmpty())
                    <p class="text-sm text-gray-400 italic">No documents uploaded yet.</p>
                @else
                <ul class="divide-y divide-gray-100">
                    @foreach($referral->documents as $doc)
                    <li class="py-3 text-sm" x-data="{ revising: false }">
                        {{-- Main row --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-medium text-gray-800">{{ $doc->title }}</span>
                                <span class="ml-2 text-xs text-gray-400">{{ $doc->uploadedBy?->name }}</span>
                                @if($doc->revision_requested)
                                    <span class="ml-2 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                                        style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                                        <i class="fa-solid fa-rotate-right text-xs"></i> Revision requested
                                    </span>
                                    <p class="mt-1 text-xs text-amber-700 italic">{{ $doc->revision_notes }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                {{-- Visibility toggle --}}
                                <form method="POST" action="{{ route('referrals.documents.visibility', [$referral, $doc]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs px-2 py-0.5 rounded-full font-medium"
                                        style="{{ $doc->visible_to_associate ? 'background:#d1fae5;color:#065f46;' : 'background:#f3f4f6;color:#6b7280;' }}">
                                        {{ $doc->visible_to_associate ? '✓ Visible' : 'Hidden' }}
                                    </button>
                                </form>
                                {{-- Request revision (only if doc is visible to associate and not already flagged) --}}
                                @if($doc->visible_to_associate && !$doc->revision_requested)
                                    <button @click="revising = !revising" type="button"
                                        class="text-xs px-2 py-0.5 rounded-full font-medium"
                                        style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                                        <i class="fa-solid fa-rotate-right mr-1"></i>Request Revision
                                    </button>
                                @endif
                                @if($doc->revision_requested)
                                    <button @click="revising = !revising" type="button"
                                        class="text-xs px-2 py-0.5 rounded-full font-medium"
                                        style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
                                        <i class="fa-solid fa-pencil mr-1"></i>Edit Note
                                    </button>
                                @endif
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                    class="text-xs underline" style="color:#0092b4;">Download</a>
                                <form method="POST" action="{{ route('referrals.documents.destroy', [$referral, $doc]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600"
                                        onclick="return confirm('Delete this document?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        {{-- Revision request form (inline, collapsible) --}}
                        <div x-show="revising" x-cloak class="mt-3">
                            <form method="POST" action="{{ route('referrals.documents.request-revision', [$referral, $doc]) }}"
                                class="rounded-lg border border-amber-200 bg-amber-50 p-4 space-y-3">
                                @csrf
                                <p class="text-xs font-semibold text-amber-800">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    What needs to be changed in "{{ $doc->title }}"?
                                </p>
                                <textarea name="revision_notes" rows="2" required
                                    placeholder="e.g. Please correct the date on page 2 and re-upload..."
                                    class="w-full rounded border border-amber-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                                    style="background:#fffbeb;">{{ $doc->revision_notes }}</textarea>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="rounded px-3 py-1.5 text-xs font-semibold text-white"
                                        style="background:#d97706;">
                                        Send Revision Request
                                    </button>
                                    <button type="button" @click="revising = false"
                                        class="rounded px-3 py-1.5 text-xs font-semibold text-gray-600"
                                        style="background:#f3f4f6;">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        {{-- Bills --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-file-invoice-dollar mr-2 text-amber-500"></i>Bills</h3>
                @if($referral->bills->isNotEmpty())
                <span class="text-xs text-gray-400">Total: £{{ number_format($referral->bills->sum('amount'), 2) }}</span>
                @endif
            </div>
            @if($referral->bills->isEmpty())
                <div class="px-6 py-5 text-sm text-gray-400 italic">No bills submitted yet.</div>
            @else
            <table class="min-w-full text-sm divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-2 text-left">Date</th>
                        <th class="px-6 py-2 text-left">Amount</th>
                        <th class="px-6 py-2 text-left">Status</th>
                        <th class="px-6 py-2 text-left">Notes</th>
                        <th class="px-6 py-2 text-left">Invoice</th>
                        <th class="px-6 py-2 text-left">By</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @foreach($referral->bills as $bill)
                    @php
                    $bs = ['Paid'=>['bg'=>'#d1fae5','txt'=>'#065f46'],'Unpaid'=>['bg'=>'#fee2e2','txt'=>'#991b1b'],'Pending'=>['bg'=>'#fef9c3','txt'=>'#854d0e']][$bill->status] ?? [];
                    @endphp
                    <tr>
                        <td class="px-6 py-3 text-gray-600">{{ $bill->bill_date->format('d M Y') }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">£{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('referrals.bills.status', [$referral, $bill]) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                    class="rounded-full px-2 py-0.5 text-xs font-medium border-0 cursor-pointer"
                                    style="background:{{ $bs['bg'] ?? '#f3f4f6' }};color:{{ $bs['txt'] ?? '#374151' }};">
                                    @foreach(['Pending','Unpaid','Paid'] as $s)
                                        <option value="{{ $s }}" @selected($bill->status === $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $bill->notes ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @if($bill->document_path)
                                <a href="{{ Storage::url($bill->document_path) }}" target="_blank"
                                    class="text-xs underline" style="color:#0092b4;">View</a>
                            @else —
                            @endif
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $bill->createdBy?->name }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('referrals.bills.destroy', [$referral, $bill]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600"
                                    onclick="return confirm('Remove this bill?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>
</x-app-layout>
