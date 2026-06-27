<x-app-layout>
    <x-slot name="header">Email Intake</x-slot>

    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div x-data="{ showCreateModal: false }" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700">
                    {{ $emailLogs->where('processed', false)->count() }} Unprocessed
                </span>
                <div class="flex gap-1 rounded-lg border border-gray-200 bg-white p-1 text-sm">
                    <a href="{{ route('email-intake.index', ['filter' => 'all']) }}" class="rounded-md px-3 py-1 {{ request('filter', 'all') === 'all' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
                    <a href="{{ route('email-intake.index', ['filter' => 'unprocessed']) }}" class="rounded-md px-3 py-1 {{ request('filter') === 'unprocessed' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Unprocessed</a>
                    <a href="{{ route('email-intake.index', ['filter' => 'processed']) }}" class="rounded-md px-3 py-1 {{ request('filter') === 'processed' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Processed</a>
                </div>
            </div>
            <button @click="showCreateModal = true" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                <i class="fa-solid fa-plus"></i>
                Create Manual Email
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                        <th class="px-4 py-3">Received</th>
                        <th class="px-4 py-3">From</th>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Attachments</th>
                        <th class="px-4 py-3">Linked To</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($emailLogs as $email)
                    <tr class="even:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $email->received_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $email->from_name ?? $email->from_email }}</td>
                        <td class="px-4 py-3 max-w-xs truncate text-gray-700">{{ $email->subject }}</td>
                        <td class="px-4 py-3">
                            @if($email->has_attachments)
                            <i class="fa-solid fa-paperclip text-gray-400" title="Has attachments"></i>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($email->linkedPatient)
                            <a href="{{ route('patients.show', $email->linkedPatient) }}" class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Patient</a>
                            @elseif($email->linkedCaseManager)
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-700">Case Manager</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex gap-2" x-data="{ showLinkModal: false }">
                                @if(!$email->processed)
                                <button @click="showLinkModal = true" class="text-sm text-[#0092b4] hover:underline">Link</button>

                                <div x-show="showLinkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showLinkModal = false">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h4 class="mb-4 text-lg font-semibold text-gray-800">Link Email</h4>
                                        <form method="POST" action="{{ route('email-intake.link', $email) }}" class="space-y-4">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Link to Patient</label>
                                                <select name="patient_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                                    <option value="">Select Patient</option>
                                                    @foreach(\App\Models\Patient::all() as $p)
                                                    <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Link to Case Manager</label>
                                                <select name="case_manager_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                                    <option value="">Select Case Manager</option>
                                                    @foreach(\App\Models\CaseManager::all() as $cm)
                                                    <option value="{{ $cm->id }}">{{ $cm->first_name }} {{ $cm->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showLinkModal = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('email-intake.destroy', $email) }}" class="inline" data-swal-label="this email">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:underline">Delete</button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">Processed</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No emails found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $emailLogs->links() }}
        </div>

        {{-- Create Manual Email Modal --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showCreateModal = false">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                <h4 class="mb-4 text-lg font-semibold text-gray-800">Create Manual Email</h4>
                <form method="POST" action="{{ route('email-intake.manual') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Email *</label>
                        <input type="email" name="from_email" required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"
                            placeholder="sender@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Name</label>
                        <input type="text" name="from_name"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"
                            placeholder="Sender Name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject *</label>
                        <input type="text" name="subject" required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"
                            placeholder="Email subject">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Body</label>
                        <textarea name="body" rows="4"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"
                            placeholder="Email body content..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Received Date</label>
                        <input type="datetime-local" name="received_at"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="has_attachments" id="has_attachments" value="1"
                            class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                        <label for="has_attachments" class="text-sm text-gray-700">Has attachments</label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
