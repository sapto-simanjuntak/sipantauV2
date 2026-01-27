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
            'name' => 'Fusiana',
            'email' => 'fusiana@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('superadmin');

        $admin = User::create([
            'name' => 'Yoel',
            'email' => 'yoel@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('admin');

        $admin = User::create([
            'name' => 'Zikri',
            'email' => 'zikri@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('teknisi');

        $admin = User::create([
            'name' => 'Aldi',
            'email' => 'aldi@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('teknisi');

        $admin = User::create([
            'name' => 'Asido',
            'email' => 'asido@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('teknisi');

        $admin = User::create([
            'name' => 'Dudi',
            'email' => 'dudi@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('teknisi');

        $admin = User::create([
            'name' => 'Marco',
            'email' => 'marco@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('user');

        $admin = User::create([
            'name' => 'Sastroni',
            'email' => 'sastroni@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $admin->assignRole('user');



        // $penulis = User::create([
        //     'name' => 'penulis',
        //     'email' => 'penulis@mail.com',
        //     'password' => bcrypt('12345678')
        // ]);
        // $penulis->assignRole('penulis');
    }
}
