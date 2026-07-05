<x-app-layout>
    <x-slot name="header">{{ $patient->first_name }} {{ $patient->last_name }}</x-slot>

    @php
    $quickActions = [
        'LOI Received' => ['label' => 'Add Assessment', 'url' => '#assessment-section', 'icon' => 'fa-file-pen'],
        'Assessment Completed' => ['label' => 'Mark Report Drafted', 'action' => 'status', 'status' => 'Report Drafted', 'icon' => 'fa-file-lines'],
        'Report Sent' => ['label' => 'Create Cost Estimation', 'url' => route('cost-estimations.create', ['patient_id' => $patient->id]), 'icon' => 'fa-calculator'],
        'Cost Estimation Sent' => ['label' => 'Add Funding Cycle', 'url' => route('funding-cycles.create', ['patient_id' => $patient->id]), 'icon' => 'fa-coins'],
        'Awaiting Funding Approval' => ['label' => 'Add Funding Cycle', 'url' => route('funding-cycles.create', ['patient_id' => $patient->id]), 'icon' => 'fa-coins'],
        'Funding Approved' => ['label' => 'Mark Treatment Active', 'action' => 'status', 'status' => 'Treatment Active', 'icon' => 'fa-play'],
    ];
    @endphp
    @if(isset($quickActions[$patient->status]))
    @php $action = $quickActions[$patient->status]; @endphp
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-amber-100 p-2 text-amber-700">
                    <i class="fa-solid {{ $action['icon'] }}"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-amber-900">Next Step</p>
                    <p class="text-xs text-amber-700">{{ $action['label'] }}</p>
                </div>
            </div>
            @if(isset($action['url']))
            <a href="{{ $action['url'] }}" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                {{ $action['label'] }}
            </a>
            @elseif(isset($action['action']) && $action['action'] === 'status')
            <form method="POST" action="{{ route('patients.status', $patient) }}" data-swal="Change patient status to {{ $action['status'] }}?">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ $action['status'] }}">
                <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    {{ $action['label'] }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif

    @if($patient->clinical_alert)
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="rounded-lg bg-red-100 p-2 text-red-700 shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-900">Clinical Alert</p>
                <p class="text-sm text-red-800 mt-0.5">{{ $patient->clinical_alert }}</p>
            </div>
            <form method="POST" action="{{ route('patients.clinical-alert', $patient) }}" class="shrink-0">
                @csrf @method('PATCH')
                <input type="hidden" name="clinical_alert" value="">
                <button type="submit" class="text-xs text-red-400 hover:text-red-600" title="Clear alert">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Patient Information</h3>
                    <a href="{{ route('patients.edit', $patient) }}" class="text-sm text-[#0092b4] hover:underline">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </a>
                </div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Patient ID</dt><dd class="font-medium text-gray-800">{{ $patient->patient_ref ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Full Name</dt><dd class="font-medium text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</dd></div>
                    <div><dt class="text-gray-500">Date of Birth</dt><dd class="font-medium text-gray-800">{{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $patient->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $patient->phone ?? '—' }}</dd></div>
                    @if($patient->address_line_1 || $patient->city || $patient->postcode)
                    <div class="col-span-2">
                        <dt class="text-gray-500">Address</dt>
                        <dd class="font-medium text-gray-800">
                            @if($patient->address_line_1)<div>{{ $patient->address_line_1 }}</div>@endif
                            @if($patient->address_line_2)<div>{{ $patient->address_line_2 }}</div>@endif
                            @if($patient->city || $patient->postcode)<div>{{ trim($patient->city . ' ' . $patient->postcode) }}</div>@endif
                        </dd>
                    </div>
                    @elseif($patient->address)
                    <div class="col-span-2"><dt class="text-gray-500">Address</dt><dd class="font-medium text-gray-800">{{ $patient->address }}</dd></div>
                    @endif
                    <div><dt class="text-gray-500">Diagnosis / Condition</dt><dd class="font-medium text-gray-800">{{ $patient->condition ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Referral Date</dt><dd class="font-medium text-gray-800">{{ $patient->referral_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">First Contact</dt><dd class="font-medium text-gray-800">{{ $patient->first_contact_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    @if($patient->reason_for_referral)
                    <div class="col-span-2"><dt class="text-gray-500">Reason for Referral</dt><dd class="font-medium text-gray-800">{{ $patient->reason_for_referral }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Clinical Alert Edit --}}
            <div class="rounded-lg border border-red-100 bg-white p-5" x-data="{ editing: false }">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-sm"></i>
                        <h3 class="text-sm font-semibold text-gray-800">Clinical Alert / Special Instructions</h3>
                    </div>
                    <button type="button" @click="editing = !editing" class="text-xs text-[#0092b4] hover:underline">
                        <span x-text="editing ? 'Cancel' : ({{ $patient->clinical_alert ? 'true' : 'false' }} ? 'Edit' : 'Add Alert')"></span>
                    </button>
                </div>
                <div x-show="!editing">
                    @if($patient->clinical_alert)
                    <p class="text-sm text-red-800 bg-red-50 rounded-md px-3 py-2">{{ $patient->clinical_alert }}</p>
                    @else
                    <p class="text-sm text-gray-400">No clinical alert set.</p>
                    @endif
                </div>
                <div x-show="editing" x-cloak>
                    <form method="POST" action="{{ route('patients.clinical-alert', $patient) }}">
                        @csrf @method('PATCH')
                        <textarea name="clinical_alert" rows="2" placeholder="e.g. Patient needs carer to communicate. Requires interpreter." class="block w-full rounded-md border border-red-200 px-3 py-2 text-sm shadow-sm focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400">{{ $patient->clinical_alert }}</textarea>
                        <div class="mt-2 flex justify-end gap-2">
                            <button type="button" @click="editing = false" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs text-white" style="background-color:#dc2626;">Save Alert</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ STAFF ASSIGNMENT ══ --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6" x-data="{ confirming: false, selectedName: '' }">
                <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-[#0092b4]"></i> Staff Assignment
                </h4>
                <p class="text-xs text-gray-400 mb-4">The assigned staff member will see this patient in their dashboard when they log in. Changes are recorded in the audit log.</p>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Currently assigned to:</p>
                            <p class="text-base font-semibold mt-0.5" style="color:#0092b4;">{{ $patient->assignedStaff?->name ?? 'Unassigned' }}</p>
                        </div>
                        <button type="button" @click="confirming = !confirming" class="text-sm text-[#0092b4] hover:underline">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Change Staff
                        </button>
                    </div>
                    <div x-show="confirming" x-cloak class="mt-4 border-t border-gray-200 pt-4">
                        <form method="POST" action="{{ route('patients.assign-staff', $patient) }}" x-ref="staffForm">
                            @csrf @method('PATCH')
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select New Staff Member</label>
                            <select name="assigned_staff_id" @change="selectedName = $event.target.options[$event.target.selectedIndex].text" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">— Unassigned —</option>
                                @foreach(\App\Models\User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" @selected($patient->assigned_staff_id == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="mt-3 flex gap-2">
                                <button type="button"
                                        @click="Swal.fire({ title: 'Reassign Staff?', html: 'This patient will be assigned to <strong>' + (selectedName || 'Unassigned') + '</strong>.<br><small>This change will be recorded in the audit log.</small>', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Reassign', cancelButtonText: 'Cancel', confirmButtonColor: '#0092b4', reverseButtons: true }).then(result => { if (result.isConfirmed) $refs.staffForm.submit() })"
                                        class="rounded-lg px-4 py-2 text-sm text-white" style="background-color:#0092b4;">
                                    <i class="fa-solid fa-check mr-1"></i> Confirm Reassignment
                                </button>
                                <button type="button" @click="confirming = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ NEXT OF KIN ══ --}}
            @php
                $initialNok = $patient->nextOfKin->isNotEmpty()
                    ? $patient->nextOfKin->map(fn($n) => ['name' => $n->name, 'relationship' => $n->relationship, 'email' => $n->email, 'phone' => $n->phone])->values()->toArray()
                    : [['name' => '', 'relationship' => '', 'email' => '', 'phone' => '']];
            @endphp
            <div class="rounded-lg border border-gray-200 bg-white p-6" x-data="{ editing: false, nok: @js($initialNok) }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Next of Kin</h3>
                    <button type="button" @click="editing = !editing" class="text-sm text-[#0092b4] hover:underline">
                        <i class="fa-solid fa-pen mr-1"></i> <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                    </button>
                </div>

                {{-- Display mode --}}
                <div x-show="!editing">
                    @if($patient->nextOfKin->isEmpty())
                    <p class="text-sm text-gray-400">No next of kin recorded.</p>
                    @else
                    <div class="space-y-3">
                        @foreach($patient->nextOfKin as $nok)
                        <div class="rounded-md border border-gray-100 bg-gray-50 px-4 py-3 text-sm">
                            <p class="font-medium text-gray-800">{{ $nok->name }}@if($nok->relationship) <span class="font-normal text-gray-500">({{ $nok->relationship }})</span>@endif</p>
                            @if($nok->email || $nok->phone)
                            <p class="text-gray-500 mt-0.5">{{ $nok->email }}@if($nok->email && $nok->phone) &nbsp;·&nbsp; @endif{{ $nok->phone }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Edit mode --}}
                <div x-show="editing" x-cloak>
                    <form method="POST" action="{{ route('patients.update-nok', $patient) }}">
                        @csrf @method('PATCH')
                        <template x-for="(person, index) in nok" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Name</label>
                                    <input type="text" x-model="person.name" :name="'next_of_kin['+index+'][name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Relationship</label>
                                    <input type="text" x-model="person.relationship" :name="'next_of_kin['+index+'][relationship]'" placeholder="e.g. Spouse, Parent" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Email</label>
                                    <input type="email" x-model="person.email" :name="'next_of_kin['+index+'][email]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Phone</label>
                                    <input type="text" x-model="person.phone" :name="'next_of_kin['+index+'][phone]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                    <button type="button" @click="nok.splice(index, 1)" class="mt-1 text-xs text-red-600 hover:underline" x-show="nok.length > 1">Remove</button>
                                </div>
                            </div>
                        </template>
                        <div class="flex items-center justify-between mt-2">
                            <button type="button" @click="nok.push({ name: '', relationship: '', email: '', phone: '' })" class="text-sm text-[#0092b4] hover:underline">
                                <i class="fa-solid fa-plus mr-1"></i> Add Another
                            </button>
                            <button type="submit" class="rounded-lg px-4 py-2 text-sm text-white" style="background-color:#0092b4;">Save Next of Kin</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ REFERRERS ══ --}}
            @php
                $initialReferrers = $patient->referrers->map(fn($r) => [
                    'name' => $r->name, 'role' => $r->role, 'company_name' => $r->company_name,
                    'address' => $r->address, 'email' => $r->email, 'phone' => $r->phone,
                    'special_instructions' => $r->special_instructions,
                ])->values()->toArray();
            @endphp
            <div class="rounded-lg border border-gray-200 bg-white p-6" x-data="{ editing: false, referrers: @js($initialReferrers) }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Referrers</h3>
                    <button type="button" @click="editing = !editing" class="text-sm text-[#0092b4] hover:underline">
                        <i class="fa-solid fa-pen mr-1"></i> <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                    </button>
                </div>

                {{-- Display mode --}}
                <div x-show="!editing">
                    {{-- Mapped Case Manager --}}
                    <div class="rounded-lg border border-[#0092b4]/30 bg-[#0092b4]/5 p-3 mb-3">
                        <p class="text-xs font-semibold text-[#0092b4] uppercase tracking-wide mb-1"><i class="fa-solid fa-user-tie mr-1"></i> Mapped Case Manager</p>
                        @if($patient->caseManager)
                        <p class="text-sm font-medium text-gray-800">{{ $patient->caseManager->first_name }} {{ $patient->caseManager->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $patient->caseManager->company?->name }}</p>
                        @else
                        <p class="text-sm text-gray-400">None assigned.</p>
                        @endif
                    </div>
                    @if($patient->referrers->isEmpty())
                    <p class="text-sm text-gray-400">No additional referrers.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($patient->referrers as $ref)
                        <div class="rounded-md border border-gray-100 bg-gray-50 px-4 py-3 text-sm">
                            <p class="font-medium text-gray-800">{{ $ref->name }}@if($ref->role) <span class="text-gray-500 font-normal">({{ $ref->role }})</span>@endif@if($ref->company_name) &nbsp;— {{ $ref->company_name }}@endif</p>
                            @if($ref->email || $ref->phone)<p class="text-gray-500 text-xs mt-0.5">{{ $ref->email }}@if($ref->email && $ref->phone) &nbsp;·&nbsp; @endif{{ $ref->phone }}</p>@endif
                            @if($ref->special_instructions)<p class="text-xs text-amber-700 mt-0.5"><i class="fa-solid fa-circle-info mr-1"></i>{{ $ref->special_instructions }}</p>@endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Edit mode --}}
                <div x-show="editing" x-cloak>
                    <form method="POST" action="{{ route('patients.update-referrers', $patient) }}">
                        @csrf @method('PATCH')
                        {{-- Mapped Case Manager --}}
                        <div class="rounded-lg border border-[#0092b4]/30 bg-[#0092b4]/5 p-4 mb-4">
                            <p class="text-xs font-semibold text-[#0092b4] uppercase tracking-wide mb-2"><i class="fa-solid fa-user-tie mr-1"></i> Mapped Case Manager</p>
                            <select name="case_manager_id" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">— Select Case Manager —</option>
                                @foreach(\App\Models\CaseManager::with('company')->orderBy('first_name')->get() as $cm)
                                <option value="{{ $cm->id }}" @selected($patient->case_manager_id == $cm->id)>{{ $cm->first_name }} {{ $cm->last_name }}{{ $cm->company ? ' — ' . $cm->company->name : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Additional Referrers --}}
                        <template x-for="(referrer, index) in referrers" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div><label class="block text-xs font-medium text-gray-500">Name</label><input type="text" x-model="referrer.name" :name="'referrers['+index+'][name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Role</label>
                                    <select x-model="referrer.role" :name="'referrers['+index+'][role]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                        <option value="">— Select —</option>
                                        <option value="Case Manager">Case Manager</option>
                                        <option value="Deputy">Deputy</option>
                                        <option value="Solicitor">Solicitor</option>
                                        <option value="Insurer">Insurer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div><label class="block text-xs font-medium text-gray-500">Company Name</label><input type="text" x-model="referrer.company_name" :name="'referrers['+index+'][company_name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Email</label><input type="email" x-model="referrer.email" :name="'referrers['+index+'][email]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Phone</label><input type="text" x-model="referrer.phone" :name="'referrers['+index+'][phone]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Special Instructions</label><textarea x-model="referrer.special_instructions" :name="'referrers['+index+'][special_instructions]'" rows="1" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea></div>
                                <div class="flex items-end"><button type="button" @click="referrers.splice(index, 1)" class="text-xs text-red-600 hover:underline"><i class="fa-solid fa-trash mr-1"></i> Remove</button></div>
                            </div>
                        </template>
                        <div class="flex items-center justify-between mt-2">
                            <button type="button" @click="referrers.push({ name: '', role: '', company_name: '', address: '', email: '', phone: '', special_instructions: '' })" class="text-sm text-[#0092b4] hover:underline">
                                <i class="fa-solid fa-plus mr-1"></i> Add Referrer
                            </button>
                            <button type="submit" class="rounded-lg px-4 py-2 text-sm text-white" style="background-color:#0092b4;">Save Referrers</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ ASSOCIATES (above Current Status) ══ --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Associates</h3>
                <button type="button" onclick="document.getElementById('addAssociateForm').classList.toggle('hidden')" class="mb-4 rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-plus mr-1"></i> Add Associate
                </button>
                <form id="addAssociateForm" method="POST" action="{{ route('patients.associates', $patient) }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2" data-swal="Assign this associate to the patient?">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Associate</label>
                        <select name="associate_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Associate</option>
                            @foreach($associates as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                        @error('associate_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Role</label>
                        <select name="role" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Assessment">Assessment</option>
                            <option value="Treatment">Treatment</option>
                            <option value="Supervision">Supervision</option>
                            <option value="MDT">MDT</option>
                        </select>
                        @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="is_primary" value="1" id="is_primary" class="rounded border-gray-300">
                        <label for="is_primary" class="text-xs font-medium text-gray-500">Primary associate</label>
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addAssociateForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Save Associate</button>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                                <th class="pb-2 pr-3">Name</th>
                                <th class="pb-2 pr-3">Specialty</th>
                                <th class="pb-2 pr-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($patient->patientAssociates as $pa)
                            <tr class="even:bg-gray-50">
                                <td class="py-2 pr-3 font-medium text-gray-800">{{ $pa->associate?->name }}</td>
                                <td class="py-2 pr-3 text-gray-600">{{ $pa->associate?->speciality ?? '—' }}</td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $pa->is_primary ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $pa->is_primary ? 'Primary' : 'Secondary' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">No associates assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Current Status</h3>
                    @php
                        $statusColors = [
                            'Enquiry Logged' => 'bg-blue-100 text-blue-700',
                            'Response Sent' => 'bg-indigo-100 text-indigo-700',
                            'Awaiting LOI' => 'bg-amber-100 text-amber-700',
                            'LOI Received' => 'bg-green-100 text-green-700',
                            'Assessment Scheduled' => 'bg-purple-100 text-purple-700',
                            'Assessment Completed' => 'bg-teal-100 text-teal-700',
                            'Report Drafted' => 'bg-cyan-100 text-cyan-700',
                            'Report Sent' => 'bg-sky-100 text-sky-700',
                            'Cost Estimation Sent' => 'bg-orange-100 text-orange-700',
                            'Awaiting Funding Approval' => 'bg-amber-100 text-amber-700',
                            'Funding Approved' => 'bg-green-100 text-green-700',
                            'Treatment Active' => 'bg-emerald-100 text-emerald-700',
                            'Awaiting Further Funding' => 'bg-yellow-100 text-yellow-700',
                            'Under Treatment' => 'bg-blue-100 text-blue-700',
                            'Awaiting Refunding' => 'bg-orange-100 text-orange-700',
                            'Refunding Granted' => 'bg-green-100 text-green-700',
                            'Discharged' => 'bg-gray-100 text-gray-600',
                            'Case Closed' => 'bg-gray-200 text-gray-500',
                            'Closed' => 'bg-gray-300 text-gray-700',
                            'Not Proceeding' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$patient->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->status }}</span>
                </div>
                <form method="POST" action="{{ route('patients.status', $patient) }}" class="flex gap-3 mb-4" data-swal="Change patient status to the selected value?">
                    @csrf @method('PATCH')
                    <select name="status" class="block rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                        @foreach($allowedTransitions as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Update</button>
                </form>
            </div>

{{-- Assessment Section --}}
<div id="assessment-section" class="rounded-lg border border-gray-200 bg-white p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Assessment</h3>
        @if($patient->assessment)
        <a href="{{ route('assessments.edit', $patient->assessment) }}" class="rounded-lg bg-[#0092b4] px-3 py-1.5 text-xs text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-pen mr-1"></i> Edit
        </a>
        @endif
    </div>

    @if($patient->assessment)
    @php $a = $patient->assessment; @endphp
    <dl class="grid grid-cols-2 gap-3 text-sm">
        <div><dt class="text-gray-500">Fee Agreed</dt><dd class="font-medium">£{{ number_format($a->fee_agreed_amount, 2) ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Assessor</dt><dd class="font-medium">{{ $a->assessor ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Assessment Date</dt><dd class="font-medium">{{ $a->assessment_date?->format('d/m/Y') ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Assessment Cost</dt><dd class="font-medium">£{{ number_format($a->assessment_cost, 2) }} @if($a->vtaInvoice) <span class="text-xs text-green-600">(Invoiced)</span> @endif</dd></div>
        <div><dt class="text-gray-500">Report Sent</dt><dd class="font-medium">{{ $a->report_sent ? 'Yes' : 'No' }}</dd></div>
        <div><dt class="text-gray-500">Venue</dt><dd class="font-medium">{{ $a->venue ?? '—' }}</dd></div>
    </dl>
    @if($a->special_instructions)
    <div class="mt-3 text-sm"><dt class="text-gray-500">Special Instructions</dt><dd class="text-gray-700">{{ $a->special_instructions }}</dd></div>
    @endif
    @if($a->assessment_cost && !$a->vtaInvoice)
    <div class="mt-4 flex gap-2">
        <a href="{{ route('vta-invoices.create', ['patient_id' => $a->patient_id, 'assessment_id' => $a->id]) }}"
           class="inline-flex items-center gap-1 rounded-lg bg-[#0092b4] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#007a9a]">
            <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Create Assessment Invoice
        </a>
    </div>
    @endif
    @else
    <form method="POST" action="{{ route('assessments.store', $patient) }}" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-500">Fee Agreed (£)</label>
            <input type="number" name="fee_agreed_amount" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Fee Agreed Document</label>
            <input type="file" name="fee_agreed_document" class="mt-1 block w-full text-sm text-gray-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Date Client Contacted</label>
            <input type="date" name="date_client_contacted" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Assessor</label>
            <input type="text" name="assessor" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Venue</label>
            <input type="text" name="venue" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Assessment Date</label>
            <input type="date" name="assessment_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Assessment Cost (£)</label>
            <input type="number" name="assessment_cost" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500">Cost Document</label>
            <input type="file" name="assessment_cost_document" class="mt-1 block w-full text-sm text-gray-500">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-500">Special Instructions</label>
            <textarea name="special_instructions" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-500">Notes</label>
            <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
        </div>
        <div class="sm:col-span-2 flex justify-end">
            <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Save Assessment</button>
        </div>
    </form>
    @endif
</div>

            {{-- ══ FINANCIAL TRACK ══ --}}
            <div class="rounded-xl border border-emerald-200 bg-white overflow-hidden">

                {{-- Track header --}}
                <div class="flex items-center gap-3 border-b border-emerald-100 px-5 py-4" style="background:linear-gradient(135deg,#ecfdf5 0%,#f0fdf4 100%);">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background:#d1fae5;">
                        <i class="fa-solid fa-sack-dollar text-sm" style="color:#065f46;"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold" style="color:#065f46;">Financial Track</p>
                        <p class="text-xs mt-0.5" style="color:#047857;">
                            Tracks the full financial journey of this case across three stages: fee agreement at intake → cost estimations to funder → approved funding cycles.
                        </p>
                    </div>
                    {{-- Progress indicators --}}
                    <div class="hidden sm:flex items-center gap-1.5 shrink-0">
                        @php
                            $s1done = $patient->fee_agreed_amount || $patient->invoice_recipient_type;
                            $s2done = $patient->costEstimations->isNotEmpty();
                            $s3done = $patient->fundingCycles->isNotEmpty();
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $s1done ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fa-solid {{ $s1done ? 'fa-circle-check' : 'fa-circle' }} text-xs"></i> Step 1
                        </span>
                        <span class="text-gray-300 text-xs">›</span>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $s2done ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fa-solid {{ $s2done ? 'fa-circle-check' : 'fa-circle' }} text-xs"></i> Step 2
                        </span>
                        <span class="text-gray-300 text-xs">›</span>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $s3done ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fa-solid {{ $s3done ? 'fa-circle-check' : 'fa-circle' }} text-xs"></i> Step 3
                        </span>
                    </div>
                </div>

                {{-- Step 1: Fee Agreement & Invoice Setup --}}
                <div class="border-b border-gray-100" x-data="{ editing: false }">
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white" style="background:#0092b4;">1</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Fee Agreement &amp; Invoice Setup</p>
                                <p class="text-xs text-gray-400">Fee agreed before assessment, supporting documents, and invoice recipient.</p>
                            </div>
                        </div>
                        <button type="button" @click="editing = !editing" class="text-xs text-[#0092b4] hover:underline flex items-center gap-1 shrink-0 ml-4">
                            <i class="fa-solid fa-pen"></i> <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                        </button>
                    </div>
                    <div class="px-5 py-4">
                        {{-- Display --}}
                        <div x-show="!editing">
                            <dl class="grid grid-cols-2 gap-3 text-sm">
                                <div><dt class="text-xs text-gray-400 mb-0.5">Fee Agreed</dt><dd class="font-medium text-gray-800">{{ $patient->fee_agreed_amount ? '£'.number_format($patient->fee_agreed_amount, 2) : '—' }}</dd></div>
                                <div><dt class="text-xs text-gray-400 mb-0.5">Assessment Report Sent</dt><dd class="font-medium text-gray-800">{{ $patient->assessment_report_sent ? 'Yes' : 'No' }}</dd></div>
                                <div><dt class="text-xs text-gray-400 mb-0.5">Invoice Recipient Type</dt><dd class="font-medium text-gray-800">{{ $patient->invoice_recipient_type ?? '—' }}</dd></div>
                                <div><dt class="text-xs text-gray-400 mb-0.5">Recipient Name</dt><dd class="font-medium text-gray-800">{{ $patient->invoice_recipient_name ?? '—' }}</dd></div>
                                @if($patient->invoice_recipient_email)
                                <div><dt class="text-xs text-gray-400 mb-0.5">Recipient Email</dt><dd class="font-medium text-gray-800">{{ $patient->invoice_recipient_email }}</dd></div>
                                @endif
                                @if($patient->invoice_recipient_address)
                                <div><dt class="text-xs text-gray-400 mb-0.5">Recipient Address</dt><dd class="font-medium text-gray-800">{{ $patient->invoice_recipient_address }}</dd></div>
                                @endif
                            </dl>
                            @if($patient->fee_agreed_document || $patient->assessment_report_document)
                            <div class="mt-3 flex gap-3 flex-wrap">
                                @if($patient->fee_agreed_document)
                                <a href="{{ Storage::url($patient->fee_agreed_document) }}" target="_blank" class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-[#0092b4] hover:bg-gray-100"><i class="fa-solid fa-file-lines"></i> Fee Agreement</a>
                                @endif
                                @if($patient->assessment_report_document)
                                <a href="{{ Storage::url($patient->assessment_report_document) }}" target="_blank" class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-[#0092b4] hover:bg-gray-100"><i class="fa-solid fa-file-lines"></i> Assessment Report</a>
                                @endif
                            </div>
                            @endif
                        </div>
                        {{-- Edit --}}
                        <div x-show="editing" x-cloak>
                            <form method="POST" action="{{ route('patients.update-accounts', $patient) }}" enctype="multipart/form-data">
                                @csrf @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Fee Agreed for Assessment (£)</label>
                                        <input type="number" step="0.01" min="0" name="fee_agreed_amount" value="{{ old('fee_agreed_amount', $patient->fee_agreed_amount) }}" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Fee Agreement Document</label>
                                        <input type="file" name="fee_agreed_document" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-[#0092b4] file:text-white hover:file:bg-[#007a9a]">
                                        @if($patient->fee_agreed_document)<p class="mt-1 text-xs text-gray-400">Current: <a href="{{ Storage::url($patient->fee_agreed_document) }}" target="_blank" class="text-[#0092b4] hover:underline">View</a></p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Assessment Report Sent</label>
                                        <div class="flex items-center gap-4 mt-1">
                                            <label class="flex items-center gap-1.5 text-sm"><input type="radio" name="assessment_report_sent" value="1" @checked($patient->assessment_report_sent)> Yes</label>
                                            <label class="flex items-center gap-1.5 text-sm"><input type="radio" name="assessment_report_sent" value="0" @checked(!$patient->assessment_report_sent)> No</label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Assessment Report Document</label>
                                        <input type="file" name="assessment_report_document" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-[#0092b4] file:text-white hover:file:bg-[#007a9a]">
                                        @if($patient->assessment_report_document)<p class="mt-1 text-xs text-gray-400">Current: <a href="{{ Storage::url($patient->assessment_report_document) }}" target="_blank" class="text-[#0092b4] hover:underline">View</a></p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Invoice Recipient Type</label>
                                        <select name="invoice_recipient_type" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                            <option value="">Select Type</option>
                                            <option value="Case Manager Company" @selected($patient->invoice_recipient_type === 'Case Manager Company')>Case Manager Company</option>
                                            <option value="Solicitor" @selected($patient->invoice_recipient_type === 'Solicitor')>Solicitor</option>
                                            <option value="Insurance Company" @selected($patient->invoice_recipient_type === 'Insurance Company')>Insurance Company</option>
                                            <option value="Other" @selected($patient->invoice_recipient_type === 'Other')>Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Recipient Name</label>
                                        <input type="text" name="invoice_recipient_name" value="{{ old('invoice_recipient_name', $patient->invoice_recipient_name) }}" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Recipient Email</label>
                                        <input type="email" name="invoice_recipient_email" value="{{ old('invoice_recipient_email', $patient->invoice_recipient_email) }}" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Recipient Address</label>
                                        <input type="text" name="invoice_recipient_address" value="{{ old('invoice_recipient_address', $patient->invoice_recipient_address) }}" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="rounded-lg px-4 py-2 text-sm text-white" style="background-color:#0092b4;">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>{{-- /Step 1 --}}

                {{-- Step 2: Treatment Cost Estimations --}}
                <div class="border-b border-gray-100">
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white" style="background:#0092b4;">2</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Treatment Cost Estimations</p>
                                <p class="text-xs text-gray-400">Cost proposals prepared after assessment and sent to the funder for approval.</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('addCostEstimationForm').classList.toggle('hidden')" class="text-xs text-white rounded-lg px-3 py-1.5 shrink-0 ml-4" style="background-color:#0092b4;">
                            <i class="fa-solid fa-plus mr-1"></i> Add
                        </button>
                    </div>
                    <div class="px-5 py-4">
                        <form id="addCostEstimationForm" method="POST" action="{{ route('cost-estimations.store') }}" class="hidden mb-4 grid gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                <input type="text" name="title" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Estimated Amount (£) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" name="estimated_amount" required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                @error('estimated_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Estimated Sessions</label>
                                <input type="number" min="0" name="estimated_sessions" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sent Date</label>
                                <input type="date" name="sent_date" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            </div>
                            <div class="sm:col-span-2 flex justify-end gap-2">
                                <button type="button" onclick="document.getElementById('addCostEstimationForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs text-white" style="background-color:#0092b4;">Save Estimation</button>
                            </div>
                        </form>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-left">
                                    <th class="pb-2 pr-3 text-xs font-medium text-gray-400 uppercase">Title</th>
                                    <th class="pb-2 pr-3 text-xs font-medium text-gray-400 uppercase">Amount</th>
                                    <th class="pb-2 pr-3 text-xs font-medium text-gray-400 uppercase">Sent Date</th>
                                    <th class="pb-2 text-xs font-medium text-gray-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($patient->costEstimations as $cost)
                                <tr>
                                    <td class="py-2 pr-3 text-gray-800">{{ $cost->title ?? 'Version '.$cost->version_number }}</td>
                                    <td class="py-2 pr-3 font-medium text-gray-800">£{{ number_format($cost->estimated_amount, 2) }}</td>
                                    <td class="py-2 pr-3 text-gray-600 text-xs">{{ $cost->sent_date ? $cost->sent_date->format('d/m/Y') : '—' }}</td>
                                    <td class="py-2"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cost->sent_date ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $cost->sent_date ? 'Sent' : 'Draft' }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="py-4 text-center text-sm text-gray-400">No cost estimations yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>{{-- /Step 2 --}}

                {{-- Step 3: Funding Cycles --}}
                <div>
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white" style="background:#0092b4;">3</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Approved Funding Cycles</p>
                                <p class="text-xs text-gray-400">Funding approved by the funder — tracks approved amount, spend to date, and remaining balance.</p>
                            </div>
                        </div>
                        @if(in_array(Auth::user()->role, ['admin', 'developer']))
                        <a href="{{ route('funding-cycles.create', ['patient_id' => $patient->id]) }}" class="text-xs text-white rounded-lg px-3 py-1.5 shrink-0 ml-4" style="background-color:#0092b4;">
                            <i class="fa-solid fa-plus mr-1"></i> Add
                        </a>
                        @endif
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        @forelse($patient->fundingCycles as $cycle)
                        <div class="rounded-lg border p-4 {{ $cycle->is_active ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-200 bg-gray-50/40' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="font-semibold text-gray-800">Cycle {{ $cycle->cycle_number }}</span>
                                    @if($cycle->funder_name)<span class="text-sm text-gray-500 ml-2">{{ $cycle->funder_name }}</span>@endif
                                </div>
                                @if($cycle->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Active</span>
                                @endif
                            </div>
                            @php
                                $approved = $cycle->approved_amount;
                                $used = $fundingBalanceService->invoicedAmount($cycle);
                                $remaining = $fundingBalanceService->remainingBalance($cycle);
                                $usagePct = $fundingBalanceService->usagePercentage($cycle);
                            @endphp
                            <div class="grid grid-cols-3 gap-3 text-sm mb-3">
                                <div class="rounded-md bg-white border border-gray-200 px-3 py-2 text-center">
                                    <p class="text-xs text-gray-400 mb-0.5">Approved</p>
                                    <p class="font-semibold text-gray-800">£{{ number_format($approved, 2) }}</p>
                                </div>
                                <div class="rounded-md bg-white border border-gray-200 px-3 py-2 text-center">
                                    <p class="text-xs text-gray-400 mb-0.5">Used</p>
                                    <p class="font-semibold text-gray-800">£{{ number_format($used, 2) }}</p>
                                </div>
                                <div class="rounded-md border px-3 py-2 text-center {{ $usagePct >= 100 ? 'border-red-200 bg-red-50' : ($usagePct >= 80 ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50') }}">
                                    <p class="text-xs mb-0.5 {{ $usagePct >= 100 ? 'text-red-400' : ($usagePct >= 80 ? 'text-amber-500' : 'text-emerald-500') }}">Remaining</p>
                                    <p class="font-semibold {{ $usagePct >= 100 ? 'text-red-700' : ($usagePct >= 80 ? 'text-amber-700' : 'text-emerald-700') }}">£{{ number_format($remaining, 2) }}</p>
                                </div>
                            </div>
                            {{-- Usage bar --}}
                            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $usagePct >= 100 ? 'bg-red-500' : ($usagePct >= 80 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width:{{ min($usagePct, 100) }}%"></div>
                            </div>
                            <p class="text-xs mt-1 {{ $usagePct >= 100 ? 'text-red-600' : ($usagePct >= 80 ? 'text-amber-600' : 'text-gray-400') }}">
                                {{ round($usagePct) }}% used
                                @if($usagePct >= 100) — funding exhausted
                                @elseif($usagePct >= 80) — consider requesting further funding
                                @endif
                            </p>
                            @if($cycle->approval_document_path)
                            <p class="text-xs text-emerald-600 mt-2"><i class="fa-solid fa-file-check mr-1"></i> Approval document uploaded</p>
                            @endif
                        </div>
                        @empty
                        <p class="py-3 text-sm text-gray-400 text-center">No funding cycles yet — added once the funder approves a cost estimation.</p>
                        @endforelse
                    </div>
                </div>{{-- /Step 3 --}}

            </div>{{-- /Financial Track --}}

            {{-- ══ MDT MEETINGS ══ --}}
            <div class="rounded-lg border border-purple-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                            <i class="fa-solid fa-people-group text-purple-600 text-sm"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">MDT Meeting Discussions</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('addMdtForm').classList.toggle('hidden')"
                        class="rounded-lg px-3 py-1.5 text-xs text-white" style="background:#7c3aed;">
                        <i class="fa-solid fa-plus mr-1"></i> Record Meeting
                    </button>
                </div>

                {{-- Add MDT Meeting Form --}}
                <form id="addMdtForm" method="POST" action="{{ route('patients.mdt-meetings.store', $patient) }}"
                    class="hidden mb-5 rounded-xl border border-purple-200 bg-purple-50 p-4 grid gap-3 sm:grid-cols-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Meeting Date <span class="text-red-500">*</span></label>
                        <input type="date" name="meeting_date" value="{{ now()->toDateString() }}"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Attendees</label>
                        <input type="text" name="attendees" placeholder="e.g. Samy, Nick Hill, Jane (CM)"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Discussion <span class="text-red-500">*</span></label>
                        <textarea name="discussion" rows="3" placeholder="Summary of what was discussed in the MDT meeting…"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Outcomes / Action Items</label>
                        <textarea name="outcomes" rows="2" placeholder="Decisions made, next steps agreed…"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400"></textarea>
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addMdtForm').classList.add('hidden')"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit"
                            class="rounded-lg px-4 py-2 text-xs text-white" style="background:#7c3aed;">Save Meeting</button>
                    </div>
                </form>

                {{-- MDT Meeting List --}}
                <div class="space-y-3">
                    @forelse($patient->mdtMeetings as $mdt)
                    <div class="rounded-xl border border-purple-100 bg-purple-50/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="text-sm font-semibold text-purple-800">
                                        <i class="fa-solid fa-calendar-days mr-1 text-purple-500"></i>
                                        {{ $mdt->meeting_date->format('d M Y') }}
                                    </span>
                                    @if($mdt->attendees)
                                    <span class="text-xs text-gray-500"><i class="fa-solid fa-users mr-1"></i>{{ $mdt->attendees }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed mb-1">{{ $mdt->discussion }}</p>
                                @if($mdt->outcomes)
                                <div class="mt-2 rounded-lg bg-white border border-purple-100 px-3 py-2">
                                    <p class="text-xs font-semibold text-purple-700 mb-0.5"><i class="fa-solid fa-circle-check mr-1"></i>Outcomes / Actions</p>
                                    <p class="text-sm text-gray-600">{{ $mdt->outcomes }}</p>
                                </div>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">Recorded by {{ $mdt->creator?->name ?? 'Unknown' }}</p>
                            </div>
                            @if(in_array(Auth::user()->role, ['admin', 'developer']))
                            <form method="POST" action="{{ route('patients.mdt-meetings.destroy', [$patient, $mdt]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this MDT meeting record?')"
                                    class="text-xs text-red-400 hover:text-red-600 mt-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-400">No MDT meetings recorded yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- ══ ASSOCIATE COMMUNICATIONS ══ --}}
            <div class="rounded-lg border border-blue-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                            <i class="fa-solid fa-envelope text-blue-600 text-sm"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">Associate Communications</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('addAssocCommForm').classList.toggle('hidden')"
                        class="rounded-lg px-3 py-1.5 text-xs text-white" style="background:#2563eb;">
                        <i class="fa-solid fa-plus mr-1"></i> Add Communication
                    </button>
                </div>

                {{-- Add Associate Communication Form --}}
                <form id="addAssocCommForm" method="POST" action="{{ route('communications.store') }}"
                    class="hidden mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4 grid gap-3 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Associate <span class="text-red-500">*</span></label>
                        <select name="patient_associate_id"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                            <option value="">Select associate…</option>
                            @foreach($patient->patientAssociates as $pa)
                            <option value="{{ $pa->id }}">{{ $pa->associate?->name }} ({{ $pa->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                            <option value="Email">Email</option>
                            <option value="Phone">Phone</option>
                            <option value="Letter">Letter</option>
                            <option value="Meeting">Meeting</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Direction <span class="text-red-500">*</span></label>
                        <select name="direction"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                            <option value="Inbound">Inbound (from associate)</option>
                            <option value="Outbound">Outbound (to associate)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                        <input type="datetime-local" name="communication_date" value="{{ now()->format('Y-m-d\TH:i') }}"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" placeholder="e.g. Session update, availability query…"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes / Summary</label>
                        <textarea name="summary" rows="3" placeholder="Key points from the communication…"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"></textarea>
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addAssocCommForm').classList.add('hidden')"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit"
                            class="rounded-lg px-4 py-2 text-xs text-white" style="background:#2563eb;">Save</button>
                    </div>
                </form>

                {{-- Associate Communication List --}}
                <div class="space-y-3">
                    @php
                    $assocComms = $patient->communications()
                        ->whereNotNull('patient_associate_id')
                        ->with('patientAssociate.associate')
                        ->latest('communication_date')
                        ->get();
                    @endphp
                    @forelse($assocComms as $comm)
                    <div class="flex gap-3 rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                            {{ $comm->type === 'Email' ? 'bg-blue-100' : ($comm->type === 'Phone' ? 'bg-green-100' : 'bg-gray-100') }}">
                            <i class="fa-solid text-xs
                                {{ $comm->type === 'Email' ? 'fa-envelope text-blue-600' :
                                   ($comm->type === 'Phone' ? 'fa-phone text-green-600' :
                                   ($comm->type === 'Letter' ? 'fa-envelope-open text-gray-600' :
                                   ($comm->type === 'WhatsApp' ? 'fa-whatsapp text-emerald-600' : 'fa-comment text-gray-600'))) }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <span class="text-sm font-semibold text-gray-800">{{ $comm->patientAssociate?->associate?->name ?? '—' }}</span>
                                <span class="text-xs text-gray-400">{{ $comm->direction === 'Inbound' ? '← from' : '→ to' }} associate</span>
                                <span class="text-xs text-gray-400">{{ $comm->communication_date?->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700">{{ $comm->subject }}</p>
                            @if($comm->summary)
                            <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">{{ $comm->summary }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-400">No associate communications recorded yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Documents</h3>
                    <button type="button" onclick="document.getElementById('uploadDocumentForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-upload mr-1"></i> Upload
                    </button>
                </div>
                <form id="uploadDocumentForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Document Type</label>
                        <select name="document_type_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">Select Type</option>
                            @foreach($documentTypes as $dt)
                            <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                            @endforeach
                        </select>
                        @error('document_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">File</label>
                        <input type="file" name="file" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('uploadDocumentForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Upload</button>
                    </div>
                </form>
                <div class="space-y-2">
                    @forelse($patient->documents as $doc)
                    <div class="flex items-center justify-between rounded-md border border-gray-100 bg-gray-50 px-3 py-2 text-sm">
                        <div class="flex items-center gap-2 min-w-0">
                            <i class="fa-regular fa-file-lines shrink-0 text-gray-400"></i>
                            <span class="truncate text-gray-700">{{ $doc->file_name }}</span>
                        </div>
                        <a href="{{ route('documents.download', $doc) }}" class="shrink-0 text-[#0092b4] hover:underline" target="_blank">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-500">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Communications</h3>
                    <button type="button" onclick="document.getElementById('addCommForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Log Communication
                    </button>
                </div>
                <form id="addCommForm" method="POST" action="{{ route('communications.store') }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Type</label>
                        <select name="type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
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
                        <select name="direction" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Inbound">Inbound</option>
                            <option value="Outbound">Outbound</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500">Subject *</label>
                        <input type="text" name="subject" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500">Summary</label>
                        <textarea name="summary" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Date/Time</label>
                        <input type="datetime-local" name="communication_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addCommForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Save</button>
                    </div>
                </form>
                <div class="space-y-4">
                    @forelse($patient->communications as $comm)
                    <div class="flex gap-3 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                            <i class="fa-regular fa-{{ $comm->type === 'Email' ? 'envelope' : ($comm->type === 'Phone' ? 'phone' : 'comment') }} text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-medium text-gray-800">{{ $comm->direction === 'Inbound' ? '←' : '→' }} {{ $comm->type }}</span>
                                <span class="text-xs text-gray-400">{{ $comm->communication_date ?? $comm->created_at }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700">{{ $comm->subject }}</p>
                            <p class="text-sm text-gray-500">{{ $comm->summary }}</p>
                            @if($comm->follow_up_date && !$comm->follow_up_completed)
                            <div class="mt-1 flex items-center gap-2">
                                <p class="text-xs text-amber-600">Follow-up: {{ $comm->follow_up_date?->format('d/m/Y') }}</p>
                                <form method="POST" action="{{ route('communications.complete-follow-up', $comm) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-emerald-600 hover:underline">Mark Done</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-500">No communications recorded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Treatment Notes</h3>
                    <a href="{{ route('case-notes.index', ['patient_id' => $patient->id]) }}" class="text-sm text-[#0092b4] hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($patient->caseNotes as $note)
                    <div class="flex gap-3 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                            <i class="fa-regular fa-file-lines text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 text-sm">
                                <a href="{{ route('case-notes.show', $note) }}" class="font-medium text-gray-800 hover:text-[#0092b4]">{{ $note->note_type }}</a>
                                <span class="text-xs text-gray-400">{{ $note->session_date?->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-500">{{ $note->associate?->name ?? 'Unknown associate' }}</p>
                            <span class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $note->is_signed_off ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $note->is_signed_off ? 'Signed off' . ($note->signedOffBy ? ' by ' . $note->signedOffBy->name : '') : 'Pending sign-off' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-500">No case notes recorded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Patient Journey</h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @forelse($timeline as $event)
                        <div class="relative flex gap-4 pl-10">
                            <div class="absolute left-2.5 -translate-x-1/2 w-3 h-3 rounded-full border-2 border-white {{ str_replace('text', 'bg', explode(' ', $event['color'])[0]) }}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $event['color'] }}">{{ $event['type'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $event['date'] instanceof \Carbon\Carbon ? $event['date']->format('d/m/Y') : date('d/m/Y', strtotime($event['date'])) }}</span>
                                </div>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $event['desc'] }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="py-4 text-center text-sm text-gray-500">No timeline events recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
