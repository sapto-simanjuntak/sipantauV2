<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Yoel',
            'email' => 'yoel@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('Superadmin');

        $admin = User::create([
            'name' => 'Zikri',
            'email' => 'zikri@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('Superadmin');

        $admin = User::create([
            'name' => 'Aldi',
            'email' => 'aldi@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('Superadmin');

        $admin = User::create([
            'name' => 'Asido',
            'email' => 'asido@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('Superadmin');

        $admin = User::create([
            'name' => 'Dudi',
            'email' => 'dudi@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('Superadmin');



        // $penulis = User::create([
        //     'name' => 'penulis',
        //     'email' => 'penulis@mail.com',
        //     'password' => bcrypt('12345678')
        // ]);
        // $penulis->assignRole('penulis');
    }
}
