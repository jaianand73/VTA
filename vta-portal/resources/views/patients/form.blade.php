@props(['patient' => null, 'enquiry' => null])

<form method="POST" action="{{ $patient ? route('patients.update', $patient) : route('patients.store') }}" enctype="multipart/form-data">
    @csrf
    @if($patient) @method('PUT') @endif


    <div class="grid gap-6 sm:grid-cols-2">
        @php
            $enquiryNameParts = $enquiry ? explode(' ', trim($enquiry->enquirer_name ?? ''), 2) : [];
            $enquiryFirstName = $enquiryNameParts[0] ?? '';
            $enquiryLastName  = $enquiryNameParts[1] ?? '';
        @endphp
        <div class="sm:col-span-2">
            <label for="patient_ref" class="block text-sm font-medium text-gray-700">Patient ID</label>
            <input type="text" id="patient_ref" name="patient_ref" value="{{ old('patient_ref', $patient?->patient_ref) }}" placeholder="e.g. P001" class="mt-1 block w-48 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            <p class="mt-1 text-xs text-gray-400">Assigned manually by admin. Leave blank if not yet allocated.</p>
            @error('patient_ref') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $patient?->first_name ?? $enquiryFirstName) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $patient?->last_name ?? $enquiryLastName) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $patient?->email ?? $enquiry?->email) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $patient?->phone ?? $enquiry?->phone) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
            <input type="text" id="address_line_1" name="address_line_1" value="{{ old('address_line_1', $patient?->address_line_1) }}" placeholder="House number and street name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('address_line_1') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2 <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" id="address_line_2" name="address_line_2" value="{{ old('address_line_2', $patient?->address_line_2) }}" placeholder="Apartment, suite, unit, etc." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">City / Town</label>
            <input type="text" id="city" name="city" value="{{ old('city', $patient?->city) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="postcode" class="block text-sm font-medium text-gray-700">Postcode</label>
            <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $patient?->postcode) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('postcode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient?->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('date_of_birth') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="condition" class="block text-sm font-medium text-gray-700">Diagnosis / Condition</label>
            <input type="text" id="condition" name="condition" value="{{ old('condition', $patient?->condition) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('condition') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="reason_for_referral" class="block text-sm font-medium text-gray-700">Reason for Referral</label>
            <select id="reason_for_referral" name="reason_for_referral" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                <option value="">— Select (optional) —</option>
                @foreach(['Return to Work','Expert Witness','VTA Treatment','Case Management','Other'] as $opt)
                <option value="{{ $opt }}" @selected(old('reason_for_referral', $patient?->reason_for_referral) === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="referral_date" class="block text-sm font-medium text-gray-700">Referral Date</label>
            <input type="date" id="referral_date" name="referral_date" value="{{ old('referral_date', $patient?->referral_date?->format('Y-m-d') ?? $enquiry?->created_at?->toDateString() ?? date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('referral_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="first_contact_date" class="block text-sm font-medium text-gray-700">First Contact Date</label>
            <input type="date" id="first_contact_date" name="first_contact_date" value="{{ old('first_contact_date', $patient?->first_contact_date?->format('Y-m-d') ?? $enquiry?->first_response_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('first_contact_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Staff Assignment (create only — edit / NOK / Referrers / Accounts are on the show page) ── --}}
    @if(!$patient)
    <div class="mt-8 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-user-check text-[#0092b4]"></i> Staff Assignment
        </h4>
        <p class="text-xs text-gray-400 mb-4">The assigned staff member will see this patient in their dashboard when they log in.</p>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <label for="assigned_staff_id" class="block text-sm font-medium text-gray-700 mb-1">Assign to Staff Member</label>
            <select id="assigned_staff_id" name="assigned_staff_id"
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">— Unassigned —</option>
                @foreach(\App\Models\User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get() as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_staff_id') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('assigned_staff_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    @endif

    <div class="mt-6">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $patient?->notes ?? $enquiry?->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- On create, also show Referrers and Accounts sections --}}
    @if(!$patient)
    <div class="mt-8 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-1">Referrers</h4>
        <p class="text-xs text-gray-400 mb-4">The mapped case manager is the primary referrer. Add any additional referrers below.</p>
        <div class="rounded-lg border border-[#0092b4]/30 bg-[#0092b4]/5 p-4 mb-4">
            <p class="text-xs font-semibold text-[#0092b4] uppercase tracking-wide mb-2">
                <i class="fa-solid fa-user-tie mr-1"></i> Mapped Case Manager
            </p>
            <select id="case_manager_id" name="case_manager_id" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">— Select Case Manager —</option>
                @foreach(\App\Models\CaseManager::with('company')->orderBy('first_name')->get() as $cm)
                <option value="{{ $cm->id }}" @selected(old('case_manager_id', $patient?->case_manager_id) == $cm->id)>
                    {{ $cm->first_name }} {{ $cm->last_name }}{{ $cm->company ? ' — ' . $cm->company->name : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div x-data="{ referrers: [] }">
            <template x-for="(referrer, index) in referrers" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Name</label>
                        <input type="text" x-model="referrer.name" :name="'referrers['+index+'][name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Role</label>
                        <select x-model="referrer.role" :name="'referrers['+index+'][role]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">— Select —</option>
                            <option value="Case Manager">Case Manager</option>
                            <option value="Deputy">Deputy</option>
                            <option value="Solicitor">Solicitor</option>
                            <option value="Insurer">Insurer</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Company Name</label>
                        <input type="text" x-model="referrer.company_name" :name="'referrers['+index+'][company_name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Email</label>
                        <input type="email" x-model="referrer.email" :name="'referrers['+index+'][email]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Phone</label>
                        <input type="text" x-model="referrer.phone" :name="'referrers['+index+'][phone]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Special Instructions</label>
                        <textarea x-model="referrer.special_instructions" :name="'referrers['+index+'][special_instructions]'" rows="1" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="referrers.splice(index, 1)" class="text-xs text-red-600 hover:underline"><i class="fa-solid fa-trash mr-1"></i> Remove</button>
                    </div>
                </div>
            </template>
            <button type="button" @click="referrers.push({ name: '', role: '', company_name: '', address: '', email: '', phone: '', special_instructions: '' })" class="text-sm text-[#0092b4] hover:underline mt-1">
                <i class="fa-solid fa-plus mr-1"></i> Add Referrer
            </button>
        </div>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-sterling-sign text-[#0092b4]"></i> Accounts / Financial
        </h4>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Fee Agreed for Assessment (£)</label>
                <input type="number" step="0.01" min="0" name="fee_agreed_amount" value="{{ old('fee_agreed_amount') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Invoice Recipient Type</label>
                <select name="invoice_recipient_type" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                    <option value="">Select Type</option>
                    <option value="Case Manager Company">Case Manager Company</option>
                    <option value="Solicitor">Solicitor</option>
                    <option value="Insurance Company">Insurance Company</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Recipient Name</label>
                <input type="text" name="invoice_recipient_name" value="{{ old('invoice_recipient_name') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Recipient Email</label>
                <input type="email" name="invoice_recipient_email" value="{{ old('invoice_recipient_email') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            </div>
        </div>
    </div>
    @endif

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('patients.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
            {{ $patient ? 'Update Patient' : 'Create Patient' }}
        </button>
    </div>
</form>
