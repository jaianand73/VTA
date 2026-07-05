<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase1EnquiryToPatientTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 1.1 Create new enquiry with required fields ──────────────────────────
    public function test_1_1_admin_can_create_enquiry(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();

        $response = $this->actingAs($admin)->post('/enquiries', [
            'enquirer_name' => 'Test Referrer',
            'company_id'    => $company->id,
            'source'        => 'Email',
            'enquiry_date'  => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', ['enquirer_name' => 'Test Referrer']);
    }

    // ── 1.2 Staff can also create enquiry ────────────────────────────────────
    public function test_1_2_staff_can_create_enquiry(): void
    {
        $staff   = $this->makeStaff();
        $company = $this->makeCompany();

        $response = $this->actingAs($staff)->post('/enquiries', [
            'enquirer_name' => 'Staff Referrer',
            'company_id'    => $company->id,
            'source'        => 'Phone',
            'enquiry_date'  => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', ['enquirer_name' => 'Staff Referrer']);
    }

    // ── 1.3 Enquiry requires mandatory fields ────────────────────────────────
    public function test_1_3_enquiry_requires_name_and_company(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/enquiries', []);

        $response->assertSessionHasErrors(['enquirer_name', 'company_id']);
    }

    // ── 1.4 Log communication against enquiry ────────────────────────────────
    public function test_1_4_can_log_communication_against_enquiry(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        $enquiry = $this->makeEnquiry($admin, $company);

        $response = $this->actingAs($admin)->post('/communications', [
            'enquiry_id'         => $enquiry->id,
            'type'               => 'Email',
            'direction'          => 'Inbound',
            'subject'            => 'Initial enquiry response',
            'communication_date' => now()->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('communications', [
            'enquiry_id' => $enquiry->id,
            'subject'    => 'Initial enquiry response',
        ]);
    }

    // ── 1.5 Communication with follow-up date saved correctly ────────────────
    public function test_1_5_communication_follow_up_date_saved(): void
    {
        $admin    = $this->makeAdmin();
        $company  = $this->makeCompany();
        $enquiry  = $this->makeEnquiry($admin, $company);
        $followUp = now()->addDays(3)->toDateString();

        $this->actingAs($admin)->post('/communications', [
            'enquiry_id'         => $enquiry->id,
            'type'               => 'Phone',
            'direction'          => 'Outbound',
            'subject'            => 'Follow-up call',
            'communication_date' => now()->format('Y-m-d H:i'),
            'follow_up_date'     => $followUp,
        ]);

        $this->assertDatabaseHas('communications', [
            'enquiry_id'     => $enquiry->id,
            'follow_up_date' => $followUp,
        ]);
    }

    // ── 1.6 Enquiry list accessible to admin ─────────────────────────────────
    public function test_1_6_enquiry_list_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/enquiries')->assertOk();
    }

    // ── 1.7 Enquiry list accessible to staff ─────────────────────────────────
    public function test_1_7_enquiry_list_accessible_to_staff(): void
    {
        $staff = $this->makeStaff();

        $this->actingAs($staff)->get('/enquiries')->assertOk();
    }

    // ── 1.8 Unauthenticated user redirected to login ──────────────────────────
    public function test_1_8_unauthenticated_redirected_from_enquiries(): void
    {
        $this->get('/enquiries')->assertRedirect('/login');
    }

    // ── 1.9 Case manager cannot access enquiries ──────────────────────────────
    public function test_1_9_case_manager_cannot_access_enquiries(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/enquiries')->assertForbidden();
    }

    // ── 1.10 Associate cannot access enquiries ────────────────────────────────
    public function test_1_10_associate_cannot_access_enquiries(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/enquiries')->assertForbidden();
    }
}
