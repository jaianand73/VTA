# VTA Portal — Complete Development Overview

> **Generated:** 2026-06-21  
> **Last Updated:** 2026-06-28 (UAT Guide built and deployed; Sprint Log created; "How We Work" section added)  
> **Previous update:** 2026-06-22 (Company + Case Manager features)  
> **Project:** Vestibular Therapy Associates (VTA) — Practice Management Portal  
> **Specification:** `VTA_Portal_Master_Spec.md`  
> **Developed by:** AI-assisted (opencode); audited and hardened via Claude (Cowork)  
> **Repository:** Local only (not yet under Git version control)

> **Hosting model (clarified 2026-06-22):**
> - **Staging / Testing:** `easyerp.co.in/VTA` — owned/operated by VTA's own team (not the client). Used for internal testing and client sign-off. Server access via `D:\EC2` (SSH key + EC2 details).
> - **Production / Client:** `vestibulartherapyassociates.co.uk` — the client's live domain, hosted on **Krystal Emerald**. Go-live only happens here *after* staging testing is complete and the client has signed off.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [System Architecture](#2-system-architecture)
3. [Build Order Progress (Part 12)](#3-build-order-progress)
4. [Screens Built](#4-screens-built)
5. [Business Rules Status](#5-business-rules-status)
6. [Database Schema](#6-database-schema)
7. [Seed Data](#7-seed-data)
8. [Static Marketing Site](#8-static-marketing-site)
9. [Credentials & Access](#9-credentials--access)
10. [Development Setup](#10-development-setup)
11. [Deployment Guide](#11-deployment-guide)
12. [Gaps & Known Issues](#12-gaps--known-issues)
13. [Pending Activities — Stage 6](#13-pending-activities)
14. [Project Files Reference](#14-project-files-reference)
15. [How We Work — Iteration Cycle](#15-how-we-work)
16. [Session: 2026-06-27 — Feedback Board & Phase 2 Planning](#16-session-2026-06-27)

---

## 1. Project Overview

### What is the VTA Portal?

The VTA Portal is a purpose-built practice management system for **Vestibular Therapy Associates (VTA)**, a UK vestibular therapy practice founded by Dr. Samy Selvanayagam. It replaces three existing systems:

| Old System | Replacement in Portal |
|---|---|
| Excel referral tracker | Enquiries + Patients CRM |
| Qunote (clinical platform) | Case Notes + Appointments + Documents |
| Xero (invoicing) | Associate Invoices + VTA Invoices + Funding Cycles |

### Two Deliverables

1. **Static Marketing Site** — Public-facing website at `vestibulartherapyassociates.co.uk` (Materialize CSS)
2. **Laravel Practice Management Portal** — Password-protected web app at `portal.vestibulartherapyassociates.co.uk`

### Scope

- **37 screens** across 4 phases + settings
- **19 custom database tables** + default Laravel tables (corrected from a previous miscount of 21 — verified against both migrations and the live database)
- **4 user roles:** Admin, Staff, Associate, Case Manager
- **18 business rules** (clinical, financial, document, access)

### Audit & Testing Pass (2026-06-22)

The build was independently re-verified end-to-end: full code audit against this spec, then live browser testing of every screen as **all four roles** (Admin, Staff, Associate, Case Manager) using real test data created through the UI (company → enquiry → conversion → patient → associate assignment → documents → invoices → appointments → case notes). This surfaced **17 real defects** that static code review had missed — most were runtime-only bugs that only appear once the database has real data in it (see §12 for the full list). All 17 have been fixed and re-verified live.

### Client Testing Pass — Round 2 (2026-06-22, continued)

The client (HariKrishnan) ran his own hands-on testing pass after the round above and surfaced three further findings, all now fixed:

1. **Company and Case Manager masters were missing — client added them.** Originally, the Enquiry form captured company/case-manager as free text, which would have produced duplicate records over time (e.g. "Acme Solicitors" vs "ACME Solicitors Ltd"). The client added `company_id` / `case_manager_id` master-record dropdowns directly to the Enquiry create/edit screens (migrations `2026_06_22_000001` and `...000002`), with an inline "Add New" quick-create modal as a fallback when the company/case manager genuinely doesn't exist yet. This is now the correct pattern and has been extended to the Convert-to-Patient flow (pick an existing Company/Case Manager or create new, in one screen). Note: quick-add does not enforce uniqueness on company name — two records with the same or near-identical name can still be created if staff don't check the dropdown first. No hard duplicate-prevention has been added (a strict unique constraint risks blocking legitimate cases — e.g. two different branches of the same firm) — this is a soft recommendation, not yet acted on.
2. **Patient "Notes" field threw "The first name field is required."** Root cause: the Notes box on the Patient detail page posted to the generic `patients.update` route (`PATCH/PUT`), which runs full-patient validation requiring `first_name`/`last_name` — fields the Notes form never sends. Fixed by adding a dedicated `PATCH /patients/{patient}/notes` route → `PatientController::updateNotes()`, validating only `notes`, and pointing the form at it.
3. **No way to log communications, or attach documents, during the Enquiry stage (before conversion).** The `Communication` and `Document` models could only attach to a `case_manager_id`/`patient_id` — there was no `enquiry_id`. Combined with the fact that no page in the app actually exposed an "Add Communication" form (the backend route existed, but every page only ever rendered the log read-only), there was genuinely no way to capture the back-and-forth (calls, emails, letters) that happens between first enquiry and conversion. Fixed by: adding nullable `enquiry_id` to both `communications` and `documents` tables (migrations `2026_06_22_000003`/`...000004`); adding "Log Communication" forms (type, direction, subject, summary, date, follow-up date) to the Enquiry, Patient, and Case Manager detail pages; adding a "Document Upload" form to the Enquiry and Case Manager detail pages (Patient already had one). The Enquiry detail page now shows the full communication/document trail whether it was logged before or after conversion to a full patient record — nothing is lost at the conversion step.

**How notes vs. communications are meant to be used going forward:** each entity (Enquiry, Patient, Case Manager) still has a single free-text `notes` field — that's a quick "current state" scratchpad and gets overwritten every time it's edited, it is **not** a dated audit trail. For anything that needs a timestamped history (a phone call, an email sent, a letter received, a follow-up reminder), use "Log Communication" instead — each entry is its own row with date, direction, and author, and nothing is ever overwritten. If a fuller multi-entry, timestamped Notes log (like an activity feed) is wanted instead of the single free-text field, that would be a separate, larger change (new `notes` table per entity) — flagged here as a possible future enhancement, not yet built.

### Key Technologies

| Layer | Technology |
|---|---|
| Backend | Laravel 11.54 (PHP 8.2+) |
| Frontend | Blade + Livewire 4 + Tailwind CSS |
| Calendar | FullCalendar.js (CDN) |
| Charts | Chart.js (CDN) |
| PDF | barryvdh/laravel-dompdf |
| Email Intake | webklex/php-imap |
| Database | MySQL / MariaDB |
| Icons | Font Awesome 6 (CDN) + Blade Heroicons |
| Hosting | Krystal Emerald (cPanel) |
| Staging | easyerp.co.in (VPS, Ubuntu) |

---

## 2. System Architecture

### Directory Structure

```
C:\xampp\htdocs\vta-portal\          # Laravel Portal (development)
├── app/
│   ├── Http/
│   │   ├── Controllers/             # 19 custom controllers
│   │   │   └── Auth/                 # 8 Breeze auth controllers
│   │   └── Middleware/
│   │       └── CheckRole.php         # Role-based gate middleware
│   ├── Livewire/                     # 11 Livewire components (stubs)
│   │   ├── Dashboard/                # KpiCards, DailyActions, OverdueAlerts
│   │   ├── Enquiries/               # EnquiryList
│   │   ├── Companies/               # CompanyList
│   │   ├── Patients/                # PatientList, PatientStatusUpdate, AssociateAllocation
│   │   ├── Documents/               # DocumentUpload
│   │   ├── Appointments/            # AppointmentCalendar
│   │   └── Finance/                 # FundingBalance
│   ├── Models/                       # 20 Eloquent models
│   └── Services/                     # 5 service classes
├── bootstrap/
│   └── app.php                       # Middleware registration (role alias)
├── config/
│   └── filesystems.php               # vta-documents disk
├── database/
│   ├── migrations/                   # 24 migration files
│   └── seeders/                      # 5 seeder files
├── resources/
│   └── views/                        # 88+ Blade templates
├── routes/
│   ├── web.php                       # All application routes
│   ├── auth.php                      # Authentication routes
│   └── console.php                   # Artisan commands (empty)
├── storage/
│   └── app/documents/               # Document storage (vta-documents)
├── start-dev.ps1                     # MySQL + dev server launcher
└── reset-db.ps1                      # Database rebuild script

C:\xampp\htdocs\VTA_NEW\              # Static Marketing Site
├── index.php                         # Homepage
├── About-Us/index.php               # About page
├── How-We-Help/index.php            # Services page
├── Meet-the-Team/index.php          # Team page
├── Case-Managers-Solicitors/index.php # CMs page
├── Contact-Us/index.php             # Contact page
├── includes/header.php              # Shared header (navigation)
├── includes/footer.php              # Shared footer
├── css/                             # Stylesheets
├── gallery/                         # Images
└── Document/VTA_Portal_Master_Spec.md # Full specification
```

### Laravel Portal Architecture

```
Request → web.php → Middleware (auth, verified, role) → Controller → Service → Model → DB
                                                                   ↓
                                                            Blade View ← Livewire Component
```

### Route Structure

| Prefix | Middleware | Roles | Purpose |
|---|---|---|---|
| `/` | auth, verified, role:admin,staff | Admin, Staff | Dashboard |
| `/enquiries`, `/patients`, `/companies`, etc. | auth, verified, role:admin,staff | Admin, Staff | Phase 1 CRM |
| `/appointments`, `/case-notes` | auth, verified, role:admin,staff | Admin, Staff | Phase 2 Clinical |
| `/associate-portal` | auth, verified, role:associate | Associate | Phase 2 Portal |
| `/case-manager-portal` | auth, verified, role:case_manager | Case Manager | Phase 4 Portal |
| `/associate-invoices`, `/vta-invoices`, `/finance` | auth, verified, role:admin | Admin | Phase 3 Finance |
| `/settings` | auth, verified, role:admin | Admin | Settings |
| `/profile` | auth | All | Profile |
| `/register` | guest | None | Registration (still active) |

### Role Permissions Summary

| Capability | Admin | Staff | Associate | Case Manager |
|---|---|---|---|---|
| Dashboard | ✓ | ✓ | ✗ | ✗ |
| Enquiries CRUD | ✓ | ✓ | ✗ | ✗ |
| Companies CRUD | ✓ | ✓ | ✗ | ✗ |
| Patients (all) | ✓ | ✓ | ✗ | ✗ |
| Patients (own) | ✓ | ✓ | ✓ (BR-A1) | ✓ (BR-A2) |
| Change Status | ✓ | ✓ | ✗ | ✗ |
| Transfer CM | ✓ | ✓ | ✗ | ✗ |
| Upload Documents | ✓ | ✓ | ✓ (own pts) | ✗ |
| Download Docs | ✓ | ✓ | Permitted types | Permitted types |
| Delete Documents | ✓ | ✗ | ✗ | ✗ |
| Appointments (team) | ✓ | ✓ | ✗ | ✗ |
| Appointments (own) | ✗ | ✗ | ✓ | ✗ |
| Case Notes | ✓ | ✓ | Own patients | ✗ |
| Sign Off Notes | ✓ | ✗ | ✗ | ✗ |
| Finance | ✓ | ✗ | ✗ | ✗ |
| Settings | ✓ | ✗ | ✗ | ✗ |

---

## 3. Build Order Progress

**Total: 63 / 66 items completed (95%), plus 1 partial** — only cache optimization and the staging/production deployment steps remain undone (deployment was intentionally out of scope for this local audit pass); the cron item is partially done (schedule code confirmed correct, but the actual server cron entry can't be created until deployed).

### Stage 1 — Foundation (20/20 ✓)

| # | Item | Status | Notes |
|---|---|---|---|
| 1 | Install Laravel 11 fresh | ✓ Done | Laravel 11.54 |
| 2 | Install packages | ✓ Done | breeze, livewire, php-imap, dompdf, blade-heroicons |
| 3 | Configure Tailwind CSS | ✓ Done | Tailwind 3 (vite.config.js) |
| 4 | Run default Laravel migrations | ✓ Done | users, cache, jobs tables |
| 5 | Migration: extend users table | ✓ Done | Adds role, is_active, phone, notes, last_login_at |
| 6 | Migrations: activity_types, document_types, document_type_permissions | ✓ Done | Parts 2.2-2.4 |
| 7 | Migrations: companies, enquiries, case_managers, associates | ✓ Done | Parts 2.5-2.8 |
| 8 | Migrations: patients, patient_associates, patient_case_manager_history | ✓ Done | Parts 2.9-2.11 |
| 9 | Migrations: cost_estimations, funding_cycles | ✓ Done | Parts 2.12-2.13 |
| 10 | Migrations: appointments, case_notes | ✓ Done | Parts 2.14-2.15 |
| 11 | Migrations: documents, communications | ✓ Done | Parts 2.16-2.17 |
| 12 | Migrations: associate_invoices, vta_invoices, email_intake_logs | ✓ Done | Parts 2.18-2.20 |
| 13 | Run all seeders | ✓ Done | Users, activity types, document types, associates |
| 14 | CheckRole middleware + register 'role' alias | ✓ Done | bootstrap/app.php |
| 15 | app.blade.php layout with sidebar | ✓ Done | Role-based nav, responsive toggle |
| 16 | guest.blade.php login layout | ✓ Done | VTA branding with teal/gold |
| 17 | Breeze login — custom role redirect | ✓ Done | 4-role redirect in AuthenticatedSessionController |
| 18 | Configure vta-documents storage disk | ✓ Done | Root is `storage/app/vta-documents` (BR-D1 compliant) |
| 19 | Configure IMAP settings | ✓ Done | .env has IMAP_* vars (placeholder values) |
| 20 | Verify login for 3 seeded users | ✓ Done | Verified live 2026-06-22 — logged in successfully as admin, staff, associate, and case manager (all 4 roles, exceeding the original 3-user scope) |

### Stage 2 — Phase 1 Core Screens (17/17 ✓)

| # | Item | Status | Notes |
|---|---|---|---|
| 21 | Screen 6.2 — Dashboard | ✓ Done | KPI cards, Daily Actions, overdue alerts, upcoming appointments |
| 22 | Screen 6.3 — Enquiries List | ✓ Done | Search, filter, status badges, pagination, case manager column |
| 23 | Screen 6.4 — Log New Enquiry | ✓ Done | Quick form with all fields |
| 24 | Screen 6.5 — Enquiry Detail + Convert | ✓ Done | Converts to Company + Case Manager; supports selecting existing CM (uses that CM's company) or creating new CM + new company |
| 25 | Screen 6.6 — Companies List | ✓ Done | Search, filter by status/type |
| 26 | Screen 6.7 — Company Detail | ✓ Done | 5 sections: info, case managers (with Add/Edit/Delete modals), patients, comms, docs |
| 27 | Screen 6.8 — Case Manager Detail | ✓ Done | 6 sections: info, NDA, materials, patients, comms, docs + "Create Portal Login" |
| 28 | Screen 6.9 — Patients List | ✓ Done | Filter sidebar, statuses, needs_review, search |
| 29 | Screen 6.10 — Patient Detail | ✓ Done | 10 cards: info, status, CM, team, associates, funding, docs, cost estimations, comms, notes |
| 30 | Screen 6.11 — Add/Edit Patient | ✓ Done | Full form with validation |
| 31 | Screen 6.12 — Email Intake | ✓ Done | List with filters, link to patient/CM |
| 32 | Screen 6.13 — Communication Add Modal | ✓ Done | Reusable modal |
| 33 | DocumentController — upload/download | ✓ Done | Permission check on download |
| 34 | PatientController — status gate checks | ✓ Done | BR-C1, BR-C2, allowed transitions |
| 35 | PatientController — transfer CM | ✓ Done | BR-C4: history record created |
| 36 | EmailIntakeService — IMAP fetch | ✓ Done | Service implemented with IMAP connection, unseen message fetch, and attachment handling; console schedule defined for every 15 min |
| 37 | Dashboard — overdue alerts | ✓ Done | Unprocessed email badge + overdue invoice alerts |

### Stage 3 — Phase 2 Clinical Screens (9/9 ✓)

| # | Item | Status | Notes |
|---|---|---|---|
| 38 | Screen 7.1 — Appointments Calendar | ✓ Done | FullCalendar.js team view with filters + event detail modal |
| 39 | Screen 7.2 — Add/Edit Appointment | ✓ Done | Full form with validation |
| 40 | Screen 7.3 — Appointment Detail | ✓ Done | Detail view with case notes |
| 41 | Screen 7.4 — Case Notes | ✓ Done | Per patient list with filters |
| 42 | Screen 7.5 — Sign Off Case Note | ✓ Done | Admin-only sign off |
| 43 | Screen 7.6 — Associate Portal Dashboard | ✓ Done | KPIs, patient list, appointments |
| 44 | Screen 7.7 — Associate Portal Patient View | ✓ Done | Read-only, permits docs, case notes |
| 45 | Screen 7.8 — Associate Portal Calendar | ✓ Done | Own appointments only |
| 46 | Verify BR-A1 (portal screens) | ✓ Done | Enforced in AssociatePortalController@patient |

### Stage 4 — Phase 3 Finance Screens (9/9 ✓)

| # | Item | Status | Notes |
|---|---|---|---|
| 47 | Screen 8.7 — Cost Estimation | ✓ Done | Create/edit with versioning |
| 48 | Screen 8.1 — Funding Cycles | ✓ Done | Balance progress bar, FundingBalanceService |
| 49 | Screen 8.2 — Associate Invoices List | ✓ Done | Filter, overdue flagging |
| 50 | Screen 8.3 — Log Associate Invoice | ✓ Done | Auto-calculates, auto due_date +28 days (BR-F3) |
| 51 | Screen 8.4 — VTA Invoices List | ✓ Done | Filter by status, summary |
| 52 | Screen 8.5 — Create VTA Invoice | ✓ Done | BR-F1 warning, InvoiceNumberService (BR-F5) |
| 53 | Screen 8.6 — Finance Reports | ✓ Done | Chart.js revenue charts |
| 54 | InvoiceNumberService | ✓ Done | VTA-YYYY-NNNN format |
| 55 | Dashboard — overdue invoice alerts | ✓ Done | BR-F4 alerts for due invoices |

### Stage 5 — Settings Screens (6/6 ✓)

| # | Item | Status | Notes |
|---|---|---|---|
| 56 | Screen 10.1 — Settings Index | ✓ Done | Tab navigation |
| 57 | Screen 10.2 — Activity Types | ✓ Done | CRUD with inline edit |
| 58 | Screen 10.3 — Document Types | ✓ Done | CRUD with inline edit |
| 59 | Screen 10.4 — Permissions Matrix | ✓ Done | Toggle switches per role |
| 60 | Screen 10.5 — Associates | ✓ Done | CRUD + "Create Login" inline |
| 61 | Screen 10.6 — Users | ✓ Done | CRUD + password reset inline |

### Stage 6 — Final (2/5 ✓, 1 partial, 2 pending)

| # | Item | Status | Notes |
|---|---|---|---|
| 62 | Run full testing checklist | ✓ Done | Live-tested every screen as all 4 roles (Admin/Staff/Associate/Case Manager) with real data end-to-end. Only IMAP email fetch couldn't be tested (no live mail server available). See Appendix A for the item-by-item result. |
| 63 | Cache config/routes/views | ✗ Pending | `php artisan optimize` — run this immediately before each deployment (staging and production), not before |
| 64 | Set up cron (cPanel) | ⚠️ Partial | `routes/console.php` schedule confirmed correct (`everyFifteenMinutes()` for email intake); the actual cPanel cron entry can only be created once deployed to a server — not yet done on staging or production |
| 65 | Deploy to staging (easyerp.co.in/VTA) | ✗ Pending | Per clarified workflow: deploy here first, get client sign-off, **then** deploy to Krystal Emerald production. Access via `D:\EC2`. |
| 66 | Verify mobile screens | ✓ Done | Verified at 390×844 viewport. App remains usable but does **not** match spec Part 11.5 — tables fall back to horizontal scroll instead of converting to stacked cards. See §12 UI deviations. |

---

## 4. Screens Built

### Phase 1 — CRM (13 screens)

| Screen | Route | Controller | Built |
|---|---|---|---|
| 6.1 Login | `/login` | AuthenticatedSessionController | ✓ |
| 6.2 Dashboard | `/` | DashboardController | ✓ |
| 6.3 Enquiries List | `/enquiries` | EnquiryController@index | ✓ |
| 6.4 Log New Enquiry | `/enquiries/create` | EnquiryController@create | ✓ |
| 6.5 Enquiry Detail | `/enquiries/{id}` | EnquiryController@show + convert | ✓ |
| 6.6 Companies List | `/companies` | CompanyController@index | ✓ |
| 6.7 Company Detail | `/companies/{id}` | CompanyController@show | ✓ |
| 6.8 Case Manager Detail | `/companies/{c}/case-managers/{cm}` | CaseManagerController@show | ✓ |
| 6.9 Patients List | `/patients` | PatientController@index | ✓ |
| 6.10 Patient Detail | `/patients/{id}` | PatientController@show | ✓ |
| 6.11 Add/Edit Patient | `/patients/create`, `/patients/{id}/edit` | PatientController | ✓ |
| 6.12 Email Intake | `/email-intake` | EmailIntakeController@index | ✓ |
| 6.13 Communication Modal | Reusable partial | CommunicationController | ✓ |

### Phase 2 — Clinical (8 screens)

| Screen | Route | Controller | Built |
|---|---|---|---|
| 7.1 Calendar | `/appointments/calendar` | AppointmentController@calendar | ✓ |
| 7.2 Add/Edit Appointment | `/appointments/create`, `/appointments/{id}/edit` | AppointmentController | ✓ |
| 7.3 Appointment Detail | `/appointments/{id}` | AppointmentController@show | ✓ |
| 7.4 Case Notes | `/case-notes` | CaseNoteController | ✓ |
| 7.5 Sign Off Note | `PATCH /case-notes/{id}/sign-off` | CaseNoteController@signOff | ✓ |
| 7.6 Associate Dashboard | `/associate-portal` | AssociatePortalController@index | ✓ |
| 7.7 Associate Patient View | `/associate-portal/patients/{id}` | AssociatePortalController@patient | ✓ |
| 7.8 Associate Calendar | `/associate-portal/calendar` | AssociatePortalController@calendar | ✓ |

### Phase 3 — Finance (7 screens)

| Screen | Route | Controller | Built |
|---|---|---|---|
| 8.1 Funding Cycles | `/funding-cycles` | FundingCycleController | ✓ |
| 8.2 Associate Invoices List | `/associate-invoices` | AssociateInvoiceController@index | ✓ |
| 8.3 Log Associate Invoice | `/associate-invoices/create` | AssociateInvoiceController@create | ✓ |
| 8.4 VTA Invoices List | `/vta-invoices` | VtaInvoiceController@index | ✓ |
| 8.5 Create VTA Invoice | `/vta-invoices/create` | VtaInvoiceController@create | ✓ |
| 8.6 Finance Reports | `/finance/reports` | FinanceController@reports | ✓ |
| 8.7 Cost Estimation | `/cost-estimations` | CostEstimationController | ✓ |

### Phase 4 — Case Manager Portal (2 screens)

| Screen | Route | Controller | Built |
|---|---|---|---|
| Dashboard | `/case-manager-portal` | CaseManagerPortalController@index | ✓ |
| Patient View | `/case-manager-portal/patients/{id}` | CaseManagerPortalController@patient | ✓ |

### Settings (6 screens, tab-based)

| Screen | Route | Controller | Built |
|---|---|---|---|
| Activity Types | `/settings?tab=activity-types` | SettingsController | ✓ |
| Document Types | `/settings?tab=document-types` | SettingsController | ✓ |
| Permissions Matrix | `/settings?tab=document-permissions` | SettingsController | ✓ |
| Associates | `/settings?tab=associates` | SettingsController | ✓ |
| Users | `/settings?tab=users` | SettingsController | ✓ |

---

## 5. Business Rules Status

All rules below were not just code-reviewed but **exercised live** in the browser (creating real records, attempting the disallowed action, confirming it was blocked, then confirming the allowed path succeeds). "Verified" means both the positive and negative case were tested where applicable.

### Clinical Rules

| Rule | Description | Status | Location |
|---|---|---|---|
| BR-C1 | Status → "Funding Approved" requires funding cycle with document | ✓ Verified | `PatientController@updateStatus` |
| BR-C2 | Status → "Treatment Active" requires current = "Funding Approved" | ✓ Verified | `PatientController@updateStatus` |
| BR-C3 | Only one associate per role type per patient | ✓ Verified | `PatientController@addAssociate`. Live-tested: adding a 2nd "Assessment" associate to a patient who already had one was correctly rejected. |
| BR-C4 | Transfer creates patient_case_manager_history | ✓ Implemented | `PatientController@transfer` (via `PatientTransferService`) |
| BR-C5 | Case notes: associate's own patients or admin/staff | ✓ Implemented | `AssociatePortalController@uploadNote` |

### Financial Rules

| Rule | Description | Status | Location |
|---|---|---|---|
| BR-F1 | VTA invoice balance warning | ✓ Verified | `FundingBalanceService@willExceedBalance`. Live amber warning confirmed on the Create/Edit VTA Invoice screen when the entered amount exceeds the funding cycle's remaining balance. |
| BR-F2 | Remaining balance = approved - sum(paid) | ✓ Verified | `FundingBalanceService@remainingBalance` |
| BR-F3 | Associate invoice due_date = +28 days | ✓ Verified | `AssociateInvoiceController@store`. Confirmed: invoice dated 21/06 auto-set due date to 19/07. |
| BR-F4 | Overdue associate invoice alert (7 days) | ✓ Implemented | `DashboardController@index` |
| BR-F5 | VTA-YYYY-NNNN auto number | ✓ Verified | `InvoiceNumberService@generate`. Confirmed: first invoice generated as `VTA-2026-0001`. |
| BR-F6 | VTA invoice can't be Sent without document | ✓ Verified (both directions) | `VtaInvoiceController@updateStatus`. Negative case: marking "Sent" with no document attached was rejected. Positive case: after a document upload field was added to the Update Status form (it didn't exist before — see §12), attaching a document and retrying succeeded. |

### Document Rules

| Rule | Description | Status | Location |
|---|---|---|---|
| BR-D1 | Storage path: company/cm/patient/doc-type/uuid.ext | ✓ Implemented | `DocumentService@store` builds the full `{company}/{case-manager}/{patient}/{doc-type}/{uuid}.{ext}` path on the `vta-documents` disk |
| BR-D2 | Downloads authenticated, permission-checked | ✓ Verified | `DocumentController@download` via `DocumentPolicy`. Required fixing a bug where case managers were always denied (policy checked the wrong relationship) — see §12. |
| BR-D3 | Document appears in both CM and patient lists | ✓ Implemented | On upload, links to both |
| BR-D4 | Max 20MB, allowed types | ✓ Implemented | Validated in `DocumentController@store` |
| BR-D5 | Report passwords encrypted | ✓ Implemented | Uses `encrypt()` in store |

### Access Rules

| Rule | Description | Status | Location |
|---|---|---|---|
| BR-A1 | Associates see only own patients | ✓ Verified | `AssociatePortalController@patient` |
| BR-A2 | Case managers see only own patients | ✓ Verified | `CaseManagerPortalController@patient`. Live-tested with a real case manager portal login. |
| BR-A3 | Doc visibility controlled by permissions table | ✓ Verified (both directions) | Both portals filter by `document_type_permissions`. Negative case tested: a document of a type the associate has no permission for was correctly hidden from the list **and** blocked (403) on direct download URL. |
| BR-A4 | Only admin can access finance | ✓ Implemented | Route middleware `role:admin` |
| BR-A5 | Staff can see all but not finance | ✓ Implemented | Route middleware `role:admin,staff`. Funding Cycles specifically corrected to view-only for Staff (was previously full CRUD, which the spec's Part 4 matrix does not allow). |

---

## 6. Database Schema

### 19 Custom Tables

(Corrected from a previous count of 21 in this document — verified by both counting migration files and browsing the live `vta_portal` database directly via phpMyAdmin.)

| Table | Key Columns | Foreign Keys |
|---|---|---|
| `activity_types` | name, description, is_active, sort_order | — |
| `document_types` | name, description, is_active, sort_order | — |
| `document_type_permissions` | document_type_id, role, can_view, updated_by | →document_types, →users |
| `companies` | name, type, address, city, postcode, phone, email, status, first_contact_date | →users (created_by) |
| `enquiries` | enquirer_name, company_name, email, phone, source, status, case_manager_id, converted_to_company_id | →companies, →case_managers, →users |
| `case_managers` | user_id, company_id, first_name, last_name, email, phone, job_title, nda_signed, status | →users, →companies, →users |
| `associates` | user_id, name, region, speciality, session_rate, travel_rate_per_mile, is_active | →users |
| `patients` | case_manager_id, first_name, last_name, date_of_birth, condition, **status**(15-enum), needs_review | →case_managers, →users |
| `patient_associates` | patient_id, associate_id, role(enum: Assessment/Treatment/Supervision/MDT), start_date, end_date, is_primary | →patients, →associates, →users |
| `patient_case_manager_history` | patient_id, previous/new CM IDs, previous/new company IDs, change_date, reason | →patients, →case_managers, →companies, →users |
| `cost_estimations` | patient_id, version_number, estimated_amount, estimated_sessions | →patients, →users |
| `funding_cycles` | patient_id, cost_estimation_id, cycle_number, approved_amount, approval_document_path, is_active | →patients, →cost_estimations, →users |
| `appointments` | patient_id, associate_id, activity_type_id, scheduled_at, duration_minutes, status(enum) | →patients, →associates, →activity_types, →users |
| `case_notes` | patient_id, appointment_id, associate_id, session_date, note_type, is_signed_off | →patients, →appointments, →associates, →users |
| `documents` | document_type_id, patient_id, case_manager_id, appointment_id, file_name, stored_file_name, file_path, is_password_protected, report_password(encrypted) | →document_types, →patients, →case_managers, →appointments, →users |
| `communications` | case_manager_id, patient_id, type, direction, subject, summary, follow_up_date | →case_managers, →patients, →users |
| `associate_invoices` | associate_id, patient_id, funding_cycle_id, invoice_reference, invoice_date, total_amount, status(enum), due_date | →associates, →patients, →funding_cycles, →users |
| `vta_invoices` | patient_id, funding_cycle_id, invoice_number(unique), invoice_date, total_amount, status(enum) | →patients, →funding_cycles, →users |
| `email_intake_logs` | from_email, from_name, subject, body, received_at, has_attachments, processed, linked_patient_id, linked_case_manager_id | →patients, →case_managers, →users |

### Enum Column Corrections (found via live testing, now fixed)

Three enum columns had form dropdowns that didn't match the actual database enum values — submitting the default/blank option, or certain choices, sent `NULL` or an invalid value into a `NOT NULL` enum column and crashed with a 500 error:

| Column | Was (broken) | Now (matches DB enum) |
|---|---|---|
| `companies.type` | Insurance / Legal / Medical / Other | Case Management / Law Firm / Solicitor / Insurance / Individual / Other |
| `enquiries.source` | Website / Referral / Phone / Email / Other | Website / Referral Letter / Phone / Email / LinkedIn / Word of Mouth / Other |
| `patients.invoice_recipient_type` | Company / Individual / Insurance | Case Manager Company / Solicitor / Insurance Company / Other |

All three forms (create, edit, and any inline-edit variants) were corrected, and the controllers now also default safely server-side if a value is somehow still missing, instead of crashing.

### Status Enum (15 values on `patients` table)

```
Enquiry Logged → Response Sent → Awaiting LOI → LOI Received →
Assessment Scheduled → Assessment Completed → Report Drafted → Report Sent →
Cost Estimation Sent → Awaiting Funding Approval → Funding Approved →
Treatment Active → [Awaiting Further Funding → Funding Approved → Treatment Active] →
Discharged → Case Closed
```

---

## 7. Seed Data

> **Important — seeder files vs. live database:** `ActivityTypeSeeder.php` and `DocumentTypeSeeder.php` were updated at some point to define 7 activity types and 17 document types (matching/exceeding the spec). However, the **live `vta_portal` database has not been re-seeded since that change** — verified directly via the Settings screens and phpMyAdmin on 2026-06-22. The tables below reflect what is **actually in the database right now** (6 / 10), not what the seeder files would produce on a fresh `migrate:fresh --seed`. Run a fresh seed (or add the missing types manually via Settings) before relying on the fuller list.

### Users (5 users)

| Name | Email | Password | Role |
|---|---|---|---|
| Admin User | `admin@vta.com` | `password` | admin |
| Staff User | `staff@vta.com` | `password` | staff |
| Associate User | `associate@vta.com` | `password` | associate |
| Samy Selvanayagam | `samy@vestibulartherapyassociates.co.uk` | `ChangeMe2026!` | admin |
| Jai Anand | `jai@vestibulartherapyassociates.co.uk` | `ChangeMe2026!` | admin |
| Sheeba Rossewilliam | `sheeba@vestibulartherapyassociates.co.uk` | `ChangeMe2026!` | staff |

> **Note:** `ChangeMe2026!` users must change password on first login (policy not yet implemented).

### Activity Types (6)

| # | Name | Sort |
|---|---|---|
| 1 | Assessment | 1 |
| 2 | Treatment | 2 |
| 3 | Supervision Session | 3 |
| 4 | MDT Meeting | 4 |
| 5 | Review | 5 |
| 6 | Joint Visit | 6 |

### Document Types (10)

| # | Name | Sort |
|---|---|---|
| 1 | Assessment Report | 1 |
| 2 | Progress Report | 2 |
| 3 | Discharge Report | 3 |
| 4 | Supervision Note | 4 |
| 5 | Invoice | 5 |
| 6 | LOI (Letter of Instruction) | 6 |
| 7 | Cost Estimation | 7 |
| 8 | Clinical Notes | 8 |
| 9 | Correspondence | 9 |
| 10 | Other | 10 |

> **Note:** The spec lists 13 types; the live database currently has these 10. `DocumentTypeSeeder.php` has since been updated to define 17 types (all spec'd types plus extras), but that change hasn't been applied to this database yet — see the warning at the top of this section.

### Associates (10 — 9 seeded + 1 created during testing)

| Name | Region | Speciality |
|---|---|---|
| Kate Bryce | North East England | Falls and Balance Rehabilitation |
| Anna Bennett | Yorkshire | Advanced Vestibular Physiotherapy |
| Lewis Brennan | London and Cambridgeshire | MSK and Vestibular Rehabilitation |
| Georgios Tsiknas | West Midlands | Specialist Vestibular Physiotherapy |
| Ileana Dascalu | London | Paediatric and Adult Rehabilitation |
| Nick Hill | North West England | Specialist Vestibular Physiotherapy |
| Sultana Parvin | Manchester | Specialist Vestibular Physiotherapy |
| Sahash Palanisamy | Dorset | Specialist Vestibular Physiotherapy |
| Samy Selvanayagam | Nationwide | Consultant Vestibular Physiotherapy |

> **Note:** `associate@vta.com` user is linked to Kate Bryce via `user_id`.

### Test Data Created During the 2026-06-22 Audit

The following records were created through the live UI while verifying each workflow end-to-end. They are real rows in `vta_portal` and can be deleted (or wiped via `migrate:fresh --seed`) before client demos or staging deployment:

| Type | Record |
|---|---|
| Company | Harrison Associates (Case Management), Irwin Mitchell (Case Management) |
| Enquiry | Jane Smith → converted to Irwin Mitchell / Sarah Jones |
| Case Manager | Sarah Jones (Irwin Mitchell) — has an active portal login |
| Patient | Tom Walker (under Sarah Jones, associate Kate Bryce assigned as Assessment) |
| Documents | 2 test PDFs on Tom Walker (Assessment Report — permitted; Progress Report — used to test the permission block) |
| Cost Estimation | 1 version, £1,500 |
| Funding Cycle | 1 cycle, £1,500 approved |
| Associate Invoice | Kate Bryce → Tom Walker, £200 |
| VTA Invoice | VTA-2026-0001, Tom Walker, £2,000, status Sent |
| Appointment | Tom Walker / Kate Bryce, 24 Jun 2026 |
| Case Note | Signed off, Tom Walker |
| Associate (Settings) | "Test Associate" / Test Region |
| User (Settings) | "Test User" / `testuser@vta.com` / Staff |

---

## 8. Static Marketing Site

### Pages (6)

| Page | URL Path | Description |
|---|---|---|
| Home | `/` | Hero, services overview, CTA |
| About Us | `/About-Us/` | Practice background |
| How We Help | `/How-We-Help/` | Services detail, conditions treated |
| Meet the Team | `/Meet-the-Team/` | Staff profiles |
| Case Managers/Solicitors | `/Case-Managers-Solicitors/` | Information for referrers |
| Contact Us | `/Contact-Us/` | Contact form, details |

### Technology

- **Framework:** Materialize CSS (CDN)
- **Colors:** Teal blue `#0092b4` (primary), Gold `#f5a623` (accent), Mint `#d7fec0` (light bg)
- **Icons:** Font Awesome 6 (CDN)
- **Template:** PHP includes for header/footer

### Hosting

- **Staging / Testing (owned by VTA's dev team, not the client):** `https://easyerp.co.in/VTA/`. All internal testing and client sign-off happens here before anything touches production.
- **Production / Client (Krystal Emerald):** `https://vestibulartherapyassociates.co.uk/`. Go-live happens here only after staging sign-off.

---

## 9. Credentials & Access

### Development

| Resource | URL / Path | Credentials |
|---|---|---|
| Static Site (local) | `http://localhost/VTA_NEW/` | None (public) |
| Laravel Portal (local) | `http://localhost:8080` | See users above |
| MySQL | `127.0.0.1:3306` | root / (no password) |
| Database | `vta_portal` | — |
| PHP | `C:/xampp/php/php.exe` | — |
| MySQL Binary | `C:/xampp/mysql/bin/mysqld.exe --standalone` | Start manually |

### Staging (`easyerp.co.in/VTA` — internal, used for testing & client sign-off)

| Resource | URL / Credentials |
|---|---|
| Static Site | `https://easyerp.co.in/VTA/` |
| Portal | `http://52.66.166.34:8080` |
| Server | `ssh -i D:/EC2/easyerp-key.pem ubuntu@52.66.166.34` |
| Local access to server files/keys | `D:\EC2` |

**Workflow:** deploy and test here first → get client (VTA) sign-off → only then deploy to the Krystal Emerald production environment below. Do not deploy directly to production.

### Live

| Resource | URL |
|---|---|
| Static Site | `https://vestibulartherapyassociates.co.uk/` |
| Portal | `https://portal.vestibulartherapyassociates.co.uk/` (Krystal Emerald) |

---

## 10. Development Setup

### Prerequisites

- XAMPP (PHP 8.2+, MySQL)
- Composer
- Node.js 20+ (for Vite)
- Git

### Quick Start

```powershell
# 1. Start MySQL (must be run first)
C:\xampp\mysql\bin\mysqld.exe --standalone

# 2. Or use the launcher script
.\start-dev.ps1

# 3. In a separate terminal, start Laravel
php artisan serve --port=8080

# 4. For frontend (optional, for hot reload)
npm run dev
```

### Database Reset

```powershell
.\reset-db.ps1
# Runs: php artisan migrate:fresh --seed
```

### Important Environment Notes

- `COMPOSER_POLICY_ADVISORIES_BLOCK=false` needed for Composer on this Windows system
- MySQL uses `--standalone` flag (no `mysqld.exe` running as service by default)
- SMTP is configured to `log` driver in development
- IMAP is configured with placeholder values (`localhost`, `null` user/pass)

---

## 11. Deployment Guide

> **Deployment gate:** Staging (`easyerp.co.in/VTA`, owned by VTA's dev team) must be deployed to and tested first. Production deployment to Krystal Emerald only happens after the client has reviewed and signed off on staging. Do not skip straight to production.

### Staging (easyerp.co.in VPS)

1. SSH to EC2: `ssh -i D:/EC2/easyerp-key.pem ubuntu@52.66.166.34`
2. Pull/upload code
3. Configure `.env` with staging DB, APP_URL, etc.
4. Run: `php artisan migrate --force`
5. Run: `php artisan config:cache route:cache view:cache`
6. Serve: `php artisan serve --port=8080 --host=0.0.0.0`

### Live (Krystal Emerald)

1. Create MySQL database in cPanel
2. Deploy via Git (Krystal Git integration) or FTP
3. Configure `.env`:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://portal.vestibulartherapyassociates.co.uk`
   - Database credentials
   - IMAP credentials (DoctorBridie@outlook.com)
   - SMTP credentials for email sending
4. Run: `php artisan migrate --force`
5. Run: `php artisan config:cache route:cache view:cache`
6. Create cron job in cPanel (every 15 min):
   ```
   php /home/.../artisan schedule:run
   ```
7. Set up subdomain `portal.vestibulartherapyassociates.co.uk` pointing to the portal directory

---

## 12. Gaps & Known Issues

### Critical (should fix before production)

| # | Issue | Impact | Fix Location |
|---|---|---|---|
| 1 | BR-C3 not enforced: multiple associates same role | ✅ Fixed | `PatientController@addAssociate` |
| 2 | BR-F6 not enforced: VTA invoice Sent without document | ✅ Fixed | `VtaInvoiceController@updateStatus` |
| 3 | `/register` route still accessible | ✅ Fixed | Commented out in `routes/auth.php` |
| 4 | Document storage path doesn't match BR-D1 | ✅ Fixed | Root is now `storage/app/vta-documents` |
| 5 | BR-D1 path structure not implemented | ✅ Fixed | `DocumentService` generates `{company}/{cm}/{patient}/{doc-type}/{uuid}.{ext}` path |

### Medium Priority

| # | Issue | Impact | Fix Location |
|---|---|---|---|
| 6 | EmailIntakeService empty | ✅ Fixed | IMAP fetch implemented with attachment handling |
| 7 | PatientTransferService empty | ✅ Fixed | Transfer logic moved from controller to service |
| 8 | DocumentService empty | ✅ Fixed | Full BR-D1 path generation, storage, encrypted passwords |
| 9 | Console routes empty | ✅ Fixed | Schedule defined for email intake every 15 min |
| 10 | Seeded document types: 10 of 13 spec'd types | ⚠️ Seeder file fixed (17 types), but **live database not yet re-seeded** — still has the old 10. Run `migrate:fresh --seed` or add manually via Settings. |
| 11 | Seeded activity types: 6 of 7 spec'd types | ⚠️ Same as above — seeder file now has 7 types, live database still has the old 6. |
| 12 | `PatientPolicy` and `DocumentPolicy` not created | ✅ Fixed, then found disconnected, now properly wired | Created in `app/Policies/`, registered via `AuthServiceProvider` — but initially registered with **no controller ever calling `$this->authorize()`**, making them dead code. Added the `AuthorizesRequests` trait to the base `Controller` and wired `authorize()` calls into `DocumentController`, `PatientController`, `AssociatePortalController`, and `CaseManagerPortalController`. |

### Low Priority / Polish

| # | Issue | Impact |
|---|---|---|
| 13 | No Git repository initialized | No version control or change tracking |
| 14 | Timezone set to Europe/London | ✅ Fixed |
| 15 | Deferred manual testing: PHP lint and Vite build verified, browser interaction not | Some JS/CSS issues may exist |
| 16 | Livewire components are stub-only (11 components, render-only) | Not wired into any existing views; existing Blade + controller code works independently |
| 17 | `password` for test users is plain `password` | Weak credentials for demo accounts |
| 18 | No formal unit/feature tests written | Only manual checklist exists |

### Defects Found via Live Browser Testing (2026-06-22) — all fixed and re-verified

Static code review (the table above) did not catch these — they only surfaced once the app was actually run with real data, across all four roles. Listed roughly in the order discovered:

| # | Defect | Impact | Fix |
|---|---|---|---|
| 19 | `Patient` model had no date casts | **Crashed the entire admin/staff Dashboard** the moment any patient existed (`->format()` called on a plain string) | Added `$casts` to `Patient`, then extended the same fix to all 13 other models that were missing date casts (`Appointment`, `AssociateInvoice`, `CaseManager`, `CaseNote`, `Communication`, `Company`, `CostEstimation`, `Document`, `EmailIntakeLog`, `Enquiry`, `FundingCycle`, `PatientAssociate`, `PatientCaseManagerHistory`, `VtaInvoice`, `User`). Then swept ~25 Blade files that echoed those fields raw or fed them into `<input type="date">` expecting a string. |
| 20 | `User` model had no `associate()` / `caseManager()` relations | Silently broke every associate/case-manager permission check in `PatientPolicy` and `DocumentPolicy` — `$user->associate` always resolved to `null` | Added the two `hasOne` relations to `User` |
| 21 | Company/Enquiry/Patient create forms crashed with 500 | Dropdown values didn't match the real DB enum (see §6 enum corrections table) | Fixed dropdowns + added safe server-side defaults |
| 22 | Cost Estimation & Funding Cycle screens fully built but **zero routes registered** | Completely unreachable — `/cost-estimations` and `/funding-cycles` both 404'd | Registered the routes in `routes/web.php` (had to fix a route-ordering bug afterward — `{fundingCycle}` wildcard was swallowing the literal `/create` segment) |
| 23 | "Add Associate," "Upload Document," "Add Cost Estimation" buttons on the Patient page did nothing | Wired to `Livewire.dispatch('openModal', ...)` for a Livewire modal package that was never installed | Replaced with working inline toggle forms posting to the existing backend routes |
| 24 | Sidebar showed "Patients / Companies / Appointments / Case Notes" to Associates and Case Managers | Clicking any of them gave a 403 — dead links for those roles | Wrapped the links in the same `role:admin,staff` guard used elsewhere in the sidebar |
| 25 | Document download route was admin/staff-only | Associates and Case Managers had a working-looking download link in their own portals that always 403'd | Moved the route to a shared `auth,verified` group; access is now enforced inside `DocumentController` via `DocumentPolicy` instead of route middleware |
| 26 | Funding Cycles gave Staff full CRUD | Spec Part 4 says Staff should be **view-only** | Split the routes: `index`/`show` stay in the admin+staff group, `create`/`store`/`edit`/`update`/`destroy` moved to admin-only. Hid the Edit/Delete buttons from Staff in the views too. |
| 27 | `FundingCycle` model missing `vtaInvoices()` relation | Funding Cycle detail page crashed (`RelationNotFoundException`) the moment it had a linked invoice | Added the `hasMany` relation |
| 28 | Patient page "Funding Overview" card referenced non-existent `total_allocated` / `used` columns | Always silently showed £0.00 regardless of real funding data | Corrected to use `approved_amount` and `FundingBalanceService::invoicedAmount()` |
| 29 | Three route-name mismatches: `patients.update-status`, `associate-invoices.update-status`, `vta-invoices.update-status` (registered names are `patients.status`, `associate-invoices.status`, `vta-invoices.status`) | Patients list, Associate Invoice detail, and VTA Invoice detail pages all threw `RouteNotFoundException` the moment they tried to render the status-change control | Corrected the `route()` calls in all three Blade files |
| 30 | `case-managers.create-portal-login` route-name mismatch (registered name is `companies.case-managers.create-portal-login`, and needs both `$company` and `$caseManager` params) | Case Manager detail page crashed every time it was opened | Corrected the `route()` call |
| 31 | `DocumentPolicy`'s case_manager branch required `document.case_manager_id` to literally equal the case manager's ID | Case managers could **see** a document was listed but always got 403 trying to download it, because documents uploaded against a patient never set `case_manager_id` | Added a `belongsToCaseManager()` check that also accepts documents linked via the patient relationship |
| 32 | `last_login_at` column exists and is displayed (`->format()`) on Settings → Users, but nothing ever wrote to it | Always showed "Never"; would have crashed the moment it *did* get a value, since the column had no date cast either | Set it on every successful login in `AuthenticatedSessionController`, added the missing cast |
| 33 | No UI anywhere to attach a document to a VTA invoice | BR-F6 ("can't mark Sent without a document") would have **permanently blocked every invoice from ever being marked Sent** | Added a file upload field to the Update Status form, storing to the `vta-documents` disk |

### UI Design Rules — Not Matching Spec Part 11 (not fixed; flagging only)

These are real, confirmed differences from the spec, not bugs — the app is internally consistent, it just doesn't look like what Part 11 describes:

| Spec (Part 11) | Actual Implementation |
|---|---|
| Primary green `#1A7A4A` / navy `#1E3A5F` palette | Teal `#0092b4` / gold accent palette |
| Inter typeface | Figtree (loaded from Bunny Fonts) |
| Sidebar: Email Intake under "OPERATIONS" near the top | Email Intake under "ADMIN" near the bottom |
| Mobile tables convert to stacked label:value cards | Mobile tables fall back to horizontal scroll (functional, but not spec'd behavior) |

### Spec Deviations (intentional)

| Item | Spec | Implementation | Reason |
|---|---|---|---|
| Document storage root | `vta-documents/` | `vta-documents/` (confirmed matching — see §6) | N/A — this is now correct, no longer a deviation |
| Livewire components | Interactive UI elements | Stub components only | Existing Blade + controller pattern covers all screens; Livewire available for future enhancement |
| UserSeeder | Separate seeder file | Users seeded both in DatabaseSeeder (3 test) and UserSeeder (3 production) | Both needed for dev and prod |

---

## 13. Pending Activities

> **Deployment gate:** nothing below skips staging. All items deploy to `easyerp.co.in/VTA` (`D:\EC2`) first; production (`vestibulartherapyassociates.co.uk` on Krystal Emerald) only receives a release after client sign-off on staging.

### Data / Database

- [ ] **Re-seed or manually backfill lookup tables on the live database** — `document_types` (live DB still has 10 of the 17 the seeder file now defines) and `activity_types` (live DB still has 6 of the 7). Either run `migrate:fresh --seed` on staging (acceptable pre-go-live, destroys data) or add the missing rows manually via Settings (safe, preserves data). **This is the one concretely open item from the original Master Spec gap analysis.**

### Stage 6 — Final Checklist

- [x] **Run full testing checklist** (Part 13 of spec) — completed 2026-06-22 across all 4 roles (admin, staff, associate, case manager); see Appendix A and the defects table above
- [ ] **Cache configuration:** `php artisan config:cache route:cache view:cache` (run before deployment, not yet done — local dev intentionally runs uncached)
- [ ] **Set up cron** on the staging/production server: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` (the `everyFifteenMinutes()` task is defined in `routes/console.php`) — not applicable to local XAMPP, needed once on `easyerp.co.in`
- [ ] **Deploy to staging (`easyerp.co.in/VTA`)** for client testing — not yet done
- [ ] **Client sign-off on staging**
- [ ] **Deploy to production (Krystal Emerald, `vestibulartherapyassociates.co.uk`)** — only after sign-off
- [x] **Verify all screens responsive** on mobile viewports — checked 2026-06-22; functional but uses horizontal-scroll tables rather than the spec'd card layout (see UI Design Rules deviation note above)

### Security

- [ ] **Enforce password change** on first login for seeded users (users created with `ChangeMe2026!`)
- [ ] **Configure IMAP** with live `DoctorBridie@outlook.com` credentials on staging/production (`.env` IMAP vars) — local testing used the screen UI only, no live mailbox connected

### Infrastructure

- [ ] **Initialize Git repository** and push to remote (GitHub/GitLab) — still not done; all fixes this session exist only on the local XAMPP filesystem
- [ ] **Write unit/feature tests** for all controllers and business rules — still none exist; all verification to date has been manual browser testing
- [x] **Verify `storage/app/vta-documents`** directory exists — confirmed present and receiving uploads during live testing (case notes, cost estimations, VTA invoice documents)

### Design Decision Needed

- [ ] **Decide whether to align the UI with Master Spec Part 11** (green/navy palette, Inter font, mobile card layout, sidebar grouping) or formally accept the current teal/gold implementation as the approved design. Currently undocumented as an approved deviation — needs an explicit decision either way before go-live.

### Already Completed

- ✅ BR-C3: One associate per role enforced in `addAssociate` — verified live (duplicate-role attempt correctly blocked)
- ✅ BR-F6: VTA invoice Sent status requires document — verified live (blocked without document, succeeded after upload added)
- ✅ BR-D1: Nested folder structure in `DocumentService`
- ✅ `/register` route disabled
- ✅ PatientPolicy + DocumentPolicy created, registered, **and now actually wired into controllers** (was dead code until this session)
- ✅ EmailIntakeService implemented with IMAP fetch
- ✅ DocumentService implemented with BR-D1 path
- ✅ PatientTransferService implemented
- ✅ Console schedule defined for email intake
- ✅ Seed data (seeder file): 17 document types (all spec'd + extras) — **live DB not yet re-seeded, see Data/Database above**
- ✅ Seed data (seeder file): 7 activity types (all spec'd) — **live DB not yet re-seeded, see Data/Database above**
- ✅ Timezone set to Europe/London
- ✅ Document storage root set to `storage/app/vta-documents`
- ✅ All 17 defects in the "Defects Found via Live Browser Testing" table above (§12) — date-cast crashes, missing routes, broken buttons, enum mismatches, route-name mismatches, missing relations, permission gaps, missing invoice-document upload

---

## 14. Project Files Reference

### Portal — Controllers

| File | Methods |
|---|---|
| `app/Http/Controllers/DashboardController.php` | index |
| `app/Http/Controllers/EnquiryController.php` | index, create, store, show, edit, update, convert (handles existing/new CM) |
| `app/Http/Controllers/CompanyController.php` | index, create, store, show, edit, update |
| `app/Http/Controllers/CaseManagerController.php` | show, store, update, destroy, createPortalLogin, markNdaSigned, markMaterialsSent |
| `app/Http/Controllers/PatientController.php` | index, create, store, show, edit, update, updateStatus, transfer, toggleNeedsReview, addAssociate |
| `app/Http/Controllers/AppointmentController.php` | index, calendar, fetchEvents, create, store, show, edit, update, destroy |
| `app/Http/Controllers/CaseNoteController.php` | index, create, store, show, edit, update, destroy, signOff |
| `app/Http/Controllers/DocumentController.php` | store, download, destroy |
| `app/Http/Controllers/CommunicationController.php` | store, update, destroy |
| `app/Http/Controllers/EmailIntakeController.php` | index, link, destroy |
| `app/Http/Controllers/CostEstimationController.php` | index, create, store, show, edit, update, destroy |
| `app/Http/Controllers/FundingCycleController.php` | index, create, store, show, edit, update, destroy |
| `app/Http/Controllers/AssociateInvoiceController.php` | index, create, store, show, edit, update, updateStatus, destroy |
| `app/Http/Controllers/VtaInvoiceController.php` | index, create, store, show, edit, update, updateStatus, destroy |
| `app/Http/Controllers/FinanceController.php` | index, reports |
| `app/Http/Controllers/SettingsController.php` | index, store/update/destroy for ActivityType, DocumentType, permissions, associates, users |
| `app/Http/Controllers/AssociatePortalController.php` | index, patient, uploadNote, calendar |
| `app/Http/Controllers/CaseManagerPortalController.php` | index, patient |
| `app/Http/Controllers/ProfileController.php` | edit, update, destroy |

### Portal — Services

| File | Status | Purpose |
|---|---|---|
| `app/Services/InvoiceNumberService.php` | ✅ Implemented | Auto-generates VTA-YYYY-NNNN |
| `app/Services/FundingBalanceService.php` | ✅ Implemented | Remaining balance, usage %, will-exceed check |
| `app/Services/DocumentService.php` | ⚠️ Empty | Should handle storage path, slugs |
| `app/Services/EmailIntakeService.php` | ⚠️ Empty | Should handle IMAP fetch |
| `app/Services/PatientTransferService.php` | ⚠️ Empty | Should orchestrate transfers |

### Portal — Key Blade Views

| View | Purpose |
|---|---|
| `layouts/app.blade.php` | Main authenticated layout with sidebar |
| `layouts/guest.blade.php` | Guest layout (login) with branding |
| `dashboard/index.blade.php` | Admin/staff dashboard |
| `patients/show.blade.php` | Most complex screen (10 cards) |
| `patients/index.blade.php` | Patient list with filters |
| `enquiries/index.blade.php` | Enquiry list with search/filter |
| `appointments/calendar.blade.php` | FullCalendar.js integration |
| `finance/reports.blade.php` | Chart.js revenue charts |
| `portal/associate/dashboard.blade.php` | Associate home |
| `portal/case-manager/dashboard.blade.php` | Case manager home |
| `settings/index.blade.php` | Settings tab container |

### Portal — Routes

| Route File | Contains |
|---|---|
| `routes/web.php` | All app routes (Phases 1-4, Finance, Settings, Portal) |
| `routes/auth.php` | Breeze auth routes (login, register, password reset, email verify) |
| `routes/console.php` | Artisan commands (only `inspire`) |

---

## Appendix A: Testing Checklist

Full checklist from Part 13 of the spec. Verified live in browser on 2026-06-22 across all 4 roles (admin, staff, associate, case manager) using real test data created during the session (see §7). The only item that could not be exercised is the live IMAP fetch, since no real mailbox was connected to local XAMPP — that one stays unchecked and will need confirming on staging once `DoctorBridie@outlook.com` credentials are configured.

### Phase 1 Tests
- [x] Admin can log in and see dashboard
- [x] Staff can log in and see dashboard
- [x] Associate cannot access /patients (gets 403)
- [x] New enquiry can be logged (all fields save correctly)
- [x] Enquiry can be converted to Company + Case Manager record
- [x] Company created with all fields
- [x] Case Manager created under company
- [x] NDA marked as signed — date recorded
- [x] Materials marked as sent — date recorded
- [x] Patient created under case manager
- [x] Patient status can be changed (valid transitions only)
- [x] Patient cannot move to Funding Approved without funding cycle document (BR-C1)
- [x] Patient cannot move to Treatment Active from wrong status (BR-C2)
- [x] Patient can be transferred to new case manager — history record created
- [x] Document uploaded — appears in patient record
- [x] Document downloaded — permission check passes
- [x] Associate cannot download document type they don't have permission for (BR-A3)
- [x] Communication logged — appears in correct timeline
- [ ] Email intake: IMAP fetches new emails — **not testable locally, no live mailbox connected; verify on staging**
- [x] Email intake: email can be linked to patient (tested against manually-seeded intake log rows)
- [x] Email intake: email can create new patient (tested against manually-seeded intake log rows)
- [x] Needs Review toggle works — patient leaves Daily Actions when set to FALSE
- [x] Dashboard KPI cards show correct counts
- [x] Daily Actions shows only needs_review = TRUE patients

### Phase 2 Tests
- [x] Appointment created and appears on team calendar
- [x] Associate portal shows only own assigned patients
- [x] Associate can upload case note for own patient
- [x] Associate cannot upload case note for unassigned patient
- [x] Admin can sign off case note
- [x] Associate calendar shows own appointments only
- [x] Multiple associates can be assigned to one patient with different roles

### Phase 3 Tests
- [x] Cost estimation created and linked to patient
- [x] Funding cycle created with document — triggers status gate
- [x] Funding cycle remaining balance calculates correctly
- [x] Associate invoice logged — due date auto-set to +28 days
- [x] Overdue associate invoice appears as alert on dashboard
- [x] VTA invoice created — number auto-generated correctly (VTA-2026-0001)
- [x] VTA invoice exceeding funding cycle balance shows warning
- [x] VTA invoice cannot be marked Sent without document (BR-F6)
- [x] Finance reports load and show correct figures

### Settings Tests
- [x] New activity type added — appears in appointment form dropdown
- [x] Activity type deactivated — disappears from dropdowns
- [x] New document type added — appears in upload form
- [x] Document permission toggled — associate can/cannot see that document type
- [x] Associate session rate set — auto-calculates in invoice form
- [x] New user created — can log in with temporary password

---

## Appendix B: Environment Variables (`.env`)

```
APP_NAME="VTA Portal"
APP_ENV=local
APP_KEY=base64:UeO3qH7qhhnD6LQ6iCoZAhGLaOnjQlZrpMBM3AbxhhY=
APP_DEBUG=true
APP_TIMEZONE=UTC                     # Should be Europe/London for production
APP_URL=http://localhost/vta-portal   # Change for production

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vta_portal
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log                      # Change to smtp for production
MAIL_FROM_ADDRESS="operations@vestibulartherapyassociates.co.uk"

IMAP_HOST=localhost                   # Change for production
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_USERNAME=null                    # Set: DoctorBridie@outlook.com
IMAP_PASSWORD=null                    # Set password
```

---

## Appendix C: Quick Command Reference

```bash
# Start MySQL
C:/xampp/mysql/bin/mysqld.exe --standalone

# Start Portal
php artisan serve --port=8080

# Run migrations with seed
php artisan migrate:fresh --seed

# Cache everything for production
php artisan optimize

# Clear all cache
php artisan optimize:clear

# PHP lint check
C:/xampp/php/php.exe -l app/Http/Controllers/PatientController.php

# Vite build
npm run build

# List routes
php artisan route:list
```

---

## 15. How We Work — Iteration Cycle

VTA Portal development runs in **sprints**. Each sprint follows a fixed 6-step cycle:

```
① Samy answers questions     →   ② Dev plans (updates Agent Spec)
                                          ↓
⑦ Next sprint                ←   ③ Dev builds + pushes to EC2
       ↑                                  ↓
⑥ Dev reviews (fixes fails,  ←   ④ UAT steps added to UAT Guide
   logs suggestions)                      ↓
                                  ⑤ Samy tests (Pass / Fail / Suggest)
```

### Where things live

| Step | Tool |
|---|---|
| ① Samy's answers | **Feedback Board → Questions tab** (portal, EC2) |
| ② Dev plans | **`VTA_Portal_Phase2_Agent_Spec.md`** — updated before each sprint |
| ③ Dev builds | Local XAMPP → pushed to EC2 via `scp` |
| ④ UAT steps | **UAT Guide** (`/uat-guide` on portal) — new steps appended each sprint |
| ⑤ Samy tests | **UAT Guide** — Pass / Fail / Suggestion recorded in `uat_test_results` table |
| ⑥ Dev reviews | **Feedback Board → Bugs / Improvements tabs** — auto-populated from Fails and Suggestions |
| Permanent record | **`VTA_Portal_Sprint_Log.md`** — one entry per sprint, filled in after each sprint completes |

### Rules

1. **Never skip staging.** All builds go to EC2 first. Production (Krystal Emerald) only after Samy signs off.
2. **Agent Spec is the build contract.** Do not build anything not in the spec — add it to the spec first, then build.
3. **UAT Guide always reflects what's on EC2.** Add UAT steps in the same push as the feature.
4. **Fails block the next sprint.** If Samy records a Fail, fix it before adding new features in that area.
5. **Suggestions are logged, not acted on immediately.** They go to the Feedback Board → Improvements tab for the developer to prioritise.
6. **Sprint Log is updated at the end of each sprint**, not during — it's a record of what happened, not a plan.

### Sprint status (as of 2026-06-28)

| Sprint | Status | Summary |
|---|---|---|
| Sprint 0 | ✅ Complete | Full portal foundation (Phases 1-4, all screens, all business rules) |
| Sprint 1 | ✅ Complete | Feedback Board, UAT Guide (41 steps), Phase 2 Spec, Phase 3 questions |
| Sprint 2 | ⏳ Waiting | Blocked on Samy's P2/P3 answers + UAT results |
| Sprint 3+ | 🔜 Future | Defined after Sprint 2 completes |

→ Full detail: **`VTA_Portal_Sprint_Log.md`**

---

## 16. Session: 2026-06-27 — Feedback Board & Phase 2 Planning

### 15.1 Work completed this session

#### Analysis of Samy's working documents

Four documents shared by Samy were fully analysed:

1. **Handwritten PDF 1** — Clinical workflow (Page 1: Enquiry flow, Page 2: Patient + Assessment + Funding)
2. **Handwritten PDF 2** — Referral tracking process
3. **"Actions from meeting on 25th June.pptx"** — Portal structure overview (Slide 1: nav sections, Slide 2: dashboard widgets)
4. **"Process mapping.xlsx"** — Samy's live working Excel tracker (source of truth for data model)

Key findings:

- Enquiry flow has **4 follow-up columns** and a formal **"Qualified as Referral"** gate before patient creation
- Two separate people at enquiry stage: **Case Manager Name** and **Lead Professional**
- **No Assessment module** exists despite being a critical real-world stage
- **EnquiryController::convert()** never creates a Patient — Enquiry and Patient are permanently disconnected
- Samy calls Finance **"Accounts"** throughout all documents
- Dashboard should have **5 widgets** per PPTX Slide 2

Analysis document: `C:\xampp\htdocs\VTA_NEW\Document\VTA_Portal_Analysis_27June2026.md`

#### Feedback & Questions board (fully built and deployed)

A client-facing feedback system for Samy to answer questions and log corrections.

**New table: `portal_feedback_items`**

Migration: `2026_06_27_000001_create_portal_feedback_items_table.php`

Key columns: `type` (change/question/improvement/bug), `section`, `reference`, `priority`, `samy_status` (pending/approved/hold/rejected), `samy_response`, `samy_responded_at`, `dev_status` (not_started/in_progress/done), `is_seeded`

**Pre-loaded with 46 items:**
- 18 change items (A1–G2) — identified gaps
- 18 question items (Q1–Q18) — questions for Samy
- 10 improvement items (I1–I10) — workflow enhancements

**New files:**

| File | Purpose |
|---|---|
| `app/Models/PortalFeedbackItem.php` | Model with scopes: changes(), questions(), improvements(), bugs() |
| `app/Http/Controllers/PortalFeedbackController.php` | index(), respond(), storeBug(), storeImprovement() |
| `resources/views/portal-feedback/index.blade.php` | Full interactive Blade view |
| `database/migrations/2026_06_27_000001_create_portal_feedback_items_table.php` | Table migration |
| `database/seeders/PortalFeedbackSeeder.php` | 46 pre-loaded items (guarded by is_seeded flag) |

**Routes:** `GET/POST/PATCH /portal-feedback/*` — protected by `role:admin,developer`

**Tab visibility:**

| Role | Tabs visible |
|---|---|
| `admin` (Samy) | Questions + Corrections only |
| `developer` (Jai Anand) | All 4: Questions, Corrections, Changes, Improvements |

#### Developer role added

Migration: `2026_06_27_000002_add_developer_role_to_users_table.php` — adds `developer` to `users.role` ENUM.

Jai Anand (`jai@vestibulartherapyassociates.co.uk`) updated from `admin` → `developer`.

#### EC2 staging first deployment

| Item | Value |
|---|---|
| Server | Ubuntu 22.04, PHP 8.2.31, MySQL 8.0.46, Composer 2.9.5 |
| SSH | `ssh -i D:\EC2\easyerp-key.pem ubuntu@52.66.166.34` |
| Portal path on server | `/var/www/easyerp/WebSite/vta-portal/` |
| Staging URL | `https://easyerp.co.in/vta-portal` |
| Apache alias | `Alias /vta-portal /var/www/easyerp/WebSite/vta-portal/public` |

All feedback board files, migrations and seeders deployed and verified on EC2.

#### Bug fixes

| Bug | Fix |
|---|---|
| Feedback page opened on "Changes" approval content for Samy | `PortalFeedbackController` default tab changed from `changes` → `questions` |
| Changes/Improvements tab content visible even without tab buttons | Fixed `x-show` bindings in `portal-feedback/index.blade.php` |

### 15.2 Current user accounts (as of 2026-06-27)

| Name | Email | Role | Password |
|---|---|---|---|
| Admin User | admin@vta.com | admin | password |
| Staff User | staff@vta.com | staff | password |
| Kate Bryce | associate@vta.com | associate | password |
| Samy Selvanayagam | samy@vestibulartherapyassociates.co.uk | admin | ChangeMe2026! |
| **Jai Anand** | jai@vestibulartherapyassociates.co.uk | **developer** | ChangeMe2026! |
| Sheeba Rossewilliam | sheeba@vestibulartherapyassociates.co.uk | staff | ChangeMe2026! |

### 15.3 Phase 2 development plan

**Phase 2 spec document:** `C:\xampp\htdocs\VTA_NEW\Document\VTA_Portal_Phase2_Agent_Spec.md`

This is the single source of truth for all Phase 2 development. It contains full database migrations, controller changes with code snippets, view changes, build order, business rules, testing checklist and deployment instructions.

**Sprint 1 — 15 items, no blockers, build immediately:**

| Ref | Title | Est. |
|---|---|---|
| D1 | Document upload on Funding Cycle form | 2h |
| E1 | Rename Finance → Accounts in nav | 5m |
| G1 | Promote Email Intake to top-level nav | 30m |
| A1 | "Qualified as Referral" gate on Enquiry | 3h |
| A2 | Link Enquiry → Patient | 2h |
| A4 | Four follow-up slots on Enquiry | 1h |
| B1 | Next of Kin fields on Patient | 1h |
| D2 | Show all funding cycles on patient page | 1h |
| D3 | "Add Funding Cycle" button on patient page | 1h |
| E2 | Associate invoice rate card auto-calculation | 2h |
| E3 | Filter patient dropdown on Associate Invoice | 1h |
| I3 | Patient Journey timeline view | 2h |
| I7 | Associate allocation activity log | 30m |
| I8 | Email intake FK tagging | 2h |
| I10 | Fix CLAUDE.md project path | 30m |

**Sprint 2 — 7 items, blocked pending Samy's Q&A answers:**

| Ref | Title | Blocked by |
|---|---|---|
| A3 | Multiple enquiry contacts with roles | Q3 |
| B2 | Patient referrer roles (4 named roles) | Q3, Q4 |
| C1 | Assessment module (new table + controller + views) | Q6, Q7 |
| C2 | Assessment cost vs Cost Estimation (two figures) | Q8, C1 |
| B3 | Auto status transitions on Patient | C1 |
| F1 | Dashboard 5 widgets | Q14, Q15 |
| G2 | Reports section (4 basic reports) | Q15 |

**Sprint 3 — 3 items, depend on Sprint 2:**

I1 (enforce linear status progression), I4 (Quick Actions panel), I5 (Assessment document gate)

### 15.4 Security action required

`D:\EC2\New Text Document.txt` contains a live GitHub PAT. **Delete this file and revoke the token** at GitHub → Settings → Developer settings → Personal access tokens.
