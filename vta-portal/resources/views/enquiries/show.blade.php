<x-app-layout>
    <x-slot name="header">Enquiry Details</x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Enquiry Information</h3>
                    <button type="button" onclick="document.getElementById('enquiryForm').classList.toggle('hidden')" class="text-sm text-[#0092b4] hover:underline">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </button>
                </div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Enquirer Name</dt><dd class="font-medium text-gray-800">{{ $enquiry->enquirer_name }}</dd></div>
                    <div><dt class="text-gray-500">Company</dt><dd class="font-medium text-gray-800">{{ $enquiry->selectedCompany?->name ?? $enquiry->company_name }}</dd></div>
                    <div><dt class="text-gray-500">Case Manager</dt><dd class="font-medium text-gray-800">{{ $enquiry->selectedCaseManager?->first_name }} {{ $enquiry->selectedCaseManager?->last_name }}{{ $enquiry->selectedCaseManager ? ' (' . $enquiry->selectedCaseManager->company?->name . ')' : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $enquiry->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $enquiry->phone ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Source</dt><dd class="font-medium text-gray-800">{{ $enquiry->source ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Enquiry Date</dt><dd class="font-medium text-gray-800">{{ $enquiry->enquiry_date?->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">First Response</dt><dd class="font-medium text-gray-800">{{ $enquiry->first_response_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @php $colors = ['New'=>'bg-blue-100 text-blue-700','In Progress'=>'bg-amber-100 text-amber-700','Converted'=>'bg-green-100 text-green-700','Not Proceeding'=>'bg-gray-100 text-gray-600']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$enquiry->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $enquiry->status }}</span>
                        </dd>
                    </div>
                    <div class="col-span-2"><dt class="text-gray-500">Reason</dt><dd class="font-medium text-gray-800">{{ $enquiry->reason ?? '—' }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Notes</dt><dd class="font-medium text-gray-800">{{ $enquiry->notes ?? '—' }}</dd></div>
                </dl>

                <form id="enquiryForm" method="POST" action="{{ route('enquiries.update', $enquiry) }}" class="hidden mt-6 border-t border-gray-200 pt-6">
                    @csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
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
                        <div><label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="New" @selected($enquiry->status === 'New')>New</option>
                                <option value="In Progress" @selected($enquiry->status === 'In Progress')>In Progress</option>
                                <option value="Converted" @selected($enquiry->status === 'Converted')>Converted</option>
                                <option value="Not Proceeding" @selected($enquiry->status === 'Not Proceeding')>Not Proceeding</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium text-gray-700">Reason</label><textarea name="reason" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ $enquiry->reason }}</textarea></div>
                    <div class="mt-4"><label class="block text-sm font-medium text-gray-700">Notes</label><textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ $enquiry->notes }}</textarea></div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('enquiryForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            @if($enquiry->status !== 'Converted')
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Convert to Full Record</h3>
                <form method="POST" action="{{ route('enquiries.convert', $enquiry) }}" data-swal="Convert this enquiry to a full patient record? This will create Company and Case Manager records.">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Existing Company</label>
                        <select name="existing_company_id" onchange="toggleCompanyFields(this)" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                            <option value="">-- Create New Company --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="newCompanyFields" class="border-t border-gray-200 pt-4 mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">New Company Details</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><label class="block text-xs font-medium text-gray-500">Company Name</label><input type="text" name="company_name" value="{{ $enquiry->selectedCompany?->name ?? $enquiry->company_name }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-500">Type</label>
                                <select name="company_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                    <option value="Case Management">Case Management</option>
                                    <option value="Law Firm">Law Firm</option>
                                    <option value="Solicitor">Solicitor</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div><label class="block text-xs font-medium text-gray-500">Address</label><input type="text" name="address" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-500">City</label><input type="text" name="city" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-500">Phone</label><input type="text" name="phone" value="{{ $enquiry->phone }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-500">Email</label><input type="email" name="email" value="{{ $enquiry->email }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Case Manager</p>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Select Existing (optional)</label>
                            <select name="existing_cm_id" onchange="toggleCmFields(this)" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                                <option value="">-- Create New Case Manager --</option>
                                @foreach($caseManagers as $cm)
                                    <option value="{{ $cm->id }}">{{ $cm->first_name }} {{ $cm->last_name }} ({{ $cm->company?->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="newCmFields">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div><label class="block text-xs font-medium text-gray-500">First Name</label><input type="text" name="case_manager[first_name]" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Last Name</label><input type="text" name="case_manager[last_name]" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                                <div><label class="block text-xs font-medium text-gray-500">Email</label><input type="email" name="case_manager[email]" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-arrow-right mr-1"></i> Confirm Conversion
                    </button>
                </form>
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
                    @if($enquiry->case_manager_id || $enquiry->converted_to_case_manager_id)
                    <input type="hidden" name="case_manager_id" value="{{ $enquiry->converted_to_case_manager_id ?? $enquiry->case_manager_id }}">
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
                    <h3 class="text-lg font-semibold text-gray-800">Documents</h3>
                    <button type="button" onclick="document.getElementById('addEnqDocForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-upload mr-1"></i> Upload
                    </button>
                </div>
                <form id="addEnqDocForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4">
                    @csrf
                    <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
                    @if($enquiry->case_manager_id || $enquiry->converted_to_case_manager_id)
                    <input type="hidden" name="case_manager_id" value="{{ $enquiry->converted_to_case_manager_id ?? $enquiry->case_manager_id }}">
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-500">File *</label>
                        <input type="file" name="file" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addEnqDocForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Upload</button>
                    </div>
                </form>
                <div class="space-y-2">
                    @forelse($documents as $doc)
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
