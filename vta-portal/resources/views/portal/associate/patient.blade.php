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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Case Notes</h2>

                @if($caseNotes->count())
                <div class="space-y-3 mb-4">
                    @foreach($caseNotes as $note)
                    <div class="border-b border-gray-100 pb-2">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($note->session_date)->format('d M Y') }}
                                — {{ $note->note_type }}
                            </p>
                            @if($note->is_signed_off)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Signed</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pending</span>
                            @endif
                        </div>
                        @if($note->content)
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $note->content }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <button type="button" onclick="document.getElementById('note-form').classList.toggle('hidden')" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-plus"></i>
                    Add Case Note
                </button>

                <form id="note-form" method="POST" action="{{ route('associate-portal.upload-note') }}" class="hidden mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Date</label>
                        <input type="date" name="session_date" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Note Type</label>
                        <select name="note_type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Session Note">Session Note</option>
                            <option value="Progress Note">Progress Note</option>
                            <option value="Discharge Note">Discharge Note</option>
                            <option value="Supervision Note">Supervision Note</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Content</label>
                        <textarea name="content" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                        Save Case Note
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
