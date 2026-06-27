@props(['caseManagerId' => null, 'patientId' => null])

<div x-data="{ open: false }">
    <button @@click="open = true" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]" {{ $attributes }}>
        <i class="fa-solid fa-plus mr-1"></i> Add Communication
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @@click.self="open = false">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-800">New Communication</h4>
                <button @@click="open = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('communications.store') }}">
                @csrf
                @if($caseManagerId)
                <input type="hidden" name="case_manager_id" value="{{ $caseManagerId }}">
                @endif
                @if($patientId)
                <input type="hidden" name="patient_id" value="{{ $patientId }}">
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="Email">Email</option>
                            <option value="Phone">Phone</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Note">Note</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Direction</label>
                        <select name="direction" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="Inbound">Inbound</option>
                            <option value="Outbound">Outbound</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date / Time</label>
                        <input type="datetime-local" name="communication_date" value="{{ old('communication_date', date('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Summary</label>
                    <textarea name="summary" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @@click="open = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Save Communication</button>
                </div>
            </form>
        </div>
    </div>
</div>
