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
