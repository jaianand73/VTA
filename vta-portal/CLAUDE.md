# VTA Portal — Project Context for Claude

## Project Overview

**VTA Portal** is a Laravel 11 case management system for a rehabilitation therapy company (VTA). It manages the full lifecycle: Enquiry → Patient → Cost Estimation → Funding Approval → Treatment → Invoicing.

- **Stack:** Laravel 11.54 / PHP 8.2 / Blade + Tailwind CSS / MySQL via XAMPP
- **Local URL:** http://localhost/vta-portal/public
- **Code:** `C:\xampp\htdocs\vta-portal`
- **Documents/assets:** `C:\xampp\htdocs\VTA_NEW`
- **Original spec:** `C:\xampp\htdocs\VTA_NEW\Document\Original\VTA_Portal_Master_Spec.md`
- **Project context (Q&A with owner):** `C:\xampp\htdocs\VTA_NEW\Document\Original\VTA_Project_Context.md`

---

## Key Architecture Points

### Security / Storage
- `'password' => 'hashed'` cast in `User` model → bcrypt applied automatically by Laravel 11
- Documents stored on `Storage::disk('vta-documents')` → `storage/app/vta-documents` — **private disk, never publicly accessible**
- No field-level DB encryption (not required at VTA's scale; not claimed in GDPR report)

### Business Rules (spec Part 3)
- **BR-C1:** Patient cannot reach "Funding Approved" status unless `funding_cycles.approval_document_path IS NOT NULL`
- **BR-F1:** Warn (but don't block) if VTA invoice would exceed funding cycle balance
- **BR-F3:** Associate invoice `due_date` auto-set to `invoice_date + 28 days`
- **BR-F6:** VTA Invoice cannot be marked "Sent" without a document uploaded

### Services
- `FundingBalanceService::remainingBalance()` — sums only **Paid** VTA invoices (not Sent) against `approved_amount`
- `InvoiceNumberService::generate()` — produces `VTA-YYYY-NNNN` by scanning last invoice for the year

---

## Current Flow: Enquiry → Patient

### What works
1. **Enquiry created** with company linkage, source, reason, notes
2. **Communications logged** on enquiry: Type / Direction / Subject / Summary / Follow-up Date
3. **Follow-up dates** → appear as purple events on the Team Calendar ✓
4. **Documents** uploadable against the enquiry ✓
5. **Convert to Full Record** → creates/links Company + Case Manager, sets `status='Converted'`

### Critical Gap — No "Create Patient" after Conversion
`EnquiryController::convert()` redirects back to `enquiries.show` after creating the Case Manager. There is **no "Create Patient" button** and no automatic redirect to patient creation. The user must manually navigate to Patients → Create and re-select the case manager.

**Fix needed:** After conversion, show a "Create Patient for [Case Manager Name]" button that pre-fills `case_manager_id` from `converted_to_case_manager_id`.

---

## Funding Cycle Gaps (Critical)

### Gap A — No "Add Funding Cycle" button on the Patient page
`resources/views/patients/show.blade.php` Funding Overview section has no link to create a funding cycle for that patient. Users must navigate to the standalone Funding Cycles menu.

### Gap B — No document upload on Funding Cycle create form
`resources/views/funding-cycles/create.blade.php` has **no file input for `approval_document_path`**. This means BR-C1 is permanently unsatisfiable through the UI — `approval_document_path` is always NULL, so no patient can ever reach "Funding Approved" status via the normal UI. (Test data was seeded directly via phpMyAdmin.)

### Gap C — Patient page only shows first funding cycle
`$patient->fundingCycles->first()` in `patients/show.blade.php` — hard-coded to first cycle only. Multi-phase patients lose visibility of subsequent cycles.

### Gap D — Cost Estimation dropdown not filtered by patient
On the Funding Cycle create form, all patients' cost estimations appear in the dropdown, not filtered to the selected patient.

### Gap E — Associate Invoice: no rate card auto-calculation
`AssociateInvoiceController::store()` — staff enter all amounts manually. No auto-calculation from associate rate card.

### Gap F — Associate Invoice: patient dropdown not filtered by associate
All patients shown, not filtered to the selected associate's patients.

---

## Design Decisions Pending (Discuss with Samy)

### 1. Merge Cost Estimation + Funding Cycle?
**Proposal:** Add approval fields directly to `cost_estimations` table (`approved_amount`, `approval_date`, `approval_document_path`, `funder_name`, `funder_reference`, `is_approved`). VTA Invoices would link to `cost_estimation_id` instead of `funding_cycle_id`. This matches Samy's Excel process map where CE and FC are one record per phase.

### 2. "Record Funding Approval" as a formal action
Single button on patient page (visible when status = "Awaiting Funding Approval") that simultaneously:
- Creates/updates the Funding Cycle record
- Accepts the approval document upload
- Moves patient status to "Funding Approved"
Document stored in both `funding_cycles.approval_document_path` AND the `documents` table.

### 3. Email as the primary evidence trail
All key events arrive by email (funding approvals, associate invoices, payment confirmations). Proposal: extend `email_intake_logs` with financial event tagging and foreign keys to financial records (`funding_cycle_id`, `associate_invoice_id`, `vta_invoice_id`).

### 4. Rename "Finance" → "Accounts" in nav
Samy uses "Accounts" terminology.

### 5. Clinical Head Review dashboard widget
Samy's slides (Actions from meeting 25th June) reference a dashboard widget showing case notes pending clinical head sign-off. New requirement — not yet built.

---

## Files of Note

| File | Purpose |
|------|---------|
| `app/Models/User.php` | `'password'=>'hashed'` cast, `$hidden` guard |
| `app/Models/CostEstimation.php` | No approval fields yet |
| `app/Models/AssociateInvoice.php` | status: Received/Verified/Paid/Disputed |
| `app/Models/VtaInvoice.php` | status: Draft/Sent/Paid/Overdue/Cancelled |
| `app/Http/Controllers/EnquiryController.php` | `convert()` stops at CM creation — no patient step |
| `app/Http/Controllers/AssociateInvoiceController.php` | `store()` sets due_date+28d; no rate card logic |
| `app/Http/Controllers/VtaInvoiceController.php` | `updateStatus()` enforces BR-F6 |
| `app/Services/FundingBalanceService.php` | Counts only Paid invoices against balance |
| `app/Services/InvoiceNumberService.php` | VTA-YYYY-NNNN generation |
| `resources/views/funding-cycles/create.blade.php` | Missing document upload field (Gap B) |
| `resources/views/patients/show.blade.php` | Only shows first funding cycle (Gap C) |
| `config/filesystems.php` | `vta-documents` disk definition |

---

## Documents Produced

| File | Description |
|------|-------------|
| `C:\xampp\htdocs\VTA_NEW\Document\VTA_GDPR_Status_Report.html` | GDPR compliance status — all encryption claims verified against code |
| `C:\xampp\htdocs\VTA_NEW\Document\VTA_Portal_Discussion_Samy.html` | Discussion doc for Samy call — 10 questions on funding, email trail, portal gaps |

---

## Immediate Next Steps (Prioritised)

1. **Fix Gap B** — Add document upload field to `funding-cycles/create.blade.php` and handle in `FundingCycleController::store()`. This unblocks BR-C1 and is the most critical blocker.
2. **Fix Enquiry → Patient flow** — Add "Create Patient" button on `enquiries/show.blade.php` after conversion, linking to `patients/create?case_manager_id=X`.
3. **Fix Gap A** — Add "Add Funding Cycle" button to `patients/show.blade.php` Funding Overview section, pre-filled with the patient ID.
4. **Fix Gap C** — Show all funding cycles on patient page, not just `->first()`.
5. **Fix Gap D** — Filter cost estimation dropdown by selected patient (JavaScript or Livewire).
6. **Clinical Head Review widget** — Dashboard card for case notes pending sign-off (pending Samy's confirmation of requirement).
7. **Rename Finance → Accounts** in nav (pending Samy's confirmation).
8. **EC2 deployment** — Sync vta-portal code + DB to easyerp.co.in/VTA (blocked — revisit bash sandbox access).

---

## Security Note

- `D:\EC2\GitHub.txt` — PAT was rotated and file deleted (2026-06-27). New token stored in Windows Credential Manager (`git config --global credential.helper wincred`).
- EC2 private key at `D:\EC2\easyerp-key.pem` — do not expose.
