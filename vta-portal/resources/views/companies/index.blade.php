<x-app-layout>
    <x-slot name="header">Companies</x-slot>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('companies.index') }}" class="flex flex-1 gap-4">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..." class="block w-full rounded-md border border-gray-300 pl-10 pr-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
            </div>
            <select name="status" class="block rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">All Statuses</option>
                <option value="Active" @selected(request('status') === 'Active')>Active</option>
                <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
            </select>
            <select name="type" class="block rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">All Types</option>
                <option value="Case Management" @selected(request('type') === 'Case Management')>Case Management</option>
                <option value="Law Firm" @selected(request('type') === 'Law Firm')>Law Firm</option>
                <option value="Solicitor" @selected(request('type') === 'Solicitor')>Solicitor</option>
                <option value="Insurance" @selected(request('type') === 'Insurance')>Insurance</option>
                <option value="Individual" @selected(request('type') === 'Individual')>Individual</option>
                <option value="Other" @selected(request('type') === 'Other')>Other</option>
            </select>
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Filter</button>
        </form>
        <a href="{{ route('companies.create') }}" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus mr-1"></i> Add Company
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                    <th class="px-4 py-3">Company Name</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">City</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Case Managers</th>
                    <th class="px-4 py-3">First Contact</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($companies as $company)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $company->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $company->type }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $company->city ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $company->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $company->case_managers_count }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $company->first_contact_date ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('companies.show', $company) }}" class="text-sm text-[#0092b4] hover:underline">View</a>
                            <a href="{{ route('companies.edit', $company) }}" class="text-sm text-gray-600 hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No companies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $companies->links() }}
    </div>
</x-app-layout>
