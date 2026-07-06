# VTA Portal — Design & Architecture Decisions

This document records WHY certain things were built the way they are, so future sessions don't undo or re-debate settled choices.

---

## Case Manager Login — Blocked at Controller, Not Middleware

**Decision:** Case manager login is blocked inside `AuthenticatedSessionController::store()` — after authentication succeeds, if `$user->role === 'case_manager'`, the session is immediately destroyed and the user is redirected to login with a plain-English error message.

**Why not middleware?** Middleware would return a 403 error page, which is confusing for a non-technical case manager. The controller approach lets us show a friendly, specific message: _"Case manager portal access is not currently available. Please contact the VTA administrator."_

**Why not delete the user accounts?** Case manager records in the `case_managers` table have a `user_id` FK referencing `users`. Deleting the portal user would violate the FK constraint (or null out the link and lose the association). More importantly, the business needs to keep case manager contacts linked to their company records. The accounts stay; login is simply blocked.

---

## Feedback Notes Split into Two Fields (client_notes / dev_notes)

**Decision:** When marking a feedback item done, write two separate fields — `client_notes` (plain English for Samy/Sheeba) and `dev_notes` (technical detail for developer).

**Why not one field?** Samy and Sheeba are non-technical. Original `dev_notes` entries contained terms like `RouteNotFoundException`, file paths (`app.blade.php`), and method names — confusing and alarming to a client. A single field forces a compromise that serves neither audience well.

**Implementation:** `client_notes` column added via migration `2026_07_04_100001_add_client_notes_to_portal_feedback_items.php`. Admin role sees `client_notes` only (falls back to `dev_notes` for legacy items). Developer role sees both in labelled boxes.

---

## Tailwind CSS — Inline Styles for New Utilities

**Decision:** Any new colour, spacing, or utility not already used in the codebase must be applied via `style=""` inline attribute, not a new Tailwind class.

**Why:** The Tailwind CSS file is compiled/bundled. The build step is not part of the current workflow (no `npm run build` in the deploy process). New class names added to Blade files simply don't exist in the stylesheet and render with no effect. Inline styles always work regardless of the build state.

---

## Seeder Scripts — Local Only

**Decision:** `seed_rich_data.php` and all database seeders are **never** deployed to Bluehost/production.

**Why:** These scripts insert large amounts of fake/test data. Running them on production would corrupt Samy's real business data. The rule is absolute — no exceptions.

---

## Associate Resources — Restricted to Samy Only

**Decision:** The Associates Resource section is only accessible to the user with `can_access_associate_resources = 1` (Samy, user id = 4).

**Why:** This section contains sensitive associate commercial data (rates, compliance docs). Samy requested that only he can access it, not all admins or staff.

---

## Case Notes — Removed from Navigation, Kept in Database

**Decision:** The "Case Notes" link was removed from the admin sidebar (Clinical section in `app.blade.php`). The underlying controller, routes, and database table were NOT removed.

**Why the link was removed:** Samy requested it — case notes are accessed per-patient from the patient record page, so a global list isn't needed in the nav.

**Why the backend was kept:** Removing routes and tables is irreversible and risky. The feature is accessible by direct URL if needed. If Samy later wants it back in the nav, it's a one-line addition.

---

## Travel Miles — Removed from UI, Kept in Database

**Decision:** The `travel_miles` field was removed from the appointment create, edit, show, and calendar modal views. The column on the `appointments` table and the controller validation rule were NOT removed.

**Why:** Samy requested removal — mileage is tracked on associate invoices, not individual appointments. Keeping the DB column preserves any existing data and allows restoration without a migration.

---

## Settings → Users — Hides case_manager Accounts

**Decision:** `SettingsController::index()` uses `User::whereNotIn('role', ['case_manager'])` to exclude case managers from the Users list.

**Why:** Case managers cannot log in and are managed through Companies/case_managers. Showing them in the Users list alongside admin/staff/associate accounts is misleading — they appear to be portal users when they are actually just contact records.

---

## Enquiry Conversion Check — FK over Status String

**Decision:** The "Convert to Patient Record" button visibility and the "Create Patient" button visibility in `enquiries/show.blade.php` use `$enquiry->converted_to_case_manager_id` (the FK) rather than checking `$enquiry->status === 'Converted'`.

**Why:** The status string proved unreliable — some enquiries had been converted (FK set) but the status string was in an unexpected state (e.g. 'Qualified' not 'Converted'), causing the Create Patient button to only appear on the first enquiry. The FK is the authoritative signal: if it is set, the enquiry has been converted. If it is null, it has not.

---

## dev_follow_up — Threaded Developer Reply After Samy's Second Reply

**Decision:** A `dev_follow_up` column was added to `portal_feedback_items` (text, nullable). It renders as a second "Developer Update" block appearing after Samy's Reply in the Improvements thread. The input is only shown to developer role users (in Dev Controls), and only when a `samy_response` already exists.

**Why not update client_notes?** Updating `client_notes` would change the first Developer Update — the one that appears above Samy's Reply. That makes the response appear to predate Samy's question, which reads as out of order and confusing. A separate field rendered after Samy's Reply preserves the correct conversational thread.

---

## white-space: pre-wrap for Feedback Text Panels

**Decision:** All text display panels in `portal-feedback/index.blade.php` use `style="white-space:pre-wrap;"` on the `<p>` tag, and use plain `{{ }}` escaping (not `{!! nl2br(e()) !!}`).

**Why:** `nl2br` only converts `\n` to `<br>` — it does not preserve blank lines (paragraph spacing between sections). `white-space:pre-wrap` renders all whitespace and line breaks exactly as stored in the database, giving proper section spacing in multi-paragraph responses.

---

## Enquiry ID → Patient ID Auto-Copy on Conversion

**Decision:** When an enquiry is converted to a patient record, `PatientController::store()` auto-copies `$enquiry->enquiry_ref` to `$patient->patient_ref` — but only if the admin has not already typed a `patient_ref` in the create patient form.

**Why:** The same patient/case is tracked under a single reference throughout the lifecycle. Admin assigns `enquiry_ref` (e.g. E001) at the enquiry stage; on conversion that becomes the `patient_ref`. Admin can override or edit `patient_ref` at any time on the patient record. If the enquiry had no ref assigned, `patient_ref` stays blank for the admin to fill in later.

---

## Three-Stage Case Funnel: Enquiry → Referral → Patient

**Decision:** The case management flow is restructured into three distinct stages, replacing the previous two-stage Enquiry → Patient model.

**Why:** The original model collapsed two fundamentally different activities into one stage. At the Enquiry stage Samy has no patient identity — he is gathering intelligence from Referees. Only once he has the patient's address/postcode and case manager does the case become a Referral with a real person at the centre. Separating these stages reflects how Samy actually works and ensures the right fields (Special Instructions, Go-ahead to Visit, Associate mapping, Proposal) are captured at the right time.

**Key design choices confirmed by Samy/Sheeba (2026-07-05):**
- Referees have roles (GP, Solicitor, Family Member, Insurer, Other) — same pattern as enquiry_contacts
- Go-ahead to Visit = date field + optional document upload (not just a checkbox)
- Proposal document is produced externally and uploaded — system does not generate it
- Assessment sessions not logged individually — associate manages their own calendar
- Special Instructions = free text only (too variable for structured fields)
- The same reference (e.g. E001) follows the case through all three stages

**Migration approach:** All existing case data wiped on 2026-07-05 (production + local). Clean slate for the new flow. The `enquiry_contacts` table is repurposed as Referees. A new `referrals` table is added between `enquiries` and `patients`.

---

## patients.case_manager_id — Made Nullable

**Decision:** The `case_manager_id` column on the `patients` table was changed from `NOT NULL` to nullable via migration `2026_07_06_000001_make_patients_case_manager_nullable`.

**Why:** In the new three-stage flow, a referral can be created with no case manager assigned — Samy may not know who the case manager is at the time of referral. When `ReferralController::storePatient()` converts an approved referral to a patient, it passes `$referral->case_manager_id` which may be null. The original `patients` table schema (from the pre-referral era) had `case_manager_id NOT NULL` with no default, so any conversion from a referral with no case manager caused a 500. Making it nullable matches the business reality: a case manager can be assigned to the patient record at any point after conversion.

---

## how-it-works.blade.php — Replaced with Interactive UAT Guide

**Decision:** The original static "how it works" page was replaced in full with an interactive 4-stage UAT testing guide (2026-07-06).

**Why:** The page had described the old two-stage flow (Enquiry → Patient directly). Rather than update the static copy, it was repurposed as a living test guide for Samy — covering all four stages (Enquiry, Referral, Associate Portal, Patient) with step-by-step instructions, expand/collapse steps, Key/Optional tags, and deliberate error scenarios to probe. This doubles as developer QA documentation and a client onboarding tool.

**CSS approach:** All styles use `tg-` prefixed classes (scoped to this page) and hardcoded hex colour values — not new Tailwind classes — because the compiled Tailwind bundle is fixed. See [[tailwind-inline-styles]] decision.

---

## SSH via PowerShell Only

**Decision:** All SSH and SCP operations to Bluehost must use the PowerShell tool with `dangerouslyDisableSandbox: true`.

**Why:** The Bash tool sandbox blocks outbound network connections (exits with code 58). PowerShell without the sandbox runs normally and can reach the Bluehost server. This has been verified repeatedly — do not attempt SSH via Bash tool.
