<x-app-layout>
    <x-slot name="header">Log New Enquiry</x-slot>

    <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('enquiries.store') }}">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="enquiry_ref" class="block text-sm font-medium text-gray-700">Enquiry ID</label>
                    <input type="text" id="enquiry_ref" name="enquiry_ref" value="{{ old('enquiry_ref') }}" placeholder="e.g. E001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('enquiry_ref') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
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
                                <option value="{{ $cm->id }}" data-email="{{ $cm->email }}" data-phone="{{ $cm->phone }}" @selected(old('case_manager_id') == $cm->id)>{{ $cm->first_name }} {{ $cm->last_name }}</option>
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
            </div>

            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Enquiry</label>
                <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('reason') }}</textarea>
                @error('reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="client_location" class="block text-sm font-medium text-gray-700">Client's Location</label>
                    <input type="text" id="client_location" name="client_location" value="{{ old('client_location') }}" placeholder="e.g. Manchester" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('client_location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nearest_associate_id" class="block text-sm font-medium text-gray-700">Nearest Associate</label>
                    <select id="nearest_associate_id" name="nearest_associate_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="">-- Select Associate --</option>
                        @foreach($associates as $assoc)
                            <option value="{{ $assoc->id }}" @selected(old('nearest_associate_id') == $assoc->id)>{{ $assoc->name }}@if($assoc->region) ({{ $assoc->region }})@endif</option>
                        @endforeach
                    </select>
                    @error('nearest_associate_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="first_response_date" class="block text-sm font-medium text-gray-700">First Response Date</label>
                    <input type="date" id="first_response_date_2" name="first_response_date" value="{{ old('first_response_date') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <div>
                    <label for="first_response_remarks" class="block text-sm font-medium text-gray-700">Response Remarks</label>
                    <input type="text" id="first_response_remarks" name="first_response_remarks" value="{{ old('first_response_remarks') }}" placeholder="Notes on first response…" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('first_response_remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Language needs, access requirements, availability, urgent notes…" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6" x-data="{ approved: '{{ old('initial_assessment_approved', '') }}' }">
                <label class="block text-sm font-medium text-gray-700">Approved for Initial Assessment</label>
                <div class="mt-2 flex gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="radio" name="initial_assessment_approved" value="1" x-model="approved" class="text-[#0092b4] focus:ring-[#0092b4]"> Yes
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="radio" name="initial_assessment_approved" value="0" x-model="approved" class="text-[#0092b4] focus:ring-[#0092b4]"> No
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                        <input type="radio" name="initial_assessment_approved" value="" x-model="approved" class="focus:ring-[#0092b4]"> Not set
                    </label>
                </div>
                <div x-show="approved === '0'" x-cloak class="mt-2">
                    <label class="block text-sm font-medium text-gray-700">Reason (Not Approved)</label>
                    <textarea name="initial_assessment_reason" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ old('initial_assessment_reason') }}</textarea>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6" x-data="{ contacts: [] }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Contacts <span class="text-sm font-normal text-gray-400">(optional)</span></h3>
                </div>
                <template x-for="(contact, index) in contacts" :key="index">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Name</label>
                            <input type="text" x-model="contact.name" :name="'contacts['+index+'][name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Role</label>
                            <select x-model="contact.role" :name="'contacts['+index+'][role]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="Case Manager">Case Manager</option>
                                <option value="Health Professional">Health Professional</option>
                                <option value="Line Manager">Line Manager</option>
                                <option value="Solicitor">Solicitor</option>
                                <option value="Insurer">Insurer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Email</label>
                            <input type="email" x-model="contact.email" :name="'contacts['+index+'][email]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Phone</label>
                            <input type="text" x-model="contact.phone" :name="'contacts['+index+'][phone]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <button type="button" @click="contacts.splice(index, 1)" class="mt-2 text-xs text-red-600 hover:underline">Remove</button>
                        </div>
                    </div>
                </template>
                <button type="button" @click="contacts.push({ name: '', role: 'Case Manager', email: '', phone: '' })" class="text-sm text-[#0092b4] hover:underline">
                    <i class="fa-solid fa-plus mr-1"></i> Add Contact
                </button>
            </div>

            {{-- Communications --}}
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6" x-data="{ comms: [] }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Communications <span class="text-sm font-normal text-gray-400">(optional)</span></h3>
                </div>
                <template x-for="(comm, index) in comms" :key="index">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Type</label>
                            <select x-model="comm.type" :name="'communications['+index+'][type]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="Email">Email</option>
                                <option value="Phone">Phone</option>
                                <option value="Letter">Letter</option>
                                <option value="Meeting">Meeting</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Direction</label>
                            <select x-model="comm.direction" :name="'communications['+index+'][direction]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                <option value="Inbound">Inbound</option>
                                <option value="Outbound">Outbound</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Subject</label>
                            <input type="text" x-model="comm.subject" :name="'communications['+index+'][subject]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Date</label>
                            <input type="date" x-model="comm.communication_date" :name="'communications['+index+'][communication_date]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500">Summary</label>
                            <textarea x-model="comm.summary" :name="'communications['+index+'][summary]'" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                        </div>
                        <div class="md:col-span-2 text-right">
                            <button type="button" @click="comms.splice(index, 1)" class="text-xs text-red-600 hover:underline">Remove</button>
                        </div>
                    </div>
                </template>
                <button type="button" @click="comms.push({ type: 'Email', direction: 'Inbound', subject: '', summary: '', communication_date: '' })" class="text-sm text-[#0092b4] hover:underline">
                    <i class="fa-solid fa-plus mr-1"></i> Add Communication
                </button>
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
                    opt.textContent = data.name;
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

        document.getElementById('case_manager_id')?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const email = opt.dataset.email || '';
            const phone = opt.dataset.phone || '';
            const emailField = document.getElementById('email');
            const phoneField = document.getElementById('phone');
            if (emailField && !emailField.value) emailField.value = email;
            if (phoneField && !phoneField.value) phoneField.value = phone;
        });
    </script>
    @endpush
</x-app-layout>
