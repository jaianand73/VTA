<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- F1 — Dashboard 5 widgets --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Widget 1: Emails --}}
        <a href="{{ route('email-intake.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 block hover:border-blue-300 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Emails</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $unprocessedEmailsCount }}</p>
                    <p class="text-xs text-gray-400">Unprocessed</p>
                </div>
                <div class="rounded-lg bg-blue-100 p-3 text-blue-600">
                    <i class="fa-solid fa-envelope-open-text text-lg"></i>
                </div>
            </div>
        </a>

        {{-- Widget 2: Clinical Head Review --}}
        <a href="{{ route('patients.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 block hover:border-purple-300 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Clinical Review</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $clinicalHeadReview }}</p>
                    <p class="text-xs text-gray-400">Needs review</p>
                </div>
                <div class="rounded-lg bg-purple-100 p-3 text-purple-600">
                    <i class="fa-solid fa-stethoscope text-lg"></i>
                </div>
            </div>
        </a>

        {{-- Widget 3: Pending Enquiries --}}
        <a href="{{ route('enquiries.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 block hover:border-amber-300 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Enquiries</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $pendingEnquiries }}</p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
                <div class="rounded-lg bg-amber-100 p-3 text-amber-600">
                    <i class="fa-solid fa-circle-question text-lg"></i>
                </div>
            </div>
        </a>

        {{-- Widget 4: Invoices Due --}}
        <a href="{{ route('accounts.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 block hover:border-red-300 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoices Due</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $invoicesDue }}</p>
                    <p class="text-xs text-gray-400">Unpaid</p>
                </div>
                <div class="rounded-lg bg-red-100 p-3 text-red-600">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
            </div>
        </a>

        {{-- Widget 5: Shared Calendar --}}
        <a href="{{ route('appointments.calendar') }}" class="rounded-xl border border-gray-200 bg-white p-4 block hover:border-teal-300 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Calendar</p>
                    <p class="text-xs text-gray-400 mt-1">Appointments</p>
                </div>
                <div class="rounded-lg bg-teal-100 p-3 text-teal-600">
                    <i class="fa-solid fa-calendar-days text-lg"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Q54 compliance expiry alerts --}}
    @if($expiringCompliance->isNotEmpty())
    <div class="rounded-xl border mb-4 p-4" style="border-color:#fde68a;background:#fffbeb;">
        <h3 class="text-sm font-semibold mb-2" style="color:#92400e;">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            Associate Compliance Alerts ({{ $expiringCompliance->count() }})
        </h3>
        <div class="space-y-1">
            @foreach($expiringCompliance as $doc)
            @php $docExpired = $doc->expiry_date->isPast(); @endphp
            <div class="flex items-center justify-between text-sm">
                <span>
                    <a href="{{ route('settings.associates.show', $doc->associate) }}" class="font-medium text-amber-800 hover:underline">{{ $doc->associate->name }}</a>
                    — {{ $doc->document_type }}
                </span>
                <span class="{{ $docExpired ? 'text-red-600 font-semibold' : 'text-amber-600' }}">
                    {{ $docExpired ? 'EXPIRED' : 'Expires' }} {{ $doc->expiry_date->format('d M Y') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Q65 pending associate invoices --}}
    @if($pendingAssocInvoices->isNotEmpty())
    <div class="rounded-xl border mb-4 p-4" style="border-color:#bfdbfe;background:#eff6ff;">
        <h3 class="text-sm font-semibold mb-2" style="color:#1e40af;">
            <i class="fa-solid fa-file-invoice mr-1"></i>
            Associate Invoices Awaiting Approval ({{ $pendingAssocInvoices->count() }})
        </h3>
        @foreach($pendingAssocInvoices as $inv)
        <div class="flex justify-between text-sm py-1">
            <span>{{ $inv->associate->name }} — £{{ number_format($inv->total_amount, 2) }}</span>
            <a href="{{ route('associate-invoices.show', $inv) }}" class="text-blue-600 hover:underline">Review</a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Q32 clinical head review detail panel --}}
    @if($reviewNotes->isNotEmpty())
    <div class="rounded-xl border mb-4 p-4" style="border-color:#e9d5ff;background:#faf5ff;">
        <h3 class="text-sm font-semibold mb-2" style="color:#6b21a8;">
            <i class="fa-solid fa-user-doctor mr-1"></i>
            Clinical Head Review Required ({{ $clinicalHeadReview }})
        </h3>
        <div class="space-y-1">
            @foreach($reviewNotes as $note)
            <div class="flex items-center justify-between text-sm">
                <span>
                    <span class="font-medium">{{ $note->patient?->first_name }} {{ $note->patient?->last_name }}</span>
                    <span class="text-gray-400 text-xs ml-1">— {{ $note->associate?->name }} · {{ $note->session_date?->format('d M Y') }}</span>
                </span>
                <a href="{{ route('case-notes.show', $note) }}" class="text-xs font-medium px-3 py-1 rounded-full" style="background:#e9d5ff;color:#6b21a8;">Review</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</x-app-layout>
