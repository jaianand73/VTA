@php $header = $referral->patient_full_name . ' — ' . ($referral->referral_ref ?? 'Referral'); @endphp

<x-app-layout>

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

    <div class="space-y-6">

        {{-- Patient & Case Info ─────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Patient Details --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                    <h3 class="font-semibold text-gray-700 text-sm">Patient Details</h3>
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 px-6 py-5 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Full Name</dt>
                        <dd class="font-semibold text-gray-900">{{ $referral->patient_full_name }}</dd>
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
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Go-ahead Date</dt>
                        <dd class="font-medium text-green-700">{{ $referral->visit_approved_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Case Management + Special Instructions --}}
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                        <h3 class="font-semibold text-gray-700 text-sm">Case Management</h3>
                    </div>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 px-6 py-5 text-sm">
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Company</dt>
                            <dd class="font-medium text-gray-900">{{ $referral->company?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Case Manager</dt>
                            <dd class="text-gray-700">
                                @if($referral->caseManager)
                                    {{ $referral->caseManager->first_name }} {{ $referral->caseManager->last_name }}
                                @else —
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                @if($referral->special_instructions)
                <div class="rounded-xl border border-amber-200 bg-amber-50 overflow-hidden">
                    <div class="px-6 py-4 border-b border-amber-200">
                        <h3 class="font-semibold text-amber-800 text-sm">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>Special Instructions
                        </h3>
                    </div>
                    <div class="px-6 py-5 text-sm text-amber-900 leading-relaxed" style="white-space:pre-wrap;">{{ $referral->special_instructions }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Documents shared by Samy (includes revision requests) ──────── --}}
        @if($referral->documents->isNotEmpty())
        @php $revisionDocs = $referral->documents->where('revision_requested', true); @endphp

        {{-- Revision alert banner --}}
        @if($revisionDocs->isNotEmpty())
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-5 py-4">
            <p class="text-sm font-semibold text-amber-800">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                {{ $revisionDocs->count() }} document{{ $revisionDocs->count() > 1 ? 's require' : ' requires' }} your attention
            </p>
            <p class="text-xs text-amber-700 mt-1">VTA has requested changes. Please re-upload the corrected file(s) below.</p>
        </div>
        @endif

        <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-blue-200">
                <h3 class="font-semibold text-blue-800 text-sm"><i class="fa-solid fa-folder-open mr-2"></i>Documents from VTA</h3>
            </div>
            <ul class="divide-y divide-blue-100">
                @foreach($referral->documents as $doc)
                <li class="px-6 py-4 text-sm" x-data="{ reuploading: false }">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="font-medium text-gray-800">{{ $doc->title }}</span>
                            @if($doc->revision_requested)
                                <span class="ml-2 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                                    style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                                    <i class="fa-solid fa-rotate-right text-xs"></i> Revision needed
                                </span>
                                <p class="mt-1 text-xs text-amber-800 font-medium italic">{{ $doc->revision_notes }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($doc->revision_requested)
                                <button type="button" @click="reuploading = !reuploading"
                                    class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white"
                                    style="background:#d97706;">
                                    <i class="fa-solid fa-file-arrow-up mr-1"></i>Re-upload
                                </button>
                            @endif
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                class="text-xs font-medium underline" style="color:#0092b4;">Download</a>
                        </div>
                    </div>
                    {{-- Re-upload form --}}
                    <div x-show="reuploading" x-cloak class="mt-3">
                        <form method="POST"
                            action="{{ route('associate-portal.referrals.documents.reupload', [$referral, $doc]) }}"
                            enctype="multipart/form-data"
                            class="rounded-lg border border-amber-300 bg-amber-50 p-4 space-y-3">
                            @csrf
                            <p class="text-xs font-semibold text-amber-800">Upload corrected version of "{{ $doc->title }}"</p>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.png"
                                class="w-full text-xs text-gray-700">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="rounded px-3 py-1.5 text-xs font-semibold text-white"
                                    style="background:#d97706;">
                                    Submit Corrected File
                                </button>
                                <button type="button" @click="reuploading = false"
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
        </div>
        @endif

        {{-- Log Session ──────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-calendar-check mr-2 text-green-600"></i>Assessment Sessions</h3>
            </div>
            <div class="p-6 space-y-5">

                {{-- Log new session --}}
                <form method="POST" action="{{ route('associate-portal.referrals.sessions.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border border-gray-200 rounded-xl p-4 bg-gray-50/60">
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
                        <label class="block text-xs font-medium text-gray-600 mb-1">Document (optional)</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.png"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="What happened in this session…"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#0092b4;">
                            Log Session
                        </button>
                    </div>
                </form>

                {{-- Session list --}}
                @if($referral->sessions->isEmpty())
                    <p class="text-sm text-gray-400 italic">No sessions logged yet.</p>
                @else
                <div class="space-y-3">
                    @foreach($referral->sessions as $session)
                    <div class="flex items-start gap-4 rounded-xl border border-gray-100 bg-white px-4 py-3 text-sm">
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
                            @if($session->notes)
                                <p class="text-gray-500 mt-0.5">{{ $session->notes }}</p>
                            @endif
                            @if($session->document_path)
                                <a href="{{ Storage::url($session->document_path) }}" target="_blank"
                                    class="mt-1 inline-block text-xs underline" style="color:#0092b4;">View document</a>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">{{ $session->createdBy?->name }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Log Communication ────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-comments mr-2 text-blue-500"></i>Communications</h3>
            </div>
            <div class="p-6 space-y-5">

                <form method="POST" action="{{ route('associate-portal.referrals.communications.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border border-gray-200 rounded-xl p-4 bg-gray-50/60">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="communication_date" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                            @foreach(['Email','Phone','WhatsApp','Video Call','In-person','Letter','Other'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Direction <span class="text-red-500">*</span></label>
                        <select name="direction" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                            <option value="Outbound">Outbound (I contacted them)</option>
                            <option value="Inbound">Inbound (They contacted me)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                        <input type="text" name="subject" placeholder="Brief subject…"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Attachment (optional)</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.png"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#0092b4;">
                            Log Communication
                        </button>
                    </div>
                </form>

                @if($referral->communications->isEmpty())
                    <p class="text-sm text-gray-400 italic">No communications logged yet.</p>
                @else
                <div class="space-y-2">
                    @foreach($referral->communications as $comm)
                    <div class="flex items-start gap-4 rounded-xl border border-gray-100 bg-white px-4 py-3 text-sm">
                        <div class="mt-0.5 text-xs text-gray-400 whitespace-nowrap">{{ $comm->communication_date->format('d M Y') }}</div>
                        <div class="flex-1">
                            <span class="font-medium text-gray-700">{{ $comm->type }}</span>
                            <span class="ml-2 text-xs px-1.5 py-0.5 rounded" style="background:#f0f9ff;color:#0369a1;">{{ $comm->direction }}</span>
                            @if($comm->subject) <span class="ml-2 text-gray-600">— {{ $comm->subject }}</span> @endif
                            @if($comm->notes) <p class="text-gray-500 mt-0.5">{{ $comm->notes }}</p> @endif
                            @if($comm->document_path)
                                <a href="{{ Storage::url($comm->document_path) }}" target="_blank"
                                    class="mt-1 inline-block text-xs underline" style="color:#0092b4;">Attachment</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Upload Document ──────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-file-arrow-up mr-2 text-purple-500"></i>Documents</h3>
            </div>
            <div class="p-6 space-y-5">

                <form method="POST" action="{{ route('associate-portal.referrals.documents.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end border border-gray-200 rounded-xl p-4 bg-gray-50/60">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Initial Assessment Report"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.png"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#7c3aed;">
                            Upload Document
                        </button>
                    </div>
                </form>

                {{-- Documents uploaded by this associate --}}
                @php $myDocs = $referral->documents->where('uploaded_by', auth()->id()); @endphp
                @if($myDocs->isEmpty())
                    <p class="text-sm text-gray-400 italic">No documents uploaded by you yet.</p>
                @else
                <ul class="divide-y divide-gray-100">
                    @foreach($myDocs as $doc)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="text-gray-800">{{ $doc->title }}</span>
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                            class="text-xs font-medium underline" style="color:#0092b4;">Download</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        {{-- Submit Bill ──────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h3 class="font-semibold text-gray-700 text-sm"><i class="fa-solid fa-file-invoice-dollar mr-2 text-amber-500"></i>Bills</h3>
            </div>
            <div class="p-6 space-y-5">

                <form method="POST" action="{{ route('associate-portal.referrals.bills.store', $referral) }}"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border border-gray-200 rounded-xl p-4 bg-gray-50/60">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="bill_date" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Amount (£) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="0" step="0.01" placeholder="0.00"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                            <option value="Pending">Pending</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Invoice PDF (optional)</label>
                        <input type="file" name="document" accept=".pdf"
                            class="w-full text-xs text-gray-600">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Initial assessment fee"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none">
                    </div>
                    <div>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background:#d97706;">
                            Submit Bill
                        </button>
                    </div>
                </form>

                @if($referral->bills->isEmpty())
                    <p class="text-sm text-gray-400 italic">No bills submitted yet.</p>
                @else
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wide">
                            <th class="pb-2 text-left">Date</th>
                            <th class="pb-2 text-left">Amount</th>
                            <th class="pb-2 text-left">Status</th>
                            <th class="pb-2 text-left">Notes</th>
                            <th class="pb-2 text-left">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($referral->bills as $bill)
                        @php
                        $bs = ['Paid'=>['bg'=>'#f0fdf4','txt'=>'#15803d'],'Unpaid'=>['bg'=>'#fef2f2','txt'=>'#b91c1c'],'Pending'=>['bg'=>'#fefce8','txt'=>'#854d0e']][$bill->status] ?? [];
                        @endphp
                        <tr>
                            <td class="py-2 text-gray-600">{{ $bill->bill_date->format('d M Y') }}</td>
                            <td class="py-2 font-semibold text-gray-900">£{{ number_format($bill->amount, 2) }}</td>
                            <td class="py-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                    style="background:{{ $bs['bg'] ?? '#f3f4f6' }};color:{{ $bs['txt'] ?? '#374151' }};">
                                    {{ $bill->status }}
                                </span>
                            </td>
                            <td class="py-2 text-gray-500">{{ $bill->notes ?? '—' }}</td>
                            <td class="py-2">
                                @if($bill->document_path)
                                    <a href="{{ Storage::url($bill->document_path) }}" target="_blank"
                                        class="text-xs underline" style="color:#0092b4;">View</a>
                                @else —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="pt-2">
            <a href="{{ route('associate-portal.referrals') }}"
                class="text-sm font-medium" style="color:#0092b4;">← Back to My Referrals</a>
        </div>

    </div>
</x-app-layout>
