@php
    $header = 'Edit Appointment';
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_id" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" id="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->first_name }} {{ $patient->last_name }}
                                ({{ $patient->caseManager?->company?->name ?? 'No Company' }})
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
                            <option value="{{ $associate->id }}" {{ old('associate_id', $appointment->associate_id) == $associate->id ? 'selected' : '' }}>
                                {{ $associate->name }} ({{ $associate->region }})
                            </option>
                            @endforeach
                        </select>
                        @error('associate_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="activity_type_id" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type <span class="text-red-500">*</span></label>
                        <select name="activity_type_id" id="activity_type_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Type</option>
                            @foreach($activityTypes as $type)
                            <option value="{{ $type->id }}" {{ old('activity_type_id', $appointment->activity_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('activity_type_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="scheduled_at" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $appointment->scheduled_at ? \Carbon\Carbon::parse($appointment->scheduled_at)->format('Y-m-d\TH:i') : '') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('scheduled_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="duration_minutes" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $appointment->duration_minutes ?? 60) }}" min="15" max="480" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('duration_minutes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="location" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $appointment->location) }}" placeholder="e.g. Patient Home, Clinic, Remote" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Scheduled" {{ old('status', $appointment->status) == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Completed" {{ old('status', $appointment->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status', $appointment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="DNA" {{ old('status', $appointment->status) == 'DNA' ? 'selected' : '' }}>DNA</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('appointments.show', $appointment) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Update Appointment
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
