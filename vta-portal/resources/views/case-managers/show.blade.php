<x-app-layout>
    <x-slot name="header">{{ $caseManager->first_name }} {{ $caseManager->last_name }}</x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Case Manager Information</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-800">{{ $caseManager->first_name }} {{ $caseManager->last_name }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $caseManager->email }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $caseManager->phone ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Job Title</dt><dd class="font-medium text-gray-800">{{ $caseManager->job_title ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Company</dt>
                        <dd><a href="{{ route('companies.show', $caseManager->company) }}" class="text-[#0092b4] hover:underline">{{ $caseManager->company->name }}</a></dd>
                    </div>
                    <div><dt class="text-gray-500">Status</dt>
                        <dd><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $caseManager->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $caseManager->status }}</span></dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Portal Access</h3>
                    @if($caseManager->user_id)
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                        <i class="fa-solid fa-check mr-1"></i> Active
                    </span>
                    @else
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                    @endif
                </div>
                @if($caseManager->user_id)
                <p class="text-sm text-gray-500">Portal login active — {{ $caseManager->user?->email }}</p>
                @else
                <button onclick="document.getElementById('createPortalForm').classList.toggle('hidden')"
                        class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">
                    Create Portal Login
                </button>
                <form id="createPortalForm" method="POST" action="{{ route('companies.case-managers.create-portal-login', [$company, $caseManager]) }}" class="hidden mt-3 space-y-2" data-swal="Create portal login for this case manager?">
                    @csrf
                    <input type="email" name="email" placeholder="Login email" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                    <input type="password" name="password" placeholder="Temporary password" required min="8" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0092b4] focus:ring-[#0092b4] text-sm">
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a9a]">Create Login</button>
                </form>
                @endif
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">NDA Status</h3>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $caseManager->nda_signed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $caseManager->nda_signed ? 'Signed' : 'Pending' }}</span>
                </div>
                @if($caseManager->nda_signed && $caseManager->nda_signed_date)
                <p class="text-sm text-gray-500">Signed on {{ $caseManager->nda_signed_date->format('d/m/Y') }}.</p>
                @else
                <p class="text-sm text-gray-500">NDA has not been signed yet.</p>
                <form method="POST" action="{{ route('companies.case-managers.mark-nda-signed', [$company, $caseManager]) }}" class="mt-3" data-swal="Mark NDA as signed for {{ $caseManager->first_name }} {{ $caseManager->last_name }}?">
                    @csrf
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-file-signature mr-1"></i> Mark as Signed
                    </button>
                </form>
                @endif
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Materials Sent</h3>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $caseManager->materials_sent ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $caseManager->materials_sent ? 'Sent' : 'Not Sent' }}</span>
                </div>
                @if($caseManager->materials_sent && $caseManager->materials_sent_date)
                <p class="text-sm text-gray-500">Sent on {{ $caseManager->materials_sent_date->format('d/m/Y') }}.</p>
                @else
                <p class="text-sm text-gray-500">Materials have not been sent yet.</p>
                <form method="POST" action="{{ route('companies.case-managers.mark-materials-sent', [$company, $caseManager]) }}" class="mt-3" data-swal="Mark materials as sent for {{ $caseManager->first_name }} {{ $caseManager->last_name }}?">
                    @csrf
                    <button type="submit" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Mark as Sent
                    </button>
                </form>
                @endif
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Patients</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase text-gray-500">
                                <th class="pb-2 pr-3">Patient Name</th>
                                <th class="pb-2 pr-3">Status</th>
                                <th class="pb-2 pr-3">Referral Date</th>
                                <th class="pb-2 pr-3">Needs Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($caseManager->patients as $patient)
                            <tr class="even:bg-gray-50">
                                <td class="py-2 pr-3 font-medium text-gray-800">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-[#0092b4] hover:underline">{{ $patient->first_name }} {{ $patient->last_name }}</a>
                                </td>
                                <td class="py-2 pr-3">
                                    @php $colors = ['New'=>'bg-blue-100 text-blue-700','In Progress'=>'bg-amber-100 text-amber-700','Converted'=>'bg-green-100 text-green-700','Not Proceeding'=>'bg-gray-100 text-gray-600','Needs Review'=>'bg-red-100 text-red-700']; @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$patient->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->status }}</span>
                                </td>
                                <td class="py-2 pr-3 text-gray-600">{{ $patient->referral_date?->format('d/m/Y') }}</td>
                                <td class="py-2 pr-3">
                                    @if($patient->needs_review)
                                    <i class="fa-solid fa-circle-exclamation text-red-500" title="Needs Review"></i>
                                    @else
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">No patients assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Communication Log</h3>
                    <button type="button" onclick="document.getElementById('addCommForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-plus mr-1"></i> Log Communication
                    </button>
                </div>
                <form id="addCommForm" method="POST" action="{{ route('communications.store') }}" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="case_manager_id" value="{{ $caseManager->id }}">
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
                    @forelse($caseManager->communications as $comm)
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
                    <button type="button" onclick="document.getElementById('addCmDocForm').classList.toggle('hidden')" class="rounded-lg bg-[#0092b4] px-4 py-2 text-xs text-white hover:bg-[#007a9a]">
                        <i class="fa-solid fa-upload mr-1"></i> Upload
                    </button>
                </div>
                <form id="addCmDocForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="hidden mb-4 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4">
                    @csrf
                    <input type="hidden" name="case_manager_id" value="{{ $caseManager->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">File *</label>
                        <input type="file" name="file" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#0092b4] focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addCmDocForm').classList.add('hidden')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0092b4] px-3 py-2 text-xs text-white hover:bg-[#007a9a]">Upload</button>
                    </div>
                </form>
                <div class="space-y-2">
                    @forelse($caseManager->documents as $doc)
                    <div class="flex items-center justify-between rounded-md border border-gray-100 bg-gray-50 px-3 py-2 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-file-lines text-gray-400"></i>
                            <span class="text-gray-700">{{ $doc->file_name }}</span>
                        </div>
                        <a href="{{ route('documents.download', $doc) }}" class="text-[#0092b4] hover:underline" target="_blank">
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
</x-app-layout>
