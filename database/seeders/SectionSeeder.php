<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we're using the tenant connection
        DB::setDefaultConnection('tenant');
        
        $sections = [
            [
                'name' => 'Breads',
                'description' => 'Fresh baked breads and rolls',
                'status' => 'active',
            ],
            [
                'name' => 'Pastries',
                'description' => 'Delicious pastries and croissants',
                'status' => 'active',
            ],
            [
                'name' => 'Cakes',
                'description' => 'Custom and specialty cakes',
                'status' => 'active',
            ],
            [
                'name' => 'Cookies',
                'description' => 'Homemade cookies and biscuits',
                'status' => 'active',
            ],
            [
                'name' => 'Desserts',
                'description' => 'Special desserts and sweet treats',
                'status' => 'active',
            ],
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }
        
        // Reset to default connection
        DB::setDefaultConnection('mysql');
    }
} 