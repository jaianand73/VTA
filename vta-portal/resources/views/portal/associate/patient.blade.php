@php
    $header = $patient->first_name . ' ' . $patient->last_name;
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Patient information (read-only)</p>
            <a href="{{ route('associate-portal.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        {{-- Patient Info + Appointments grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Patient Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</p>
                        <p class="text-sm text-gray-700">{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</p>
                        <p class="text-sm text-gray-700">{{ $patient->location ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</p>
                        <p class="text-sm text-gray-700">{{ $patient->condition ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $patient->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Case Manager</p>
                        <p class="text-sm text-gray-700">{{ $patient->caseManager?->first_name }} {{ $patient->caseManager?->last_name }} ({{ $patient->caseManager?->company?->name ?? 'N/A' }})</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">My Appointments</h2>
                @if($appointments->count())
                <div class="space-y-2">
                    @foreach($appointments as $appt)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y H:i') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $appt->activityType?->name }} — {{ $appt->location ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @switch($appt->status)
                                @case('Scheduled') bg-blue-100 text-blue-800 @break
                                @case('Completed') bg-green-100 text-green-800 @break
                                @case('Cancelled') bg-red-100 text-red-800 @break
                                @case('DNA') bg-amber-100 text-amber-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ $appt->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No appointments found.</p>
                @endif
            </div>
        </div>

        {{-- Session count card --}}
        @php
            $myAssignment = $patient->patientAssociates->firstWhere('associate_id', $associate->id);
        @endphp
        @if($myAssignment)
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">My Sessions</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-lg bg-blue-50 p-4 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $myAssignment->sessions_approved ?? '—' }}</p>
                    <p class="text-xs text-gray-500">Approved</p>
                </div>
                <div class="rounded-lg bg-green-50 p-4 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $myAssignment->sessions_used ?? '—' }}</p>
                    <p class="text-xs text-gray-500">Used</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Three-stage Case Notes --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Case Notes & Reports</h2>
            @foreach(['Draft' => 'Draft submitted for review', 'Revision' => 'Revisions made following review', 'Final' => 'Approved final report'] as $stage => $label)
            @php $stageNotes = $caseNotes->where('stage', $stage); @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 mb-3" x-data="{ open: false }">
                <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                    <h3 class="text-sm font-semibold text-gray-800">{{ $label }}</h3>
                    <div class="flex items-center gap-3">
                        @if($stageNotes->count())
                        <span class="text-xs text-green-600 font-medium">{{ $stageNotes->count() }} doc(s)</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition" :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </div>
                <div x-show="open" x-collapse class="mt-3 space-y-2">
                    @foreach($stageNotes as $note)
                    <div class="text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                        <div class="flex items-center justify-between">
                            <span>{{ $note->note_type }} — {{ $note->session_date?->format('d M Y') ?? '' }}</span>
                            @if($note->is_signed_off)
                            <span class="text-xs text-green-600 font-medium">✓ Signed off</span>
                            @endif
                        </div>
                        @if($note->review_feedback && !$note->is_signed_off)
                        <div class="mt-2 rounded-lg p-2" style="background:#fef3c7;border:1px solid #fde68a;">
                            <p class="text-xs font-semibold text-amber-700 mb-0.5"><i class="fa-solid fa-comment-dots mr-1"></i> Revision requested</p>
                            <p class="text-xs text-amber-900">{{ $note->review_feedback }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    <form method="POST" action="{{ route('associate-portal.upload-note') }}" enctype="multipart/form-data"
                          class="mt-2 border-t border-gray-100 pt-3">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <input type="hidden" name="stage" value="{{ $stage }}">
                        <input type="hidden" name="note_type" value="{{ $label }}">
                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500">Session date</label>
                                <input type="date" name="session_date" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs text-gray-500">Document</label>
                                <input type="file" name="document" class="mt-1 block w-full text-sm text-gray-500">
                            </div>
                            @if($stage === 'Final')
                            <label class="flex items-center gap-2 text-xs text-gray-600 mb-1">
                                <input type="checkbox" name="needs_review" value="1"> Flag for Clinical Head Review
                            </label>
                            @endif
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-[#0092b4] px-3 py-2 text-sm font-medium text-white hover:bg-[#007a99] flex-shrink-0">
                                <i class="fa-solid fa-upload"></i> Add Doc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Invoices --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">My Invoices for this Patient</h2>
            @php
                $myInvoices = $associate->invoices->where('patient_id', $patient->id)->sortByDesc('invoice_date');
            @endphp
            @if($myInvoices->count())
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-gray-100">
                        <th class="text-left pb-2">Invoice Ref</th>
                        <th class="text-left pb-2">Date</th>
                        <th class="text-left pb-2">Sessions</th>
                        <th class="text-right pb-2">Amount</th>
                        <th class="text-left pb-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myInvoices as $inv)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 text-gray-700">{{ $inv->invoice_reference }}</td>
                        <td class="py-2 text-gray-500">{{ $inv->invoice_date?->format('d M Y') }}</td>
                        <td class="py-2 text-gray-500">{{ $inv->sessions_count ?? '—' }}</td>
                        <td class="py-2 text-right text-gray-700">£{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="py-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $inv->status === 'Paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-gray-500">No invoices recorded for this patient yet.</p>
            @endif

            {{-- Q65 — Submit new invoice --}}
            <div class="mt-4 pt-4 border-t border-gray-100" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                        class="inline-flex items-center gap-2 rounded-lg text-sm font-medium px-4 py-2"
                        style="background:#0092b4;color:#fff;">
                    <i class="fa-solid fa-plus"></i> Submit Invoice
                </button>
                <form x-show="open" x-transition method="POST" action="{{ route('associate-portal.invoices.store') }}"
                      class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Invoice Date</label>
                        <input type="date" name="invoice_date" value="{{ now()->toDateString() }}" required
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sessions Completed</label>
                        <input type="number" name="sessions_completed" min="0"
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Session Amount (£)</label>
                        <input type="number" name="session_amount" step="0.01" min="0"
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Travel Amount (£)</label>
                        <input type="number" name="travel_amount" step="0.01" min="0"
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Amount (£) *</label>
                        <input type="number" name="total_amount" step="0.01" min="0" required
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <input type="text" name="notes" placeholder="Optional notes for admin"
                               class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" style="background:#0092b4;color:#fff;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Submit Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Permitted Documents --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Permitted Documents</h2>
            @php
                $visibleDocs = $patient->documents->filter(fn($doc) => $permittedDocTypes->contains($doc->document_type_id));
            @endphp
            @if($visibleDocs->count())
            <div class="space-y-2">
                @foreach($visibleDocs as $doc)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $doc->file_name }}</p>
                        <p class="text-xs text-gray-500">{{ $doc->documentType?->name }}</p>
                    </div>
                    <a href="{{ route('documents.download', $doc) }}" class="text-[#0092b4] hover:text-[#007a9a]">
                        <i class="fa-solid fa-download"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500">No documents available.</p>
            @endif
        </div>
    </div>
</x-app-layout>
