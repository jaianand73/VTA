<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">New Referral</h2>
            <p class="text-sm text-gray-500 mt-0.5">Stage 2 — Patient identity known, case manager identified</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('referrals.store') }}" class="space-y-6 max-w-4xl">
        @csrf

        @if($enquiry)
            <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
                <i class="fa-solid fa-link mr-2"></i>
                Promoting from Enquiry: <strong>{{ $enquiry->enquiry_ref ?? '#'.$enquiry->id }}</strong> — {{ $enquiry->enquirer_name }}
            </div>
        @endif

        {{-- Referral ID --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Reference</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referral ID <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="referral_ref" value="{{ old('referral_ref', $enquiry?->enquiry_ref) }}" placeholder="e.g. E001"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('referral_ref')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Patient Identity --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Patient Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="patient_first_name" value="{{ old('patient_first_name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('patient_first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="patient_last_name" value="{{ old('patient_last_name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    @error('patient_last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="patient_dob" value="{{ old('patient_dob') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input type="text" name="patient_postcode" value="{{ old('patient_postcode') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="patient_address" rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('patient_address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="patient_phone" value="{{ old('patient_phone') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="patient_email" value="{{ old('patient_email') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                </div>
            </div>
        </div>

        {{-- Case Management --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Case Management</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                    <select name="company_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="">— Select company —</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Case Manager</label>
                    <select name="case_manager_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <option value="">— Select case manager —</option>
                        @foreach($caseManagers as $cm)
                            <option value="{{ $cm->id }}" @selected(old('case_manager_id') == $cm->id)>
                                {{ $cm->first_name }} {{ $cm->last_name }} — {{ $cm->company?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Special Instructions --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Special Instructions</h3>
            <p class="text-xs text-gray-500">Language preferences, availability windows, access requirements, communication preferences, etc.</p>
            <textarea name="special_instructions" rows="4" placeholder="e.g. Speaks Urdu — interpreter needed. Only available Mon–Wed 10am–3pm. Ground floor access required."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('special_instructions') }}</textarea>
        </div>

        {{-- Notes --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Notes</h3>
            <textarea name="notes" rows="3"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded-lg px-6 py-2 text-sm font-semibold text-white" style="background:#0092b4;">
                Create Referral
            </button>
            <a href="{{ route('referrals.index') }}" class="rounded-lg px-6 py-2 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</x-app-layout>
