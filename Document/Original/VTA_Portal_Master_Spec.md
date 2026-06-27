# VTA Portal — Master Specification Document
## Vestibular Therapy Associates — Practice Management System
### Version 2.0 | June 2026 | Single Source of Truth for Code Agent

---

## CRITICAL INSTRUCTIONS FOR CODE AGENT

Read this entire document before writing a single line of code. This document is the complete, authoritative specification for the VTA Portal. Every decision about database design, screen layout, business logic and access control is defined here.

**Rules for the code agent:**
- Build exactly what is specified. Do not add features not described here.
- Do not make assumptions. If something appears ambiguous, implement the most conservative interpretation.
- Follow the build order in Part 12 exactly. Do not skip ahead.
- Every screen in Parts 6, 7 and 8 must be built completely before moving to the next phase.
- All business rules in Part 3 must be enforced in the backend — not just the frontend.

---

## PART 0 — DOCUMENT STRUCTURE

```
Part 0   — Document Structure (this section)
Part 1   — Project Overview
Part 2   — Complete Database Schema
Part 3   — Business Rules
Part 4   — Roles and Permissions Matrix
Part 5   — Application Structure
Part 6   — Phase 1 Screens (CRM, Patients, Documents, Communications, Email Intake, Dashboard)
Part 7   — Phase 2 Screens (Clinical, Appointments, Associate Portal)
Part 8   — Phase 3 Screens (Finance, Invoicing, Reports)
Part 9   — Workflow and Status Logic
Part 10  — Settings Screens (Dynamic Lookups)
Part 11  — UI Design Rules
Part 12  — Build Order for Code Agent
Part 13  — Testing Checklist
```

---

## PART 1 — PROJECT OVERVIEW

### 1.1 What This System Is
The VTA Portal is a purpose-built practice management system for Vestibular Therapy Associates (VTA), a UK vestibular therapy practice founded by Dr. Samy Selvanayagam. It replaces three existing systems — an Excel referral tracker, Qunote (clinical platform) and Xero (invoicing) — with a single web application hosted on Krystal Emerald.

### 1.2 Business Context
VTA receives referrals from case management companies, law firms and solicitors. The referral journey begins with an enquiry, progresses through clinical assessment, treatment delivery and ends with discharge and financial reconciliation. The full workflow has 19 steps across 5 phases. Currently managed manually across 6 separate systems.

### 1.3 Key Business Relationships
```
Company (e.g. Harrison Associates, Irwin Mitchell)
    └── Case Manager (employed by company, manages client cases)
            └── Patient (referred by case manager)
                    ├── Associate (delivers clinical treatment)
                    ├── Funder (approves and pays — may be different from company)
                    └── Funding Cycles (each cycle has own approved sessions + amount)
```

**Important edge cases:**
- A patient may be transferred to a different case manager within the same company
- A patient may follow a case manager who moves to a different company
- A patient may have multiple associates across their treatment (e.g. Samy for assessment, associate for treatment)
- A patient may have multiple funding cycles (Phase 1, Phase 2, Phase 3...) — each is a separate cost estimation and approval

### 1.4 Technology Stack
| Layer | Technology | Version |
|---|---|---|
| Backend Framework | Laravel | 11.x |
| Frontend | Laravel Blade + Livewire | 3.x |
| Database | MySQL | 8.x |
| CSS Framework | Tailwind CSS | 3.x |
| Authentication | Laravel Breeze (Blade stack) | Latest |
| Calendar | FullCalendar.js | 6.x |
| Charts | Chart.js | 4.x |
| File Storage | Laravel Storage (local disk) | Built-in |
| Email | Laravel Mail + IMAP (webklex/php-imap) | Latest |
| Icons | Heroicons via Blade UI Kit | Latest |
| PDF | barryvdh/laravel-dompdf | Latest |

### 1.5 Hosting
- **Platform:** Krystal Emerald (UK shared hosting, cPanel)
- **Domain:** vestibulartherapyassociates.co.uk
- **Portal URL:** portal.vestibulartherapyassociates.co.uk
- **Email:** operations@vestibulartherapyassociates.co.uk (and individual staff emails)
- **Database:** MySQL via cPanel
- **File storage:** Server local disk (unlimited NVMe on Krystal Emerald)
- **Backups:** Every 4 hours (Krystal automated)

### 1.6 Phased Delivery
| Phase | Timeline | Replaces |
|---|---|---|
| Phase 1 | Month 1 | Excel tracker + TBITA Master folders |
| Phase 2 | Month 2 | Qunote (clinical platform) |
| Phase 3 | Month 3 | Xero (invoicing + payment tracking) |
| Phase 4 | Future | Case Manager portal |
| Phase 5 | Future | Patient portal |

---

## PART 2 — COMPLETE DATABASE SCHEMA

Run all migrations in the exact order listed below.

### 2.1 Extend Users Table
```sql
-- Run after default Laravel migration
ALTER TABLE users
  ADD COLUMN role ENUM('admin','staff','associate','case_manager','patient') DEFAULT 'staff' AFTER email,
  ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER role,
  ADD COLUMN phone VARCHAR(50) NULL AFTER is_active,
  ADD COLUMN notes TEXT NULL AFTER phone,
  ADD COLUMN last_login_at TIMESTAMP NULL AFTER notes;
```

### 2.2 Activity Types (Dynamic Lookup)
```sql
CREATE TABLE activity_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed data
INSERT INTO activity_types (name, description, sort_order) VALUES
  ('Initial Assessment', 'First clinical assessment of patient', 1),
  ('Treatment Session', 'Ongoing vestibular therapy treatment', 2),
  ('Report Writing', 'Writing assessment or progress reports', 3),
  ('MDT Call', 'Multi-disciplinary team call', 4),
  ('Home Visit', 'Assessment or treatment at patient home', 5),
  ('Supervision Session', 'Clinical supervision by Samy', 6),
  ('Travel', 'Travel to/from patient location', 7);
```

### 2.3 Document Types (Dynamic Lookup)
```sql
CREATE TABLE document_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed data
INSERT INTO document_types (name, description, sort_order) VALUES
  ('Letter of Instruction', 'LOI received from case manager', 1),
  ('INA', 'Initial Needs Assessment from another clinician', 2),
  ('Medical Records', 'Patient medical history and records', 3),
  ('Cost Estimation', 'Quote sent to case manager or funder', 4),
  ('Assessment Report', 'Clinical assessment report', 5),
  ('Funding Approval', 'Written funding approval from funder', 6),
  ('Associate Invoice', 'Invoice received from associate', 7),
  ('VTA Invoice', 'Invoice sent by VTA to funder', 8),
  ('Case Close Summary', 'End of case financial and clinical summary', 9),
  ('NDA', 'Non-disclosure agreement', 10),
  ('Brochure / Materials', 'VTA marketing and information materials', 11),
  ('Progress Report', 'Interim clinical progress report', 12),
  ('Correspondence', 'General correspondence', 13);
```

### 2.4 Document Type Permissions (Dynamic — Samy Controls)
```sql
CREATE TABLE document_type_permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_type_id BIGINT UNSIGNED NOT NULL,
  role ENUM('case_manager','associate','patient') NOT NULL,
  can_view BOOLEAN DEFAULT FALSE,
  updated_by BIGINT UNSIGNED NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE CASCADE,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  UNIQUE KEY unique_permission (document_type_id, role)
);

-- Default permissions (Samy adjusts in Settings)
-- case_manager can see: LOI, Cost Estimation, Assessment Report, Funding Approval, VTA Invoice, Case Close Summary, NDA, Brochure
-- associate can see: LOI, INA, Medical Records, Assessment Report, Funding Approval
-- patient: none by default
```

### 2.5 Companies
```sql
CREATE TABLE companies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type ENUM('Case Management','Law Firm','Solicitor','Insurance','Individual','Other') DEFAULT 'Case Management',
  address TEXT NULL,
  city VARCHAR(100) NULL,
  postcode VARCHAR(20) NULL,
  phone VARCHAR(50) NULL,
  email VARCHAR(255) NULL,
  website VARCHAR(255) NULL,
  status ENUM('Enquiry','Active','Inactive') DEFAULT 'Enquiry',
  first_contact_date DATE NULL,
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.6 Enquiries (Quick Log — Stage 1 Before Full Record)
```sql
CREATE TABLE enquiries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  enquirer_name VARCHAR(255) NOT NULL,
  company_name VARCHAR(255) NULL,
  -- Free text at enquiry stage — not yet linked to companies table
  email VARCHAR(255) NULL,
  phone VARCHAR(50) NULL,
  source ENUM('Email','LinkedIn','Phone','Referral Letter','Website','Word of Mouth','Other') NOT NULL,
  reason TEXT NULL,
  enquiry_date DATE NOT NULL,
  first_response_date DATE NULL,
  status ENUM('New','In Progress','Converted','Not Proceeding') DEFAULT 'New',
  converted_to_company_id BIGINT UNSIGNED NULL,
  converted_to_case_manager_id BIGINT UNSIGNED NULL,
  converted_date DATE NULL,
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (converted_to_company_id) REFERENCES companies(id) ON DELETE SET NULL,
  FOREIGN KEY (converted_to_case_manager_id) REFERENCES case_managers(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.7 Case Managers
```sql
CREATE TABLE case_managers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  -- Set when case manager is given portal login (Phase 4)
  company_id BIGINT UNSIGNED NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NULL,
  job_title VARCHAR(150) NULL,
  nda_signed BOOLEAN DEFAULT FALSE,
  nda_signed_date DATE NULL,
  materials_sent BOOLEAN DEFAULT FALSE,
  materials_sent_date DATE NULL,
  status ENUM('Active','Inactive') DEFAULT 'Active',
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.8 Associates
```sql
CREATE TABLE associates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  -- Set when associate is given portal login (Phase 2)
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  phone VARCHAR(50) NULL,
  region VARCHAR(255) NOT NULL,
  speciality TEXT NULL,
  qualifications TEXT NULL,
  session_rate DECIMAL(8,2) NULL,
  -- Fixed fee per session
  travel_rate_per_mile DECIMAL(6,2) NULL,
  -- Pence/pounds per mile for travel
  is_active BOOLEAN DEFAULT TRUE,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Seed: 9 VTA associates
INSERT INTO associates (name, region, speciality, is_active) VALUES
  ('Kate Bryce', 'North East England', 'Falls and Balance Rehabilitation', TRUE),
  ('Anna Bennett', 'Yorkshire', 'Advanced Vestibular Physiotherapy', TRUE),
  ('Lewis Brennan', 'London and Cambridgeshire', 'Musculoskeletal and Vestibular Rehabilitation', TRUE),
  ('Georgios Tsiknas', 'West Midlands', 'Specialist Vestibular Physiotherapy', TRUE),
  ('Ileana Dascalu', 'London', 'Paediatric and Adult Rehabilitation', TRUE),
  ('Nick Hill', 'North West England', 'Specialist Vestibular Physiotherapy', TRUE),
  ('Sultana Parvin', 'Manchester', 'Specialist Vestibular Physiotherapy', TRUE),
  ('Sahash Palanisamy', 'Dorset', 'Specialist Vestibular Physiotherapy', TRUE),
  ('Samy Selvanayagam', 'Nationwide', 'Consultant Vestibular Physiotherapy', TRUE);
```

### 2.9 Patients
```sql
CREATE TABLE patients (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_manager_id BIGINT UNSIGNED NOT NULL,
  -- Current case manager
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  date_of_birth DATE NULL,
  location VARCHAR(255) NULL,
  condition TEXT NULL,
  status ENUM(
    'Enquiry Logged',
    'Response Sent',
    'Awaiting LOI',
    'LOI Received',
    'Assessment Scheduled',
    'Assessment Completed',
    'Report Drafted',
    'Report Sent',
    'Cost Estimation Sent',
    'Awaiting Funding Approval',
    'Funding Approved',
    'Treatment Active',
    'Awaiting Further Funding',
    'Discharged',
    'Case Closed'
  ) DEFAULT 'Enquiry Logged',
  referral_date DATE NOT NULL,
  first_contact_date DATE NULL,
  discharge_date DATE NULL,
  invoice_recipient_type ENUM('Case Manager Company','Solicitor','Insurance Company','Other') NULL,
  invoice_recipient_name VARCHAR(255) NULL,
  invoice_recipient_email VARCHAR(255) NULL,
  invoice_recipient_address TEXT NULL,
  assigned_staff_id BIGINT UNSIGNED NULL,
  needs_review BOOLEAN DEFAULT TRUE,
  folder_path VARCHAR(500) NULL,
  -- Relative path: companies/{slug}/case-managers/{slug}/patients/{slug}/
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (case_manager_id) REFERENCES case_managers(id),
  FOREIGN KEY (assigned_staff_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.10 Patient Associates (Multiple Associates Per Patient)
```sql
CREATE TABLE patient_associates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  associate_id BIGINT UNSIGNED NOT NULL,
  role ENUM('Assessment','Treatment','Supervision','MDT') NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  -- NULL means currently active
  is_primary BOOLEAN DEFAULT FALSE,
  notes TEXT NULL,
  assigned_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (associate_id) REFERENCES associates(id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.11 Patient Case Manager History
```sql
CREATE TABLE patient_case_manager_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  previous_case_manager_id BIGINT UNSIGNED NULL,
  new_case_manager_id BIGINT UNSIGNED NOT NULL,
  previous_company_id BIGINT UNSIGNED NULL,
  new_company_id BIGINT UNSIGNED NOT NULL,
  change_date DATE NOT NULL,
  reason TEXT NULL,
  changed_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (previous_case_manager_id) REFERENCES case_managers(id) ON DELETE SET NULL,
  FOREIGN KEY (new_case_manager_id) REFERENCES case_managers(id) ON DELETE CASCADE,
  FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.12 Cost Estimations
```sql
CREATE TABLE cost_estimations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  version_number INT NOT NULL DEFAULT 1,
  -- 1 = initial enquiry quote, 2+ = post-assessment / subsequent cycles
  title VARCHAR(255) NULL,
  -- e.g. "Initial Assessment Quote" or "Phase 1 Treatment Funding"
  estimated_amount DECIMAL(10,2) NOT NULL,
  estimated_sessions INT NULL,
  estimated_duration VARCHAR(100) NULL,
  -- e.g. "6 months", "12 months"
  sent_date DATE NULL,
  sent_to VARCHAR(255) NULL,
  notes TEXT NULL,
  document_path VARCHAR(500) NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.13 Funding Cycles
```sql
CREATE TABLE funding_cycles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  cost_estimation_id BIGINT UNSIGNED NULL,
  -- Links to the cost estimation that led to this approval
  cycle_number INT NOT NULL DEFAULT 1,
  -- 1, 2, 3... sequential per patient
  approved_amount DECIMAL(10,2) NOT NULL,
  approved_sessions INT NULL,
  approval_date DATE NOT NULL,
  approval_document_path VARCHAR(500) NULL,
  estimated_duration VARCHAR(100) NULL,
  -- e.g. "6 months"
  funder_name VARCHAR(255) NULL,
  funder_reference VARCHAR(255) NULL,
  is_active BOOLEAN DEFAULT TRUE,
  -- Only one cycle active at a time
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (cost_estimation_id) REFERENCES cost_estimations(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.14 Appointments
```sql
CREATE TABLE appointments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  associate_id BIGINT UNSIGNED NOT NULL,
  activity_type_id BIGINT UNSIGNED NOT NULL,
  scheduled_at DATETIME NOT NULL,
  duration_minutes INT DEFAULT 60,
  location VARCHAR(255) NULL,
  -- e.g. "Patient Home", "Clinic", "Remote"
  status ENUM('Scheduled','Completed','Cancelled','DNA') DEFAULT 'Scheduled',
  -- DNA = Did Not Attend
  notes TEXT NULL,
  travel_miles DECIMAL(6,2) NULL,
  -- Miles travelled (for travel rate calculation)
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (associate_id) REFERENCES associates(id) ON DELETE CASCADE,
  FOREIGN KEY (activity_type_id) REFERENCES activity_types(id),
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.15 Case Notes
```sql
CREATE TABLE case_notes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  appointment_id BIGINT UNSIGNED NULL,
  -- Links to appointment if relevant
  associate_id BIGINT UNSIGNED NOT NULL,
  session_date DATE NOT NULL,
  note_type ENUM('Session Note','Progress Note','Discharge Note','Supervision Note','Other') DEFAULT 'Session Note',
  content TEXT NULL,
  -- Text summary (optional if document uploaded)
  document_path VARCHAR(500) NULL,
  -- PDF upload of formal notes
  is_signed_off BOOLEAN DEFAULT FALSE,
  signed_off_by BIGINT UNSIGNED NULL,
  signed_off_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
  FOREIGN KEY (associate_id) REFERENCES associates(id) ON DELETE CASCADE,
  FOREIGN KEY (signed_off_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.16 Documents
```sql
CREATE TABLE documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_type_id BIGINT UNSIGNED NOT NULL,
  patient_id BIGINT UNSIGNED NULL,
  case_manager_id BIGINT UNSIGNED NULL,
  -- At least one must be set. Both can be set (e.g. LOI belongs to CM and patient)
  appointment_id BIGINT UNSIGNED NULL,
  -- Optional: links document to specific appointment
  file_name VARCHAR(255) NOT NULL,
  stored_file_name VARCHAR(255) NOT NULL,
  -- UUID-based name on disk
  file_path VARCHAR(500) NOT NULL,
  file_size BIGINT UNSIGNED NULL,
  mime_type VARCHAR(100) NULL,
  is_password_protected BOOLEAN DEFAULT FALSE,
  report_password VARCHAR(255) NULL,
  -- Encrypted. Only stored if Samy chooses to record it.
  password_shared_date DATE NULL,
  password_shared_via ENUM('Email','WhatsApp','Post','Other') NULL,
  uploaded_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (document_type_id) REFERENCES document_types(id),
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (case_manager_id) REFERENCES case_managers(id) ON DELETE CASCADE,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.17 Communications
```sql
CREATE TABLE communications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_manager_id BIGINT UNSIGNED NULL,
  patient_id BIGINT UNSIGNED NULL,
  -- Set if communication is about a specific patient
  type ENUM('Email','Phone','Letter','Meeting','WhatsApp','LinkedIn','Other') NOT NULL,
  direction ENUM('Inbound','Outbound') NOT NULL,
  subject VARCHAR(255) NULL,
  summary TEXT NOT NULL,
  communication_date DATETIME NOT NULL,
  follow_up_date DATE NULL,
  follow_up_completed BOOLEAN DEFAULT FALSE,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (case_manager_id) REFERENCES case_managers(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.18 Associate Invoices (Received by VTA)
```sql
CREATE TABLE associate_invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  associate_id BIGINT UNSIGNED NOT NULL,
  patient_id BIGINT UNSIGNED NOT NULL,
  funding_cycle_id BIGINT UNSIGNED NULL,
  invoice_reference VARCHAR(255) NULL,
  -- Associate's own invoice number
  invoice_date DATE NOT NULL,
  sessions_completed INT NULL,
  travel_miles DECIMAL(6,2) NULL,
  session_amount DECIMAL(10,2) NULL,
  -- sessions x associate session_rate
  travel_amount DECIMAL(10,2) NULL,
  -- miles x travel_rate_per_mile
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('Received','Verified','Paid','Disputed') DEFAULT 'Received',
  payment_date DATE NULL,
  due_date DATE NULL,
  -- invoice_date + 28 days (auto-calculated)
  document_path VARCHAR(500) NULL,
  notes TEXT NULL,
  logged_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (associate_id) REFERENCES associates(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (funding_cycle_id) REFERENCES funding_cycles(id) ON DELETE SET NULL,
  FOREIGN KEY (logged_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.19 VTA Invoices (Sent by VTA to Funder)
```sql
CREATE TABLE vta_invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  funding_cycle_id BIGINT UNSIGNED NULL,
  invoice_number VARCHAR(100) NOT NULL UNIQUE,
  -- Format: VTA-2026-0001 (auto-generated)
  invoice_date DATE NOT NULL,
  due_date DATE NULL,
  recipient_type ENUM('Case Manager Company','Solicitor','Insurance Company','Other') NOT NULL,
  recipient_name VARCHAR(255) NOT NULL,
  recipient_email VARCHAR(255) NULL,
  recipient_address TEXT NULL,
  sessions_invoiced INT NULL,
  session_amount DECIMAL(10,2) NULL,
  additional_charges DECIMAL(10,2) NULL,
  -- e.g. report writing, travel supervision
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('Draft','Sent','Paid','Overdue','Cancelled') DEFAULT 'Draft',
  payment_date DATE NULL,
  document_path VARCHAR(500) NULL,
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (funding_cycle_id) REFERENCES funding_cycles(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.20 Email Intake Log
```sql
CREATE TABLE email_intake_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_email VARCHAR(255) NOT NULL,
  from_name VARCHAR(255) NULL,
  subject VARCHAR(500) NULL,
  body TEXT NULL,
  received_at DATETIME NOT NULL,
  has_attachments BOOLEAN DEFAULT FALSE,
  attachment_paths TEXT NULL,
  -- JSON array of saved file paths
  processed BOOLEAN DEFAULT FALSE,
  linked_patient_id BIGINT UNSIGNED NULL,
  linked_case_manager_id BIGINT UNSIGNED NULL,
  action_taken ENUM('Linked to Patient','New Patient Created','Linked to Case Manager','Marked Irrelevant','Deleted') NULL,
  processed_by BIGINT UNSIGNED NULL,
  processed_at TIMESTAMP NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (linked_patient_id) REFERENCES patients(id) ON DELETE SET NULL,
  FOREIGN KEY (linked_case_manager_id) REFERENCES case_managers(id) ON DELETE SET NULL,
  FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.21 Seed Data — Users
```sql
INSERT INTO users (name, email, password, role, is_active) VALUES
  ('Samy Selvanayagam', 'samy@vestibulartherapyassociates.co.uk', '$2y$12$[bcrypt_of_ChangeMe2026!]', 'admin', TRUE),
  ('Jai Anand', 'jai@vestibulartherapyassociates.co.uk', '$2y$12$[bcrypt_of_ChangeMe2026!]', 'admin', TRUE),
  ('Sheeba Rossewilliam', 'sheeba@vestibulartherapyassociates.co.uk', '$2y$12$[bcrypt_of_ChangeMe2026!]', 'staff', TRUE);
-- Note: Use bcrypt. In seeder: Hash::make('ChangeMe2026!')
-- Users must change password on first login.
```

---

## PART 3 — BUSINESS RULES

These rules must be enforced in the backend (controllers/services), not just the frontend.

### 3.1 Clinical Rules
- **BR-C1:** A patient's status cannot move to 'Funding Approved' unless at least one funding_cycle record exists for that patient with a document uploaded.
- **BR-C2:** A patient's status cannot move to 'Treatment Active' unless status is currently 'Funding Approved' or 'Awaiting Further Funding'.
- **BR-C3:** A patient may have multiple active patient_associates records but only one per role type at any given time (e.g. only one Treatment associate at a time).
- **BR-C4:** When a patient is transferred to a new case manager, a patient_case_manager_history record must be created automatically before the case_manager_id on the patient record is updated.
- **BR-C5:** Case notes can only be uploaded by the associate assigned to that patient or by admin/staff.

### 3.2 Financial Rules
- **BR-F1:** A VTA invoice total_amount must not exceed the remaining balance of the linked funding_cycle. If it would exceed it, show a warning (do not block — Samy may override with a note).
- **BR-F2:** The remaining balance of a funding_cycle is calculated as: approved_amount minus the sum of all paid vta_invoices linked to that funding_cycle.
- **BR-F3:** When an associate invoice is created, the due_date is automatically set to invoice_date + 28 days.
- **BR-F4:** Associate invoices with status 'Received' or 'Verified' where due_date is within 7 days must show an alert on the dashboard.
- **BR-F5:** VTA invoice numbers are auto-generated in the format VTA-YYYY-NNNN where NNNN is zero-padded and sequential per year.
- **BR-F6:** A VTA invoice cannot be marked as Sent unless a document (PDF) has been uploaded to it.

### 3.3 Document Rules
- **BR-D1:** Documents are stored at the path: storage/app/vta-documents/{company-slug}/{case-manager-slug}/{patient-slug}/{document-type-slug}/{uuid}.{ext}
- **BR-D2:** Documents are never served from the public directory. All downloads go through an authenticated controller that checks the user's role and the document_type_permissions table.
- **BR-D3:** When a document is uploaded and linked to both a case_manager_id and a patient_id, it appears in both the case manager's document list and the patient's document list.
- **BR-D4:** Maximum file size is 20MB. Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF.
- **BR-D5:** Report passwords must be stored encrypted (use Laravel's encrypt() helper).

### 3.4 Access Rules
- **BR-A1:** Associates can only see patients where a patient_associates record exists with their associate_id and end_date is NULL.
- **BR-A2:** Case managers (Phase 4) can only see patients where case_manager_id matches their linked case_manager record.
- **BR-A3:** Document visibility for non-admin roles is controlled by the document_type_permissions table, which Samy manages in Settings.
- **BR-A4:** Only admin role can access the Finance module (associate invoices, VTA invoices, funding cycles).
- **BR-A5:** Staff role can see all patient and document records but cannot access finance screens.

---

## PART 4 — ROLES AND PERMISSIONS MATRIX

| Feature | Admin | Staff | Associate | Case Manager (Ph4) | Patient (Ph5) |
|---|---|---|---|---|---|
| **ENQUIRIES** | | | | | |
| View all enquiries | ✓ | ✓ | ✗ | ✗ | ✗ |
| Create enquiry | ✓ | ✓ | ✗ | ✗ | ✗ |
| Convert enquiry to record | ✓ | ✓ | ✗ | ✗ | ✗ |
| **COMPANIES** | | | | | |
| View all companies | ✓ | ✓ | ✗ | ✗ | ✗ |
| Create / Edit company | ✓ | ✓ | ✗ | ✗ | ✗ |
| **CASE MANAGERS** | | | | | |
| View all case managers | ✓ | ✓ | ✗ | ✗ | ✗ |
| Create / Edit case manager | ✓ | ✓ | ✗ | ✗ | ✗ |
| View own profile | ✗ | ✗ | ✗ | ✓ | ✗ |
| **PATIENTS** | | | | | |
| View all patients | ✓ | ✓ | ✗ | ✗ | ✗ |
| View own assigned patients | ✗ | ✗ | ✓ | ✗ | ✗ |
| View own case manager patients | ✗ | ✗ | ✗ | ✓ | ✗ |
| View own record | ✗ | ✗ | ✗ | ✗ | ✓ |
| Create patient | ✓ | ✓ | ✗ | ✗ | ✗ |
| Edit patient | ✓ | ✓ | ✗ | ✗ | ✗ |
| Change patient status | ✓ | ✓ | ✗ | ✗ | ✗ |
| Transfer case manager | ✓ | ✓ | ✗ | ✗ | ✗ |
| **DOCUMENTS** | | | | | |
| Upload document | ✓ | ✓ | ✓ (own patients only) | ✗ | ✗ |
| Download document | ✓ | ✓ | Permitted types only | Permitted types only | Permitted types only |
| Delete document | ✓ | ✗ | ✗ | ✗ | ✗ |
| **APPOINTMENTS** | | | | | |
| View team calendar | ✓ | ✓ | ✗ | ✗ | ✗ |
| View own calendar | ✗ | ✗ | ✓ | ✗ | ✗ |
| Create appointment | ✓ | ✓ | ✗ | ✗ | ✗ |
| Edit appointment | ✓ | ✓ | ✗ | ✗ | ✗ |
| **CASE NOTES** | | | | | |
| View case notes | ✓ | ✓ | Own patients only | ✗ | ✗ |
| Upload case note | ✓ | ✓ | Own patients only | ✗ | ✗ |
| Sign off case note | ✓ | ✗ | ✗ | ✗ | ✗ |
| **FINANCE** | | | | | |
| View associate invoices | ✓ | ✗ | ✗ | ✗ | ✗ |
| Log associate invoice | ✓ | ✗ | ✗ | ✗ | ✗ |
| Create VTA invoice | ✓ | ✗ | ✗ | ✗ | ✗ |
| View funding cycles | ✓ | ✓ (read only) | ✗ | ✗ | ✗ |
| Create funding cycle | ✓ | ✗ | ✗ | ✗ | ✗ |
| View revenue reports | ✓ | ✗ | ✗ | ✗ | ✗ |
| **SETTINGS** | | | | | |
| Manage activity types | ✓ | ✗ | ✗ | ✗ | ✗ |
| Manage document types | ✓ | ✗ | ✗ | ✗ | ✗ |
| Manage document permissions | ✓ | ✗ | ✗ | ✗ | ✗ |
| Manage associates | ✓ | ✗ | ✗ | ✗ | ✗ |
| Manage users | ✓ | ✗ | ✗ | ✗ | ✗ |
| **EMAIL INTAKE** | | | | | |
| View email intake | ✓ | ✓ | ✗ | ✗ | ✗ |
| Process / link emails | ✓ | ✓ | ✗ | ✗ | ✗ |

---

## PART 5 — APPLICATION STRUCTURE

### 5.1 Folder Structure
```
vta-portal/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── DashboardController.php
│   │   │   ├── EnquiryController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── CaseManagerController.php
│   │   │   ├── PatientController.php
│   │   │   ├── AssociateController.php
│   │   │   ├── DocumentController.php
│   │   │   ├── CommunicationController.php
│   │   │   ├── AppointmentController.php
│   │   │   ├── CaseNoteController.php
│   │   │   ├── CostEstimationController.php
│   │   │   ├── FundingCycleController.php
│   │   │   ├── AssociateInvoiceController.php
│   │   │   ├── VtaInvoiceController.php
│   │   │   ├── EmailIntakeController.php
│   │   │   ├── SettingsController.php
│   │   │   └── Portal/
│   │   │       ├── AssociatePortalController.php
│   │   │       └── CaseManagerPortalController.php
│   │   └── Middleware/
│   │       └── CheckRole.php
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   │   ├── KpiCards.php
│   │   │   ├── DailyActions.php
│   │   │   └── OverdueAlerts.php
│   │   ├── Enquiries/
│   │   │   └── EnquiryList.php
│   │   ├── Companies/
│   │   │   └── CompanyList.php
│   │   ├── Patients/
│   │   │   ├── PatientList.php
│   │   │   ├── PatientStatusUpdate.php
│   │   │   └── AssociateAllocation.php
│   │   ├── Documents/
│   │   │   └── DocumentUpload.php
│   │   ├── Appointments/
│   │   │   └── AppointmentCalendar.php
│   │   └── Finance/
│   │       └── FundingBalance.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── ActivityType.php
│   │   ├── DocumentType.php
│   │   ├── DocumentTypePermission.php
│   │   ├── Company.php
│   │   ├── Enquiry.php
│   │   ├── CaseManager.php
│   │   ├── Associate.php
│   │   ├── Patient.php
│   │   ├── PatientAssociate.php
│   │   ├── PatientCaseManagerHistory.php
│   │   ├── CostEstimation.php
│   │   ├── FundingCycle.php
│   │   ├── Appointment.php
│   │   ├── CaseNote.php
│   │   ├── Document.php
│   │   ├── Communication.php
│   │   ├── AssociateInvoice.php
│   │   ├── VtaInvoice.php
│   │   └── EmailIntakeLog.php
│   ├── Services/
│   │   ├── DocumentService.php
│   │   ├── EmailIntakeService.php
│   │   ├── FundingBalanceService.php
│   │   ├── InvoiceNumberService.php
│   │   └── PatientTransferService.php
│   └── Policies/
│       ├── PatientPolicy.php
│       └── DocumentPolicy.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── AssociateSeeder.php
│       ├── ActivityTypeSeeder.php
│       └── DocumentTypeSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── navigation.blade.php
│   ├── dashboard/index.blade.php
│   ├── enquiries/
│   ├── companies/
│   ├── case-managers/
│   ├── patients/
│   ├── documents/
│   ├── communications/
│   ├── appointments/
│   ├── case-notes/
│   ├── cost-estimations/
│   ├── funding-cycles/
│   ├── associate-invoices/
│   ├── vta-invoices/
│   ├── email-intake/
│   ├── settings/
│   └── portal/
│       ├── associate/
│       └── case-manager/
└── routes/web.php
```

### 5.2 Routes Summary
```php
// Auth
Route::middleware('guest'): login, password reset
Route::middleware('auth'):
  GET  /                          → DashboardController@index
  
  // Enquiries (admin + staff)
  GET  /enquiries                 → EnquiryController@index
  GET  /enquiries/create          → EnquiryController@create
  POST /enquiries                 → EnquiryController@store
  GET  /enquiries/{id}            → EnquiryController@show
  PUT  /enquiries/{id}            → EnquiryController@update
  POST /enquiries/{id}/convert    → EnquiryController@convert
  
  // Companies (admin + staff)
  Resource: /companies
  Resource: /companies/{company}/case-managers
  
  // Patients (admin + staff)
  Resource: /patients
  PATCH /patients/{id}/status          → PatientController@updateStatus
  PATCH /patients/{id}/transfer        → PatientController@transfer
  PATCH /patients/{id}/needs-review    → PatientController@toggleNeedsReview
  POST  /patients/{id}/associates      → PatientController@addAssociate
  
  // Documents (auth — access controlled by policy)
  POST   /documents                    → DocumentController@store
  GET    /documents/{id}/download      → DocumentController@download
  DELETE /documents/{id}               → DocumentController@destroy (admin only)
  
  // Communications (admin + staff)
  Resource: /communications (store, update, destroy only)
  
  // Cost Estimations (admin + staff)
  Resource: /cost-estimations
  
  // Funding Cycles (admin only)
  Resource: /funding-cycles
  
  // Appointments (admin + staff)
  Resource: /appointments
  GET /appointments/calendar           → AppointmentController@calendar
  
  // Case Notes (admin + staff + associate)
  Resource: /case-notes
  PATCH /case-notes/{id}/sign-off      → CaseNoteController@signOff (admin only)
  
  // Finance (admin only)
  Resource: /associate-invoices
  PATCH /associate-invoices/{id}/status
  Resource: /vta-invoices
  PATCH /vta-invoices/{id}/status
  GET   /finance/reports              → FinanceController@reports
  
  // Email Intake (admin + staff)
  GET    /email-intake                → EmailIntakeController@index
  PATCH  /email-intake/{id}/link      → EmailIntakeController@link
  DELETE /email-intake/{id}           → EmailIntakeController@destroy
  
  // Settings (admin only)
  GET  /settings                      → SettingsController@index
  Resource: /settings/activity-types
  Resource: /settings/document-types
  POST /settings/document-permissions → SettingsController@updatePermissions
  Resource: /settings/associates
  Resource: /settings/users
  
  // Associate Portal (associate only)
  GET  /associate-portal              → AssociatePortalController@index
  GET  /associate-portal/patients/{id}→ AssociatePortalController@patient
  POST /associate-portal/case-notes   → AssociatePortalController@uploadNote
  GET  /associate-portal/calendar     → AssociatePortalController@calendar
```

### 5.3 CheckRole Middleware
```php
// app/Http/Middleware/CheckRole.php
public function handle(Request $request, Closure $next, string ...$roles): mixed
{
    if (!$request->user() || !in_array($request->user()->role, $roles)) {
        abort(403, 'You do not have permission to access this area.');
    }
    return $next($request);
}
// Register alias 'role' in bootstrap/app.php
```

---

## PART 6 — PHASE 1 SCREENS

### SCREEN 6.1 — Login Page
**Route:** GET /login  
**Access:** Guest only  
**Design:** Clean centred card. VTA logo top. Email + Password fields. Remember me checkbox. Forgot password link. No register link — accounts created by admin only.  
**After login:** Redirect based on role — admin/staff → /dashboard, associate → /associate-portal

---

### SCREEN 6.2 — Dashboard
**Route:** GET /  
**Access:** Admin, Staff  

**Section A — KPI Cards (top row, 4 cards):**
- Total Active Cases (patients where status not in Discharged, Case Closed)
- Needs Review Today (needs_review = TRUE count)
- Awaiting Funding Approval (status = Awaiting Funding Approval)
- Overdue Associate Invoices (status Received/Verified, due_date < today) — admin only

**Section B — Daily Actions Table:**
Columns: Patient Name | Company | Case Manager | Status (badge) | Referral Date | Assigned Staff | Action  
Filter: needs_review = TRUE only  
Actions per row: View Patient | Mark as Reviewed (sets needs_review = FALSE)  
Default sort: Referral Date ascending (oldest first)

**Section C — Unprocessed Emails:**
Shows last 5 email_intake_logs where processed = FALSE  
Link: "View all unprocessed emails" → /email-intake

**Section D — Upcoming Appointments (admin/staff view):**
Next 7 days — all appointments across all associates  
Columns: Date/Time | Patient | Associate | Activity Type | Location

**Section E — Overdue Alerts (admin only):**
Associate invoices due within 7 days that are not yet paid

---

### SCREEN 6.3 — Enquiries List
**Route:** GET /enquiries  
**Access:** Admin, Staff  

**Top bar:** Search (enquirer name, company) | Filter by status | "Log New Enquiry" button  

**Table columns:** Date | Enquirer Name | Company | Source | Status | First Response | Days Since Enquiry | Action  
**Actions:** View | Convert to Record | Mark Not Proceeding  
**Status badge colours:** New=blue, In Progress=amber, Converted=green, Not Proceeding=grey

---

### SCREEN 6.4 — Log New Enquiry (Quick Form)
**Route:** GET /enquiries/create  
**Access:** Admin, Staff  
**Fields:**
- Enquirer Name* (text)
- Company Name* (text — free entry, not lookup yet)
- Email (text)
- Phone (text)
- Source* (dropdown: Email / LinkedIn / Phone / Referral Letter / Website / Word of Mouth / Other)
- Reason for Enquiry (textarea)
- Enquiry Date* (date, default today)
- First Response Date (date)
- Notes (textarea)

**Save button:** Creates enquiry record, redirects to enquiry detail

---

### SCREEN 6.5 — Enquiry Detail + Convert
**Route:** GET /enquiries/{id}  
**Access:** Admin, Staff  

**Left panel:** All enquiry fields (editable inline)  
**Right panel — Convert to Full Record button:**  
When clicked, opens a panel showing:
- Existing Company search (search companies table — does this company already exist?)
- If not found: Create new Company form (name, type, address, phone, email)
- Case Manager form (first name, last name, email, phone, job title)
- On confirm: creates Company + CaseManager records, links enquiry.converted_to fields, sets status = Converted

**Communication log** for this enquiry (type, direction, date, summary)  
**Add Communication button**

---

### SCREEN 6.6 — Companies List
**Route:** GET /companies  
**Access:** Admin, Staff  

**Top bar:** Search | Filter by status (Enquiry/Active/Inactive) | Filter by type | "Add Company" button  
**Table:** Company Name | Type | City | Status | Case Managers | Active Patients | First Contact | Action  
**Row actions:** View | Edit

---

### SCREEN 6.7 — Company Detail
**Route:** GET /companies/{id}  
**Access:** Admin, Staff  

**Section 1 — Company Info:** Name, type, address, contact, status, notes. Edit button.  

**Section 2 — Case Managers:** Table showing all case managers for this company.  
Columns: Name | Email | NDA | Materials Sent | Active Patients | Status | Action  
"Add Case Manager" button  

**Section 3 — All Patients:** Table of all patients from all case managers of this company.  
Columns: Patient Name | Case Manager | Status | Associate | Referral Date  

**Section 4 — Communications:** Timeline of all communications with any case manager at this company  

**Section 5 — Documents:** Documents linked to any case manager at this company (NDA, brochures)

---

### SCREEN 6.8 — Case Manager Detail
**Route:** GET /companies/{company}/case-managers/{id}  
**Access:** Admin, Staff  

**Section 1 — Case Manager Info:** Name, email, phone, job title, company, status. Edit button.  

**Section 2 — NDA Status:**  
If nda_signed = FALSE: Show "NDA Not Signed" badge + "Mark as Signed" button (sets nda_signed = TRUE, nda_signed_date = today, prompts document upload)  
If nda_signed = TRUE: Show date signed + link to NDA document  

**Section 3 — Materials Sent:**  
Similar pattern — "Materials Not Sent" or date sent + "Mark as Sent" button  

**Section 4 — Patients:** Table of all patients referred by this case manager.  
Columns: Patient Name | Status | Associate | Referral Date | Needs Review  
"Add New Patient" button  

**Section 5 — Communication Log:** Chronological timeline (newest first).  
Type icon | Direction | Date | Subject | Summary | Follow-up date  
"Add Communication" button → modal with fields: Type, Direction, Date/Time, Subject, Summary, Follow-up Date  

**Section 6 — Documents:** Documents linked to this case manager (NDA, materials)  
Upload document button

---

### SCREEN 6.9 — Patients List
**Route:** GET /patients  
**Access:** Admin, Staff  

**Filter sidebar:**
- Status (multi-select checkboxes — all 15 statuses)
- Needs Review (toggle — default ON showing only needs_review = TRUE)
- Assigned Staff (dropdown)
- Associate (dropdown)
- Company (dropdown)
- Date range (referral date)
- Search (patient name)

**Table columns:** Patient Name | Company | Case Manager | Status | Associate | Assigned Staff | Referral Date | Needs Review | Action  
**Row actions:** View | Edit | Change Status (inline dropdown)  
**Default view:** needs_review = TRUE, sorted by referral date ascending  
**"Show All" toggle:** removes needs_review filter

---

### SCREEN 6.10 — Patient Detail (Most Important Screen)
**Route:** GET /patients/{id}  
**Access:** Admin, Staff  

**Layout:** Two-column desktop, single column mobile.

**Left column:**

*Card 1 — Patient Info:*
Full name, DOB, location, condition, referral date, first contact date  
Edit button → opens edit form  

*Card 2 — Current Status:*
Large status badge (colour-coded)  
Status change dropdown + "Update Status" button  
Status enforces business rules (BR-C1, BR-C2)  
Status history log below (timestamp + who changed it)  

*Card 3 — Case Manager:*
Current case manager name + company + link to their detail page  
"Transfer to Different Case Manager" button → opens modal:
  - Search existing case managers
  - Select new case manager (and auto-selects their company)
  - Reason field (required)
  - On save: creates patient_case_manager_history record + updates patient.case_manager_id  

*Card 4 — Team Assignment:*
Assigned Staff dropdown (all active staff users)  
Needs Review toggle (large, prominent)  

*Card 5 — Associates:*
Table of patient_associates records  
Columns: Associate Name | Role | Start Date | End Date | Active  
"Add Associate" button → modal: select associate, select role, start date  
"End Assignment" button per row → sets end_date to today  

*Card 6 — Funding Overview (read-only for staff):*
Shows current active funding cycle:
- Cycle number, approved amount, approved sessions
- Amount invoiced so far (sum of paid VTA invoices)
- Remaining balance (calculated)
- Progress bar: invoiced vs approved
- Link to funding cycles detail (admin only can edit)

**Right column:**

*Card 7 — Documents:*
List of all documents linked to this patient  
Columns: Document Type | File Name | Uploaded By | Date | Password? | Download  
Upload button → opens upload modal:
  - Select document type (from document_types table)
  - Upload file
  - Also link to case manager? (Yes/No toggle — if yes, links to both patient and case manager)
  - Is password protected? (Yes/No) — if Yes: password field, shared date, shared via  

*Card 8 — Cost Estimations:*
List of all cost estimations for this patient  
Columns: Version | Title | Amount | Sessions | Sent Date | Document  
"Add Cost Estimation" button  

*Card 9 — Communications:*
Timeline of all communications linked to this patient  
Add Communication button  

*Card 10 — Notes:*
Free text area — saves to patients.notes on blur  

---

### SCREEN 6.11 — Add / Edit Patient
**Route:** GET /patients/create | /patients/{id}/edit  
**Access:** Admin, Staff  

**Fields:**
- Case Manager* (searchable dropdown — search by name or company)
- First Name* / Last Name*
- Date of Birth
- Location (city/region — used for associate matching)
- Condition (textarea)
- Referral Date* (date)
- First Contact Date
- Invoice Recipient Type* (dropdown: Case Manager Company / Solicitor / Insurance Company / Other)
- Invoice Recipient Name
- Invoice Recipient Email
- Invoice Recipient Address
- Assigned Staff (dropdown)
- Notes

---

### SCREEN 6.12 — Email Intake
**Route:** GET /email-intake  
**Access:** Admin, Staff  

**Top bar:** Unprocessed count badge | Filter (All / Unprocessed / Processed) | "Check for New Emails" button (triggers manual IMAP check)  

**Instructions panel (collapsible):**
"Review each email below. For each one: link it to an existing patient, link it to a case manager, create a new patient from it, or mark it as irrelevant."

**Email list table:**
Columns: Received | From | Subject | Attachments | Linked To | Action  

**Actions per row:**
- Link to Patient → opens modal with patient search
- Link to Case Manager → opens modal with case manager search  
- Create New Patient → pre-fills create patient form with email sender details
- Mark as Irrelevant → sets processed = TRUE, action = Marked Irrelevant
- View Full Email → expands email body inline

**When linked:** Row shows green badge "Processed" and moves to bottom of list

---

### SCREEN 6.13 — Communication Add Modal (Reusable)
Used across multiple screens (case manager detail, patient detail, company detail)  
**Fields:** Type | Direction | Date/Time | Subject | Summary | Follow-up Date (optional)  
**Save:** Creates communication record with appropriate case_manager_id and/or patient_id

---

## PART 7 — PHASE 2 SCREENS

### SCREEN 7.1 — Appointments Calendar (Team View — Admin/Staff)
**Route:** GET /appointments/calendar  
**Access:** Admin, Staff  

Uses FullCalendar.js in month/week/day view toggle.  

**Events on calendar:**
- Associate appointments (colour-coded per associate)
- Samy's supervision sessions
- MDT calls
- Report deadlines (from cost estimation sent date + agreed turnaround)

**Clicking an event:** Opens appointment detail popup showing patient, associate, activity type, location, status, notes  
**"Add Appointment" button:** Opens appointment create form  
**Filter sidebar:** Filter by associate | Filter by activity type

---

### SCREEN 7.2 — Add / Edit Appointment
**Route:** GET /appointments/create | /appointments/{id}/edit  
**Access:** Admin, Staff  

**Fields:**
- Patient* (searchable dropdown)
- Associate* (dropdown — shows associates assigned to selected patient first)
- Activity Type* (from activity_types table — dynamic)
- Scheduled Date/Time*
- Duration (minutes, default 60)
- Location (text — e.g. Patient Home, Clinic, Remote)
- Travel Miles (decimal — for travel rate calculation)
- Notes

---

### SCREEN 7.3 — Appointment Detail
**Route:** GET /appointments/{id}  
**Access:** Admin, Staff, Associate (own appointments only)  

Shows all appointment fields.  
Status update: Scheduled → Completed / Cancelled / DNA  
Link to add case note for this appointment  

---

### SCREEN 7.4 — Case Notes (per Patient)
Accessed from Patient Detail screen — not a standalone page.  
**List shows:** Date | Associate | Type | Summary | Document | Signed Off  
**Add Case Note button:** Opens form:
- Patient (pre-filled)
- Appointment (optional dropdown — appointments for this patient)
- Associate* (pre-filled if associate is logged in)
- Session Date*
- Note Type*
- Content (textarea)
- Document Upload (PDF)

---

### SCREEN 7.5 — Sign Off Case Note
**Access:** Admin only  
Button on case note row → sets is_signed_off = TRUE, signed_off_by = auth user, signed_off_at = now()

---

### SCREEN 7.6 — Associate Portal Dashboard
**Route:** GET /associate-portal  
**Access:** Associate only  

**My Patients card:** Count of active assigned patients  
**Upcoming Appointments:** List of own upcoming appointments (next 14 days)  
**Awaiting Case Notes:** Appointments marked Completed but no case note uploaded  

---

### SCREEN 7.7 — Associate Portal — Patient View
**Route:** GET /associate-portal/patients/{id}  
**Access:** Associate (own patients only — enforced by BR-A1)  

Read-only patient info: name, condition, location, status  
Documents: shows only document types the associate has permission to see (from document_type_permissions)  
Appointments: own appointments for this patient  
Case Notes: own case notes for this patient + upload new note button  

---

### SCREEN 7.8 — Associate Portal — Calendar
**Route:** GET /associate-portal/calendar  
**Access:** Associate only  

FullCalendar.js showing own appointments only.  
Clicking appointment → appointment detail (read-only)

---

## PART 8 — PHASE 3 SCREENS

### SCREEN 8.1 — Funding Cycles (per Patient)
Accessed from Patient Detail screen.  
**Access:** Admin (edit), Staff (read only)  

**List shows:** Cycle # | Approved Amount | Approved Sessions | Approval Date | Remaining Balance | Status  
Remaining Balance = approved_amount - SUM(paid vta_invoices for this cycle)  
Progress bar per cycle showing spend vs approval  

**Add Funding Cycle button (admin only):**
- Patient (pre-filled)
- Link to Cost Estimation (dropdown of patient's cost estimations)
- Cycle Number (auto-incremented)
- Approved Amount*
- Approved Sessions
- Approval Date*
- Funder Name
- Funder Reference
- Estimated Duration
- Upload Funding Approval Document

---

### SCREEN 8.2 — Associate Invoices List
**Route:** GET /associate-invoices  
**Access:** Admin only  

**Summary row:** Total received this month | Total paid this month | Overdue count  
**Filter:** Associate | Status | Date range | Patient  
**Table:** Associate | Patient | Invoice Ref | Date | Amount | Due Date | Status | Action  
**Overdue rows highlighted in amber**  

---

### SCREEN 8.3 — Log Associate Invoice
**Route:** GET /associate-invoices/create  
**Access:** Admin only  

**Fields:**
- Associate* (dropdown)
- Patient* (searchable dropdown — filtered to patients where this associate is assigned)
- Funding Cycle (dropdown — active cycles for this patient)
- Invoice Reference (associate's own number)
- Invoice Date*
- Sessions Completed (integer)
- Travel Miles (decimal)
- Session Amount (auto-calculated: sessions × associate.session_rate — editable)
- Travel Amount (auto-calculated: miles × associate.travel_rate_per_mile — editable)
- Total Amount* (editable — overrides calculated)
- Notes
- Upload Invoice Document

**On save:** due_date auto-set to invoice_date + 28 days (BR-F3)

---

### SCREEN 8.4 — VTA Invoices List
**Route:** GET /vta-invoices  
**Access:** Admin only  

**Summary row:** Total invoiced this month | Total paid | Outstanding | Overdue  
**Filter:** Status | Recipient type | Patient | Date range  
**Table:** Invoice # | Patient | Recipient | Amount | Funding Cycle Balance | Status | Due Date | Action  

---

### SCREEN 8.5 — Create VTA Invoice
**Route:** GET /vta-invoices/create  
**Access:** Admin only  

**Fields:**
- Patient* (searchable dropdown)
- Funding Cycle* (dropdown — active cycles for patient, shows remaining balance)
- Funding Cycle Remaining Balance (read-only display — BR-F1 warning if invoice would exceed)
- Invoice Date*
- Due Date
- Recipient Type* (auto-filled from patient.invoice_recipient_type — editable)
- Recipient Name* (auto-filled from patient.invoice_recipient_name)
- Recipient Email
- Recipient Address
- Sessions Invoiced
- Session Amount
- Additional Charges (e.g. report writing, travel supervision)
- Total Amount* (calculated — editable)
- Notes

**Validation:** If total_amount > remaining balance on selected cycle → show amber warning but allow save with a mandatory note explaining the override. (BR-F1)  
**On save:** Invoice number auto-generated VTA-YYYY-NNNN (BR-F5)  
**Cannot mark as Sent without uploading document (BR-F6)**

---

### SCREEN 8.6 — Finance Reports
**Route:** GET /finance/reports  
**Access:** Admin only  

**Report 1 — Revenue Summary:**
- Total VTA invoices sent this month / year
- Total paid this month / year
- Outstanding invoices (bar chart by age: 0-30 days, 31-60, 60+)

**Report 2 — Revenue by Company:**
- Table: Company | Total Invoiced | Total Paid | Outstanding

**Report 3 — Associate Payments:**
- Table: Associate | Total Invoiced to VTA | Total Paid by VTA | Overdue

**Report 4 — Case Close Summary (per patient):**
Select patient → shows: referral date, discharge date, total sessions, total VTA revenue, total associate costs, net margin

---

### SCREEN 8.7 — Cost Estimation Create/Edit
**Route:** GET /cost-estimations/create  
**Access:** Admin, Staff  

**Fields:**
- Patient* (searchable dropdown)
- Version Number (auto-incremented per patient — editable)
- Title (e.g. "Initial Assessment Quote", "Phase 1 Treatment — 6 months")
- Estimated Amount*
- Estimated Sessions
- Estimated Duration (text — e.g. "6 months", "12 months")
- Sent Date
- Sent To (text — name/email of recipient)
- Notes
- Upload Cost Estimation Document

---

## PART 9 — WORKFLOW AND STATUS LOGIC

### 9.1 Patient Status Flow
```
Enquiry Logged
    ↓ (after first response sent)
Response Sent
    ↓ (after LOI requested)
Awaiting LOI
    ↓ (after LOI received + uploaded)
LOI Received
    ↓ (after appointment booked)
Assessment Scheduled
    ↓ (after assessment marked Completed)
Assessment Completed
    ↓ (after report created as draft)
Report Drafted
    ↓ (after report document uploaded + sent)
Report Sent
    ↓ (after cost estimation created and sent)
Cost Estimation Sent
    ↓ (after case created in waiting for approval)
Awaiting Funding Approval
    ↓ (GATE: requires funding_cycle record with document — BR-C1)
Funding Approved
    ↓ (GATE: requires status = Funding Approved — BR-C2)
Treatment Active
    ↓ (when current funding cycle exhausted)
Awaiting Further Funding
    ↓ (loops back to Funding Approved when new cycle created)
Funding Approved → Treatment Active (repeat)
    ↓ (when treatment complete)
Discharged
    ↓ (after case close summary created)
Case Closed
```

### 9.2 Status Transition Rules
All transitions must be validated in PatientController@updateStatus:

```php
private function allowedTransitions(): array {
    return [
        'Enquiry Logged'            => ['Response Sent'],
        'Response Sent'             => ['Awaiting LOI', 'Enquiry Logged'],
        'Awaiting LOI'              => ['LOI Received', 'Response Sent'],
        'LOI Received'              => ['Assessment Scheduled'],
        'Assessment Scheduled'      => ['Assessment Completed', 'LOI Received'],
        'Assessment Completed'      => ['Report Drafted'],
        'Report Drafted'            => ['Report Sent', 'Assessment Completed'],
        'Report Sent'               => ['Cost Estimation Sent'],
        'Cost Estimation Sent'      => ['Awaiting Funding Approval'],
        'Awaiting Funding Approval' => ['Funding Approved', 'Cost Estimation Sent'],
        'Funding Approved'          => ['Treatment Active'],
        'Treatment Active'          => ['Awaiting Further Funding', 'Discharged'],
        'Awaiting Further Funding'  => ['Funding Approved'],
        'Discharged'                => ['Case Closed'],
        'Case Closed'               => [], // terminal state
    ];
}
```

### 9.3 Gate Checks
```php
// In PatientController@updateStatus — before saving:

if ($newStatus === 'Funding Approved') {
    $hasCycle = FundingCycle::where('patient_id', $patient->id)
        ->whereNotNull('approval_document_path')
        ->exists();
    if (!$hasCycle) {
        return back()->withErrors(['status' => 'Cannot approve funding: no funding approval document uploaded.']);
    }
}

if ($newStatus === 'Treatment Active') {
    if ($patient->status !== 'Funding Approved') {
        return back()->withErrors(['status' => 'Treatment can only start from Funding Approved status.']);
    }
}
```

---

## PART 10 — SETTINGS SCREENS

### SCREEN 10.1 — Settings Index
**Route:** GET /settings  
**Access:** Admin only  

Tab navigation: Activity Types | Document Types | Document Permissions | Associates | Users | System

---

### SCREEN 10.2 — Activity Types Management
**Route:** GET /settings/activity-types  
**Access:** Admin only  

Table: Name | Description | Active | Sort Order | Actions (Edit, Deactivate)  
"Add Activity Type" button → inline form  
Deactivated types are hidden from dropdowns but historical records are preserved

---

### SCREEN 10.3 — Document Types Management
**Route:** GET /settings/document-types  
**Access:** Admin only  

Same pattern as activity types  
"Add Document Type" button

---

### SCREEN 10.4 — Document Permissions Matrix
**Route:** GET /settings/document-permissions  
**Access:** Admin only  

**Visual matrix table:**
Rows: each document type  
Columns: Case Manager | Associate | Patient  
Each cell: toggle switch (ON/OFF)  
"Save All Permissions" button at bottom  

When saved: updates document_type_permissions table  
This determines what external portal users can see when they log in

---

### SCREEN 10.5 — Associates Management
**Route:** GET /settings/associates  
**Access:** Admin only  

Table: Name | Region | Speciality | Session Rate | Travel Rate | Active | Portal Access | Actions  
"Add Associate" button  
Edit form: name, email, phone, region, speciality, qualifications, session_rate, travel_rate_per_mile, is_active  
"Create Portal Login" button per row → creates user account with role = associate, links user_id on associates table

---

### SCREEN 10.6 — Users Management
**Route:** GET /settings/users  
**Access:** Admin only  

Table: Name | Email | Role | Last Login | Active | Actions  
"Add User" button → form: name, email, role, temporary password  
Edit: name, email, role, is_active  
Reset password button

---

## PART 11 — UI DESIGN RULES

### 11.1 Colour Palette
```css
/* Primary green — VTA brand */
--color-primary:       #1A7A4A;
--color-primary-light: #D4EDDA;
--color-primary-dark:  #145C37;

/* Secondary navy */
--color-secondary:       #1E3A5F;
--color-secondary-light: #D0E4F7;

/* Neutrals */
--color-background: #F8F9FA;
--color-surface:    #FFFFFF;
--color-border:     #E5E7EB;
--color-text:       #1F2937;
--color-text-muted: #6B7280;

/* Status colours — patient status badges */
--status-enquiry-logged:          #3B82F6; /* blue */
--status-response-sent:           #06B6D4; /* cyan */
--status-awaiting-loi:            #F59E0B; /* amber */
--status-loi-received:            #8B5CF6; /* purple */
--status-assessment-scheduled:    #EC4899; /* pink */
--status-assessment-completed:    #6366F1; /* indigo */
--status-report-drafted:          #14B8A6; /* teal */
--status-report-sent:             #0EA5E9; /* sky */
--status-cost-estimation-sent:    #F97316; /* orange */
--status-awaiting-funding:        #EF4444; /* red */
--status-funding-approved:        #10B981; /* emerald */
--status-treatment-active:        #1A7A4A; /* green */
--status-awaiting-further-funding:#F59E0B; /* amber */
--status-discharged:              #6B7280; /* slate */
--status-case-closed:             #374151; /* dark grey */
```

### 11.2 Typography
- Font: Inter (load from Google Fonts)
- Base size: 14px (Tailwind text-sm)
- Headings: font-semibold
- Labels: text-xs font-medium text-gray-500 uppercase tracking-wider

### 11.3 Layout
- Sidebar: fixed left, 240px wide, collapses to icon-only on mobile
- Main content: max-width 1400px, centred, padding 24px
- Cards: white background, rounded-xl, shadow-sm, border border-gray-100
- Tables: full width, striped (even rows bg-gray-50), sticky header

### 11.4 Navigation Sidebar (Admin/Staff)
```
VTA Logo
─────────────────
📊  Dashboard
─────────────────
OPERATIONS
📥  Email Intake  [badge: unprocessed count]
📋  Enquiries
🏢  Companies
🧑‍⚕️  Patients
─────────────────
CLINICAL (Phase 2)
📅  Appointments
📝  Case Notes
─────────────────
FINANCE (Phase 3 — admin only)
💰  Associate Invoices
🧾  VTA Invoices
📈  Reports
─────────────────
SETTINGS (admin only)
⚙️  Settings
─────────────────
[User name + role]
[Logout]
```

### 11.5 Mobile Responsive Rules
- Sidebar collapses to hamburger menu on < 768px
- Tables show as cards on mobile (each row becomes a card with label: value pairs)
- All forms are single column on mobile
- Minimum tap target: 44px height
- Calendar switches to list view on mobile (FullCalendar listWeek view)

### 11.6 Flash Messages
All successful actions show a green toast notification (top right, auto-dismiss 4 seconds)  
All errors show a red toast notification  
All warnings (e.g. BR-F1 invoice exceeds funding) show an amber inline alert before the form submit button

### 11.7 Status Badges
```html
<!-- Example status badge -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
  Treatment Active
</span>
```
Each status maps to a Tailwind colour class pair defined in Patient::STATUS_COLORS constant.

---

## PART 12 — BUILD ORDER FOR CODE AGENT

Follow this exact sequence. Do not build screen N+1 before screen N is complete and tested.

### Stage 1 — Foundation (do this before any screens)
```
1.  Install Laravel 11 fresh project
2.  Install packages: breeze, livewire, php-imap, dompdf, blade-heroicons
3.  Configure Tailwind CSS
4.  Run default Laravel migrations
5.  Run migration: extend users table (Part 2.1)
6.  Run migrations: activity_types, document_types, document_type_permissions (Parts 2.2–2.4)
7.  Run migrations: companies, enquiries, case_managers, associates (Parts 2.5–2.8)
8.  Run migrations: patients, patient_associates, patient_case_manager_history (Parts 2.9–2.11)
9.  Run migrations: cost_estimations, funding_cycles (Parts 2.12–2.13)
10. Run migrations: appointments, case_notes (Parts 2.14–2.15)
11. Run migrations: documents, communications (Parts 2.16–2.17)
12. Run migrations: associate_invoices, vta_invoices, email_intake_logs (Parts 2.18–2.20)
13. Run all seeders (Part 2.21 + activity types + document types + associates)
14. Build CheckRole middleware and register as 'role' alias
15. Build app.blade.php layout with sidebar navigation
16. Build guest.blade.php layout for login
17. Configure Laravel Breeze login — custom redirect by role
18. Configure storage disk for vta-documents
19. Configure IMAP settings in .env
20. Verify login works for all 3 seeded users
```

### Stage 2 — Phase 1 Core Screens
```
21. Screen 6.2  — Dashboard (KPI cards + Daily Actions)
22. Screen 6.3  — Enquiries List
23. Screen 6.4  — Log New Enquiry
24. Screen 6.5  — Enquiry Detail + Convert
25. Screen 6.6  — Companies List
26. Screen 6.7  — Company Detail
27. Screen 6.8  — Case Manager Detail
28. Screen 6.9  — Patients List (with filters)
29. Screen 6.10 — Patient Detail (most complex — build all 10 cards)
30. Screen 6.11 — Add/Edit Patient
31. Screen 6.12 — Email Intake
32. Screen 6.13 — Communication Add Modal
33. DocumentController — upload + download with permission check
34. PatientController — status update with gate checks (Part 9.3)
35. PatientController — transfer case manager
36. EmailIntakeService — IMAP fetch scheduled task
37. Dashboard — wire up unprocessed email badge + overdue alerts
```

### Stage 3 — Phase 2 Clinical Screens
```
38. Screen 7.1  — Appointments Calendar (FullCalendar.js team view)
39. Screen 7.2  — Add/Edit Appointment
40. Screen 7.3  — Appointment Detail
41. Screen 7.4  — Case Notes (per patient)
42. Screen 7.5  — Sign Off Case Note
43. Screen 7.6  — Associate Portal Dashboard
44. Screen 7.7  — Associate Portal Patient View
45. Screen 7.8  — Associate Portal Calendar
46. Verify BR-A1 (associate sees only own patients) across all portal screens
```

### Stage 4 — Phase 3 Finance Screens
```
47. Screen 8.7  — Cost Estimation Create/Edit (build before funding cycles)
48. Screen 8.1  — Funding Cycles (per patient) + FundingBalanceService
49. Screen 8.2  — Associate Invoices List
50. Screen 8.3  — Log Associate Invoice (with auto-calculations)
51. Screen 8.4  — VTA Invoices List
52. Screen 8.5  — Create VTA Invoice (with BR-F1 warning)
53. Screen 8.6  — Finance Reports (Chart.js)
54. InvoiceNumberService — auto-generate VTA-YYYY-NNNN
55. Dashboard — wire up overdue associate invoice alerts
```

### Stage 5 — Settings Screens
```
56. Screen 10.1 — Settings Index (tabs)
57. Screen 10.2 — Activity Types Management
58. Screen 10.3 — Document Types Management
59. Screen 10.4 — Document Permissions Matrix
60. Screen 10.5 — Associates Management + create portal login
61. Screen 10.6 — Users Management
```

### Stage 6 — Final
```
62. Run full testing checklist (Part 13)
63. php artisan config:cache route:cache view:cache
64. Set up cron in Krystal cPanel for scheduled tasks (email intake every 15 min)
65. Deploy to Krystal Emerald via Git
66. Verify all screens on mobile
```

---

## PART 13 — TESTING CHECKLIST

### Phase 1 Tests
- [ ] Admin can log in and see dashboard
- [ ] Staff can log in and see dashboard
- [ ] Associate cannot access /patients (gets 403)
- [ ] New enquiry can be logged (all fields save correctly)
- [ ] Enquiry can be converted to Company + Case Manager record
- [ ] Company created with all fields
- [ ] Case Manager created under company
- [ ] NDA marked as signed — date recorded
- [ ] Materials marked as sent — date recorded
- [ ] Patient created under case manager
- [ ] Patient status can be changed (valid transitions only)
- [ ] Patient cannot move to Funding Approved without funding cycle document (BR-C1)
- [ ] Patient cannot move to Treatment Active from wrong status (BR-C2)
- [ ] Patient can be transferred to new case manager — history record created
- [ ] Document uploaded — appears in patient record
- [ ] Document downloaded — permission check passes
- [ ] Associate cannot download document type they don't have permission for (BR-A3)
- [ ] Communication logged — appears in correct timeline
- [ ] Email intake: IMAP fetches new emails
- [ ] Email intake: email can be linked to patient
- [ ] Email intake: email can create new patient
- [ ] Needs Review toggle works — patient leaves Daily Actions when set to FALSE
- [ ] Dashboard KPI cards show correct counts
- [ ] Daily Actions shows only needs_review = TRUE patients

### Phase 2 Tests
- [ ] Appointment created and appears on team calendar
- [ ] Associate portal shows only own assigned patients
- [ ] Associate can upload case note for own patient
- [ ] Associate cannot upload case note for unassigned patient
- [ ] Admin can sign off case note
- [ ] Associate calendar shows own appointments only
- [ ] Multiple associates can be assigned to one patient with different roles

### Phase 3 Tests
- [ ] Cost estimation created and linked to patient
- [ ] Funding cycle created with document — triggers status gate
- [ ] Funding cycle remaining balance calculates correctly
- [ ] Associate invoice logged — due date auto-set to +28 days
- [ ] Overdue associate invoice appears as alert on dashboard
- [ ] VTA invoice created — number auto-generated correctly (VTA-2026-0001)
- [ ] VTA invoice exceeding funding cycle balance shows warning
- [ ] VTA invoice cannot be marked Sent without document (BR-F6)
- [ ] Finance reports load and show correct figures

### Settings Tests
- [ ] New activity type added — appears in appointment form dropdown
- [ ] Activity type deactivated — disappears from dropdowns
- [ ] New document type added — appears in upload form
- [ ] Document permission toggled — associate can/cannot see that document type
- [ ] Associate session rate set — auto-calculates in invoice form
- [ ] New user created — can log in with temporary password

---

## PART 14 — ENVIRONMENT FILE TEMPLATE

```env
APP_NAME="VTA Portal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://portal.vestibulartherapyassociates.co.uk

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vta_portal
DB_USERNAME=vta_user
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=480

MAIL_MAILER=smtp
MAIL_HOST=mail.vestibulartherapyassociates.co.uk
MAIL_PORT=587
MAIL_USERNAME=operations@vestibulartherapyassociates.co.uk
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=operations@vestibulartherapyassociates.co.uk
MAIL_FROM_NAME="VTA Operations"

MAIL_IMAP_HOST=mail.vestibulartherapyassociates.co.uk
MAIL_IMAP_PORT=993
MAIL_IMAP_ENCRYPTION=ssl
MAIL_IMAP_USERNAME=operations@vestibulartherapyassociates.co.uk
MAIL_IMAP_PASSWORD=

VTA_DOCUMENTS_PATH=vta-documents
VTA_MAX_UPLOAD_MB=20
```

---

*Document Version: 2.0*  
*Created: June 2026*  
*For: Code Agent — VTA Portal Build*  
*Stack: Laravel 11 + Livewire 3 + MySQL + Tailwind CSS + FullCalendar.js + Krystal Emerald*  
*Business: Vestibular Therapy Associates — Dr. Samy Selvanayagam*  
*Total screens: 37 across 3 phases*  
*Total database tables: 21*  

---

## PART 15 — HOSTING SETUP ON KRYSTAL EMERALD

### 15.1 Current Setup
The main VTA website is already hosted on Krystal Emerald at:
> **vestibulartherapyassociates.co.uk**

The VTA Portal will run on a subdomain:
> **portal.vestibulartherapyassociates.co.uk**

The two are completely separate. The existing website is not touched at any point during portal setup or deployment.

---

### 15.2 Subdomain Setup in Krystal cPanel

**Step 1 — Create the Subdomain:**
1. Log into Krystal cPanel at: https://vestibulartherapyassociates.co.uk:2083
2. Find **Domains** section → click **Subdomains**
3. Fill in:
   - Subdomain: `portal`
   - Domain: `vestibulartherapyassociates.co.uk`
   - Document Root: auto-filled as `portal.vestibulartherapyassociates.co.uk`
4. Click **Create**

**Step 2 — Point Document Root to Laravel Public Folder:**
The Laravel application will live at:
```
/home/{cpanel-username}/portal.vestibulartherapyassociates.co.uk/
```
But the document root (what the web server serves) must point to the Laravel `public/` folder:
```
/home/{cpanel-username}/portal.vestibulartherapyassociates.co.uk/public/
```
In cPanel → Subdomains → find the portal subdomain → click **Edit** → change Document Root to:
```
portal.vestibulartherapyassociates.co.uk/public
```
Click **Change**

**Step 3 — SSL Certificate:**
1. In cPanel → **SSL/TLS** → **Let's Encrypt SSL**
2. Find `portal.vestibulartherapyassociates.co.uk` in the list
3. Click **Issue** — free SSL auto-issued and auto-renewed
4. Portal will be accessible at: https://portal.vestibulartherapyassociates.co.uk

---

### 15.3 MySQL Database Setup in cPanel

1. In cPanel → **MySQL Databases**
2. Create database: `vta_portal`
   - Full name will be: `{cpanel-username}_vta_portal`
3. Create database user: `vta_user`
   - Full name: `{cpanel-username}_vta_user`
   - Set a strong password — save it securely
4. Add user to database with **All Privileges**
5. Update `.env` with the full database name and username:
```env
DB_DATABASE={cpanel-username}_vta_portal
DB_USERNAME={cpanel-username}_vta_user
DB_PASSWORD={the password you set}
```

---

### 15.4 Email Setup in cPanel

Krystal Emerald includes email hosting. The following mailboxes are needed:

| Email Address | Purpose |
|---|---|
| operations@vestibulartherapyassociates.co.uk | Shared intake mailbox — already exists |
| samy@vestibulartherapyassociates.co.uk | Samy's email |
| sheeba@vestibulartherapyassociates.co.uk | Sheeba's email |
| jai@vestibulartherapyassociates.co.uk | Jai's admin email |

**To create/verify mailboxes:**
1. cPanel → **Email Accounts**
2. Click **Create** for any that do not exist
3. Set password for each account
4. SMTP/IMAP settings for the portal `.env`:
```env
MAIL_HOST=mail.vestibulartherapyassociates.co.uk
MAIL_PORT=587
MAIL_USERNAME=operations@vestibulartherapyassociates.co.uk
MAIL_ENCRYPTION=tls

MAIL_IMAP_HOST=mail.vestibulartherapyassociates.co.uk
MAIL_IMAP_PORT=993
MAIL_IMAP_ENCRYPTION=ssl
MAIL_IMAP_USERNAME=operations@vestibulartherapyassociates.co.uk
```

---

### 15.5 Git Deployment to Krystal

**Initial deployment:**
```bash
# SSH into Krystal server
ssh {cpanel-username}@vestibulartherapyassociates.co.uk

# Navigate to portal directory
cd ~/portal.vestibulartherapyassociates.co.uk

# Clone the repository
git clone {your-repository-url} .

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Copy and configure environment file
cp .env.example .env
# Edit .env with production values (DB credentials, mail settings, app key)

# Generate application key
php artisan key:generate

# Run database migrations and seeders
php artisan migrate --seed

# Set correct file permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/vta-documents

# Create storage symlink
php artisan storage:link

# Optimise for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Subsequent deployments (updates):**
```bash
cd ~/portal.vestibulartherapyassociates.co.uk
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 15.6 Cron Job for Scheduled Tasks

The email intake service checks the operations@ inbox every 15 minutes. Set this up in cPanel:

1. cPanel → **Cron Jobs**
2. Set frequency: Every 15 minutes
   - Minute: `*/15`
   - Hour: `*`
   - Day: `*`
   - Month: `*`
   - Weekday: `*`
3. Command:
```bash
/usr/local/bin/php /home/{cpanel-username}/portal.vestibulartherapyassociates.co.uk/artisan schedule:run >> /dev/null 2>&1
```
4. Click **Add New Cron Job**

---

### 15.7 Login Page Design

The portal login page at `https://portal.vestibulartherapyassociates.co.uk/login` should:

- Show the VTA logo at the top
- Show the heading: **"VTA Staff Portal"**
- Show Email and Password fields
- Show a **"Sign In"** button
- Show a **"Forgot your password?"** link
- Show this message at the bottom:
  > *"Access to this portal is by invitation only. If you need an account, please contact operations@vestibulartherapyassociates.co.uk"*
- **No Register button** — accounts are created by admin only inside Settings

---

### 15.8 Link from Main Website to Portal

Add a subtle **"Staff Portal"** or **"Login"** link to the existing VTA website navigation pointing to:
> https://portal.vestibulartherapyassociates.co.uk/login

This is the only connection between the public website and the portal. The main website is not modified in any other way.

**Suggested placement:** Top right of the main website navigation bar, styled as a small secondary button or text link — not prominent, as it is for internal use only.

---

### 15.9 File Storage Path

All uploaded documents are stored on the Krystal server at:
```
/home/{cpanel-username}/portal.vestibulartherapyassociates.co.uk/storage/app/vta-documents/
```

Folder structure within:
```
vta-documents/
├── {company-slug}/
│   └── {case-manager-slug}/
│       ├── nda/
│       ├── materials/
│       └── {patient-slug}/
│           ├── loi/
│           ├── medical-records/
│           ├── assessment-reports/
│           ├── cost-estimations/
│           ├── funding-approvals/
│           ├── associate-invoices/
│           ├── vta-invoices/
│           ├── case-notes/
│           └── correspondence/
└── inbox/
    └── (email attachments from intake — pending linking to patient)
```

Krystal Emerald provides unlimited NVMe storage — no storage limit concerns.

---

### 15.10 Security Notes for Krystal Deployment

1. **Never expose the storage folder publicly** — documents are always served through the authenticated DocumentController, never via direct URL
2. **Set APP_DEBUG=false** in production `.env` — never expose error details publicly
3. **The `.env` file must not be in the public/ folder** — Laravel's default structure already ensures this
4. **Krystal's DDoS protection (2,000 Gbps)** covers the portal automatically — no extra setup needed
5. **Backups run every 4 hours automatically** on Krystal Emerald — verify backup schedule in cPanel → **JetBackup**
6. **PHP version:** Ensure cPanel is running PHP 8.1 or higher — check in cPanel → **MultiPHP Manager** → set portal subdomain to PHP 8.2

---

*Updated: June 2026 — Added Part 15: Hosting Setup on Krystal Emerald*
