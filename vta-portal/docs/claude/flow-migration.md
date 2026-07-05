# Flow Migration: Enquiry → Patient (Old) to Enquiry → Referral → Patient (New)

**Date:** 2026-07-05  
**Status:** Documented — Development not yet started  
**Author:** Jai  
**Confirmed by:** Samy / Sheeba

---

## Why This Change

The old system had two stages: Enquiry then Patient. In practice, there is always an
intermediate stage between intelligence gathering and a confirmed patient — a period
where the patient identity is known, a case manager is involved, clinical visits are
being approved, and a proposal is produced. This stage (Referral) was being squeezed
into the Enquiry, making Enquiry records messy and mixing "intelligence gathering" with
"active clinical preparation."

The new design gives each stage its own record and its own clear purpose.

---

## The Old Flow (What Was There)

### Stage 1 — Enquiry
- Captured caller/referee details, initial nature of case, postcode, nearest associate
- Admin could mark as "Qualified Referral" (`qualified_as_referral = true`, `qualified_date`, `qualified_remarks`)
- Status progression: New → In Progress → Qualified → Converted → Not Proceeding

### Stage 2 — Direct Convert to Patient (inside Enquiry)
- After qualifying, admin selected/created a Company and Case Manager on the Enquiry record
- This set `converted_to_company_id`, `converted_to_case_manager_id`, `converted_date`, `status = 'Converted'`
- A separate "Create Patient Record" button then appeared on the Enquiry page
- Clicking it submitted a form that POSTed to `PatientController::store()` with `enquiry_id`
- `PatientController::store()` would:
  - Copy `enquiry_ref` → `patient_ref`
  - Link `patient.enquiry_id`
  - Transfer documents from enquiry to patient
  - Transfer communications from enquiry to patient
  - Mark enquiry as 'Converted'

### Summary — Old DB columns on `enquiries` table used for this flow
| Column | Type | Purpose |
|--------|------|---------|
| `qualified_as_referral` | boolean | Flag: has this enquiry been approved to proceed? |
| `qualified_date` | date | Date admin marked it as qualified |
| `qualified_remarks` | text | Admin notes at point of qualification |
| `converted_to_company_id` | FK → companies | Company assigned during conversion |
| `converted_to_case_manager_id` | FK → case_managers | Case manager assigned during conversion |
| `converted_date` | date | Date of conversion to patient |

### Old Routes
```
POST /enquiries/{enquiry}/qualify   → EnquiryController@qualify    (enquiries.qualify)
POST /enquiries/{enquiry}/convert   → EnquiryController@convert    (enquiries.convert)
```

### Old Controller Methods
- `EnquiryController::qualify()` — sets qualified_as_referral, qualified_date, qualified_remarks, status='Qualified'
- `EnquiryController::convert()` — sets converted_to_company_id, converted_to_case_manager_id, converted_date, status='Converted'

### Old View Panels in `enquiries/show.blade.php`
1. **"Qualified" badge** — shown in header if `qualified_as_referral = true`
2. **"Mark as Qualified Referral"** panel — admin-only, shown if NOT yet qualified
3. **"Convert to Patient Record"** panel — shown if qualified AND not yet converted to CM
   - Quick-convert (if CM already on enquiry) or full form (create new company/CM)
4. **"Create Patient Record"** panel — shown if qualified AND converted to CM AND no patient yet
   - Form POSTed to `patients.store` with hidden `enquiry_id`

---

## The New Flow (What We Are Building)

### Stage 1 — Enquiry (Intelligence Gathering — No Patient Identity)
- Captures caller/referee details, role of referee, initial situation, postcode, nearest associate
- **No patient identity captured here**
- Status: New → In Progress → Converted to Referral → Not Proceeding
- Enquiry can be **promoted to a Referral** via a button on the show page
- Once promoted: a "View Referral" link replaces the Promote button

### Stage 2 — Referral (Patient Known — Pre-Approval Work)
- Created from an Enquiry (or standalone)
- Captures patient full identity (name, DOB, address, phone, email)
- Captures Company, Case Manager, Associate assignment
- Captures Special Instructions (free text)
- **Go-ahead to Visit:** date + optional document — unlocks the Assessment stage
- **Proposal:** submitted date + document — marks Proposal Submitted stage
- Status: New → In Progress → Awaiting Go-ahead → Assessment → Proposal Submitted → Approved → Not Proceeding
- When status = Approved: "Convert to Patient" button appears

### Stage 3 — Patient (Approved — Clinical Work Begins)
- Created from Referral via Convert to Patient flow
- Pre-fills name, DOB, address, phone, email from Referral
- `patient_ref` carried over from `referral_ref` (same ID throughout)
- Links `patient.referral_id` and `patient.enquiry_id` (if came via enquiry)
- All subsequent clinical work (sessions, notes, invoices) lives on the Patient record

### New `referrals` Table (Key Columns)
| Column | Type | Purpose |
|--------|------|---------|
| `referral_ref` | string | Shared ID across all 3 stages |
| `enquiry_id` | FK → enquiries | Source enquiry (if promoted) |
| `patient_first_name` | string | Patient identity |
| `patient_last_name` | string | Patient identity |
| `patient_dob` | date | Patient identity |
| `patient_address` | string | Patient identity |
| `patient_postcode` | string | Patient identity |
| `patient_phone` | string | Patient identity |
| `patient_email` | string | Patient identity |
| `company_id` | FK → companies | Insurer / employer |
| `case_manager_id` | FK → case_managers | Case manager |
| `associate_id` | FK → associates | Assigned associate |
| `special_instructions` | text | Free text — language, access, availability |
| `visit_approved_date` | date | Go-ahead to Visit date |
| `visit_approved_document` | string | Go-ahead document path |
| `proposal_submitted_date` | date | Proposal submission date |
| `proposal_document` | string | Proposal document path |
| `proposal_approved_date` | date | Optional: date proposal approved |
| `status` | enum | New / In Progress / Awaiting Go-ahead / Assessment / Proposal Submitted / Approved / Not Proceeding |
| `notes` | text | General notes |
| `created_by` | FK → users | User who created it |

### New Routes (Already Added)
```
GET  /referrals                              → referrals.index
GET  /referrals/create                       → referrals.create
POST /referrals                              → referrals.store
GET  /referrals/{referral}                   → referrals.show
PUT  /referrals/{referral}                   → referrals.update
DELETE /referrals/{referral}                 → referrals.destroy
GET  /referrals/{referral}/convert           → referrals.convertToPatient
POST /referrals/{referral}/convert           → referrals.storePatient
POST /referrals/{referral}/approve-visit     → referrals.approveVisit
POST /referrals/{referral}/submit-proposal   → referrals.submitProposal
POST /enquiries/{enquiry}/promote-to-referral → enquiries.promoteToReferral
```

---

## File-by-File Change Plan

### Already Done (Before This Migration Task)
- [x] Migration: `create_referrals_table`
- [x] Migration: `add_referral_id_to_patients_table`
- [x] Migration: `update_enquiry_contacts_role_enum` (adds GP, Family Member)
- [x] Model: `Referral` (with all relationships and helpers)
- [x] Observer: `ReferralObserver` (logs referral_created, status_changed, visit_approved, proposal_submitted)
- [x] AppServiceProvider: registers ReferralObserver
- [x] Controller: `ReferralController` (index, create, store, show, update, destroy, convertToPatient, storePatient, approveVisit, submitProposal)
- [x] Routes: all referral routes + enquiries.promoteToReferral
- [x] Nav: Referrals link in sidebar (between Enquiries and Patients)
- [x] View: `referrals/index.blade.php`
- [x] View: `referrals/create.blade.php`
- [x] View: `referrals/show.blade.php`
- [x] View: `referrals/convert.blade.php`
- [x] `Enquiry` model: added `referral()` hasOne relationship
- [x] `EnquiryController::show()`: eager-loads referral
- [x] `EnquiryController::promoteToReferral()`: redirects to referral create
- [x] `Patient` model: added `referral()` belongsTo + `referral_id` in fillable
- [x] `enquiries/show.blade.php` header: Promote to Referral / View Referral buttons added

### To Do — Removing Old Flow

#### 1. `resources/views/enquiries/show.blade.php`
**Remove:**
- "Qualified" badge in header (line ~72) — `qualified_as_referral` display
- "Mark as Qualified Referral" panel (lines ~211–233)
- "Convert to Patient Record" panel with all sub-forms (lines ~236–343)
- "Create Patient Record" panel (lines ~346–393)
- Hidden `case_manager_id` inputs referencing `converted_to_case_manager_id` in comm/document forms (lines ~376, 462, 517)

**Add / Keep:**
- The new "Promote to Referral" / "View Referral" buttons in header (already done)
- Enquiry status values updated — 'Qualified' and 'Converted' statuses replaced by 'Converted to Referral'

#### 2. `app/Http/Controllers/EnquiryController.php`
**Remove:**
- `qualify()` method (lines ~167–182)
- `convert()` method (lines ~184–244)

**Keep:**
- `promoteToReferral()` (already added — redirects to referral create)

#### 3. `routes/web.php`
**Remove:**
- `Route::post('/enquiries/{enquiry}/qualify', ...)` → `enquiries.qualify`
- `Route::post('/enquiries/{enquiry}/convert', ...)` → `enquiries.convert`

**Keep:**
- `Route::post('/enquiries/{enquiry}/promote-to-referral', ...)` → `enquiries.promoteToReferral` (already there)

#### 4. `app/Http/Controllers/PatientController.php`
**Old behaviour (lines ~81–166):**
- Accepted `enquiry_id` from form POST
- Loaded the enquiry, copied enquiry_ref → patient_ref
- Transferred documents and communications from enquiry to patient
- Updated enquiry status to 'Converted'

**New behaviour:**
- Accept `referral_id` from the convert form
- Copy `referral.referral_ref` → `patient_ref`
- Link `patient.referral_id` and `patient.enquiry_id` (from the referral's enquiry_id)
- Do NOT update the enquiry status (ReferralController::storePatient already handles this)
- Note: `storePatient()` in ReferralController already creates the patient correctly — PatientController::store() should be reviewed to remove the old `enquiry_id` special-casing so direct patient creation (admin creating a patient not from a referral) still works cleanly

#### 5. `resources/views/patients/form.blade.php`
**Remove:**
- Hidden `enquiry_id` input (line ~8)
- `converted_to_case_manager_id` pre-fill logic (line ~132)

**Note:** The `patients/form.blade.php` is used for the standalone "Create Patient" form (not from a referral). The referral → patient path goes via `referrals/convert.blade.php` → `ReferralController::storePatient()` instead.

#### 6. `app/Observers/EnquiryObserver.php`
**Remove:**
- The `qualified_as_referral` change detection and logging (lines ~30, 38)

#### 7. `app/Models/Company.php`
**Remove:**
- `hasMany(Enquiry::class, 'converted_to_company_id')` relationship (line ~25) — orphaned, nothing uses it

#### 8. `resources/views/understanding-each-page.blade.php`
**Update:**
- Enquiries section: remove reference to "Qualify" and "Convert to Patient" steps; add "Promote to Referral" step
- Add Referrals section explaining the 3 action panels (Go-ahead, Proposal, Convert to Patient)
- Patients section: note patients now arrive via Referral (not directly from Enquiry)

#### 9. `resources/views/how-it-works.blade.php`
**Update:**
- Update the flow diagram / steps to show 3 stages: Enquiry → Referral → Patient

#### 10. `resources/views/uat-guide/show.blade.php`
**Update:**
- UAT testing steps currently reference the old qualify/convert flow
- Replace with new 3-stage flow test steps

---

## Database Columns — Left In Place (No Migration Needed)

The following columns on the `enquiries` table are **no longer used by the UI** after this migration but are left in the database:
- `qualified_as_referral`
- `qualified_date`
- `qualified_remarks`
- `converted_to_company_id`
- `converted_to_case_manager_id`
- `converted_date`

**Reason:** The DB has been wiped to a clean slate. These columns cause no harm and removing them would require additional migrations with no benefit. They can be cleaned up in a future DB maintenance migration if desired.

---

## Enquiry Status Values — Before vs After

| Old Status | New Status | Notes |
|------------|------------|-------|
| New | New | unchanged |
| In Progress | In Progress | unchanged |
| Qualified | ~~Qualified~~ | Removed — qualification step no longer exists |
| Converted | Converted to Referral | Renamed to reflect actual next step |
| Not Proceeding | Not Proceeding | unchanged |

**Action required:** Update the status enum check in the Enquiry model/migration if needed.

---

## Associate / Appointment / Accounts / Reports / Support Impact

### Associates
- No direct reference to the old qualify/convert flow found in associate views or controller
- Associates are assigned at the **Referral** stage (new `associate_id` FK on referrals table)
- Previously associates were assigned directly on the Patient record
- **No code changes needed** in associate module — the new referral shows associate assignment in its show view

### Appointments
- Appointments link to patients (`patient_id`) — not to enquiries or referrals
- No reference to old qualify/convert flow found in appointment views or controller
- **No code changes needed** in appointments module

### Accounts (Invoices / Funding)
- Invoices and funding cycles link to patients (`patient_id`) — not to enquiries
- No reference to old qualify/convert flow found
- **No code changes needed** in accounts/invoices module

### Reports (Audit)
- AuditController was already fixed in this session (action name mismatches corrected)
- Reports query `activity_logs` by action name — `referral_created`, `referral_status_changed` etc. will appear once referrals are used
- The AuditController `buildDateSummary()` / `buildPatientSummary()` / `buildAssociateSummary()` methods do not reference the old qualify/convert actions
- **To add later:** Referral activity in audit reports (referral_created, referral_status_changed, referral_visit_approved, referral_proposal_submitted) — not blocking

### Support / Portal Feedback
- `portal_feedback_items` are static help text entries — no flow logic
- No references to old qualify/convert flow
- **No code changes needed**

### UAT Guide (`uat-guide/show.blade.php`)
- Line 237 references the old qualify/convert steps as part of the testing walkthrough
- **Needs updating** — replace with new 3-stage test scenario

---

## Risk / Rollback Notes

- All migrations already run locally — no rollback risk on the new `referrals` table
- Old `enquiries` columns (`qualified_as_referral` etc.) are kept in DB — removing old UI code does not drop data
- The old `enquiries.qualify` and `enquiries.convert` routes will 404 after route removal — acceptable since DB is clean and no existing records used them
- Production has NOT yet had the new migrations run — old and new code both need to be deployed together with `php artisan migrate` on production
