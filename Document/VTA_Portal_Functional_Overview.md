# VTA Portal — Functional Overview & Workflow Guide

## Vestibular Therapy Associates

### June 2026 | For: Dr. Samy Selvanayagam

> **Read this first, then use `VTA_Portal_UAT_Guide.md` to actually click through and test.** This document explains *what* the portal does and *why* it's built the way it is. The UAT Guide tells you exactly what to click. This one gives you the mental model so the UAT steps make sense rather than feeling like a list of disconnected clicks.

---

## 1. WHAT THIS PORTAL DOES

The VTA Portal is a single system that replaces several things you currently juggle separately:

| You currently use... | For... | The portal replaces it with... |
|---|---|---|
| Excel tracker | Logging enquiries, tracking status, follow-up dates, nearest associate | Enquiries, Patients, and Communications screens with built-in status workflow and follow-up reminders |
| Desktop folders (Company → Case Manager → Patient) | Storing documents | Documents attached directly to the Company, Case Manager, or Patient record — searchable, permission-controlled, never misfiled |
| Qunote calendar | Booking and tracking appointments | A shared Team Calendar, plus each Associate's own filtered view |
| Xero | Invoicing associates and funders | Associate Invoices and VTA Invoices, with auto-calculated rates and auto-generated invoice numbers |
| Email inbox | Tracking enquiries that come in by email | Email Intake screen — emails can be linked straight to a patient or used to create one |

The core idea: **one patient's entire journey — from first enquiry to discharge — lives in one place**, with the right people seeing the right amount of it.

---

## 2. THE FOUR ROLES

Every person who logs in is one of four roles. The role decides what they see the moment they log in — there's no separate "permissions setup" you need to do per person beyond picking their role.

| Role | Who | Lands on | Can see | Cannot do |
|---|---|---|---|---|
| **Admin** | You (Samy), Jai | Main Dashboard | Everything — all patients, all companies, finance, settings | Nothing is restricted |
| **Staff** | Sheeba | Main Dashboard | All patients and companies, same as Admin — **except finance** (no invoices, no revenue reports) | Cannot see Associate Invoices, VTA Invoices, or Finance Reports; cannot delete documents |
| **Associate** | Kate, and the other therapists | Associate Portal (separate, simplified view) | Only their own assigned patients — nothing else | Cannot see other associates' patients, cannot see Companies, Email Intake, or any financial information |
| **Case Manager** | Sarah, Michael, David, and any future case manager who gets a login | Case Manager Portal (separate, simplified, read-only view) | Only patients belonging to their own company | Read-only — cannot add, edit, or delete anything; only views and downloads permitted documents |

A few things worth understanding about this design:

- **Associates and Case Managers don't get the same screens as you.** They're redirected to their own simplified portal the moment they log in — they never see your main navigation menu at all, not even a greyed-out version of it.
- **Case Managers are fully read-only.** Until you decide otherwise, they can only look and download — there is no "edit" capability for them anywhere, by design. This protects your data from accidental changes by people outside your organisation.
- **Document visibility per role is separately configurable.** Even within what an Associate or Case Manager can see, you control *which types* of documents they're allowed to view (Settings → Document Permissions) — e.g. you might let Case Managers see Assessment Reports but not Medical Records.

---

## 3. THE END-TO-END WORKFLOW

This is the heart of the system: every patient has a **Status** that moves through a fixed sequence of stages. You can't skip stages, and a few stages have a hard gate that physically blocks you from moving forward without the right document — this is intentional, to stop "Treatment Active" ever happening without funding actually being in writing.

```
 1. Enquiry Logged          ── someone gets in touch (Section A2 of UAT Guide)
        ↓
 2. Response Sent           ── you've replied to the enquirer
        ↓
 3. Awaiting LOI            ── waiting on the Letter of Instruction
        ↓
 4. LOI Received            ── LOI is in hand
        ↓
 5. Assessment Scheduled    ── appointment booked with an Associate
        ↓
 6. Assessment Completed    ── the initial assessment has happened
        ↓
 7. Report Drafted          ── Associate/Admin has written up the assessment
        ↓
 8. Report Sent             ── report sent to the referrer
        ↓
 9. Cost Estimation Sent    ── quote for treatment sent
        ↓
10. Awaiting Funding Approval ── waiting on the funder's written approval
        ↓
11. Funding Approved  🔒    ── BLOCKED until a Funding Cycle with an approval document is uploaded
        ↓
12. Treatment Active        ── sessions are happening, case notes being logged, invoices being raised
        ↓
13. Awaiting Further Funding ── (if treatment needs to extend beyond the approved sessions/amount)
        ↓
14. Discharged               ── treatment has ended
        ↓
15. Case Closed              ── administratively closed
```

**What happens at each major stage, and who's involved:**

**Enquiry (stages 1–4).** Someone — usually a Case Manager, solicitor, or family member — contacts VTA. You log it as an Enquiry first (quick capture: who, which company, why), before creating any permanent records. Only once you decide to proceed do you "Convert" the enquiry into proper Company and Case Manager records (or link it to ones that already exist — more on that in Section 4). Communications (calls, emails) with the enquirer can be logged from this point on, each with an optional follow-up date that shows up on the Team Calendar so nothing gets forgotten.

**Assessment (stages 5–8).** The patient record is created, an Associate is assigned (based on location/specialism), and an appointment is booked. After the assessment happens, a report is drafted and sent.

**Funding (stages 9–11).** A Cost Estimation (a quote) is sent to whoever is paying — the Case Manager's company, a solicitor, or an insurer. Once funding is agreed, you create a **Funding Cycle** — this is the formal record of "£X has been approved for Y sessions," and it requires an uploaded approval document. **The portal will not let you mark a patient as Funding Approved without this document attached** — this is one of the built-in safety rules, not a bug, and it's worth deliberately testing that it blocks you (see UAT Guide step A17).

**Treatment (stages 12–13).** Sessions happen. The Associate logs a Case Note after each one (visible to you on the patient's own page, with a sign-off status). You log the Associate's invoice when it arrives, and raise your own invoice to the funder against the Funding Cycle — the portal tracks the remaining balance automatically as invoices are raised against it, and (separately) **blocks marking your invoice "Sent" until a document is attached to it** — same idea as the funding gate, just applied to invoicing.

**Closure (stages 14–15).** Treatment ends, the case is discharged, and eventually closed administratively.

---

## 4. THE RECORDS INVOLVED (GLOSSARY)

| Record | What it is | Created by |
|---|---|---|
| **Enquiry** | The very first log of someone getting in touch. Lightweight — just enough to capture who and why. | Admin/Staff |
| **Company** | The organisation that refers patients to you — a case management firm, law firm, or insurer. A **master record**, picked from a dropdown everywhere it's used (see note below) | Admin/Staff |
| **Case Manager** | A named person at a Company who refers patients and is your day-to-day contact. Also a master record. | Admin/Staff |
| **Patient** | The actual person receiving treatment. Belongs to one Case Manager at a time (can be transferred). | Admin/Staff |
| **Associate** | Your therapists who carry out assessments and treatment. Has a session rate and a travel rate, used to auto-calculate their invoices. | Admin/Staff (Settings) |
| **Communication** | A logged call, email, letter, meeting, or message — with anyone (an enquirer, a Case Manager, or about a Patient). Can carry a follow-up date. | Admin/Staff |
| **Follow-up** | Not a separate record — it's a date you set on a Communication. While open, it shows as a purple marker on the Team Calendar; marking it done clears it from the calendar and from the log. | Set on a Communication |
| **Case Note** | A clinical note an Associate writes after a treatment session. Needs Admin sign-off. Shown on the Patient's own page. | Associate |
| **Document** | Any file — LOI, assessment report, funding approval, invoice PDF, correspondence. Can attach to an Enquiry, a Company/Case Manager, or a Patient. Who can see which *type* of document is controlled separately (Settings → Document Permissions). | Anyone with upload rights |
| **Cost Estimation** | A quote for treatment, sent to the payer. A patient can have several over time (e.g. an initial assessment quote, then a treatment-phase quote). | Admin/Staff |
| **Funding Cycle** | The formal record of approved funding — amount, sessions, approval document. This is the gate that has to exist before treatment can become "Active." | Admin/Staff |
| **Associate Invoice** | What an Associate bills VTA for their sessions and travel. Due date auto-set to 28 days out. | Admin/Staff |
| **VTA Invoice** | What VTA bills the funder. Auto-numbered (VTA-2026-0001, etc.), tracked against the Funding Cycle's remaining balance. | Admin/Staff |
| **Activity Type** | The kind of appointment (Initial Assessment, Treatment Session, Video Consultation, etc.) — you can add new ones in Settings without needing a developer. | Admin (Settings) |
| **Document Type** | The category a Document falls into (LOI, Assessment Report, Medical Records, etc.) — also extendable in Settings, each with its own per-role visibility toggle. | Admin (Settings) |

**A note on Companies and Case Managers being "master records":** earlier in development, these were free-text fields — meaning the same company could end up typed three slightly different ways across different enquiries ("Smith & Jones", "Smith and Jones Solicitors", etc.), making it impossible to reliably group a Case Manager's patients together. They're now proper dropdown-selected records with a quick "+ Add New" option when one genuinely doesn't exist yet — this is one of the things specifically worth checking during UAT (Guide steps A2/A3).

---

## 5. SAFETY RULES BUILT INTO THE PORTAL

These aren't configurable — they're deliberately hard-coded so the system enforces good practice even under time pressure:

- **Can't mark a patient "Funding Approved" without an uploaded funding approval document** attached to a Funding Cycle.
- **Can't mark a VTA Invoice "Sent" without a document attached** (the invoice itself, as a PDF).
- **An Associate can only hold one active role per patient at a time** (e.g. can't be assigned "Assessment" twice simultaneously) — stops accidental double-booking of the same function.
- **Patient status can only move to specific next stages**, not jump arbitrarily (e.g. you can't jump straight from "Enquiry Logged" to "Treatment Active").
- **Associates and Case Managers only ever see their own patients** — enforced at the data level, not just hidden in the menu (typing in a direct URL to another patient still gets blocked).
- **Document visibility is checked per document type and per role** every time, not just once when uploaded — so changing a permission in Settings takes effect immediately across all existing documents of that type.

---

## 6. WHAT'S NEW SINCE THE LAST ROUND OF TESTING

A few things were added specifically because they came up during hands-on testing, not in the original plan — worth knowing about going in:

- **Follow-up dates on Communications now show on the Team Calendar** as a separate purple marker, with a one-click "Mark Done" — previously a follow-up date just sat unseen on a Communication record.
- **Case Notes now appear directly on the Patient's own page** (previously only visible via a separate, disconnected Case Notes menu).
- **A few bugs were found and fixed:** saving the free-text Notes box on a Patient was throwing an error; leaving the Summary blank on a Communication was throwing an error; a couple of dropdown menus didn't match what was actually stored in the database (Company Type, Enquiry Source).

All of this is covered in the UAT Guide's new steps (A6B, A10B, A10C, and the tweaks to A2/A3/B7) — you'll exercise all of it naturally as you go through.

---

## 7. HOW TO USE THIS ALONGSIDE THE UAT GUIDE

1. Read this document end to end first — no clicking required.
2. Keep the Glossary (Section 4) and the Workflow diagram (Section 3) open in another tab while you test — most of the UAT Guide's steps map directly onto one stage of that workflow.
3. Open `VTA_Portal_UAT_Guide.md` (or the formatted `.html` version) and work through Section A as Admin, exactly as written.
4. When a step's "What this represents" note doesn't quite make sense, come back here — it's almost certainly explained in Section 3 or 4.
5. Anything that doesn't behave as this document or the UAT Guide describes is worth flagging to Jai — that's the whole point of UAT.

---

*Functional Overview Version: 1.0 | June 2026*
*Companion document to: VTA_Portal_UAT_Guide.md*
*Prepared by: Jai Anand | jai@vestibulartherapyassociates.co.uk*
