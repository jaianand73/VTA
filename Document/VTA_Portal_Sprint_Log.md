# VTA Portal — Sprint Log

> **Purpose:** Living record of every development iteration.
> Each sprint follows the same cycle: Samy answers questions → Dev plans → Dev builds → UAT steps added → Samy tests → Dev reviews findings → next sprint.
> The Feedback Board (portal) is the live tracker; this document is the permanent record.
>
> **Last updated:** 2026-06-28 (Sprint 2 scope filled in — all 17 Q&A answers recorded)
> **Related documents:**
> - `VTA_Portal_Development_Overview.md` — full technical reference
> - `VTA_Portal_Phase2_Agent_Spec.md` — current sprint's build contract
> **How we work:** See §15 "How We Work" in the Overview doc.

---

## Sprint 0 — Foundation Build
**Period:** May – Jun 2026
**Trigger:** Initial project kick-off; no Samy input yet — built against the Master Spec.

### What was built
- Full Laravel 11 portal skeleton (auth, roles, middleware)
- **Phase 1 — CRM:** Enquiries, Patients, Companies, Case Managers, Communications, Documents, Email Intake (13 screens)
- **Phase 2 — Clinical:** Appointments Calendar, Case Notes, Associate Portal, Case Manager Portal (8 screens)
- **Phase 3 — Finance:** Funding Cycles, Cost Estimations, Associate Invoices, VTA Invoices, Finance Reports (7 screens)
- **Settings:** Activity Types, Document Types, Permissions, Associates, Users (6 screens)
- 19 custom database tables, 5 seeders
- 18 business rules implemented (BR-C1 to BR-A5)

### Live testing & defects
- Live browser tested all 4 roles end-to-end on 2026-06-22
- **17 defects** found and fixed (missing date casts, broken routes, enum mismatches, dead policies — see §12 of Overview)
- 3 additional client-found issues fixed (company/CM masters, Notes field crash, Communications on Enquiry)

### UAT steps added
None — UAT guide not yet built.

### Outcome
Portal fully functional. First staging deployment to EC2 (`52.66.166.34:8080`) on 2026-06-27. Static marketing site deployed alongside.

---

## Sprint 1 — Feedback Board, Phase 2 Spec & UAT Guide
**Period:** 2026-06-27 – 2026-06-28
**Trigger:** Live Samy walkthrough revealed gaps vs. his actual workflow (Excel tracker, handwritten PDFs, PPTX meeting notes).

### Questions asked / trigger for this sprint
Four Samy documents analysed:
- Handwritten PDF 1 & 2 (clinical workflow, referral tracking)
- "Actions from meeting on 25th June.pptx" (nav structure, dashboard widgets)
- "Process mapping.xlsx" (live working tracker — source of truth for data model)

Key gaps found:
- No "Qualified as Referral" gate on Enquiry
- Enquiry and Patient permanently disconnected (no link)
- No Assessment module
- Finance labelled "Accounts" everywhere by Samy
- Dashboard missing 3 of 5 expected widgets

### What was built
| Feature | Files |
|---|---|
| **Feedback Board** | `portal_feedback_items` table, `PortalFeedbackItem` model, `PortalFeedbackController`, full Blade view |
| **46 pre-loaded items** | 18 changes, 18 questions (Q1–Q18), 10 improvements |
| **Developer role** | Added to `users.role` ENUM; Jai Anand → `developer` |
| **Phase 2 questions (P2)** | 9 Phase 2 questions added below Q18 on Feedback Board (toggle UX, patient classification etc.) |
| **Phase 3 questions (P3)** | 15 Phase 3 questions added (P3-Q1 to P3-Q15) |
| **UAT Guide** | `uat_test_results` table, `UatTestResult` model, `UatGuideController`, `show.blade.php`, `_section.blade.php` |
| **41 UAT steps** | Sections A (Admin/Samy), B (Staff/Sheeba), C (Associate/Kate), D (Case Manager), F (Final) |
| **UAT in top bar** | Progress bar + stat chips injected into existing fixed top bar via `$topbar` Blade slot |

### UAT steps added this sprint
| Section | Steps | Covers |
|---|---|---|
| A — Admin (Samy) | A1–A17 | Login, Enquiry, Referral, Patient, Assessment, Appointment, Case Notes, Finance, Settings |
| B — Staff (Sheeba) | B1–B6 | Enquiry, Communications, Patient management |
| C — Associate (Kate) | C1–C5 | Associate portal, Calendar, Case Notes, Documents |
| D — Case Manager | D1–D4 | Case Manager portal, Patient view, Documents |
| F — Final | F1–F4 | Cross-role checks, Business rule gates |
| **Total** | **41 steps** | All current portal features |

### Samy's UAT results (as of 2026-06-28)
*(Update this as Samy records results on the portal)*

| Result | Count |
|---|---|
| Pass | — |
| Fail | — |
| Pass + Suggestion | — |
| Not yet tested | 41 |

### Samy's Q&A answers (as of 2026-06-27)

All 17 Phase 1 questions answered. Key decisions recorded here; full developer impact in `VTA_Portal_Phase2_Agent_Spec.md` Part 12.

| Ref | Answer / Decision | Dev Impact |
|---|---|---|
| Q1 | Qualify = referrer approves first assessment. Date + remarks sufficient. No funding promise at this stage. | A1 route confirmed (admin only) |
| Q2 | Admin only can qualify — LOI always comes to VTA, not staff. | A1 locked to `role:admin` |
| Q3 | Flexible contact types — whoever was on the enquiry letter. Can be health professional + their line manager. Both need to be recorded for future comms. | A3 (enquiry contacts) unblocked |
| Q5 | Step 2 of Excel was for patient diagram — superseded by Samy's image of associate portal. | No code change |
| Q6 | One initial assessment per patient (one-to-one). Reviews are part of treatment cycle, not separate assessments. | C1: UNIQUE constraint on `assessments.patient_id` |
| Q7 | Assessor = free text (Samy or any associate). Not a FK. | C1: free text `assessor` field |
| Q8 | Funder (insurer/solicitor) pays assessment fee. Invoiced via VTA invoice system. Cost document = invoice document. | C1 + E4: `assessment_id` FK on `vta_invoices` |
| Q9 | Report sent externally by email — portal records checkbox + upload only. `special_instructions` field governs who gets what. | C1: no email-sending needed |
| Q10 | Keep Cost Estimation and Funding Cycle separate. Patient created only after referrer agrees to costs post-assessment. | A2 flow updated: "Create Patient" appears after cost agreement |
| Q11 | Multiple payer types: Case Manager, Deputy, Solicitor, Insurer, Others. These are also invoice recipients. | B2: patient referrer roles confirmed |
| Q12 | Must verify associate invoices against estimation. Portal must show used/remaining balance per patient. | I6 elevated to HIGH: balance summary on patient page |
| Q13 | Always sequential — one active funding cycle at a time. | BR-P6: block new cycle if one is active |
| Q14 | Samy is Clinical Head now. Associates manually flag each report submission for CH review. | F1: `needs_review` checkbox in Associate Portal |
| Q15 | Priority 1 report: Funding/session balance per active patient. Priority 2: Financial summary. Conversions + associate activity = low priority (every 6 months). | G2 reports re-ordered accordingly |
| Q16 | Keep emails OUT of portal (too much junk). Only log relevant emails to Communications on Patient page. | I8 simplified: link email → patient comms only |
| Q17 | Nearest associate = suggestion only. Samy sometimes does assessment first, then allocates. | Associate field stays as suggestion, not auto-allocate |
| Q18 | Yes, max 2 associates per patient (e.g. Samy + junior). Not always the case. | `patient_associates` table confirmed; no change needed |

- **P2 Questions:** Pending Samy's response
- **P3-Q1 to P3-Q15:** Pending Samy's response

### Open items carried into Sprint 2
All Phase 1 questions are resolved. P2/P3 questions (Phase 2/3 process flow) are pending.

| Ref | Feature | Status |
|---|---|---|
| A3 | Multiple enquiry contacts with roles | Ready — Q3 answered |
| B2 | Patient referrer roles | Ready — Q11 confirmed roles |
| C1 | Assessment module | Ready — Q6/Q7/Q8/Q9 answered |
| C2 | Assessment cost vs Cost Estimation | Ready — depends on C1 |
| E4 | Assessment fee → VTA invoice link | Ready — depends on C1 |
| B3 | Auto status transitions | Ready — depends on C1 |
| F1 | Dashboard 5 widgets | Ready — Q14/Q15 answered |
| G2 | Reports section | Ready — Q15 answered (priority order set) |
| Accounts Admin questions | AA-Q1/Q2/Q3 | Pending Samy's response on EC2 |
| P2/P3 process questions | 25 questions | Pending Samy's response on EC2 |

---

## Sprint 2 — Assessment, Dashboard & Referrer Data
**Trigger:** All 17 Phase 1 Q&A answers received from Samy (2026-06-27). Sprint 1 UAT results pending.

### How to start Sprint 2
1. ✅ Read Samy's answers on the Feedback Board (done — see Sprint 1 above)
2. Read UAT results when available (Fail + Suggestion items auto-appear on Feedback Board)
3. Build features below in order specified in `VTA_Portal_Phase2_Agent_Spec.md` Part 8, Sprint 2 section
4. Push to EC2 after each group
5. Add new UAT steps beyond A17/B6/C5/D4/F4 for new features
6. Notify Samy to test

### Sprint 2 build scope (ready to start)

All items below are **fully unblocked** — all required answers received.

| Item | Feature | From Agent Spec | Est. time |
|---|---|---|---|
| A3 | Multiple enquiry contacts with roles (Case Manager, Health Professional, Line Manager, Solicitor, Insurer, Other) | Part 4 / A3 | 3h |
| B2 | Patient referrer details — 4 payer roles (Case Manager, Deputy, Solicitor, Insurer, Other) | Part 4 / B2 | 3h |
| C1 | Assessment module — new table, model, controller, routes, views. One per patient, free text assessor, `special_instructions` field | Part 4 / C1 | 5h |
| C2 | Assessment cost vs Cost Estimation — two separate figures on patient page | Part 4 / C2 | 1h |
| E4 | Assessment fee → VTA invoice auto-link | Part 4 / E4 | 1h |
| B3 | Auto status transitions on Patient (Assessment Scheduled, Report Sent, etc.) | Part 4 / B3 | 2h |
| F1 | Dashboard 5 widgets: Emails, Clinical Head Review, Pending Enquiries, Invoices Due, Calendar | Part 4 / F1 | 3h |
| G2 | Reports: Priority 1 = Funding balance per patient; Priority 2 = Financial summary | Part 4 / G2 | 4h |
| Fixes | Any UAT Fails Samy has recorded | Feedback Board | TBD |
| Improvements | Any UAT Suggestions Samy has recorded | Feedback Board | TBD |

### Waiting on (does NOT block above items)
- Samy's answers to P2 questions (Phase 2 process flow)
- Samy's answers to P3-Q1 to P3-Q15 (Phase 3 accounts/invoicing detail)
- Samy's answers to AA-Q1/Q2/Q3 (Accounts Admin section questions)
- Samy's UAT test results (A1–F4)

---

## Sprint 3 — [Future]
*(Defined after Sprint 2 is complete)*

---

## How to add a new sprint entry

When starting a new sprint, copy this template:

```markdown
## Sprint N — [Short Title]
**Period:** [Start] – [End]
**Trigger:** [What kicked this off — Q&A answers / UAT results / new requirement]

### Questions answered / trigger detail
[Which questions Samy answered, key decisions made]

### What was built
| Feature | Files |
|---|---|
| | |

### UAT steps added this sprint
| Section | Steps | Covers |
|---|---|---|
| | | |

### UAT results
| Result | Count |
|---|---|
| Pass | |
| Fail | |
| Pass + Suggestion | |

### Open items carried into Sprint N+1
[List unresolved items]
```
