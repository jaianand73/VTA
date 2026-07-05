# VTA Portal — Development Sprint Tracker

**Last updated:** 2026-07-05  
**Single source of truth for all active and planned development.**  
Update this file at the end of every session or whenever a task status changes.

---

## Status Key

| Symbol | Meaning |
|--------|---------|
| ✅ | Done — live in local, tested |
| 🚀 | Done — deployed to production |
| 🔄 | In progress |
| ⏳ | Pending — not started |
| ❌ | Blocked — needs decision or dependency |
| 🔁 | Needs revisit / known gap |

---

## Phase 1 — Core Enquiry → Referral → Patient Flow

> The foundational 3-stage funnel. All other work depends on this being stable.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1.1 | Enquiries module (CRUD, status, contacts) | ✅ | Full CRUD, enquiry_contacts table, status enum |
| 1.2 | Enquiry ref (VTA-xxx) auto-generation | ✅ | enquiry_ref column, format enforced |
| 1.3 | Referrals module (CRUD, 7-status enum) | ✅ | Created from enquiry only — no standalone creation |
| 1.4 | "Promote to Referral" from enquiry | ✅ | Guard: blocks Not Proceeding; redirects if already promoted |
| 1.5 | Same ref ID flows enquiry → referral → patient | ✅ | enquiry_ref = referral_ref = patient_ref |
| 1.6 | Remove standalone "New Referral" button | ✅ | Index + empty state both redirect to enquiries |
| 1.7 | Go-ahead recording + associate assignment | ✅ | `approveVisit()` saves visit_approved_date + associate_id, status → Assessment |
| 1.8 | Proposal submission | ✅ | `submitProposal()`, status → Proposal Submitted |
| 1.9 | Proposal approval | ✅ | `approveProposal()`, status → Approved |
| 1.10 | Convert to Patient | ✅ | Shows assessment associate; allows change for treatment associate |
| 1.11 | Patient audit trail (enquiry_id + referral_id FKs) | ✅ | Both FKs on patients table |
| 1.12 | Referral status shown on patient show page | ⏳ | Patient show page does not surface the referral history/timeline |

---

## Phase 2 — Associate Activity Layer (Referral Stage)

> Associate does the first assessment. All activity logged against the referral, not yet a patient.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 2.1 | referral_sessions table + model | ✅ | activity_type_id FK (not method enum), scheduled_at, duration_minutes, location |
| 2.2 | referral_bills table + model | ✅ | bill_date, amount, status enum (Pending/Paid/Unpaid), document_path |
| 2.3 | referral_communications table + model | ✅ | type, direction, subject, notes, document_path |
| 2.4 | referral_documents table + model | ✅ | title, file_path, visible_to_associate, revision_requested, revision_notes |
| 2.5 | ReferralActivityController (Samy CRUD) | ✅ | Sessions, bills, comms, documents — store/destroy; bill status update |
| 2.6 | Activity sections on Samy's referral show page | ✅ | Sessions (with Log Session inline form), Comms, Documents, Bills |
| 2.7 | Document revision loop | ✅ | Samy requests revision with note → associate sees amber alert → re-uploads → flag clears |
| 2.8 | Associate portal — My Referrals list | ✅ | Shows Assessment/Proposal Submitted/Approved referrals |
| 2.9 | Associate portal — Referral detail page | ✅ | Patient info, special instructions, Samy's shared docs, session/comm/doc/bill forms |
| 2.10 | Associate dashboard — My Referrals counter card | ✅ | Orange card, links to referrals list |
| 2.11 | Associate portal nav — "My Referrals" sidebar link | ✅ | Expanded to 4 links: Dashboard, My Referrals (amber badge for pending revisions), My Patients, My Calendar |
| 2.12 | Referral sessions use activity_types table | ✅ | Migrated from enum; activityType relationship added |

---

## Phase 3 — Calendar & Appointments Integration

> Referral sessions and patient appointments are parallel. Both visible on the same calendar.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 3.1 | Calendar shows referral sessions (dark green) | ✅ | fetchReferralSessionEvents() merged into fetchEvents() |
| 3.2 | Calendar legend updated with referral session colour | ✅ | Dark green added to colour key |
| 3.3 | Calendar modal handles referral_session event type | ✅ | Shows patient, ref, associate, activity, location, "View Referral" link |
| 3.4 | Appointment create — context toggle (Patient / Referral) | ✅ | Alpine.js toggle; referral context saves to referral_sessions via storeReferralSession() |
| 3.5 | Associate calendar shows referral sessions | ✅ | eventClick now handles referral_session type with green badge, ref, "View Referral" link; legend added |
| 3.6 | Referral sessions filterable on calendar by associate/activity | ✅ | fetchReferralSessionEvents() respects associate_id and activity_type_id filters |

---

## Phase 4 — Accounts / Finance

> Referral bills (pre-patient stage) need to feed into finance views alongside VTA invoices.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 4.1 | referral_bills table exists | ✅ | Phase 2.2 |
| 4.2 | Referral bills visible in finance/reports.blade.php | ✅ | Added "Assessment Costs" panel to financial-summary (3-col grid) |
| 4.3 | Referral bills in financial summary report | ✅ | referralBillsTotal + referralBillsPaid passed from ReportsController |
| 4.4 | Referral bills in funding balance report | 🔁 | Funding balance is patient/cycle data; referral bills are pre-patient — doesn't apply |
| 4.5 | Accounts index (accounts/index.blade.php) — referral bill totals | ✅ | New "Referral Bills" tab: 4 KPI cards, filters, table with associate/patient/amount/status |
| 4.6 | Associate bill status (Pending/Paid/Unpaid) drives payment workflow | ✅ | markBillPaid() action + PATCH route; "Mark Paid" button on accounts Referral Bills tab |

---

## Phase 5 — Audit Reports

> Patient audit should show the complete life journey. Associate audit should include referral-stage activity.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 5.1 | Patient audit — add enquiry → referral chain | ✅ | "Referral Stage" card with milestone timeline, activity counts, sessions list, bills list |
| 5.2 | Patient audit — referral milestone dates (go-ahead, proposal, approval, conversion) | ✅ | 6-step milestone bar: Enquiry → Promoted → Go-ahead → Proposal Submitted → Approved → Patient |
| 5.3 | Patient audit — referral sessions and bills | ✅ | Mini-lists of sessions and bills within the Referral Stage card |
| 5.4 | Associate audit — referral sessions | ✅ | "Referral Assessment Sessions" section grouped by referral_ref; 3 new stat tiles |
| 5.5 | Associate audit — referral bills | ✅ | Bills Pending + Bills Paid (Ref.) stat tiles added; full audit links to associate audit page |
| 5.6 | Master log report — referral stage in case timeline | 🔁 | Master log is patient-stage funding/expense only; referral bills are pre-patient — n/a |
| 5.7 | Associate activity report — referral sessions data | ✅ | 2 new columns: Referral Sessions + Bills (£); merged by allAssociateIds; direct audit link |

---

## Phase 6 — Seed Data

> Realistic test data that covers the full E→R→P flow with activity at each stage.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 6.1 | seed_rich_data.php — chained flow VTA-009/010 (enquiry→referral→patient) | ✅ | Full chain: enquiry_id + referral_id both on patient |
| 6.2 | seed_rich_data.php — referrals VTA-004–008 with various statuses | ✅ | All 7 statuses covered across 7 referrals |
| 6.3 | Add referral_sessions records to seeder | ✅ | VTA-006 (2 sessions), VTA-007 (3 sessions), VTA-009 (2 sessions) — 7 total |
| 6.4 | Add referral_bills records to seeder | ✅ | VTA-007 (2 bills: £350 Paid + £120 Pending), VTA-009 (1 bill: £280 Paid) |
| 6.5 | Add referral_communications records to seeder | ✅ | 4 comms across VTA-006, VTA-007, VTA-009 |
| 6.6 | Add referral_documents (with revision loop example) | ✅ | VTA-006 has doc with revision_requested=true and revision_notes set |
| 6.7 | Associate users with user_id linked to Associate records | ✅ | Georgios (4), Ileana (5), Nick (6), Sultana (7) — all password: 'password' |
| 6.8 | VTA-009/010 patients — full chain integrity verified | ✅ | Chain verified: enquiry_id + referral_id correct on both patients |
| 6.9 | Remove stale /tmp/wipe_case_data.php from production | ⏳ | May still be on server — check before production deploy |
| 6.10 | Seed future-dated appointments + referral sessions (July 2026) | ✅ | 10 appointments (VTA-009/010/011/012) + 3 July referral sessions (VTA-006/007) — calendar now populated |

---

## Phase 7 — Documentation Pages (In-app)

> Three Blade views that explain the system to Samy and UAT testers.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 7.1 | understanding-each-page.blade.php | ✅ | Added "Referrals" tab: 7-status flow, all 6 activity sections (sessions/bills/comms/docs/revision loop/convert), key rules |
| 7.2 | how-it-works.blade.php | ✅ | Stage 2 "Referral & Assessment" inserted; mini-map updated; Stage 1 decision box corrected; "create patient directly" ref removed |
| 7.3 | uat-guide/show.blade.php | ✅ | Section G added — 10 steps covering full E→R→P flow, block test for no standalone referral creation, associate portal steps; total updated to 50 |

---

## Phase 8 — Associates Settings Page

> Minor polish — associate profile should surface referral workload.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 8.1 | settings/associates/show.blade.php — active referral count | ✅ | Workload card added: 4 stat tiles (active referrals, patients, in-assessment, proposal/approved) + list of live referrals with status badges and View links |
| 8.2 | settings/tabs/associates.blade.php — link to active referrals | ✅ | "Referrals" column added to table: green badge with count linking to filtered referrals index |

---

## Phase 9 — Production Deploy

> Nothing deployed to production since the referral flow was built. Full deploy needed.

| # | Task | Status | Notes |
|---|------|--------|-------|
| 9.1 | Git commit all outstanding changes | ⏳ | Large batch — referrals, activity layer, calendar, appointment create |
| 9.2 | Deploy changed files via SCP | ⏳ | All controllers, models, views, routes |
| 9.3 | Run migrations on production | ⏳ | 8 new migrations since last production deploy (see below) |
| 9.4 | Verify routes on production | ⏳ | php artisan route:cache on server |
| 9.5 | Smoke test on production | ⏳ | Enquiry → promote → go-ahead → sessions → proposal → approve → convert |

**Migrations to run on production (in order):**
1. `2026_07_05_155007_create_referrals_table`
2. `2026_07_05_155009_add_referral_id_to_patients_table`
3. `2026_07_05_155010_update_enquiry_contacts_role_enum`
4. `2026_07_05_162401_update_enquiry_status_enum_for_new_flow`
5. `2026_07_05_162510_add_missing_fields_to_patients_table`
6. `2026_07_05_172725_create_referral_sessions_table`
7. `2026_07_05_172725_create_referral_bills_table`
8. `2026_07_05_172726_create_referral_communications_table`
9. `2026_07_05_172727_create_referral_documents_table`
10. `2026_07_05_174032_add_revision_fields_to_referral_documents_table`
11. `2026_07_05_180523_swap_method_for_activity_type_on_referral_sessions_table`

---

## Known Gaps / Decisions Pending

| Item | Decision needed |
|------|----------------|
| Email notifications | Revision request and proposal approval are the natural trigger points. Build now or later? |
| Access control on activity routes | Currently any admin-role user can post to referral activity. Should case managers be blocked from requesting revisions? |
| Referral session on associate calendar | Associate's calendar (portal/associate/calendar.blade.php) has its own event source. Needs separate update. |
| Referral status on patient show page | When viewing a converted patient, should the referral history timeline be visible? If yes, Phase 1.12. |
| Bill payment workflow | Bills have Pending/Paid/Unpaid but no admin approval flow. Is that needed or is status toggle sufficient? |

---

## Recommended Next Order

Based on dependencies:

1. **Phase 6 (Seed data)** — without realistic test data, nothing else can be visually verified
2. **Phase 3.5 (Associate calendar)** — small, unblocked
3. **Phase 2.11 (Associate sidebar nav)** — small, unblocked
4. **Phase 4 (Finance/Accounts)** — unblocked once seed data is in
5. **Phase 5 (Audit reports)** — unblocked once seed data is in
6. **Phase 7 (Docs)** — do last, once all features are stable
7. **Phase 8 (Associates settings)** — minor, can slot in anytime
8. **Phase 9 (Production deploy)** — after Phases 2–5 complete and tested locally
