# VTA Portal — Project Context for Claude

> **Session Start:** Read all four supporting docs below before doing any work.
> **Session End / After each task:** Update the relevant doc(s) before moving on.
>
> Supporting docs: `docs/claude/`
> - `docs/claude/architecture.md` — data model, roles, modules, routes
> - `docs/claude/deployment.md` — SSH, SCP, artisan commands, deploy checklist
> - `docs/claude/ongoing-work.md` — open UAT items, corrections, pending questions
> - `docs/claude/decisions.md` — why things were built the way they are

---

## What This System Is

**VTA Portal** is a private case management web application for VTA, a UK-based rehabilitation therapy company. It manages the full patient lifecycle from first contact through to invoicing:

**Enquiry → Qualified → Patient → Assessment → Funding Cycle → Treatment → Invoicing**

Users of the system:
- **Admin (Samy, Sheeba)** — full access, runs the business day-to-day
- **Staff** — internal team, similar access to admin
- **Associate** — therapists/assessors, access only their own patients via the Associate Portal
- **Case Manager** — external contacts stored under Companies; they do NOT have portal login access
- **Developer (Jaian)** — full access including Feedback & audit sections hidden from others

---

## Stack

- **Framework:** Laravel 11.54 / PHP 8.2
- **Frontend:** Blade templates + Alpine.js + Tailwind CSS 3 (compiled bundle — see Tailwind note below)
- **Database:** MySQL via XAMPP locally; MySQL on Bluehost in production
- **Local URL:** `http://localhost/vta-portal/public`
- **Production URL:** `http://129.121.92.159/vta-portal/public`

---

## File Paths

| What | Path |
|------|------|
| Project root (local) | `C:\xampp\htdocs\VTA_NEW\vta-portal` |
| Assets / documents | `C:\xampp\htdocs\VTA_NEW` |
| Phase 2 spec | `C:\xampp\htdocs\VTA_NEW\Document\VTA_Portal_Phase2_Agent_Spec.md` |
| Original spec | `C:\xampp\htdocs\VTA_NEW\Document\Original\VTA_Portal_Master_Spec.md` |
| Project Q&A context | `C:\xampp\htdocs\VTA_NEW\Document\Original\VTA_Project_Context.md` |

---

## Production Server (Bluehost)

| Item | Value |
|------|-------|
| Host | `129.121.92.159` |
| SSH user | `root` |
| SSH key | `D:\blue\bluehost-key` |
| Remote project root | `/var/www/nett-apps/vta-portal/` |
| DB name | `vta_portal` |
| DB user | `vtauser` |
| DB password | `VtaPortal@2026!` |
| DB host (on server) | `localhost` |

**SSH and SCP must use PowerShell tool (dangerouslyDisableSandbox: true) — never Bash tool (exits code 58, sandbox blocks outbound SSH).**

See `docs/claude/deployment.md` for full deploy workflow.

---

## Admin Login (production)

| User | Email | Password | Role |
|------|-------|----------|------|
| Samy Selvanayagam | samy@vta.com | (ask Jaian) | admin |
| Sheeba | sheeba@vta.com | (ask Jaian) | admin |
| Developer | jaian@vta.com | (ask Jaian) | developer |

---

## Critical Rules

1. **NEVER deploy `seed_rich_data.php` or any seeder to Bluehost.** Seeders are local-only.
2. **Tailwind CSS:** The compiled bundle is fixed. Do NOT add new Tailwind utility classes in Blade — they will be invisible. Use `style=""` inline attributes for any colour, size, or spacing not already in the codebase.
3. **Case managers** are stored in the `case_managers` table (linked to companies). They have portal user accounts (role = `case_manager`) but login is blocked at `AuthenticatedSessionController`. Do not delete their user records — FK constraints link them to `case_managers.user_id`. They do not appear in Settings → Users.
4. **Associate Resources** access is restricted to Samy only (`users.can_access_associate_resources = 1`, user id = 4).
5. **Audit log** is visible to `role = developer` only.
6. **Feedback & Questions** (`portal_feedback_items`) has two note fields: `client_notes` (shown to admin in plain English) and `dev_notes` (shown to developer in technical detail). Always write both when marking an item done.

---

## Role-Based Display in Blade

```php
$isDeveloper = Auth::user()->role === 'developer';
```

Use this to show/hide technical content in views.

---

## Key Architecture Points

### Security / Storage
- `'password' => 'hashed'` cast in `User` model → bcrypt applied automatically by Laravel 11
- Documents stored on `Storage::disk('vta-documents')` → `storage/app/vta-documents` — private disk
- No field-level DB encryption (not required at VTA's scale)

### Business Rules
| Rule | Description | Enforced in |
|------|-------------|-------------|
| BR-C1 | Patient cannot reach "Funding Approved" without `approval_document_path` | `FundingCycleController` |
| BR-F1 | Warn (don't block) if VTA invoice exceeds funding cycle balance | `FundingBalanceService` |
| BR-F3 | Associate invoice `due_date` = `invoice_date + 28 days` | `AssociateInvoiceController` |
| BR-F6 | VTA Invoice cannot be marked "Sent" without a document | `VtaInvoiceController` |
| BR-P1 | "Create Patient" only on Qualified enquiry | `enquiries/show.blade.php` |
| BR-P2 | Enquiry auto-sets to "Converted" on patient create | `PatientController::store()` |
| BR-P3 | Assessment "Report Sent" requires `report_document_path` | `AssessmentController::update()` |
| BR-AP1 | Associates upload only for assigned patients | `AssociatePortalController` |

### Services
- `FundingBalanceService::remainingBalance()` — sums only **Paid** VTA invoices (not Sent) against `approved_amount`
- `InvoiceNumberService::generate()` — produces `VTA-YYYY-NNNN` format

---

## Phase 2 — Complete

All sprints built. See `docs/claude/architecture.md` for full module list.

---

## Phase 2 — New/Modified Database Tables

| Table | Changes |
|-------|---------|
| `enquiries` | Added `qualified_as_referral`, `qualified_date`, `qualified_remarks`, status includes 'Qualified' |
| `patients` | Added `enquiry_id` FK, `nok_name`, `nok_email`, `nok_phone` |
| `email_intake_logs` | Added `enquiry_id`, `vta_invoice_id`, `funding_cycle_id` FKs |
| `case_notes` | Added `stage`, `needs_review` |
| `patient_associates` | Added `sessions_approved`, `sessions_used` |
| `assessments` | NEW — 1:1 with patients, UNIQUE on patient_id |
| `vta_invoices` | Added `assessment_id` FK |
| `enquiry_contacts` | NEW — multiple contacts per enquiry |
| `patient_referrers` | NEW — multiple referrers per patient |
| `portal_feedback_items` | Added `client_notes` (plain English for admin), `dev_notes` (technical for developer), `dev_follow_up` (second developer reply after Samy's second reply) |
| `patients` | Added `patient_ref` (nullable string, manually assigned Patient ID e.g. P001) — shown on patient show/edit pages |
