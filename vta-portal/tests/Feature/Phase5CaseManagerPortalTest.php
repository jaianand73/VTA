<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase5CaseManagerPortalTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 5.1 Case manager portal dashboard loads ───────────────────────────────
    public function test_5_1_case_manager_portal_dashboard_loads(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/case-manager-portal')->assertOk();
    }

    // ── 5.2 Case manager cannot access main admin pages ───────────────────────
    public function test_5_2_case_manager_cannot_access_admin_pages(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/patients')->assertForbidden();
        $this->actingAs($cmUser)->get('/finance')->assertForbidden();
        $this->actingAs($cmUser)->get('/settings')->assertForbidden();
        $this->actingAs($cmUser)->get('/enquiries')->assertForbidden();
    }

    // ── 5.3 Case manager cannot access associate portal ───────────────────────
    public function test_5_3_case_manager_cannot_access_associate_portal(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/associate-portal')->assertForbidden();
    }

    // ── 5.4 Case manager only sees their company's patient via individual pages ─
    public function test_5_4_case_manager_only_sees_own_patients(): void
    {
        $admin = $this->makeAdmin();

        // Company A with a case manager
        $companyA        = $this->makeCompany();
        [$cmUserA, $cmA] = $this->makeCaseManagerUser($companyA);
        $patientA        = $this->makePatient($cmA, $admin, ['first_name' => 'AlphaXYZ', 'last_name' => 'Patient']);

        // Company B with a different case manager
        $companyB        = $this->makeCompany();
        [$cmUserB, $cmB] = $this->makeCaseManagerUser($companyB);
        $patientB        = $this->makePatient($cmB, $admin, ['first_name' => 'BetaXYZ', 'last_name' => 'Patient']);

        // Case manager A can view their own patient
        $responseOwn = $this->actingAs($cmUserA)->get("/case-manager-portal/patients/{$patientA->id}");
        $responseOwn->assertOk();
        $responseOwn->assertSee('AlphaXYZ');

        // Case manager A cannot view another CM's patient
        $responseOther = $this->actingAs($cmUserA)->get("/case-manager-portal/patients/{$patientB->id}");
        $this->assertTrue(
            $responseOther->status() === 403 || $responseOther->status() === 302,
            'Expected 403 or redirect for another company patient'
        );
    }

    // ── 5.5 How It Works page accessible to admin ─────────────────────────────
    public function test_5_5_how_it_works_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/how-it-works')->assertOk();
    }

    // ── 5.6 Understanding Each Page accessible to admin ───────────────────────
    public function test_5_6_understanding_each_page_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/understanding-each-page')->assertOk();
    }

    // ── 5.7 How It Works not accessible to case manager ───────────────────────
    public function test_5_7_how_it_works_not_accessible_to_case_manager(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/how-it-works')->assertForbidden();
    }

    // ── 5.8 How It Works not accessible to associate ──────────────────────────
    public function test_5_8_how_it_works_not_accessible_to_associate(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/how-it-works')->assertForbidden();
    }
}
