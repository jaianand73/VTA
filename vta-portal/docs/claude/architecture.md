# VTA Portal — Architecture Reference

## User Roles

| Role | Description | Portal Access |
|------|-------------|---------------|
| `admin` | Samy, Sheeba — full access | All sections |
| `staff` | Internal team | Same as admin (minus some settings) |
| `associate` | External therapists | Associate Portal only (`/associate-portal/*`) |
| `case_manager` | External case manager contacts | **No login** — blocked at `AuthenticatedSessionController` |
| `developer` | Jaian | All sections + Audit log + full Feedback detail |

## Modules / Navigation

The sidebar is divided into two logical groups — **Core Process** (the patient workflow, in order) and **Supportive** (reference data and support tools).

### Core Process (admin/staff) — in workflow order
| Section | Route prefix | Controller | Notes |
|---------|-------------|------------|-------|
| Dashboard | `/dashboard` | `DashboardController` | |
| Enquiries | `/enquiries` | `EnquiryController` | Stage 1 — intelligence gathering, Referees captured |
| Referrals | `/referrals` | `ReferralController` | Stage 2 — known patient, Go-ahead, Assessment, Proposal (NEW — 2026-07-05) |
| Patients | `/patients` | `PatientController` | Stage 3 — approved cases in treatment |
| Associates | `/settings?tab=associates` | `SettingsController` | Links to settings associates tab |
| Appointments | `/appointments` | `AppointmentController` | |
| Associate Invoices | `/associate-invoices` | `AssociateInvoiceController` | admin only |
| VTA Invoices | `/vta-invoices` | `VtaInvoiceController` | admin only |
| Accounts Reports | `/accounts-reports` | `AccountsReportController` | admin only |

### Supportive (admin/staff)
| Section | Route prefix | Controller | Notes |
|---------|-------------|------------|-------|
| Companies | `/companies` | `CompanyController` | |
| Associate Resources | `/associate-resources` | redirect to settings | Samy only (`can_access_associate_resources = 1`) |
| Emails | `/email-intake` | `EmailIntakeController` | |
| Reports | `/reports` | `ReportController` | admin + developer |
| Audit | `/audit-log` | `AuditLogController` | developer only |

### Settings / Support (admin)
| Section | Route prefix |
|---------|-------------|
| Settings | `/settings` |
| How It Works | `/how-it-works` |
| Understanding Each Page | `/understanding-each-page` |
| Feedback | `/portal-feedback` |
| UAT Testing | `/uat-guide` |

### Associate Portal (role = associate)
| Section | Route prefix |
|---------|-------------|
| Dashboard | `/associate-portal/dashboard` |
| My Patients | `/associate-portal/patients` |
| My Invoices | `/associate-portal/invoices` |

## Database — Key Tables

| Table | Purpose |
|-------|---------|
| `users` | Portal login accounts (admin, staff, associate, case_manager, developer) |
| `enquiries` | Initial referral/enquiry records — `enquiry_ref` (varchar 50, nullable, unique) is the manual Enquiry ID (e.g. E001) |
| `enquiry_contacts` | Multiple contacts per enquiry with roles |
| `patients` | Patient records (linked to enquiry via `enquiry_id`) |
| `patient_referrers` | Referrers linked to patients |
| `patient_associates` | Associate assignment to patient (with session counts) |
| `assessments` | 1:1 with patients — assessment details |
| `appointments` | Patient appointments (Scheduled/Completed/Cancelled/DNA) |
| `funding_cycles` | Funding approval cycles per patient |
| `vta_invoices` | VTA (company) invoices to funders |
| `associate_invoices` | Invoices from associates to VTA |
| `associates` | Associate profile (linked to `users` via `user_id`) |
| `companies` | Case management companies / law firms |
| `case_managers` | Case manager contacts under companies (`user_id` nullable FK to `users`) |
| `communications` | Associate communications / follow-ups |
| `patient_mdt_meetings` | MDT meeting records |
| `email_intake_logs` | Emails received, tagged to enquiry/invoice/cycle |
| `activity_types` | Lookup — appointment activity types |
| `document_types` | Lookup — document categories |
| `document_type_permissions` | Per-role view permissions for document types |
| `portal_feedback_items` | Feedback, corrections, improvements, questions (UAT tracker) |
| `audit_logs` | Action log (developer-only view) |

## portal_feedback_items — Column Reference

| Column | Type | Purpose |
|--------|------|---------|
| `type` | enum | `question`, `correction`, `improvement` |
| `section` | string | Grouping label (Enquiry, Patients, Associates, etc.) |
| `reference` | string | e.g. Q19, C3, A17 |
| `priority` | enum | `critical`, `high`, `medium`, `low` |
| `title` | string | Short label |
| `description` | text | Full description / question text |
| `dev_context` | text | Internal dev notes (not shown to admin) |
| `samy_status` | string | Admin's status on item |
| `samy_response` | text | Admin's answer / response |
| `dev_status` | enum | `not_started`, `in_progress`, `done` |
| `dev_notes` | text | **Technical detail** — shown to developer only |
| `client_notes` | text | **Plain English** — shown to admin (Samy/Sheeba) |
| `severity` | string | |
| `raised_by` | string | |
| `is_seeded` | boolean | 1 = pre-loaded at UAT start |
| `screenshots` | text | |

## Key Files — Quick Reference

| Purpose | File |
|---------|------|
| Nav / sidebar | `resources/views/layouts/app.blade.php` |
| Login controller | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| Appointment calendar | `resources/views/appointments/calendar.blade.php` |
| Feedback view | `resources/views/portal-feedback/index.blade.php` |
| Feedback controller | `app/Http/Controllers/PortalFeedbackController.php` |
| Settings controller | `app/Http/Controllers/SettingsController.php` |
| Funding balance logic | `app/Services/FundingBalanceService.php` |
| Invoice number logic | `app/Services/InvoiceNumberService.php` |
| Routes | `routes/web.php` |
