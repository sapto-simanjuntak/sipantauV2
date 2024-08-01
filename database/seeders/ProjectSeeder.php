<?php

namespace Database\Seeders;

use App\Models\Modul\Task;
use App\Models\Modul\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert data proyek
        $projects = [
            [
                'name' => 'Proyek A',
                'description_before' => 'Sebelum perubahan, Proyek A adalah proyek dengan beberapa komponen.',
                'description_after' => 'Setelah perubahan, Proyek A memiliki fitur tambahan.',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => Project::STATUS_NOT_STARTED
            ],
            [
                'name' => 'Proyek B',
                'description_before' => 'Sebelum perubahan, Proyek B hanya melibatkan satu tim.',
                'description_after' => 'Setelah perubahan, Proyek B melibatkan dua tim.',
                'start_date' => '2024-02-01',
                'end_date' => '2024-06-30',
                'status' => Project::STATUS_IN_PROGRESS
            ],
            [
                'name' => 'Proyek C',
                'description_before' => 'Sebelum perubahan, Proyek C hanya dalam fase perencanaan.',
                'description_after' => 'Setelah perubahan, Proyek C sudah memasuki fase pengembangan.',
                'start_date' => '2024-03-01',
                'end_date' => '2024-09-30',
                'status' => Project::STATUS_COMPLETED
            ],
            [
                'name' => 'Proyek D',
                'description_before' => 'Sebelum perubahan, Proyek D melibatkan satu lab.',
                'description_after' => 'Setelah perubahan, Proyek D melibatkan beberapa lab.',
                'start_date' => '2024-04-01',
                'end_date' => '2024-10-31',
                'status' => Project::STATUS_NOT_STARTED
            ],
            [
                'name' => 'Proyek E',
                'description_before' => 'Sebelum perubahan, Proyek E hanya melakukan uji coba awal.',
                'description_after' => 'Setelah perubahan, Proyek E melakukan uji coba lebih mendalam.',
                'start_date' => '2024-05-01',
                'end_date' => '2024-12-31',
                'status' => Project::STATUS_IN_PROGRESS
            ],
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
}
