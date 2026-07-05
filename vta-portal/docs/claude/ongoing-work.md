# VTA Portal — Ongoing Work & Open Items

_Last updated: 2026-07-05 (session 4)_

## Current Phase

**New Flow Build** — Samy and Sheeba have confirmed a redesigned case funnel: Enquiry → Referral → Patient. All existing case data wiped (clean slate). New flow build starting. UAT feedback items remain open and will be revisited after the new flow is built.

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

### Build tasks — TODO
- [ ] Migration: create `referrals` table
- [ ] Migration: add `referral_id` to `patients`
- [ ] Migration: update `enquiry_contacts` role enum (add GP, Family Member)
- [ ] Migration: strip referral-stage columns from `enquiries` (company_id, case_manager_id, converted_*, qualified_*, client_location, nearest_associate_id)
- [ ] Model: `Referral`
- [ ] Observer: `ReferralObserver`
- [ ] Register observer in `AppServiceProvider`
- [ ] Controller: `ReferralController` (index, create, store, show, update, destroy, promoteToPatient)
- [ ] Views: `referrals/index`, `referrals/create`, `referrals/show`
- [ ] Update `EnquiryController` — replace convert logic with "Promote to Referral"
- [ ] Update `enquiries/show` — replace old Convert button with Promote to Referral
- [ ] Update `PatientController::store()` — accept `referral_id` instead of `enquiry_id`
- [ ] Update nav (`app.blade.php`) — add Referrals link between Enquiries and Patients
- [ ] Seed rich sample data (local + production) covering all status stages and scenarios

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
