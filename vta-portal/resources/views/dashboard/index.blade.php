<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-[#0092b4]/10 p-3">
                    <i class="fa-solid fa-user-injured text-xl text-[#0092b4]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Active Cases</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalActiveCases }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-amber-100 p-3">
                    <i class="fa-solid fa-flag text-xl text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Needs Review Today</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $needsReviewCount }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-purple-100 p-3">
                    <i class="fa-solid fa-clock text-xl text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Awaiting Funding Approval</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $awaitingFundingCount }}</p>
                </div>
            </div>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-red-100 p-3">
                    <i class="fa-solid fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Overdue Invoices</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $overdueInvoices->count() }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">Daily Actions — Needs Review</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Referral Date</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($dailyActions as $patient)
                            <tr class="even:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">{{ $patient->status }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-600">{{ $patient->referral_date?->format('d/m/Y') }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-sm text-[#0092b4] hover:underline">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No patients need review.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-3">
                    <a href="{{ route('patients.index') }}" class="text-sm text-[#0092b4] hover:underline">View all patients</a>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">Upcoming Appointments (7 days)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Date/Time</th>
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Associate</th>
                                <th class="px-6 py-3">Location</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($upcomingAppointments as $apt)
                            <tr class="even:bg-gray-50">
                                <td class="px-6 py-3 text-gray-800">{{ $apt->scheduled_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $apt->patient?->first_name }} {{ $apt->patient?->last_name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $apt->associate?->name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $apt->location }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No appointments in the next 7 days.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">Unprocessed Emails</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($unprocessedEmails as $email)
                    <div class="px-6 py-3">
                        <p class="truncate text-sm font-medium text-gray-800">{{ $email->from_name ?? $email->from_email }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $email->subject }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $email->received_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-500">All emails processed.</div>
                    @endforelse
                </div>
                <div class="border-t border-gray-200 px-6 py-3">
                    <a href="{{ route('email-intake.index') }}" class="text-sm text-[#0092b4] hover:underline">View all unprocessed emails</a>
                </div>
            </div>

            @if(Auth::user()->role === 'admin' && $overdueAlerts->isNotEmpty())
            <div class="rounded-lg border border-red-200 bg-red-50">
                <div class="border-b border-red-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-red-800">Overdue Alerts</h3>
                </div>
                <div class="divide-y divide-red-100">
                    @foreach($overdueAlerts as $alert)
                    <div class="px-6 py-3">
                        <p class="font-medium text-red-800">{{ $alert->associate?->name }} — £{{ number_format($alert->total_amount, 2) }}</p>
                        <p class="text-xs text-red-600">Due: {{ $alert->due_date?->format('d/m/Y') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
