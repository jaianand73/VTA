# Phase 10 — Sheeba UAT Corrections Sprint

**Source:** Corrections C81 and C82 logged by Sheeba via the portal feedback system on 2026-07-05 and 2026-07-06.  
**Created:** 2026-07-06  
**Status:** Planning  

---

## Background

Sheeba performed her first full UAT walkthrough of the enquiry flow (creating a company, logging an enquiry, uploading documents and communications). She surfaced 18 distinct issues across two corrections. All issues relate to the Enquiry section and the Companies section.

One attempted screenshot upload during the correction submission appears to have failed silently — the upload UI showed files as "pending" and no new files could be added. This is logged as a P1 bug.

---

## Sprint Goal

Fix all blocking bugs, clean up the enquiry form UX, and add the missing fields Sheeba identified — without touching the Referral or Patient layers.

---

## Phase 10.1 — Blocking Bugs (must fix first)

These prevent Sheeba from using the system at all.

| # | Task | Status | Source |
|---|------|--------|--------|
| 10.1.1 | Company "View" button leads to error page | ✅ | `Company::enquirySelections()` renamed to `enquiries()` — name mismatch caused RelationNotFoundException |
| 10.1.2 | Enquiry contacts are mandatory — blocks saving with no contacts | ✅ | Contacts section now starts empty; "Add Contact" is optional. Remove button always visible. |
| 10.1.3 | Enquiry edit button is not functional | ✅ | Edit form now scrolls into view when opened. Save was also blocked by 10.1.2 (contacts); both fixed. |
| 10.1.4 | File upload on correction form stuck (pending state, new files blocked) | ✅ | Not a bug: files uploaded fine, "Pending" was approval status badge. Renamed to "Awaiting Review" to avoid confusion. document-list.blade.php also pulled into repo from production. |

### 10.1.1 — Company View Error
- **Likely cause:** `companies/show.blade.php` references a relationship not eager-loaded, or a `withCount` call fails when there are no case managers. The `$patientIds` query via `CaseManager::where('company_id')` may break if the `case_managers` table has no records for this company.
- **Fix:** Load the view defensively; guard `$patients` and `$communications` against empty collections. Investigate the exact error message on production.

### 10.1.2 — Contacts Optional
- **Current state:** Controller validation uses `contacts.*.name => required_with:contacts.*.role` — contacts array itself is nullable. The issue is likely in the Blade form: if a blank contact row is rendered by default and the browser submits it, the backend rejects the empty name.
- **Fix:** Either (a) don't render a default contact row (user clicks "Add contact" to start), or (b) strip empty rows client-side before submit via Alpine.js.

### 10.1.3 — Enquiry Edit Button
- **Current state:** The Edit button in `enquiries/show.blade.php` uses `onclick` to toggle a hidden `#enquiryForm` div. If that div's `id` is wrong or the form itself is missing from the page, nothing happens.
- **Fix:** Inspect the show page for the form element. Confirm the edit form is rendered and the toggle ID matches. If the edit form was accidentally removed during the flow migration, restore it.

### 10.1.4 — File Upload Stuck on Correction Form
- **Current state:** Sheeba tried to upload a screenshot with correction C82. The upload UI showed files as "pending" and blocked further uploads. The images never appeared in the correction record on the DB.
- **Likely cause:** Either (a) the `feedback-screenshots` storage disk is not linked on production (`storage:link` not run or broken), or (b) the `screenshots.*` validation rejected the file type/size silently and left the Alpine.js `previews` array in a broken state.
- **Fix:** 
  1. SSH and check `ls /var/www/nett-apps/vta-portal/public/storage` — confirm symlink exists.
  2. Check Laravel log for upload errors around that timestamp.
  3. If symlink missing: run `php artisan storage:link` on production.
  4. Review the Alpine.js file preview code to ensure it doesn't lock up on validation failure.

---

## Phase 10.2 — Enquiry Form UX Fixes

Changes to existing fields and layout. No new DB columns needed.

| # | Task | Status | Source |
|---|------|--------|--------|
| 10.2.1 | Rename "Notes" field label → "Special Instructions" on enquiry form | ✅ | C82 point 6 |
| 10.2.2 | Remove "Follow-ups" section from enquiry show page | ✅ | C82 point 4 |
| 10.2.3 | Replace document type dropdown with free-text label field | ✅ | C82 point 5 |
| 10.2.4 | Detach company name from case manager display label | ✅ | C81 point 4 |
| 10.2.5 | Case manager autofill: email + phone populate when case manager selected | ✅ | C81 point 5 |
| 10.2.6 | Communication log: add Edit and Delete actions per entry | ✅ | C82 point 8 |
| 10.2.7 | Enquiry create: allow multiple communication entries ("Add another") | ✅ | C81 point 7 |

### Notes for 10.2.3
The `enquiry_documents` table likely has a `type` or `category` column with a fixed enum. Changing to free-text means either (a) changing the column type on both local and production, or (b) keeping the column but changing the UI to a plain `<input type="text">`. Option (b) is lower risk. Check the column type first.

### Notes for 10.2.5
The enquiry create form has a case manager `<select>`. On change, fetch the case manager's email and phone via a small JSON endpoint (or embed the data as a JS object from the Blade template) and populate the fields. No new route needed if data is embedded.

### Notes for 10.2.6
Communications against an enquiry are stored in `enquiry_contacts` or a separate `enquiry_communications` table — confirm which. If no edit/delete routes exist, add PATCH and DELETE routes. The edit form should be inline (same expand/collapse pattern used elsewhere).

---

## Phase 10.3 — New Fields & Features

New functionality that requires schema or controller changes.

| # | Task | Status | Source |
|---|------|--------|--------|
| 10.3.1 | "Role" dropdown on enquiry form (replacing "Enquirer" field) | ❌ | C81 point 3 — blocked, waiting on Samy's role list |
| 10.3.2 | "Approved for Initial Assessment" field on enquiry (Yes/No + reason) | ✅ | C81 point 9 |
| 10.3.3 | Document upload available at enquiry creation time (not just after save) | ✅ | C81 point 6 — implemented as Option C: banner after save prompts to upload now |
| 10.3.4 | Company delete from companies list | ✅ | C81 point 1 |

### Notes for 10.3.2
New columns needed on `enquiries` table:
- `initial_assessment_approved` — boolean, nullable
- `initial_assessment_reason` — text, nullable (only relevant if approved = false)

Migration + controller validation + show/edit form update required.

### Notes for 10.3.3
Enquiry documents currently attach after the enquiry is created (because they need `enquiry_id`). To support creation-time upload:
- Option A: Create the enquiry first (ajax), then attach files — complex.
- Option B: Accept files on the create form, store temporarily, attach in `store()` — simpler.
- Option C: Upload immediately after save (UX: "Enquiry saved — add your documents now") — no code change, just UX copy update.
Option C is lowest risk. Confirm with Samy whether true creation-time upload is required or if a "next step" prompt suffices.

### Notes for 10.3.4
`CompanyController` is a full resource controller. A `destroy()` method needs to be added with a FK safety check — if the company has associated case managers or enquiries, block deletion and show a warning. If no dependents exist, soft-delete or hard-delete.

---

## Phase 10.4 — Data Cleanup (one-off, production only)

| # | Task | Status | Source |
|---|------|--------|--------|
| 10.4.1 | Delete all companies except CCM from production | ✅ | C81 point 1 — deleted 7 companies + 5 case managers; only CCM (ID 8) remains |

**Approach:** Write a PHP script locally → SCP to `/tmp/` → run → delete. Must show a confirmation list of companies to be deleted before executing. Never delete CCM.

---

## Recommended Build Order

1. **10.1 all** — unblock Sheeba first
2. **10.2.1, 10.2.2, 10.2.4** — quick label/layout wins, low risk
3. **10.2.5** — autofill (JS work, moderate effort)
4. **10.2.3, 10.2.6, 10.2.7** — form enhancements
5. **10.3.2, 10.3.4** — new fields + company delete
6. **10.3.3** — document upload at creation (confirm approach first)
7. **10.3.1** — role dropdown (blocked on Samy's list)
8. **10.4.1** — data cleanup last, after company delete feature is tested

---

## Open Questions

| # | Question | Asked | Answer |
|---|----------|-------|--------|
| Q1 | For 10.3.1 — what are the exact role options for the "Role" dropdown on the enquiry form? | Pending Samy | — |
| Q2 | For 10.3.3 — is same-page document upload required at creation time, or is "add documents immediately after save" acceptable? | Not yet asked | — |
| Q3 | For 10.3.4 — should company delete hard-delete or soft-delete (archive)? | Not yet asked | — |
