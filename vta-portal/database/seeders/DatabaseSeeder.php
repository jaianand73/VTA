<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@vta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@vta.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Associate User',
            'email' => 'associate@vta.com',
            'password' => bcrypt('password'),
            'role' => 'associate',
            'is_active' => true,
        ]);

        $this->call([
            UserSeeder::class,
            ActivityTypeSeeder::class,
            DocumentTypeSeeder::class,
            AssociateSeeder::class,
        ]);
    }
}
