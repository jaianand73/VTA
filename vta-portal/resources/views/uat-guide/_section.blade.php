@once
<style>
/* ── UAT Step Cards ── */
.uat-card          { background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.07),0 0 0 1px rgba(0,0,0,.06); margin-bottom:10px; overflow:hidden; transition:box-shadow .15s; }
.uat-card:hover    { box-shadow:0 3px 8px rgba(0,0,0,.1),0 0 0 1px rgba(0,0,0,.06); }

/* Header */
.uat-hdr           { display:flex; align-items:center; gap:12px; padding:13px 16px; cursor:pointer; user-select:none; transition:background .15s; }
.uat-ref           { flex-shrink:0; font-size:11px; font-weight:800; letter-spacing:.4px; color:#fff; padding:3px 9px; border-radius:999px; transition:background .15s; }
.uat-title         { flex:1; font-size:14px; font-weight:600; color:#111827; line-height:1.3; }
.uat-chevron       { flex-shrink:0; color:#9ca3af; font-size:10px; transition:transform .2s; margin-left:2px; }

/* Status chip */
.uat-chip          { display:inline-flex; align-items:center; gap:5px; border-radius:999px; padding:3px 11px; font-size:12px; font-weight:600; white-space:nowrap; }
.uat-chip-pass     { background:#dcfce7; color:#15803d; }
.uat-chip-fail     { background:#fee2e2; color:#b91c1c; }
.uat-chip-sug      { background:#fef3c7; color:#92400e; }
.uat-chip-none     { background:#f3f4f6; color:#9ca3af; font-style:italic; font-weight:400; }
.uat-ts            { font-size:11px; color:#9ca3af; white-space:nowrap; }

/* Body */
.uat-body          { border-top:1px solid #f3f4f6; padding:18px 20px 20px; }
.uat-inst-text     { font-size:13px !important; color:#374151 !important; line-height:1.6 !important; }
.uat-inst-text strong { color:#111827 !important; font-weight:600 !important; }
.uat-inst-text code,
.uat-inst-text kbd { background:#f3f4f6 !important; color:#374151 !important; border:1px solid #d1d5db !important;
                     border-radius:4px !important; padding:1px 6px !important; font-size:12px !important; font-family:monospace !important; }
/* Widget pills */
.uat-widget-strip  { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
.uat-widget-pill   { display:inline-flex; flex-direction:column; background:#e6f5f9; border:1px solid #bae6fd;
                     border-radius:8px; padding:6px 12px; font-size:12px; line-height:1.35; }
.uat-widget-pill b { color:#0092b4 !important; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; }
.uat-widget-pill span { color:#4b5563 !important; margin-top:1px; }

/* Record result */
.uat-divider       { border:none; border-top:1px solid #f3f4f6; margin:16px 0; }
.uat-section-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#9ca3af; margin:0 0 10px; }

/* Result toggle buttons */
.uat-pick          { display:inline-flex; align-items:center; gap:7px; border:1.5px solid #e5e7eb; border-radius:8px;
                     padding:8px 16px; font-size:13px; font-weight:600; color:#6b7280; background:#fff;
                     cursor:pointer; transition:all .15s; white-space:nowrap; }
.uat-pick:hover    { border-color:#9ca3af; color:#374151; background:#f9fafb; }
.uat-pick.sel-pass { border-color:#16a34a; background:#f0fdf4; color:#15803d; }
.uat-pick.sel-fail { border-color:#dc2626; background:#fef2f2; color:#b91c1c; }
.uat-pick.sel-sug  { border-color:#d97706; background:#fffbeb; color:#92400e; }

/* Save button */
.uat-save-btn      { display:inline-flex; align-items:center; gap:8px; background:#0092b4; color:#fff; border:none;
                     border-radius:8px; padding:9px 22px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
.uat-save-btn:hover:not(:disabled) { background:#007a9a; }
.uat-save-btn:disabled { opacity:.7; cursor:default; }
</style>
@endonce

{{-- ── Section heading ── --}}
<div id="{{ $sectionId }}" style="margin-top:28px; margin-bottom:10px;">
    <div style="display:flex; align-items:center; gap:10px; padding-bottom:8px; border-bottom:2px solid #f3f4f6;">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $roleClass }}">{{ $roleLabel }}</span>
        <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:#9ca3af;">{{ $sectionLabel }}</span>
    </div>
</div>

@if(isset($note))
<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:11px 15px; font-size:13px; color:#1e40af; margin-bottom:10px;">
    <i class="fa-solid fa-circle-info" style="margin-right:7px;"></i>{{ $note }}
</div>
@endif

{{-- ── Steps ── --}}
@foreach($steps as $step)
@php
    $ref    = $step['ref'];
    $result = $results[$ref] ?? null;

    if (!$result)                   { $stripe='#d1d5db'; $hdrBg='#fff';     $refBg='#6b7280'; }
    elseif ($result->result==='pass')                { $stripe='#16a34a'; $hdrBg='#f8fffe'; $refBg='#16a34a'; }
    elseif ($result->result==='fail')                { $stripe='#dc2626'; $hdrBg='#fff8f8'; $refBg='#dc2626'; }
    else                            { $stripe='#d97706'; $hdrBg='#fffcf0'; $refBg='#d97706'; }

    $initLabel = $result ? ($result->result === 'pass' ? 'pass' : ($result->result === 'fail' ? 'fail' : 'pass_with_improvement')) : '';
@endphp

<div class="uat-card"
     style="border-left:5px solid {{ $stripe }};"
     :style="{ borderLeftColor: chosen==='pass'?'#16a34a': chosen==='fail'?'#dc2626': chosen==='pass_with_improvement'?'#d97706':'{{ $stripe }}' }"
     x-data="{
         open: {{ $result ? 'false' : 'true' }},
         chosen: '{{ $result->result ?? '' }}',
         showComment: {{ ($result && $result->comment) ? 'true' : 'false' }},
         saving: false,
         saved: false,
         async saveResult(form) {
             if (!this.chosen) return;
             this.saving = true;
             const fd = new FormData(form);
             fd.set('result', this.chosen);
             try {
                 const res = await fetch(form.action, {
                     method: 'POST',
                     headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' },
                     body: fd
                 });
                 if (res.ok) {
                     this.saved = true;
                     this.open = false;
                     setTimeout(() => this.saved = false, 3000);
                 } else {
                     const j = await res.json().catch(() => ({}));
                     alert(j.message || 'Something went wrong.');
                 }
             } catch(e) {
                 alert('Network error. Please try again.');
             }
             this.saving = false;
         }
     }">

    {{-- ── Header ── --}}
    <div class="uat-hdr"
         style="background:{{ $hdrBg }};"
         :style="{ background: chosen==='pass'?'#f8fffe': chosen==='fail'?'#fff8f8': chosen==='pass_with_improvement'?'#fffcf0':'{{ $hdrBg }}' }"
         @click="open = !open">

        {{-- Ref badge --}}
        <span class="uat-ref"
              style="background:{{ $refBg }};"
              :style="{ background: chosen==='pass'?'#16a34a': chosen==='fail'?'#dc2626': chosen==='pass_with_improvement'?'#d97706':'{{ $refBg }}' }">
            {{ $ref }}
        </span>

        {{-- Title --}}
        <span class="uat-title">{{ $step['title'] }}</span>

        {{-- Status chip — reactive --}}
        <span x-show="chosen===''" x-cloak class="uat-chip uat-chip-none">Not tested</span>
        <span x-show="chosen==='pass'" x-cloak class="uat-chip uat-chip-pass">
            <i class="fa-solid fa-circle-check" style="font-size:10px;"></i> Pass
        </span>
        <span x-show="chosen==='fail'" x-cloak class="uat-chip uat-chip-fail">
            <i class="fa-solid fa-circle-xmark" style="font-size:10px;"></i> Fail
        </span>
        <span x-show="chosen==='pass_with_improvement'" x-cloak class="uat-chip uat-chip-sug">
            <i class="fa-solid fa-lightbulb" style="font-size:10px;"></i> Suggestion
        </span>

        {{-- Timestamp --}}
        @if($result)
        <span class="uat-ts" x-show="!saved">{{ $result->tested_at->format('d M, H:i') }}</span>
        @endif
        <span class="uat-ts" x-show="saved" x-cloak style="color:#0092b4; font-weight:600;">✓ Saved</span>

        {{-- Chevron --}}
        <i class="fa-solid fa-chevron-down uat-chevron" :style="open ? 'transform:rotate(180deg)' : ''"></i>
    </div>

    {{-- ── Body ── --}}
    <div class="uat-body" x-show="open" x-cloak>

        {{-- Instructions --}}
        <ol style="list-style:none; margin:0 0 14px; padding:0; display:flex; flex-direction:column; gap:9px;">
            @foreach($step['instructions'] as $i => $instruction)
            <li style="display:flex; gap:11px; align-items:flex-start;">
                <span style="flex-shrink:0; width:22px; height:22px; border-radius:50%; background:#0092b4; color:#fff;
                             font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-top:2px;">
                    {{ $i + 1 }}
                </span>
                <span class="uat-inst-text">{!! $instruction !!}</span>
            </li>
            @endforeach
        </ol>

        {{-- Block test --}}
        @if(isset($step['block']))
        <div style="background:#fff5f5; border:1px solid #fecaca; border-radius:8px; padding:11px 14px; margin-bottom:12px;">
            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#b91c1c; margin:0 0 4px;">
                <i class="fa-solid fa-ban" style="margin-right:5px;"></i>Block Test — this action must be refused
            </p>
            <p style="font-size:13px; color:#991b1b; margin:0;">{{ $step['block'] }}</p>
        </div>
        @endif

        {{-- Expected outcome --}}
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:11px 14px; margin-bottom:18px;">
            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#16a34a; margin:0 0 4px;">
                <i class="fa-solid fa-circle-check" style="margin-right:5px;"></i>Expected Outcome
            </p>
            <p style="font-size:13px; color:#166534; margin:0;">{!! $step['outcome'] !!}</p>
        </div>

        {{-- Your Note (read mode + inline edit) --}}
        @if($result && $result->comment)
        <div x-data="{ editing: false }" style="margin-bottom:14px;">
            {{-- Read mode --}}
            <div x-show="!editing" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:11px 14px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                    <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin:0;">Your Note</p>
                    <button type="button" @click="editing=true"
                        style="font-size:12px; font-weight:600; color:#0092b4; background:none; border:none; cursor:pointer; padding:0;">
                        <i class="fa-solid fa-pen-to-square" style="margin-right:3px;"></i>Edit
                    </button>
                </div>
                <p style="font-size:13px; color:#374151; margin:0;">{!! nl2br(e($result->comment)) !!}</p>
            </div>

            {{-- Edit mode --}}
            <div x-show="editing" x-cloak style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:11px 14px;">
                <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#92400e; margin:0 0 8px;">
                    <i class="fa-solid fa-pen-to-square" style="margin-right:4px;"></i>Edit Your Note
                </p>
                <form @submit.prevent="
                    const fd = new FormData($el);
                    saving = true;
                    fetch('{{ route('uat-guide.store') }}', {
                        method:'POST',
                        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
                        body:fd
                    }).then(r => r.ok ? (editing=false, saved=true, setTimeout(()=>saved=false,3000)) : alert('Could not save.'))
                    .finally(()=>saving=false);
                " method="POST">
                    @csrf
                    <input type="hidden" name="step_reference" value="{{ $ref }}">
                    <input type="hidden" name="step_title" value="{{ $step['title'] }}">
                    <input type="hidden" name="result" value="{{ $result->result }}">
                    <textarea name="comment" rows="4"
                        style="width:100%; border:1px solid #fcd34d; border-radius:8px; padding:9px 13px;
                               font-size:13px; color:#374151; resize:vertical; outline:none; font-family:inherit;
                               box-sizing:border-box; background:#fff;">{{ $result->comment }}</textarea>
                    <div style="display:flex; gap:8px; margin-top:8px;">
                        <button type="submit"
                            style="display:inline-flex; align-items:center; gap:6px; background:#0092b4; color:#fff;
                                   border:none; border-radius:8px; padding:7px 16px; font-size:13px; font-weight:600; cursor:pointer;">
                            <i class="fa-solid fa-floppy-disk"></i> Save Note
                        </button>
                        <button type="button" @click="editing=false"
                            style="display:inline-flex; align-items:center; gap:6px; background:#fff; color:#6b7280;
                                   border:1px solid #d1d5db; border-radius:8px; padding:7px 16px; font-size:13px; font-weight:600; cursor:pointer;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Record result --}}
        <hr class="uat-divider">
        <p class="uat-section-label">{{ $result ? 'Update result' : 'Record your result' }}</p>

        <form @submit.prevent="saveResult($el)" method="POST" action="{{ route('uat-guide.store') }}">
            @csrf
            <input type="hidden" name="step_reference" value="{{ $ref }}">
            <input type="hidden" name="step_title" value="{{ $step['title'] }}">
            <input type="hidden" name="result" :value="chosen">

            {{-- Choice buttons --}}
            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px;">
                <button type="button"
                        @click="chosen='pass'; showComment=false"
                        :class="chosen==='pass' ? 'uat-pick sel-pass' : 'uat-pick'">
                    <i class="fa-solid fa-circle-check"></i> Pass
                </button>
                <button type="button"
                        @click="chosen='fail'; showComment=true"
                        :class="chosen==='fail' ? 'uat-pick sel-fail' : 'uat-pick'">
                    <i class="fa-solid fa-circle-xmark"></i> Fail
                </button>
                <button type="button"
                        @click="chosen='pass_with_improvement'; showComment=true"
                        :class="chosen==='pass_with_improvement' ? 'uat-pick sel-sug' : 'uat-pick'">
                    <i class="fa-solid fa-lightbulb"></i> Pass + Suggest
                </button>
            </div>

            {{-- Comment — only shown when no existing note, or when changing result --}}
            @if(!$result || !$result->comment)
            <div x-show="showComment" x-cloak style="margin-bottom:12px;">
                <textarea name="comment" rows="3"
                    style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:9px 13px;
                           font-size:13px; color:#374151; resize:vertical; outline:none; font-family:inherit;
                           box-sizing:border-box;"
                    placeholder="Describe what went wrong, or what you'd like to improve…"></textarea>
                <p x-show="chosen==='fail'" style="font-size:12px; color:#dc2626; margin:3px 0 0;">
                    A comment is required for a Fail.
                </p>
                <p x-show="chosen==='pass_with_improvement'" style="font-size:12px; color:#d97706; margin:3px 0 0;">
                    Your suggestion will appear on the Feedback Board.
                </p>
            </div>
            @else
            {{-- Hidden field carries the existing comment when only the result badge is being changed --}}
            <input type="hidden" name="comment" value="{{ $result->comment }}">
            @endif

            {{-- Submit --}}
            <div x-show="chosen !== ''" x-cloak style="display:flex; align-items:center; gap:12px;">
                <button type="submit" class="uat-save-btn" :disabled="saving">
                    <i class="fa-solid"
                       :class="saving ? 'fa-spinner fa-spin' : (saved ? 'fa-circle-check' : 'fa-floppy-disk')"></i>
                    <span x-text="saving ? 'Saving…' : (saved ? 'Saved!' : '{{ $result ? 'Update Result' : 'Save Result' }}')"></span>
                </button>
                @if($result)
                <span style="font-size:12px; color:#9ca3af;">
                    Last saved {{ $result->tested_at->format('d M Y, H:i') }}
                </span>
                @endif
            </div>
        </form>

    </div>
</div>
@endforeach
