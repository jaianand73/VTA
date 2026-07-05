<x-app-layout>
    <x-slot name="header">Enquiries</x-slot>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-4">
            <form method="GET" action="{{ route('enquiries.index') }}" class="flex flex-1 gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search enquiries..." class="block w-full rounded-md border border-gray-300 pl-10 pr-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                </div>
                <select name="status" class="block rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    <option value="">All Statuses</option>
                    <option value="New" @selected(request('status') === 'New')>New</option>
                    <option value="In Progress" @selected(request('status') === 'In Progress')>In Progress</option>
                    <option value="Converted to Referral" @selected(request('status') === 'Converted to Referral')>Converted to Referral</option>
                    <option value="Not Proceeding" @selected(request('status') === 'Not Proceeding')>Not Proceeding</option>
                </select>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Filter</button>
            </form>
        </div>
        <a href="{{ route('enquiries.create') }}" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus mr-1"></i> Log New Enquiry
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                    <th class="px-4 py-3">Enquiry ID</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Enquirer Name</th>
                    <th class="px-4 py-3">Company</th>
                    <th class="px-4 py-3">Case Manager</th>
                    <th class="px-4 py-3">Source</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">First Response</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($enquiries as $enquiry)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 text-xs font-medium text-[#0092b4]">{{ $enquiry->enquiry_ref ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $enquiry->enquiry_date?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $enquiry->enquirer_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $enquiry->company_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $enquiry->selectedCaseManager?->first_name }} {{ $enquiry->selectedCaseManager?->last_name }}{{ $enquiry->selectedCaseManager ? '' : '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $enquiry->source }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['New' => 'bg-blue-100 text-blue-700', 'In Progress' => 'bg-amber-100 text-amber-700', 'Converted to Referral' => 'bg-teal-100 text-teal-700', 'Not Proceeding' => 'bg-gray-100 text-gray-600'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$enquiry->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $enquiry->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $enquiry->first_response_date ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('enquiries.show', $enquiry) }}" class="text-sm text-[#0092b4] hover:underline">View</a>
                            @if(in_array(Auth::user()->role, ['admin', 'staff', 'developer']))
                            <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}"
                                  data-swal="Delete enquiry for {{ $enquiry->enquirer_name }}? This cannot be undone.">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No enquiries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $enquiries->links() }}
    </div>
</x-app-layout>
