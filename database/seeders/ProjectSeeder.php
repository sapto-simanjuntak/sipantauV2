<?php

namespace Database\Seeders;

use App\Models\Modul\Task;
use App\Models\Modul\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/ProjectSeeder.php
    public function run()
    {
        // Insert data proyek
        $projects = [
            ['name' => 'Proyek A', 'description' => 'Proyek A merupakan proyek besar', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => Project::STATUS_NOT_STARTED],
            ['name' => 'Proyek B', 'description' => 'Proyek B adalah proyek kecil', 'start_date' => '2024-02-01', 'end_date' => '2024-06-30', 'status' => Project::STATUS_IN_PROGRESS],
            ['name' => 'Proyek C', 'description' => 'Proyek C untuk pengembangan app', 'start_date' => '2024-03-01', 'end_date' => '2024-09-30', 'status' => Project::STATUS_COMPLETED],
            ['name' => 'Proyek D', 'description' => 'Proyek D adalah proyek penelitian', 'start_date' => '2024-04-01', 'end_date' => '2024-10-31', 'status' => Project::STATUS_NOT_STARTED],
            ['name' => 'Proyek E', 'description' => 'Proyek E adalah proyek uji coba', 'start_date' => '2024-05-01', 'end_date' => '2024-12-31', 'status' => Project::STATUS_IN_PROGRESS],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Menambahkan tugas untuk setiap proyek
            Task::create([
                'project_id' => $project->id,
                'title' => 'Tugas A',
                'description' => 'Deskripsi Tugas A untuk ' . $project->name,
                'status' => Task::STATUS_NOT_STARTED,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Tugas B',
                'description' => 'Deskripsi Tugas B untuk ' . $project->name,
                'status' => Task::STATUS_IN_PROGRESS,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Tugas C',
                'description' => 'Deskripsi Tugas C untuk ' . $project->name,
                'status' => Task::STATUS_COMPLETED,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Tugas D',
                'description' => 'Deskripsi Tugas D untuk ' . $project->name,
                'status' => Task::STATUS_NOT_STARTED,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Tugas E',
                'description' => 'Deskripsi Tugas E untuk ' . $project->name,
                'status' => Task::STATUS_IN_PROGRESS,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
            ]);
        }

        // Insert data project-user
        DB::table('project_user')->insert([
            ['project_id' => 1, 'user_id' => 1],
            ['project_id' => 1, 'user_id' => 2],
            ['project_id' => 2, 'user_id' => 2],
            ['project_id' => 3, 'user_id' => 3],
            ['project_id' => 4, 'user_id' => 4],
            ['project_id' => 4, 'user_id' => 5],
            ['project_id' => 5, 'user_id' => 1],
            ['project_id' => 5, 'user_id' => 2]
        ]);
    }


    // public function run(): void
    // {
    //     // Contoh Proyek 1
    //     $project1 = Project::create([
    //         'name' => 'Proyek A',
    //         'description' => 'Deskripsi Proyek A',
    //         'start_date' => '2024-06-01',
    //         'end_date' => '2024-12-31',
    //     ]);

    //     $task1_1 = Task::create([
    //         'project_id' => $project1->id,
    //         'name' => 'Tugas A1',
    //         'description' => 'Deskripsi Tugas A1',
    //         'start_date' => '2024-06-01',
    //         'end_date' => '2024-07-01',
    //         'status' => 'completed'
    //     ]);

    //     $task1_2 = Task::create([
    //         'project_id' => $project1->id,
    //         'name' => 'Tugas A2',
    //         'description' => 'Deskripsi Tugas A2',
    //         'start_date' => '2024-07-02',
    //         'end_date' => '2024-08-01',
    //         'status' => 'in_progress'
    //     ]);

    //     // Menambahkan PIC untuk Proyek A
    //     $project1->users()->attach([1, 2]);

    //     // Implementasikan proyek lainnya seperti contoh di atas
    //     // ...

    //     // Contoh Proyek 2
    //     $project2 = Project::create([
    //         'name' => 'Proyek B',
    //         'description' => 'Deskripsi Proyek B',
    //         'start_date' => '2024-05-01',
    //         'end_date' => '2024-11-30',
    //     ]);

    //     $task2_1 = Task::create([
    //         'project_id' => $project2->id,
    //         'name' => 'Tugas B1',
    //         'description' => 'Deskripsi Tugas B1',
    //         'start_date' => '2024-05-01',
    //         'end_date' => '2024-06-01',
    //         'status' => 'not_started'
    //     ]);

    //     $task2_2 = Task::create([
    //         'project_id' => $project2->id,
    //         'name' => 'Tugas B2',
    //         'description' => 'Deskripsi Tugas B2',
    //         'start_date' => '2024-06-02',
    //         'end_date' => '2024-07-01',
    //         'status' => 'in_progress'
    //     ]);

    //     // Menambahkan PIC untuk Proyek B
    //     $project2->users()->attach([2, 3]);
    // }
}
