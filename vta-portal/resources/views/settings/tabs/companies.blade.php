<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-800">Companies</h2>
        <button onclick="document.getElementById('newCompanyForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-plus"></i> Add Company
        </button>
    </div>

    <form id="newCompanyForm" method="POST" action="{{ route('settings.companies.store') }}" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Name *</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Type</label>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                    <option value="Case Management">Case Management</option>
                    <option value="Law Firm">Law Firm</option>
                    <option value="Solicitor">Solicitor</option>
                    <option value="Insurance">Insurance</option>
                    <option value="Individual">Individual</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
            <button type="button" onclick="document.getElementById('newCompanyForm').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($companies as $company)
                <tr class="even:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $company->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $company->type }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $company->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $company->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = ['Active' => 'bg-emerald-100 text-emerald-800', 'Enquiry' => 'bg-amber-100 text-amber-800', 'Inactive' => 'bg-gray-100 text-gray-600'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$company->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $company->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="toggleEdit('company-{{ $company->id }}')" class="text-[#0092b4] hover:text-[#007a9a] text-sm font-medium">Edit</button>
                    </td>
                </tr>
                <tr id="edit-company-{{ $company->id }}" class="hidden">
                    <td colspan="6" class="bg-gray-50 px-4 py-3">
                        <form method="POST" action="{{ route('settings.companies.update', $company) }}" class="grid gap-4 sm:grid-cols-3">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $company->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                                <option value="Case Management" @selected($company->type === 'Case Management')>Case Management</option>
                                <option value="Law Firm" @selected($company->type === 'Law Firm')>Law Firm</option>
                                <option value="Solicitor" @selected($company->type === 'Solicitor')>Solicitor</option>
                                <option value="Insurance" @selected($company->type === 'Insurance')>Insurance</option>
                                <option value="Individual" @selected($company->type === 'Individual')>Individual</option>
                                <option value="Other" @selected($company->type === 'Other')>Other</option>
                            </select>
                            <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                                <option value="Active" @selected($company->status === 'Active')>Active</option>
                                <option value="Enquiry" @selected($company->status === 'Enquiry')>Enquiry</option>
                                <option value="Inactive" @selected($company->status === 'Inactive')>Inactive</option>
                            </select>
                            <input type="text" name="phone" value="{{ $company->phone }}" placeholder="Phone" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <input type="email" name="email" value="{{ $company->email }}" placeholder="Email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                            <div class="flex items-center gap-3">
                                <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#007a9a]">Save</button>
                                <button type="button" onclick="toggleEdit('company-{{ $company->id }}')" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
