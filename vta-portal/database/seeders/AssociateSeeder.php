<?php

namespace Database\Seeders;

use App\Models\Associate;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssociateSeeder extends Seeder
{
    public function run(): void
    {
        $associates = [
            ['name' => 'Kate Bryce',              'region' => 'North East England',     'speciality' => 'Falls and Balance Rehabilitation'],
            ['name' => 'Anna Bennett',            'region' => 'Yorkshire',              'speciality' => 'Advanced Vestibular Physiotherapy'],
            ['name' => 'Lewis Brennan',           'region' => 'London and Cambridgeshire', 'speciality' => 'Musculoskeletal and Vestibular Rehabilitation'],
            ['name' => 'Georgios Tsiknas',        'region' => 'West Midlands',          'speciality' => 'Specialist Vestibular Physiotherapy'],
            ['name' => 'Ileana Dascalu',          'region' => 'London',                 'speciality' => 'Paediatric and Adult Rehabilitation'],
            ['name' => 'Nick Hill',               'region' => 'North West England',     'speciality' => 'Specialist Vestibular Physiotherapy'],
            ['name' => 'Sultana Parvin',          'region' => 'Manchester',            'speciality' => 'Specialist Vestibular Physiotherapy'],
            ['name' => 'Sahash Palanisamy',       'region' => 'Dorset',                 'speciality' => 'Specialist Vestibular Physiotherapy'],
            ['name' => 'Samy Selvanayagam',       'region' => 'Nationwide',             'speciality' => 'Consultant Vestibular Physiotherapy'],
        ];

        foreach ($associates as $data) {
            $data['is_active'] = true;
            Associate::create($data);
        }

        $associateUser = User::where('email', 'associate@vta.com')->first();
        if ($associateUser) {
            $assoc = Associate::where('name', 'Kate Bryce')->first();
            if ($assoc) {
                $assoc->update(['user_id' => $associateUser->id]);
            }
        }
    }
}
