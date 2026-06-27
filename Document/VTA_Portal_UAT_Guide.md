# VTA Portal — User Acceptance Testing (UAT) Guide

## Vestibular Therapy Associates

### June 2026 | For: Dr. Samy Selvanayagam, Sheeba Rossewilliam, Associates

---

## HOW TO ACCESS THE PORTAL

**Portal URL:** https://easyerp.co.in/vta-portal/login

**From the VTA Website:**

1. Go to https://easyerp.co.in/VTA/
2. Click **LOGIN TO VTA** in the top navigation menu
3. You will be taken to the portal login page

---

## LOGIN CREDENTIALS FOR UAT TESTING

|Role|Email|Password|What You Can See|
|-|-|-|-|
|**Samy (Admin)**|samy@vestibulartherapyassociates.co.uk|ChangeMe2026!|Everything|
|**Sheeba (Staff)**|sheeba@vestibulartherapyassociates.co.uk|ChangeMe2026!|All patients — no finance|
|**Associate (Kate)**|kate@test.com|ChangeMe2026!|Own patients only|
|**Case Manager (Sarah)**|sarah.mitchell@smithjones.co.uk|ChangeMe2026!|Own company patients + documents|
|**Case Manager (Michael)**|m.turner@turnerins.co.uk|ChangeMe2026!|Own company patients + documents|
|**Case Manager (David)**|david.williams@cunninghamclaims.co.uk|ChangeMe2026!|Own company patients + documents|

> **Important:** Please change your password after UAT testing is complete. Go to your profile settings in the portal.

---

## HOW TO USE THIS GUIDE

* Work through **Section A** logged in as **Samy**
* Work through **Section B** logged in as **Sheeba**
* Work through **Section C** logged in as **Kate** (associate)
* Work through **Section D** logged in as a **Case Manager** (Sarah, Michael or David)
* **To switch users:** Click **Logout** in the sidebar → you will be redirected to the login page. Enter the next user's credentials. Use this "Switch User" method — do not use the browser back button.
* Each step tells you exactly what to click, what to fill in, and what you should see
* If something does not work as described, note it down and report to Jai

---

## SECTION A — SAMY (ADMIN) TESTING

Log in as: **samy@vestibulartherapyassociates.co.uk**

---

### A1. CHECK THE DASHBOARD

**Go to:** https://easyerp.co.in/vta-portal/

**What you should see:**

* Four KPI cards at the top (Total Active Cases, Needs Review Today, Awaiting Funding Approval, Overdue Associate Invoices)
* A "Daily Actions" table below showing patients that need attention
* An "Unprocessed Emails" section showing recent emails that need actioning
* An "Upcoming Appointments" section
* Left sidebar with full navigation menu

**Steps:**

1. After logging in you land on the Dashboard automatically
2. Check the four coloured KPI cards at the top — they should show numbers
3. Check the Daily Actions table — it may be empty if no test data exists yet
4. Check the left sidebar — you should see all menu items including Finance and Settings

**✓ Pass if:** Dashboard loads, KPI cards show, sidebar shows all menus including Finance and Settings

---

### A2. LOG A NEW ENQUIRY

**Go to:** Click **Enquiries** in the left sidebar → click **Log New Enquiry**

**What this represents in your current workflow:**
This is the first thing you do when someone contacts you — before you create any folders or records. It is the quick log of "someone got in touch."

**Steps:**

1. Click **Enquiries** in the left sidebar
2. Click **Log New Enquiry** button (top right)
3. Fill in:

   * Enquirer Name: `Emma Bracher`
   * Company: this is now a **dropdown** of existing companies (so the same company can never be typed twice with slightly different spelling). Since "Community Case Management Ltd" doesn't exist yet, click **+ Add New** next to the Company dropdown — a small popup opens — fill in:

     * Company Name: `Community Case Management Ltd`
     * Type: `Case Management`
     * Phone: `01608 682522`
     * Email: `emma@communitycasemanagement.co.uk`
     * Click **Add Company** — the popup closes and the new company is automatically selected in the dropdown
   * Case Manager: leave as **"-- Select (optional) --"** for now — we'll create Emma as the Case Manager in the next step (A3)
   * Email: `emma@communitycasemanagement.co.uk`
   * Phone: `01608 682522`
   * Source: Select **Email** from dropdown
   * Reason: `Enquiry about vestibular patient in Rugby area — TBI with dizziness`
   * Enquiry Date: Today's date
4. Click **Save Enquiry**

**✓ Pass if:** Enquiry saved, redirected to enquiry detail page, status shows "New". Going back to **Log New Enquiry** again, "Community Case Management Ltd" should now appear in the Company dropdown — confirming it was saved as a proper master record, not just text on this one enquiry.

---

### A3. CONVERT ENQUIRY TO COMPANY AND CASE MANAGER RECORD

**What this represents:**
After the initial enquiry, you decide to proceed. Now you create the proper Company and Case Manager records — just like when you create the Company and Case Manager folders on your desktop.

**Steps (continuing from A2):**

1. On the Enquiry detail page, find the **Convert to Full Record** panel
2. Under **Select Existing Company**, choose `Community Case Management Ltd` (the one you just created in A2) — this hides the "New Company Details" fields since you're using the existing record
3. Under **Case Manager → Select Existing (optional)**, leave as "-- Create New Case Manager --" and fill in:

   * First Name: `Emma`
   * Last Name: `Bracher`
   * Email: `emma@communitycasemanagement.co.uk`
4. Click **Confirm Conversion**

**✓ Pass if:** Case Manager record "Emma Bracher" created under Community Case Management Ltd, Enquiry status changes to "Converted", links to the new Case Manager record shown.

> **Note:** Job Title cannot currently be set at this step (or when editing a Case Manager afterwards) — there's no field for it anywhere in the UI yet, even though the system has a place to store it. Not a blocker for UAT, just don't expect to find a Job Title box.

---

### A4. MARK NDA AS SIGNED

**What this represents:**
When Emma Bracher signs the NDA — you record this in the portal just as you currently note it in your Excel.

**Steps:**

1. Navigate to **Companies** in the sidebar
2. Click **Community Case Management Ltd**
3. In the Case Managers section, click **Emma Bracher**
4. On her detail page, find the **NDA Status** section
5. Click **Mark as Signed**
6. Today's date will be recorded automatically
7. Upload the NDA document if you have one handy

**✓ Pass if:** NDA shows as "Signed on \[today's date]"

---

### A5. MARK MATERIALS AS SENT

**Steps (continuing on Emma Bracher's page):**

1. Find the **Materials Sent** section
2. Click **Mark as Sent**

**✓ Pass if:** Materials section shows "Sent on \[today's date]"

---

### A6. LOG A COMMUNICATION

**What this represents:**
Recording the emails and calls you have with Emma — just like you currently note follow-up dates in your Excel columns (Follow up 1, Follow up 2 etc.). This now replaces those Excel follow-up columns properly: instead of a date sitting in a spreadsheet cell, a follow-up date you set here shows up automatically on the **Team Calendar** until someone marks it done.

**Steps (on Emma Bracher's detail page):**

1. Find the **Communication Log** card and click **Log Communication**
2. Fill in:

   * Type: **Email**
   * Direction: **Inbound**
   * Subject: `Possible referral — Rugby patient`
   * Summary: `Emma contacted to enquire about a TBI patient in Rugby with vestibular disorder and dizziness`
   * Date/Time: leave as the pre-filled current date/time
   * Follow-up Date: set to **3 days from today**
3. Click **Save**

**✓ Pass if:** Communication appears in the Communication Log on Emma's page, with a "Follow-up: \[date]" note and a **Mark Done** link underneath it.

---

### A6B. CHECK THE FOLLOW-UP APPEARS ON THE TEAM CALENDAR (NEW)

**What this represents:**
A way to make sure nobody forgets to chase someone — every open follow-up from any Communication Log (Enquiries, Patients, or Case Managers) now shows up automatically on the same calendar as appointments, in a different colour, so it can't get buried in a spreadsheet column.

**Steps:**

1. Click **Appointments** → **Calendar** in the sidebar
2. Navigate to the date you set as the follow-up date in A6 (3 days from today)
3. Look for a **purple** event titled "Follow-up: Emma Bracher" (purple is called out separately in the Legend panel on the left as "Follow-up (from Communications)")
4. Click on it — a popup should show the Contact name, Subject, and a **Mark Done** button alongside **View Record**
5. Click **Mark Done**

**✓ Pass if:** The purple follow-up event appears on the correct date. Clicking **Mark Done** removes it from the calendar immediately (no page reload needed). Going back to Emma Bracher's Communication Log, the "Follow-up:" note under that entry should now be gone too (it only shows while a follow-up is still open).

---

### A7. CREATE A NEW PATIENT

**What this represents:**
Emma has referred a patient. You create the patient record — this replaces creating the Patient folder under Emma's Case Manager folder, AND adds the row in your Excel tracker.

**Steps:**

1. On Emma Bracher's detail page, click **Add New Patient**
2. Fill in:

   * First Name: `David`
   * Last Name: `Clarke`
   * Date of Birth: `15/03/1975`
   * Location: `Rugby, Warwickshire`
   * Condition: `TBI with vestibular disorder — dizziness and balance issues following road traffic accident`
   * Referral Date: Today
   * Invoice Recipient Type: `Case Manager Company`
   * Invoice Recipient Name: `Community Case Management Ltd`
   * Invoice Recipient Email: `emma@communitycasemanagement.co.uk`
3. Click **Save**

**✓ Pass if:** Patient David Clarke created, status shows "Enquiry Logged", appears in Emma's patient list

---

### A8. ASSIGN AN ASSOCIATE TO THE PATIENT

**What this represents:**
Identifying the nearest associate for David Clarke in Rugby — just like the "Nearest Associate" column in your Excel.

**Steps:**

1. Navigate to David Clarke's patient detail page
2. In the **Associates** card, click **Add Associate**
3. Select:

   * Associate: **Kate Bryce** (North East England — nearest to Rugby)
   * Role: **Assessment**
   * Start Date: Today
4. Click **Save**

**✓ Pass if:** Kate Bryce appears in patient's associates list with role "Assessment"

---

### A9. UPDATE PATIENT STATUS THROUGH THE WORKFLOW

**What this represents:**
Moving David Clarke through the stages — just like updating the status columns in your Excel.

**Steps:**

1. On David Clarke's patient detail, find the **Status** card
2. Change status from **Enquiry Logged** to **Response Sent**
3. Click **Update Status**
4. Verify the status history log shows the change
5. Continue updating through: **Awaiting LOI** → **LOI Received**

**✓ Pass if:** Each status change saves correctly and appears in history log

---

### A10. UPLOAD A DOCUMENT

**What this represents:**
Uploading the LOI from Emma — the same document you currently save in the Patient folder on your desktop.

**Steps:**

1. On David Clarke's patient detail, find the **Documents** card
2. Click **Upload**
3. Select Document Type: **Letter of Instruction**
4. Upload any test PDF file
5. Click **Upload**

**✓ Pass if:** Document appears in David Clarke's document list.

> **Note:** there is currently no "also link to Case Manager" toggle when uploading from a patient's page — a document uploaded here only attaches to the patient. If you want a copy attached directly to Emma's own record, go to her Case Manager page and use the **Upload** button there instead — Documents can now be uploaded directly on Enquiry pages and Case Manager pages too, not just Patient pages (this is new — worth spot-checking on Emma's page while you're there).

---

### A10B. VIEW CASE NOTES ON A PATIENT'S PAGE (NEW)

**What this represents:**
Clinical session notes that an Associate logs after a treatment session (you'll see how those get created in Section C4 of this guide). Previously, the only way to see a patient's case notes was through a separate "Case Notes" menu item with no link back to the patient — they didn't show up on the patient's own page at all. That's now fixed.

**Steps:**

1. Since David Clarke doesn't have any case notes yet at this point in the walkthrough, instead open an existing test patient: navigate to **Patients** in the sidebar and click on **James Hartley**
2. Scroll down James Hartley's page until you find the **Case Notes** card (sits between Communications and the free-text Notes box)

**✓ Pass if:** You see a short list of case notes (date, note type, associate name, and a green "Signed off" or amber "Pending sign-off" badge for each). Clicking **View All** in the top-right of that card takes you to the full Case Notes list, already filtered to just James Hartley.

---

### A10C. SAVE A NOTE ON THE PATIENT PAGE (BUG FIX CHECK)

**What this represents:**
This used to throw an error — worth specifically re-checking it now works.

**Steps:**

1. Still on James Hartley's page (or any patient), scroll to the **Notes** box near the bottom (separate from Case Notes — this is a single free-text box for quick context, not a dated history)
2. Type something, e.g. `Checked in — no change to plan`
3. Click **Save Notes**

**✓ Pass if:** The page saves without any error message and your text remains in the box after the page reloads. (It previously failed with "The first name field is required.")

---

### A11. CREATE A COST ESTIMATION

**What this represents:**
The first quote you send when someone enquires — "Fee for Initial Ax agreed with Referrer" in your Excel.

**Steps:**

1. On David Clarke's patient detail, find the **Cost Estimations** card
2. Click **Add Cost Estimation**
3. Fill in:

   * Version: 1
   * Title: `Initial Assessment Quote`
   * Estimated Amount: `£850.00`
   * Estimated Sessions: `1`
   * Sent Date: Today
   * Sent To: `Emma Bracher`
4. Click **Save**

**✓ Pass if:** Cost estimation appears in patient's cost estimation list

---

### A12. UPDATE STATUS TO ASSESSMENT SCHEDULED

**Steps:**

1. On David Clarke's patient detail
2. Change status to **Assessment Scheduled**
3. Click **Update Status**

**✓ Pass if:** Status updates to Assessment Scheduled

---

### A13. ADD AN APPOINTMENT

**What this represents:**
Booking the initial assessment appointment with Kate Bryce — this replaces your Qunote calendar.

**Steps:**

1. Click **Appointments** in the left sidebar
2. Click **Add Appointment**
3. Fill in:

   * Patient: **David Clarke**
   * Associate: **Kate Bryce**
   * Activity Type: **Initial Assessment**
   * Date/Time: Next Monday at 10:00am
   * Duration: 90 minutes
   * Location: `Patient Home, Rugby`
   * Travel Miles: `45`
4. Click **Save**

**✓ Pass if:** Appointment created, appears on team calendar on correct date

---

### A14. CHECK THE TEAM CALENDAR

**Steps:**

1. Click **Appointments** → **Calendar** in the sidebar
2. View the week containing the appointment you just created

**✓ Pass if:** Kate Bryce's appointment shows on correct day, colour-coded, with patient name visible

---

### A15. MARK ASSESSMENT AS COMPLETED AND UPDATE STATUS

**Steps:**

1. Click on the appointment in the calendar
2. Change appointment status to **Completed**
3. Save
4. Go to David Clarke's patient detail
5. Update status to **Assessment Completed**

**✓ Pass if:** Appointment marked completed, patient status updated

---

### A16. CREATE A SECOND COST ESTIMATION (POST ASSESSMENT)

**What this represents:**
The second quote — "Funding agreed Phase 1" in your Excel. This is the treatment cost estimation sent after the initial assessment.

**Steps:**

1. On David Clarke's patient detail
2. Add another Cost Estimation:

   * Version: 2
   * Title: `Phase 1 Treatment — 6 months`
   * Estimated Amount: `£4,500.00`
   * Estimated Sessions: `12`
   * Duration: `6 months`
3. Update patient status through: **Report Drafted** → **Report Sent** → **Cost Estimation Sent** → **Awaiting Funding Approval**

**✓ Pass if:** Second cost estimation saved, status reaches Awaiting Funding Approval

---

### A17. CREATE A FUNDING CYCLE (FUNDING APPROVAL RECEIVED)

**What this represents:**
Written funding approval received — the portal will not allow treatment to start without this. This replaces "Funding agreed Phase 1" in your Excel.

**Steps:**

1. On David Clarke's patient detail, find the **Funding Cycles** section
2. Click **Add Funding Cycle**
3. Fill in:

   * Link to Cost Estimation: **Phase 1 Treatment — 6 months**
   * Approved Amount: `£4,500.00`
   * Approved Sessions: `12`
   * Approval Date: Today
   * Funder Name: `Community Case Management Ltd`
4. Upload the funding approval document (any PDF)
5. Click **Save**
6. Now update patient status to **Funding Approved**

**✓ Pass if:** Funding cycle created with remaining balance showing £4,500.00. Status successfully moves to Funding Approved. If you try to move to Funding Approved WITHOUT the cycle document — it should be blocked with an error message.

---

### A18. MOVE TO TREATMENT ACTIVE

**Steps:**

1. On David Clarke's patient detail
2. Update status to **Treatment Active**

**✓ Pass if:** Status updates to Treatment Active (green badge)

---

### A19. LOG AN ASSOCIATE INVOICE (RECEIVED FROM KATE)

**What this represents:**
Kate Bryce emails her invoice to you. You log it in the portal — this replaces your Xero entry.

**Steps:**

1. Click **Associate Invoices** in the sidebar (Finance section)
2. Click **Log Associate Invoice**
3. Fill in:

   * Associate: **Kate Bryce**
   * Patient: **David Clarke**
   * Funding Cycle: **Cycle 1**
   * Invoice Reference: `KB-2026-001`
   * Invoice Date: Today
   * Sessions Completed: `3`
   * Travel Miles: `45`
4. Check that Session Amount and Travel Amount are auto-calculated
5. Click **Save**

**✓ Pass if:** Invoice created with due date auto-set to today + 28 days. Sessions × Kate's rate shown.

---

### A20. CREATE A VTA INVOICE (SENT TO FUNDER)

**What this represents:**
Creating the invoice you send to Community Case Management Ltd — this replaces your Xero invoice creation.

**Steps:**

1. Click **VTA Invoices** in the sidebar
2. Click **Create VTA Invoice**
3. Fill in:

   * Patient: **David Clarke**
   * Funding Cycle: **Cycle 1** (check remaining balance shown)
   * Invoice Date: Today
   * Recipient Type: `Case Manager Company`
   * Recipient Name: `Community Case Management Ltd`
   * Sessions Invoiced: `3`
   * Total Amount: `£2,550.00`
4. Click **Save**
5. Note the invoice number — it should be **VTA-2026-0001** (or next in sequence)
6. Upload the invoice PDF
7. Change status to **Sent**

**✓ Pass if:** Invoice number auto-generated. Funding cycle remaining balance reduces. Cannot mark Sent without document uploaded.

---

### A21. CHECK FUNDING CYCLE REMAINING BALANCE

**Steps:**

1. Go to David Clarke's patient detail
2. Find the Funding Cycles card
3. Check the remaining balance

**✓ Pass if:** Remaining balance shows £1,950.00 (£4,500 - £2,550). Progress bar shows approximately 57% spent.

---

### A22. SETTINGS — MANAGE ACTIVITY TYPES

**What this represents:**
Adding a new activity type — for example if VTA starts offering video consultations.

**Steps:**

1. Click **Settings** in the left sidebar
2. Click **Activity Types** tab
3. Click **Add Activity Type**
4. Name: `Video Consultation`
5. Description: `Remote video assessment or treatment session`
6. Click **Save**
7. Go to Appointments → Add Appointment and check the Activity Type dropdown

**✓ Pass if:** Video Consultation appears in the appointment form dropdown

---

### A23. SETTINGS — MANAGE DOCUMENT PERMISSIONS

**What this represents:**
Controlling which documents case managers can see when they eventually get portal access.

**Steps:**

1. Click **Settings** → **Document Permissions** tab
2. You will see a grid — document types as rows, roles as columns
3. Toggle **Assessment Report** → **Case Manager** to ON
4. Toggle **Medical Records** → **Associate** to OFF
5. Click **Save All Permissions**

**✓ Pass if:** Permissions saved. Associate login will now reflect the change.

---

### A24. SETTINGS — SET ASSOCIATE RATES

**Steps:**

1. Click **Settings** → **Associates** tab
2. Find **Kate Bryce**
3. Click **Edit**
4. Set Session Rate: `£95.00`
5. Set Travel Rate Per Mile: `£0.45`
6. Click **Save**

**✓ Pass if:** Rates saved. Next associate invoice for Kate auto-calculates based on these rates.

---

### A25. CHECK FINANCE REPORTS

**Steps:**

1. Click **Finance** → **Reports** in the sidebar
2. View Revenue Summary

**✓ Pass if:** Reports load showing invoiced and paid amounts. Charts display.

---

### A26. EMAIL INTAKE — PROCESS AN EMAIL

**What this represents:**
Checking the shared mailbox and actioning new emails — replaces manually checking email and logging in Excel.

**Steps:**

1. Click **Email Intake** in the sidebar
2. You will see any emails that have arrived at operations@vestibulartherapyassociates.co.uk
3. Click **Link to Patient** on an email → search for David Clarke → Confirm
4. OR click **Mark as Irrelevant** on any spam/internal emails

**✓ Pass if:** Email linked to patient and marked as processed. Disappears from unprocessed view.

---

## SECTION B — SHEEBA (STAFF) TESTING

**Log out of Samy's account first.**
Log in as: **sheeba@vestibulartherapyassociates.co.uk**

---

### B1. VERIFY SHEEBA'S VIEW IS CORRECT

**Steps:**

1. Log in as Sheeba
2. Check the left sidebar

**✓ Pass if:** Dashboard, Enquiries, Companies, Patients, Email Intake visible. **Finance and Settings menus NOT visible.**

---

### B2. DAILY ACTIONS VIEW

**Steps:**

1. Navigate to **Patients** in the sidebar
2. The default view should show only patients where "Needs Review" is ON

**✓ Pass if:** Only patients flagged for review shown. David Clarke should appear.

---

### B3. MARK PATIENT AS REVIEWED

**Steps:**

1. Find David Clarke in the Daily Actions list
2. Click **Mark as Reviewed** (or toggle Needs Review to OFF on his patient detail)

**✓ Pass if:** David Clarke disappears from Daily Actions view. Can still be found via "Show All" toggle.

---

### B4. SHEEBA CANNOT ACCESS FINANCE

**Steps:**

1. Try to navigate directly to: https://easyerp.co.in/vta-portal/vta-invoices
2. Try to navigate directly to: https://easyerp.co.in/vta-portal/finance/reports

**✓ Pass if:** Both pages show "403 — You do not have permission to access this area." Sheeba cannot see any financial information.

---

### B5. UPLOAD A DOCUMENT AS SHEEBA

**Steps:**

1. Navigate to David Clarke's patient detail
2. Upload any test document (e.g. a correspondence PDF)
3. Select type: **Correspondence**

**✓ Pass if:** Document uploaded successfully by Sheeba.

---

### B6. SHEEBA CANNOT DELETE DOCUMENTS

**Steps:**

1. On David Clarke's patient detail, look at the documents list
2. Check what action buttons are available

**✓ Pass if:** Delete button is NOT visible for Sheeba. Only Download is available.

---

### B7. LOG A COMMUNICATION AS SHEEBA

**Steps:**

1. Navigate to Emma Bracher (case manager)
2. Click **Log Communication**
3. Fill in: Type Phone, Direction Outbound, Subject "Confirmed appointment", and leave Summary blank this time
4. Click **Save**

**✓ Pass if:** Communication saves successfully even with Summary left blank — Summary is optional, only Subject is required. Entry appears in Emma's Communication Log.

---

### B8. PROCESS EMAIL INTAKE AS SHEEBA

**Steps:**

1. Click **Email Intake** in the sidebar
2. Action any unprocessed emails

**✓ Pass if:** Sheeba can view and process email intake just like Samy.

---

## SECTION C — ASSOCIATE TESTING (KATE BRYCE)

**Log out of Sheeba's account first.**
Log in as: **kate@test.com**

---

### C1. VERIFY ASSOCIATE PORTAL LOADS

**Steps:**

1. Log in as Kate
2. Check where you land and what is visible

**✓ Pass if:** Redirected to /associate-portal (NOT the main dashboard). Sees "My Patients", "My Calendar", "Upload Case Note". Does NOT see Companies, Email Intake, Finance or Settings.

---

### C2. KATE SEES ONLY HER ASSIGNED PATIENTS

**Steps:**

1. In the associate portal, view the patient list

**✓ Pass if:** Only David Clarke appears (assigned to Kate). Any other patients NOT assigned to Kate are invisible.

---

### C3. VIEW PATIENT DETAIL AS KATE

**Steps:**

1. Click on David Clarke
2. Check what is visible

**✓ Pass if:** Patient details visible (name, condition, location). Documents shows only permitted types (LOI visible, Associate Invoice NOT visible if permissions set correctly in A23).

---

### C4. UPLOAD A CASE NOTE

**What this represents:**
Kate uploading her session notes after the assessment — replaces emailing notes to Samy.

**Steps:**

1. On David Clarke's page in the associate portal
2. Click **Upload Case Note**
3. Fill in:

   * Session Date: Today
   * Note Type: **Session Note**
   * Content: `Initial vestibular assessment completed. Patient presents with BPPV and balance difficulties. VOR exercises commenced. Next session recommended in 2 weeks.`
4. Upload a test PDF of clinical notes
5. Click **Save**

**✓ Pass if:** Case note saved and appears on David Clarke's page.

---

### C5. KATE CANNOT ACCESS OTHER PATIENTS

**Steps:**

1. Try to navigate to a patient NOT assigned to Kate (if any test patients exist)
2. Try: https://easyerp.co.in/vta-portal/patients

**✓ Pass if:** Gets 403 error or is redirected to associate portal. Cannot see the main patients list.

---

### C6. VIEW KATE'S OWN CALENDAR

**Steps:**

1. Click **My Calendar** in the associate portal
2. Check the calendar view

**✓ Pass if:** Only Kate's own appointments visible. The assessment appointment created in A13 should appear.

---

### C7. VERIFY KATE CANNOT SEE FINANCE

**Steps:**

1. Try to navigate to: https://easyerp.co.in/vta-portal/associate-invoices

**✓ Pass if:** 403 error. Kate cannot see any invoices or financial information.

---

## SECTION D — CASE MANAGER PORTAL TESTING

**Log out of Kate's account first.**
Log in as: **sarah.mitchell@smithjones.co.uk** (or any case manager)

---

### D1. VERIFY CASE MANAGER PORTAL LOADS

**Steps:**

1. Log in as Sarah Mitchell
2. Check where you land and what is visible

**✓ Pass if:** Redirected to /case-manager-portal. Sees "My Patients", "Documents". Does NOT see Companies, Enquiries, Email Intake, Finance or Settings.

---

### D2. CASE MANAGER SEES ONLY THEIR COMPANY'S PATIENTS

**Steps:**

1. In the case manager portal, view the patient list
2. Check which patients appear

**✓ Pass if:** Only patients belonging to Sarah's company (Smith & Jones) are visible. David Clarke (belongs to Community Case Management Ltd) is NOT visible to Sarah.

---

### D3. VIEW PATIENT DETAILS

**Steps:**

1. Click on a patient assigned to Sarah's company
2. Check what information is visible

**✓ Pass if:** Patient details visible (name, condition, location). Documents section shows only document types permitted for case manager role.

---

### D4. VIEW AND DOWNLOAD DOCUMENTS

**Steps:**

1. On a patient detail page, find the Documents section
2. Click **Download** on a document

**✓ Pass if:** Documents visible and downloadable. Only document types with case_manager can_view = TRUE are shown.

---

### D5. CASE MANAGER CANNOT UPLOAD DOCUMENTS

**Steps:**

1. Look for any "Upload" button on the patient detail page

**✓ Pass if:** No upload button visible. Case managers are read-only — they cannot add, edit or delete any data.

---

### D6. CASE MANAGER CANNOT ACCESS ADMIN ROUTES

**Steps:**

1. Try to navigate to: https://easyerp.co.in/vta-portal/patients
2. Try to navigate to: https://easyerp.co.in/vta-portal/enquiries
3. Try to navigate to: https://easyerp.co.in/vta-portal/settings

**✓ Pass if:** All three routes return 403 Forbidden. Case manager is restricted to /case-manager-portal only.

---

## FULL CYCLE SUMMARY — WHAT YOU HAVE JUST TESTED

If all steps above passed, you have verified the complete VTA workflow from start to finish:

```
Enquiry received (A2)
        ↓
Company + Case Manager created (A3)
        ↓
NDA signed, Materials sent (A4, A5)
        ↓
Communication logged + follow-up date set (A6)
        ↓
Follow-up shows on Team Calendar, then marked Done (A6B)
        ↓
Patient created (A7)
        ↓
Associate assigned (A8)
        ↓
Status tracked through workflow (A9, A12, A15, A18)
        ↓
Documents uploaded (A10)
        ↓
Case notes visible on the patient's own page (A10B)
        ↓
Cost estimation sent (A11, A16)
        ↓
Appointment booked and completed (A13, A14, A15)
        ↓
Funding approval received (A17)
        ↓
Treatment active (A18)
        ↓
Associate uploads case note (C4)
        ↓
Associate invoice logged (A19)
        ↓
VTA invoice created and sent (A20)
        ↓
Funding balance tracked (A21)
        ↓
Finance reports reviewed (A25)
```

---

## WHAT TO DO IF SOMETHING DOES NOT WORK

1. Note down the exact step that failed (e.g. "Step A17 — funding cycle save button did nothing")
2. Take a screenshot if possible
3. Note the exact error message shown
4. Send to Jai at jai@vestibulartherapyassociates.co.uk with the subject: **VTA Portal UAT — Issue Found**

---

## THINGS TO SPECIFICALLY CHECK AND GIVE FEEDBACK ON

Beyond pass/fail, please give Jai your opinion on:

1. **Is the terminology correct?** Does the portal use the same words you use (e.g. "Funding Cycle", "Cost Estimation", "Associate")
2. **Is anything missing?** Is there any step in your current workflow that the portal does not seem to cover?
3. **Is anything confusing?** Any screen or label that is unclear
4. **Mobile use:** Try accessing the portal on your phone — does it work comfortably?
5. **Speed:** Does the portal feel fast enough for daily use?

---

*UAT Guide Version: 1.1 | June 2026 (updated 22 June — added A6B Follow-up Calendar check, A10B Case Notes on Patient page, A10C Patient Notes save check; corrected A2/A3 Company/Case Manager dropdown + quick-add flow, removed inaccurate "Also link to Case Manager" toggle and unsupported "Job Title" field, corrected "Add Communication" to "Log Communication")
Portal URL: https://easyerp.co.in/vta-portal/login
Prepared by: Jai Anand | jai@vestibulartherapyassociates.co.uk*

