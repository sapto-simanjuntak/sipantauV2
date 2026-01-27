<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProblemCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Hardware',
                'category_code' => 'HW',
                'requires_validation' => false,
                'default_sla_hours' => 24,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'Software',
                'category_code' => 'SW',
                'requires_validation' => false,
                'default_sla_hours' => 24,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'Jaringan',
                'category_code' => 'NET',
                'requires_validation' => false,
                'default_sla_hours' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'PABX/Telepon',
                'category_code' => 'PABX',
                'requires_validation' => false,
                'default_sla_hours' => 12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'CCTV',
                'category_code' => 'CCTV',
                'requires_validation' => false,
                'default_sla_hours' => 24,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'Access Control',
                'category_code' => 'AC',
                'requires_validation' => false,
                'default_sla_hours' => 12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'Pengembangan/Request Fitur',
                'category_code' => 'DEV',
                'requires_validation' => true, // Perlu approval!
                'default_sla_hours' => 720, // 30 hari
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_name' => 'Email',
                'category_code' => 'EMAIL',
                'requires_validation' => false,
                'default_sla_hours' => 12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('problem_categories')->insert($categories);
    }
}
