<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Samy Selvanayagam',
            'email' => 'samy@vestibulartherapyassociates.co.uk',
            'password' => Hash::make('ChangeMe2026!'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jai Anand',
            'email' => 'jai@vestibulartherapyassociates.co.uk',
            'password' => Hash::make('ChangeMe2026!'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Sheeba Rossewilliam',
            'email' => 'sheeba@vestibulartherapyassociates.co.uk',
            'password' => Hash::make('ChangeMe2026!'),
            'role' => 'staff',
            'is_active' => true,
        ]);
    }
}
