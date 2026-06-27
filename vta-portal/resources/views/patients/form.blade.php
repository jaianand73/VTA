@props(['patient' => null])

<form method="POST" action="{{ $patient ? route('patients.update', $patient) : route('patients.store') }}">
    @csrf
    @if($patient) @method('PUT') @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="case_manager_id" class="block text-sm font-medium text-gray-700">Case Manager</label>
            <select id="case_manager_id" name="case_manager_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">Select Case Manager</option>
                @foreach(\App\Models\CaseManager::all() as $cm)
                <option value="{{ $cm->id }}" @selected(old('case_manager_id', $patient?->case_manager_id) == $cm->id)>{{ $cm->first_name }} {{ $cm->last_name }}</option>
                @endforeach
            </select>
            @error('case_manager_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $patient?->first_name) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $patient?->last_name) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient?->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('date_of_birth') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" id="location" name="location" value="{{ old('location', $patient?->location) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
            <input type="text" id="condition" name="condition" value="{{ old('condition', $patient?->condition) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('condition') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="referral_date" class="block text-sm font-medium text-gray-700">Referral Date</label>
            <input type="date" id="referral_date" name="referral_date" value="{{ old('referral_date', $patient?->referral_date?->format('Y-m-d') ?? date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('referral_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="first_contact_date" class="block text-sm font-medium text-gray-700">First Contact Date</label>
            <input type="date" id="first_contact_date" name="first_contact_date" value="{{ old('first_contact_date', $patient?->first_contact_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('first_contact_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="invoice_recipient_type" class="block text-sm font-medium text-gray-700">Invoice Recipient Type</label>
            <select id="invoice_recipient_type" name="invoice_recipient_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">Select Type</option>
                <option value="Case Manager Company" @selected(old('invoice_recipient_type', $patient?->invoice_recipient_type) === 'Case Manager Company')>Case Manager Company</option>
                <option value="Solicitor" @selected(old('invoice_recipient_type', $patient?->invoice_recipient_type) === 'Solicitor')>Solicitor</option>
                <option value="Insurance Company" @selected(old('invoice_recipient_type', $patient?->invoice_recipient_type) === 'Insurance Company')>Insurance Company</option>
                <option value="Other" @selected(old('invoice_recipient_type', $patient?->invoice_recipient_type) === 'Other')>Other</option>
            </select>
            @error('invoice_recipient_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="invoice_recipient_name" class="block text-sm font-medium text-gray-700">Recipient Name</label>
            <input type="text" id="invoice_recipient_name" name="invoice_recipient_name" value="{{ old('invoice_recipient_name', $patient?->invoice_recipient_name) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('invoice_recipient_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="invoice_recipient_email" class="block text-sm font-medium text-gray-700">Recipient Email</label>
            <input type="email" id="invoice_recipient_email" name="invoice_recipient_email" value="{{ old('invoice_recipient_email', $patient?->invoice_recipient_email) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('invoice_recipient_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="invoice_recipient_address" class="block text-sm font-medium text-gray-700">Recipient Address</label>
            <input type="text" id="invoice_recipient_address" name="invoice_recipient_address" value="{{ old('invoice_recipient_address', $patient?->invoice_recipient_address) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
            @error('invoice_recipient_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="assigned_staff_id" class="block text-sm font-medium text-gray-700">Assigned Staff</label>
            <select id="assigned_staff_id" name="assigned_staff_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                <option value="">Unassigned</option>
                @foreach(\App\Models\User::whereIn('role', ['admin', 'staff'])->get() as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_staff_id', $patient?->assigned_staff_id) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('assigned_staff_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('notes', $patient?->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('patients.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
            {{ $patient ? 'Update Patient' : 'Create Patient' }}
        </button>
    </div>
</form>
