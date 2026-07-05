<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase6SettingsEdgeCasesTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 6.1 Settings index accessible to admin ────────────────────────────────
    public function test_6_1_settings_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/settings')->assertOk();
    }

    // ── 6.2 Settings not accessible to staff ─────────────────────────────────
    public function test_6_2_settings_not_accessible_to_staff(): void
    {
        $staff = $this->makeStaff();

        $this->actingAs($staff)->get('/settings')->assertForbidden();
    }

    // ── 6.3 Settings page (includes associate section) accessible to admin ───
    public function test_6_3_associate_list_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/settings')->assertOk();
    }

    // ── 6.4 Associate show page loads (CV section visible) ────────────────────
    public function test_6_4_associate_show_page_loads(): void
    {
        $admin = $this->makeAdmin();
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($admin)->get("/settings/associates/{$assoc->id}")->assertOk();
    }

    // ── 6.5 Associate show page contains CV section ───────────────────────────
    public function test_6_5_associate_show_has_cv_section(): void
    {
        $admin = $this->makeAdmin();
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $response = $this->actingAs($admin)->get("/settings/associates/{$assoc->id}");

        $response->assertSee('CV');
    }

    // ── 6.6 Feedback index accessible to admin ────────────────────────────────
    public function test_6_6_feedback_index_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/portal-feedback')->assertOk();
    }

    // ── 6.7 MDT meeting section visible on patient page ───────────────────────
    public function test_6_7_mdt_meeting_section_visible_on_patient_page(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->get("/patients/{$patient->id}");

        $response->assertSee('MDT Meeting Discussions');
    }

    // ── 6.8 Associate communications section visible on patient page ──────────
    public function test_6_8_associate_comms_section_visible_on_patient_page(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->get("/patients/{$patient->id}");

        $response->assertSee('Associate Communications');
    }

    // ── 6.9 MDT meeting can be deleted by admin ───────────────────────────────
    public function test_6_9_admin_can_delete_mdt_meeting(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $meeting = \App\Models\PatientMdtMeeting::create([
            'patient_id'   => $patient->id,
            'meeting_date' => now()->toDateString(),
            'discussion'   => 'Test discussion.',
            'created_by'   => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete("/patients/{$patient->id}/mdt-meetings/{$meeting->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('patient_mdt_meetings', ['id' => $meeting->id]);
    }

    // ── 6.10 Reports page accessible to admin ─────────────────────────────────
    public function test_6_10_reports_page_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/reports')->assertOk();
    }

    // ── 6.11 Case notes index accessible to admin ─────────────────────────────
    public function test_6_11_case_notes_index_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/case-notes')->assertOk();
    }

    // ── 6.12 Companies index accessible to admin ──────────────────────────────
    public function test_6_12_companies_index_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/companies')->assertOk();
    }

    // ── 6.13 Dashboard loads for admin ────────────────────────────────────────
    public function test_6_13_dashboard_loads_for_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/')->assertOk();
    }

    // ── 6.14 Dashboard loads for staff ────────────────────────────────────────
    public function test_6_14_dashboard_loads_for_staff(): void
    {
        $staff = $this->makeStaff();

        $this->actingAs($staff)->get('/')->assertOk();
    }
}
