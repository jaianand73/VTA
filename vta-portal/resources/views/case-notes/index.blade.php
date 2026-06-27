@php
    $header = 'Case Notes';
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">All clinical case notes</p>
            <a href="{{ route('case-notes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                <i class="fa-solid fa-plus"></i>
                Add Case Note
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Associate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Signed Off</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($caseNotes as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($note->session_date)->format('d M Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $note->patient?->first_name }} {{ $note->patient?->last_name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $note->associate?->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $note->note_type }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($note->is_signed_off)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                <i class="fa-solid fa-check mr-1"></i> Signed
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('case-notes.show', $note) }}" class="text-[#0092b4] hover:text-[#007a9a]">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(!$note->is_signed_off)
                                <a href="{{ route('case-notes.edit', $note) }}" class="text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('case-notes.sign-off', $note) }}" class="inline" data-swal="Sign off this case note? This cannot be undone.">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-500 hover:text-green-700" title="Sign Off">
                                        <i class="fa-solid fa-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                            <i class="fa-solid fa-note-sticky text-3xl text-gray-300 mb-2"></i>
                            <p>No case notes found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $caseNotes->links() }}
        </div>
    </div>
</x-app-layout>
