<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase4AssociatePortalTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 4.1 Associate portal dashboard accessible ─────────────────────────────
    public function test_4_1_associate_portal_dashboard_loads(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/associate-portal')->assertOk();
    }

    // ── 4.2 Associate cannot access main patient list ─────────────────────────
    public function test_4_2_associate_cannot_access_main_patient_list(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/patients')->assertForbidden();
    }

    // ── 4.3 Associate cannot access finance ───────────────────────────────────
    public function test_4_3_associate_cannot_access_finance(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/finance')->assertForbidden();
    }

    // ── 4.4 Associate cannot access enquiries ─────────────────────────────────
    public function test_4_4_associate_cannot_access_enquiries(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/enquiries')->assertForbidden();
    }

    // ── 4.5 Associate cannot access settings ──────────────────────────────────
    public function test_4_5_associate_cannot_access_settings(): void
    {
        [$assocUser, $assoc] = $this->makeAssociateUser();

        $this->actingAs($assocUser)->get('/settings')->assertForbidden();
    }

    // ── 4.6 Associate can only see their allocated patients via dashboard ──────
    public function test_4_6_associate_only_sees_allocated_patients(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]       = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();

        // Patient allocated to this associate
        $myPatient    = $this->makePatient($cm, $admin, ['first_name' => 'AllocatedXYZ', 'last_name' => 'Patient']);
        $this->allocateAssociate($myPatient, $assoc);

        // Patient NOT allocated to this associate
        $otherPatient = $this->makePatient($cm, $admin, ['first_name' => 'UnallocatedXYZ', 'last_name' => 'Patient']);

        // Associate can view their allocated patient's detail page
        $response = $this->actingAs($assocUser)->get("/associate-portal/patients/{$myPatient->id}");
        $response->assertOk();
        $response->assertSee('AllocatedXYZ');

        // Associate cannot view unallocated patient
        $response2 = $this->actingAs($assocUser)->get("/associate-portal/patients/{$otherPatient->id}");
        $this->assertTrue(
            $response2->status() === 403 || $response2->status() === 302,
            'Expected 403 or redirect for unallocated patient'
        );
    }

    // ── 4.7 Associate can submit a case note ──────────────────────────────────
    public function test_4_7_associate_can_submit_case_note(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]       = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin, ['status' => 'Treatment Active']);
        $this->allocateAssociate($patient, $assoc);

        $response = $this->actingAs($assocUser)->post('/associate-portal/case-notes', [
            'patient_id'   => $patient->id,
            'session_date' => now()->toDateString(),
            'note_type'    => 'Session Note',
            'content'      => 'Patient attended session. Good progress with balance exercises.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('case_notes', [
            'patient_id'  => $patient->id,
            'associate_id' => $assoc->id,
            'note_type'   => 'Session Note',
        ]);
    }

    // ── 4.8 Submitted case note is pending review ─────────────────────────────
    public function test_4_8_submitted_case_note_is_pending_review(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]       = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin, ['status' => 'Treatment Active']);
        $this->allocateAssociate($patient, $assoc);

        $this->actingAs($assocUser)->post('/associate-portal/case-notes', [
            'patient_id'   => $patient->id,
            'session_date' => now()->toDateString(),
            'note_type'    => 'Session Note',
            'content'      => 'Session notes here.',
        ]);

        $this->assertDatabaseHas('case_notes', [
            'patient_id'   => $patient->id,
            'associate_id' => $assoc->id,
            'is_signed_off' => 0,
        ]);
    }

    // ── 4.9 Associate cannot submit note for unallocated patient ──────────────
    public function test_4_9_associate_cannot_submit_note_for_unallocated_patient(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]       = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin); // Not allocated to this associate

        $response = $this->actingAs($assocUser)->post('/associate-portal/case-notes', [
            'patient_id'   => $patient->id,
            'session_date' => now()->toDateString(),
            'note_type'    => 'Session Note',
            'content'      => 'Should not be allowed.',
        ]);

        // Should be forbidden or redirect with error
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Expected 403 or redirect for unallocated patient'
        );
        $this->assertDatabaseMissing('case_notes', [
            'patient_id' => $patient->id,
            'content'    => 'Should not be allowed.',
        ]);
    }
}
