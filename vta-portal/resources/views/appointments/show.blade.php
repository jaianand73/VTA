@php
    $header = 'Appointment Details';
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">View and manage appointment</p>
            <div class="flex gap-3">
                <a href="{{ route('appointments.edit', $appointment) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit
                </a>
                <a href="{{ route('appointments.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to List
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Patient</p>
                    <p class="text-sm font-medium text-gray-900">
                        <a href="{{ route('patients.show', $appointment->patient) }}" class="text-[#0092b4] hover:underline">
                            {{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}
                        </a>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Associate</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->associate?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Activity Type</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->activityType?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @switch($appointment->status)
                            @case('Scheduled') bg-blue-100 text-blue-800 @break
                            @case('Completed') bg-green-100 text-green-800 @break
                            @case('Cancelled') bg-red-100 text-red-800 @break
                            @case('DNA') bg-amber-100 text-amber-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ $appointment->status ?? 'Scheduled' }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date & Time</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->scheduled_at ? \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y H:i') : '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Duration</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->duration_minutes ?? 60 }} minutes</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Location</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->location ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Travel Miles</p>
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->travel_miles ?? '0' }}</p>
                </div>
            </div>

            @if($appointment->notes)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</p>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $appointment->notes }}</p>
            </div>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Created by</p>
                <p class="text-sm text-gray-600">{{ $appointment->createdBy?->name ?? 'Unknown' }} on {{ $appointment->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" data-swal-label="this appointment">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                <i class="fa-solid fa-trash-can"></i>
                Delete Appointment
            </button>
        </form>
    </div>
</x-app-layout>
