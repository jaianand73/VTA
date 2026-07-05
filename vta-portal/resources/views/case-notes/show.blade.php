@php
    $header = 'Case Note Details';
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">View case note</p>
            <div class="flex gap-3">
                @if(!$caseNote->is_signed_off)
                <a href="{{ route('case-notes.edit', $caseNote) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit
                </a>
                <form method="POST" action="{{ route('case-notes.sign-off', $caseNote) }}" class="inline" data-swal="Sign off this case note? This action cannot be undone.">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        <i class="fa-solid fa-check-circle"></i>
                        Sign Off
                    </button>
                </form>
                @endif
                <a href="{{ route('case-notes.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p>
                    <p class="text-sm font-medium text-gray-900">
                        <a href="{{ route('patients.show', $caseNote->patient) }}" class="text-[#0092b4] hover:underline">
                            {{ $caseNote->patient?->first_name }} {{ $caseNote->patient?->last_name }}
                        </a>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Associate</p>
                    <p class="text-sm font-medium text-gray-900">{{ $caseNote->associate?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Session Date</p>
                    <p class="text-sm font-medium text-gray-900">{{ $caseNote->session_date ? \Carbon\Carbon::parse($caseNote->session_date)->format('d M Y') : '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Note Type</p>
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                        {{ $caseNote->note_type }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>
                    @if($caseNote->is_signed_off)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                        Signed off by {{ $caseNote->signedOffBy?->name ?? 'Unknown' }}
                        ({{ $caseNote->signed_off_at ? \Carbon\Carbon::parse($caseNote->signed_off_at)->format('d M Y H:i') : '' }})
                    </span>
                    @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pending Sign-off</span>
                    @endif
                </div>
            </div>

            @if($caseNote->content)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Content</p>
                <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-4">{{ $caseNote->content }}</div>
            </div>
            @endif

            @if($caseNote->document_path)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Document</p>
                <a href="#" class="inline-flex items-center gap-2 text-sm text-[#0092b4] hover:underline">
                    <i class="fa-solid fa-file-pdf"></i>
                    View Document
                </a>
            </div>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Created</p>
                <p class="text-sm text-gray-600">{{ $caseNote->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        {{-- Q47 — Clinical Head revision feedback panel (admin only) --}}
        @if(in_array(Auth::user()->role, ['admin', 'developer']))
        <div class="rounded-xl border p-5" style="border-color:#e9d5ff;background:#faf5ff;">
            <h3 class="text-sm font-semibold mb-3" style="color:#6b21a8;">
                <i class="fa-solid fa-comment-medical mr-1"></i> Clinical Head Review / Revision Feedback
            </h3>
            @if($caseNote->review_feedback)
            <div class="rounded-lg p-3 mb-3" style="background:#fef3c7;border:1px solid #fde68a;">
                <p class="text-xs font-semibold text-amber-700 mb-1">
                    Previous feedback
                    @if($caseNote->reviewedBy) from {{ $caseNote->reviewedBy->name }}@endif
                    @if($caseNote->reviewed_at) on {{ \Carbon\Carbon::parse($caseNote->reviewed_at)->format('d M Y') }}@endif
                </p>
                <p class="text-sm text-amber-900">{{ $caseNote->review_feedback }}</p>
            </div>
            @endif
            @if(!$caseNote->is_signed_off)
            <form method="POST" action="{{ route('case-notes.feedback', $caseNote) }}">
                @csrf @method('PATCH')
                <label class="block text-xs font-medium text-gray-600 mb-1">Send revision request to associate</label>
                <textarea name="review_feedback" rows="3"
                          placeholder="Describe what needs to be revised or clarified…"
                          class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-purple-400 mb-2">{{ $caseNote->review_feedback }}</textarea>
                <button type="submit"
                        style="background:#7c3aed;color:#fff;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Send Feedback
                </button>
            </form>
            @endif
        </div>
        @endif

        @if(!$caseNote->is_signed_off)
        <form method="POST" action="{{ route('case-notes.destroy', $caseNote) }}" data-swal-label="this case note">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                <i class="fa-solid fa-trash-can"></i>
                Delete Case Note
            </button>
        </form>
        @endif
    </div>
</x-app-layout>
