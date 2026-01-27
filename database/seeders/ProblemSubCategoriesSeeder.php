<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProblemSubCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $subCategories = [
            // HARDWARE (problem_category_id = 1)
            ['problem_category_id' => 1, 'sub_category_name' => 'PC/Komputer Mati'],
            ['problem_category_id' => 1, 'sub_category_name' => 'PC/Komputer Lambat'],
            ['problem_category_id' => 1, 'sub_category_name' => 'PC/Komputer Hang/Freeze'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Printer Tidak Bisa Print'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Printer Paper Jam'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Printer Hasil Buram/Pudar'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Monitor Tidak Ada Tampilan'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Monitor Bergaris/Berkedip'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Keyboard/Mouse Rusak'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Scanner Tidak Terdeteksi'],
            ['problem_category_id' => 1, 'sub_category_name' => 'UPS Bermasalah'],
            ['problem_category_id' => 1, 'sub_category_name' => 'Hardware Lainnya'],

            // SOFTWARE (problem_category_id = 2)
            ['problem_category_id' => 2, 'sub_category_name' => 'SIMRS Error/Tidak Bisa Login'],
            ['problem_category_id' => 2, 'sub_category_name' => 'SIMRS Lambat'],
            ['problem_category_id' => 2, 'sub_category_name' => 'SIMRS Data Tidak Muncul'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Aplikasi Radiologi Bermasalah'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Aplikasi Lab Bermasalah'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Aplikasi Farmasi Bermasalah'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Aplikasi Billing Error'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Microsoft Office Bermasalah'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Antivirus Expired'],
            ['problem_category_id' => 2, 'sub_category_name' => 'Software Lainnya'],

            // JARINGAN (problem_category_id = 3)
            ['problem_category_id' => 3, 'sub_category_name' => 'Internet Tidak Bisa Akses'],
            ['problem_category_id' => 3, 'sub_category_name' => 'Internet Lambat'],
            ['problem_category_id' => 3, 'sub_category_name' => 'WiFi Tidak Terdeteksi'],
            ['problem_category_id' => 3, 'sub_category_name' => 'WiFi Lemah/Putus-putus'],
            ['problem_category_id' => 3, 'sub_category_name' => 'Tidak Bisa Akses Folder Share'],
            ['problem_category_id' => 3, 'sub_category_name' => 'Tidak Bisa Remote Desktop'],
            ['problem_category_id' => 3, 'sub_category_name' => 'Masalah Jaringan Lainnya'],
        ];

        DB::table('problem_sub_categories')->insert($subCategories);
    }
}
