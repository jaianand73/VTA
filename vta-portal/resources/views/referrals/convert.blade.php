<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Convert to Patient</h2>
            <p class="text-sm text-gray-500 mt-0.5">Creating patient record from approved referral</p>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-5">

        {{-- Referral summary --}}
        <div class="rounded-xl border border-teal-200 bg-teal-50 px-5 py-4 text-sm text-teal-800">
            <p class="font-semibold">Referral: {{ $referral->referral_ref ?? '#'.$referral->id }} — {{ $referral->patient_full_name }}</p>
            <p class="mt-0.5 text-teal-700">Status: Approved. A patient record will be created with the details below.</p>
            @if($referral->associate)
            <p class="mt-2 text-teal-700"><span class="font-medium">Assessment carried out by:</span> {{ $referral->associate->name }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('referrals.storePatient', $referral) }}" class="space-y-5">
            @csrf

            {{-- Patient details pre-filled from referral --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Patient Details</h3>
                <p class="text-xs text-gray-500">Pre-filled from the referral. Correct anything before creating.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $referral->patient_first_name) }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $referral->patient_last_name) }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob', $referral->patient_dob?->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                        <input type="text" name="postcode" value="{{ old('postcode', $referral->patient_postcode) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="2"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">{{ old('address', $referral->patient_address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $referral->patient_phone) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $referral->patient_email) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                </div>
            </div>

            {{-- Patient reference --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Patient Reference</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient Ref</label>
                        <input type="text" name="patient_ref" value="{{ old('patient_ref', $referral->referral_ref) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        <p class="mt-1 text-xs text-gray-400">Carried over from the referral ref by default.</p>
                    </div>
                </div>
            </div>

            {{-- Associate for treatment --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-blue-800 text-sm uppercase tracking-wide">
                    <i class="fa-solid fa-user-doctor mr-2"></i>Associate for Treatment
                </h3>
                @if($referral->associate)
                <div class="rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-gray-700">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Assessment carried out by</p>
                    <p class="font-semibold text-gray-900">{{ $referral->associate->name }}</p>
                </div>
                <p class="text-sm text-blue-700 font-medium">Who will lead the treatment?</p>
                @endif
                <select name="associate_id" class="w-full rounded-lg border border-blue-300 px-3 py-2 text-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    <option value="">— No associate assigned —</option>
                    @foreach($associates as $a)
                        <option value="{{ $a->id }}"
                            @selected(old('associate_id', $referral->associate_id) == $a->id)>
                            {{ $a->name }}
                            @if($a->id == $referral->associate_id) (assessment associate) @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-blue-600">Pre-selected to the assessment associate. Change if a different associate will lead treatment.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg px-6 py-2 text-sm font-semibold text-white" style="background:#0f766e;">
                    <i class="fa-solid fa-user-plus mr-2"></i>Create Patient Record
                </button>
                <a href="{{ route('referrals.show', $referral) }}" class="rounded-lg px-6 py-2 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
