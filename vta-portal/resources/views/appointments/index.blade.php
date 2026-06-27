@php
    $header = 'Appointments';
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Manage all patient appointments</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('appointments.calendar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-calendar-days"></i>
                    Calendar View
                </a>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-plus"></i>
                    Add Appointment
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date/Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Associate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Activity Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($appointments as $appt)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y H:i') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $appt->patient?->first_name }} {{ $appt->patient?->last_name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $appt->associate?->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $appt->activityType?->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $appt->location ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
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
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('appointments.show', $appt) }}" class="text-[#0092b4] hover:text-[#007a9a]">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('appointments.edit', $appt) }}" class="text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                            <i class="fa-solid fa-calendar-xmark text-3xl text-gray-300 mb-2"></i>
                            <p>No appointments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>
</x-app-layout>
