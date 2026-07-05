<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\AssociateComplianceDocument;
use App\Models\AssociateInvoice;
use App\Models\CaseNote;
use App\Models\Communication;
use App\Models\Document;
use App\Models\Enquiry;
use App\Models\FundingCycle;
use App\Models\Patient;
use App\Models\PatientMdtMeeting;
use App\Models\VtaInvoice;
use App\Observers\AppointmentObserver;
use App\Observers\AssociateComplianceDocumentObserver;
use App\Observers\AssociateInvoiceObserver;
use App\Observers\CaseNoteObserver;
use App\Observers\CommunicationObserver;
use App\Observers\DocumentObserver;
use App\Observers\EnquiryObserver;
use App\Observers\FundingCycleObserver;
use App\Observers\PatientMdtMeetingObserver;
use App\Observers\PatientObserver;
use App\Observers\VtaInvoiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Enquiry::observe(EnquiryObserver::class);
        Patient::observe(PatientObserver::class);
        CaseNote::observe(CaseNoteObserver::class);
        Document::observe(DocumentObserver::class);
        Appointment::observe(AppointmentObserver::class);
        AssociateInvoice::observe(AssociateInvoiceObserver::class);
        VtaInvoice::observe(VtaInvoiceObserver::class);
        FundingCycle::observe(FundingCycleObserver::class);
        Communication::observe(CommunicationObserver::class);
        AssociateComplianceDocument::observe(AssociateComplianceDocumentObserver::class);
        PatientMdtMeeting::observe(PatientMdtMeetingObserver::class);
    }
}
