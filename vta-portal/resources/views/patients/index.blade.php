<x-app-layout>
    <x-slot name="header">Patients</x-slot>

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-64 shrink-0">
            <div x-data="{ open: true }" class="rounded-lg border border-gray-200 bg-white p-4">
                <button @@click="open = !open" class="flex w-full items-center justify-between text-sm font-semibold text-gray-800 lg:hidden">
                    Filters <i class="fa-solid fa-chevron-down" x-bind:class="open && 'rotate-180'"></i>
                </button>
                <form method="GET" action="{{ route('patients.index') }}" x-show="open" class="space-y-4 mt-4 lg:mt-0">
                    <div>
                        <label class="block text-xs font-medium uppercase text-gray-500 mb-2">Status</label>
                        <div class="space-y-2 text-sm">
                            @foreach(['New','In Progress','Converted','Not Proceeding','Needs Review'] as $status)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="status[]" value="{{ $status }}" @checked(in_array($status, (array)request('status', []))) class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                                {{ $status }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium uppercase text-gray-500 mb-2">Needs Review</label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="needs_review" value="1" @checked(request('needs_review') === '1') class="rounded border-gray-300 text-[#0092b4] focus:ring-[#0092b4]">
                            Show needs review only
                        </label>
                    </div>

                    <div>
                        <label for="assigned_staff" class="block text-xs font-medium uppercase text-gray-500 mb-1">Assigned Staff</label>
                        <select name="assigned_staff" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="">All Staff</option>
                            @foreach($staffUsers as $user)
                            <option value="{{ $user->id }}" @selected(request('assigned_staff') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="associate" class="block text-xs font-medium uppercase text-gray-500 mb-1">Associate</label>
                        <select name="associate" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="">All Associates</option>
                            @foreach($associates as $associate)
                            <option value="{{ $associate->id }}" @selected(request('associate') == $associate->id)>{{ $associate->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="company" class="block text-xs font-medium uppercase text-gray-500 mb-1">Company</label>
                        <select name="company" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(request('company') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium uppercase text-gray-500 mb-1">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase text-gray-500 mb-1">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                        </div>
                    </div>

                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patients..." class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Apply</button>
                        <a href="{{ route('patients.index') }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-center text-sm text-gray-700 hover:bg-gray-50">Show All</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                            <th class="px-4 py-3">Patient Name</th>
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3">Case Manager</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Associate</th>
                            <th class="px-4 py-3">Assigned Staff</th>
                            <th class="px-4 py-3">Referral Date</th>
                            <th class="px-4 py-3">Review</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($patients as $patient)
                        <tr class="even:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                <a href="{{ route('patients.show', $patient) }}" class="text-[#0092b4] hover:underline">{{ $patient->first_name }} {{ $patient->last_name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $patient->caseManager?->company?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $patient->caseManager?->first_name }} {{ $patient->caseManager?->last_name }}</td>
                            <td class="px-4 py-3">
                                @php $colors = ['New'=>'bg-blue-100 text-blue-700','In Progress'=>'bg-amber-100 text-amber-700','Converted'=>'bg-green-100 text-green-700','Not Proceeding'=>'bg-gray-100 text-gray-600','Needs Review'=>'bg-red-100 text-red-700']; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$patient->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $patient->patientAssociates->first()?->associate?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $patient->assignedStaff?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $patient->referral_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($patient->needs_review)
                                <i class="fa-solid fa-circle-exclamation text-red-500" title="Needs Review"></i>
                                @else
                                <i class="fa-regular fa-circle-check text-green-500"></i>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('patients.show', $patient) }}" class="text-sm text-[#0092b4] hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">No patients found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
