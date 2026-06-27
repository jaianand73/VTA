@php
    $header = 'Associate Portal';
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">My Patients</p>
                <p class="mt-2 text-3xl font-bold text-[#0092b4]">{{ $patientCount }}</p>
                <p class="mt-1 text-xs text-gray-500">Active assigned patients</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming Appointments</p>
                <p class="mt-2 text-3xl font-bold text-[#0092b4]">{{ $upcomingAppointments->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">Next 14 days</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Awaiting Case Notes</p>
                <p class="mt-2 text-3xl font-bold text-amber-500">{{ $completedAppointments->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">Completed appointments without notes</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Upcoming Appointments</h2>
                @if($upcomingAppointments->count())
                <div class="space-y-3">
                    @foreach($upcomingAppointments as $appt)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $appt->patient?->first_name }} {{ $appt->patient?->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y H:i') }}
                                — {{ $appt->activityType?->name }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $appt->location ?? '-' }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No upcoming appointments.</p>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Awaiting Case Notes</h2>
                @if($completedAppointments->count())
                <div class="space-y-3">
                    @foreach($completedAppointments as $appt)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $appt->patient?->first_name }} {{ $appt->patient?->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pending</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">All case notes up to date.</p>
                @endif
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('associate-portal.calendar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-calendar-days"></i>
                My Calendar
            </a>
        </div>
    </div>
</x-app-layout>
