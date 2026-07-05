<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('settings.index', ['tab' => 'associates']) }}"
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $associate->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $associate->region }} · {{ $associate->speciality }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                    {{ $associate->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $associate->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
            @endif

            {{-- Profile card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-gray-400 text-sm"></i>
                    <span class="font-semibold text-gray-700 text-sm">Profile</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Email</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->email ?? '—' }}</p></div>
                    <div><span class="text-gray-500">Phone</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->phone ?? '—' }}</p></div>
                    <div><span class="text-gray-500">Region</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->region }}</p></div>
                    <div><span class="text-gray-500">Speciality</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->speciality ?? '—' }}</p></div>
                    @if($associate->qualifications)
                    <div class="col-span-2"><span class="text-gray-500">Qualifications</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->qualifications }}</p></div>
                    @endif
                    @if($associate->notes)
                    <div class="col-span-2"><span class="text-gray-500">Notes</span><p class="font-medium text-gray-900 mt-0.5">{{ $associate->notes }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Workload summary --}}
            @php
                $activeReferrals = \App\Models\Referral::where('associate_id', $associate->id)
                    ->whereIn('status', ['Assessment', 'Proposal Submitted', 'Approved'])
                    ->get();
                $activePatients  = $associate->patients->count();
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase-medical text-gray-400 text-sm"></i>
                    <span class="font-semibold text-gray-700 text-sm">Current Workload</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    {{-- Active referrals tile --}}
                    <div class="rounded-lg border p-3 text-center" style="border-color:#d1fae5;background:#f0fdf4;">
                        <p class="text-2xl font-bold" style="color:#059669;">{{ $activeReferrals->count() }}</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">Active Referrals</p>
                    </div>
                    {{-- Active patients tile --}}
                    <div class="rounded-lg border p-3 text-center" style="border-color:#dbeafe;background:#eff6ff;">
                        <p class="text-2xl font-bold" style="color:#2563eb;">{{ $activePatients }}</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">Active Patients</p>
                    </div>
                    {{-- Assessment count --}}
                    <div class="rounded-lg border p-3 text-center" style="border-color:#e9d5ff;background:#faf5ff;">
                        <p class="text-2xl font-bold" style="color:#7c3aed;">{{ $activeReferrals->where('status','Assessment')->count() }}</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">In Assessment</p>
                    </div>
                    {{-- Pending approval --}}
                    <div class="rounded-lg border p-3 text-center" style="border-color:#fde68a;background:#fffbeb;">
                        <p class="text-2xl font-bold" style="color:#d97706;">{{ $activeReferrals->whereIn('status',['Proposal Submitted','Approved'])->count() }}</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">Proposal / Approved</p>
                    </div>
                </div>
                @if($activeReferrals->count() > 0)
                <div class="px-5 pb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Active Referrals</p>
                    <div class="space-y-1.5">
                        @foreach($activeReferrals as $ref)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-semibold text-gray-700">{{ $ref->referral_ref }}</span>
                                <span class="text-sm text-gray-800">{{ $ref->patient_first_name }} {{ $ref->patient_last_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($ref->status === 'Assessment') bg-purple-100 text-purple-700
                                    @elseif($ref->status === 'Proposal Submitted') bg-amber-100 text-amber-700
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ $ref->status }}
                                </span>
                                <a href="{{ route('referrals.show', $ref) }}" class="text-xs text-[#0092b4] hover:underline">View</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- CV card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-gray-400 text-sm"></i>
                    <span class="font-semibold text-gray-700 text-sm">CV / Curriculum Vitae</span>
                </div>
                <div class="p-5">
                    @if($associate->cv_path)
                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-pdf text-blue-500 text-xl"></i>
                            <div>
                                <p class="text-sm font-semibold text-blue-900">CV on file</p>
                                <p class="text-xs text-blue-600">{{ basename($associate->cv_path) }}</p>
                            </div>
                        </div>
                        <a href="{{ Storage::disk('vta-documents')->url($associate->cv_path) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs font-semibold px-3 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">Upload a new file to replace the current CV.</p>
                    @else
                    <p class="text-sm text-gray-500 mb-4">No CV uploaded yet. Upload a PDF, DOC, or DOCX file (max 10 MB).</p>
                    @endif

                    <form method="POST" action="{{ route('settings.associates.upload-cv', $associate) }}"
                          enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf
                        <input type="file" name="cv" accept=".pdf,.doc,.docx"
                               class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-[#0092b4] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[#007a9a] transition-colors">
                            <i class="fa-solid fa-upload"></i> Upload CV
                        </button>
                    </form>
                    @error('cv')<p class="text-red-600 text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- HR & Compliance --}}
            <div class="bg-white rounded-xl border border-amber-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-amber-100 flex items-center gap-2" style="background:#fffbeb;">
                    <i class="fa-solid fa-shield-halved text-amber-500 text-sm"></i>
                    <span class="font-semibold text-gray-700 text-sm">HR & Compliance</span>
                </div>
                <div class="p-5">
                    {{-- Hourly rate --}}
                    <form method="POST" action="{{ route('settings.associates.update', $associate) }}"
                          class="flex items-end gap-3 mb-5">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hourly Rate (£)</label>
                            <input type="number" name="hourly_rate" step="0.01" min="0"
                                   value="{{ $associate->hourly_rate }}"
                                   class="rounded-lg border-gray-300 text-sm w-40 px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-[#0092b4]">
                        </div>
                        <input type="hidden" name="name" value="{{ $associate->name }}">
                        <input type="hidden" name="region" value="{{ $associate->region }}">
                        <button type="submit" style="background:#0092b4;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                            Save Rate
                        </button>
                    </form>

                    {{-- Existing docs --}}
                    @if($associate->complianceDocuments->isNotEmpty())
                    <div class="mb-4 space-y-2">
                        @foreach($associate->complianceDocuments as $doc)
                        @php
                            $docExpired = $doc->isExpired();
                            $docSoon    = !$docExpired && $doc->isExpiringSoon();
                            $docBorder  = $docExpired ? 'border-red-200;background:#fef2f2;' : ($docSoon ? 'border-amber-200;background:#fffbeb;' : 'border-green-200;background:#f0fdf4;');
                        @endphp
                        <div class="flex items-center justify-between rounded-lg border px-3 py-2"
                             style="border-color:{{ $docExpired ? '#fca5a5' : ($docSoon ? '#fde68a' : '#bbf7d0') }};background:{{ $docExpired ? '#fef2f2' : ($docSoon ? '#fffbeb' : '#f0fdf4') }};">
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $doc->document_type }}</span>
                                @if($doc->expiry_date)
                                <span class="text-xs ml-2 {{ $docExpired ? 'text-red-600 font-semibold' : ($docSoon ? 'text-amber-600 font-semibold' : 'text-gray-400') }}">
                                    {{ $docExpired ? 'EXPIRED' : ($docSoon ? 'Expires soon' : 'Expires') }}
                                    {{ $doc->expiry_date->format('d M Y') }}
                                </span>
                                @endif
                                @if($doc->notes)
                                <span class="text-xs text-gray-400 ml-2">— {{ $doc->notes }}</span>
                                @endif
                            </div>
                            @if($doc->document_path)
                            <a href="{{ Storage::disk('vta-documents')->url($doc->document_path) }}" target="_blank"
                               class="text-xs text-blue-500 hover:underline">View</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Add new compliance doc --}}
                    <form method="POST"
                          action="{{ route('settings.associates.compliance.store', $associate) }}"
                          enctype="multipart/form-data"
                          class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3 pt-3 border-t border-gray-100">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Document Type</label>
                            <select name="document_type" required class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-amber-400">
                                <option value="">— Select —</option>
                                @foreach(['DBS Check','Professional Registration','Contract','CSP Membership','Insurance','Other'] as $dtype)
                                <option value="{{ $dtype }}">{{ $dtype }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date (optional)</label>
                            <input type="date" name="expiry_date" class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Upload Document</label>
                            <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full text-sm text-gray-600">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                            <input type="text" name="notes" placeholder="e.g. Renewal pending"
                                   class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-amber-400">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    style="background:#d97706;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;width:100%;">
                                <i class="fa-solid fa-plus mr-1"></i> Add Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Communications log --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-comments text-gray-400 text-sm"></i>
                        <span class="font-semibold text-gray-700 text-sm">Communications</span>
                        <span class="ml-1 bg-gray-200 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                            {{ $associate->communications->count() }}
                        </span>
                    </div>
                </div>

                {{-- Log form --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-[#0092b4] hover:text-[#007a9a]">
                        <i class="fa-solid fa-plus" :class="open ? 'rotate-45' : ''" style="transition:transform .15s"></i>
                        Log Communication
                    </button>

                    <div x-show="open" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('communications.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="associate_id" value="{{ $associate->id }}">

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent">
                                        <option value="">Select…</option>
                                        <option value="Email">Email</option>
                                        <option value="Call">Phone Call</option>
                                        <option value="MDT Meeting">MDT Meeting</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Letter">Letter</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Direction</label>
                                    <select name="direction" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent">
                                        <option value="in">Incoming</option>
                                        <option value="out">Outgoing</option>
                                        <option value="internal">Internal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                                    <input type="date" name="communication_date" value="{{ date('Y-m-d') }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                                <input type="text" name="subject" required placeholder="Brief subject…"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes / Discussion Summary</label>
                                <textarea name="summary" rows="3" placeholder="Key points, decisions, action items…"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Follow-up Date (optional)</label>
                                    <input type="date" name="follow_up_date"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0092b4] focus:border-transparent">
                                </div>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-[#0092b4] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[#007a9a] transition-colors">
                                <i class="fa-solid fa-floppy-disk"></i> Save
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Communication entries --}}
                @forelse($associate->communications as $comm)
                <div class="px-5 py-4 border-b border-gray-100 last:border-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                                {{ $comm->type === 'MDT Meeting' || $comm->type === 'Meeting' ? 'bg-purple-100' :
                                   ($comm->type === 'Email' ? 'bg-blue-100' : 'bg-gray-100') }}">
                                <i class="fa-solid text-xs
                                    {{ $comm->type === 'MDT Meeting' ? 'fa-people-group text-purple-600' :
                                       ($comm->type === 'Meeting' ? 'fa-handshake text-purple-600' :
                                       ($comm->type === 'Email' ? 'fa-envelope text-blue-600' :
                                       ($comm->type === 'Call' ? 'fa-phone text-green-600' : 'fa-comment text-gray-600'))) }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-sm text-gray-900">{{ $comm->subject }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                        {{ $comm->direction === 'in' ? 'bg-green-100 text-green-700' :
                                           ($comm->direction === 'out' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $comm->direction === 'in' ? 'Incoming' : ($comm->direction === 'out' ? 'Outgoing' : 'Internal') }}
                                    </span>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $comm->type }}</span>
                                </div>
                                @if($comm->summary)
                                <p class="text-sm text-gray-600 mt-1">{{ $comm->summary }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1.5">
                                    {{ $comm->communication_date->format('d M Y') }}
                                    @if($comm->createdBy) · by {{ $comm->createdBy->name }} @endif
                                    @if($comm->follow_up_date && !$comm->follow_up_completed)
                                    · <span class="text-amber-600 font-medium">Follow-up: {{ $comm->follow_up_date->format('d M Y') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    <i class="fa-solid fa-comments text-2xl mb-2 block text-gray-300"></i>
                    No communications logged yet.
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
