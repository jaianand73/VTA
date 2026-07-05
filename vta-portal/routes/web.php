<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssociatePortalController;
use App\Http\Controllers\CaseManagerController;
use App\Http\Controllers\CaseNoteController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailIntakeController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientMdtMeetingController;
use App\Http\Controllers\AssociateInvoiceController;
use App\Http\Controllers\CaseManagerPortalController;
use App\Http\Controllers\CostEstimationController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FundingCycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VtaInvoiceController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\PortalFeedbackController;
use App\Http\Controllers\UatGuideController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::post('/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
    Route::get('/enquiries/create', [EnquiryController::class, 'create'])->name('enquiries.create');
    Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
    Route::get('/enquiries/{enquiry}/edit', [EnquiryController::class, 'edit'])->name('enquiries.edit');
    Route::put('/enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
    Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    Route::post('/enquiries/{enquiry}/convert', [EnquiryController::class, 'convert'])->name('enquiries.convert');

    Route::resource('companies', CompanyController::class);
    Route::get('/companies/{company}/case-managers/{caseManager}', [CaseManagerController::class, 'show'])->name('companies.case-managers.show');
    Route::put('/companies/{company}/case-managers/{caseManager}', [CaseManagerController::class, 'update'])->name('companies.case-managers.update');
    Route::delete('/companies/{company}/case-managers/{caseManager}', [CaseManagerController::class, 'destroy'])->name('companies.case-managers.destroy');
    Route::post('/companies/{company}/case-managers/{caseManager}/create-portal-login', [CaseManagerController::class, 'createPortalLogin'])->name('companies.case-managers.create-portal-login');
    Route::post('/companies/{company}/case-managers/{caseManager}/mark-nda-signed', [CaseManagerController::class, 'markNdaSigned'])->name('companies.case-managers.mark-nda-signed');
    Route::post('/companies/{company}/case-managers/{caseManager}/mark-materials-sent', [CaseManagerController::class, 'markMaterialsSent'])->name('companies.case-managers.mark-materials-sent');
    Route::post('/case-managers/quick-add', [CaseManagerController::class, 'store'])->name('case-managers.quick-add');

    Route::patch('/patients/{patient}/status', [PatientController::class, 'updateStatus'])->name('patients.status');
    Route::patch('/patients/{patient}/transfer', [PatientController::class, 'transfer'])->name('patients.transfer');
    Route::patch('/patients/{patient}/needs-review', [PatientController::class, 'toggleNeedsReview'])->name('patients.needs-review');
    Route::patch('/patients/{patient}/assign-staff', [PatientController::class, 'assignStaff'])->name('patients.assign-staff');
    Route::patch('/patients/{patient}/update-nok', [PatientController::class, 'updateNok'])->name('patients.update-nok');
    Route::patch('/patients/{patient}/update-referrers', [PatientController::class, 'updateReferrers'])->name('patients.update-referrers');
    Route::patch('/patients/{patient}/update-accounts', [PatientController::class, 'updateAccounts'])->name('patients.update-accounts');
    Route::patch('/patients/{patient}/notes', [PatientController::class, 'updateNotes'])->name('patients.notes');
    Route::patch('/patients/{patient}/clinical-alert', [PatientController::class, 'updateClinicalAlert'])->name('patients.clinical-alert');
    Route::post('/patients/{patient}/associates', [PatientController::class, 'addAssociate'])->name('patients.associates');
    Route::post('/patients/{patient}/mdt-meetings', [PatientMdtMeetingController::class, 'store'])->name('patients.mdt-meetings.store');
    Route::delete('/patients/{patient}/mdt-meetings/{meeting}', [PatientMdtMeetingController::class, 'destroy'])->name('patients.mdt-meetings.destroy');
    Route::resource('patients', PatientController::class);

    Route::post('/communications', [CommunicationController::class, 'store'])->name('communications.store');
    Route::patch('/communications/{communication}/complete-follow-up', [CommunicationController::class, 'completeFollowUp'])->name('communications.complete-follow-up');
    Route::delete('/communications/{id}', [CommunicationController::class, 'destroy'])->name('communications.destroy');

    Route::get('/email-intake', [EmailIntakeController::class, 'index'])->name('email-intake.index');
    Route::post('/email-intake/manual', [EmailIntakeController::class, 'storeManual'])->name('email-intake.manual');
    Route::patch('/email-intake/{id}/link', [EmailIntakeController::class, 'link'])->name('email-intake.link');
    Route::delete('/email-intake/{id}', [EmailIntakeController::class, 'destroy'])->name('email-intake.destroy');

    // Phase 2 — Appointments
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/events', [AppointmentController::class, 'fetchEvents'])->name('appointments.events');
    Route::resource('appointments', AppointmentController::class);

    // Phase 2 — Case Notes
    Route::patch('/case-notes/{caseNote}/sign-off', [CaseNoteController::class, 'signOff'])->name('case-notes.sign-off');
    Route::patch('/case-notes/{caseNote}/feedback', [CaseNoteController::class, 'sendFeedback'])->name('case-notes.feedback');
    Route::resource('case-notes', CaseNoteController::class);

    // Phase 3 — Cost Estimations (controller + views existed but were never routed)
    Route::resource('cost-estimations', CostEstimationController::class);

    // C1 — Assessment module
    Route::post('/patients/{patient}/assessment', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');

    // Funding Cycles — view only here per spec Part 4 ("View funding cycles: Admin, Staff
    // (read only)"). Create/edit/delete are admin-only, registered in the admin-only group below.
    // NOTE: /funding-cycles/create (admin-only, registered below) must be matched before this
    // wildcard show route, otherwise Laravel treats "create" as a {fundingCycle} id and 404s.
    Route::get('/funding-cycles', [FundingCycleController::class, 'index'])->name('funding-cycles.index');
});

// Documents — shared across all authenticated roles. Route is open to any
// logged-in user; fine-grained access (who can upload/view/download/delete
// which document) is enforced inside DocumentController via DocumentPolicy,
// so associates and case managers can reach their permitted documents too.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

// Document approval — admin and staff only
Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {
    Route::patch('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
});

// Phase 2 — Associate Portal
Route::middleware(['auth', 'verified', 'role:associate'])->prefix('associate-portal')->name('associate-portal.')->group(function () {
    Route::get('/', [AssociatePortalController::class, 'index'])->name('dashboard');
    Route::get('/patients/{patient}', [AssociatePortalController::class, 'patient'])->name('patient');
    Route::post('/case-notes', [AssociatePortalController::class, 'uploadNote'])->name('upload-note');
    Route::post('/invoices', [AssociatePortalController::class, 'storeInvoice'])->name('invoices.store');
    Route::get('/calendar', [AssociatePortalController::class, 'calendar'])->name('calendar');
});

// Phase 4 — Case Manager Portal
Route::middleware(['auth', 'verified', 'role:case_manager'])->prefix('case-manager-portal')->name('case-manager-portal.')->group(function () {
    Route::get('/', [CaseManagerPortalController::class, 'index'])->name('dashboard');
    Route::get('/patients/{patient}', [CaseManagerPortalController::class, 'patient'])->name('patient');
    Route::post('/patients/{patient}/case-notes', [CaseManagerPortalController::class, 'storeCaseNote'])->name('patient.case-notes.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// A1 — Qualify enquiry (admin only — Q2: LOI always comes to VTA, only admin qualifies)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::post('/enquiries/{enquiry}/qualify', [EnquiryController::class, 'qualify'])->name('enquiries.qualify');
});

// Phase 3 — Finance Screens (admin only)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('associate-invoices', AssociateInvoiceController::class);
    Route::patch('/associate-invoices/{associateInvoice}/status', [AssociateInvoiceController::class, 'updateStatus'])->name('associate-invoices.status');

    Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');
    Route::resource('vta-invoices', VtaInvoiceController::class);
    Route::patch('/vta-invoices/{vtaInvoice}/status', [VtaInvoiceController::class, 'updateStatus'])->name('vta-invoices.status');

    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/reports', [FinanceController::class, 'reports'])->name('finance.reports');

    // Funding Cycles — create/edit/delete are admin-only per spec Part 4.
    Route::get('/funding-cycles/create', [FundingCycleController::class, 'create'])->name('funding-cycles.create');
    Route::post('/funding-cycles', [FundingCycleController::class, 'store'])->name('funding-cycles.store');
    Route::get('/funding-cycles/{fundingCycle}/edit', [FundingCycleController::class, 'edit'])->name('funding-cycles.edit');
    Route::put('/funding-cycles/{fundingCycle}', [FundingCycleController::class, 'update'])->name('funding-cycles.update');
    Route::delete('/funding-cycles/{fundingCycle}', [FundingCycleController::class, 'destroy'])->name('funding-cycles.destroy');
});

// Funding Cycles — show route, registered after /funding-cycles/create above so the
// literal "create" segment isn't swallowed by this {fundingCycle} wildcard.
Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {
    Route::get('/funding-cycles/{fundingCycle}', [FundingCycleController::class, 'show'])->name('funding-cycles.show');
});

// Stage 5 — Settings Screens (admin only)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');

    Route::post('/activity-types', [SettingsController::class, 'storeActivityType'])->name('activity-types.store');
    Route::put('/activity-types/{activityType}', [SettingsController::class, 'updateActivityType'])->name('activity-types.update');
    Route::delete('/activity-types/{activityType}', [SettingsController::class, 'destroyActivityType'])->name('activity-types.destroy');

    Route::post('/document-types', [SettingsController::class, 'storeDocumentType'])->name('document-types.store');
    Route::put('/document-types/{documentType}', [SettingsController::class, 'updateDocumentType'])->name('document-types.update');
    Route::delete('/document-types/{documentType}', [SettingsController::class, 'destroyDocumentType'])->name('document-types.destroy');

    Route::post('/document-permissions', [SettingsController::class, 'updatePermissions'])->name('document-permissions.update');

    Route::get('/associates', [SettingsController::class, 'index'])->name('associates.index');
    Route::post('/associates', [SettingsController::class, 'storeAssociate'])->name('associates.store');
    Route::get('/associates/{associate}', [SettingsController::class, 'showAssociate'])->name('associates.show');
    Route::put('/associates/{associate}', [SettingsController::class, 'updateAssociate'])->name('associates.update');
    Route::post('/associates/{associate}/upload-cv', [SettingsController::class, 'uploadAssociateCv'])->name('associates.upload-cv');
    Route::post('/associates/{associate}/create-login', [SettingsController::class, 'createAssociateLogin'])->name('associates.create-login');
    Route::post('/associates/{associate}/compliance', [SettingsController::class, 'storeCompliance'])->name('associates.compliance.store');

    Route::post('/companies', [SettingsController::class, 'storeCompany'])->name('companies.store');
    Route::put('/companies/{company}', [SettingsController::class, 'updateCompany'])->name('companies.update');

    Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [SettingsController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/reset-password', [SettingsController::class, 'resetUserPassword'])->name('users.reset-password');
});

// Companies listing (admin only)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
});

// Associate Resources — HR & Compliance (admin only, Samy)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/associate-resources', fn() => view('associate-resources.index'))->name('associate-resources.index');
});

// Audit section (developer only)
Route::middleware(['auth', 'verified', 'role:developer'])->prefix('audit')->name('audit.')->group(function () {
    Route::get('/date',      [AuditController::class, 'dateAudit'])->name('date');
    Route::get('/patient',   [AuditController::class, 'patientAudit'])->name('patient');
    Route::get('/associate', [AuditController::class, 'associateAudit'])->name('associate');
});

// G2 — Reports section (admin and developer)
Route::middleware(['auth', 'verified', 'role:admin,developer'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/funding-balance', [ReportsController::class, 'fundingBalanceSummary'])->name('reports.funding-balance');
    Route::get('/reports/financial-summary', [ReportsController::class, 'financialSummary'])->name('reports.financial-summary');
    Route::get('/reports/patients-by-status', [ReportsController::class, 'activePatientsByStatus'])->name('reports.patients-by-status');
    Route::get('/reports/associate-activity', [ReportsController::class, 'associateActivity'])->name('reports.associate-activity');
    Route::get('/reports/master-log', [ReportsController::class, 'masterLog'])->name('reports.master-log');
});

// How It Works — patient journey guide (admin, staff, developer)
Route::middleware(['auth', 'verified', 'role:admin,staff,developer'])->group(function () {
    Route::get('/how-it-works', fn() => view('how-it-works'))->name('how-it-works');
    Route::get('/understanding-each-page', fn() => view('understanding-each-page'))->name('understanding-each-page');
});

// UAT Guide — admin only (Samy tests, developer reads results via Feedback Board)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/uat-guide', [UatGuideController::class, 'show'])->name('uat-guide.show');
    Route::post('/uat-guide', [UatGuideController::class, 'store'])->name('uat-guide.store');
});

// Portal Feedback Board — admin and developer
Route::middleware(['auth', 'verified', 'role:admin,developer'])->prefix('portal-feedback')->name('portal-feedback.')->group(function () {
    Route::get('/', [PortalFeedbackController::class, 'index'])->name('index');
    Route::post('/bugs', [PortalFeedbackController::class, 'storeBug'])->name('bugs.store');
    Route::post('/improvements', [PortalFeedbackController::class, 'storeImprovement'])->name('improvements.store');
    Route::patch('/{item}/respond', [PortalFeedbackController::class, 'respond'])->name('respond');
});

// Switch-user: logs out and shows login page (for clicking from marketing site)
Route::get('/switch-user', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('switch-user');

require __DIR__.'/auth.php';
