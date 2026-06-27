<x-app-layout>
    <x-slot name="header">{{ $company->name }}</x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Company Information</h3>
                    <a href="{{ route('companies.edit', $company) }}" class="text-sm text-[#0092b4] hover:underline">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </a>
                </div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-800">{{ $company->name }}</dd></div>
                    <div><dt class="text-gray-500">Type</dt><dd class="font-medium text-gray-800">{{ $company->type }}</dd></div>
                    <div><dt class="text-gray-500">Address</dt><dd class="font-medium text-gray-800">{{ $company->address ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">City</dt><dd class="font-medium text-gray-800">{{ $company->city ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $company->phone ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $company->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt>
                        <dd><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $company->status }}</span></dd>
                    </div>
                    <div><dt class="text-gray-500">First Contact</dt><dd class="font-medium text-gray-800">{{ $company->first_contact_date?->format('d/m/Y') ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Case Managers</h3>
                    <button onclick="openAddCmModal()" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-sm text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Add Case Manager
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                                <th class="pb-2 pr-3">Name</th>
                                <th class="pb-2 pr-3">Email</th>
                                <th class="pb-2 pr-3">NDA</th>
                                <th class="pb-2 pr-3">Materials</th>
                                <th class="pb-2 pr-3">Active Patients</th>
                                <th class="pb-2 pr-3">Status</th>
                                <th class="pb-2 pr-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($company->caseManagers as $cm)
                            <tr class="even:bg-gray-50">
                                <td class="py-2 pr-3 font-medium text-gray-800">{{ $cm->first_name }} {{ $cm->last_name }}</td>
                                <td class="py-2 pr-3 text-gray-600">{{ $cm->email }}</td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cm->nda_signed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $cm->nda_signed ? 'Signed' : 'Pending' }}</span>
                                </td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cm->materials_sent ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $cm->materials_sent ? 'Sent' : 'Not Sent' }}</span>
                                </td>
                                <td class="py-2 pr-3 text-gray-600">{{ $cm->patients_count }}</td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cm->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $cm->status }}</span>
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-2">
                                        <a href="{{ route('companies.case-managers.show', [$company, $cm]) }}" class="text-sm text-[#0092b4] hover:underline">View</a>
                                        <button onclick="openEditCmModal({{ $cm->id }}, '{{ $cm->first_name }}', '{{ $cm->last_name }}', '{{ $cm->email }}')" class="text-sm text-amber-600 hover:underline">Edit</button>
                                        <form method="POST" action="{{ route('companies.case-managers.destroy', [$company, $cm]) }}" onsubmit="return confirm('Remove {{ $cm->first_name }} {{ $cm->last_name }}?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="py-4 text-center text-gray-500">No case managers assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">All Patients</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                                <th class="pb-2 pr-3">Patient Name</th>
                                <th class="pb-2 pr-3">Case Manager</th>
                                <th class="pb-2 pr-3">Status</th>
                                <th class="pb-2 pr-3">Referral Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $colors = ['New'=>'bg-blue-100 text-blue-700','In Progress'=>'bg-amber-100 text-amber-700','Converted'=>'bg-green-100 text-green-700','Not Proceeding'=>'bg-gray-100 text-gray-600','Needs Review'=>'bg-red-100 text-red-700']; @endphp
                            @forelse($patients as $patient)
                            <tr class="even:bg-gray-50">
                                <td class="py-2 pr-3 font-medium text-gray-800">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-[#0092b4] hover:underline">{{ $patient->first_name }} {{ $patient->last_name }}</a>
                                </td>
                                <td class="py-2 pr-3 text-gray-600">{{ $patient->caseManager?->first_name }} {{ $patient->caseManager?->last_name }}</td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$patient->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->status }}</span>
                                </td>
                                <td class="py-2 pr-3 text-gray-600">{{ $patient->referral_date?->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">No patients linked.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enquiries</h3>
                <div class="space-y-2 text-sm">
                    @forelse($company->enquiries as $enq)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="font-medium text-gray-800">{{ $enq->enquirer_name }}</p>
                            <p class="text-gray-500">{{ $enq->enquiry_date?->format('d/m/Y') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $enq->status === 'Converted' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $enq->status }}</span>
                    </div>
                    @empty
                    <p class="py-4 text-center text-gray-500">No enquiries.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Add Case Manager Modal -->
<div id="addCmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Add Case Manager</h3>
            <button onclick="closeAddCmModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('case-managers.quick-add') }}">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700">First Name</label><input type="text" name="first_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700">Last Name</label><input type="text" name="last_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeAddCmModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Case Manager Modal -->
<div id="editCmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Edit Case Manager</h3>
            <button onclick="closeEditCmModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="">
            @csrf @method('PUT')
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700">First Name</label><input type="text" id="editCmFirstName" name="first_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700">Last Name</label><input type="text" id="editCmLastName" name="last_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700">Email</label><input type="email" id="editCmEmail" name="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditCmModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddCmModal() {
        document.getElementById('addCmModal').classList.remove('hidden');
        document.getElementById('addCmModal').classList.add('flex');
    }
    function closeAddCmModal() {
        document.getElementById('addCmModal').classList.add('hidden');
        document.getElementById('addCmModal').classList.remove('flex');
    }
    function openEditCmModal(id, firstName, lastName, email) {
        document.getElementById('editCmFirstName').value = firstName;
        document.getElementById('editCmLastName').value = lastName;
        document.getElementById('editCmEmail').value = email;
        document.querySelector('#editCmModal form').action = '/companies/{{ $company->id }}/case-managers/' + id;
        document.getElementById('editCmModal').classList.remove('hidden');
        document.getElementById('editCmModal').classList.add('flex');
    }
    function closeEditCmModal() {
        document.getElementById('editCmModal').classList.add('hidden');
        document.getElementById('editCmModal').classList.remove('flex');
    }
    document.addEventListener('click', function(e) {
        if (e.target.id === 'addCmModal') closeAddCmModal();
        if (e.target.id === 'editCmModal') closeEditCmModal();
    });
</script>
