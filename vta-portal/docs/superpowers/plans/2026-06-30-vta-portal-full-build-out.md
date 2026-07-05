# VTA Portal — Full Build-Out Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [x]`) syntax for tracking.

**Goal:** Implement all 26 outstanding feature items from Samy's Q&A (Open + In Progress) across 7 phases, leaving the portal feature-complete for UAT sign-off.

**Architecture:** Laravel 11 MVC — new features follow existing patterns: migration → model → controller → blade. All colour-sensitive Tailwind classes use inline `style=` attributes. Deployments go Local → Bluehost via SCP + `php artisan view:clear route:clear`.

**Tech Stack:** PHP 8.2, Laravel 11, Blade + Alpine.js, Tailwind CSS 3, MySQL, Bluehost (prod) at `/var/www/nett-apps/vta-portal/`

---

## Pre-flight: Status corrections (run these first)

Q31 (sequential funding cycles) and Q29 (funder label) are already implemented in `FundingCycleController.php` (lines 66–74). Update their `dev_status` to `done` on Bluehost:

```bash
ssh -i "D:\blue\bluehost-key" root@129.121.92.159 \
  "cd /var/www/nett-apps/vta-portal && php artisan tinker --execute=\"
    App\Models\PortalFeedbackItem::whereIn('id',[29,31])->update(['dev_status'=>'done']);
    echo 'updated';
  \""
```

---

## Phase A — Quick Wins
*Items: Q35, Q56, Q59 | Est: 2–3 hours*

### Task A1: Reason for Referral field (Q59)

**Files:**
- Create: `database/migrations/2026_06_30_000001_add_reason_for_referral_to_patients_table.php`
- Modify: `app/Http/Controllers/PatientController.php` — add to store/update validation
- Modify: `resources/views/patients/create.blade.php` — add field
- Modify: `resources/views/patients/show.blade.php` — display + inline edit

**Why:** Samy needs to capture case type (RTW, Expert Witness, VTA, etc.) per patient as the workflow is the same but reason differs.

- [x] **Step 1: Create migration**

```php
// database/migrations/2026_06_30_000001_add_reason_for_referral_to_patients_table.php
public function up(): void
{
    Schema::table('patients', function (Blueprint $table) {
        $table->string('reason_for_referral', 100)->nullable()->after('condition');
    });
}
public function down(): void
{
    Schema::table('patients', function (Blueprint $table) {
        $table->dropColumn('reason_for_referral');
    });
}
```

- [x] **Step 2: Run migration locally**
```bash
cd C:\xampp\htdocs\VTA_NEW\vta-portal
C:\xampp\php\php.exe artisan migrate
```
Expected: `2026_06_30_000001 ... done`

- [x] **Step 3: Add to PatientController validation (store + update)**

In `app/Http/Controllers/PatientController.php`, in both `store()` and `update()` validation arrays, add:
```php
'reason_for_referral' => 'nullable|string|max:100',
```

- [x] **Step 4: Add field to patient create form**

In `resources/views/patients/create.blade.php`, after the `condition` field block, add:
```html
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Referral</label>
    <select name="reason_for_referral" class="w-full rounded-lg border-gray-300 text-sm">
        <option value="">— Select —</option>
        <option value="Return to Work">Return to Work (RTW)</option>
        <option value="Expert Witness">Expert Witness</option>
        <option value="VTA Treatment">VTA Treatment</option>
        <option value="Case Management">Case Management</option>
        <option value="Other">Other</option>
    </select>
</div>
```

- [x] **Step 5: Display on patient show page**

In `resources/views/patients/show.blade.php`, in the patient details card, add alongside condition/location:
```html
@if($patient->reason_for_referral)
<div class="flex items-center gap-2">
    <span class="text-xs text-gray-400 w-32 flex-shrink-0">Reason</span>
    <span class="text-sm font-medium text-gray-800">{{ $patient->reason_for_referral }}</span>
</div>
@endif
```

- [x] **Step 6: Commit**
```bash
git add database/migrations/2026_06_30_000001_add_reason_for_referral_to_patients_table.php \
        app/Http/Controllers/PatientController.php \
        resources/views/patients/create.blade.php \
        resources/views/patients/show.blade.php
git commit -m "feat: add reason_for_referral field to patients (Q59)"
```

- [x] **Step 7: Deploy to Bluehost**
```powershell
scp -i "D:\blue\bluehost-key" `
  "C:\xampp\htdocs\VTA_NEW\vta-portal\database\migrations\2026_06_30_000001_add_reason_for_referral_to_patients_table.php" `
  "root@129.121.92.159:/var/www/nett-apps/vta-portal/database/migrations/"

scp -i "D:\blue\bluehost-key" `
  "C:\xampp\htdocs\VTA_NEW\vta-portal\app\Http\Controllers\PatientController.php" `
  "C:\xampp\htdocs\VTA_NEW\vta-portal\resources\views\patients\create.blade.php" `
  "C:\xampp\htdocs\VTA_NEW\vta-portal\resources\views\patients\show.blade.php" `
  "root@129.121.92.159:/var/www/nett-apps/vta-portal/..."

ssh -i "D:\blue\bluehost-key" root@129.121.92.159 \
  "cd /var/www/nett-apps/vta-portal && php artisan migrate && php artisan view:clear"
```

---

### Task A2: 20% Fund Balance Warning (Q56)

**Files:**
- Modify: `app/Services/FundingBalanceService.php` — add `isLowBalance()` helper
- Modify: `resources/views/patients/show.blade.php` — show warning badge on active funding cycle

**Why:** Samy wants a visual alert when remaining fund drops to ≤20% of approved amount so treatment isn't interrupted.

- [x] **Step 1: Add `isLowBalance()` to FundingBalanceService**

In `app/Services/FundingBalanceService.php`, add after `willExceedBalance()`:
```php
public function isLowBalance(FundingCycle $cycle, float $threshold = 20.0): bool
{
    return $this->usagePercentage($cycle) >= (100 - $threshold);
}
```

- [x] **Step 2: Pass balance data in PatientController::show()**

In `app/Http/Controllers/PatientController.php`, the `show()` method already injects `$fundingBalanceService`. Find the line that returns the view and ensure `$fundingBalanceService` is passed. If it already is, no change needed. If not:
```php
return view('patients.show', compact(
    'patient', 'associates', 'documentTypes',
    'fundingBalanceService', 'timeline', 'allowedTransitions'
));
```

- [x] **Step 3: Add warning badge to patient show page**

In `resources/views/patients/show.blade.php`, find where the active funding cycle is displayed. After the funding cycle card heading, add:
```html
@php
    $activeCycle = $patient->fundingCycles->where('is_active', true)->first();
@endphp
@if($activeCycle && $fundingBalanceService->isLowBalance($activeCycle))
<div class="flex items-center gap-2 rounded-lg px-3 py-2 mb-3"
     style="background:#fef9c3;border:1px solid #fde047;">
    <i class="fa-solid fa-triangle-exclamation" style="color:#ca8a04;"></i>
    <span class="text-sm font-medium" style="color:#854d0e;">
        Low funding warning — only
        £{{ number_format($fundingBalanceService->remainingBalance($activeCycle), 2) }}
        remaining ({{ round(100 - $fundingBalanceService->usagePercentage($activeCycle), 1) }}% left)
    </span>
</div>
@endif
```

- [x] **Step 4: Commit**
```bash
git add app/Services/FundingBalanceService.php resources/views/patients/show.blade.php
git commit -m "feat: 20% fund balance warning on patient page (Q56)"
```

---

### Task A3: Nearest Associate suggestion on Enquiry (Q35)

**Files:**
- Modify: `app/Http/Controllers/EnquiryController.php` — pass nearest associates to show view
- Modify: `resources/views/enquiries/show.blade.php` — show suggestion card

**Why:** Samy wants a hint on the enquiry as to which associate is geographically closest (by region match on the patient/enquiry location).

- [x] **Step 1: Pass nearest associates in EnquiryController::show()**

In `app/Http/Controllers/EnquiryController.php`, inside `show()`, add before the return:
```php
use App\Models\Associate;

// Suggest associates whose region appears in the enquiry location text
$location = $enquiry->company?->location ?? '';
$nearestAssociates = Associate::where('is_active', true)
    ->when($location, function ($q) use ($location) {
        $q->where(function ($inner) use ($location) {
            $inner->whereRaw('LOWER(?) LIKE CONCAT("%", LOWER(region), "%")', [$location])
                  ->orWhereRaw('LOWER(region) LIKE CONCAT("%", LOWER(?), "%")', [$location]);
        });
    })
    ->orderBy('name')
    ->get();

return view('enquiries.show', compact('enquiry', 'nearestAssociates', ...));
```

- [x] **Step 2: Add suggestion card to enquiry show page**

In `resources/views/enquiries/show.blade.php`, after the enquiry details card, add:
```html
@if($nearestAssociates->isNotEmpty())
<div class="rounded-xl border p-4 mb-4" style="border-color:#bfdbfe;background:#eff6ff;">
    <h3 class="text-sm font-semibold mb-2" style="color:#1e40af;">
        <i class="fa-solid fa-location-dot mr-1"></i> Suggested Associates (by region)
    </h3>
    <div class="flex flex-wrap gap-2">
        @foreach($nearestAssociates as $assoc)
        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
              style="background:#dbeafe;color:#1e3a8a;">
            {{ $assoc->name }}
            <span class="text-xs opacity-60">— {{ $assoc->region }}</span>
        </span>
        @endforeach
    </div>
</div>
@endif
```

- [x] **Step 3: Commit**
```bash
git add app/Http/Controllers/EnquiryController.php resources/views/enquiries/show.blade.php
git commit -m "feat: nearest associate suggestion on enquiry page (Q35)"
```

---

## Phase B — Enquiry Qualification Workflow
*Items: Q19, Q20, Q21 | Est: 3–4 hours*

**Background:** The DB already has `qualified_as_referral`, `qualified_date`, `qualified_remarks` on `enquiries` (migration `2026_06_27_100001`). Also `enquiry_contacts` table exists (migration `2026_06_28_100001`). What's missing is the UI action on the enquiry show page.

### Task B1: "Qualify as Referral" admin action (Q19, Q20)

**Files:**
- Modify: `app/Http/Controllers/EnquiryController.php` — add `qualify()` method
- Modify: `routes/web.php` — add PATCH route
- Modify: `resources/views/enquiries/show.blade.php` — add qualify panel

- [x] **Step 1: Add qualify() to EnquiryController**

```php
public function qualify(Request $request, Enquiry $enquiry): RedirectResponse
{
    // Only admin can qualify
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    $data = $request->validate([
        'qualified_date'    => 'required|date',
        'qualified_remarks' => 'nullable|string|max:1000',
    ]);

    $enquiry->update([
        'qualified_as_referral' => true,
        'qualified_date'        => $data['qualified_date'],
        'qualified_remarks'     => $data['qualified_remarks'] ?? null,
        'status'                => 'Qualified',
    ]);

    return redirect()->route('enquiries.show', $enquiry)
        ->with('success', 'Enquiry marked as Qualified Referral.');
}
```

- [x] **Step 2: Add route in routes/web.php**

Inside the auth middleware group, after the existing enquiry routes:
```php
Route::patch('/enquiries/{enquiry}/qualify', [EnquiryController::class, 'qualify'])
    ->name('enquiries.qualify');
```

- [x] **Step 3: Add qualify panel to enquiry show page**

In `resources/views/enquiries/show.blade.php`, after the communications section, add:
```html
@if(Auth::user()->role === 'admin')
<div class="rounded-xl border p-5 mt-4"
     style="border-color:{{ $enquiry->qualified_as_referral ? '#bbf7d0' : '#fed7aa' }};
            background:{{ $enquiry->qualified_as_referral ? '#f0fdf4' : '#fff7ed' }};">

    @if($enquiry->qualified_as_referral)
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
            <span class="font-semibold text-sm" style="color:#15803d;">Qualified as Referral</span>
            <span class="text-xs text-gray-400 ml-2">
                on {{ $enquiry->qualified_date?->format('d M Y') }}
            </span>
        </div>
        @if($enquiry->qualified_remarks)
        <p class="text-sm mt-2" style="color:#166534;">{{ $enquiry->qualified_remarks }}</p>
        @endif
    @else
        <h3 class="text-sm font-semibold mb-3" style="color:#9a3412;">
            <i class="fa-solid fa-user-check mr-1"></i> Qualify as Referral (Admin only)
        </h3>
        <form method="POST" action="{{ route('enquiries.qualify', $enquiry) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Qualification date</label>
                    <input type="date" name="qualified_date" value="{{ now()->toDateString() }}"
                           required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remarks (optional)</label>
                    <input type="text" name="qualified_remarks"
                           placeholder="e.g. First assessment approved by AXA"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
            <div class="flex justify-end mt-3">
                <button type="submit"
                        style="background:#ea580c;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    <i class="fa-solid fa-check mr-1"></i> Mark as Qualified Referral
                </button>
            </div>
        </form>
    @endif
</div>
@endif
```

- [x] **Step 4: Commit**
```bash
git add app/Http/Controllers/EnquiryController.php \
        routes/web.php \
        resources/views/enquiries/show.blade.php
git commit -m "feat: admin-only qualify-as-referral action on enquiry (Q19/Q20)"
```

---

### Task B2: Lead Professional / Additional Contact field (Q21)

**Background:** `enquiry_contacts` table already exists (migration `2026_06_28_100001`). Check if there's already a contacts section on the enquiry show page. If yes, ensure the "Lead Professional" role is an option in the contact type.

**Files:**
- Modify: `resources/views/enquiries/show.blade.php` — ensure contact form has "Lead Professional" role option
- Modify: `app/Http/Controllers/EnquiryContactController.php` (if exists) — or add inline in EnquiryController

- [x] **Step 1: Check enquiry_contacts table schema**
```powershell
ssh -i "D:\blue\bluehost-key" root@129.121.92.159 `
  "cd /var/www/nett-apps/vta-portal && php artisan tinker --execute=""echo implode(', ', Schema::getColumnListing('enquiry_contacts'));\"""
```

- [x] **Step 2: Ensure 'Lead Professional' appears as role option in the contacts form on enquiry show page**

Find the contact form in `resources/views/enquiries/show.blade.php` and verify these role options exist (add if missing):
```html
<select name="role" class="w-full rounded-lg border-gray-300 text-sm">
    <option value="Case Manager">Case Manager</option>
    <option value="Lead Professional">Lead Professional</option>
    <option value="Line Manager">Line Manager</option>
    <option value="Solicitor">Solicitor</option>
    <option value="Deputy">Deputy</option>
    <option value="Other">Other</option>
</select>
```

- [x] **Step 3: Commit**
```bash
git add resources/views/enquiries/show.blade.php
git commit -m "feat: add Lead Professional role option for enquiry contacts (Q21)"
```

---

## Phase C — Assessment Module UI
*Items: Q24, Q25, Q26, Q27 | Est: 3–4 hours*

**Background:** `assessments` table, `Assessment` model, `AssessmentController`, and `assessments/edit.blade.php` already exist. `vta_invoices` has `assessment_id` FK (migration `2026_06_27_100005b`). What's missing:
1. Assessment create/summary section on `patients/show.blade.php`
2. Link from assessment to VTA invoice creation
3. Report recipients section (using `special_instructions` field)

### Task C1: Assessment section on Patient Show page (Q24, Q25, Q26, Q27)

**Files:**
- Modify: `resources/views/patients/show.blade.php` — add assessment section with create form or edit link
- Modify: `resources/views/assessments/edit.blade.php` — ensure all fields present including special_instructions

- [x] **Step 1: Add Assessment section to patients/show.blade.php**

Find a logical position (after the patient details card, before funding cycles). Add:
```html
{{-- ══ ASSESSMENT ══ --}}
<div class="rounded-xl border border-purple-200 bg-white p-5 mb-4">
    <h2 class="text-base font-semibold text-gray-800 mb-4">
        <i class="fa-solid fa-stethoscope mr-2 text-purple-500"></i> Initial Assessment
    </h2>

    @if($patient->assessment)
        @php $a = $patient->assessment; @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm mb-4">
            <div><span class="text-xs text-gray-400 block">Assessor</span>{{ $a->assessor ?? '—' }}</div>
            <div><span class="text-xs text-gray-400 block">Assessment Date</span>{{ $a->assessment_date?->format('d M Y') ?? '—' }}</div>
            <div><span class="text-xs text-gray-400 block">Venue</span>{{ $a->venue ?? '—' }}</div>
            <div><span class="text-xs text-gray-400 block">Fee Agreed</span>{{ $a->fee_agreed_amount ? '£'.number_format($a->fee_agreed_amount,2) : '—' }}</div>
            <div><span class="text-xs text-gray-400 block">Assessment Cost</span>{{ $a->assessment_cost ? '£'.number_format($a->assessment_cost,2) : '—' }}</div>
            <div>
                <span class="text-xs text-gray-400 block">Report Sent</span>
                @if($a->report_sent)
                    <span class="text-green-600 font-medium">✓ Yes</span>
                    @if($a->report_document_path)
                        <a href="{{ Storage::url($a->report_document_path) }}" target="_blank"
                           class="text-xs text-blue-500 ml-1">View report</a>
                    @endif
                @else
                    <span class="text-gray-400">No</span>
                @endif
            </div>
        </div>
        @if($a->special_instructions)
        <div class="rounded-lg p-3 mb-3" style="background:#fefce8;border:1px solid #fde047;">
            <p class="text-xs font-semibold text-yellow-700 mb-1">Special Instructions / Report Recipients</p>
            <p class="text-sm text-yellow-900">{{ $a->special_instructions }}</p>
        </div>
        @endif
        <a href="{{ route('assessments.edit', $a) }}"
           class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium px-4 py-2"
           style="background:#7c3aed;color:#fff;">
            <i class="fa-solid fa-pen-to-square"></i> Edit Assessment
        </a>
        {{-- Link to create VTA invoice for this assessment --}}
        @if(!$a->vtaInvoices?->count())
        <a href="{{ route('vta-invoices.create', ['patient_id' => $patient->id, 'assessment_id' => $a->id]) }}"
           class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium px-4 py-2 ml-2"
           style="background:#0092b4;color:#fff;">
            <i class="fa-solid fa-file-invoice-dollar"></i> Invoice Assessment Fee
        </a>
        @endif

    @else
        <p class="text-sm text-gray-400 mb-3">No assessment recorded yet.</p>
        <form method="POST" action="{{ route('patients.assessments.store', $patient) }}"
              enctype="multipart/form-data"
              x-data="{ open: false }">
            @csrf
            <button type="button" @click="open = !open"
                    class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium px-4 py-2"
                    style="background:#7c3aed;color:#fff;">
                <i class="fa-solid fa-plus"></i> Record Initial Assessment
            </button>
            <div x-show="open" x-transition class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assessor</label>
                    <input type="text" name="assessor" placeholder="Name of assessor"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assessment Date</label>
                    <input type="date" name="assessment_date" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Venue</label>
                    <input type="text" name="venue" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fee Agreed (£)</label>
                    <input type="number" name="fee_agreed_amount" step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assessment Cost (£)</label>
                    <input type="number" name="assessment_cost" step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date Client Contacted</label>
                    <input type="date" name="date_client_contacted"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Special Instructions / Report Recipients
                        <span class="font-normal text-gray-400">(who gets report, invoice, cost estimation)</span>
                    </label>
                    <textarea name="special_instructions" rows="3"
                              placeholder="e.g. Report to Case Manager Jane, invoice to AXA, cost estimation cc'd to Solicitor…"
                              class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit"
                            style="background:#7c3aed;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        <i class="fa-solid fa-save mr-1"></i> Save Assessment
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
```

- [x] **Step 2: Ensure AssessmentController route uses nested patient route**

In `routes/web.php`, verify this route exists (add if missing):
```php
Route::post('/patients/{patient}/assessments', [AssessmentController::class, 'store'])
    ->name('patients.assessments.store');
```

- [x] **Step 3: Add assessment_id to VTA invoice create (Q26)**

In `resources/views/vta-invoices/create.blade.php`, check if `assessment_id` is passed as a hidden field from the URL. Add if missing:
```html
@if(request('assessment_id'))
<input type="hidden" name="assessment_id" value="{{ request('assessment_id') }}">
@endif
```

In `app/Http/Controllers/VtaInvoiceController.php` `store()` validation, ensure:
```php
'assessment_id' => 'nullable|exists:assessments,id',
```

- [x] **Step 4: Commit**
```bash
git add resources/views/patients/show.blade.php \
        routes/web.php \
        resources/views/vta-invoices/create.blade.php \
        app/Http/Controllers/VtaInvoiceController.php
git commit -m "feat: assessment section on patient page with create form and invoice link (Q24-Q27)"
```

---

## Phase D — Associate HR & Compliance Module
*Items: Q54 | Est: 4–5 hours*

**Background:** CV upload already exists. Need to add: DBS check, Professional Registration, Contract, CSP membership — each with expiry date. Plus hourly rate. Plus dashboard alert when any document expires within 90 days.

### Task D1: Associate compliance documents table

**Files:**
- Create: `database/migrations/2026_06_30_000002_create_associate_compliance_documents_table.php`
- Create: `app/Models/AssociateComplianceDocument.php`

- [x] **Step 1: Create migration**

```php
Schema::create('associate_compliance_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('associate_id')->constrained()->cascadeOnDelete();
    $table->enum('document_type', [
        'DBS Check',
        'Professional Registration',
        'Contract',
        'CSP Membership',
        'Insurance',
        'Other',
    ]);
    $table->string('document_path')->nullable();
    $table->date('expiry_date')->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

- [x] **Step 2: Run migration**
```bash
C:\xampp\php\php.exe artisan migrate
```

- [x] **Step 3: Create model**

`app/Models/AssociateComplianceDocument.php`:
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociateComplianceDocument extends Model
{
    protected $fillable = [
        'associate_id', 'document_type', 'document_path', 'expiry_date', 'notes', 'uploaded_by',
    ];

    protected $casts = ['expiry_date' => 'date'];

    public function associate() { return $this->belongsTo(Associate::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function isExpiringSoon(int $days = 90): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
```

- [x] **Step 4: Add hourly_rate to associates table**

Create `database/migrations/2026_06_30_000003_add_hourly_rate_to_associates_table.php`:
```php
Schema::table('associates', function (Blueprint $table) {
    $table->decimal('hourly_rate', 8, 2)->nullable()->after('region');
});
```

Run: `C:\xampp\php\php.exe artisan migrate`

- [x] **Step 5: Commit migrations + model**
```bash
git add database/migrations/ app/Models/AssociateComplianceDocument.php
git commit -m "feat: associate compliance documents table + hourly_rate (Q54)"
```

---

### Task D2: Compliance UI on Associate settings page

**Files:**
- Modify: `app/Http/Controllers/SettingsController.php` — add storeCompliance(), uploadCompliance() methods
- Modify: `routes/web.php` — add compliance routes
- Modify: `resources/views/settings/associates/show.blade.php` — add HR/Compliance section

- [x] **Step 1: Add compliance methods to SettingsController**

```php
public function storeCompliance(Request $request, Associate $associate): RedirectResponse
{
    $data = $request->validate([
        'document_type' => 'required|string|max:50',
        'expiry_date'   => 'nullable|date',
        'notes'         => 'nullable|string|max:500',
        'document'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
    ]);

    if ($request->hasFile('document')) {
        $data['document_path'] = $request->file('document')
            ->store("associates/{$associate->id}/compliance", 'vta-documents');
    }

    $data['uploaded_by'] = Auth::id();
    unset($data['document']);

    $associate->complianceDocuments()->create($data);

    return back()->with('success', 'Compliance document saved.');
}
```

- [x] **Step 2: Add routes**

```php
Route::post('/settings/associates/{associate}/compliance',
    [SettingsController::class, 'storeCompliance'])
    ->name('settings.associates.compliance.store');
```

- [x] **Step 3: Add hourly_rate to Associate update validation**

In `SettingsController::updateAssociate()`, add:
```php
'hourly_rate' => 'nullable|numeric|min:0',
```

- [x] **Step 4: Add HR & Compliance section to associate show blade**

In `resources/views/settings/associates/show.blade.php`, after the CV section, add:
```html
{{-- ══ HR & COMPLIANCE ══ --}}
<div class="rounded-xl border border-amber-200 bg-white p-5 mb-4">
    <h2 class="text-base font-semibold text-gray-800 mb-4">
        <i class="fa-solid fa-shield-halved mr-2 text-amber-500"></i> HR & Compliance
    </h2>

    {{-- Hourly rate --}}
    <form method="POST" action="{{ route('settings.associates.update', $associate) }}"
          class="flex items-end gap-3 mb-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Hourly Rate (£)</label>
            <input type="number" name="hourly_rate" step="0.01" min="0"
                   value="{{ $associate->hourly_rate }}"
                   class="rounded-lg border-gray-300 text-sm w-40">
        </div>
        <button type="submit"
                style="background:#0092b4;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
            Save Rate
        </button>
    </form>

    {{-- Existing compliance docs --}}
    @if($associate->complianceDocuments->isNotEmpty())
    <div class="mb-4 space-y-2">
        @foreach($associate->complianceDocuments->sortBy('document_type') as $doc)
        @php
            $isExpired = $doc->isExpired();
            $isSoon    = !$isExpired && $doc->isExpiringSoon();
            $borderStyle = $isExpired ? 'border-color:#fca5a5;background:#fef2f2;'
                         : ($isSoon   ? 'border-color:#fde68a;background:#fffbeb;'
                                      : 'border-color:#d1fae5;background:#f0fdf4;');
        @endphp
        <div class="flex items-center justify-between rounded-lg border px-3 py-2" style="{{ $borderStyle }}">
            <div>
                <span class="text-sm font-medium text-gray-800">{{ $doc->document_type }}</span>
                @if($doc->expiry_date)
                <span class="text-xs ml-2 {{ $isExpired ? 'text-red-600 font-semibold' : ($isSoon ? 'text-amber-600 font-semibold' : 'text-gray-400') }}">
                    {{ $isExpired ? 'EXPIRED' : ($isSoon ? 'Expires soon' : 'Expires') }}
                    {{ $doc->expiry_date->format('d M Y') }}
                </span>
                @endif
                @if($doc->document_path)
                <a href="{{ Storage::url($doc->document_path) }}" target="_blank"
                   class="text-xs text-blue-500 ml-2">View</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add new compliance doc --}}
    <form method="POST"
          action="{{ route('settings.associates.compliance.store', $associate) }}"
          enctype="multipart/form-data"
          class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Document Type</label>
            <select name="document_type" required class="w-full rounded-lg border-gray-300 text-sm">
                <option value="">— Select —</option>
                <option value="DBS Check">DBS Check</option>
                <option value="Professional Registration">Professional Registration</option>
                <option value="Contract">Contract</option>
                <option value="CSP Membership">CSP Membership</option>
                <option value="Insurance">Insurance</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date (optional)</label>
            <input type="date" name="expiry_date" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Upload Document</label>
            <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   class="w-full text-sm text-gray-600">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
            <input type="text" name="notes" placeholder="e.g. Renewal pending"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div class="flex items-end">
            <button type="submit"
                    style="background:#d97706;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;width:100%;">
                <i class="fa-solid fa-plus mr-1"></i> Add Document
            </button>
        </div>
    </form>
</div>
```

- [x] **Step 5: Add complianceDocuments relationship to Associate model**

In `app/Models/Associate.php`, add:
```php
public function complianceDocuments()
{
    return $this->hasMany(AssociateComplianceDocument::class)->orderBy('document_type');
}
```

- [x] **Step 6: Load compliance docs in SettingsController::showAssociate()**

In `SettingsController::showAssociate()`, eager-load:
```php
$associate->load(['user', 'patients', 'complianceDocuments']);
```

- [x] **Step 7: Commit**
```bash
git add app/Http/Controllers/SettingsController.php \
        app/Models/Associate.php \
        resources/views/settings/associates/show.blade.php \
        routes/web.php
git commit -m "feat: associate HR compliance section (DBS, CSP, contract, expiry tracking) (Q54)"
```

---

### Task D3: Compliance expiry alert on Dashboard (Q54 + Q55)

**Files:**
- Modify: `app/Http/Controllers/DashboardController.php` — add expiring docs query
- Modify: `resources/views/dashboard.blade.php` (or wherever the main dashboard blade is) — add alert card

- [x] **Step 1: Add expiring docs query in DashboardController**

```php
use App\Models\AssociateComplianceDocument;

// Docs expiring within 90 days (or already expired)
$expiringDocs = AssociateComplianceDocument::with('associate')
    ->where(function ($q) {
        $q->where('expiry_date', '<=', now()->addDays(90))
          ->where('expiry_date', '>=', now()->subDays(30)); // show for 30 days after expiry
    })
    ->orderBy('expiry_date')
    ->get();
```

Pass `$expiringDocs` to the dashboard view.

- [x] **Step 2: Add alert card to dashboard view**

Find the dashboard blade (likely `resources/views/dashboard.blade.php` or similar). Add:
```html
@if($expiringDocs->isNotEmpty())
<div class="rounded-xl border mb-4 p-4" style="border-color:#fde68a;background:#fffbeb;">
    <h3 class="text-sm font-semibold mb-2" style="color:#92400e;">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
        Associate Compliance Alerts ({{ $expiringDocs->count() }})
    </h3>
    <div class="space-y-1">
        @foreach($expiringDocs as $doc)
        @php $expired = $doc->expiry_date->isPast(); @endphp
        <div class="flex items-center justify-between text-sm">
            <span>
                <span class="font-medium">{{ $doc->associate->name }}</span>
                — {{ $doc->document_type }}
            </span>
            <span class="{{ $expired ? 'text-red-600 font-semibold' : 'text-amber-600' }}">
                {{ $expired ? 'EXPIRED' : 'Expires' }} {{ $doc->expiry_date->format('d M Y') }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif
```

- [x] **Step 3: Commit**
```bash
git add app/Http/Controllers/DashboardController.php resources/views/dashboard.blade.php
git commit -m "feat: compliance expiry alert on dashboard (Q54/Q55)"
```

---

## Phase E — Associate Portal Enhancements
*Items: Q47, Q57, Q65 | Est: 4–5 hours*

### Task E1: Case Note Revision Flag (Q47)

**Background:** `needs_review` boolean already exists on `case_notes`. What's missing is admin feedback text back to the associate.

**Files:**
- Create: `database/migrations/2026_06_30_000004_add_review_feedback_to_case_notes_table.php`
- Modify: `app/Http/Controllers/CaseNoteController.php` — add feedback/signoff flow
- Modify: `resources/views/case-notes/show.blade.php` (or admin case note view) — add feedback form

- [x] **Step 1: Add review_feedback column**

```php
Schema::table('case_notes', function (Blueprint $table) {
    $table->text('review_feedback')->nullable()->after('needs_review');
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('review_feedback');
    $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
});
```

- [x] **Step 2: Add sendFeedback() to CaseNoteController**

```php
public function sendFeedback(Request $request, CaseNote $caseNote): RedirectResponse
{
    $data = $request->validate([
        'review_feedback' => 'required|string|max:2000',
    ]);

    $caseNote->update([
        'review_feedback' => $data['review_feedback'],
        'needs_review'    => true,
        'reviewed_by'     => Auth::id(),
        'reviewed_at'     => now(),
        'is_signed_off'   => false,
    ]);

    return back()->with('success', 'Feedback sent to associate.');
}
```

- [x] **Step 3: Add route**

```php
Route::patch('/case-notes/{caseNote}/feedback',
    [CaseNoteController::class, 'sendFeedback'])
    ->name('case-notes.feedback');
```

- [x] **Step 4: Add feedback panel to case note show/list views**

In the admin case notes view, for each note with `needs_review = true`, show:
```html
@if($note->review_feedback)
<div class="rounded-lg p-3 mb-2" style="background:#fef3c7;border:1px solid #fde68a;">
    <p class="text-xs font-semibold text-amber-700 mb-1">
        Feedback from {{ $note->reviewer?->name }} on {{ $note->reviewed_at?->format('d M Y') }}
    </p>
    <p class="text-sm text-amber-900">{{ $note->review_feedback }}</p>
</div>
@endif
<form method="POST" action="{{ route('case-notes.feedback', $note) }}">
    @csrf @method('PATCH')
    <textarea name="review_feedback" rows="2" placeholder="Send revision feedback to associate…"
              class="w-full rounded-lg border-gray-300 text-sm mb-2">{{ $note->review_feedback }}</textarea>
    <button type="submit"
            style="background:#d97706;color:#fff;padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
        Send Feedback
    </button>
</form>
```

- [x] **Step 5: Show review feedback to associate on their portal**

In the associate portal patient view (`resources/views/portal/associate/patient.blade.php`), for each case note:
```html
@if($note->review_feedback && !$note->is_signed_off)
<div class="rounded-lg p-3 mt-2" style="background:#fef3c7;border:1px solid #fde68a;">
    <p class="text-xs font-semibold text-amber-700 mb-1">
        <i class="fa-solid fa-comment-dots mr-1"></i>
        Revision requested — {{ $note->reviewed_at?->format('d M Y') }}
    </p>
    <p class="text-sm text-amber-900">{{ $note->review_feedback }}</p>
</div>
@endif
```

- [x] **Step 6: Commit**
```bash
git add database/migrations/2026_06_30_000004_* \
        app/Http/Controllers/CaseNoteController.php \
        routes/web.php \
        resources/views/
git commit -m "feat: case note revision feedback loop from admin to associate (Q47)"
```

---

### Task E2: Associate Invoice Creation (Q65)

**Background:** `associate_invoices` table and `AssociateInvoiceController` already exist. Associates need to be able to create invoices from their portal (currently admin-only).

**Files:**
- Modify: `app/Http/Controllers/AssociatePortalController.php` — add createInvoice(), storeInvoice()
- Modify: `routes/web.php` — add associate portal invoice routes
- Create: `resources/views/portal/associate/create-invoice.blade.php`
- Modify: `app/Http/Controllers/DashboardController.php` — add pending associate invoice notification

- [x] **Step 1: Add invoice methods to AssociatePortalController**

```php
public function createInvoice(Request $request)
{
    $associate = $this->getAssociate();
    $patients  = $associate->patients()->whereNull('patient_associates.end_date')->get();
    return view('portal.associate.create-invoice', compact('associate', 'patients'));
}

public function storeInvoice(Request $request)
{
    $associate = $this->getAssociate();

    $data = $request->validate([
        'patient_id'          => 'required|exists:patients,id',
        'invoice_date'        => 'required|date',
        'sessions_completed'  => 'nullable|integer|min:0',
        'travel_miles'        => 'nullable|numeric|min:0',
        'session_amount'      => 'nullable|numeric|min:0',
        'travel_amount'       => 'nullable|numeric|min:0',
        'total_amount'        => 'required|numeric|min:0',
        'notes'               => 'nullable|string|max:1000',
    ]);

    // Ensure associate is assigned to this patient
    $isAssigned = $associate->patients()
        ->where('patient_id', $data['patient_id'])
        ->whereNull('patient_associates.end_date')
        ->exists();

    if (!$isAssigned) abort(403);

    $data['associate_id'] = $associate->id;
    $data['status']       = 'Submitted';

    \App\Models\AssociateInvoice::create($data);

    return redirect()->route('associate-portal.dashboard')
        ->with('success', 'Invoice submitted for admin review.');
}
```

- [x] **Step 2: Add routes**

```php
Route::get('/associate-portal/invoices/create',
    [AssociatePortalController::class, 'createInvoice'])
    ->name('associate-portal.invoices.create');
Route::post('/associate-portal/invoices',
    [AssociatePortalController::class, 'storeInvoice'])
    ->name('associate-portal.invoices.store');
```

- [x] **Step 3: Create invoice form blade**

`resources/views/portal/associate/create-invoice.blade.php`:
```html
<x-app-layout>
    <x-slot name="header">Submit Invoice</x-slot>

    <div class="max-w-xl mx-auto">
        <form method="POST" action="{{ route('associate-portal.invoices.store') }}"
              class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                <select name="patient_id" required class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">— Select patient —</option>
                    @foreach($patients as $p)
                    <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                <input type="date" name="invoice_date" value="{{ now()->toDateString() }}"
                       required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sessions Completed</label>
                    <input type="number" name="sessions_completed" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Session Amount (£)</label>
                    <input type="number" name="session_amount" step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Travel Miles</label>
                    <input type="number" name="travel_miles" step="0.1" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Travel Amount (£)</label>
                    <input type="number" name="travel_amount" step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (£)</label>
                <input type="number" name="total_amount" step="0.01" min="0" required
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Any notes for admin…"
                          class="w-full rounded-lg border-gray-300 text-sm"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                        style="background:#0092b4;color:#fff;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Submit Invoice
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
```

- [x] **Step 4: Add "Submit Invoice" button to associate portal dashboard**

In `resources/views/portal/associate/dashboard.blade.php` (or index), add:
```html
<a href="{{ route('associate-portal.invoices.create') }}"
   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium"
   style="background:#0092b4;color:#fff;">
    <i class="fa-solid fa-file-invoice-dollar"></i> Submit Invoice
</a>
```

- [x] **Step 5: Add pending associate invoices alert to admin dashboard**

In `DashboardController`, add:
```php
$pendingAssocInvoices = \App\Models\AssociateInvoice::where('status', 'Submitted')
    ->with('associate')
    ->latest()
    ->get();
```

In dashboard blade:
```html
@if($pendingAssocInvoices->isNotEmpty())
<div class="rounded-xl border mb-4 p-4" style="border-color:#bfdbfe;background:#eff6ff;">
    <h3 class="text-sm font-semibold mb-2" style="color:#1e40af;">
        <i class="fa-solid fa-file-invoice mr-1"></i>
        Associate Invoices Awaiting Approval ({{ $pendingAssocInvoices->count() }})
    </h3>
    @foreach($pendingAssocInvoices as $inv)
    <div class="flex justify-between text-sm py-1">
        <span>{{ $inv->associate->name }} — £{{ number_format($inv->total_amount, 2) }}</span>
        <a href="{{ route('associate-invoices.show', $inv) }}" class="text-blue-600 hover:underline">Review</a>
    </div>
    @endforeach
</div>
@endif
```

- [x] **Step 6: Commit**
```bash
git add app/Http/Controllers/AssociatePortalController.php \
        resources/views/portal/associate/ \
        app/Http/Controllers/DashboardController.php \
        routes/web.php
git commit -m "feat: associates can submit invoices via portal, admin dashboard alert (Q65)"
```

---

## Phase F — Clinical Head Review Dashboard
*Items: Q32, Q55 | Est: 2–3 hours*

### Task F1: Clinical Head review panel (Q32)

**Background:** `needs_review` flag already exists on case notes. Just need a dashboard section surfacing these for Samy.

**Files:**
- Modify: `app/Http/Controllers/DashboardController.php` — query flagged notes
- Modify: `resources/views/dashboard.blade.php` — add CH review panel

- [x] **Step 1: Query flagged case notes in DashboardController**

```php
use App\Models\CaseNote;

$reviewNotes = CaseNote::where('needs_review', true)
    ->where('is_signed_off', false)
    ->with(['patient', 'associate'])
    ->latest('session_date')
    ->get();
```

- [x] **Step 2: Add CH review panel to dashboard**

```html
@if($reviewNotes->isNotEmpty())
<div class="rounded-xl border mb-4 p-4" style="border-color:#e9d5ff;background:#faf5ff;">
    <h3 class="text-sm font-semibold mb-2" style="color:#6b21a8;">
        <i class="fa-solid fa-user-doctor mr-1"></i>
        Clinical Head Review Required ({{ $reviewNotes->count() }})
    </h3>
    <div class="space-y-2">
        @foreach($reviewNotes as $note)
        <div class="flex items-center justify-between text-sm">
            <span>
                <span class="font-medium">{{ $note->patient?->first_name }} {{ $note->patient?->last_name }}</span>
                <span class="text-gray-400 text-xs ml-1">— {{ $note->associate?->name }}</span>
                <span class="text-gray-400 text-xs ml-1">{{ $note->session_date?->format('d M Y') }}</span>
            </span>
            <a href="{{ route('case-notes.show', $note) }}"
               class="text-xs font-medium px-3 py-1 rounded-full"
               style="background:#e9d5ff;color:#6b21a8;">
                Review
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif
```

- [x] **Step 3: Commit**
```bash
git add app/Http/Controllers/DashboardController.php resources/views/dashboard.blade.php
git commit -m "feat: clinical head review panel on dashboard (Q32)"
```

---

## Phase G — Finance, Reports & Master Log
*Items: Q30, Q33, Q66 | Est: 4–5 hours*

### Task G1: Fund balance per patient in Accounts (Q30)

**Files:**
- Modify: `resources/views/patients/show.blade.php` — add detailed balance breakdown on funding cycle card

- [x] **Step 1: Enhanced balance display on patient show page**

In the funding cycles section of `patients/show.blade.php`, for each cycle, add:
```html
@foreach($patient->fundingCycles as $cycle)
@php
    $remaining = $fundingBalanceService->remainingBalance($cycle);
    $usagePct  = $fundingBalanceService->usagePercentage($cycle);
    $isLow     = $fundingBalanceService->isLowBalance($cycle);
@endphp
<div class="rounded-xl border border-gray-200 bg-white p-4 mb-3">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-800 text-sm">
            Cycle {{ $cycle->cycle_number }}
            @if($cycle->is_active)
            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs"
                  style="background:#dcfce7;color:#16a34a;">Active</span>
            @endif
        </h3>
        <span class="text-xs text-gray-400">{{ $cycle->funder_name }}</span>
    </div>

    {{-- Balance bar --}}
    <div class="mb-2">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Used: £{{ number_format($fundingBalanceService->invoicedAmount($cycle), 2) }}</span>
            <span class="{{ $isLow ? 'font-semibold' : '' }}"
                  style="{{ $isLow ? 'color:#b45309;' : '' }}">
                Remaining: £{{ number_format($remaining, 2) }}
            </span>
        </div>
        <div class="w-full rounded-full h-2" style="background:#e5e7eb;">
            <div class="h-2 rounded-full transition-all"
                 style="width:{{ min(100, $usagePct) }}%;
                        background:{{ $usagePct >= 80 ? '#ef4444' : ($usagePct >= 60 ? '#f59e0b' : '#22c55e') }};">
            </div>
        </div>
        <div class="text-xs text-right mt-0.5 text-gray-400">
            {{ $usagePct }}% of £{{ number_format($cycle->approved_amount, 2) }} used
        </div>
    </div>
</div>
@endforeach
```

- [x] **Step 2: Commit**
```bash
git add resources/views/patients/show.blade.php
git commit -m "feat: detailed fund balance bar per funding cycle on patient page (Q30)"
```

---

### Task G2: Financial Summary Reports (Q33)

**Files:**
- Modify: `app/Http/Controllers/ReportsController.php` — add financial summary queries
- Modify: `resources/views/reports/index.blade.php` — add financial summary section

- [x] **Step 1: Add financial summary data in ReportsController**

```php
use App\Models\VtaInvoice;
use App\Models\AssociateInvoice;
use App\Models\FundingCycle;
use App\Models\Patient;

// Financial summary
$financialSummary = [
    'vta_invoiced_ytd'    => VtaInvoice::whereYear('invoice_date', now()->year)->sum('total_amount'),
    'vta_paid_ytd'        => VtaInvoice::whereYear('invoice_date', now()->year)->where('status','Paid')->sum('total_amount'),
    'assoc_invoiced_ytd'  => AssociateInvoice::whereYear('invoice_date', now()->year)->sum('total_amount'),
    'assoc_paid_ytd'      => AssociateInvoice::whereYear('invoice_date', now()->year)->where('status','Paid')->sum('total_amount'),
];

// Active patients with low balance
$lowBalancePatients = Patient::where('status', 'Treatment Active')
    ->with(['fundingCycles' => fn($q) => $q->where('is_active', true)])
    ->get()
    ->filter(fn($p) => $p->fundingCycles->first()
        && app(\App\Services\FundingBalanceService::class)->isLowBalance($p->fundingCycles->first())
    );
```

Pass both to the reports view.

- [x] **Step 2: Add financial summary section to reports blade**

In `resources/views/reports/index.blade.php`, add at the top:
```html
{{-- Financial Summary --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-800">£{{ number_format($financialSummary['vta_invoiced_ytd'], 0) }}</div>
        <div class="text-xs text-gray-400 mt-1">VTA Invoiced YTD</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-green-600">£{{ number_format($financialSummary['vta_paid_ytd'], 0) }}</div>
        <div class="text-xs text-gray-400 mt-1">VTA Received YTD</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-800">£{{ number_format($financialSummary['assoc_invoiced_ytd'], 0) }}</div>
        <div class="text-xs text-gray-400 mt-1">Associate Invoiced YTD</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-red-600">£{{ number_format($financialSummary['assoc_paid_ytd'], 0) }}</div>
        <div class="text-xs text-gray-400 mt-1">Associate Paid YTD</div>
    </div>
</div>

{{-- Low balance patients --}}
@if($lowBalancePatients->isNotEmpty())
<div class="rounded-xl border p-4 mb-6" style="border-color:#fde68a;background:#fffbeb;">
    <h3 class="text-sm font-semibold mb-3" style="color:#92400e;">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
        Patients with Low Funding Balance (≤20% remaining)
    </h3>
    @foreach($lowBalancePatients as $p)
    @php $cycle = $p->fundingCycles->first(); $svc = app(\App\Services\FundingBalanceService::class); @endphp
    <div class="flex justify-between text-sm py-1 border-b border-yellow-100 last:border-0">
        <a href="{{ route('patients.show', $p) }}" class="font-medium text-amber-800 hover:underline">
            {{ $p->first_name }} {{ $p->last_name }}
        </a>
        <span class="text-amber-700">
            £{{ number_format($svc->remainingBalance($cycle), 2) }} left
            ({{ round(100 - $svc->usagePercentage($cycle), 1) }}%)
        </span>
    </div>
    @endforeach
</div>
@endif
```

- [x] **Step 3: Commit**
```bash
git add app/Http/Controllers/ReportsController.php resources/views/reports/index.blade.php
git commit -m "feat: financial summary report and low-balance patient list (Q33)"
```

---

### Task G3: Master Log page (Q66)

**Background:** Master log = admin-only view pulling a consolidated activity feed across the portal (patients, invoices, communications, case notes). Read-only. Balance = Approved funding − all expenses per patient.

**Files:**
- Modify: `app/Http/Controllers/ReportsController.php` — add masterLog() method
- Create: `resources/views/reports/master-log.blade.php`
- Modify: `routes/web.php` — add route

- [x] **Step 1: Add masterLog() to ReportsController**

```php
public function masterLog(Request $request)
{
    $patients = Patient::with([
        'caseManager.company',
        'fundingCycles',
        'vtaInvoices',
        'associateInvoices',
        'patientAssociates.associate',
    ])
    ->whereNotIn('status', ['Not Proceeding', 'Case Closed'])
    ->orderBy('last_name')
    ->get()
    ->map(function ($p) {
        $activeCycle    = $p->fundingCycles->where('is_active', true)->first();
        $svc            = app(\App\Services\FundingBalanceService::class);
        $approved       = $activeCycle?->approved_amount ?? 0;
        $vtaPaid        = $p->vtaInvoices->where('status', 'Paid')->sum('total_amount');
        $assocPaid      = $p->associateInvoices->where('status', 'Paid')->sum('total_amount');
        $totalExpenses  = $vtaPaid + $assocPaid;
        $balance        = max(0, $approved - $totalExpenses);

        return [
            'patient'        => $p,
            'active_cycle'   => $activeCycle,
            'approved'       => $approved,
            'vta_paid'       => $vtaPaid,
            'assoc_paid'     => $assocPaid,
            'balance'        => $balance,
        ];
    });

    return view('reports.master-log', compact('patients'));
}
```

- [x] **Step 2: Add route**

```php
Route::get('/reports/master-log', [ReportsController::class, 'masterLog'])
    ->name('reports.master-log');
```

- [x] **Step 3: Create master-log blade**

`resources/views/reports/master-log.blade.php`:
```html
<x-app-layout>
    <x-slot name="header">Master Log</x-slot>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm bg-white rounded-xl border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Approved (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">VTA Paid (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Assoc Paid (£)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance (£)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($patients as $row)
                @php $p = $row['patient']; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('patients.show', $p) }}"
                           class="font-medium text-[#0092b4] hover:underline">
                            {{ $p->first_name }} {{ $p->last_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $p->status }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $p->caseManager?->company?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">{{ $row['approved'] ? number_format($row['approved'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-green-700">{{ number_format($row['vta_paid'], 2) }}</td>
                    <td class="px-4 py-3 text-right text-red-600">{{ number_format($row['assoc_paid'], 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold
                        {{ $row['balance'] < ($row['approved'] * 0.2) && $row['approved'] > 0 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ number_format($row['balance'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
```

- [x] **Step 4: Add link to Master Log in reports nav**

In the reports view or sidebar, add:
```html
<a href="{{ route('reports.master-log') }}" class="...">Master Log</a>
```

- [x] **Step 5: Commit**
```bash
git add app/Http/Controllers/ReportsController.php \
        resources/views/reports/master-log.blade.php \
        routes/web.php
git commit -m "feat: master log page with balance per active patient (Q66)"
```

---

## Final Step — Deploy all phases to Bluehost

After completing all phases locally and verifying with `php artisan test --testsuite=Feature`:

```powershell
# 1. SCP all changed files
# (use robocopy or individual scp for each modified file)

# 2. On Bluehost: run migrations + clear caches
ssh -i "D:\blue\bluehost-key" root@129.121.92.159 `
  "cd /var/www/nett-apps/vta-portal && php artisan migrate && php artisan view:clear && php artisan route:clear && php artisan config:clear"

# 3. Update dev_status for all completed questions
ssh -i "D:\blue\bluehost-key" root@129.121.92.159 "cd /var/www/nett-apps/vta-portal && php artisan tinker --execute=\"
  App\Models\PortalFeedbackItem::whereIn('id',[19,20,21,24,25,26,27,30,32,33,35,47,54,55,56,57,59,65,66])
    ->update(['dev_status'=>'done']);
  echo 'all closed';
\""
```

---

## GDPR Notes (Q52, Q53)

These are policy decisions, not engineering tasks:
- **Q52**: Follow UK ICO guidelines — 8 years for clinical records
- **Q53**: Hold documents for 8 years per policy; delete/anonymise functionality to be added in a future phase

No dev work required now. Mark as Closed after adding a note in system settings or a GDPR policy document page.

---

## Summary

| Phase | Items | Est. Time |
|-------|-------|-----------|
| A — Quick wins | Q35, Q56, Q59 | 2–3 hrs |
| B — Enquiry qualification | Q19, Q20, Q21 | 3–4 hrs |
| C — Assessment module UI | Q24–Q27 | 3–4 hrs |
| D — Associate HR & Compliance | Q54 + Q55 partial | 4–5 hrs |
| E — Associate portal | Q47, Q57, Q65 | 4–5 hrs |
| F — Clinical Head review | Q32 + Q55 | 2–3 hrs |
| G — Finance, reports, log | Q30, Q33, Q66 | 4–5 hrs |
| **Total** | **26 items** | **~22–29 hrs** |
