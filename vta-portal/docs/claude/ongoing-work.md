# VTA Portal — Ongoing Work & Open Items

_Last updated: 2026-07-06 (session 7)_

## Current Phase

**Flow Migration** — New 3-stage funnel (Enquiry → Referral → Patient) has been built. Now removing the old direct Enquiry→Patient conversion code and updating all dependent pages. Full migration plan documented in `docs/claude/flow-migration.md`.

---

## Open Corrections (portal_feedback_items — type = correction)

| ID | Ref | Title | Dev Status | Notes |
|----|-----|-------|------------|-------|
| 76 | C1 | Finance & Accounts inaccessible | done | Fixed. Samy needs to close. |
| 75 | D3 | Case Manager Access Control | done | Fixed. Samy needs to close. |
| 72 | C3 | Case Notes link in admin nav | done | Fixed. Samy needs to close. |
| 78 | B78 | Convert form / Create Patient button | done | Fixed: condition changed to `converted_to_case_manager_id` FK check. Samy needs to close. |
| 79 | B79 | Dashboard duplicate widgets | done | Fixed: removed duplicate Unprocessed Emails + Invoices Due panels. Samy needs to close. |
| 80 | B80 | VTA Invoice create form (8 points) | done | All 8 points fixed including Patient ID UI, auto-fill, multi-recipient email, Approved by Director status, invoice date sort. Samy needs to close. |

**Action needed from Samy:** Click "Close this correction" on all 6 above in the Corrections tab.

---

## Open Improvements (portal_feedback_items — type = improvement)

All 5 UAT improvements (A1, A2, A6, A12, A17) are marked `dev_status = done`. Developer Update (client_notes) responses have been written for all. Dev follow-up replies (after Samy's second replies) have been written for A1 and A2 and are live in the portal.

| ID | Ref | Title | Dev Status | Notes |
|----|-----|-------|------------|-------|
| 69 | A1 | Login & Dashboard Widgets | done | All 7 points fixed. Follow-up reply written responding to Samy's second reply (Associate Resources CPD space, duplicate panels cache fix). |
| 70 | A2 | Create a New Enquiry | done | All 11 points fixed. Follow-up reply written: Role dropdown awaiting Samy's role list, qualification step instructions given, Create Patient button fix confirmed. **Pending: Samy to confirm role options for Point 3 dropdown.** |
| 71 | A6 | Create Patient Record | done | All 10 points fixed. Inline editing of cost estimation rows noted as future work. |
| 73 | A12 | Allocate an Associate | done | All 7 points fixed. |
| 74 | A17 | Book an Appointment | done | Legend expanded, travel_miles removed from all views. |

---

## Open Questions (portal_feedback_items — type = question)

| ID | Ref | Title | Section | Status |
|----|-----|-------|---------|--------|
| 77 | Q19 | Case managers — should they be visible anywhere in the portal? | Associates | Awaiting Samy's answer |

**Context for Q19:** Case managers are stored under Companies as contacts. They have user accounts (role = case_manager) but cannot log in. Q19 asks whether Samy wants a dedicated list of all case managers anywhere in the portal, or if per-company viewing is sufficient. No dev action until Samy responds.

---

## Phase 10 — Sheeba UAT Corrections Sprint (IN PROGRESS, 2026-07-06)

Sheeba completed her first UAT walkthrough of the Enquiry flow. She raised corrections C81 and C82, surfacing 18 issues. Full sprint plan: `docs/sprints/phase-10-sheeba-uat-corrections.md`.

**Blocking bugs (10.1) — fix first:**
- 10.1.1 Company "View" → error page (C81 point 2)
- 10.1.2 Enquiry contacts mandatory — blocks saving (C81 point 8)
- 10.1.3 Enquiry edit button not working (C82 point 2)
- 10.1.4 File upload stuck in pending state — Sheeba's screenshot never uploaded (C82 point 7); investigate storage symlink on production first

**Corrections UI fix (deployed 2026-07-06):**
- Added inline edit form to each correction card (title/severity/description)
- Added `white-space:pre-wrap` to description and samy_response panels

**Open questions before building 10.3:**
- Q1: Role list for enquiry "Role" dropdown — waiting on Samy (replaces "Enquirer" field, C81 point 3)
- Q2: Document upload at creation time — confirm approach (C81 point 6)
- Q3: Company delete — hard-delete or soft-delete? (C81 point 1)

---

## Phase 9 Production Deploy — COMPLETE (2026-07-06)

Full Phase 1–8 build deployed to production. Smoke test run end-to-end. Two bugs found and fixed:

1. **`ReferralController::store()`** — `referral_ref` changed from required to nullable; auto-generates from `enquiry_ref` or VTA-xxx sequence if not provided.
2. **`ReferralController::storePatient()`** — `referral_date` was missing from `Patient::create()`, causing a NOT NULL constraint failure. Added `'referral_date' => now()->toDateString()`.
3. **`patients.case_manager_id` column** — was `NOT NULL` on production but referrals can have no case manager. Migration `2026_07_06_000001_make_patients_case_manager_nullable` deployed and run.

Both fixes committed locally as `0a644f7` and deployed to production via SCP. Smoke test data (VTA-001, Smoke Test) cleaned up from production after test.

---

## `how-it-works.blade.php` — Replaced with Interactive Testing Guide (2026-07-06)

The old static how-it-works page has been replaced with a full interactive 4-stage testing guide for Samy's UAT. Deployed to production at `https://nett.co.in/vta-portal/how-it-works`.

**4 stages:**
- Stage 1 — Enquiry (5 steps)
- Stage 2 — Referral (10 steps)
- Stage 3 — Associate Portal (8 steps)
- Stage 4 — Patient (9 steps)

Each step is expandable, tagged Key/Optional, with a "Things to deliberately try" section per stage. Font sizes and colours improved after initial deploy.

---

## New Flow Build — IN PROGRESS (started 2026-07-05)

### Confirmed design (from Samy & Sheeba via WhatsApp, 2026-07-05)

**Stage 1 — Enquiry**
- Source: LinkedIn, phone, email, etc.
- People providing info = Referees (multiple per enquiry, with role: GP, Solicitor, Family Member, Insurer, Other)
- No patient identity at this stage — pure intelligence gathering
- Communications, documents, meetings all captured
- Trigger to advance: patient postcode/address + case manager known → becomes a Referral

**Stage 2 — Referral**
- Patient identity now known (not yet Samy's patient)
- Special Instructions captured as free text (language, availability, access needs etc.)
- Go-ahead to Visit required before any clinical activity — recorded as date + optional document upload
- Once go-ahead received: associate assigned, assessment begins (multi-session, multi-modal)
- Proposal (cost estimation document) produced outside system and uploaded
- Proposal approval → becomes a Patient

**Stage 3 — Patient** (existing flow, unchanged)

### Confirmed decisions
- Referees: capture role (GP, Solicitor, Family Member, Insurer, Other) — same pattern as existing enquiry contacts
- Go-ahead to Visit: date field + document upload
- Proposal: uploaded as document (produced externally by Samy)
- Assessment sessions: NOT logged individually — associate manages own calendar; report covers sessions
- Special Instructions: free text field only
- All existing case data wiped on 2026-07-05 (production + local clean slate)
- `referral_ref` carries forward from `enquiry_ref` (same reference throughout lifecycle)

### Build tasks — COMPLETED
- [x] Migration: create `referrals` table
- [x] Migration: add `referral_id` to `patients`
- [x] Migration: update `enquiry_contacts` role enum (add GP, Family Member)
- [x] Model: `Referral` (with all relationships and helpers)
- [x] Observer: `ReferralObserver`
- [x] Register ReferralObserver in `AppServiceProvider`
- [x] Controller: `ReferralController` (index, create, store, show, update, destroy, convertToPatient, storePatient, approveVisit, submitProposal)
- [x] Routes: all referral routes + `enquiries.promoteToReferral`
- [x] Nav: Referrals link in sidebar between Enquiries and Patients
- [x] View: `referrals/index.blade.php`
- [x] View: `referrals/create.blade.php`
- [x] View: `referrals/show.blade.php` (with Go-ahead, Proposal, Convert panels)
- [x] View: `referrals/convert.blade.php`
- [x] `Enquiry` model: `referral()` hasOne relationship added
- [x] `EnquiryController::promoteToReferral()` added
- [x] `EnquiryController::show()` eager-loads referral
- [x] `enquiries/show.blade.php` header: Promote to Referral / View Referral buttons added
- [x] `Patient` model: `referral()` belongsTo + `referral_id` in fillable

---

## Flow Migration — Old Code Removal (IN PROGRESS)

**Reference document:** `docs/claude/flow-migration.md` — full before/after details

### Tasks

#### Code Removal
- [x] `enquiries/show.blade.php` — removed 3 old panels (Qualify, Convert to Record, Create Patient) + qualified badge + hidden CM inputs in comm/doc forms
- [x] `EnquiryController.php` — removed `qualify()` method + `convert()` method
- [x] `routes/web.php` — removed `enquiries.qualify` + `enquiries.convert` routes
- [x] `PatientController.php` — removed `enquiry_id` special-casing in `store()`; `create()` no longer accepts enquiry_id param
- [x] `patients/form.blade.php` — removed hidden `enquiry_id` input + `converted_to_case_manager_id` pre-fill logic
- [x] `EnquiryObserver.php` — removed `qualified_as_referral` change detection + log entry
- [x] `Company` model — removed `hasMany(Enquiry, 'converted_to_company_id')` orphaned relationship

#### Documentation Updates
- [ ] `understanding-each-page.blade.php` — update Enquiries section (remove Qualify/Convert steps); add Referrals section; update Patients section
- [x] `how-it-works.blade.php` — replaced entirely with interactive 4-stage testing guide (2026-07-06)
- [x] `uat-guide/show.blade.php` — Section G added covering full E→R→P flow (Phase 7.3, done in session 5)

#### Enquiry Status Enum
- [x] Updated enquiry status values: removed 'Qualified', renamed 'Converted' → 'Converted to Referral' in index filter, index colour map, and show edit dropdown

#### Production Deploy
- [ ] Deploy all referral views + controller + model + observer changes to production
- [ ] Run 3 new migrations on production (`referrals` table, `referral_id` on patients, `enquiry_contacts` role enum)
- [ ] Delete stray `/tmp/wipe_case_data.php` from production if still present

#### Seed Data
- [ ] Seed rich diversified sample data locally — covering all 7 referral statuses, all patient scenarios
- [ ] Seed same data on production so Samy can experience all scenarios

---

## Pending / Future Work

### Enquiry ID → Patient ID feature — DONE (2026-07-05)
`enquiry_ref` column (varchar 50, nullable, unique) added to `enquiries` table. Column existed on production already (from prior session); migration now tracked. All views updated: list shows single "Enquiry ID" column (duplicate removed), create form has Enquiry ID field, show/edit form has Enquiry ID field. `PatientController::store()` auto-copies `enquiry_ref` → `patient_ref` on enquiry conversion if patient_ref not already set.

### Role dropdown for enquiry form (A2 Point 3) — WAITING ON SAMY
Samy needs to confirm the exact list of role options for the enquiry form "Role" dropdown. Current reply to Samy: _"Please confirm which roles you would like included (e.g. Case Manager, Solicitor, Insurance Company, Self-Referral, Other) and we will add them immediately."_ Do not build this until Samy replies.

### A6 Point 6 — Inline edit of cost estimation rows
Cost estimation table exists with Draft/Sent status and Sent Date. Inline editing of individual rows (update amount/description without full form) is noted as a follow-up for the next development phase.

### Case manager visibility (depends on Q19 answer)
Samy may want a list of all case managers in one place (Associates page tab, Companies page tab, or standalone section). Await Q19 answer before building anything.

### Associate Resources — full CPD module
Currently a placeholder page under Subsidiary menu. Samy confirmed: it is for associate CPD and professional competency — no connection to patient records. Associates log in and complete training modules. Full module to be built in next phase.

---

## Settings → Users

Case manager accounts are hidden from the Users list (`SettingsController` filters `role = case_manager`). The Add User form only offers `admin`, `staff`, `associate` as role options — `case_manager` is not offered.

---

## How Feedback Items Work

- **Admin view:** sees `client_notes` (plain English "Developer Update") — if not set, falls back to `dev_notes`
- **Developer view:** sees `client_notes` (Client Summary box) + `dev_notes` (Technical Detail box, monospace)
- **Thread order (Improvements tab):** Developer Update → Samy's Reply → Developer Update (follow-up)
- `dev_follow_up` field: a second developer reply written *after* Samy's second reply. Only writeable by developer role via Dev Controls. Visible to all roles. Added 2026-07-04 via migration `2026_07_04_120001_add_dev_follow_up_to_portal_feedback_items.php`.
- All text display panels use `style="white-space:pre-wrap;"` — preserves line breaks and paragraph spacing without `nl2br`.
- Samy's reply textarea is always blank (never pre-filled).
- When marking an item done, always write both `client_notes` and `dev_notes`.
- Samy closes corrections by clicking "Close this correction" in the Corrections tab.
