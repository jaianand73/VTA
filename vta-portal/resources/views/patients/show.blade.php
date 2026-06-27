<x-app-layout>
    <x-slot name="header">{{ $patient->first_name }} {{ $patient->last_name }}</x-slot>

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
                    <div><dt class="text-gray-500">Full Name</dt><dd class="font-medium text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</dd></div>
                    <div><dt class="text-gray-500">Date of Birth</dt><dd class="font-medium text-gray-800">{{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Location</dt><dd class="font-medium text-gray-800">{{ $patient->location ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Condition</dt><dd class="font-medium text-gray-800">{{ $patient->condition ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Referral Date</dt><dd class="font-medium text-gray-800">{{ $patient->referral_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">First Contact</dt><dd class="font-medium text-gray-800">{{ $patient->first_contact_date?->format('d/m/Y') ?? '—' }}</dd></div>
                </dl>
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
                            'Discharged' => 'bg-gray-100 text-gray-600',
                            'Case Closed' => 'bg-gray-200 text-gray-500',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$patient->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->status }}</span>
                </div>
                <form method="POST" action="{{ route('patients.status', $patient) }}" class="flex gap-3 mb-4" data-swal="Change patient status to the selected value?">
                    @csrf @method('PATCH')
                    <select name="status" class="block rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">
                        @php
                            $allStatuses = ['Enquiry Logged','Response Sent','Awaiting LOI','LOI Received','Assessment Scheduled','Assessment Completed','Report Drafted','Report Sent','Cost Estimation Sent','Awaiting Funding Approval','Funding Approved','Treatment Active','Awaiting Further Funding','Discharged','Case Closed'];
                        @endphp
                        @foreach($allStatuses as $s)
                        <option value="{{ $s }}" @selected($patient->status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Update</button>
                </form>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Case Manager</h3>
                @if($patient->caseManager)
                <div class="text-sm">
                    <p class="font-medium text-gray-800">{{ $patient->caseManager->first_name }} {{ $patient->caseManager->last_name }}</p>
                    <p class="text-gray-500">{{ $patient->caseManager->company?->name }}</p>
                    <a href="{{ route('companies.case-managers.show', [$patient->caseManager->company_id, $patient->caseManager]) }}" class="text-[#0092b4] hover:underline">View Profile</a>
                </div>
                @else
                <p class="text-sm text-gray-500">No case manager assigned.</p>
                @endif
            </div>

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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Funding Overview</h3>
                @php
                    $fundingCycle = $patient->fundingCycles->first();
                    $balanceService = $fundingCycle ? app(\App\Services\FundingBalanceService::class) : null;
                    $used = $fundingCycle ? $balanceService->invoicedAmount($fundingCycle) : 0;
                @endphp
                @if($fundingCycle)
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Allocated</span>
                        <span class="font-medium text-gray-800">£{{ number_format($fundingCycle->approved_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Used</span>
                        <span class="font-medium text-gray-800">£{{ number_format($used, 2) }}</span>
                    </div>
                    @php $pct = $fundingCycle->approved_amount > 0 ? round(($used / $fundingCycle->approved_amount) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Progress</span>
                            <span>{{ $pct }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-[#0092b4]" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-500">No funding cycle assigned.</p>
                @endif
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
                    <h3 class="text-lg font-semibold text-gray-800">Cost Estimations</h3>
                    <button type="button" onclick="document.getElementById('addCostEstimationForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Add
                    </button>
                </div>
                <form id="addCostEstimationForm" method="POST" action="{{ route('cost-estimations.store') }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Title</label>
                        <input type="text" name="title" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Estimated Amount (£) *</label>
                        <input type="number" step="0.01" min="0" name="estimated_amount" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        @error('estimated_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Estimated Sessions</label>
                        <input type="number" min="0" name="estimated_sessions" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Sent Date</label>
                        <input type="date" name="sent_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addCostEstimationForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Save Estimation</button>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                                <th class="pb-2 pr-3">Title</th>
                                <th class="pb-2 pr-3">Amount</th>
                                <th class="pb-2 pr-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($patient->costEstimations as $cost)
                            <tr class="even:bg-gray-50">
                                <td class="py-2 pr-3 text-gray-800">{{ $cost->title ?? 'Version ' . $cost->version_number }}</td>
                                <td class="py-2 pr-3 text-gray-800">£{{ number_format($cost->estimated_amount, 2) }}</td>
                                <td class="py-2 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cost->sent_date ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $cost->sent_date ? 'Sent' : 'Draft' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">No cost estimations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
                    <h3 class="text-lg font-semibold text-gray-800">Case Notes</h3>
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Notes</h3>
                <form method="POST" action="{{ route('patients.notes', $patient) }}">
                    @csrf @method('PATCH')
                    <textarea name="notes" rows="5" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4] text-sm">{{ $patient->notes }}</textarea>
                    <div class="mt-2 flex justify-end">
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm text-white hover:bg-[#007a9a]">Save Notes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
