<?php

namespace Tests\Feature;

use App\Models\PatientMdtMeeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\MakesTestData;

class Phase2PatientJourneyTest extends TestCase
{
    use RefreshDatabase, MakesTestData;

    // ── 2.1 Admin can view patient list ──────────────────────────────────────
    public function test_2_1_patient_list_accessible_to_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/patients')->assertOk();
    }

    // ── 2.2 Admin can create a patient ───────────────────────────────────────
    public function test_2_2_admin_can_create_patient(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $response = $this->actingAs($admin)->post('/patients', [
            'case_manager_id' => $cm->id,
            'first_name'      => 'Eleanor',
            'last_name'       => 'Winchester',
            'referral_date'   => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'first_name' => 'Eleanor',
            'last_name'  => 'Winchester',
        ]);
    }

    // ── 2.3 Patient show page loads ──────────────────────────────────────────
    public function test_2_3_patient_show_page_loads(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $this->actingAs($admin)->get("/patients/{$patient->id}")->assertOk();
    }

    // ── 2.4 Patient status can be updated to next valid stage ─────────────────
    public function test_2_4_patient_status_moves_forward(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin, ['status' => 'Enquiry Logged']);

        $response = $this->actingAs($admin)->patch("/patients/{$patient->id}/status", [
            'status' => 'Response Sent',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id'     => $patient->id,
            'status' => 'Response Sent',
        ]);
    }

    // ── 2.5 Patient status cannot skip stages ────────────────────────────────
    public function test_2_5_patient_status_cannot_skip_stages(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin, ['status' => 'Enquiry Logged']);

        // Try to jump straight to Treatment Active
        $this->actingAs($admin)->patch("/patients/{$patient->id}/status", [
            'status' => 'Treatment Active',
        ]);

        // Status should still be Enquiry Logged
        $this->assertDatabaseHas('patients', [
            'id'     => $patient->id,
            'status' => 'Enquiry Logged',
        ]);
    }

    // ── 2.6 Associate can be allocated to patient ─────────────────────────────
    public function test_2_6_associate_can_be_allocated_to_patient(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]     = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->post("/patients/{$patient->id}/associates", [
            'associate_id' => $assoc->id,
            'role'         => 'Treatment',
            'start_date'   => now()->toDateString(),
            'is_primary'   => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_associates', [
            'patient_id'   => $patient->id,
            'associate_id' => $assoc->id,
            'role'         => 'Treatment',
        ]);
    }

    // ── 2.7 MDT associate can be allocated ───────────────────────────────────
    public function test_2_7_mdt_associate_can_be_allocated(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]     = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->post("/patients/{$patient->id}/associates", [
            'associate_id' => $assoc->id,
            'role'         => 'MDT',
            'start_date'   => now()->toDateString(),
            'is_primary'   => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_associates', [
            'patient_id'   => $patient->id,
            'associate_id' => $assoc->id,
            'role'         => 'MDT',
        ]);
    }

    // ── 2.8 MDT meeting discussion can be recorded ───────────────────────────
    public function test_2_8_mdt_meeting_discussion_can_be_recorded(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->post("/patients/{$patient->id}/mdt-meetings", [
            'meeting_date' => now()->toDateString(),
            'attendees'    => 'Samy, Nick Hill, Jane CM',
            'discussion'   => 'Patient showing signs of improvement. Recommend 4 more sessions.',
            'outcomes'     => 'Continue treatment. Review in 4 weeks.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_mdt_meetings', [
            'patient_id' => $patient->id,
            'attendees'  => 'Samy, Nick Hill, Jane CM',
        ]);
    }

    // ── 2.9 MDT meeting requires discussion field ─────────────────────────────
    public function test_2_9_mdt_meeting_requires_discussion(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->post("/patients/{$patient->id}/mdt-meetings", [
            'meeting_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['discussion']);
    }

    // ── 2.10 MDT meeting with only required fields (nullable fields omitted) ──
    public function test_2_10_mdt_meeting_with_only_required_fields(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $response = $this->actingAs($admin)->post("/patients/{$patient->id}/mdt-meetings", [
            'meeting_date' => now()->toDateString(),
            'discussion'   => 'Patient reviewed.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_mdt_meetings', [
            'patient_id' => $patient->id,
            'discussion' => 'Patient reviewed.',
            'attendees'  => null,
            'outcomes'   => null,
        ]);
    }

    // ── 2.11 Associate communication can be logged ───────────────────────────
    public function test_2_11_associate_communication_can_be_logged(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm]     = $this->makeCaseManagerUser($company);
        [$assocUser, $assoc] = $this->makeAssociateUser();
        $patient = $this->makePatient($cm, $admin);
        $pa      = $this->allocateAssociate($patient, $assoc);

        $response = $this->actingAs($admin)->post('/communications', [
            'patient_id'          => $patient->id,
            'patient_associate_id'=> $pa->id,
            'type'                => 'Email',
            'direction'           => 'Outbound',
            'subject'             => 'Session schedule update',
            'communication_date'  => now()->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('communications', [
            'patient_id'           => $patient->id,
            'patient_associate_id' => $pa->id,
            'subject'              => 'Session schedule update',
        ]);
    }

    // ── 2.12 Case manager cannot access patient admin pages ───────────────────
    public function test_2_12_case_manager_cannot_access_patient_list(): void
    {
        $company       = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);

        $this->actingAs($cmUser)->get('/patients')->assertForbidden();
    }

    // ── 2.13 Internal notes update saved ─────────────────────────────────────
    public function test_2_13_internal_notes_can_be_updated(): void
    {
        $admin   = $this->makeAdmin();
        $company = $this->makeCompany();
        [$cmUser, $cm] = $this->makeCaseManagerUser($company);
        $patient = $this->makePatient($cm, $admin);

        $this->actingAs($admin)->patch("/patients/{$patient->id}/notes", [
            'notes' => 'Funder prefers email contact only.',
        ]);

        $this->assertDatabaseHas('patients', [
            'id'    => $patient->id,
            'notes' => 'Funder prefers email contact only.',
        ]);
    }
}
