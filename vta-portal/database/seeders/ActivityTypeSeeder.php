<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Assessment', 'sort_order' => 1],
            ['name' => 'Treatment', 'sort_order' => 2],
            ['name' => 'Supervision Session', 'sort_order' => 3],
            ['name' => 'Report Writing', 'sort_order' => 4],
            ['name' => 'MDT Meeting', 'sort_order' => 5],
            ['name' => 'Review', 'sort_order' => 6],
            ['name' => 'Joint Visit', 'sort_order' => 7],
        ];

        foreach ($types as $type) {
            ActivityType::create($type);
        }
    }
}
