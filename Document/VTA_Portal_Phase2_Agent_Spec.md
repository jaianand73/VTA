# VTA Portal — Phase 2 Development Specification
## Single Source of Truth for Code Agent

> **Version:** 1.2  
> **Created:** 2026-06-27  
> **Updated:** 2026-06-28 — All 18 Q&A answers incorporated. Q4 resolved by decision (single company). Associate Portal changes from Samy's process diagram added (AP1, AP2, AP3). All sprints fully unblocked.  
> **Author:** Jai Anand (HariKrishnan) — with AI analysis  
> **Status:** READY FOR CODE AGENT — all questions resolved, no blockers remaining.  
> **Staging URL:** `https://easyerp.co.in/vta-portal`  
> **Project path (local):** `C:\xampp\htdocs\VTA_NEW\vta-portal`  
> **Project path (EC2):** `/var/www/easyerp/WebSite/vta-portal`

---

## CRITICAL INSTRUCTIONS FOR CODE AGENT

Read the entire document before writing any code. This is the complete, authoritative specification for all Phase 2 development work.

**Rules:**
1. Build exactly what is specified. Do not invent features.
2. Every database change requires a new timestamped migration — never alter existing migration files.
3. Follow the build order in Part 8. Some items have dependencies that block others.
4. All business rules must be enforced server-side in the controller — not only in the frontend.
5. Use the exact paths, class names and column names specified. The codebase already exists; you are extending it.
6. Run `php artisan migrate` after each migration. Run `php artisan view:clear route:clear` after route/view changes.
7. Items marked **[BLOCKED — awaiting Samy's answer]** must NOT be built yet. Build the non-blocked items first.

---

## PART 1 — PROJECT CONTEXT

### 1.1 What the system is

The VTA Portal is a practice management system for **Vestibular Therapy Associates (VTA)**, a UK vestibular therapy practice run by Dr. Samy Selvanayagam. It replaced three systems: an Excel referral tracker, Qunote (clinical platform) and Xero (invoicing).

### 1.2 Current state of the codebase

Phase 1 (CRM, Clinical, Finance, Settings) is fully built and tested. The portal handles:

- Enquiries → Patients pipeline
- Associates, Companies, Case Managers
- Appointments and Case Notes
- Funding Cycles, Cost Estimations, Invoices (Associate + VTA)
- Settings, Email Intake, Document management

Phase 2 is the gap-filling and enhancement work defined in this document — based on analysis of Samy's live working documents (Excel process map, handwritten PDF clinical flow, PPTX portal overview).

### 1.3 Technology stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11.x, PHP 8.2+ |
| Frontend | Blade + Alpine.js + Tailwind CSS 3 |
| Database | MySQL 8.x |
| Auth | Laravel Breeze (Blade stack) |
| Calendar | FullCalendar.js 6 (CDN) |
| Charts | Chart.js 4 (CDN) |
| Alerts/confirms | SweetAlert2 (CDN) |
| Icons | Font Awesome 6 (CDN) |
| File Storage | Laravel Storage (`vta-documents` private disk) |
| PDF | barryvdh/laravel-dompdf |
| Email intake | webklex/php-imap |

### 1.4 Key paths

```
C:\xampp\htdocs\VTA_NEW\vta-portal\      # Local development root
├── app/Http/Controllers/                # All controllers
├── app/Models/                          # All Eloquent models
├── app/Services/                        # Service classes
├── database/migrations/                 # Migration files
├── database/seeders/                    # Seeder files
├── resources/views/                     # Blade templates
│   ├── layouts/app.blade.php            # Main layout with sidebar nav
│   └── portal-feedback/                 # Feedback board (completed)
└── routes/web.php                       # All routes
```

### 1.5 Running the project locally

```powershell
# PHP binary
C:\xampp\php\php.exe

# Run migration
& "C:\xampp\php\php.exe" artisan migrate

# Run specific migration
& "C:\xampp\php\php.exe" artisan migrate --path=database/migrations/FILENAME.php

# Clear caches
& "C:\xampp\php\php.exe" artisan view:clear
& "C:\xampp\php\php.exe" artisan route:clear
```

### 1.6 User roles

| Role | Access |
|---|---|
| `admin` | Full access — Samy's role |
| `staff` | CRM + Clinical, no Finance |
| `associate` | Own patients + appointments only |
| `case_manager` | Own patients only (read) |
| `developer` | Same access as admin + all 4 Feedback tabs — Jai Anand's role |

The `role` column on `users` table is an ENUM. As of 2026-06-27 it contains: `admin, staff, associate, case_manager, developer`.

---

## PART 2 — EXISTING DATABASE SCHEMA

The following 19 tables already exist. Do not re-create them. Add columns via new migration files only.

| Table | Key columns relevant to Phase 2 |
|---|---|
| `users` | id, name, email, role (enum), is_active |
| `companies` | id, name, type, status |
| `enquiries` | id, enquirer_name, company_id, case_manager_id, source, status, converted_to_company_id, notes |
| `case_managers` | id, user_id, company_id, first_name, last_name, email, phone |
| `associates` | id, user_id, name, region, speciality, session_rate, travel_rate_per_mile, is_active |
| `patients` | id, case_manager_id, first_name, last_name, date_of_birth, condition, status (15-value enum), needs_review, notes |
| `patient_associates` | id, patient_id, associate_id, role (enum: Assessment/Treatment/Supervision/MDT), start_date, end_date, is_primary |
| `patient_case_manager_history` | id, patient_id, previous/new CM + company IDs, change_date, reason |
| `cost_estimations` | id, patient_id, version_number, estimated_amount, estimated_sessions |
| `funding_cycles` | id, patient_id, cost_estimation_id, cycle_number, funder_name, funder_reference, approved_amount, approval_document_path, is_active |
| `appointments` | id, patient_id, associate_id, activity_type_id, scheduled_at, duration_minutes, status |
| `case_notes` | id, patient_id, appointment_id, associate_id, session_date, note_type, is_signed_off |
| `documents` | id, document_type_id, patient_id, case_manager_id, enquiry_id, appointment_id, file_name, stored_file_name, file_path, is_password_protected |
| `communications` | id, case_manager_id, patient_id, enquiry_id, type, direction, subject, summary, follow_up_date |
| `associate_invoices` | id, associate_id, patient_id, funding_cycle_id, invoice_reference, invoice_date, total_amount, status, due_date |
| `vta_invoices` | id, patient_id, funding_cycle_id, invoice_number, invoice_date, total_amount, status |
| `email_intake_logs` | id, from_email, from_name, subject, body, received_at, has_attachments, processed, linked_patient_id, linked_case_manager_id |
| `activity_types` | id, name, is_active, sort_order |
| `document_types` | id, name, is_active, sort_order |
| `portal_feedback_items` | id, type, section, reference, priority, title, description, dev_context, samy_status, samy_response, dev_status, severity, raised_by, is_seeded |

### Current patient status enum (15 values)

```
Enquiry Logged → Response Sent → Awaiting LOI → LOI Received →
Assessment Scheduled → Assessment Completed → Report Drafted → Report Sent →
Cost Estimation Sent → Awaiting Funding Approval → Funding Approved →
Treatment Active → Awaiting Further Funding → Discharged → Case Closed
```

---

## PART 3 — EXISTING BUSINESS RULES (do not break these)

| Rule | Description | Enforced in |
|---|---|---|
| BR-C1 | Status → "Funding Approved" requires a funding cycle with `approval_document_path` set | `PatientController@updateStatus` |
| BR-C2 | Status → "Treatment Active" requires current = "Funding Approved" | `PatientController@updateStatus` |
| BR-C3 | Only one associate per role type per patient | `PatientController@addAssociate` |
| BR-C4 | Case manager transfer creates `patient_case_manager_history` row | `PatientController@transfer` |
| BR-D1 | Document stored at `{company}/{case-manager}/{patient}/{doc-type}/{uuid}.{ext}` | `DocumentService` |
| BR-D2 | Downloads authenticated and permission-checked via `DocumentPolicy` | `DocumentController@download` |
| BR-F1 | VTA invoice shows warning if amount exceeds funding cycle remaining balance | `FundingBalanceService@willExceedBalance` |
| BR-F3 | Associate invoice `due_date` = invoice date + 28 days | `AssociateInvoiceController@store` |
| BR-F5 | VTA invoice number auto-generated as `VTA-YYYY-NNNN` | `InvoiceNumberService` |
| BR-F6 | VTA invoice cannot be marked "Sent" without an attached document | `VtaInvoiceController@updateStatus` |

---

## PART 4 — PHASE 2 CHANGES (18 items)

Each change item below is a complete specification. Items that depend on others are noted.

---

### A1 — "Qualified as Referral" gate

**Priority:** Critical  
**Module:** Enquiry  
**Status:** Ready to build

**Samy's clarification (Q1, Q2):** "Qualified" = referrer approves the first assessment. No funding promise at this point. Nothing extra to record beyond date and remarks. **Only admin can perform this action** (LOI always comes to VTA, not staff).

**What to build:**

The enquiry workflow currently jumps from "New" → "Converted" with no formal qualification step. Samy's process requires a "Qualified as Referral" status between follow-ups and patient creation.

**Important workflow clarification:** Patient creation does NOT happen immediately after qualification. The full journey is:
> Enquiry → Qualify → Assessment → Cost Agreement → Patient Created → Funding Cycle

The "Create Patient" button appears after the Assessment module records cost agreement (see C1 + A2).

**Database migration** — new file `2026_06_27_100001_add_qualified_fields_to_enquiries_table.php`:

```php
Schema::table('enquiries', function (Blueprint $table) {
    $table->boolean('qualified_as_referral')->default(false)->after('status');
    $table->date('qualified_date')->nullable()->after('qualified_as_referral');
    $table->text('qualified_remarks')->nullable()->after('qualified_date');
});
```

**Enquiry status enum** — modify the status column in new migration `2026_06_27_100002_update_enquiry_status_enum.php`:

```php
DB::statement("ALTER TABLE enquiries MODIFY COLUMN status 
    ENUM('New','In Progress','Qualified','Converted','Not Proceeding') 
    NOT NULL DEFAULT 'New'");
```

**EnquiryController changes:**

Add a new `qualify` method:

```php
public function qualify(Request $request, Enquiry $enquiry): RedirectResponse
{
    $data = $request->validate([
        'qualified_date'    => 'required|date',
        'qualified_remarks' => 'nullable|string|max:2000',
    ]);
    $enquiry->update([
        'qualified_as_referral' => true,
        'qualified_date'        => $data['qualified_date'],
        'qualified_remarks'     => $data['qualified_remarks'],
        'status'                => 'Qualified',
    ]);
    return back()->with('success', 'Enquiry marked as Qualified.');
}
```

**Route** — add inside the `role:admin` group in `routes/web.php` (**admin only, not staff**):

```php
Route::post('/enquiries/{enquiry}/qualify', [EnquiryController::class, 'qualify'])->name('enquiries.qualify');
```

**View change on `enquiries/show.blade.php`:**

1. Add a "Mark as Qualified" section/card that only shows when `!$enquiry->qualified_as_referral`. Contains: qualified_date (date input), qualified_remarks (textarea), submit button.
2. Show a green "Qualified ✓" badge with qualified_date when `$enquiry->qualified_as_referral === true`.
3. Move the "Convert to Patient" button: only show it when `$enquiry->qualified_as_referral === true`. Remove it from the default view.

**Enquiry::$fillable** — add: `qualified_as_referral`, `qualified_date`, `qualified_remarks`.

---

### A2 — Link Enquiry → Patient

**Priority:** Critical  
**Module:** Enquiry → Patient  
**Status:** Ready to build  
**Depends on:** A1 (qualified gate), C1 (Assessment module — patient is created after cost agreement, not just after qualification)

**Samy's clarification (Q10):** Not all cost estimations get approved. The referral becomes a patient only after the referrer agrees to costs post-assessment. The "Create Patient" button therefore sits after Assessment + Cost Estimation, not immediately after qualification.

**Revised flow:**
1. Enquiry qualified (A1) → Assessment created (C1) → Cost estimation created → if agreed → "Create Patient" button appears
2. For Sprint 1, implement the technical link (enquiry_id FK) and pre-fill. The button placement (post-assessment) is enforced in Sprint 2 when C1 is built.

**What to build:**

Currently `EnquiryController::convert()` creates a Case Manager and stops — it never creates a Patient, and there is no `enquiry_id` on the patients table. The two records are permanently disconnected.

**Database migration** — `2026_06_27_100003_add_enquiry_id_to_patients_table.php`:

```php
Schema::table('patients', function (Blueprint $table) {
    $table->foreignId('enquiry_id')->nullable()->after('id')
          ->constrained('enquiries')->nullOnDelete();
});
```

**Flow change:**

Replace the current "Convert to Patient" button on `enquiries/show.blade.php` with a link to:

```
/patients/create?enquiry_id={{ $enquiry->id }}
```

This button only appears when `$enquiry->qualified_as_referral === true` (A1 gate).

**PatientController::create() change:**

```php
public function create(Request $request): View
{
    $enquiry = null;
    if ($request->has('enquiry_id')) {
        $enquiry = Enquiry::find($request->enquiry_id);
    }
    // pass $enquiry to view
    return view('patients.create', compact('enquiry', /* existing vars */));
}
```

**Patient create form (`patients/create.blade.php`) — pre-fill from enquiry:**

When `$enquiry` is not null, pre-fill these fields using the enquiry data as default values (use `old()` with enquiry as fallback so re-submit works):

| Patient form field | Source from Enquiry |
|---|---|
| Company (dropdown) | `$enquiry->company_id` |
| Case Manager (dropdown) | `$enquiry->case_manager_id` |
| Referral date | `$enquiry->created_at->toDateString()` |
| Notes | `$enquiry->notes` |

Add a hidden input: `<input type="hidden" name="enquiry_id" value="{{ $enquiry->id ?? '' }}">`.

**PatientController::store() change:**

```php
if ($request->filled('enquiry_id')) {
    $patient->enquiry_id = $request->enquiry_id;
    $patient->save();
    // Also update the enquiry status to 'Converted'
    Enquiry::find($request->enquiry_id)->update(['status' => 'Converted']);
}
```

**Patient model** — add `enquiry_id` to `$fillable`. Add relation:

```php
public function enquiry(): BelongsTo
{
    return $this->belongsTo(Enquiry::class);
}
```

**Enquiry model** — add relation:

```php
public function patient(): HasOne
{
    return $this->hasOne(Patient::class);
}
```

**On `enquiries/show.blade.php`** — if the enquiry has a linked patient, show a link to the patient record instead of the "Create Patient" button.

---

### A3 — Multiple enquiry contacts with roles

**Priority:** Medium  
**Module:** Enquiry  
**Status:** ✅ UNBLOCKED (Q3 answered)

**Samy's clarification (Q3):** Sometimes a non-case-manager health professional refers a patient and copies their line manager. Both people need to be recorded for future communications — their roles are not fixed titles, just whoever was on the enquiry letter. The roles are flexible contact types, not a strict "Case Manager vs Lead Professional" binary.

**Database migration** — `enquiry_contacts` table:

```php
Schema::create('enquiry_contacts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->enum('role', ['Case Manager', 'Health Professional', 'Line Manager', 'Solicitor', 'Insurer', 'Other']);
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->timestamps();
});
```

**EnquiryContact model** — create at `app/Models/EnquiryContact.php`.

**Enquiry model** — add: `public function contacts(): HasMany { return $this->hasMany(EnquiryContact::class); }`

**Enquiry create/edit forms** — add a dynamic "Contacts" section with "Add Another" JavaScript rows (Alpine.js). Each row: name, role (dropdown), email, phone.

**EnquiryController::store()/update()** — after saving the enquiry, sync contacts:

```php
$enquiry->contacts()->delete();
foreach ($request->contacts ?? [] as $contact) {
    if (!empty($contact['name'])) {
        $enquiry->contacts()->create($contact);
    }
}
```

---

### A4 — Four follow-up slots on Enquiry

**Priority:** Medium  
**Module:** Enquiry  
**Status:** Ready to build  
**Note:** The `communications` table already has `enquiry_id`. Follow-ups are stored there as communication records of type "Follow Up".

**What to build:**

Restructure `enquiries/show.blade.php` Follow-ups section to:

1. Show all communications where `enquiry_id = $enquiry->id` and `type = 'Follow Up'` ordered by date — numbered #1, #2, #3, #4 etc.
2. Show a compact "Log Follow-up" inline form (date, notes/summary) below the list.
3. The existing communication store route (`POST /communications`) already handles this — just pass `enquiry_id`, `type = 'Follow Up'`, `direction = 'Outbound'`.

No database changes needed for A4.

---

### B1 — Next of Kin fields on Patient

**Priority:** Low  
**Module:** Patient  
**Status:** Ready to build

**Database migration** — `2026_06_27_100004_add_nok_fields_to_patients_table.php`:

```php
Schema::table('patients', function (Blueprint $table) {
    $table->string('nok_name')->nullable()->after('notes');
    $table->string('nok_email')->nullable()->after('nok_name');
    $table->string('nok_phone')->nullable()->after('nok_email');
});
```

**Patient model** — add to `$fillable`: `nok_name`, `nok_email`, `nok_phone`.

**Patient create/edit forms** — add a "Next of Kin" section with three fields: Name, Email, Phone.

**patients/show.blade.php** — add a "Next of Kin" card showing the three values (if any are set).

---

### B2 — Referrer details — four named roles on Patient

**Priority:** Medium  
**Module:** Patient  
**Status:** ✅ UNBLOCKED for roles (Q3 answered). Q4 (multi-company) still outstanding but does not block building this — use single `company_name` text field per referrer row as a safe default.

**Samy's clarification (Q11):** Multiple payer types confirmed: Case Manager, Deputy, Solicitor, Insurer, Others. These referrer contacts are also the potential invoice recipients. The `funder_role` on funding cycles should reference these roles.

**Build:

**Database migration** — `patient_referrers` table:

```php
Schema::create('patient_referrers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->enum('role', ['Case Manager', 'Deputy', 'Solicitor', 'Insurer', 'Other']);
    $table->string('company_name')->nullable();
    $table->text('address')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->timestamps();
});
```

**PatientReferrer model** — create at `app/Models/PatientReferrer.php`.

**Patient model** — add: `public function referrers(): HasMany { return $this->hasMany(PatientReferrer::class); }`

**Patient forms + show view** — dynamic referrer rows (same pattern as A3 contacts).

---

### B3 — Auto status transitions on Patient

**Priority:** Medium  
**Module:** Patient  
**Status:** ✅ UNBLOCKED (Q6 answered — one assessment per patient confirmed)

**Samy's clarification (Q6):** One initial assessment per patient. Treatment reviews are part of the treatment cycle, not separate assessments.

When unblocked, add auto-transitions at these trigger points:

| Event | Auto-set patient status to |
|---|---|
| Assessment created (C1) | `Assessment Scheduled` |
| Assessment `report_sent` set to true | `Report Sent` |
| Cost estimation created | `Cost Estimation Sent` |
| Funding cycle created with `approval_document_path` | `Funding Approved` |
| First VTA invoice status set to `Sent` | `Treatment Active` |

Implementation: in each relevant Controller's `store()` or `update()`, after saving, call:

```php
$patient = Patient::find($relevant_patient_id);
$patient->update(['status' => 'Assessment Scheduled']); // etc.
```

Only transition if the current status is "logically before" the new one — don't downgrade.

---

### C1 — Assessment module (new table, model, controller, routes, views)

**Priority:** New feature — build after A1 and A2  
**Module:** Assessment (new)  
**Status:** ✅ UNBLOCKED (Q6 + Q7 + Q8 + Q9 answered)

**Samy's clarifications:**
- **Q6:** One initial assessment per patient (one-to-one). Add `UNIQUE` constraint on `patient_id`.
- **Q7:** Assessor = free text (either Samy or an associate — not a FK).
- **Q8:** Assessment fee is invoiced via the VTA invoice system. The assessment cost document = invoice document. Add `assessment_id` FK to `vta_invoices` so the VTA invoice references the assessment.
- **Q9:** Report sent externally by email to whoever `special_instructions` specifies (can be multiple). Portal records "Report Sent" + document upload only — no portal-based email sending needed.

**Database migration** — `2026_06_27_100005_create_assessments_table.php`:

```php
Schema::create('assessments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
    $table->unique('patient_id'); // one assessment per patient (Q6)
    $table->decimal('fee_agreed_amount', 10, 2)->nullable();
    $table->string('fee_agreed_document_path')->nullable();
    $table->date('date_client_contacted')->nullable();
    $table->string('assessor')->nullable(); // free text — Samy or associate name (Q7)
    $table->string('venue')->nullable();
    $table->date('assessment_date')->nullable();
    $table->decimal('assessment_cost', 10, 2)->nullable();
    $table->string('assessment_cost_document_path')->nullable();
    $table->boolean('report_sent')->default(false);
    $table->string('report_document_path')->nullable();
    $table->text('special_instructions')->nullable(); // who receives which communication (Q9)
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

**Also add `assessment_id` FK to `vta_invoices`** in new migration `2026_06_27_100005b_add_assessment_id_to_vta_invoices_table.php`:

```php
Schema::table('vta_invoices', function (Blueprint $table) {
    $table->foreignId('assessment_id')->nullable()->after('patient_id')
          ->constrained('assessments')->nullOnDelete();
});
```

**Assessment model** — `app/Models/Assessment.php`:

```php
class Assessment extends Model {
    protected $fillable = [
        'patient_id','fee_agreed_amount','fee_agreed_document_path',
        'date_client_contacted','assessor','venue','assessment_date',
        'assessment_cost','assessment_cost_document_path',
        'report_sent','report_document_path','special_instructions','notes','created_by'
    ];
    protected $casts = ['assessment_date'=>'date','date_client_contacted'=>'date','report_sent'=>'boolean'];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function vtaInvoice(): HasOne { return $this->hasOne(VtaInvoice::class); }
}
```

**Patient model** — add: `public function assessment(): HasOne { return $this->hasOne(Assessment::class); }`

**AssessmentController** — `app/Http/Controllers/AssessmentController.php`:

Methods: `store(Request $request, Patient $patient)`, `edit(Assessment $assessment)`, `update(Request $request, Assessment $assessment)`.

`store()` validates all fields, creates the Assessment, then auto-transitions patient to `Assessment Scheduled` (B3).

Document uploads (fee_agreed, assessment_cost, report) use the existing `vta-documents` disk:

```php
if ($request->hasFile('fee_agreed_document')) {
    $path = $request->file('fee_agreed_document')->store('assessments', 'vta-documents');
    $data['fee_agreed_document_path'] = $path;
}
```

**BR enforcement in `update()`**: if `report_sent` is being set to `true`, require `report_document_path` to be set — either existing or newly uploaded. Block with validation error if missing.

**Routes** — add inside the `role:admin,staff` group:

```php
Route::post('/patients/{patient}/assessment', [AssessmentController::class, 'store'])->name('assessments.store');
Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
```

**patients/show.blade.php** — add an "Assessment" card/section after the patient info section showing all assessment fields with an inline edit button if the assessment exists, or an "Add Assessment" form if it doesn't.

---

### C2 — Cost for Assessment vs Cost Estimation — two separate figures

**Priority:** New feature  
**Module:** Assessment / Finance  
**Status:** ✅ UNBLOCKED (Q8 answered — assessment cost IS invoiced via VTA system)

**Samy's clarification (Q8):** The funder (insurer/solicitor) pays the assessment fee. It is invoiced via the VTA invoice system. The `assessment_cost_document_path` on the assessment = the invoice/cost document. The VTA invoice references the assessment via `assessment_id` FK (added in C1).

The `assessment_cost` column on the `assessments` table (added in C1) covers the assessment cost. No new table needed.

Ensure both figures appear on the patient page separately:
- "Assessment Cost: £X" — from `assessment.assessment_cost` — with a "Create VTA Invoice" button linking `vta_invoices.assessment_id`
- "Cost Estimation: £X" — from `cost_estimations` (already exists, separate record)

**VtaInvoice model** — add: `public function assessment(): BelongsTo { return $this->belongsTo(Assessment::class); }`

**VTA invoice create form** — add an optional "Assessment" dropdown so the invoice can be linked to the patient's assessment when applicable.

---

### D1 — Document upload on Funding Cycle form (Gap B)

**Priority:** Critical  
**Module:** Funding Cycles  
**Status:** Ready to build (most critical gap in the current codebase)

**What to build:**

The `funding_cycles` table has `approval_document_path` but there is no file upload field anywhere in the UI. BR-C1 (Funding Approved status requires a document) can never be satisfied via the current UI.

**funding-cycles/create.blade.php and funding-cycles/edit.blade.php** — add a file upload field:

```html
<div>
    <label class="block text-sm font-medium text-gray-700">Approval Document</label>
    <input type="file" name="approval_document" 
           accept=".pdf,.doc,.docx,.jpg,.png"
           class="mt-1 block w-full text-sm text-gray-500 ...">
    @if(isset($fundingCycle) && $fundingCycle->approval_document_path)
        <p class="mt-1 text-xs text-green-600">
            <i class="fa-solid fa-check mr-1"></i> Document already uploaded.
            <a href="{{ route('documents.download', ...) }}" class="underline">Download</a>
        </p>
    @endif
</div>
```

Ensure the form has `enctype="multipart/form-data"`.

**FundingCycleController::store() and update() changes:**

```php
if ($request->hasFile('approval_document')) {
    $file = $request->file('approval_document');
    $path = $file->store('funding-cycles', 'vta-documents');
    $data['approval_document_path'] = $path;
}
```

**Validation** — add to the store/update request validation:

```php
'approval_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
```

No new migration needed — `approval_document_path` column already exists.

---

### D2 — Show all funding cycles on patient page (Gap C)

**Priority:** Medium  
**Module:** Patient / Funding  
**Status:** Ready to build

**What to fix:**

`patients/show.blade.php` uses `$patient->fundingCycles->first()` — hard-coded to show only one cycle. Multi-phase patients lose visibility of all subsequent cycles.

**patients/show.blade.php** — replace the single funding cycle card with a loop:

```blade
@foreach($patient->fundingCycles as $cycle)
<div class="bg-white border rounded-xl p-4 mb-3">
    <div class="flex justify-between items-start">
        <div>
            <span class="font-semibold">Cycle {{ $cycle->cycle_number }}</span>
            <span class="text-sm text-gray-500 ml-2">{{ $cycle->funder_name }}</span>
        </div>
        @if($cycle->is_active)
        <span class="badge bg-green-100 text-green-700">Active</span>
        @endif
    </div>
    <div class="grid grid-cols-3 gap-3 mt-3 text-sm">
        <div><span class="text-gray-500">Approved:</span> £{{ number_format($cycle->approved_amount, 2) }}</div>
        <div><span class="text-gray-500">Used:</span> £{{ number_format($fundingBalanceService->invoicedAmount($cycle), 2) }}</div>
        <div><span class="text-gray-500">Remaining:</span> £{{ number_format($fundingBalanceService->remainingBalance($cycle), 2) }}</div>
    </div>
    @if($cycle->approval_document_path)
    <p class="text-xs text-green-600 mt-2"><i class="fa-solid fa-file-check mr-1"></i> Approval document uploaded</p>
    @endif
</div>
@endforeach
```

**PatientController::show()** — inject `FundingBalanceService`:

```php
public function show(Patient $patient, FundingBalanceService $fundingBalanceService): View
{
    // ... existing code ...
    return view('patients.show', compact('patient', 'fundingBalanceService', /* ... */));
}
```

---

### D3 — "Add Funding Cycle" button on patient page (Gap A)

**Priority:** Medium  
**Module:** Patient / Funding  
**Status:** Ready to build

**patients/show.blade.php** — in the Funding section header, add:

```blade
@if(in_array(Auth::user()->role, ['admin', 'developer']))
<a href="{{ route('funding-cycles.create', ['patient_id' => $patient->id]) }}" 
   class="btn-sm btn-primary">
    <i class="fa-solid fa-plus mr-1"></i> Add Funding Cycle
</a>
@endif
```

**FundingCycleController::create()** — pre-fill the patient dropdown when `patient_id` is in the query string:

```php
public function create(Request $request): View
{
    $preselectedPatient = $request->filled('patient_id') 
        ? Patient::find($request->patient_id) 
        : null;
    // pass to view
}
```

**funding-cycles/create.blade.php** — pre-select the patient in the patient dropdown using `$preselectedPatient`.

**Cost estimation filter (Gap D)** — on the funding cycle create/edit form, filter the `cost_estimation_id` dropdown to only show cost estimations belonging to the selected patient. Use Alpine.js to watch the patient dropdown and filter via a pre-rendered JSON:

```blade
<script>
const estimationsByPatient = @json($estimationsByPatient);
</script>
```

In `FundingCycleController::create()`, pass `$estimationsByPatient` as a grouped collection: `CostEstimation::all()->groupBy('patient_id')->map->pluck('estimated_amount', 'id')`.

---

### E1 — Rename Finance → Accounts in navigation

**Priority:** Low  
**Module:** Navigation  
**Status:** Ready to build (one-line change)

**`resources/views/layouts/app.blade.php`** — find the "Finance" section divider label and rename it:

Change: `Finance` → `Accounts`

The URL paths `/finance/...` do not need to change.

---

### E2 — Associate invoice rate card auto-calculation (Gap E)

**Priority:** Medium  
**Module:** Finance  
**Status:** Ready to build

**Database migration** — `2026_06_27_100006_add_rate_fields_to_associates_table.php`:

The `associates` table already has `session_rate` and `travel_rate_per_mile`. Verify these columns exist before adding. If they already exist, skip the migration.

```sql
-- check first:
SHOW COLUMNS FROM associates LIKE 'session_rate';
```

If missing:

```php
Schema::table('associates', function (Blueprint $table) {
    $table->decimal('session_rate', 10, 2)->nullable()->after('speciality');
    $table->decimal('travel_rate_per_mile', 10, 2)->nullable()->after('session_rate');
});
```

**associate-invoices/create.blade.php** — add two new fields: `sessions_count` (number input) and `mileage` (number input, optional). Add Alpine.js to auto-calculate `total_amount`:

```html
<div x-data="{ rate: 0, sessions: 0, mileage: 0, mileRate: 0 }">
    <!-- When associate dropdown changes, set rate via data attribute -->
    <select name="associate_id" 
            @change="rate = $el.selectedOptions[0]?.dataset.rate || 0; 
                     mileRate = $el.selectedOptions[0]?.dataset.mileRate || 0">
        @foreach($associates as $a)
        <option value="{{ $a->id }}" 
                data-rate="{{ $a->session_rate }}"
                data-mile-rate="{{ $a->travel_rate_per_mile }}">{{ $a->name }}</option>
        @endforeach
    </select>
    <input type="number" name="sessions_count" x-model="sessions" step="0.5" min="0">
    <input type="number" name="mileage" x-model="mileage" step="1" min="0">
    <!-- Auto-calculated total (editable override) -->
    <input type="number" name="total_amount" step="0.01"
           :value="(rate * sessions + mileRate * mileage).toFixed(2)">
</div>
```

**AssociateInvoiceController::store()** — accept `sessions_count` and `mileage` in the request but `total_amount` remains the authoritative field (editable override allowed):

```php
$data = $request->validate([
    'associate_id'  => 'required|exists:associates,id',
    'patient_id'    => 'required|exists:patients,id',
    'sessions_count'=> 'nullable|numeric|min:0',
    'mileage'       => 'nullable|numeric|min:0',
    'total_amount'  => 'required|numeric|min:0',
    // ... other existing fields
]);
```

No `sessions_count`/`mileage` columns needed on `associate_invoices` unless you want to store them for audit — add them if desired in the migration above.

---

### E4 — Assessment fee → VTA invoice auto-link (NEW — from Q8)

**Priority:** Medium  
**Module:** Assessment / Finance  
**Status:** ✅ Ready to build (depends on C1)

**Samy's clarification (Q8):** The assessment fee is invoiced via the VTA invoice system. When an assessment cost is entered, Samy should be able to create a VTA invoice for it directly from the patient page.

**What to build:**

On the Assessment card on `patients/show.blade.php`, when `assessment.assessment_cost` is set and no VTA invoice is yet linked to this assessment, show a button:

```blade
@if($patient->assessment && $patient->assessment->assessment_cost && !$patient->assessment->vtaInvoice)
<a href="{{ route('vta-invoices.create', ['patient_id' => $patient->id, 'assessment_id' => $patient->assessment->id]) }}"
   class="btn-sm btn-primary">
    <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Create Assessment Invoice
</a>
@endif
```

**VtaInvoiceController::create()** — pre-fill `total_amount` from the assessment cost when `assessment_id` is in the query string:

```php
$assessment = $request->filled('assessment_id') ? Assessment::find($request->assessment_id) : null;
// pass to view; pre-fill total_amount = $assessment->assessment_cost
```

**vta-invoices/create.blade.php** — add hidden `assessment_id` input when pre-filling from assessment.

**VtaInvoiceController::store()** — accept and save `assessment_id`:

```php
$data = $request->validate([
    // existing fields...
    'assessment_id' => 'nullable|exists:assessments,id',
]);
```

---

### E3 — Filter associate invoice patient dropdown (Gap F)

**Priority:** Medium  
**Module:** Finance  
**Status:** Ready to build

**associate-invoices/create.blade.php** — pre-render all patient-associate mappings as JSON and use Alpine.js to filter the patient dropdown when the associate changes:

```blade
<script>
const patientsByAssociate = @json(
    \App\Models\PatientAssociate::with('patient')
        ->get()
        ->groupBy('associate_id')
        ->map(fn($rows) => $rows->map(fn($r) => ['id'=>$r->patient_id,'name'=>$r->patient->full_name]))
);
</script>

<div x-data="{ selectedAssociate: '', patients: [] }"
     x-init="$watch('selectedAssociate', val => { patients = patientsByAssociate[val] || [] })">
    <select name="associate_id" x-model="selectedAssociate">...</select>
    <select name="patient_id">
        <template x-for="p in patients" :key="p.id">
            <option :value="p.id" x-text="p.name"></option>
        </template>
    </select>
</div>
```

No database changes needed.

---

### F1 — Dashboard — 5 widgets

**Priority:** Medium  
**Module:** Dashboard  
**Status:** ✅ UNBLOCKED (Q14 + Q15 answered)

**Samy's clarifications:**
- **Q14:** Samy is currently the Clinical Head. Associates must **manually flag** each report submission for clinical head review. Use the existing `needs_review` flag on `case_notes`. Associates add a "Flag for Clinical Head Review" checkbox when submitting a case note in the Associate Portal.
- **Q15:** Most urgent reports = **Funding/session balance per active patient** + **Financial summary (outgoings + incomings)**. Conversions + associate activity only needed every 6 months — lowest priority.

Build 5 dashboard widget cards based on Samy's PPTX Slide 2:

1. **Emails** — count of `email_intake_logs` where `processed = false`
2. **Clinical Head Review** — count of `case_notes` where `needs_review = true` and `is_signed_off = false`
3. **Pending Enquiries/Referrals** — count of `enquiries` where `status IN ('New','In Progress','Qualified')`
4. **Invoices/Payments Due** — count of `associate_invoices` where `due_date < today` and `status != 'Paid'`
5. **Shared Calendar** — link to `/appointments/calendar` (already exists)

**Additional change (Q14) — Associate Portal:** Add a `needs_review` checkbox to the case note upload form in the Associate Portal so associates can flag a report for Samy's review on submission.

**DashboardController::index()** — add the 5 counts to the data passed to the view.

**dashboard/index.blade.php** — replace/add the 5 widget cards.

---

### G1 — Promote Email Intake to top-level nav section

**Priority:** Low  
**Module:** Navigation  
**Status:** Ready to build

**`resources/views/layouts/app.blade.php`** — move the Email Intake nav link:

From: inside the Admin section (bottom of nav, role:admin,staff)

To: its own section near the top of the nav, between Patients/Companies and Clinical:

```blade
<div class="border-t border-gray-200 my-2">
    <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Emails</p>
</div>
@if(in_array(Auth::user()->role, ['admin', 'staff']))
<a href="{{ route('email-intake.index') }}" 
   class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('email-intake.*') ? 'bg-[#0092b4]/10 text-[#0092b4]' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fa-solid fa-envelope-open-text w-5 text-center"></i>
    Emails
    @php $unprocessed = \App\Models\EmailIntakeLog::where('processed', false)->count(); @endphp
    @if($unprocessed > 0)
    <span class="ml-auto inline-flex items-center justify-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">{{ $unprocessed }}</span>
    @endif
</a>
@endif
```

Remove the old Email Intake link from the Admin section.

---

### G2 — Reports section — prioritised reports

**Priority:** Medium  
**Module:** Reports  
**Status:** ✅ UNBLOCKED (Q15 answered)

**Samy's clarification (Q15):** Priority order: (1) Funding/session balance per active patient, (2) Financial summary (outgoings + incomings). Conversions and associate activity are low priority (checked every 6 months only).

Build reports in this priority order:

**ReportsController** — `app/Http/Controllers/ReportsController.php`:

**ReportsController** — `app/Http/Controllers/ReportsController.php` — build methods in priority order:

```php
class ReportsController extends Controller
{
    // Priority 1 — Funding balance per active patient
    public function fundingBalanceSummary(): View { ... }

    // Priority 2 — Financial summary (outgoings + incomings)
    public function financialSummary(Request $request): View { ... } // date range filter

    // Priority 3 (low) — Active patients by status
    public function activePatientsByStatus(): View { ... }

    // Priority 4 (low) — Associate activity (check every 6 months)
    public function associateActivity(Request $request): View { ... }
}
```

**Routes** — inside `role:admin` middleware:

```php
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
Route::get('/reports/funding-balance', [ReportsController::class, 'fundingBalanceSummary'])->name('reports.funding-balance');
Route::get('/reports/financial-summary', [ReportsController::class, 'financialSummary'])->name('reports.financial-summary');
Route::get('/reports/patients-by-status', [ReportsController::class, 'activePatientsByStatus'])->name('reports.patients-by-status');
Route::get('/reports/associate-activity', [ReportsController::class, 'associateActivity'])->name('reports.associate-activity');
```

**Nav** — add a "Reports" nav item for `admin` and `developer` roles.

---

## PART 5 — PHASE 2 IMPROVEMENTS (10 items)

---

### I1 — Enforce linear status progression with auto-transitions

**Priority:** High  
**Status:** [BLOCKED — depends on B3, C1]

After C1 (Assessment module) and B3 (auto-transitions) are built, add enforcement:

In `PatientController::updateStatus()`, add a transition map that defines which statuses can follow which. Block any jump that skips a mandatory step.

```php
private const ALLOWED_TRANSITIONS = [
    'Enquiry Logged'            => ['Response Sent', 'Not Proceeding'],
    'Response Sent'             => ['Awaiting LOI', 'Not Proceeding'],
    'Awaiting LOI'              => ['LOI Received', 'Not Proceeding'],
    'LOI Received'              => ['Assessment Scheduled'],
    'Assessment Scheduled'      => ['Assessment Completed'],
    'Assessment Completed'      => ['Report Drafted'],
    'Report Drafted'            => ['Report Sent'],
    'Report Sent'               => ['Cost Estimation Sent'],
    'Cost Estimation Sent'      => ['Awaiting Funding Approval'],
    'Awaiting Funding Approval' => ['Funding Approved'],
    'Funding Approved'          => ['Treatment Active'],
    'Treatment Active'          => ['Awaiting Further Funding', 'Discharged'],
    'Awaiting Further Funding'  => ['Funding Approved'],
    'Discharged'                => ['Case Closed'],
];
```

Return a 422 validation error if the requested transition is not in the allowed list.

---

### I2 — Pre-fill patient form from enquiry data

This is the same as **A2** above — implemented as part of A2. Mark I2 as complete when A2 is done.

---

### I3 — Single "Patient Journey" timeline view

**Priority:** Medium  
**Status:** Ready to build (no DB changes needed)

Add a "Timeline" tab or section to `patients/show.blade.php`. Merge all related events into a single chronological feed:

**PatientController::show()** — collect and merge events:

```php
$timeline = collect()
    ->merge($patient->communications->map(fn($c) => ['date'=>$c->created_at,'type'=>'Communication','icon'=>'fa-message','desc'=>$c->subject]))
    ->merge($patient->documents->map(fn($d) => ['date'=>$d->created_at,'type'=>'Document','icon'=>'fa-file','desc'=>$d->file_name]))
    ->merge($patient->caseNotes->map(fn($n) => ['date'=>$n->session_date,'type'=>'Case Note','icon'=>'fa-note-sticky','desc'=>$n->note_type]))
    ->merge($patient->appointments->map(fn($a) => ['date'=>$a->scheduled_at,'type'=>'Appointment','icon'=>'fa-calendar','desc'=>$a->activityType?->name]))
    ->merge($patient->vtaInvoices->map(fn($i) => ['date'=>$i->invoice_date,'type'=>'Invoice','icon'=>'fa-file-invoice','desc'=>'VTA '.$i->invoice_number.' — £'.number_format($i->total_amount,2)]))
    ->sortByDesc('date')
    ->values();
```

Render as a vertical timeline in the view. Each entry: coloured icon, type badge, description, date.

---

### I4 — "Quick Actions" panel — next logical step only

**Priority:** Medium  
**Status:** [BLOCKED — depends on I1 (transition map)]

After I1 is built, add a "Next Step" card at the top of `patients/show.blade.php`:

Map each patient status to a single CTA:

| Current status | Next action | Link |
|---|---|---|
| LOI Received | Add Assessment | `#assessment-section` (scroll) |
| Assessment Completed | Mark Report Drafted | status update button |
| Report Sent | Create Cost Estimation | `/cost-estimations/create?patient_id=X` |
| Awaiting Funding Approval | Add Funding Cycle | `/funding-cycles/create?patient_id=X` |
| Funding Approved | Mark Treatment Active | status update button |

Render a prominent yellow/blue CTA card at the top of the patient page.

---

### I5 — Document enforcement at three critical gates

**Priority:** High  
**Status:** Partially done — see notes

| Gate | Status |
|---|---|
| BR-F6: VTA Invoice "Sent" without document | ✅ Already enforced in `VtaInvoiceController@updateStatus` |
| BR-C1: Funding Approved without `approval_document_path` | ✅ Already enforced in `PatientController@updateStatus` — but D1 (adding the upload UI) must be built so documents can actually be uploaded |
| Assessment "Report Sent" without report document | ❌ Build in C1's `AssessmentController::update()` |

Only the Assessment gate needs new code — handle it in C1.

---

### I6 — Funding balance live on the patient page

**Priority:** HIGH (elevated — Samy explicitly requested this in Q12: "the Accounts section should show used up and balances for each patient")  
**Status:** Ready to build (same as D2)

Implemented as part of **D2** above — the `FundingBalanceService` is already built, just needs surfacing. Mark I6 as complete when D2 is done.

---

### I7 — Associate allocation with automatic activity log

**Priority:** Low  
**Status:** Ready to build

**PatientController::addAssociate()** — after saving the `PatientAssociate` record, auto-create a communication:

```php
Communication::create([
    'patient_id' => $patient->id,
    'type'       => 'Note',
    'direction'  => 'Internal',
    'subject'    => 'Associate allocated',
    'summary'    => $associate->name . ' allocated as ' . $request->role . ' on ' . now()->toDateString(),
    'created_by' => Auth::id(),
]);
```

No database changes needed.

---

### I8 — Email intake — tag emails to patient/enquiry records

**Priority:** Medium  
**Status:** Ready to build

**Samy's clarification (Q16):** Keep emails OUT of the portal as a primary communication channel (too much junk). The only requirement is to be able to **log a specific email from Email Intake directly to a Patient's Communications section**. No full email-reading UI needed — just the "link to patient" action that already partially exists in `EmailIntakeController::link()`.

**Database migration** — `2026_06_27_100007_add_fk_fields_to_email_intake_logs_table.php`:

```php
Schema::table('email_intake_logs', function (Blueprint $table) {
    $table->foreignId('enquiry_id')->nullable()->after('linked_case_manager_id')
          ->constrained('enquiries')->nullOnDelete();
    $table->foreignId('vta_invoice_id')->nullable()->after('enquiry_id')
          ->constrained('vta_invoices')->nullOnDelete();
    $table->foreignId('funding_cycle_id')->nullable()->after('vta_invoice_id')
          ->constrained('funding_cycles')->nullOnDelete();
});
```

Note: `linked_patient_id` and `linked_case_manager_id` already exist on the table.

**EmailIntakeController::link()** — update to accept any of the four FK types:

```php
$data = $request->validate([
    'link_type'       => 'required|in:patient,case_manager,enquiry,vta_invoice,funding_cycle',
    'link_id'         => 'required|integer',
]);
// Map link_type to column name
$column = match($data['link_type']) {
    'patient'       => 'linked_patient_id',
    'case_manager'  => 'linked_case_manager_id',
    'enquiry'       => 'enquiry_id',
    'vta_invoice'   => 'vta_invoice_id',
    'funding_cycle' => 'funding_cycle_id',
};
EmailIntakeLog::findOrFail($id)->update([$column => $data['link_id'], 'processed' => true]);
```

**EmailIntakeLog model** — add the new FKs to `$fillable` and add relations for `enquiry()`, `vtaInvoice()`, `fundingCycle()`.

**Surface tagged emails** — on `patients/show.blade.php` and `enquiries/show.blade.php`, add a section showing emails tagged to that record.

---

### I9 — Reports section — 4 basic reports

This is the same as **G2** above. Mark I9 as complete when G2 is done.

---

### AP1 — Associate Portal: Session Number on patient record

**Priority:** Medium  
**Module:** Associate Portal  
**Status:** Ready to build  
**Source:** Samy's process diagram (image shared 2026-06-28) shows "Session Number" prominently on the Associate's patient view.

**What "Session Number" means:** Total approved sessions from the patient's active funding cycle (`funding_cycles.estimated_sessions` from the linked `cost_estimations` record).

**Change to `resources/views/portal/associate/patient.blade.php`:**

In the Patient Information card, add after "Status":

```blade
<div>
    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Sessions</p>
    @php
        $activeCycle = $patient->fundingCycles->where('is_active', true)->first();
        $approvedSessions = $activeCycle?->costEstimation?->estimated_sessions ?? '—';
        $usedSessions = $appointments->where('status', 'Completed')->count();
    @endphp
    <p class="text-sm text-gray-700">{{ $usedSessions }} used of {{ $approvedSessions }} approved</p>
</div>
```

**AssociatePortalController::patient()** — load the relation needed:

```php
$patient->load(['caseManager.company', 'documents.documentType', 'patientAssociates',
                'fundingCycles.costEstimation']);
```

---

### AP2 — Associate Portal: Three-stage document workflow (Draft → Revision → Approved)

**Priority:** High  
**Module:** Associate Portal  
**Status:** Ready to build  
**Source:** Samy's process diagram shows three document stages per patient: "Draft submitted for review", "Revisions made following review", "Approved final report" — each with an ADD DOC button.

**What this means:** The existing `case_notes` + `documents` tables handle this, but the Associate Portal currently has a flat "Upload Case Note" form with no staged workflow. Each stage needs a separate document upload linked to that stage label.

**Database change** — add a `stage` column to `case_notes` via new migration `2026_06_28_200001_add_stage_to_case_notes_table.php`:

```php
Schema::table('case_notes', function (Blueprint $table) {
    $table->enum('stage', ['Draft', 'Revision', 'Final'])->default('Draft')->after('note_type');
    $table->boolean('needs_review')->default(false)->after('is_signed_off'); // Q14
});
```

**CaseNote model** — add `stage` and `needs_review` to `$fillable`.

**Associate Portal patient view** — replace the flat upload form with three collapsible stage sections. Each section shows:
- Existing case notes/documents at that stage (note_type + uploaded document link)
- An "Add Doc" upload form for that stage

```blade
@foreach(['Draft' => 'Draft submitted for review', 'Revision' => 'Revisions made following review', 'Final' => 'Approved final report'] as $stage => $label)
<div class="rounded-xl border border-gray-200 bg-white p-4" x-data="{ open: false }">
    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
        <h3 class="text-sm font-semibold text-gray-800">{{ $label }}</h3>
        @php $stageNotes = $caseNotes->where('stage', $stage); @endphp
        <div class="flex items-center gap-3">
            @if($stageNotes->count())
            <span class="text-xs text-green-600 font-medium">{{ $stageNotes->count() }} doc(s)</span>
            @endif
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition" :class="open ? 'rotate-180' : ''"></i>
        </div>
    </div>
    <div x-show="open" x-collapse class="mt-3 space-y-2">
        {{-- Existing docs for this stage --}}
        @foreach($stageNotes as $note)
        <div class="flex items-center justify-between text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
            <span>{{ $note->note_type }} — {{ $note->session_date->format('d M Y') }}</span>
            {{-- document download link if available --}}
        </div>
        @endforeach
        {{-- Upload form for this stage --}}
        <form method="POST" action="{{ route('associate-portal.upload-note') }}" enctype="multipart/form-data"
              class="mt-2 border-t border-gray-100 pt-3">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <input type="hidden" name="stage" value="{{ $stage }}">
            <input type="hidden" name="note_type" value="{{ $label }}">
            <div class="flex gap-2 items-end">
                <div class="flex-1">
                    <label class="text-xs text-gray-500">Session date</label>
                    <input type="date" name="session_date" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                </div>
                <div class="flex-1">
                    <label class="text-xs text-gray-500">Document</label>
                    <input type="file" name="document" class="mt-1 block w-full text-sm text-gray-500">
                </div>
                @if($stage === 'Final')
                <label class="flex items-center gap-2 text-xs text-gray-600 mb-1">
                    <input type="checkbox" name="needs_review" value="1"> Flag for Clinical Head Review
                </label>
                @endif
                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-[#0092b4] px-3 py-2 text-sm font-medium text-white hover:bg-[#007a99] flex-shrink-0">
                    <i class="fa-solid fa-upload"></i> Add Doc
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach
```

**AssociatePortalController::uploadNote()** — update to accept `stage` and `needs_review`, and handle document file upload:

```php
$data = $request->validate([
    'patient_id'   => 'required|exists:patients,id',
    'appointment_id' => 'nullable|exists:appointments,id',
    'session_date' => 'required|date',
    'note_type'    => 'required|string|max:100',
    'stage'        => 'required|in:Draft,Revision,Final',
    'needs_review' => 'nullable|boolean',
    'document'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
]);

$data['associate_id'] = $associate->id;
$data['is_signed_off'] = false;
$data['needs_review'] = $request->boolean('needs_review');

$caseNote = CaseNote::create($data);

// If a document is uploaded, store it via the existing Document system
if ($request->hasFile('document')) {
    $file = $request->file('document');
    $path = $file->store("case-notes/{$caseNote->patient_id}", 'vta-documents');
    Document::create([
        'patient_id'         => $caseNote->patient_id,
        'document_type_id'   => null, // or a default "Case Note" document type
        'file_name'          => $file->getClientOriginalName(),
        'stored_file_name'   => basename($path),
        'file_path'          => $path,
        'is_password_protected' => false,
    ]);
}
```

---

### AP3 — Associate Portal: Read-only invoice summary per patient

**Priority:** Low  
**Module:** Associate Portal  
**Status:** Ready to build  
**Source:** Samy's process diagram shows "Financial Records" on the associate's patient view — Patient Id, Sessions, Date — as **read-only**, linked to the Accounts section.

**What to build:**

On `resources/views/portal/associate/patient.blade.php`, add a read-only "Invoices" card showing the associate's invoices for this patient:

```blade
<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h2 class="text-sm font-semibold text-gray-800 mb-4">My Invoices for this Patient</h2>
    @php
        $myInvoices = $associate->invoices->where('patient_id', $patient->id)->sortByDesc('invoice_date');
    @endphp
    @if($myInvoices->count())
    <table class="w-full text-sm">
        <thead>
            <tr class="text-xs text-gray-500 border-b border-gray-100">
                <th class="text-left pb-2">Invoice Ref</th>
                <th class="text-left pb-2">Date</th>
                <th class="text-left pb-2">Sessions</th>
                <th class="text-right pb-2">Amount</th>
                <th class="text-left pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($myInvoices as $inv)
            <tr class="border-b border-gray-50">
                <td class="py-2 text-gray-700">{{ $inv->invoice_reference }}</td>
                <td class="py-2 text-gray-500">{{ $inv->invoice_date?->format('d M Y') }}</td>
                <td class="py-2 text-gray-500">{{ $inv->sessions_count ?? '—' }}</td>
                <td class="py-2 text-right text-gray-700">£{{ number_format($inv->total_amount, 2) }}</td>
                <td class="py-2">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $inv->status === 'Paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $inv->status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-sm text-gray-500">No invoices recorded for this patient yet.</p>
    @endif
</div>
```

**AssociatePortalController::patient()** — add invoice loading:

```php
$myInvoices = \App\Models\AssociateInvoice::where('associate_id', $associate->id)
    ->where('patient_id', $patient->id)
    ->orderBy('invoice_date', 'desc')
    ->get();

return view('portal.associate.patient', compact(
    'associate', 'patient', 'appointments', 'caseNotes', 'permittedDocTypes', 'myInvoices'
));
```

No new database migrations needed for AP3.

---

### I10 — Correct CLAUDE.md and update architecture docs

**Priority:** Low  
**Status:** Ready to do at any time

**`C:\xampp\htdocs\VTA_NEW\vta-portal\CLAUDE.md`** — update:
1. Fix project path: `C:\xampp\htdocs\VTA_NEW\vta-portal` (not `C:\xampp\htdocs\vta-portal`)
2. Add `assessments` table to the database schema section
3. Add `enquiry_id` FK to patients in the Patient model notes
4. Add `enquiry_contacts` and `patient_referrers` when built
5. Update "Next Steps" to reference this Phase 2 spec document

---

## PART 6 — NEW MIGRATIONS SUMMARY

Run in this order:

| # | File | Changes | Depends on |
|---|---|---|---|
| 1 | `2026_06_27_100001_add_qualified_fields_to_enquiries_table.php` | Adds 3 cols to enquiries | — |
| 2 | `2026_06_27_100002_update_enquiry_status_enum.php` | Updates enquiry status enum | — |
| 3 | `2026_06_27_100003_add_enquiry_id_to_patients_table.php` | Adds enquiry_id FK to patients | — |
| 4 | `2026_06_27_100004_add_nok_fields_to_patients_table.php` | Adds nok_name/email/phone to patients | — |
| 5a | `2026_06_27_100005_create_assessments_table.php` | New assessments table (one-to-one, free text assessor) | Depends on C1 |
| 5b | `2026_06_27_100005b_add_assessment_id_to_vta_invoices_table.php` | assessment_id FK on vta_invoices | Depends on C1 (5a first) |
| 6 | `2026_06_27_100006_add_rate_fields_to_associates_table.php` | Rate fields on associates (if missing) | — |
| 7 | `2026_06_27_100007_add_fk_fields_to_email_intake_logs_table.php` | enquiry/invoice/cycle FKs on intake logs | — |
| 10 | `2026_06_28_200001_add_stage_to_case_notes_table.php` | stage ENUM + needs_review bool on case_notes | Depends on AP2 |
| 8 | `enquiry_contacts` table | New table (roles: Case Manager, Health Professional, Line Manager, Solicitor, Insurer, Other) | Depends on A3 |
| 9 | `patient_referrers` table | New table (roles: Case Manager, Deputy, Solicitor, Insurer, Other) | Depends on B2 |

---

## PART 7 — NEW FILES TO CREATE

| File | Type | Item |
|---|---|---|
| `app/Http/Controllers/AssessmentController.php` | Controller | C1 |
| `app/Http/Controllers/ReportsController.php` | Controller | G2/I9 |
| `app/Http/Controllers/AssessmentController.php` updated | VTA invoice link on store | E4 |
| `app/Models/Assessment.php` | Model | C1 |
| `app/Models/EnquiryContact.php` | Model | A3 |
| `app/Models/PatientReferrer.php` | Model | B2 |
| `resources/views/assessments/` | Views directory | C1 |
| `resources/views/reports/` | Views directory | G2/I9 |
| `resources/views/portal/associate/patient.blade.php` | Updated view | AP1, AP2, AP3 |
| `app/Http/Controllers/AssociatePortalController.php` | Updated controller | AP1, AP2, AP3 |

---

## PART 8 — BUILD ORDER

Build in this sequence. Do not skip ahead — later items depend on earlier ones.

**Sprint 1 — No blockers (build immediately):**

1. D1 — Document upload on Funding Cycle form *(critical gap, 2 hours)*
2. E1 — Rename Finance → Accounts in nav *(5 minutes)*
3. G1 — Promote Email Intake to top-level nav *(30 minutes)*
4. A1 — Qualified as Referral gate on Enquiry — **admin only** *(3 hours)*
5. A2 — Link Enquiry → Patient *(2 hours — depends on A1 being done first)*
6. A4 — Four follow-up slots on Enquiry *(1 hour)*
7. B1 — Next of Kin fields on Patient *(1 hour)*
8. D2 — Show all funding cycles on patient page with balance *(1 hour — HIGH priority per Q12)*
9. D3 — Add Funding Cycle button on patient page *(1 hour)*
10. E2 — Rate card auto-calculation on Associate Invoice *(2 hours)*
11. E3 — Filter patient dropdown on Associate Invoice *(1 hour)*
12. I3 — Patient Journey timeline *(2 hours)*
13. I7 — Associate allocation activity log *(30 minutes)*
14. I8 — Email intake: link email to patient communications *(2 hours)*
15. I10 — Fix CLAUDE.md *(30 minutes)*

**Sprint 1 additions (no blockers — add to Sprint 1):**

16. AP1 — Associate Portal: Session Number (approved/used) on patient view *(30 minutes)*
17. AP2 — Associate Portal: Three-stage document workflow (Draft → Revision → Approved) with needs_review flag *(3 hours)*
18. AP3 — Associate Portal: Read-only invoice summary per patient *(1 hour)*

**Sprint 2 — All unblocked (all questions resolved):**

19. A3 — Multiple enquiry contacts with roles *(Q3 answered — roles updated)*
20. B2 — Patient referrer roles *(single company per row — Q4 resolved)*
21. C1 — Assessment module *(Q6: one-to-one; Q7: free text assessor — ready)*
22. C2 — Assessment vs Cost Estimation figures *(Q8: invoiced via VTA — ready, C1 first)*
23. E4 — Assessment fee → VTA invoice auto-link *(C1 first)*
24. B3 — Auto status transitions *(C1 first)*
25. F1 — Dashboard 5 widgets + Associate Portal "Flag for review" checkbox *(Q14 + Q15 answered)*
26. G2/I9 — Reports: funding balance first, then financial summary *(Q15 prioritised)*

**Sprint 3 — After Sprint 2:**

27. I1 — Enforce linear status progression *(B3 first)*
28. I4 — Quick Actions panel *(I1 first)*
29. I5 — Assessment document gate *(C1 first)*

---

## PART 9 — BUSINESS RULES FOR PHASE 2

These new business rules must be enforced server-side:

| Rule | Description | Enforced in |
|---|---|---|
| BR-P1 | "Create Patient" button only shows on a Qualified enquiry | `enquiries/show.blade.php` + `PatientController::store()` (reject if enquiry not qualified) |
| BR-P2 | Enquiry status auto-sets to "Converted" when a patient is created from it | `PatientController::store()` |
| BR-P3 | Assessment "Report Sent" requires `report_document_path` to be set | `AssessmentController::update()` |
| BR-P4 | Auto status transitions are one-way — never downgrade automatically | All trigger points check current status before updating |
| BR-P5 | VTA invoice for assessment cost can only reference one assessment per invoice | `AssessmentController` (when C2 is built) |
| BR-P6 | Funding cycles are always sequential — block creating a new cycle while one is active | `FundingCycleController::store()` — check `is_active` before creating |
| BR-AP1 | Associates can only upload case notes / documents for patients they are currently assigned to | `AssociatePortalController::uploadNote()` — existing `isAssigned` check |
| BR-AP2 | The "Flag for Clinical Head Review" checkbox only appears on the Final stage document upload | `portal/associate/patient.blade.php` — conditionally rendered on `stage === 'Final'` |

---

## PART 10 — TESTING CHECKLIST FOR PHASE 2

After each Sprint 1 item:

- [ ] D1: Upload a PDF on the Funding Cycle create form. Confirm the file saves and `approval_document_path` is populated. Confirm status can now advance to "Funding Approved" via the patient page.
- [ ] A1: Create an enquiry, click "Mark as Qualified". Confirm status changes to "Qualified". Confirm "Create Patient" button now appears.
- [ ] A2: Click "Create Patient" from a qualified enquiry. Confirm the patient form pre-fills company and case manager. Confirm saving creates a patient with `enquiry_id` set. Confirm the enquiry status auto-sets to "Converted".
- [ ] B1: Add/edit a patient with Next of Kin fields. Confirm they save and display on the patient page.
- [ ] D2: Create a patient with 3 funding cycles. Confirm all 3 appear on the patient page with correct balances.
- [ ] D3: Click "Add Funding Cycle" from the patient page. Confirm the create form pre-selects the patient and filters cost estimations to that patient only.
- [ ] E2: Select an associate on the invoice form. Confirm sessions × rate auto-calculates the total. Confirm the total can be manually overridden.
- [ ] E3: Select an associate on the invoice form. Confirm the patient dropdown filters to only that associate's assigned patients.
- [ ] I3: Open a patient page. Confirm the timeline tab shows all communications, documents, case notes, appointments and invoices in date order.
- [ ] I7: Assign an associate to a patient. Confirm a communication record is auto-created and appears in the patient's communication log.
- [ ] I8: Tag an email to an enquiry. Confirm the email appears in the enquiry's email section. Tag to a patient — confirm it appears on the patient page.

After Sprint 2 (when Samy's answers are received):

- [ ] C1: Create an assessment for a patient. Confirm all fields save. Confirm uploading a report and marking "Report Sent" is blocked without a document, succeeds with one.
- [ ] B3: Confirm that creating an assessment auto-sets patient status to "Assessment Scheduled".

---

## PART 11 — DEPLOYMENT AFTER EACH SPRINT

After each group of changes is complete, deploy to EC2 staging:

```powershell
# From Windows PowerShell
$key = "D:\EC2\easyerp-key.pem"
$server = "ubuntu@52.66.166.34"
$remote = "/var/www/easyerp/WebSite/vta-portal"

# Copy changed files
scp -i $key -r "C:\xampp\htdocs\VTA_NEW\vta-portal\app" "${server}:${remote}/"
scp -i $key -r "C:\xampp\htdocs\VTA_NEW\vta-portal\database" "${server}:${remote}/"
scp -i $key -r "C:\xampp\htdocs\VTA_NEW\vta-portal\resources" "${server}:${remote}/"
scp -i $key "C:\xampp\htdocs\VTA_NEW\vta-portal\routes\web.php" "${server}:${remote}/routes/"

# Then on EC2:
ssh -i $key $server "cd $remote && php artisan migrate --force && php artisan view:clear && php artisan route:clear"
```

Staging URL: `https://easyerp.co.in/vta-portal`

---

## PART 12 — QUESTIONS STATUS (updated 2026-06-28)

All questions answered by Samy on 2026-06-27 except Q4.

| Ref | Status | Summary of answer | Impact |
|---|---|---|---|
| Q1 | ✅ Answered | Qualify = referrer approves first assessment. Date + remarks sufficient. | A1 confirmed |
| Q2 | ✅ Answered | Admin only can qualify. | A1 route moved to admin group |
| Q3 | ✅ Answered | Flexible contact types — whoever was on the enquiry letter. Roles updated. | A3, B2 unblocked |
| Q4 | ✅ Decision made | Single company per enquiry — safe default. No multi-company support needed now. | B2: confirmed, one company per enquiry |
| Q5 | ✅ Answered | Step 2 = patient diagram shared as image (Associate Portal design). | No code change |
| Q6 | ✅ Answered | One-to-one. Add UNIQUE constraint on assessments.patient_id. | C1 confirmed |
| Q7 | ✅ Answered | Free text assessor (Samy or associate). | C1 confirmed |
| Q8 | ✅ Answered | Funder pays. Invoiced via VTA invoice system. Add assessment_id FK to vta_invoices. | C1, C2, E4 unblocked |
| Q9 | ✅ Answered | Sent externally by email. Special instructions governs recipients. Portal = checkbox + upload only. | C1 confirmed |
| Q10 | ✅ Answered | Keep separate. Patient created after cost agreement, not just after qualify. | A2 flow updated |
| Q11 | ✅ Answered | Payer types: Case Manager, Deputy, Solicitor, Insurer, Other — same as B2 referrer roles. | B2 roles confirmed |
| Q12 | ✅ Answered | Yes, verify invoices against estimation. Show used/remaining balance per patient. | I6 elevated to HIGH |
| Q13 | ✅ Answered | Always sequential — one active funding cycle at a time. | Validation rule added to B3 |
| Q14 | ✅ Answered | Samy is Clinical Head. Associates manually flag reports. Use needs_review on case_notes. | F1 + Associate Portal update |
| Q15 | ✅ Answered | Priority 1: funding balance. Priority 2: financial summary. Conversions/activity = low. | G2 reports re-ordered |
| Q16 | ✅ Answered | No emails in portal. Just link relevant emails to patient communications. | I8 simplified |
| Q17 | ✅ Answered | Nearest associate = suggestion only. Samy sometimes does assessment first, then allocates. | Associate field = suggestion, not auto |
| Q18 | ✅ Answered | Yes, multiple associates per patient (e.g. Samy + junior). patient_associates confirmed. | No change needed |

**All questions resolved.** Q4 decision: single company per enquiry (safe default — no multi-company support needed). All Sprint 1, Sprint 2, and Sprint 3 items are fully unblocked.
