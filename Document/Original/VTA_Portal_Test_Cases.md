# VTA Portal — Complete Test Case Document
## Vestibular Therapy Associates — Quality Assurance Suite
### Version 1.0 | June 2026 | For Code Agent Test Execution

---

## INSTRUCTIONS FOR CODE AGENT

This document contains every test case for the VTA Portal. Execute all tests in the order listed. Each test case specifies:
- **TC-ID:** Unique test case identifier
- **Module:** Which part of the system is being tested
- **Type:** Functional / Security / Business Rule / UI / Integration
- **Priority:** P1 (Critical) / P2 (High) / P3 (Medium)
- **Preconditions:** What must exist before the test runs
- **Steps:** Exact actions to perform
- **Expected Result:** What must happen for the test to pass
- **Pass/Fail:** Record outcome

All P1 tests must pass before the application is considered production-ready.
All P2 tests must pass before Phase delivery is considered complete.
P3 tests are quality improvements — resolve before client handover.

---

## TEST DATA — SEED BEFORE RUNNING TESTS

Use this exact data for all tests. Run the seeders before starting.

```
ADMIN USERS:
  Email: samy@vestibulartherapyassociates.co.uk  | Password: ChangeMe2026! | Role: admin
  Email: jai@vestibulartherapyassociates.co.uk   | Password: ChangeMe2026! | Role: admin

STAFF USER:
  Email: sheeba@vestibulartherapyassociates.co.uk | Password: ChangeMe2026! | Role: staff

ASSOCIATE USER (create in Settings before running Phase 2 tests):
  Name: Kate Bryce | Email: kate@test.com | Role: associate | Password: TestAssoc2026!

TEST COMPANY:
  Name: Community Case Management Ltd
  Type: Case Management
  City: Warwick

TEST CASE MANAGER:
  Name: Emma Bracher
  Email: emma@communitycasemanagement.co.uk
  Company: Community Case Management Ltd

TEST PATIENT:
  First Name: David | Last Name: Clarke
  Location: Rugby
  Condition: TBI with vestibular disorder
  Referral Date: today's date
```

---

## MODULE 1 — AUTHENTICATION

### TC-001
**Module:** Authentication | **Type:** Functional | **Priority:** P1
**Test Name:** Admin login with valid credentials
**Preconditions:** Seeded admin user exists
**Steps:**
1. Navigate to https://portal.vestibulartherapyassociates.co.uk/login
2. Enter email: samy@vestibulartherapyassociates.co.uk
3. Enter password: ChangeMe2026!
4. Click Sign In
**Expected Result:** Redirected to /dashboard. Dashboard page loads. User name "Samy Selvanayagam" visible in sidebar. Role badge shows "Admin".
**Pass/Fail:** ___

---

### TC-002
**Module:** Authentication | **Type:** Functional | **Priority:** P1
**Test Name:** Staff login redirects to dashboard
**Preconditions:** Seeded staff user exists
**Steps:**
1. Navigate to /login
2. Enter email: sheeba@vestibulartherapyassociates.co.uk
3. Enter password: ChangeMe2026!
4. Click Sign In
**Expected Result:** Redirected to /dashboard. Finance menu items NOT visible in sidebar. Settings NOT visible in sidebar.
**Pass/Fail:** ___

---

### TC-003
**Module:** Authentication | **Type:** Functional | **Priority:** P1
**Test Name:** Associate login redirects to associate portal
**Preconditions:** Associate user created in Settings with email kate@test.com
**Steps:**
1. Navigate to /login
2. Enter email: kate@test.com
3. Enter password: TestAssoc2026!
4. Click Sign In
**Expected Result:** Redirected to /associate-portal. NOT redirected to /dashboard. Cannot see companies, patients list or finance menus.
**Pass/Fail:** ___

---

### TC-004
**Module:** Authentication | **Type:** Security | **Priority:** P1
**Test Name:** Invalid credentials rejected
**Steps:**
1. Navigate to /login
2. Enter email: samy@vestibulartherapyassociates.co.uk
3. Enter password: WrongPassword123
4. Click Sign In
**Expected Result:** Error message shown: "These credentials do not match our records." User remains on login page. No redirect.
**Pass/Fail:** ___

---

### TC-005
**Module:** Authentication | **Type:** Security | **Priority:** P1
**Test Name:** Rate limiting on login attempts
**Steps:**
1. Navigate to /login
2. Attempt login with wrong password 6 times in succession
**Expected Result:** After 5 failed attempts, user is rate limited. Message shown: "Too many login attempts. Please try again in X minutes."
**Pass/Fail:** ___

---

### TC-006
**Module:** Authentication | **Type:** Security | **Priority:** P1
**Test Name:** Unauthenticated user cannot access protected routes
**Steps:**
1. Clear all cookies and session data
2. Navigate directly to /patients
**Expected Result:** Redirected to /login page. Patient data not visible.
**Pass/Fail:** ___

---

### TC-007
**Module:** Authentication | **Type:** Security | **Priority:** P1
**Test Name:** Staff cannot access admin-only routes
**Preconditions:** Logged in as Sheeba (staff)
**Steps:**
1. Navigate directly to /finance/reports
2. Navigate directly to /settings/users
3. Navigate directly to /vta-invoices
**Expected Result:** Each route returns HTTP 403 Forbidden. Error page shown: "You do not have permission to access this area."
**Pass/Fail:** ___

---

### TC-008
**Module:** Authentication | **Type:** Security | **Priority:** P1
**Test Name:** Associate cannot access patient management routes
**Preconditions:** Logged in as Kate (associate)
**Steps:**
1. Navigate directly to /patients
2. Navigate directly to /companies
3. Navigate directly to /email-intake
**Expected Result:** Each route returns HTTP 403 Forbidden.
**Pass/Fail:** ___

---

### TC-009
**Module:** Authentication | **Type:** Functional | **Priority:** P2
**Test Name:** Logout works correctly
**Preconditions:** Logged in as any user
**Steps:**
1. Click Logout in sidebar
2. Attempt to navigate to /dashboard
**Expected Result:** After logout, redirected to /login. Cannot access /dashboard without logging in again.
**Pass/Fail:** ___

---

### TC-010
**Module:** Authentication | **Type:** Functional | **Priority:** P2
**Test Name:** Login page has no Register button
**Steps:**
1. Navigate to /login
2. Inspect page for any register/sign-up link or button
**Expected Result:** No registration link or button exists. Invitation-only message is displayed.
**Pass/Fail:** ___

---

## MODULE 2 — DASHBOARD

### TC-011
**Module:** Dashboard | **Type:** Functional | **Priority:** P1
**Test Name:** Dashboard KPI cards load correctly
**Preconditions:** Logged in as admin. At least one patient record exists with needs_review = TRUE.
**Steps:**
1. Navigate to /dashboard
2. Observe the four KPI cards
**Expected Result:** Four cards visible: Total Active Cases, Needs Review Today, Awaiting Funding Approval, Overdue Associate Invoices. All show numeric values (not blank or error).
**Pass/Fail:** ___

---

### TC-012
**Module:** Dashboard | **Type:** Functional | **Priority:** P1
**Test Name:** Daily Actions table shows only needs_review patients
**Preconditions:** Two patients exist — one with needs_review = TRUE, one with needs_review = FALSE
**Steps:**
1. Navigate to /dashboard
2. Observe Daily Actions table
**Expected Result:** Only the patient with needs_review = TRUE appears in the table. The other patient is not shown.
**Pass/Fail:** ___

---

### TC-013
**Module:** Dashboard | **Type:** Functional | **Priority:** P1
**Test Name:** Mark as Reviewed removes patient from Daily Actions
**Preconditions:** At least one patient in Daily Actions table
**Steps:**
1. Navigate to /dashboard
2. Click "Mark as Reviewed" on a patient row
**Expected Result:** Patient disappears from Daily Actions table immediately. Patient still exists in system (visible in /patients with "Show All").
**Pass/Fail:** ___

---

### TC-014
**Module:** Dashboard | **Type:** Functional | **Priority:** P2
**Test Name:** Unprocessed email badge shows correct count
**Preconditions:** 3 email_intake_logs exist with processed = FALSE
**Steps:**
1. Navigate to /dashboard
2. Check the Email Intake section
**Expected Result:** "3 unprocessed emails" shown. Recent 5 emails listed.
**Pass/Fail:** ___

---

### TC-015
**Module:** Dashboard | **Type:** Security | **Priority:** P2
**Test Name:** Overdue Associate Invoices card hidden from staff
**Preconditions:** Logged in as Sheeba (staff)
**Steps:**
1. Navigate to /dashboard
**Expected Result:** "Overdue Associate Invoices" KPI card is NOT visible. Only 3 KPI cards shown (not 4).
**Pass/Fail:** ___

---

## MODULE 3 — ENQUIRIES

### TC-016
**Module:** Enquiries | **Type:** Functional | **Priority:** P1
**Test Name:** Log new enquiry — all fields save correctly
**Preconditions:** Logged in as admin or staff
**Steps:**
1. Navigate to /enquiries/create
2. Fill in: Enquirer Name: "Emma Bracher", Company: "Community Case Management Ltd", Email: "emma@ccm.co.uk", Source: "Email", Reason: "Possible referral for vestibular patient", Enquiry Date: today
3. Click Save
**Expected Result:** Redirected to enquiry detail page. All fields display correctly. Status shows "New". Record appears in enquiries list.
**Pass/Fail:** ___

---

### TC-017
**Module:** Enquiries | **Type:** Functional | **Priority:** P1
**Test Name:** Required fields validated on enquiry form
**Steps:**
1. Navigate to /enquiries/create
2. Leave Enquirer Name blank
3. Leave Source unselected
4. Click Save
**Expected Result:** Form does not submit. Validation errors shown below Enquirer Name field and Source field. User remains on form.
**Pass/Fail:** ___

---

### TC-018
**Module:** Enquiries | **Type:** Functional | **Priority:** P1
**Test Name:** Convert enquiry to Company + Case Manager record
**Preconditions:** Enquiry TC-016 exists with status "New"
**Steps:**
1. Navigate to the enquiry detail page
2. Click "Convert to Full Record"
3. In the panel: confirm company name "Community Case Management Ltd", type "Case Management"
4. Fill case manager: First Name "Emma", Last Name "Bracher", Email "emma@ccm.co.uk", Job Title "Case Manager"
5. Click Confirm
**Expected Result:** Company record created. Case Manager record created under that company. Enquiry status changes to "Converted". Enquiry shows links to new company and case manager records.
**Pass/Fail:** ___

---

### TC-019
**Module:** Enquiries | **Type:** Functional | **Priority:** P2
**Test Name:** Enquiry source filter works
**Preconditions:** Multiple enquiries with different sources exist
**Steps:**
1. Navigate to /enquiries
2. Filter by Source: "LinkedIn"
**Expected Result:** Only enquiries with source = LinkedIn shown. Other enquiries hidden.
**Pass/Fail:** ___

---

### TC-020
**Module:** Enquiries | **Type:** Functional | **Priority:** P2
**Test Name:** Mark enquiry as Not Proceeding
**Preconditions:** An enquiry exists with status "New"
**Steps:**
1. Navigate to enquiry detail
2. Click "Mark Not Proceeding"
**Expected Result:** Enquiry status changes to "Not Proceeding". Grey badge shown.
**Pass/Fail:** ___

---

## MODULE 4 — COMPANIES

### TC-021
**Module:** Companies | **Type:** Functional | **Priority:** P1
**Test Name:** Create company with all fields
**Preconditions:** Logged in as admin
**Steps:**
1. Navigate to /companies/create
2. Fill in: Name "Harrison Associates", Type "Case Management", City "London", Postcode "EC1A 1BB", Phone "020 7000 0000", Email "info@harrison.co.uk"
3. Click Save
**Expected Result:** Company created. Redirected to company detail page. All fields display correctly. Status shows "Enquiry".
**Pass/Fail:** ___

---

### TC-022
**Module:** Companies | **Type:** Functional | **Priority:** P1
**Test Name:** Company search works
**Preconditions:** Multiple companies exist
**Steps:**
1. Navigate to /companies
2. Type "Harrison" in search box
**Expected Result:** Only Harrison Associates shown. Other companies filtered out. Results update without page reload.
**Pass/Fail:** ___

---

### TC-023
**Module:** Companies | **Type:** Functional | **Priority:** P2
**Test Name:** Company status filter works
**Preconditions:** Companies exist with different statuses
**Steps:**
1. Navigate to /companies
2. Filter by Status: "Active"
**Expected Result:** Only Active companies shown.
**Pass/Fail:** ___

---

### TC-024
**Module:** Companies | **Type:** Functional | **Priority:** P2
**Test Name:** Company detail shows all case managers
**Preconditions:** Community Case Management Ltd exists with 2 case managers
**Steps:**
1. Navigate to company detail page for Community Case Management Ltd
**Expected Result:** Both case managers listed in Case Managers section. Patient counts correct.
**Pass/Fail:** ___

---

## MODULE 5 — CASE MANAGERS

### TC-025
**Module:** Case Managers | **Type:** Functional | **Priority:** P1
**Test Name:** Create case manager under company
**Preconditions:** Community Case Management Ltd company exists
**Steps:**
1. Navigate to company detail for Community Case Management Ltd
2. Click "Add Case Manager"
3. Fill in: First Name "Emma", Last Name "Bracher", Email "emma@ccm.co.uk", Phone "07700 000000", Job Title "Senior Case Manager"
4. Click Save
**Expected Result:** Case manager created. Appears in company's case manager list. NDA shows as "Not Signed". Materials shows as "Not Sent".
**Pass/Fail:** ___

---

### TC-026
**Module:** Case Managers | **Type:** Functional | **Priority:** P1
**Test Name:** Mark NDA as signed
**Preconditions:** Emma Bracher case manager exists with nda_signed = FALSE
**Steps:**
1. Navigate to Emma Bracher's case manager detail page
2. Click "Mark as Signed" in the NDA section
**Expected Result:** nda_signed_date set to today. NDA section shows "Signed on [today's date]". Document upload prompt appears.
**Pass/Fail:** ___

---

### TC-027
**Module:** Case Managers | **Type:** Functional | **Priority:** P1
**Test Name:** Mark materials as sent
**Preconditions:** Emma Bracher case manager exists
**Steps:**
1. Navigate to Emma Bracher's case manager detail page
2. Click "Mark as Sent" in the Materials section
**Expected Result:** materials_sent_date set to today. Materials section shows "Sent on [today's date]".
**Pass/Fail:** ___

---

### TC-028
**Module:** Case Managers | **Type:** Functional | **Priority:** P1
**Test Name:** Add communication to case manager
**Preconditions:** Emma Bracher case manager exists
**Steps:**
1. Navigate to Emma Bracher's case manager detail page
2. Click "Add Communication"
3. Fill in: Type "Email", Direction "Inbound", Date/Time: now, Subject "Enquiry about vestibular patient", Summary "Emma enquired about a patient in Rugby with TBI and dizziness"
4. Click Save
**Expected Result:** Communication appears in timeline. Shows correct type icon, direction, date and summary.
**Pass/Fail:** ___

---

### TC-029
**Module:** Case Managers | **Type:** Functional | **Priority:** P2
**Test Name:** Communication follow-up date tracking
**Preconditions:** Communication added with follow-up date set to tomorrow
**Steps:**
1. View case manager detail
2. Check communication log
**Expected Result:** Follow-up date shown. Follow-up not marked as completed.
**Pass/Fail:** ___

---

## MODULE 6 — PATIENTS

### TC-030
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Create new patient under case manager
**Preconditions:** Emma Bracher case manager exists
**Steps:**
1. Navigate to Emma Bracher's case manager detail
2. Click "Add New Patient"
3. Fill in: First Name "David", Last Name "Clarke", Date of Birth "1975-03-15", Location "Rugby", Condition "TBI with vestibular disorder — dizziness and balance issues", Referral Date: today, Invoice Recipient Type "Case Manager Company", Invoice Recipient Name "Community Case Management Ltd"
4. Click Save
**Expected Result:** Patient David Clarke created. Status shows "Enquiry Logged". needs_review = TRUE. Patient appears in case manager's patient list and in /patients list.
**Pass/Fail:** ___

---

### TC-031
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Patient status change — valid transition
**Preconditions:** David Clarke exists with status "Enquiry Logged"
**Steps:**
1. Navigate to David Clarke's patient detail
2. Change status to "Response Sent"
3. Click Update Status
**Expected Result:** Status updates to "Response Sent". Status history log shows: "Changed from Enquiry Logged to Response Sent by [user] at [time]".
**Pass/Fail:** ___

---

### TC-032
**Module:** Patients | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-C1 — Cannot move to Funding Approved without funding cycle document
**Preconditions:** David Clarke exists with status "Awaiting Funding Approval". No funding cycle record exists.
**Steps:**
1. Navigate to David Clarke's patient detail
2. Attempt to change status to "Funding Approved"
3. Click Update Status
**Expected Result:** Status update BLOCKED. Error message shown: "Cannot approve funding: no funding approval document uploaded." Status remains "Awaiting Funding Approval".
**Pass/Fail:** ___

---

### TC-033
**Module:** Patients | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-C1 — Can move to Funding Approved WITH funding cycle document
**Preconditions:** David Clarke at "Awaiting Funding Approval". Funding cycle exists WITH document uploaded.
**Steps:**
1. Navigate to David Clarke's patient detail
2. Change status to "Funding Approved"
3. Click Update Status
**Expected Result:** Status updates to "Funding Approved" successfully. No error shown.
**Pass/Fail:** ___

---

### TC-034
**Module:** Patients | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-C2 — Cannot move to Treatment Active from wrong status
**Preconditions:** David Clarke exists with status "Report Sent" (not Funding Approved)
**Steps:**
1. Navigate to David Clarke's patient detail
2. Attempt to change status to "Treatment Active"
**Expected Result:** "Treatment Active" not available in status dropdown OR error shown if attempted via direct route. Status remains "Report Sent".
**Pass/Fail:** ___

---

### TC-035
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Patient transfer to new case manager
**Preconditions:** David Clarke assigned to Emma Bracher. A second case manager "Mark Tucker" exists at a different company.
**Steps:**
1. Navigate to David Clarke's patient detail
2. Click "Transfer to Different Case Manager"
3. Search for and select Mark Tucker
4. Enter reason: "Emma Bracher left the company"
5. Click Confirm Transfer
**Expected Result:** Patient's case_manager_id updated to Mark Tucker. patient_case_manager_history record created showing previous CM, new CM, date and reason. History visible on patient detail page.
**Pass/Fail:** ___

---

### TC-036
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Assign associate to patient
**Preconditions:** David Clarke exists. Kate Bryce is an active associate.
**Steps:**
1. Navigate to David Clarke's patient detail
2. In the Associates card click "Add Associate"
3. Select Kate Bryce, Role: Assessment, Start Date: today
4. Click Save
**Expected Result:** Kate Bryce appears in patient's associates list with role "Assessment" and start date shown. is_primary can be toggled.
**Pass/Fail:** ___

---

### TC-037
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Add second associate with different role
**Preconditions:** David Clarke has Kate Bryce as Assessment associate
**Steps:**
1. Navigate to David Clarke's patient detail
2. Click "Add Associate"
3. Select Samy Selvanayagam, Role: Supervision, Start Date: today
4. Click Save
**Expected Result:** Both associates shown in patient's associates list — Kate (Assessment) and Samy (Supervision). No error.
**Pass/Fail:** ___

---

### TC-038
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** End associate assignment
**Preconditions:** Kate Bryce assigned to David Clarke
**Steps:**
1. Navigate to David Clarke's patient detail
2. Click "End Assignment" next to Kate Bryce
**Expected Result:** end_date set to today on patient_associates record. Kate Bryce shown as inactive for this patient.
**Pass/Fail:** ___

---

### TC-039
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Patient list Daily Actions default view
**Preconditions:** 3 patients exist — 2 with needs_review TRUE, 1 with needs_review FALSE
**Steps:**
1. Navigate to /patients
**Expected Result:** Only 2 patients shown (those with needs_review = TRUE). The third patient not shown. "Showing X of Y patients" counter reflects filter.
**Pass/Fail:** ___

---

### TC-040
**Module:** Patients | **Type:** Functional | **Priority:** P1
**Test Name:** Patient list Show All toggle
**Preconditions:** Mix of needs_review TRUE and FALSE patients
**Steps:**
1. Navigate to /patients
2. Click "Show All" toggle
**Expected Result:** All patients now shown regardless of needs_review value.
**Pass/Fail:** ___

---

### TC-041
**Module:** Patients | **Type:** Functional | **Priority:** P2
**Test Name:** Patient list status filter
**Preconditions:** Patients with different statuses exist
**Steps:**
1. Navigate to /patients
2. Check "Treatment Active" in status filter
**Expected Result:** Only Treatment Active patients shown.
**Pass/Fail:** ___

---

### TC-042
**Module:** Patients | **Type:** Functional | **Priority:** P2
**Test Name:** Patient notes auto-save
**Preconditions:** David Clarke patient detail open
**Steps:**
1. Navigate to David Clarke's patient detail
2. Click in the Notes text area
3. Type "Patient contacted on 16/06/2026 — confirmed assessment date"
4. Click outside the notes area
**Expected Result:** Notes saved without clicking a separate Save button. On page refresh, notes still show.
**Pass/Fail:** ___

---

## MODULE 7 — DOCUMENTS

### TC-043
**Module:** Documents | **Type:** Functional | **Priority:** P1
**Test Name:** Upload document to patient record
**Preconditions:** David Clarke patient exists. Logged in as admin.
**Steps:**
1. Navigate to David Clarke's patient detail
2. In Documents card click "Upload"
3. Select Document Type: "Letter of Instruction"
4. Upload a test PDF file (under 20MB)
5. Toggle "Also link to Case Manager" to Yes
6. Click Save
**Expected Result:** Document appears in patient's document list. Document also appears in Emma Bracher's case manager document list. File stored on server. Document type badge shown.
**Pass/Fail:** ___

---

### TC-044
**Module:** Documents | **Type:** Functional | **Priority:** P1
**Test Name:** Download document via authenticated route
**Preconditions:** Document uploaded to David Clarke
**Steps:**
1. Navigate to David Clarke's patient detail
2. Click Download next to the LOI document
**Expected Result:** File downloads successfully. File is the same file that was uploaded. Download does not expose direct server file path in URL.
**Pass/Fail:** ___

---

### TC-045
**Module:** Documents | **Type:** Security | **Priority:** P1
**Test Name:** Direct file path access blocked
**Preconditions:** Document uploaded — note the stored file name
**Steps:**
1. Attempt to access the file directly via URL:
   https://portal.vestibulartherapyassociates.co.uk/storage/vta-documents/{path}/{filename}
**Expected Result:** HTTP 404 or 403 returned. File NOT served directly. Must go through /documents/{id}/download route.
**Pass/Fail:** ___

---

### TC-046
**Module:** Documents | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-D4 — File size limit enforced
**Steps:**
1. Navigate to document upload
2. Attempt to upload a file larger than 20MB
**Expected Result:** Upload rejected. Error message: "File size must not exceed 20MB."
**Pass/Fail:** ___

---

### TC-047
**Module:** Documents | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-D4 — Invalid file type rejected
**Steps:**
1. Navigate to document upload
2. Attempt to upload a .exe file
**Expected Result:** Upload rejected. Error message about allowed file types shown.
**Pass/Fail:** ___

---

### TC-048
**Module:** Documents | **Type:** Functional | **Priority:** P1
**Test Name:** Password-protected document flag
**Steps:**
1. Navigate to document upload
2. Upload a PDF
3. Toggle "Is Password Protected" to Yes
4. Enter password: "VTA2026Secure"
5. Set Shared Date: today, Shared Via: "Email"
6. Click Save
**Expected Result:** Document shows password protection badge. Password stored encrypted in database. Password visible only to admin on document detail.
**Pass/Fail:** ___

---

### TC-049
**Module:** Documents | **Type:** Security | **Priority:** P1
**Test Name:** Associate document permission enforcement
**Preconditions:** Associate Kate logged in. Document type "Associate Invoice" has can_view = FALSE for associate role in document_type_permissions.
**Steps:**
1. Logged in as Kate (associate)
2. Navigate to /associate-portal/patients/{david-clarke-id}
3. Attempt to download an Associate Invoice document
**Expected Result:** Associate Invoice not shown in document list OR download returns 403. Access denied.
**Pass/Fail:** ___

---

### TC-050
**Module:** Documents | **Type:** Functional | **Priority:** P2
**Test Name:** Delete document — admin only
**Preconditions:** Document exists. Logged in as admin.
**Steps:**
1. Navigate to document list for patient
2. Click Delete on a document
3. Confirm deletion
**Expected Result:** Document removed from list. File deleted from server storage. Record removed from database.
**Pass/Fail:** ___

---

### TC-051
**Module:** Documents | **Type:** Security | **Priority:** P2
**Test Name:** Staff cannot delete documents
**Preconditions:** Logged in as Sheeba (staff)
**Steps:**
1. Navigate to patient document list
2. Observe document actions available
**Expected Result:** Delete button NOT visible for staff role. Only Download action available.
**Pass/Fail:** ___

---

## MODULE 8 — COST ESTIMATIONS

### TC-052
**Module:** Cost Estimations | **Type:** Functional | **Priority:** P1
**Test Name:** Create initial cost estimation (version 1)
**Preconditions:** David Clarke patient exists
**Steps:**
1. Navigate to David Clarke's patient detail
2. In Cost Estimations card click "Add Cost Estimation"
3. Fill in: Version 1, Title "Initial Assessment Quote", Amount £850.00, Sessions 1, Duration "Single assessment", Sent Date today, Sent To "Emma Bracher"
4. Click Save
**Expected Result:** Cost estimation appears in patient's cost estimation list. Version number shows 1. Amount shows £850.00.
**Pass/Fail:** ___

---

### TC-053
**Module:** Cost Estimations | **Type:** Functional | **Priority:** P1
**Test Name:** Create second cost estimation (version 2 — post assessment)
**Preconditions:** Version 1 cost estimation exists for David Clarke
**Steps:**
1. Add second cost estimation for David Clarke
2. Fill in: Version 2, Title "Phase 1 Treatment — 6 months", Amount £4,500.00, Sessions 12, Duration "6 months"
**Expected Result:** Second cost estimation added. Version shows 2. Both versions visible in list.
**Pass/Fail:** ___

---

## MODULE 9 — FUNDING CYCLES

### TC-054
**Module:** Funding Cycles | **Type:** Functional | **Priority:** P1
**Test Name:** Create funding cycle with approval document
**Preconditions:** David Clarke exists at status "Awaiting Funding Approval". Cost estimation v2 exists. Logged in as admin.
**Steps:**
1. Navigate to David Clarke's patient detail
2. In Funding Cycles section click "Add Funding Cycle"
3. Fill in: Cycle 1, link to Cost Estimation v2, Approved Amount £4,500.00, Approved Sessions 12, Approval Date today, Funder "Community Case Management Ltd"
4. Upload a test PDF as the funding approval document
5. Click Save
**Expected Result:** Funding cycle created. Remaining Balance shows £4,500.00. Progress bar shows 0% spent. Document linked.
**Pass/Fail:** ___

---

### TC-055
**Module:** Funding Cycles | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F2 — Remaining balance calculates correctly after invoice
**Preconditions:** Funding cycle with £4,500.00. One paid VTA invoice of £850.00 linked to this cycle.
**Steps:**
1. Navigate to David Clarke's funding cycle
2. Observe remaining balance
**Expected Result:** Remaining balance shows £3,650.00 (£4,500 - £850). Progress bar shows ~19% spent.
**Pass/Fail:** ___

---

### TC-056
**Module:** Funding Cycles | **Type:** Functional | **Priority:** P2
**Test Name:** Create second funding cycle (Phase 2)
**Preconditions:** First funding cycle fully invoiced. New cost estimation created.
**Steps:**
1. Add new funding cycle for David Clarke
2. Cycle Number: 2, Amount £3,200.00, Sessions 8
**Expected Result:** Second cycle created. First cycle remains visible. Patient can progress to "Awaiting Further Funding" then back to "Funding Approved".
**Pass/Fail:** ___

---

## MODULE 10 — EMAIL INTAKE

### TC-057
**Module:** Email Intake | **Type:** Functional | **Priority:** P1
**Test Name:** Email intake fetches new emails
**Preconditions:** Test email sent to operations@vestibulartherapyassociates.co.uk with subject "Test Referral" and a PDF attached
**Steps:**
1. Navigate to /email-intake
2. Click "Check for New Emails"
3. Wait for process to complete
**Expected Result:** New email appears in the list with correct From address, Subject, Received time and attachment indicator. processed = FALSE.
**Pass/Fail:** ___

---

### TC-058
**Module:** Email Intake | **Type:** Functional | **Priority:** P1
**Test Name:** Link email to existing patient
**Preconditions:** Email from TC-057 exists in intake log. David Clarke patient exists.
**Steps:**
1. Navigate to /email-intake
2. Click "Link to Patient" on the test email
3. Search for David Clarke
4. Click Confirm
**Expected Result:** Email marked as processed. linked_patient_id set. Action shows "Linked to Patient". Email moves to processed section.
**Pass/Fail:** ___

---

### TC-059
**Module:** Email Intake | **Type:** Functional | **Priority:** P1
**Test Name:** Create new patient from email
**Preconditions:** New email exists in intake log
**Steps:**
1. Navigate to /email-intake
2. Click "Create New Patient" on an email
**Expected Result:** Redirected to /patients/create with email sender details pre-filled where possible (name from From field, notes from subject). User completes remaining fields and saves.
**Pass/Fail:** ___

---

### TC-060
**Module:** Email Intake | **Type:** Functional | **Priority:** P1
**Test Name:** Mark email as irrelevant
**Preconditions:** Email exists in intake log (e.g. spam or internal email)
**Steps:**
1. Navigate to /email-intake
2. Click "Mark as Irrelevant" on an email
**Expected Result:** Email marked as processed. Action shows "Marked Irrelevant". Disappears from unprocessed view.
**Pass/Fail:** ___

---

### TC-061
**Module:** Email Intake | **Type:** Functional | **Priority:** P2
**Test Name:** Email attachment saved to inbox folder
**Preconditions:** Email with PDF attachment arrives
**Steps:**
1. Check email intake after test email with attachment arrives
**Expected Result:** Attachment saved to storage/app/vta-documents/inbox/. File path stored in email_intake_logs.attachment_paths as JSON.
**Pass/Fail:** ___

---

## MODULE 11 — APPOINTMENTS (PHASE 2)

### TC-062
**Module:** Appointments | **Type:** Functional | **Priority:** P1
**Test Name:** Create appointment for patient
**Preconditions:** David Clarke exists. Kate Bryce assigned as associate. Activity types seeded. Logged in as admin.
**Steps:**
1. Navigate to /appointments/create
2. Select Patient: David Clarke
3. Select Associate: Kate Bryce (should appear first as assigned associate)
4. Select Activity Type: Initial Assessment
5. Set Date/Time: next Monday at 10:00am
6. Set Duration: 90 minutes
7. Set Location: Patient Home
8. Set Travel Miles: 24.5
9. Click Save
**Expected Result:** Appointment created. Appears on team calendar on correct date. Shows patient name, associate name and activity type.
**Pass/Fail:** ___

---

### TC-063
**Module:** Appointments | **Type:** Functional | **Priority:** P1
**Test Name:** Team calendar shows all associates' appointments
**Preconditions:** Appointments exist for Kate Bryce and Samy Selvanayagam on same week
**Steps:**
1. Navigate to /appointments/calendar
2. View week view
**Expected Result:** Both appointments visible on calendar. Colour-coded by associate. Both names visible on calendar events.
**Pass/Fail:** ___

---

### TC-064
**Module:** Appointments | **Type:** Security | **Priority:** P1
**Test Name:** Associate can only see own appointments on calendar
**Preconditions:** Logged in as Kate (associate). Appointments exist for Kate and for Samy.
**Steps:**
1. Navigate to /associate-portal/calendar
**Expected Result:** Only Kate's appointments visible. Samy's appointments NOT shown.
**Pass/Fail:** ___

---

### TC-065
**Module:** Appointments | **Type:** Functional | **Priority:** P1
**Test Name:** Mark appointment as Completed
**Preconditions:** Appointment exists with status Scheduled
**Steps:**
1. Navigate to appointment detail
2. Change status to Completed
3. Click Save
**Expected Result:** Status updates to Completed. Appointment shows as completed on calendar (different visual style).
**Pass/Fail:** ___

---

### TC-066
**Module:** Appointments | **Type:** Functional | **Priority:** P2
**Test Name:** Calendar filter by associate
**Preconditions:** Appointments for multiple associates exist
**Steps:**
1. Navigate to /appointments/calendar
2. Filter by Associate: Kate Bryce
**Expected Result:** Only Kate Bryce's appointments shown on calendar. Other associates' appointments hidden.
**Pass/Fail:** ___

---

## MODULE 12 — CASE NOTES (PHASE 2)

### TC-067
**Module:** Case Notes | **Type:** Functional | **Priority:** P1
**Test Name:** Upload case note for patient
**Preconditions:** David Clarke exists. Kate Bryce assigned. Appointment exists and completed.
**Steps:**
1. Navigate to David Clarke's patient detail
2. In Case Notes section click "Add Case Note"
3. Select Associate: Kate Bryce
4. Link to Appointment: select the completed appointment
5. Session Date: today
6. Note Type: Session Note
7. Content: "Patient presented with balance issues. VOR exercises commenced."
8. Upload PDF of clinical notes
9. Click Save
**Expected Result:** Case note created. Appears in patient's case notes list. Shows associate name, date, type and document link.
**Pass/Fail:** ___

---

### TC-068
**Module:** Case Notes | **Type:** Security | **Priority:** P1
**Test Name:** Associate can only upload notes for own patients
**Preconditions:** Logged in as Kate (associate). David Clarke assigned to Kate. A second patient NOT assigned to Kate exists.
**Steps:**
1. Navigate to /associate-portal/patients/{second-patient-id}
**Expected Result:** HTTP 403 returned. Kate cannot see or add notes to unassigned patient.
**Pass/Fail:** ___

---

### TC-069
**Module:** Case Notes | **Type:** Functional | **Priority:** P1
**Test Name:** Admin signs off case note
**Preconditions:** Case note exists with is_signed_off = FALSE. Logged in as admin.
**Steps:**
1. Navigate to David Clarke's case notes
2. Click "Sign Off" on the case note
**Expected Result:** is_signed_off = TRUE. signed_off_by = admin user. signed_off_at = current timestamp. "Signed off" badge shown on note.
**Pass/Fail:** ___

---

### TC-070
**Module:** Case Notes | **Type:** Security | **Priority:** P2
**Test Name:** Staff cannot sign off case notes
**Preconditions:** Logged in as Sheeba (staff)
**Steps:**
1. Navigate to patient case notes
2. Observe available actions
**Expected Result:** "Sign Off" button NOT visible for staff role. Only admin can sign off.
**Pass/Fail:** ___

---

## MODULE 13 — ASSOCIATE PORTAL (PHASE 2)

### TC-071
**Module:** Associate Portal | **Type:** Functional | **Priority:** P1
**Test Name:** Associate sees only own patients
**Preconditions:** Logged in as Kate. David Clarke assigned to Kate. A second patient NOT assigned to Kate.
**Steps:**
1. Navigate to /associate-portal
**Expected Result:** Dashboard shows only David Clarke (or patients assigned to Kate). Second patient NOT visible anywhere in associate portal.
**Pass/Fail:** ___

---

### TC-072
**Module:** Associate Portal | **Type:** Functional | **Priority:** P1
**Test Name:** Associate can view permitted document types
**Preconditions:** LOI document uploaded for David Clarke. document_type_permissions has LOI visible to associate = TRUE.
**Steps:**
1. Logged in as Kate
2. Navigate to /associate-portal/patients/{david-clarke-id}
3. View documents section
**Expected Result:** LOI document visible and downloadable by Kate.
**Pass/Fail:** ___

---

### TC-073
**Module:** Associate Portal | **Type:** Security | **Priority:** P1
**Test Name:** Associate cannot view internal-only documents
**Preconditions:** Associate Invoice document exists. document_type_permissions has Associate Invoice visible to associate = FALSE.
**Steps:**
1. Logged in as Kate
2. Navigate to /associate-portal/patients/{david-clarke-id}
3. View documents section
**Expected Result:** Associate Invoice NOT shown in Kate's view of this patient's documents.
**Pass/Fail:** ___

---

## MODULE 14 — FINANCE (PHASE 3)

### TC-074
**Module:** Finance | **Type:** Functional | **Priority:** P1
**Test Name:** Log associate invoice
**Preconditions:** David Clarke in Treatment Active. Kate Bryce assigned. Funding cycle exists. Logged in as admin.
**Steps:**
1. Navigate to /associate-invoices/create
2. Select Associate: Kate Bryce
3. Select Patient: David Clarke
4. Select Funding Cycle: Cycle 1
5. Fill: Invoice Reference "KB-2026-001", Invoice Date today, Sessions Completed 3, Travel Miles 24.5
6. Verify session amount auto-calculated (sessions × Kate's session rate)
7. Verify travel amount auto-calculated (miles × Kate's travel rate)
8. Click Save
**Expected Result:** Associate invoice created. due_date = invoice_date + 28 days (auto-set). Status = Received. Appears in associate invoices list.
**Pass/Fail:** ___

---

### TC-075
**Module:** Finance | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F3 — Due date auto-set to +28 days
**Preconditions:** Associate invoice created with invoice_date = today
**Steps:**
1. View the associate invoice just created
2. Check due_date field
**Expected Result:** due_date = today + 28 days exactly.
**Pass/Fail:** ___

---

### TC-076
**Module:** Finance | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F4 — Overdue invoice alert on dashboard
**Preconditions:** Associate invoice exists with due_date = yesterday and status = Received
**Steps:**
1. Navigate to /dashboard
2. Check overdue alerts section
**Expected Result:** Overdue invoice appears in dashboard alert. Shows associate name, patient, amount and due date.
**Pass/Fail:** ___

---

### TC-077
**Module:** Finance | **Type:** Functional | **Priority:** P1
**Test Name:** Create VTA invoice — auto-generate invoice number
**Preconditions:** David Clarke exists. Funding cycle exists. Logged in as admin.
**Steps:**
1. Navigate to /vta-invoices/create
2. Select Patient: David Clarke
3. Select Funding Cycle: Cycle 1 (shows remaining balance)
4. Fill: Invoice Date today, Recipient Type "Case Manager Company", Recipient Name "Community Case Management Ltd", Sessions Invoiced 3, Session Amount £2,550.00, Total Amount £2,550.00
5. Click Save
**Expected Result:** Invoice created. Invoice number auto-generated as VTA-2026-0001 (or next in sequence). Status = Draft.
**Pass/Fail:** ___

---

### TC-078
**Module:** Finance | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F5 — Invoice number sequential and correctly formatted
**Preconditions:** VTA-2026-0001 already exists
**Steps:**
1. Create a second VTA invoice
**Expected Result:** Second invoice gets number VTA-2026-0002. Correct year prefix. Zero-padded to 4 digits.
**Pass/Fail:** ___

---

### TC-079
**Module:** Finance | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F1 — Warning when invoice exceeds funding cycle balance
**Preconditions:** Funding cycle has remaining balance of £1,000.00. Attempting to create invoice for £1,200.00.
**Steps:**
1. Navigate to /vta-invoices/create
2. Select Patient and Funding Cycle (shows £1,000 remaining)
3. Enter Total Amount: £1,200.00
4. Attempt to save
**Expected Result:** Amber warning shown: "This invoice (£1,200.00) exceeds the remaining funding balance (£1,000.00). Please add a note to explain." Mandatory notes field appears. Invoice CAN be saved after note added (not blocked — BR-F1 says warn, not block).
**Pass/Fail:** ___

---

### TC-080
**Module:** Finance | **Type:** Business Rule | **Priority:** P1
**Test Name:** BR-F6 — Cannot mark invoice as Sent without document
**Preconditions:** VTA invoice exists in Draft status with no document uploaded
**Steps:**
1. Navigate to VTA invoice detail
2. Attempt to change status from Draft to Sent
**Expected Result:** Status change blocked. Error: "Please upload the invoice document before marking as Sent."
**Pass/Fail:** ___

---

### TC-081
**Module:** Finance | **Type:** Functional | **Priority:** P1
**Test Name:** Mark VTA invoice as Sent after document uploaded
**Preconditions:** VTA invoice in Draft status
**Steps:**
1. Upload invoice PDF to the VTA invoice record
2. Change status to Sent
**Expected Result:** Status updates to Sent successfully.
**Pass/Fail:** ___

---

### TC-082
**Module:** Finance | **Type:** Functional | **Priority:** P2
**Test Name:** Finance reports show correct revenue figures
**Preconditions:** Multiple VTA invoices exist with different statuses and dates
**Steps:**
1. Navigate to /finance/reports
2. View Revenue Summary report
**Expected Result:** Total invoiced, total paid and outstanding figures are mathematically correct against database records.
**Pass/Fail:** ___

---

### TC-083
**Module:** Finance | **Type:** Security | **Priority:** P1
**Test Name:** Staff cannot access finance module
**Preconditions:** Logged in as Sheeba (staff)
**Steps:**
1. Navigate directly to /associate-invoices
2. Navigate directly to /vta-invoices
3. Navigate directly to /finance/reports
**Expected Result:** All three routes return HTTP 403. Finance menu not visible in Sheeba's sidebar.
**Pass/Fail:** ___

---

## MODULE 15 — SETTINGS (DYNAMIC LOOKUPS)

### TC-084
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Add new activity type
**Preconditions:** Logged in as admin
**Steps:**
1. Navigate to /settings/activity-types
2. Click "Add Activity Type"
3. Name: "Video Consultation", Description: "Remote video assessment or treatment session"
4. Click Save
**Expected Result:** New activity type appears in list. Immediately available in appointment form activity type dropdown.
**Pass/Fail:** ___

---

### TC-085
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Deactivate activity type — disappears from dropdowns
**Preconditions:** "Video Consultation" activity type exists and is active
**Steps:**
1. Navigate to /settings/activity-types
2. Click Deactivate next to "Video Consultation"
**Expected Result:** Activity type marked inactive. Disappears from appointment form dropdown. Historical appointments using this type are NOT affected — they still show "Video Consultation".
**Pass/Fail:** ___

---

### TC-086
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Add new document type
**Preconditions:** Logged in as admin
**Steps:**
1. Navigate to /settings/document-types
2. Add "Consent Form" as a new document type
**Expected Result:** New document type available immediately in document upload form.
**Pass/Fail:** ___

---

### TC-087
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Document permissions matrix — toggle associate access
**Preconditions:** Logged in as admin. "Medical Records" document type exists with associate can_view = FALSE.
**Steps:**
1. Navigate to /settings/document-permissions
2. Toggle "Medical Records" — Associate column to ON
3. Click Save All Permissions
**Expected Result:** document_type_permissions record updated: Medical Records + associate + can_view = TRUE. Associate can now see Medical Records documents on their portal.
**Pass/Fail:** ___

---

### TC-088
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Set associate session rate and travel rate
**Preconditions:** Kate Bryce exists in associates list without rates set
**Steps:**
1. Navigate to /settings/associates
2. Click Edit next to Kate Bryce
3. Set Session Rate: £95.00
4. Set Travel Rate Per Mile: £0.45
5. Click Save
**Expected Result:** Rates saved. When logging next associate invoice for Kate, session amount auto-calculates as sessions × £95.00.
**Pass/Fail:** ___

---

### TC-089
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Create portal login for associate
**Preconditions:** Kate Bryce exists in associates table without user_id
**Steps:**
1. Navigate to /settings/associates
2. Click "Create Portal Login" next to Kate Bryce
3. Set email: kate@test.com, temporary password: TestAssoc2026!
**Expected Result:** User record created with role = associate. user_id linked on associates table. Kate can now log in to /associate-portal.
**Pass/Fail:** ___

---

### TC-090
**Module:** Settings | **Type:** Functional | **Priority:** P1
**Test Name:** Create new staff user
**Preconditions:** Logged in as admin
**Steps:**
1. Navigate to /settings/users
2. Click "Add User"
3. Name: "New Staff Member", Email: newstaff@vestibulartherapyassociates.co.uk, Role: staff, Temporary Password: Temp2026!
**Expected Result:** User created. Can log in with credentials. Sees staff portal (not admin features).
**Pass/Fail:** ___

---

## MODULE 16 — UI AND RESPONSIVE DESIGN

### TC-091
**Module:** UI | **Type:** UI | **Priority:** P2
**Test Name:** Mobile responsive — patient list
**Steps:**
1. Open /patients on a mobile device (or browser DevTools at 375px width)
**Expected Result:** Table converts to card view. Each patient shown as a card with label: value pairs. No horizontal scroll required. All actions accessible.
**Pass/Fail:** ___

---

### TC-092
**Module:** UI | **Type:** UI | **Priority:** P2
**Test Name:** Mobile responsive — sidebar navigation
**Steps:**
1. Open any page on mobile (375px width)
**Expected Result:** Sidebar collapsed. Hamburger menu icon visible. Clicking hamburger opens slide-out menu. All navigation items accessible.
**Pass/Fail:** ___

---

### TC-093
**Module:** UI | **Type:** UI | **Priority:** P2
**Test Name:** Status badges display with correct colours
**Preconditions:** Patients exist at various statuses
**Steps:**
1. Navigate to /patients (Show All)
2. Observe status badges on each row
**Expected Result:** Each status displays with the correct Tailwind colour class as defined in Part 11.1. "Treatment Active" shows green. "Awaiting Funding Approval" shows red. "Discharged" shows grey. etc.
**Pass/Fail:** ___

---

### TC-094
**Module:** UI | **Type:** UI | **Priority:** P2
**Test Name:** Flash success message appears after save
**Steps:**
1. Create any new record (e.g. add communication)
2. Click Save
**Expected Result:** Green toast notification appears top-right. Message reads "Communication saved successfully" (or similar). Auto-dismisses after 4 seconds.
**Pass/Fail:** ___

---

### TC-095
**Module:** UI | **Type:** UI | **Priority:** P2
**Test Name:** Calendar mobile view switches to list view
**Steps:**
1. Open /appointments/calendar on mobile (375px)
**Expected Result:** FullCalendar displays in listWeek view (not month/week grid which is unusable on mobile). Appointments listed chronologically.
**Pass/Fail:** ___

---

## MODULE 17 — INTEGRATION TESTS

### TC-096
**Module:** Integration | **Type:** Integration | **Priority:** P1
**Test Name:** Full enquiry to treatment workflow
**Steps:**
1. Log new enquiry (TC-016)
2. Convert to company + case manager (TC-018)
3. Create patient (TC-030)
4. Progress patient through statuses to Awaiting Funding Approval (TC-031)
5. Create cost estimation (TC-052)
6. Create funding cycle with document (TC-054)
7. Progress to Funding Approved (TC-033)
8. Progress to Treatment Active (TC-032 confirm blocked without cycle, then TC-033)
9. Create appointment (TC-062)
10. Mark appointment Completed (TC-065)
11. Upload case note (TC-067)
12. Log associate invoice (TC-074)
13. Create VTA invoice (TC-077)
14. Mark VTA invoice as Sent (TC-081)
**Expected Result:** All steps complete without errors. Patient record shows complete history. Funding balance reduces correctly after invoice. Dashboard reflects current state.
**Pass/Fail:** ___

---

### TC-097
**Module:** Integration | **Type:** Integration | **Priority:** P1
**Test Name:** Email triggers full intake workflow
**Steps:**
1. Send test email to operations@vestibulartherapyassociates.co.uk
2. Wait for IMAP check (or trigger manually)
3. Link email to David Clarke in email intake screen
4. Verify patient's needs_review = TRUE
5. Check David Clarke appears in Dashboard Daily Actions
6. Mark as Reviewed
7. Verify David Clarke disappears from Daily Actions
**Expected Result:** All steps work end-to-end without manual database intervention.
**Pass/Fail:** ___

---

### TC-098
**Module:** Integration | **Type:** Integration | **Priority:** P1
**Test Name:** Document permission change takes effect immediately
**Preconditions:** Kate logged into associate portal. "Assessment Report" not visible to associate.
**Steps:**
1. Admin navigates to /settings/document-permissions
2. Toggles "Assessment Report" — Associate to ON
3. Saves permissions
4. Kate (associate) refreshes patient detail page
**Expected Result:** Assessment Report document now visible to Kate without requiring logout/login.
**Pass/Fail:** ___

---

### TC-099
**Module:** Integration | **Type:** Integration | **Priority:** P2
**Test Name:** Patient transfer preserves all history
**Preconditions:** David Clarke has documents, communications, appointments and case notes under Emma Bracher
**Steps:**
1. Transfer David Clarke to Mark Tucker (new case manager, different company)
**Expected Result:** All existing documents, appointments, communications and case notes remain intact. patient_case_manager_history record created. David Clarke now appears in Mark Tucker's patient list. Emma Bracher's patient list no longer shows David Clarke.
**Pass/Fail:** ___

---

### TC-100
**Module:** Integration | **Type:** Integration | **Priority:** P2
**Test Name:** Funding balance updates in real time after invoice
**Preconditions:** Funding Cycle with £4,500 approved. No invoices yet.
**Steps:**
1. View patient detail — confirm remaining balance shows £4,500.00
2. Create VTA invoice for £850.00 linked to this cycle
3. Mark invoice as Paid
4. Return to patient detail
5. View funding cycle card
**Expected Result:** Remaining balance now shows £3,650.00. Progress bar updates. No page reload required if Livewire — updates reactively.
**Pass/Fail:** ___

---

## TEST SUMMARY SHEET

| Module | Total Tests | P1 | P2 | P3 |
|---|---|---|---|---|
| 1 — Authentication | 10 | 8 | 2 | 0 |
| 2 — Dashboard | 5 | 3 | 2 | 0 |
| 3 — Enquiries | 5 | 3 | 2 | 0 |
| 4 — Companies | 4 | 2 | 2 | 0 |
| 5 — Case Managers | 5 | 4 | 1 | 0 |
| 6 — Patients | 13 | 8 | 5 | 0 |
| 7 — Documents | 9 | 7 | 2 | 0 |
| 8 — Cost Estimations | 2 | 2 | 0 | 0 |
| 9 — Funding Cycles | 3 | 2 | 1 | 0 |
| 10 — Email Intake | 5 | 4 | 1 | 0 |
| 11 — Appointments | 5 | 4 | 1 | 0 |
| 12 — Case Notes | 4 | 3 | 1 | 0 |
| 13 — Associate Portal | 3 | 3 | 0 | 0 |
| 14 — Finance | 10 | 8 | 2 | 0 |
| 15 — Settings | 7 | 7 | 0 | 0 |
| 16 — UI/Responsive | 5 | 0 | 5 | 0 |
| 17 — Integration | 5 | 3 | 2 | 0 |
| **TOTAL** | **100** | **71** | **29** | **0** |

---

## PASS/FAIL TRACKER

| TC-ID | Test Name | Priority | Phase | Pass/Fail | Notes |
|---|---|---|---|---|---|
| TC-001 | Admin login | P1 | 1 | | |
| TC-002 | Staff login | P1 | 1 | | |
| TC-003 | Associate login | P1 | 2 | | |
| TC-004 | Invalid credentials | P1 | 1 | | |
| TC-005 | Rate limiting | P1 | 1 | | |
| TC-006 | Unauthenticated access | P1 | 1 | | |
| TC-007 | Staff blocked from admin routes | P1 | 1 | | |
| TC-008 | Associate blocked from mgmt routes | P1 | 1 | | |
| TC-009 | Logout | P2 | 1 | | |
| TC-010 | No register button | P2 | 1 | | |
| TC-011 | Dashboard KPI cards | P1 | 1 | | |
| TC-012 | Daily Actions filter | P1 | 1 | | |
| TC-013 | Mark as Reviewed | P1 | 1 | | |
| TC-014 | Unprocessed email badge | P2 | 1 | | |
| TC-015 | Finance card hidden from staff | P2 | 1 | | |
| TC-016 | Log new enquiry | P1 | 1 | | |
| TC-017 | Enquiry validation | P1 | 1 | | |
| TC-018 | Convert enquiry | P1 | 1 | | |
| TC-019 | Enquiry source filter | P2 | 1 | | |
| TC-020 | Not proceeding status | P2 | 1 | | |
| TC-021 | Create company | P1 | 1 | | |
| TC-022 | Company search | P1 | 1 | | |
| TC-023 | Company status filter | P2 | 1 | | |
| TC-024 | Company case manager list | P2 | 1 | | |
| TC-025 | Create case manager | P1 | 1 | | |
| TC-026 | NDA signed | P1 | 1 | | |
| TC-027 | Materials sent | P1 | 1 | | |
| TC-028 | Add communication | P1 | 1 | | |
| TC-029 | Follow-up date | P2 | 1 | | |
| TC-030 | Create patient | P1 | 1 | | |
| TC-031 | Status change valid | P1 | 1 | | |
| TC-032 | BR-C1 gate blocked | P1 | 1 | | |
| TC-033 | BR-C1 gate passes | P1 | 1 | | |
| TC-034 | BR-C2 wrong status | P1 | 1 | | |
| TC-035 | Patient transfer | P1 | 1 | | |
| TC-036 | Assign associate | P1 | 1 | | |
| TC-037 | Multiple associates | P1 | 1 | | |
| TC-038 | End associate | P1 | 1 | | |
| TC-039 | Daily Actions default | P1 | 1 | | |
| TC-040 | Show All toggle | P1 | 1 | | |
| TC-041 | Status filter | P2 | 1 | | |
| TC-042 | Notes auto-save | P2 | 1 | | |
| TC-043 | Upload document | P1 | 1 | | |
| TC-044 | Download document | P1 | 1 | | |
| TC-045 | Direct file access blocked | P1 | 1 | | |
| TC-046 | File size limit | P1 | 1 | | |
| TC-047 | Invalid file type | P1 | 1 | | |
| TC-048 | Password-protected doc | P1 | 1 | | |
| TC-049 | Associate doc permission | P1 | 2 | | |
| TC-050 | Delete document admin | P2 | 1 | | |
| TC-051 | Staff cannot delete | P2 | 1 | | |
| TC-052 | Cost estimation v1 | P1 | 1 | | |
| TC-053 | Cost estimation v2 | P1 | 1 | | |
| TC-054 | Create funding cycle | P1 | 3 | | |
| TC-055 | BR-F2 balance calculation | P1 | 3 | | |
| TC-056 | Second funding cycle | P2 | 3 | | |
| TC-057 | Email intake fetch | P1 | 1 | | |
| TC-058 | Link email to patient | P1 | 1 | | |
| TC-059 | Create patient from email | P1 | 1 | | |
| TC-060 | Mark email irrelevant | P1 | 1 | | |
| TC-061 | Attachment saved | P2 | 1 | | |
| TC-062 | Create appointment | P1 | 2 | | |
| TC-063 | Team calendar | P1 | 2 | | |
| TC-064 | Associate own calendar only | P1 | 2 | | |
| TC-065 | Mark appointment completed | P1 | 2 | | |
| TC-066 | Calendar filter | P2 | 2 | | |
| TC-067 | Upload case note | P1 | 2 | | |
| TC-068 | Associate own patients only | P1 | 2 | | |
| TC-069 | Sign off case note | P1 | 2 | | |
| TC-070 | Staff cannot sign off | P2 | 2 | | |
| TC-071 | Associate sees own patients | P1 | 2 | | |
| TC-072 | Associate sees permitted docs | P1 | 2 | | |
| TC-073 | Associate blocked from internal docs | P1 | 2 | | |
| TC-074 | Log associate invoice | P1 | 3 | | |
| TC-075 | BR-F3 due date | P1 | 3 | | |
| TC-076 | BR-F4 overdue alert | P1 | 3 | | |
| TC-077 | Create VTA invoice | P1 | 3 | | |
| TC-078 | BR-F5 invoice number | P1 | 3 | | |
| TC-079 | BR-F1 balance warning | P1 | 3 | | |
| TC-080 | BR-F6 no doc blocked | P1 | 3 | | |
| TC-081 | Invoice sent with doc | P1 | 3 | | |
| TC-082 | Finance reports | P2 | 3 | | |
| TC-083 | Staff blocked from finance | P1 | 3 | | |
| TC-084 | Add activity type | P1 | 1 | | |
| TC-085 | Deactivate activity type | P1 | 1 | | |
| TC-086 | Add document type | P1 | 1 | | |
| TC-087 | Document permissions toggle | P1 | 1 | | |
| TC-088 | Set associate rates | P1 | 2 | | |
| TC-089 | Create associate login | P1 | 2 | | |
| TC-090 | Create staff user | P1 | 1 | | |
| TC-091 | Mobile patient list | P2 | 1 | | |
| TC-092 | Mobile sidebar | P2 | 1 | | |
| TC-093 | Status badge colours | P2 | 1 | | |
| TC-094 | Flash success message | P2 | 1 | | |
| TC-095 | Calendar mobile list view | P2 | 2 | | |
| TC-096 | Full workflow integration | P1 | 3 | | |
| TC-097 | Email intake integration | P1 | 1 | | |
| TC-098 | Permission change immediate | P1 | 2 | | |
| TC-099 | Transfer preserves history | P2 | 3 | | |
| TC-100 | Funding balance real time | P2 | 3 | | |

---

*Test Case Document Version: 1.0*
*Created: June 2026*
*Total Test Cases: 100*
*P1 Critical: 71 | P2 High: 29 | P3 Medium: 0*
*Application: VTA Portal — Vestibular Therapy Associates*
