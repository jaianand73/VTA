<?php

use App\Http\Controllers\AppointmentController;
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
use App\Http\Controllers\AssociateInvoiceController;
use App\Http\Controllers\CaseManagerPortalController;
use App\Http\Controllers\CostEstimationController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FundingCycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VtaInvoiceController;
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
    Route::patch('/patients/{patient}/notes', [PatientController::class, 'updateNotes'])->name('patients.notes');
    Route::post('/patients/{patient}/associates', [PatientController::class, 'addAssociate'])->name('patients.associates');
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
    Route::resource('case-notes', CaseNoteController::class);

    // Phase 3 — Cost Estimations (controller + views existed but were never routed)
    Route::resource('cost-estimations', CostEstimationController::class);

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

// Phase 2 — Associate Portal
Route::middleware(['auth', 'verified', 'role:associate'])->prefix('associate-portal')->name('associate-portal.')->group(function () {
    Route::get('/', [AssociatePortalController::class, 'index'])->name('dashboard');
    Route::get('/patients/{patient}', [AssociatePortalController::class, 'patient'])->name('patient');
    Route::post('/case-notes', [AssociatePortalController::class, 'uploadNote'])->name('upload-note');
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

// Phase 3 — Finance Screens (admin only)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('associate-invoices', AssociateInvoiceController::class);
    Route::patch('/associate-invoices/{associateInvoice}/status', [AssociateInvoiceController::class, 'updateStatus'])->name('associate-invoices.status');

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

    Route::post('/associates', [SettingsController::class, 'storeAssociate'])->name('associates.store');
    Route::put('/associates/{associate}', [SettingsController::class, 'updateAssociate'])->name('associates.update');
    Route::post('/associates/{associate}/create-login', [SettingsController::class, 'createAssociateLogin'])->name('associates.create-login');

    Route::post('/companies', [SettingsController::class, 'storeCompany'])->name('companies.store');
    Route::put('/companies/{company}', [SettingsController::class, 'updateCompany'])->name('companies.update');

    Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [SettingsController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/reset-password', [SettingsController::class, 'resetUserPassword'])->name('users.reset-password');
});

// Switch-user: logs out and shows login page (for clicking from marketing site)
Route::get('/switch-user', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('switch-user');

require __DIR__.'/auth.php';
