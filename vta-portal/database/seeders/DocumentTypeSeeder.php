<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Letter of Instruction', 'sort_order' => 1],
            ['name' => 'INA', 'sort_order' => 2],
            ['name' => 'Medical Records', 'sort_order' => 3],
            ['name' => 'Cost Estimation', 'sort_order' => 4],
            ['name' => 'Assessment Report', 'sort_order' => 5],
            ['name' => 'Progress Report', 'sort_order' => 6],
            ['name' => 'Discharge Report', 'sort_order' => 7],
            ['name' => 'Supervision Note', 'sort_order' => 8],
            ['name' => 'Funding Approval', 'sort_order' => 9],
            ['name' => 'Associate Invoice', 'sort_order' => 10],
            ['name' => 'VTA Invoice', 'sort_order' => 11],
            ['name' => 'Case Close Summary', 'sort_order' => 12],
            ['name' => 'NDA', 'sort_order' => 13],
            ['name' => 'Brochure / Materials', 'sort_order' => 14],
            ['name' => 'Clinical Notes', 'sort_order' => 15],
            ['name' => 'Correspondence', 'sort_order' => 16],
            ['name' => 'Other', 'sort_order' => 17],
        ];

        foreach ($types as $type) {
            DocumentType::create($type);
        }
    }
}
