@php $header = 'Testing Guide'; @endphp
<x-app-layout>

<style>
.tg-stage-nav { display: flex; align-items: stretch; border: 1px solid #d1d5db; border-radius: 12px; overflow: hidden; margin-bottom: 2rem; }
.tg-snav-btn { flex: 1; padding: 14px 8px; border: none; background: transparent; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 5px; border-right: 1px solid #d1d5db; transition: background .15s; }
.tg-snav-btn:last-child { border-right: none; }
.tg-snav-btn .sn-num { font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }
.tg-snav-btn .sn-label { font-size: 15px; font-weight: 700; color: #111827; }
.tg-snav-btn .sn-count { font-size: 12px; color: #6b7280; font-weight: 500; }
.tg-snav-btn.s1 .sn-num { color: #185FA5; }
.tg-snav-btn.s2 .sn-num { color: #854F0B; }
.tg-snav-btn.s3 .sn-num { color: #993556; }
.tg-snav-btn.s4 .sn-num { color: #3B6D11; }
.tg-snav-btn.active.s1 { background: #E6F1FB; }
.tg-snav-btn.active.s2 { background: #FAEEDA; }
.tg-snav-btn.active.s3 { background: #FBEAF0; }
.tg-snav-btn.active.s4 { background: #EAF3DE; }
.tg-stage-section { display: none; }
.tg-stage-section.active { display: block; }
.tg-stage-header { border-radius: 12px; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem; }
.tg-stage-header.s1 { background: #E6F1FB; border: 1px solid #85B7EB; }
.tg-stage-header.s2 { background: #FAEEDA; border: 1px solid #EF9F27; }
.tg-stage-header.s3 { background: #FBEAF0; border: 1px solid #ED93B1; }
.tg-stage-header.s4 { background: #EAF3DE; border: 1px solid #97C459; }
.tg-stage-header .tg-slabel { font-size: 12px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; margin-bottom: 6px; }
.tg-stage-header.s1 .tg-slabel { color: #0C447C; }
.tg-stage-header.s2 .tg-slabel { color: #633806; }
.tg-stage-header.s3 .tg-slabel { color: #72243E; }
.tg-stage-header.s4 .tg-slabel { color: #27500A; }
.tg-stage-header h2 { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 8px; }
.tg-stage-header p { font-size: 15px; color: #374151; line-height: 1.65; }
.tg-steps { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.25rem; }
.tg-step { background: #fff; border: 1px solid #d1d5db; border-radius: 12px; overflow: hidden; }
.tg-step-head { display: flex; align-items: center; gap: 14px; padding: 14px 16px; cursor: pointer; user-select: none; }
.tg-step-num { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
.s1 .tg-step-num { background: #85B7EB; color: #042C53; }
.s2 .tg-step-num { background: #EF9F27; color: #412402; }
.s3 .tg-step-num { background: #ED93B1; color: #4B1528; }
.s4 .tg-step-num { background: #97C459; color: #173404; }
.tg-step-title { font-size: 15px; font-weight: 700; color: #111827; flex: 1; }
.tg-step-tag { font-size: 12px; padding: 3px 10px; border-radius: 999px; flex-shrink: 0; font-weight: 600; }
.tg-tag-optional { background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db; }
.tg-tag-key { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
.tg-step-chevron { font-size: 18px; color: #6b7280; transition: transform .2s; flex-shrink: 0; }
.tg-step.open .tg-step-chevron { transform: rotate(180deg); }
.tg-step-body { display: none; padding: 14px 16px 18px 60px; font-size: 15px; color: #374151; line-height: 1.8; border-top: 1px solid #e5e7eb; }
.tg-step.open .tg-step-body { display: block; }
.tg-step-body ul { padding-left: 1.3rem; margin: 8px 0; }
.tg-step-body li { margin-bottom: 7px; }
.tg-step-body strong { color: #111827; font-weight: 700; }
.tg-chip { display: inline-block; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 5px; font-size: 13px; padding: 2px 8px; color: #1d4ed8; margin: 1px 2px; font-weight: 600; }
.tg-note { margin-top: 12px; padding: 11px 14px; font-size: 14px; line-height: 1.65; background: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; }
.tg-note.pink { background: #fdf2f8; color: #831843; border-left-color: #ec4899; }
.tg-note.blue { background: #eff6ff; color: #1e40af; border-left-color: #3b82f6; }
.tg-break-section { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem; }
.tg-break-section h3 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.tg-break-section ul { font-size: 15px; color: #374151; line-height: 1.75; padding-left: 1.3rem; }
.tg-break-section li { margin-bottom: 8px; }
.tg-nav-buttons { display: flex; justify-content: space-between; margin-top: 1.25rem; }
.tg-nav-btn { font-size: 14px; font-weight: 600; padding: 9px 20px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #374151; cursor: pointer; display: flex; align-items: center; gap: 8px; }
.tg-nav-btn:hover { background: #f3f4f6; border-color: #9ca3af; }
.tg-nav-btn:disabled { opacity: .35; cursor: default; background: transparent; }
</style>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Testing Guide</h1>
    <p class="text-base text-gray-600 mt-1">Work through all 4 stages in order. Each stage feeds directly into the next. Click any step to expand the full detail.</p>
</div>

<div class="tg-stage-nav">
    <button class="tg-snav-btn s1 active" onclick="tgShowStage(1)">
        <span class="sn-num">Stage 1</span>
        <span class="sn-label">Enquiry</span>
        <span class="sn-count">5 steps</span>
    </button>
    <button class="tg-snav-btn s2" onclick="tgShowStage(2)">
        <span class="sn-num">Stage 2</span>
        <span class="sn-label">Referral</span>
        <span class="sn-count">10 steps</span>
    </button>
    <button class="tg-snav-btn s3" onclick="tgShowStage(3)">
        <span class="sn-num">Stage 3</span>
        <span class="sn-label">Associate</span>
        <span class="sn-count">8 steps</span>
    </button>
    <button class="tg-snav-btn s4" onclick="tgShowStage(4)">
        <span class="sn-num">Stage 4</span>
        <span class="sn-label">Patient</span>
        <span class="sn-count">9 steps</span>
    </button>
</div>

{{-- STAGE 1 --}}
<div id="tg-stage1" class="tg-stage-section active">
    <div class="tg-stage-header s1">
        <div class="tg-slabel">Stage 1 — Enquiry</div>
        <h2>First contact and intake</h2>
        <p>The very first record in every case. No patient exists yet — this is purely intake and triage. Every case must begin here. You cannot create a referral without an enquiry.</p>
    </div>
    <div class="tg-steps" id="tg-steps1"></div>
    <div class="tg-break-section">
        <h3><i class="ti ti-alert-triangle" style="color:#BA7517; font-size:16px"></i> Things to deliberately try</h3>
        <ul>
            <li>Set status to <strong>Not Proceeding</strong>, then try clicking "Promote to Referral" — you should see a blocked message, not a form.</li>
            <li>Go to the Referrals sidebar and click <strong>+ New Referral</strong> — the system should redirect you to Enquiries with an explanation message.</li>
            <li>In the contacts section, select a role from the dropdown but leave the name blank, then try to save — you should see a validation error.</li>
            <li>Promote the same enquiry to referral, then go back to the enquiry — the "Promote to Referral" button should no longer appear.</li>
        </ul>
    </div>
    <div class="tg-nav-buttons">
        <button class="tg-nav-btn" disabled><i class="ti ti-arrow-left"></i> Previous</button>
        <button class="tg-nav-btn" onclick="tgShowStage(2)">Stage 2 — Referral <i class="ti ti-arrow-right"></i></button>
    </div>
</div>

{{-- STAGE 2 --}}
<div id="tg-stage2" class="tg-stage-section">
    <div class="tg-stage-header s2">
        <div class="tg-slabel">Stage 2 — Referral</div>
        <h2>Active assessment stage</h2>
        <p>VTA is now actively managing the case. An associate is assigned to carry out the assessment. All activity — sessions, bills, documents, communications — is logged here. The patient record does not exist yet.</p>
    </div>
    <div class="tg-steps" id="tg-steps2"></div>
    <div class="tg-break-section">
        <h3><i class="ti ti-alert-triangle" style="color:#BA7517; font-size:16px"></i> Things to deliberately try</h3>
        <ul>
            <li>Try clicking "Convert to Patient" on a referral still in <strong>New Referral</strong> or <strong>Assessment</strong> status — the button should not appear at all.</li>
            <li>Try to submit a proposal before recording a go-ahead to visit — check whether the flow enforces order.</li>
            <li>Upload a document and immediately request a revision — the document should show an amber flag.</li>
            <li>Add a bill, then go to <strong>Accounts → Referral Bills</strong> and mark it as Paid from there — confirm the status updates.</li>
            <li>After converting to a patient, return to the referral and try converting again — it should say "already converted".</li>
        </ul>
    </div>
    <div class="tg-nav-buttons">
        <button class="tg-nav-btn" onclick="tgShowStage(1)"><i class="ti ti-arrow-left"></i> Stage 1 — Enquiry</button>
        <button class="tg-nav-btn" onclick="tgShowStage(3)">Stage 3 — Associate <i class="ti ti-arrow-right"></i></button>
    </div>
</div>

{{-- STAGE 3 --}}
<div id="tg-stage3" class="tg-stage-section">
    <div class="tg-stage-header s3">
        <div class="tg-slabel">Stage 3 — Associate Portal</div>
        <h2>Associate login and first assessment</h2>
        <p>Log in as the associate who was assigned in Stage 2. The associate has their own separate portal — they see only their own referrals and patients. This stage runs in parallel with Stage 2: both sides are working on the same case simultaneously.</p>
    </div>
    <div class="tg-steps" id="tg-steps3"></div>
    <div class="tg-break-section">
        <h3><i class="ti ti-alert-triangle" style="color:#BA7517; font-size:16px"></i> Things to deliberately try</h3>
        <ul>
            <li>Log in as an associate who was <strong>not</strong> assigned to the referral — they should not see it in their list.</li>
            <li>As an associate, try to navigate to the main admin area (e.g. <code>/patients</code>) — access should be denied or redirected.</li>
            <li>Go back to Samy's login and request a document revision — log in as the associate again and verify the amber alert appears.</li>
            <li>As the associate, try to re-upload without attaching a file — you should see a validation error.</li>
            <li>Check the associate's calendar — the session you logged should appear on the correct date.</li>
        </ul>
    </div>
    <div class="tg-nav-buttons">
        <button class="tg-nav-btn" onclick="tgShowStage(2)"><i class="ti ti-arrow-left"></i> Stage 2 — Referral</button>
        <button class="tg-nav-btn" onclick="tgShowStage(4)">Stage 4 — Patient <i class="ti ti-arrow-right"></i></button>
    </div>
</div>

{{-- STAGE 4 --}}
<div id="tg-stage4" class="tg-stage-section">
    <div class="tg-stage-header s4">
        <div class="tg-slabel">Stage 4 — Patient</div>
        <h2>Long-term treatment record</h2>
        <p>The patient record is created from the approved referral. The full history — enquiry, referral, associate assessment — is all linked and visible in the audit. This is where ongoing treatment, appointments, billing, and clinical notes live.</p>
    </div>
    <div class="tg-steps" id="tg-steps4"></div>
    <div class="tg-break-section">
        <h3><i class="ti ti-alert-triangle" style="color:#BA7517; font-size:16px"></i> Things to deliberately try</h3>
        <ul>
            <li>Go to <strong>Reports → Patient Audit</strong> and select the patient — verify the full enquiry → referral → patient timeline is shown with all milestones.</li>
            <li>Set a clinical alert and confirm the amber warning banner appears at the top of the patient page.</li>
            <li>Change the patient status through multiple states and check each change is recorded in the audit trail with a timestamp and user name.</li>
            <li>Book an appointment and then log in as the associate — confirm the appointment appears on their calendar.</li>
            <li>Log in as the associate and check <strong>My Patients</strong> — the converted patient should appear there now.</li>
            <li>Go to <strong>Reports → Associate Audit</strong> and select the associate — both referral-stage sessions and patient-stage sessions should appear.</li>
        </ul>
    </div>
    <div class="tg-nav-buttons">
        <button class="tg-nav-btn" onclick="tgShowStage(3)"><i class="ti ti-arrow-left"></i> Stage 3 — Associate</button>
        <button class="tg-nav-btn" disabled>All done <i class="ti ti-check"></i></button>
    </div>
</div>

<script>
const tgData = {
  1: [
    { title: "Go to Enquiries and create a new record", tag: "key",
      body: `In the left sidebar click <span class="tg-chip">Enquiries</span>, then click <span class="tg-chip">+ New Enquiry</span> at the top right.<br><br>
      Fill in the patient details:<br>
      <ul>
        <li><strong>First name</strong> and <strong>last name</strong> — required, cannot be saved without these</li>
        <li><strong>Date of birth</strong> — optional, dd/mm/yyyy format</li>
        <li><strong>Postcode</strong> — optional, used for travel planning</li>
        <li><strong>Phone</strong> and <strong>email</strong> — optional, used for contact logging later</li>
        <li><strong>Referral source</strong> — where this case came from: Solicitor, GP, Insurance, Self-Referral, Other</li>
        <li><strong>Company</strong> — the referring organisation if there is one (links to the case manager list)</li>
        <li><strong>Special instructions</strong> — anything VTA needs to know from the outset; carries through to the referral and is visible to the associate</li>
        <li><strong>Notes</strong> — internal notes only, not visible outside Samy's admin view</li>
      </ul>`
    },
    { title: "Add contacts to the enquiry", tag: "optional",
      body: `Scroll down to the <span class="tg-chip">Contacts</span> section on the same create form.<br><br>
      Contacts are people linked to this case (not the patient themselves):<br>
      <ul>
        <li><strong>Case Manager</strong> — the person at the referring company managing the case</li>
        <li><strong>Solicitor</strong> — legal representative if this is a legal case</li>
        <li><strong>GP</strong> — the patient's doctor</li>
        <li><strong>Other</strong> — any other relevant party</li>
      </ul>
      For each contact: select a role → enter name → enter phone and email → click <span class="tg-chip">+ Add Contact</span> for another row. To remove a row click the <strong>×</strong> button.
      <div class="tg-note">If you select a role but leave the name blank and try to save, you will get a validation error. Either fill in the name or remove the row entirely.</div>`
    },
    { title: "Save and review the new enquiry", tag: "key",
      body: `Click <span class="tg-chip">Create Enquiry</span>. The system auto-generates a reference number in the format <strong>VTA-001</strong>. This same reference follows the case all the way through to the patient record.<br><br>
      You land on the enquiry detail page. From here you can:<br>
      <ul>
        <li>Click <span class="tg-chip">Edit</span> to correct any details</li>
        <li>See all contacts listed below the main info</li>
        <li>See the current status (starts as <strong>New</strong>)</li>
        <li>View the created-by user and timestamp</li>
      </ul>`
    },
    { title: "Update the enquiry status as the case progresses", tag: "optional",
      body: `The status field tracks where this enquiry stands with the referring party. Change it via the <span class="tg-chip">Edit</span> button at any point.<br><br>
      Available statuses and what they mean:<br>
      <ul>
        <li><strong>New</strong> — just logged, no action taken yet</li>
        <li><strong>Response Sent</strong> — VTA has replied to the referral source</li>
        <li><strong>Awaiting LOI</strong> — waiting for Letter of Instruction from the solicitor or insurer</li>
        <li><strong>LOI Received</strong> — letter received, ready to proceed</li>
        <li><strong>Not Proceeding</strong> — this case will not move forward; sets a block on promotion to referral</li>
        <li><strong>Converted to Referral</strong> — set automatically by the system when you promote; cannot be set manually</li>
      </ul>
      <div class="tg-note">Setting status to "Not Proceeding" blocks the "Promote to Referral" button. You must change the status back before you can promote.</div>`
    },
    { title: "Promote to Referral", tag: "key",
      body: `When the enquiry is ready to move to active assessment, click <span class="tg-chip">Promote to Referral</span> on the enquiry detail page.<br><br>
      What happens automatically:<br>
      <ul>
        <li>You are taken to the Create Referral form with all patient details pre-filled</li>
        <li>The enquiry status changes to <strong>Converted to Referral</strong></li>
        <li>The enquiry and referral are permanently linked — the audit trail preserves the full chain</li>
        <li>The "Promote to Referral" button disappears — it cannot be promoted twice</li>
      </ul>
      <div class="tg-note blue">After clicking Promote, you move directly to Stage 2. The Create Referral form is waiting for you.</div>`
    }
  ],
  2: [
    { title: "Complete and save the referral", tag: "key",
      body: `You arrive on the Create Referral form directly from "Promote to Referral". All patient details are pre-filled — check and correct anything that is wrong.<br><br>
      Additional referral-specific fields:<br>
      <ul>
        <li><strong>Company</strong> — the funding or referring organisation</li>
        <li><strong>Case Manager</strong> — select from the dropdown; the list filters by company once a company is selected</li>
        <li><strong>Special Instructions</strong> — carried over from the enquiry; visible to the associate in their portal</li>
        <li><strong>Notes</strong> — internal to Samy's admin; not visible to associates</li>
        <li><strong>Status</strong> — starts as <strong>New Referral</strong>; do not change manually, it updates through the workflow</li>
      </ul>
      Click <span class="tg-chip">Create Referral</span>. The referral gets the same VTA-xxx ref as the enquiry.`
    },
    { title: "Record go-ahead to visit (assign the associate)", tag: "key",
      body: `Once VTA receives approval from the funding party to send an associate for assessment, scroll to <span class="tg-chip">Go-Ahead to Visit</span> on the referral detail page.<br><br>
      <ul>
        <li><strong>Associate</strong> — select the associate who will carry out the assessment; only active associates appear</li>
        <li><strong>Go-Ahead Date</strong> — the date approval was received</li>
        <li><strong>Approval document</strong> — optional; attach the written approval (PDF, Word, image, max 10MB)</li>
      </ul>
      Click Save. Status changes to <strong>Assessment</strong>. The assigned associate will now see this referral in their portal.`
    },
    { title: "Log assessment sessions", tag: "optional",
      body: `Sessions can be logged by Samy from the admin side, or by the associate from their portal (Stage 3). Both create the same record.<br><br>
      Scroll to <span class="tg-chip">Sessions</span> and click <span class="tg-chip">+ Log Session</span>.<br><br>
      <ul>
        <li><strong>Activity type</strong> — Initial Assessment, Follow-up, Telephone Review, Report Writing, MDT Meeting, etc.</li>
        <li><strong>Date and time</strong> — when the session took place</li>
        <li><strong>Duration</strong> — in minutes (e.g. 60, 90, 120)</li>
        <li><strong>Location</strong> — patient's home, clinic, telephone, video call</li>
        <li><strong>Notes</strong> — what was covered; clinical summary</li>
      </ul>
      Each session also appears on the calendar in dark green. You can log as many sessions as needed.`
    },
    { title: "Log communications", tag: "optional",
      body: `Scroll to <span class="tg-chip">Communications</span> and click <span class="tg-chip">+ Log Communication</span>.<br><br>
      Record every contact with the referring party, case manager, associate, or any other party:<br>
      <ul>
        <li><strong>Type</strong> — Phone Call, Email, Letter, Meeting, Video Call, Other</li>
        <li><strong>Direction</strong> — Inbound (they contacted VTA) or Outbound (VTA contacted them)</li>
        <li><strong>Subject</strong> — brief one-line description</li>
        <li><strong>Notes</strong> — full detail of what was discussed, agreed, or actioned</li>
        <li><strong>Document</strong> — optionally attach a file (email thread, letter scan)</li>
      </ul>`
    },
    { title: "Upload documents for the associate", tag: "optional",
      body: `Scroll to <span class="tg-chip">Documents</span> and click <span class="tg-chip">+ Upload Document</span>.<br><br>
      <ul>
        <li><strong>Title</strong> — a clear descriptive name (e.g. "LOI from Carter Solicitors", "Referral Letter from GP")</li>
        <li><strong>File</strong> — PDF, Word, or image up to 10MB</li>
        <li><strong>Visible to associate</strong> — tick this checkbox if the associate needs to read this document in their portal. Leave unticked for internal-only documents.</li>
      </ul>
      Only ticked documents appear in the associate's portal.`
    },
    { title: "Request a document revision from the associate", tag: "optional",
      body: `After a document has been uploaded by the associate (Stage 3), you can request they revise it.<br><br>
      Next to any associate-uploaded document, click <span class="tg-chip">Request Revision</span>.<br><br>
      <ul>
        <li>Enter a note explaining what needs changing</li>
        <li>Click Save — the document is flagged amber with your note</li>
        <li>The associate sees an amber alert banner on their referral page the next time they log in</li>
        <li>Once they re-upload a revised version, the amber flag clears automatically</li>
      </ul>
      <div class="tg-note pink">To test this: complete Stage 3 first (have the associate upload a document), then come back to this step.</div>`
    },
    { title: "Log associate bills", tag: "optional",
      body: `Scroll to <span class="tg-chip">Bills</span> and click <span class="tg-chip">+ Add Bill</span>.<br><br>
      <ul>
        <li><strong>Bill date</strong> — when the bill was raised</li>
        <li><strong>Amount</strong> — in pounds sterling, e.g. 350.00</li>
        <li><strong>Status</strong> — Pending by default</li>
      </ul>
      Bill status options:<br>
      <ul>
        <li><strong>Pending</strong> — bill received, not yet reviewed</li>
        <li><strong>Paid</strong> — payment has been made to the associate</li>
        <li><strong>Unpaid</strong> — reviewed and marked as not payable (disputed, duplicate, etc.)</li>
      </ul>
      Bills can also be marked as Paid from <span class="tg-chip">Accounts → Referral Bills</span> in the sidebar.`
    },
    { title: "Submit the proposal", tag: "key",
      body: `Once the assessment is complete and VTA is ready to propose a treatment plan, scroll to <span class="tg-chip">Submit Proposal</span> on the referral page.<br><br>
      <ul>
        <li><strong>Proposal submission date</strong> — when the proposal was sent (required)</li>
        <li><strong>Proposal document</strong> — optional; attach the proposal PDF or Word document</li>
      </ul>
      Click Save. Status changes to <strong>Proposal Submitted</strong>.`
    },
    { title: "Record proposal approval", tag: "key",
      body: `Once the funding party approves the proposal, scroll to <span class="tg-chip">Approve Proposal</span> on the referral page.<br><br>
      <ul>
        <li><strong>Approval date</strong> — when approval was received (required)</li>
      </ul>
      Click Save. Status changes to <strong>Approved</strong>. The <span class="tg-chip">Convert to Patient</span> button now becomes available.`
    },
    { title: "Convert to Patient", tag: "key",
      body: `Click <span class="tg-chip">Convert to Patient</span> on the referral detail page.<br><br>
      A form appears pre-filled with the patient's details. Review and correct:<br>
      <ul>
        <li><strong>First name, last name</strong> — correct any spelling errors now</li>
        <li><strong>Date of birth, postcode, address, phone, email</strong> — add anything missing</li>
        <li><strong>Associate</strong> — defaults to the assessment associate from Stage 2; change this if a different associate will handle treatment</li>
      </ul>
      Click <span class="tg-chip">Create Patient Record</span>. What happens:<br>
      <ul>
        <li>The patient record is created with status <strong>Treatment Active</strong></li>
        <li>The referral and enquiry are permanently linked to the patient via VTA-xxx</li>
        <li>You are taken directly to the new patient detail page — Stage 4 begins</li>
      </ul>`
    }
  ],
  3: [
    { title: "Log in as the associate", tag: "key",
      body: `Open a new browser window (or use a private/incognito tab so you stay logged in as Samy in the other window).<br><br>
      Go to the VTA Portal login page. Use the associate's credentials:<br>
      <ul>
        <li>Test associates available: <strong>Georgios Tsiknas, Ileana Vasile, Nick Stamatis, Sultana Kiani</strong></li>
        <li>Username: their email address (check the Associates settings page for the exact email)</li>
        <li>Password: <strong>password</strong> (for all test associates)</li>
      </ul>
      After logging in, the associate sees their own portal — a completely different layout from Samy's admin view.
      <div class="tg-note pink">Make sure you assigned <strong>this associate</strong> in Step 2 of Stage 2 ("Record go-ahead to visit"), otherwise the referral will not appear in their list.</div>`
    },
    { title: "Review the associate dashboard", tag: "",
      body: `The associate's dashboard shows a summary of their current workload:<br>
      <ul>
        <li><strong>My Referrals card</strong> — number of active referrals in Assessment / Proposal stage</li>
        <li><strong>My Patients card</strong> — number of active treatment patients</li>
        <li><strong>Upcoming appointments</strong> — sessions booked in the near future</li>
        <li><strong>Pending revisions badge</strong> — amber badge on the sidebar "My Referrals" link if Samy has requested any document revisions</li>
      </ul>
      The associate cannot see any other associate's data, any financial figures, or any admin-only sections.`
    },
    { title: "Find the referral in My Referrals", tag: "key",
      body: `Click <span class="tg-chip">My Referrals</span> in the associate's sidebar.<br><br>
      The list shows all referrals currently assigned to this associate (Assessment, Proposal Submitted, and Approved statuses). The referral created in Stage 2 should appear here.<br><br>
      Click the referral row to open the referral detail page. The associate's view shows:<br>
      <ul>
        <li>Patient name and VTA reference number</li>
        <li>Go-ahead date and any special instructions from Samy</li>
        <li>Documents that Samy marked as "Visible to Associate"</li>
        <li>The associate's own sessions, communications, documents, and bills</li>
      </ul>
      <div class="tg-note pink">If the referral does not appear: go back to Samy's admin and confirm the correct associate was selected in the Go-Ahead to Visit step, and that the referral status is Assessment or later.</div>`
    },
    { title: "Log a session from the associate side", tag: "key",
      body: `On the referral detail page, scroll to <span class="tg-chip">Sessions</span> and click <span class="tg-chip">+ Log Session</span>.<br><br>
      <ul>
        <li><strong>Activity type</strong> — what kind of assessment session this was</li>
        <li><strong>Date and time</strong> — when it happened</li>
        <li><strong>Duration</strong> — in minutes</li>
        <li><strong>Location</strong> — where it took place</li>
        <li><strong>Notes</strong> — clinical summary or observations from the session</li>
      </ul>
      Click Save. The session appears in the associate's sessions list, in Samy's admin view of the referral, and on the calendar in dark green.`
    },
    { title: "Log a communication", tag: "optional",
      body: `Scroll to <span class="tg-chip">Communications</span> and click <span class="tg-chip">+ Log Communication</span>.<br><br>
      <ul>
        <li><strong>Type</strong> — Phone Call, Email, Letter, Meeting, Video Call, Other</li>
        <li><strong>Direction</strong> — Inbound or Outbound</li>
        <li><strong>Subject</strong> — brief description</li>
        <li><strong>Notes</strong> — full detail of the communication</li>
        <li><strong>Document</strong> — optional file attachment</li>
      </ul>
      All communications logged by the associate appear in Samy's admin view of the same referral.`
    },
    { title: "Upload a document (e.g. assessment report)", tag: "key",
      body: `Scroll to <span class="tg-chip">Documents</span> and click <span class="tg-chip">+ Upload Document</span>.<br><br>
      <ul>
        <li><strong>Title</strong> — a clear name for the document (e.g. "Initial Assessment Report", "Functional Capacity Evaluation")</li>
        <li><strong>File</strong> — PDF or Word document, max 10MB</li>
      </ul>
      The document is uploaded and immediately visible to Samy in his admin view of the referral.<br><br>
      If Samy has already requested a revision on a previously uploaded document, an amber alert banner appears at the top of the referral page.`
    },
    { title: "Respond to a document revision request", tag: "optional",
      body: `If Samy has flagged a document for revision (Step 6 of Stage 2), an amber alert banner appears on the referral page.<br><br>
      To resolve it:<br>
      <ul>
        <li>Click the amber banner or scroll to the flagged document — Samy's revision note is shown in amber text below the document</li>
        <li>Click <span class="tg-chip">Re-upload</span> on the flagged document</li>
        <li>Attach the revised file</li>
        <li>Click Save</li>
      </ul>
      The amber flag clears automatically. Samy's admin view will no longer show the revision request on that document.
      <div class="tg-note">This only appears if Samy has actively requested a revision. If no revision was requested, there is nothing to action here.</div>`
    },
    { title: "Check the associate calendar", tag: "",
      body: `Click <span class="tg-chip">My Calendar</span> in the associate's sidebar.<br><br>
      The calendar shows:<br>
      <ul>
        <li><strong>Dark green events</strong> — referral assessment sessions logged in Steps 4 and 5 above</li>
        <li><strong>Other colour events</strong> — patient treatment appointments (appear after Stage 4 when appointments are booked)</li>
      </ul>
      Click any event to see the details — patient name, VTA ref, activity type, duration, location, and a link to the referral or patient page.<br><br>
      If a session does not appear, check the date — it will only show on the date the session was logged as occurring.`
    }
  ],
  4: [
    { title: "Review the newly created patient record", tag: "key",
      body: `You are taken to the patient detail page automatically after clicking "Create Patient Record" in Stage 2.<br><br>
      Confirm the following are correct:<br>
      <ul>
        <li><strong>Patient ID</strong> — should be VTA-xxx (same ref as the enquiry and referral)</li>
        <li><strong>Full name</strong> — carried over from the referral</li>
        <li><strong>Referral date</strong> — automatically set to today's date</li>
        <li><strong>Status</strong> — starts as <strong>Treatment Active</strong></li>
        <li><strong>Company and case manager</strong> — linked from the referral</li>
      </ul>
      The enquiry and referral records are permanently linked to this patient. All their history is preserved.`
    },
    { title: "Edit patient details", tag: "optional",
      body: `Click <span class="tg-chip">Edit</span> on the patient page to update or complete the record.<br><br>
      All editable fields:<br>
      <ul>
        <li><strong>Contact details</strong> — address, phone, email, date of birth</li>
        <li><strong>Diagnosis / condition</strong> — the clinical condition being treated</li>
        <li><strong>Reason for referral</strong> — brief summary of why VTA was engaged</li>
        <li><strong>Fee agreed amount</strong> — agreed treatment fee in pounds; attach the fee agreement document</li>
        <li><strong>Invoice recipient type</strong> — who gets the invoices: Case Manager Company, Solicitor, Insurance Company, or Other</li>
        <li><strong>Invoice recipient details</strong> — name, email, and address of the invoice recipient</li>
        <li><strong>Clinical alert</strong> — important clinical warnings (e.g. "Patient has severe needle phobia"); appears as a prominent amber banner on the patient page</li>
        <li><strong>First contact date</strong> — when VTA first made contact with the patient directly</li>
      </ul>`
    },
    { title: "Assign a staff member", tag: "optional",
      body: `Scroll to the <span class="tg-chip">Staff Assignment</span> section on the patient page.<br><br>
      Assigning a staff member means:<br>
      <ul>
        <li>That staff member sees this patient highlighted in their dashboard</li>
        <li>Every assignment change is logged in the audit trail with a timestamp and the name of who made the change</li>
      </ul>
      Staff are VTA's internal team. This is separate from the associate — the associate is the external therapist delivering treatment.`
    },
    { title: "Book treatment appointments", tag: "key",
      body: `Go to <span class="tg-chip">Appointments</span> in the sidebar, or click <span class="tg-chip">+ New Appointment</span> if visible on the patient page.<br><br>
      Fill in the appointment details:<br>
      <ul>
        <li><strong>Patient</strong> — select from the dropdown (may be pre-selected if coming from the patient page)</li>
        <li><strong>Associate</strong> — who is delivering this treatment session</li>
        <li><strong>Date and time</strong> — when the appointment is scheduled</li>
        <li><strong>Duration</strong> — in minutes</li>
        <li><strong>Activity type</strong> — Treatment Session, Review, MDT Meeting, Telephone Check-in</li>
        <li><strong>Location</strong> — patient's home, clinic, phone, video call</li>
        <li><strong>Notes</strong> — any preparation notes or instructions for the associate</li>
      </ul>
      The appointment appears on the main admin calendar and on the associate's calendar in their portal.`
    },
    { title: "Log case notes", tag: "optional",
      body: `Scroll to <span class="tg-chip">Treatment Notes</span> on the patient page. Click <span class="tg-chip">+ Add Note</span>.<br><br>
      <ul>
        <li>Write the clinical or treatment note in the free-text area — no character limit</li>
        <li>Click Save — the note is timestamped with your name and the date/time</li>
        <li>All notes stack in reverse-chronological order (newest first)</li>
        <li>Click <span class="tg-chip">View All</span> if older notes are collapsed</li>
      </ul>
      Notes are visible to admin users only — associates do not see case notes in their portal.`
    },
    { title: "Upload patient documents", tag: "optional",
      body: `Scroll to <span class="tg-chip">Documents</span> on the patient page. Click <span class="tg-chip">Upload</span>.<br><br>
      <ul>
        <li><strong>Title</strong> — label clearly (e.g. "Discharge Report", "Funding Approval Letter", "Session 3 Report")</li>
        <li><strong>File</strong> — PDF, Word, or image up to 10MB</li>
      </ul>
      Patient documents are visible to the assigned associate when they view the patient in their portal under <span class="tg-chip">My Patients</span>.`
    },
    { title: "Log communications at patient stage", tag: "optional",
      body: `Scroll to <span class="tg-chip">Communications</span> and click <span class="tg-chip">+ Log Communication</span>.<br><br>
      Same fields as at the referral stage: Type, Direction, Subject, Notes, optional file attachment.<br><br>
      Useful for logging: phone calls with the patient or their family, emails from the case manager about funding, letters from the GP, funding review meetings, MDT communications.`
    },
    { title: "Check the Patient Audit", tag: "key",
      body: `Go to <span class="tg-chip">Reports</span> in the sidebar, then <span class="tg-chip">Patient Audit</span>. Select the patient from the dropdown.<br><br>
      The audit page shows the complete journey from day one:<br>
      <ul>
        <li><strong>6-step milestone bar</strong> — Enquiry → Promoted to Referral → Go-Ahead → Proposal Submitted → Proposal Approved → Converted to Patient</li>
        <li><strong>Referral stage summary</strong> — sessions logged, bills raised, communications, documents</li>
        <li><strong>Patient stage summary</strong> — appointments, case notes, status changes</li>
        <li><strong>Full activity log</strong> — every action taken by every user, with timestamp and user name</li>
      </ul>
      This is the single source of truth for the entire case history.`
    },
    { title: "Change the patient status through the lifecycle", tag: "optional",
      body: `The patient status tracks where they are in treatment. Change it via the <span class="tg-chip">Edit</span> button.<br><br>
      Full status progression:<br>
      <ul>
        <li><strong>Treatment Active</strong> — default after conversion; sessions are ongoing</li>
        <li><strong>On Hold</strong> — treatment paused temporarily (patient hospitalised, awaiting further assessment)</li>
        <li><strong>Awaiting Further Funding</strong> — additional funding approval needed to continue treatment</li>
        <li><strong>Funding Approved</strong> — new funding confirmed, treatment resuming</li>
        <li><strong>Discharged</strong> — treatment complete; patient formally discharged</li>
        <li><strong>Case Closed</strong> — fully closed, all documentation complete, no further action</li>
        <li><strong>Not Proceeding</strong> — case cancelled at patient stage before treatment completed</li>
      </ul>
      Every status change is logged in the audit trail with the date, time, and name of the user who made the change.`
    }
  ]
};

function tgRenderSteps(sn) {
  const c = document.getElementById('tg-steps' + sn);
  const cls = 's' + sn;
  c.innerHTML = tgData[sn].map((s, i) => {
    const tag = s.tag === 'key'      ? `<span class="tg-step-tag tg-tag-key">Key step</span>` :
                s.tag === 'optional' ? `<span class="tg-step-tag tg-tag-optional">Optional</span>` : '';
    return `<div class="tg-step ${cls}" onclick="tgToggle(this)">
      <div class="tg-step-head">
        <div class="tg-step-num">${i+1}</div>
        <div class="tg-step-title">${s.title}</div>
        ${tag}
        <i class="ti ti-chevron-down tg-step-chevron"></i>
      </div>
      <div class="tg-step-body">${s.body}</div>
    </div>`;
  }).join('');
}

function tgToggle(el) { el.classList.toggle('open'); }

function tgShowStage(n) {
  document.querySelectorAll('.tg-stage-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tg-snav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tg-stage' + n).classList.add('active');
  document.querySelectorAll('.tg-snav-btn')[n-1].classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

[1,2,3,4].forEach(tgRenderSteps);
</script>

</x-app-layout>
