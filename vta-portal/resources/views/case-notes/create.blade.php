@php
    $header = 'Add Case Note';
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('case-notes.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_id" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" id="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('patient_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="associate_id" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate <span class="text-red-500">*</span></label>
                        <select name="associate_id" id="associate_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Associate</option>
                            @foreach($associates as $associate)
                            <option value="{{ $associate->id }}" {{ old('associate_id') == $associate->id ? 'selected' : '' }}>
                                {{ $associate->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('associate_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="session_date" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Date <span class="text-red-500">*</span></label>
                        <input type="date" name="session_date" id="session_date" value="{{ old('session_date') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('session_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="note_type" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Note Type <span class="text-red-500">*</span></label>
                        <select name="note_type" id="note_type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Type</option>
                            <option value="Session Note" {{ old('note_type') == 'Session Note' ? 'selected' : '' }}>Session Note</option>
                            <option value="Progress Note" {{ old('note_type') == 'Progress Note' ? 'selected' : '' }}>Progress Note</option>
                            <option value="Discharge Note" {{ old('note_type') == 'Discharge Note' ? 'selected' : '' }}>Discharge Note</option>
                            <option value="Supervision Note" {{ old('note_type') == 'Supervision Note' ? 'selected' : '' }}>Supervision Note</option>
                            <option value="Other" {{ old('note_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('note_type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="content" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Content</label>
                    <textarea name="content" id="content" rows="6" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('content') }}</textarea>
                    @error('content')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('case-notes.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Save Case Note
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
