<x-app-layout>
    <x-slot name="header">Log New Enquiry</x-slot>

    <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('enquiries.store') }}">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="enquirer_name" class="block text-sm font-medium text-gray-700">Enquirer Name <span class="text-red-500">*</span></label>
                    <input type="text" id="enquirer_name" name="enquirer_name" value="{{ old('enquirer_name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('enquirer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700">Company <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex gap-2">
                        <select id="company_id" name="company_id" required class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">-- Select Company --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="document.getElementById('quickAddModal').classList.remove('hidden')" class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                            <i class="fa-solid fa-plus text-xs"></i> Add New
                        </button>
                    </div>
                    @error('company_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="case_manager_id" class="block text-sm font-medium text-gray-700">Case Manager</label>
                    <div class="mt-1 flex gap-2">
                        <select id="case_manager_id" name="case_manager_id" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">-- Select (optional) --</option>
                            @foreach($caseManagers as $cm)
                                <option value="{{ $cm->id }}" @selected(old('case_manager_id') == $cm->id)>{{ $cm->first_name }} {{ $cm->last_name }} ({{ $cm->company?->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="document.getElementById('cmQuickAddModal').classList.remove('hidden')" class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                            <i class="fa-solid fa-plus text-xs"></i> Add New
                        </button>
                    </div>
                    @error('case_manager_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
                    <select id="source" name="source" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="Website" @selected(old('source', 'Website') === 'Website')>Website</option>
                        <option value="Referral Letter" @selected(old('source') === 'Referral Letter')>Referral Letter</option>
                        <option value="Phone" @selected(old('source') === 'Phone')>Phone</option>
                        <option value="Email" @selected(old('source') === 'Email')>Email</option>
                        <option value="LinkedIn" @selected(old('source') === 'LinkedIn')>LinkedIn</option>
                        <option value="Word of Mouth" @selected(old('source') === 'Word of Mouth')>Word of Mouth</option>
                        <option value="Other" @selected(old('source') === 'Other')>Other</option>
                    </select>
                    @error('source') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="enquiry_date" class="block text-sm font-medium text-gray-700">Enquiry Date</label>
                    <input type="date" id="enquiry_date" name="enquiry_date" value="{{ old('enquiry_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('enquiry_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="first_response_date" class="block text-sm font-medium text-gray-700">First Response Date</label>
                    <input type="date" id="first_response_date" name="first_response_date" value="{{ old('first_response_date') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('first_response_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('reason') }}</textarea>
                @error('reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('enquiries.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Save Enquiry</button>
            </div>
        </form>
    </div>

    <div id="quickAddModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Quick Add Company</h3>
                <button type="button" onclick="document.getElementById('quickAddModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="quickAddForm" method="POST" action="{{ route('settings.companies.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Case Management">Case Management</option>
                            <option value="Law Firm">Law Firm</option>
                            <option value="Solicitor">Solicitor</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Individual">Individual</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('quickAddModal').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Add Company</button>
                </div>
            </form>
        </div>
    </div>

    <div id="cmQuickAddModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Quick Add Case Manager</h3>
                <button type="button" onclick="document.getElementById('cmQuickAddModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="cmQuickAddForm" method="POST" action="{{ route('case-managers.quick-add') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company <span class="text-red-500">*</span></label>
                        <select id="cm_company_id" name="company_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">-- Select Company --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('cmQuickAddModal').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Add Case Manager</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('quickAddForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.id) {
                    ['company_id', 'cm_company_id'].forEach(id => {
                        const select = document.getElementById(id);
                        if (select) {
                            const opt = document.createElement('option');
                            opt.value = data.id;
                            opt.textContent = data.name;
                            select.appendChild(opt);
                        }
                    });
                    document.getElementById('company_id').value = data.id;
                    document.getElementById('quickAddModal').classList.add('hidden');
                    form.reset();
                }
            })
            .catch(() => {
                alert('Failed to save. Please try again or refresh the page.');
            });
        });

        document.getElementById('cmQuickAddForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.id) {
                    const select = document.getElementById('case_manager_id');
                    const opt = document.createElement('option');
                    opt.value = data.id;
                    opt.textContent = data.name + ' (' + (data.company_name || 'N/A') + ')';
                    opt.selected = true;
                    select.appendChild(opt);
                    document.getElementById('cmQuickAddModal').classList.add('hidden');
                    form.reset();
                }
            })
            .catch(() => {
                alert('Failed to save. Please try again or refresh the page.');
            });
        });
    </script>
    @endpush
</x-app-layout>
