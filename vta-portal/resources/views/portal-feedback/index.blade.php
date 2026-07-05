<x-app-layout>
    <x-slot name="header">Feedback & Questions</x-slot>

    @push('styles')
    <style>
        .tab-btn { @apply px-4 py-2 text-sm font-medium rounded-lg transition-colors; }
        .tab-btn.active { @apply bg-[#0092b4] text-white; }
        .tab-btn:not(.active) { @apply text-gray-600 hover:bg-gray-100; }
        .badge { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium; }
        .card { @apply bg-white rounded-xl border border-gray-200 p-5 mb-4; }
        .section-divider { @apply text-xs font-semibold uppercase tracking-wider text-gray-400 mt-6 mb-2 px-1; }
    </style>
    @endpush

    @php $isDeveloper = Auth::user()->role === 'developer'; @endphp

    <div x-data="{ tab: '{{ in_array(old('tab', $tab), ['questions','bugs','changes','improvements']) ? old('tab', $tab) : 'questions' }}', expandedId: null }">

        {{-- ── Stats bar ── --}}
        @if($isDeveloper)
        <div class="mb-6" style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-yellow-600">{{ $stats['questions_pending'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Questions pending</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-green-600">{{ $stats['questions_done'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Questions answered</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-blue-600">{{ $stats['changes_pending'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Changes awaiting approval</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-purple-600">{{ $stats['items_done'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Items done</div>
            </div>
        </div>
        @else
        <div class="grid grid-cols-2 gap-3 mb-6 max-w-sm">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-yellow-600">{{ $stats['questions_pending'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Questions to answer</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <div class="text-2xl font-semibold text-green-600">{{ $stats['questions_done'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Questions answered</div>
            </div>
        </div>
        @endif

        {{-- ── Tab bar ── --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            <button @click="tab='questions'" :class="tab==='questions' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-circle-question mr-1"></i> Questions
                @if($stats['questions_pending'] > 0)
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-yellow-100 text-yellow-700 px-2 py-0.5 text-xs">{{ $stats['questions_pending'] }} pending</span>
                @endif
            </button>
            <button @click="tab='bugs'" :class="tab==='bugs' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Corrections
                @if($stats['bugs_open'] > 0)
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs">{{ $stats['bugs_open'] }} open</span>
                @endif
            </button>
            @if($isDeveloper)
            <button @click="tab='changes'" :class="tab==='changes' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-list-check mr-1"></i> Changes
                @if($stats['changes_pending'] > 0)
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-xs">{{ $stats['changes_pending'] }} pending</span>
                @endif
            </button>
            @endif
            <button @click="tab='improvements'" :class="tab==='improvements' ? 'bg-[#0092b4] text-white' : 'text-gray-600 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-lightbulb mr-1"></i> Improvements
            </button>
        </div>

        {{-- ════════════════ CHANGES TAB ════════════════ --}}
        <div x-show="tab==='changes'" x-cloak>
            <p class="text-sm text-gray-500 mb-4">Review each proposed change. Approve to add it to the development queue, Hold if you want to discuss it further, or Reject if it's not needed.</p>

            @php $currentSection = null; @endphp
            @foreach($changes as $item)
                @if($item->section !== $currentSection)
                    @php $currentSection = $item->section; @endphp
                    @php
                        $sectionLabels = [
                            'A'=>'Section A — Enquiry module',
                            'B'=>'Section B — Patient module',
                            'C'=>'Section C — Assessment module (new)',
                            'D'=>'Section D — Funding cycle module',
                            'E'=>'Section E — Accounts module',
                            'F'=>'Section F — Dashboard',
                            'G'=>'Section G — Navigation',
                        ];
                    @endphp
                    <div class="section-divider">{{ $sectionLabels[$item->section] ?? 'Section '.$item->section }}</div>
                @endif

                <div class="bg-white rounded-xl border border-gray-200 mb-3 overflow-hidden"
                     x-data="{ open: false }">
                    <div class="flex items-start gap-3 p-4 cursor-pointer" @click="open = !open">
                        {{-- Priority dot --}}
                        @php
                            $dotColour = match($item->priority) {
                                'critical','high' => 'bg-red-500',
                                'medium' => 'bg-yellow-400',
                                'low'    => 'bg-green-400',
                                'new'    => 'bg-blue-400',
                                default  => 'bg-gray-300',
                            };
                        @endphp
                        <span class="mt-1.5 h-2.5 w-2.5 rounded-full flex-shrink-0 {{ $dotColour }}"></span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-mono text-gray-400">{{ $item->reference }}</span>
                                <span class="font-medium text-gray-800 text-sm">{{ $item->title }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1" x-show="!open">{{ $item->description }}</p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Samy status badge --}}
                            @php
                                $sc = match($item->samy_status) {
                                    'approved' => 'bg-green-100 text-green-700',
                                    'hold'     => 'bg-yellow-100 text-yellow-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sc }} capitalize">{{ $item->samy_status }}</span>

                            {{-- Dev status badge --}}
                            @if($item->dev_status !== 'not_started')
                            @php
                                $dc = match($item->dev_status) {
                                    'done'        => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    default       => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $dc }}">{{ str_replace('_',' ',$item->dev_status) }}</span>
                            @endif

                            <i class="fa-solid text-gray-400 text-xs" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="border-t border-gray-100">
                        <div class="p-4 space-y-4">
                            <p class="text-sm text-gray-600">{{ $item->description }}</p>

                            @if($item->dev_context)
                            <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-500">
                                <span class="font-semibold text-gray-600">Dev context: </span>{{ $item->dev_context }}
                            </div>
                            @endif

                            {{-- Samy's response --}}
                            @if($item->samy_response)
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                                <p class="text-xs font-semibold text-blue-700 mb-1">Your notes</p>
                                <p class="text-sm text-blue-900">{!! nl2br(e($item->samy_response)) !!}</p>
                            </div>
                            @endif

                            {{-- Response form --}}
                            <form method="POST" action="{{ route('portal-feedback.respond', $item) }}">
                                @csrf @method('PATCH')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Your decision</label>
                                        <select name="samy_status" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="pending"  {{ $item->samy_status==='pending'  ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $item->samy_status==='approved' ? 'selected' : '' }}>Approve — build this</option>
                                            <option value="hold"     {{ $item->samy_status==='hold'     ? 'selected' : '' }}>Hold — discuss first</option>
                                            <option value="rejected" {{ $item->samy_status==='rejected' ? 'selected' : '' }}>Reject — not needed</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes / modifications (optional)</label>
                                        <input type="text" name="samy_response" value="{{ $item->samy_response }}" placeholder="Any changes to scope or approach…" class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                </div>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a99]">
                                        <i class="fa-solid fa-save"></i> Save response
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ════════════════ QUESTIONS TAB ════════════════ --}}
        <div x-show="tab==='questions'" x-cloak>
            <p class="text-sm text-gray-500 mb-4">These are questions the development team needs answered before building certain features. Please type your answer in the box and click Save.</p>

            @php $currentSection = null; @endphp
            @foreach($questions as $item)
                @if($item->section !== $currentSection)
                    @php $currentSection = $item->section; @endphp
                    <div class="section-divider">{{ $item->section }}</div>
                @endif

                @php
                    $devBorderClass = match($item->dev_status) {
                        'done'        => 'border-l-4 border-l-green-400',
                        'in_progress' => 'border-l-4 border-l-blue-400',
                        default       => $item->samy_response ? 'border-l-4 border-l-yellow-300' : '',
                    };
                @endphp
                <div class="bg-white rounded-xl border {{ $item->samy_response ? 'border-green-200' : 'border-gray-200' }} {{ $devBorderClass }} mb-3 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex-shrink-0">
                                @if($item->samy_response)
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                @else
                                    <i class="fa-regular fa-circle text-gray-300"></i>
                                @endif
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="text-xs font-mono text-gray-400">{{ $item->reference }}</span>
                                    @if(in_array($item->priority, ['critical','high']))
                                    <span class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs font-medium">
                                        Needed before dev starts
                                    </span>
                                    @endif
                                    {{-- Dev implementation status badge --}}
                                    @php
                                        [$devLabel, $devClass] = match($item->dev_status) {
                                            'done'        => ['Closed',      'bg-green-100 text-green-700'],
                                            'in_progress' => ['In Progress', 'bg-blue-100 text-blue-700'],
                                            default       => ['Open',        'bg-gray-100 text-gray-500'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $devClass }}">
                                        <span class="mr-1">●</span>{{ $devLabel }}
                                    </span>
                                </div>
                                <p class="font-medium text-gray-800 text-sm mb-3">{{ $item->title }}</p>
                                <p class="text-sm text-gray-500 mb-3">{{ $item->description }}</p>

                                <div x-data="{ editing: false }">

                                    @if($item->samy_response)
                                    {{-- Answered state: show answer + "Update" button --}}
                                    <div x-show="!editing" class="bg-green-50 border border-green-100 rounded-lg p-3 mb-2">
                                        <p class="text-xs font-semibold text-green-700 mb-1">Your answer</p>
                                        <p class="text-sm text-green-900">{{ $item->samy_response }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-gray-400">Answered {{ $item->samy_responded_at?->format('d M Y') }}</p>
                                            <button type="button" @click="editing = true"
                                                    class="text-xs text-[#0092b4] hover:underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-pen-to-square"></i> Update your answer
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    {{-- Unanswered state: show answer button prominently --}}
                                    <div x-show="!editing" class="mb-2">
                                        <button type="button" @click="editing = true"
                                                class="inline-flex items-center gap-2 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a99]">
                                            <i class="fa-solid fa-reply"></i> Answer this question
                                        </button>
                                    </div>
                                    @endif

                                    {{-- Edit form — hidden until triggered --}}
                                    <form x-show="editing" x-cloak
                                          method="POST" action="{{ route('portal-feedback.respond', $item) }}"
                                          class="flex gap-2 mt-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="samy_status" value="approved">
                                        <textarea name="samy_response" rows="2"
                                                  placeholder="Type your answer here…"
                                                  class="flex-1 rounded-lg border-gray-300 text-sm">{{ $item->samy_response }}</textarea>
                                        <div class="flex flex-col gap-1 flex-shrink-0">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-[#0092b4] px-3 py-2 text-sm font-medium text-white hover:bg-[#007a99]">
                                                <i class="fa-solid fa-save"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-500 hover:bg-gray-50">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ════════════════ IMPROVEMENTS TAB ════════════════ --}}
        <div x-show="tab==='improvements'" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">Improvements raised during UAT testing. Dev updates are shown below each item.</p>
            </div>

            @forelse($improvements as $item)
            @php
                $devDone   = $item->dev_status === 'done';
                $devInProg = $item->dev_status === 'in_progress';
                $hasQuery  = $item->dev_notes && !$devDone && !$devInProg;
            @endphp
            <div class="bg-white rounded-xl border mb-3 overflow-hidden {{ $devDone ? 'border-green-200' : ($devInProg ? 'border-blue-200' : 'border-gray-200') }}" x-data="{ open: false }">

                {{-- Header row --}}
                <div class="flex items-start gap-3 p-4 cursor-pointer" @click="open = !open">
                    <span class="mt-1.5 h-2.5 w-2.5 rounded-full flex-shrink-0
                        {{ $devDone ? 'bg-green-400' : ($devInProg ? 'bg-blue-400' : 'bg-amber-400') }}"></span>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-medium text-gray-800 text-sm">{{ $item->title }}</span>
                            @if($item->raised_by)
                            <span class="text-xs text-gray-400">— {{ $item->raised_by }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1" x-show="!open">{{ Str::limit(str_replace("\n", ' ', $item->description), 100) }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($devDone)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-700">
                            <i class="fa-solid fa-circle-check mr-1"></i> Fixed
                        </span>
                        @elseif($devInProg)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-700">
                            <i class="fa-solid fa-spinner mr-1"></i> In Progress
                        </span>
                        @elseif($item->dev_notes)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700">
                            <i class="fa-solid fa-circle-question mr-1"></i> Query
                        </span>
                        @else
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-500">
                            Pending
                        </span>
                        @endif
                        <i class="fa-solid text-gray-400 text-xs" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                </div>

                {{-- Expanded body --}}
                <div x-show="open" x-collapse class="border-t border-gray-100">
                    <div class="p-4 space-y-4">

                        {{-- Original description --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Samy's Feedback</p>
                            <p class="text-sm text-gray-700">{!! nl2br(e($item->description)) !!}</p>
                        </div>

                        {{-- Update panel — what admin sees vs developer --}}
                        @if($isDeveloper)
                            {{-- Developer sees both sections --}}
                            @if($item->client_notes)
                            <div class="rounded-lg p-3 {{ $devDone ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
                                <p class="text-xs font-semibold mb-1 {{ $devDone ? 'text-green-700' : 'text-amber-700' }}">
                                    <i class="fa-solid fa-user mr-1"></i> Client Summary
                                </p>
                                <p class="text-sm {{ $devDone ? 'text-green-900' : 'text-amber-900' }}" style="white-space:pre-wrap;">{{ $item->client_notes }}</p>
                            </div>
                            @endif
                            @if($item->dev_notes)
                            <div class="rounded-lg p-3 bg-gray-50 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">
                                    <i class="fa-solid fa-code mr-1"></i> Technical Detail
                                </p>
                                <p class="text-sm text-gray-700 font-mono text-xs leading-relaxed" style="white-space:pre-wrap;">{{ $item->dev_notes }}</p>
                            </div>
                            @endif
                        @else
                            {{-- Admin sees client_notes only (plain language) --}}
                            @if($item->client_notes)
                            <div class="rounded-lg p-3 {{ $devDone ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
                                <p class="text-xs font-semibold mb-1 {{ $devDone ? 'text-green-700' : 'text-amber-700' }}">
                                    <i class="fa-solid {{ $devDone ? 'fa-circle-check' : 'fa-circle-question' }} mr-1"></i>
                                    Developer Update
                                </p>
                                <p class="text-sm {{ $devDone ? 'text-green-900' : 'text-amber-900' }}" style="white-space:pre-wrap;">{{ $item->client_notes }}</p>
                            </div>
                            @elseif($item->dev_notes)
                            {{-- Fallback: if no client_notes yet, show dev_notes (legacy items) --}}
                            <div class="rounded-lg p-3 {{ $devDone ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
                                <p class="text-xs font-semibold mb-1 {{ $devDone ? 'text-green-700' : 'text-amber-700' }}">
                                    <i class="fa-solid {{ $devDone ? 'fa-circle-check' : 'fa-circle-question' }} mr-1"></i>
                                    Developer Update
                                </p>
                                <p class="text-sm {{ $devDone ? 'text-green-900' : 'text-amber-900' }}" style="white-space:pre-wrap;">{{ $item->dev_notes }}</p>
                            </div>
                            @endif
                        @endif

                        {{-- Samy's reply (visible to both) --}}
                        @if($item->samy_response)
                        <div class="rounded-lg p-3 bg-blue-50 border border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 mb-1">
                                <i class="fa-solid fa-reply mr-1"></i> Samy's Reply
                            </p>
                            <p class="text-sm text-blue-900" style="white-space:pre-wrap;">{{ $item->samy_response }}</p>
                        </div>
                        @endif

                        {{-- Developer follow-up (visible to both, written by developer after Samy's reply) --}}
                        @if($item->dev_follow_up)
                        <div class="rounded-lg p-3 {{ $devDone ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
                            <p class="text-xs font-semibold mb-1 {{ $devDone ? 'text-green-700' : 'text-amber-700' }}">
                                <i class="fa-solid fa-circle-check mr-1"></i> Developer Update
                            </p>
                            <p class="text-sm {{ $devDone ? 'text-green-900' : 'text-amber-900' }}" style="white-space:pre-wrap;">{{ $item->dev_follow_up }}</p>
                        </div>
                        @endif

                        {{-- Samy reply / update form (admin only) --}}
                        @if(Auth::user()->role === 'admin')
                        <form method="POST" action="{{ route('portal-feedback.respond', $item) }}">
                            @csrf @method('PATCH')
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                {{ $item->samy_response ? 'Update your reply' : 'Add a note' }}
                            </label>
                            <textarea name="samy_response" rows="2" placeholder="Type your reply…" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0092b4] px-4 py-2 text-sm font-medium text-white hover:bg-[#007a99]">
                                    <i class="fa-solid fa-paper-plane"></i> Send Reply
                                </button>
                            </div>
                        </form>
                        @endif

                        {{-- Developer update form (developer only) --}}
                        @if($isDeveloper)
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Dev Controls</p>
                            <form method="POST" action="{{ route('portal-feedback.respond', $item) }}">
                                @csrf @method('PATCH')
                                <div class="grid grid-cols-1 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                        <select name="dev_status" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="not_started" {{ $item->dev_status==='not_started' ? 'selected' : '' }}>Not Started</option>
                                            <option value="in_progress" {{ $item->dev_status==='in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="done"        {{ $item->dev_status==='done'        ? 'selected' : '' }}>Done</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            <i class="fa-solid fa-user mr-1 text-teal-500"></i> Client Summary
                                            <span class="font-normal text-gray-400 ml-1">— plain English, shown to Samy / Sheeba</span>
                                        </label>
                                        <textarea name="client_notes" rows="3" placeholder="e.g. The Travel Miles field has been removed from the appointment form." class="w-full rounded-lg border-gray-300 text-sm">{{ $item->client_notes }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            <i class="fa-solid fa-code mr-1 text-gray-500"></i> Technical Detail
                                            <span class="font-normal text-gray-400 ml-1">— developer-only, file paths / method names etc.</span>
                                        </label>
                                        <textarea name="dev_notes" rows="3" placeholder="e.g. Removed travel_miles input from create.blade.php, edit.blade.php, show.blade.php and calendar modal JS." class="w-full rounded-lg border-gray-300 text-sm font-mono text-xs">{{ $item->dev_notes }}</textarea>
                                    </div>
                                    @if($item->samy_response)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            <i class="fa-solid fa-reply mr-1 text-teal-500"></i> Reply to Samy's message
                                            <span class="font-normal text-gray-400 ml-1">— shown to Samy / Sheeba after their reply above</span>
                                        </label>
                                        <textarea name="dev_follow_up" rows="4" placeholder="Type your response to Samy's reply here…" class="w-full rounded-lg border-gray-300 text-sm">{{ $item->dev_follow_up }}</textarea>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                        <i class="fa-solid fa-save"></i> Update
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-400">
                <i class="fa-solid fa-lightbulb text-3xl mb-3 block"></i>
                No improvements raised yet. They will appear here automatically from UAT testing.
            </div>
            @endforelse

            {{-- Raise new improvement --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mt-6">
                <h3 class="text-sm font-semibold text-blue-800 mb-3"><i class="fa-solid fa-plus mr-1"></i> Suggest a new improvement</h3>
                <form method="POST" action="{{ route('portal-feedback.improvements.store') }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                            <input type="text" name="title" required placeholder="Brief description of the improvement" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Details</label>
                            <textarea name="description" rows="3" required placeholder="What should change and why…" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                <i class="fa-solid fa-paper-plane"></i> Submit suggestion
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════ CORRECTIONS TAB ════════════════ --}}
        <div x-show="tab==='bugs'" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">Log anything that looks wrong, behaves unexpectedly, or needs correcting in the portal.</p>
            </div>

            @if($bugs->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 mb-6">
                <i class="fa-solid fa-pen-to-square text-3xl mb-2 block"></i>
                No corrections logged yet.
            </div>
            @else
            @foreach($bugs as $item)
            <div class="bg-white rounded-xl border border-gray-200 mb-3 overflow-hidden" x-data="{ open: false }">
                <div class="flex items-start gap-3 p-4 cursor-pointer" @click="open = !open">
                    @php
                        $sevColour = match($item->severity) {
                            'critical' => 'bg-red-500',
                            'high'     => 'bg-orange-400',
                            'medium'   => 'bg-yellow-400',
                            default    => 'bg-gray-300',
                        };
                    @endphp
                    <span class="mt-1.5 h-2.5 w-2.5 rounded-full flex-shrink-0 {{ $sevColour }}"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ ucfirst($item->severity ?? 'medium') }} severity
                            · Raised by {{ $item->raised_by ?? 'unknown' }}
                            · {{ $item->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @php
                            if ($item->samy_status === 'rejected') {
                                $dc = 'bg-gray-100 text-gray-500';
                                $dl = 'Closed';
                            } else {
                                $dc = match($item->dev_status) {
                                    'done'        => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    default       => 'bg-orange-100 text-orange-700',
                                };
                                $dl = match($item->dev_status) {
                                    'done'        => 'Fixed',
                                    'in_progress' => 'In progress',
                                    default       => 'Open',
                                };
                            }
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $dc }}">{{ $dl }}</span>
                        <i class="fa-solid text-gray-400 text-xs" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                </div>
                <div x-show="open" x-collapse class="border-t border-gray-100 p-4 space-y-3">
                    <p class="text-sm text-gray-600">{{ $item->description }}</p>

                    @if($item->screenshots && count($item->screenshots) > 0)
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2"><i class="fa-solid fa-camera mr-1"></i>Screenshots</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($item->screenshots as $shot)
                            <a href="{{ asset('storage/' . $shot) }}" target="_blank"
                               class="block h-24 w-24 rounded-lg border border-gray-200 overflow-hidden hover:opacity-80 transition-opacity"
                               title="Click to view full size">
                                <img src="{{ asset('storage/' . $shot) }}" class="h-full w-full object-cover" alt="Screenshot">
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($item->samy_response)
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                        <p class="text-xs font-semibold text-orange-700 mb-1">Your notes</p>
                        <p class="text-sm text-orange-900">{{ $item->samy_response }}</p>
                    </div>
                    @endif

                    @if($isDeveloper)
                        @if($item->client_notes)
                        <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-sm text-green-800">
                            <p class="text-xs font-semibold text-green-700 mb-1"><i class="fa-solid fa-user mr-1"></i> Client Summary</p>
                            <p style="white-space:pre-wrap;">{{ $item->client_notes }}</p>
                        </div>
                        @endif
                        @if($item->dev_notes)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                            <p class="text-xs font-semibold text-gray-600 mb-1"><i class="fa-solid fa-code mr-1"></i> Technical Detail</p>
                            <p class="font-mono text-xs leading-relaxed" style="white-space:pre-wrap;">{{ $item->dev_notes }}</p>
                        </div>
                        @endif
                    @else
                        @if($item->client_notes)
                        <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-sm text-green-800">
                            <p class="text-xs font-semibold text-green-700 mb-1"><i class="fa-solid fa-circle-check mr-1"></i> Resolution</p>
                            <p style="white-space:pre-wrap;">{{ $item->client_notes }}</p>
                        </div>
                        @elseif($item->dev_notes)
                        <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-sm text-green-800">
                            <span class="font-semibold">Resolution: </span><span style="white-space:pre-wrap;">{{ $item->dev_notes }}</span>
                        </div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('portal-feedback.respond', $item) }}">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Add a note for the developer</label>
                            <textarea name="samy_response" rows="2"
                                      placeholder="Any extra context, steps to reproduce, or how urgent this is…"
                                      class="w-full rounded-lg border-gray-300 text-sm">{{ $item->samy_response }}</textarea>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            @if($item->samy_status === 'rejected')
                            <form method="POST" action="{{ route('portal-feedback.respond', $item) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="samy_status" value="approved">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-700">
                                    <i class="fa-solid fa-rotate-left"></i> Reopen
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('portal-feedback.respond', $item) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="samy_status" value="rejected">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">
                                    <i class="fa-solid fa-check"></i> Close this correction
                                </button>
                            </form>
                            @endif
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">
                                <i class="fa-solid fa-save"></i> Save note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
            @endif

            {{-- Raise new correction --}}
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mt-4">
                <h3 class="text-sm font-semibold text-orange-800 mb-3"><i class="fa-solid fa-pen-to-square mr-1"></i> Log a correction</h3>
                <form method="POST" action="{{ route('portal-feedback.bugs.store') }}"
                      enctype="multipart/form-data"
                      x-data="{ previews: [] }">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">What needs correcting?</label>
                            <input type="text" name="title" required placeholder="e.g. Patient status not saving correctly" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Details</label>
                            <textarea name="description" rows="3" required placeholder="Where did you see this? What did you expect vs what happened?" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">How urgent is this?</label>
                            <select name="severity" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="medium">Moderate — inconvenient but I can work around it</option>
                                <option value="high">Urgent — blocking part of my workflow</option>
                                <option value="critical">Critical — data loss or completely broken</option>
                                <option value="low">Minor — small cosmetic issue</option>
                            </select>
                        </div>

                        {{-- Screenshot upload --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Screenshots <span class="text-gray-400 font-normal">(optional — up to 5 images)</span>
                            </label>
                            <label class="flex items-center gap-3 w-full px-4 py-3 border-2 border-dashed border-orange-300 rounded-lg cursor-pointer bg-white hover:bg-orange-50 transition-colors">
                                <i class="fa-solid fa-camera text-orange-400 text-lg flex-shrink-0"></i>
                                <div>
                                    <p class="text-xs text-orange-600 font-medium">Click to attach screenshots</p>
                                    <p class="text-xs text-gray-400">PNG, JPG, GIF, WebP — max 10MB each</p>
                                </div>
                                <input type="file" name="screenshots[]" multiple accept="image/*" class="hidden"
                                       @change="
                                           previews = [];
                                           Array.from($event.target.files).slice(0,5).forEach(f => {
                                               const r = new FileReader();
                                               r.onload = e => previews.push(e.target.result);
                                               r.readAsDataURL(f);
                                           })
                                       ">
                            </label>
                            {{-- Preview thumbnails --}}
                            <div x-show="previews.length > 0" class="flex gap-2 flex-wrap mt-2">
                                <template x-for="(src, i) in previews" :key="i">
                                    <div class="relative">
                                        <img :src="src" class="h-16 w-16 object-cover rounded-lg border border-orange-200">
                                        <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" x-text="i+1"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
                            <button type="submit" style="background:#ea580c;color:#fff;padding:10px 28px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                                <i class="fa-solid fa-save"></i> Save Correction
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        // Restore active tab after form submit via flash
        @if(session('tab'))
        document.addEventListener('DOMContentLoaded', function() {
            const comp = document.querySelector('[x-data]').__x;
            if (comp) comp.$data.tab = '{{ session('tab') }}';
        });
        @endif
    </script>
    @endpush
</x-app-layout>
