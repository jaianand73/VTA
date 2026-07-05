@php
    $header = 'Schedule Appointment';
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto" x-data="{ context: 'patient' }">

        {{-- Context toggle --}}
        <div class="mb-6 flex rounded-xl border border-gray-200 bg-white p-1 shadow-sm w-fit gap-1">
            <button type="button" @click="context = 'patient'"
                :class="context === 'patient' ? 'bg-[#0092b4] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="rounded-lg px-5 py-2 text-sm font-medium transition">
                <i class="fa-solid fa-user-injured mr-1.5"></i>Patient Appointment
            </button>
            <button type="button" @click="context = 'referral'"
                :class="context === 'referral' ? 'text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                :style="context === 'referral' ? 'background:#059669;' : ''"
                class="rounded-lg px-5 py-2 text-sm font-medium transition">
                <i class="fa-solid fa-file-medical mr-1.5"></i>Referral — First Assessment
            </button>
        </div>

        {{-- PATIENT APPOINTMENT form --}}
        <div x-show="context === 'patient'">
            <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient <span class="text-red-500">*</span></label>
                            <select name="patient_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                    ({{ $patient->caseManager?->company?->name ?? 'No Company' }})
                                </option>
                                @endforeach
                            </select>
                            @error('patient_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Associate <span class="text-red-500">*</span></label>
                            <select name="associate_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">Select Associate</option>
                                @foreach($associates as $associate)
                                <option value="{{ $associate->id }}" {{ old('associate_id') == $associate->id ? 'selected' : '' }}>
                                    {{ $associate->name }} ({{ $associate->region }})
                                </option>
                                @endforeach
                            </select>
                            @error('associate_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type <span class="text-red-500">*</span></label>
                            <select name="activity_type_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="">Select Type</option>
                                @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}" {{ old('activity_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('activity_type_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            @error('scheduled_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" max="480"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Patient Home, Clinic, Remote"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('appointments.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-6 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Appointment
                    </button>
                </div>
            </form>
        </div>

        {{-- REFERRAL SESSION form --}}
        <div x-show="context === 'referral'" x-cloak>
            <form method="POST" action="{{ route('referrals.sessions.store.from-calendar') }}" class="space-y-6">
                @csrf
                <div class="rounded-xl border border-green-200 bg-white p-6 space-y-4">
                    <p class="text-xs text-green-700 bg-green-50 rounded-lg px-4 py-2 border border-green-200">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        This logs a session against a referral that is currently in the First Assessment stage.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Referral <span class="text-red-500">*</span></label>
                            <select name="referral_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="">Select Referral</option>
                                @foreach($referrals as $ref)
                                <option value="{{ $ref->id }}">
                                    {{ $ref->referral_ref }} — {{ $ref->patient_first_name }} {{ $ref->patient_last_name }}
                                    ({{ $ref->status }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type <span class="text-red-500">*</span></label>
                            <select name="activity_type_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="">Select Type</option>
                                @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Session Date <span class="text-red-500">*</span></label>
                            <input type="date" name="session_date" required
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</label>
                            <input type="datetime-local" name="scheduled_at"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" value="60" min="15" max="480"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</label>
                            <input type="text" name="location" placeholder="e.g. Patient's home"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                        <textarea name="notes" rows="3"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('appointments.calendar') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-lg px-6 py-2 text-sm font-medium text-white" style="background:#059669;">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Log Session
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
