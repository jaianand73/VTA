@php
    $header = 'Case Manager Portal';
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Welcome, {{ $caseManager->first_name }} {{ $caseManager->last_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $caseManager->company?->name }}</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-[#0092b4]/10 px-3 py-1 text-sm font-medium text-[#0092b4]">
                    {{ $activePatients->count() }} Active {{ Str::plural('Patient', $activePatients->count()) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Patients</p>
                <p class="mt-2 text-3xl font-bold text-[#0092b4]">{{ $patients->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">All referrals</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Patients</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $activePatients->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">Currently in treatment</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming Appointments</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ $upcomingAppointments->count() }}</p>
                <p class="mt-1 text-xs text-gray-500">Next 14 days</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">My Patients</h2>
                @if($patients->count())
                <div class="space-y-2">
                    @foreach($patients as $patient)
                    <a href="{{ route('case-manager-portal.patient', $patient) }}" class="flex items-center justify-between rounded-lg border border-gray-100 p-3 hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                            <p class="text-xs text-gray-500">{{ $patient->location ?? '-' }} | Referred: {{ \Carbon\Carbon::parse($patient->referral_date)->format('d M Y') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @switch($patient->status)
                                @case('Enquiry Logged') bg-blue-100 text-blue-800 @break
                                @case('Response Sent') bg-cyan-100 text-cyan-800 @break
                                @case('Awaiting LOI') bg-amber-100 text-amber-800 @break
                                @case('LOI Received') bg-purple-100 text-purple-800 @break
                                @case('Assessment Scheduled') bg-pink-100 text-pink-800 @break
                                @case('Assessment Completed') bg-indigo-100 text-indigo-800 @break
                                @case('Report Drafted') bg-teal-100 text-teal-800 @break
                                @case('Report Sent') bg-sky-100 text-sky-800 @break
                                @case('Cost Estimation Sent') bg-orange-100 text-orange-800 @break
                                @case('Awaiting Funding Approval') bg-red-100 text-red-800 @break
                                @case('Funding Approved') bg-emerald-100 text-emerald-800 @break
                                @case('Treatment Active') bg-green-100 text-green-800 @break
                                @case('Awaiting Further Funding') bg-amber-100 text-amber-800 @break
                                @case('Discharged') bg-gray-100 text-gray-600 @break
                                @case('Case Closed') bg-gray-200 text-gray-700 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ $patient->status }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No patients assigned to you yet.</p>
                @endif
            </div>

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
                                — {{ $appt->associate?->name ?? 'N/A' }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $appt->activityType?->name }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No upcoming appointments.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
