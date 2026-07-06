{{--
    Reusable document list with approval UI.
    Props: $documents (collection), $uploadForm (bool - show upload form), $context (string)
--}}
@props(['documents', 'showUpload' => false, 'uploadData' => []])

@if($documents->isEmpty() && !$showUpload)
<p class="text-sm text-gray-400 italic">No documents uploaded yet.</p>
@else

@if($documents->isNotEmpty())
<div class="space-y-2">
    @foreach($documents as $doc)
    @php
        $statusColor = match($doc->approval_status) {
            'approved' => ['bg'=>'#dcfce7','text'=>'#15803d','icon'=>'fa-circle-check','label'=>'Approved'],
            'rejected' => ['bg'=>'#fee2e2','text'=>'#b91c1c','icon'=>'fa-circle-xmark','label'=>'Rejected'],
            default    => ['bg'=>'#fef9c3','text'=>'#92400e','icon'=>'fa-clock','label'=>'Awaiting Review'],
        };
    @endphp
    <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5"
         x-data="{ approving: false }">
        <i class="fa-solid fa-file text-gray-400 mt-0.5 flex-shrink-0"></i>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('documents.download', $doc) }}"
                   class="text-sm font-medium text-[#0092b4] hover:underline truncate">
                    {{ $doc->file_name }}
                </a>
                {{-- Approval badge --}}
                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                      style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};">
                    <i class="fa-solid {{ $statusColor['icon'] }} text-xs"></i>
                    {{ $statusColor['label'] }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $doc->documentType?->name ?? '—' }}
                @if($doc->uploadedBy) &middot; {{ $doc->uploadedBy->name }} @endif
                &middot; {{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}
            </p>
            @if($doc->approval_remarks)
            <p class="text-xs mt-1 italic" style="color:{{ $statusColor['text'] }};">
                {{ $doc->approval_remarks }}
            </p>
            @endif

            {{-- Approval form (admin/staff only, shown when not yet approved) --}}
            @if(in_array(Auth::user()->role, ['admin','staff']) && $doc->isPending())
            <div x-show="!approving" class="mt-1.5">
                <button type="button" @click="approving=true"
                    class="text-xs font-medium text-[#0092b4] hover:underline">
                    <i class="fa-solid fa-pen-to-square mr-1"></i>Review & Approve
                </button>
            </div>
            <div x-show="approving" x-cloak class="mt-2">
                <form method="POST" action="{{ route('documents.approve', $doc) }}">
                    @csrf @method('PATCH')
                    <div class="flex flex-col gap-1.5">
                        <textarea name="approval_remarks" rows="2" placeholder="Remarks (optional)…"
                            class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0092b4]"></textarea>
                        <div class="flex gap-2">
                            <button type="submit" name="approval_status" value="approved"
                                class="inline-flex items-center gap-1 rounded px-3 py-1 text-xs font-semibold text-white"
                                style="background:#16a34a;">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                            <button type="submit" name="approval_status" value="rejected"
                                class="inline-flex items-center gap-1 rounded px-3 py-1 text-xs font-semibold text-white"
                                style="background:#dc2626;">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </button>
                            <button type="button" @click="approving=false"
                                class="text-xs text-gray-400 hover:text-gray-600 px-2">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            {{-- Re-review option for already decided docs --}}
            @if(in_array(Auth::user()->role, ['admin','staff']) && !$doc->isPending())
            <div x-show="!approving" class="mt-1">
                <button type="button" @click="approving=true"
                    class="text-xs text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-rotate-left mr-1"></i>Change decision
                </button>
            </div>
            <div x-show="approving" x-cloak class="mt-2">
                <form method="POST" action="{{ route('documents.approve', $doc) }}">
                    @csrf @method('PATCH')
                    <div class="flex flex-col gap-1.5">
                        <textarea name="approval_remarks" rows="2" placeholder="Remarks…"
                            class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-gray-700">{{ $doc->approval_remarks }}</textarea>
                        <div class="flex gap-2">
                            <button type="submit" name="approval_status" value="approved"
                                class="inline-flex items-center gap-1 rounded px-3 py-1 text-xs font-semibold text-white"
                                style="background:#16a34a;">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                            <button type="submit" name="approval_status" value="rejected"
                                class="inline-flex items-center gap-1 rounded px-3 py-1 text-xs font-semibold text-white"
                                style="background:#dc2626;">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </button>
                            <button type="button" @click="approving=false"
                                class="text-xs text-gray-400 hover:text-gray-600 px-2">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Delete --}}
        @if(in_array(Auth::user()->role, ['admin','staff']))
        <form method="POST" action="{{ route('documents.destroy', $doc) }}"
              data-swal="Delete {{ $doc->file_name }}?">
            @csrf @method('DELETE')
            <button type="submit" class="text-gray-300 hover:text-red-500 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-trash text-xs"></i>
            </button>
        </form>
        @endif
    </div>
    @endforeach
</div>
@endif

@endif
