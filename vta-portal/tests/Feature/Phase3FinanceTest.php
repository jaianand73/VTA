<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase3FinanceTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 3.1 Finance index page accessible to admin ───────────────────────────
    public function test_3_1_finance_index_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/finance')->assertOk();
    }

    // ── 3.2 Finance page not accessible to case manager ───────────────────────
    public function test_3_2_finance_not_accessible_to_case_manager(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/finance')->assertForbidden();
    }

    // ── 3.3 Finance page not accessible to associate ──────────────────────────
    public function test_3_3_finance_not_accessible_to_associate(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/finance')->assertForbidden();
    }

    // ── 3.4 VTA Invoice can be created ───────────────────────────────────────
    public function test_3_4_vta_invoice_can_be_created(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);
        $cycle   = $this->makeFundingCycle($patient);

        $response = $this->actingAs($admin)->post('/vta-invoices', [
            'patient_id'       => $patient->id,
            'funding_cycle_id' => $cycle->id,
            'invoice_date'     => now()->toDateString(),
            'recipient_type'   => 'Insurance Company',
            'recipient_name'   => 'AXA Test',
            'total_amount'     => 750.00,
            'status'           => 'Draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vta_invoices', [
            'patient_id'       => $patient->id,
            'funding_cycle_id' => $cycle->id,
            'total_amount'     => 750.00,
        ]);
    }

    // ── 3.5 VTA Invoice status can move Draft → Sent (requires document) ──────
    public function test_3_5_vta_invoice_status_moves_draft_to_sent(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);
        $cycle   = $this->makeFundingCycle($patient);
        // Pre-populate document_path so the "must upload doc" guard passes
        $invoice = $this->makeVtaInvoice($patient, $cycle, $admin, [
            'status'        => 'Draft',
            'document_path' => 'vta-invoices/test-invoice.pdf',
        ]);

        $response = $this->actingAs($admin)->patch("/vta-invoices/{$invoice->id}/status", [
            'status' => 'Sent',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vta_invoices', [
            'id'     => $invoice->id,
            'status' => 'Sent',
        ]);
    }

    // ── 3.6 VTA Invoice status can move Sent → Paid ──────────────────────────
    public function test_3_6_vta_invoice_status_moves_sent_to_paid(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);
        $cycle   = $this->makeFundingCycle($patient);
        $invoice = $this->makeVtaInvoice($patient, $cycle, $admin, [
            'status'        => 'Sent',
            'document_path' => 'vta-invoices/test-invoice.pdf',
        ]);

        $response = $this->actingAs($admin)->patch("/vta-invoices/{$invoice->id}/status", [
            'status'       => 'Paid',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vta_invoices', [
            'id'     => $invoice->id,
            'status' => 'Paid',
        ]);
    }

    // ── 3.7 Funding cycle created with correct balance ───────────────────────
    public function test_3_7_funding_cycle_stores_approved_amount(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $this->actingAs($admin)->post('/funding-cycles', [
            'patient_id'      => $patient->id,
            'cycle_number'    => 1,
            'funder_name'     => 'AXA Insurance',
            'approved_amount' => 10000.00,
            'approval_date'   => now()->toDateString(),
            'is_active'       => true,
        ]);

        $this->assertDatabaseHas('funding_cycles', [
            'patient_id'      => $patient->id,
            'approved_amount' => 10000.00,
            'funder_name'     => 'AXA Insurance',
        ]);
    }

    // ── 3.8 VTA Invoice list page loads ──────────────────────────────────────
    public function test_3_8_vta_invoice_list_loads(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/vta-invoices')->assertOk();
    }

    // ── 3.9 Associate invoice list page loads ────────────────────────────────
    public function test_3_9_associate_invoice_list_loads(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/associate-invoices')->assertOk();
    }
}
