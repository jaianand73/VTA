<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Enquiry Details</h2>
                <p class="text-sm text-gray-500 mt-0.5">Stage 1 — Intelligence gathering</p>
            </div>
            <div class="flex gap-2">
                @if(!$enquiry->referral && $enquiry->status !== 'Converted to Referral')
                <form method="POST" action="{{ route('enquiries.promoteToReferral', $enquiry) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white"
                        style="background:#0092b4;">
                        <i class="fa-solid fa-arrow-right"></i> Promote to Referral
                    </button>
                </form>
                @elseif($enquiry->referral)
                <a href="{{ route('referrals.show', $enquiry->referral) }}"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white"
                    style="background:#0f766e;">
                    <i class="fa-solid fa-file-medical"></i> View Referral
                </a>
                @endif
                <a href="{{ route('enquiries.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i> All Enquiries
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Enquiry Information</h3>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="document.getElementById('enquiryForm').classList.toggle('hidden')" class="text-sm text-[#0092b4] hover:underline">
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </button>
                        @if(in_array(Auth::user()->role, ['admin', 'staff', 'developer']))
                        <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}"
                              data-swal="Delete this enquiry? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs text-white hover:bg-red-700">
                                <i class="fa-solid fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Enquiry ID</dt><dd class="font-medium text-gray-800">{{ $enquiry->enquiry_ref ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Enquirer Name</dt><dd class="font-medium text-gray-800">{{ $enquiry->enquirer_name }}</dd></div>
                    <div><dt class="text-gray-500">Company</dt><dd class="font-medium text-gray-800">{{ $enquiry->selectedCompany?->name ?? $enquiry->company_name }}</dd></div>
                    <div><dt class="text-gray-500">Case Manager</dt><dd class="font-medium text-gray-800">{{ $enquiry->selectedCaseManager?->first_name }} {{ $enquiry->selectedCaseManager?->last_name }}{{ $enquiry->selectedCaseManager ? ' (' . $enquiry->selectedCaseManager->company?->name . ')' : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $enquiry->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $enquiry->phone ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Source</dt><dd class="font-medium text-gray-800">{{ $enquiry->source ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Enquiry Date</dt><dd class="font-medium text-gray-800">{{ $enquiry->enquiry_date?->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">First Response</dt><dd class="font-medium text-gray-800">{{ $enquiry->first_response_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    @if($enquiry->first_response_remarks)
                    <div class="col-span-2"><dt class="text-gray-500">Response Remarks</dt><dd class="font-medium text-gray-800">{{ $enquiry->first_response_remarks }}</dd></div>
                    @endif
                    <div><dt class="text-gray-500">Client's Location</dt><dd class="font-medium text-gray-800">{{ $enquiry->client_location ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Nearest Associate</dt><dd class="font-medium text-gray-800">{{ $enquiry->nearestAssociate?->name ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @php $colors = ['New'=>'bg-blue-100 text-blue-700','In Progress'=>'bg-amber-100 text-amber-700','Converted to Referral'=>'bg-teal-100 text-teal-700','Not Proceeding'=>'bg-gray-100 text-gray-600']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$enquiry->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $enquiry->status }}</span>
                        </dd>
                    </div>
                    <div class="col-span-2"><dt class="text-gray-500">Reason</dt><dd class="font-medium text-gray-800">{{ $enquiry->reason ?? '—' }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Notes</dt><dd class="font-medium text-gray-800">{{ $enquiry->notes ?? '—' }}</dd></div>
                </dl>

                <form id="enquiryForm" method="POST" action="{{ route('enquiries.update', $enquiry) }}" class="hidden mt-6 border-t border-gray-200 pt-6">
                    @csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="block text-sm font-medium text-gray-700">Enquiry ID</label><input type="text" name="enquiry_ref" value="{{ $enquiry->enquiry_ref }}" placeholder="e.g. E001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Enquirer Name</label><input type="text" name="enquirer_name" value="{{ $enquiry->enquirer_name }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Company</label>
                            <select name="company_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">-- Select --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected(($enquiry->company_id ?? $enquiry->selectedCompany?->id) == $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Case Manager</label>
                            <select name="case_manager_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">-- Select (optional) --</option>
                                @foreach($caseManagers as $cm)
                                    <option value="{{ $cm->id }}" @selected(($enquiry->case_manager_id ?? $enquiry->selectedCaseManager?->id) == $cm->id)>{{ $cm->first_name }} {{ $cm->last_name }} ({{ $cm->company?->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" value="{{ $enquiry->email }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Phone</label><input type="text" name="phone" value="{{ $enquiry->phone }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Source</label>
                            <select name="source" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="Website" @selected($enquiry->source === 'Website')>Website</option>
                                <option value="Referral Letter" @selected($enquiry->source === 'Referral Letter')>Referral Letter</option>
                                <option value="Phone" @selected($enquiry->source === 'Phone')>Phone</option>
                                <option value="Email" @selected($enquiry->source === 'Email')>Email</option>
                                <option value="LinkedIn" @selected($enquiry->source === 'LinkedIn')>LinkedIn</option>
                                <option value="Word of Mouth" @selected($enquiry->source === 'Word of Mouth')>Word of Mouth</option>
                                <option value="Other" @selected($enquiry->source === 'Other')>Other</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Enquiry Date</label><input type="date" name="enquiry_date" value="{{ $enquiry->enquiry_date?->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">First Response Date</label><input type="date" name="first_response_date" value="{{ $enquiry->first_response_date?->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700">Response Remarks</label><input type="text" name="first_response_remarks" value="{{ $enquiry->first_response_remarks }}" placeholder="Notes on first response…" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Client's Location</label><input type="text" name="client_location" value="{{ $enquiry->client_location }}" placeholder="e.g. Manchester" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Nearest Associate</label>
                            <select name="nearest_associate_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">-- Select --</option>
                                @foreach($associates as $assoc)
                                    <option value="{{ $assoc->id }}" @selected($enquiry->nearest_associate_id == $assoc->id)>{{ $assoc->name }}@if($assoc->region) ({{ $assoc->region }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="New" @selected($enquiry->status === 'New')>New</option>
                                <option value="In Progress" @selected($enquiry->status === 'In Progress')>In Progress</option>
                                <option value="Converted to Referral" @selected($enquiry->status === 'Converted to Referral')>Converted to Referral</option>
                                <option value="Not Proceeding" @selected($enquiry->status === 'Not Proceeding')>Not Proceeding</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium text-gray-700">Reason</label><textarea name="reason" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ $enquiry->reason }}</textarea></div>
                    <div class="mt-4"><label class="block text-sm font-medium text-gray-700">Notes</label><textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ $enquiry->notes }}</textarea></div>

                    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4" x-data="{ contacts: @js($enquiry->contacts->map(fn($c) => ['name' => $c->name, 'role' => $c->role, 'email' => $c->email, 'phone' => $c->phone])->values()) }">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Contacts</h4>
                        <template x-for="(contact, index) in contacts" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2 p-2 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Name</label>
                                    <input type="text" x-model="contact.name" :name="'contacts['+index+'][name]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Role</label>
                                    <select x-model="contact.role" :name="'contacts['+index+'][role]'" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                                        <option value="Case Manager">Case Manager</option>
                                        <option value="Lead Professional">Lead Professional</option>
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
                                    <button type="button" @click="contacts.splice(index, 1)" class="mt-2 text-xs text-red-600 hover:underline" x-show="contacts.length > 1">Remove</button>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="contacts.push({ name: '', role: 'Case Manager', email: '', phone: '' })" class="text-sm text-[#0092b4] hover:underline">
                            <i class="fa-solid fa-plus mr-1"></i> Add Another Contact
                        </button>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('enquiryForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Update</button>
                    </div>
                </form>
            </div>

            @if($enquiry->contacts->count())
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contacts</h3>
                <div class="space-y-3">
                    @foreach($enquiry->contacts as $contact)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $contact->name }}</p>
                            <p class="text-xs text-gray-500">{{ $contact->role }}@if($contact->email) — {{ $contact->email }}@endif @if($contact->phone) — {{ $contact->phone }}@endif</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($nearestAssociates->isNotEmpty())
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h3 class="text-sm font-semibold text-blue-800 mb-2"><i class="fa-solid fa-location-dot mr-1"></i> Suggested Associates (by region)</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($nearestAssociates as $assoc)
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium" style="background:#dbeafe;color:#1e3a8a;">
                        {{ $assoc->name }}<span class="opacity-60 ml-1">— {{ $assoc->region }}</span>
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <div class="space-y-6">
            @if($enquiry->patient)
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Patient Record</h3>
                <p class="text-sm text-gray-500 mb-4">This enquiry has been linked to a patient record.</p>
                <a href="{{ route('patients.show', $enquiry->patient) }}" class="inline-flex items-center rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
                    <i class="fa-solid fa-external-link-alt mr-1"></i> View Patient
                </a>
            </div>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Communication Log</h3>
                    <button type="button" onclick="document.getElementById('addCommForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Log Communication
                    </button>
                </div>
                <form id="addCommForm" method="POST" action="{{ route('communications.store') }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
                    @if($enquiry->case_manager_id)
                    <input type="hidden" name="case_manager_id" value="{{ $enquiry->case_manager_id }}">
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Type</label>
                        <select name="type" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="Email">Email</option>
                            <option value="Phone">Phone</option>
                            <option value="Letter">Letter</option>
                            <option value="Meeting">Meeting</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="Follow Up">Follow Up</option>
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
                    @forelse($communications as $comm)
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
                    <h3 class="text-lg font-semibold text-gray-800">Follow-ups</h3>
                    <button type="button" onclick="document.getElementById('addFollowUpForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Log Follow-up
                    </button>
                </div>
                <form id="addFollowUpForm" method="POST" action="{{ route('communications.store') }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
                    <input type="hidden" name="type" value="Follow Up">
                    <input type="hidden" name="direction" value="Outbound">
                    @if($enquiry->case_manager_id)
                    <input type="hidden" name="case_manager_id" value="{{ $enquiry->case_manager_id }}">
                    @endif
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500">Notes / Summary</label>
                        <textarea name="summary" rows="2" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Follow-up Date</label>
                        <input type="date" name="follow_up_date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="flex items-end justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addFollowUpForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Save Follow-up</button>
                    </div>
                </form>
                <div class="space-y-3">
                    @php $followUps = $communications->where('type', 'Follow Up'); @endphp
                    @forelse($followUps as $i => $fu)
                    <div class="flex gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 text-xs font-bold">
                            {{ $i + 1 }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-medium text-gray-800">Follow-up #{{ $i + 1 }}</span>
                                <span class="text-xs text-gray-400">{{ $fu->follow_up_date?->format('d/m/Y') ?? ($fu->communication_date?->format('d/m/Y') ?? $fu->created_at->format('d/m/Y')) }}</span>
                                @if($fu->follow_up_completed)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Completed</span>
                                @else
                                <form method="POST" action="{{ route('communications.complete-follow-up', $fu) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-emerald-600 hover:underline">Mark Done</button>
                                </form>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500">{{ $fu->summary ?? $fu->subject }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="py-4 text-center text-sm text-gray-500">No follow-ups recorded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Documents</h3>
                    <button type="button" onclick="document.getElementById('addEnqDocForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-upload mr-1"></i> Upload
                    </button>
                </div>
                <form id="addEnqDocForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4">
                    @csrf
                    <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
                    @if($enquiry->case_manager_id)
                    <input type="hidden" name="case_manager_id" value="{{ $enquiry->case_manager_id }}">
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Document Type</label>
                        <select name="document_type_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                            <option value="">-- Select Type (optional) --</option>
                            @foreach($documentTypes as $dt)
                                <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">File *</label>
                        <input type="file" name="file" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addEnqDocForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Upload</button>
                    </div>
                </form>
                <x-document-list :documents="$documents" />
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleCompanyFields(select) {
            const fields = document.getElementById('newCompanyFields');
            fields.style.display = select.value ? 'none' : '';
            fields.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = !!select.value;
            });
        }

        function toggleCmFields(select) {
            const fields = document.getElementById('newCmFields');
            fields.style.display = select.value ? 'none' : '';
            fields.querySelectorAll('input').forEach(el => {
                el.disabled = !!select.value;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.querySelector('select[name="existing_company_id"]');
            if (sel) toggleCompanyFields(sel);
            const cmSel = document.querySelector('select[name="existing_cm_id"]');
            if (cmSel) toggleCmFields(cmSel);
        });
    </script>
    @endpush
</x-app-layout>
