<?php

namespace Tests\Helpers;

use App\Models\Associate;
use App\Models\CaseManager;
use App\Models\Company;
use App\Models\Enquiry;
use App\Models\FundingCycle;
use App\Models\Patient;
use App\Models\PatientAssociate;
use App\Models\User;
use App\Models\VtaInvoice;
use Illuminate\Support\Facades\Hash;

trait MakesTestData
{
    protected function makeAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'name'              => 'Test Admin',
            'email'             => 'admin' . uniqid() . '@test.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'is_active'         => true,
        ], $overrides));
    }

    protected function makeStaff(array $overrides = []): User
    {
        return User::create(array_merge([
            'name'              => 'Test Staff',
            'email'             => 'staff' . uniqid() . '@test.com',
            'password'          => Hash::make('password'),
            'role'              => 'staff',
            'email_verified_at' => now(),
            'is_active'         => true,
        ], $overrides));
    }

    protected function makeCompany(): Company
    {
        return Company::create([
            'name'      => 'Test Insurance Co ' . uniqid(),
            'type'      => 'Insurance',
            'email'     => 'company' . uniqid() . '@test.com',
            'status'    => 'Active',
            'is_active' => true,
        ]);
    }

    protected function makeCaseManagerUser(Company $company): array
    {
        $user = User::create([
            'name'              => 'Test Case Manager',
            'email'             => 'cm' . uniqid() . '@test.com',
            'password'          => Hash::make('password'),
            'role'              => 'case_manager',
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        $cm = CaseManager::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'first_name' => 'Test',
            'last_name'  => 'CM',
            'email'      => $user->email,
            'status'     => 'Active',
        ]);

        return [$user, $cm];
    }

    protected function makeAssociateUser(): array
    {
        $user = User::create([
            'name'              => 'Test Associate',
            'email'             => 'assoc' . uniqid() . '@test.com',
            'password'          => Hash::make('password'),
            'role'              => 'associate',
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        $associate = Associate::create([
            'user_id'    => $user->id,
            'name'       => 'Test Associate',
            'email'      => $user->email,
            'region'     => 'London',
            'speciality' => 'Physiotherapy',
            'is_active'  => true,
        ]);

        return [$user, $associate];
    }

    protected function makeEnquiry(User $creator, Company $company, array $overrides = []): Enquiry
    {
        return Enquiry::create(array_merge([
            'enquirer_name' => 'John Doe',
            'company_id'    => $company->id,
            'source'        => 'Email',
            'enquiry_date'  => now()->toDateString(),
            'status'        => 'New',
            'created_by'    => $creator->id,
        ], $overrides));
    }

    protected function makePatient(CaseManager $cm, User $creator, array $overrides = []): Patient
    {
        return Patient::create(array_merge([
            'case_manager_id' => $cm->id,
            'first_name'      => 'Jane',
            'last_name'       => 'Test',
            'referral_date'   => now()->toDateString(),
            'status'          => 'Enquiry Logged',
            'created_by'      => $creator->id,
        ], $overrides));
    }

    protected function makeFundingCycle(Patient $patient, array $overrides = []): FundingCycle
    {
        return FundingCycle::create(array_merge([
            'patient_id'      => $patient->id,
            'cycle_number'    => 1,
            'funder_name'     => 'Test Insurance',
            'approved_amount' => 5000.00,
            'approval_date'   => now()->toDateString(),
            'is_active'       => true,
        ], $overrides));
    }

    protected function makeVtaInvoice(Patient $patient, FundingCycle $cycle, User $creator, array $overrides = []): VtaInvoice
    {
        return VtaInvoice::create(array_merge([
            'patient_id'       => $patient->id,
            'funding_cycle_id' => $cycle->id,
            'invoice_number'   => 'VTA-TEST-' . uniqid(),
            'invoice_date'     => now()->toDateString(),
            'recipient_type'   => 'Insurance Company',
            'recipient_name'   => 'Test Insurer',
            'total_amount'     => 500.00,
            'status'           => 'Draft',
            'created_by'       => $creator->id,
        ], $overrides));
    }

    protected function allocateAssociate(Patient $patient, Associate $associate, string $role = 'Treatment'): PatientAssociate
    {
        return PatientAssociate::create([
            'patient_id'   => $patient->id,
            'associate_id' => $associate->id,
            'role'         => $role,
            'start_date'   => now()->toDateString(),
            'is_primary'   => true,
        ]);
    }
}
