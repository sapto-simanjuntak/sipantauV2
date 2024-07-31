<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'User-View']);
        Permission::create(['name' => 'User-Create']);
        Permission::create(['name' => 'User-Edit']);
        Permission::create(['name' => 'User-Delete']);

        Permission::create(['name' => 'Role-View']);
        Permission::create(['name' => 'Role-Create']);
        Permission::create(['name' => 'Role-Edit']);
        Permission::create(['name' => 'Role-Delete']);

        Permission::create(['name' => 'Permission-View']);
        Permission::create(['name' => 'Permission-Create']);
        Permission::create(['name' => 'Permission-Edit']);
        Permission::create(['name' => 'Permission-Delete']);

        Permission::create(['name' => 'Data-View']);
        Permission::create(['name' => 'Data-Create']);
        Permission::create(['name' => 'Data-Edit']);
        Permission::create(['name' => 'Data-Delete']);


        Role::create(['name' => 'Superadmin']);

        $roleSuperadmin = Role::findByName('Superadmin');

        $roleSuperadmin->givePermissionTo('User-View');
        $roleSuperadmin->givePermissionTo('User-Create');
        $roleSuperadmin->givePermissionTo('User-Edit');
        $roleSuperadmin->givePermissionTo('User-Delete');

        $roleSuperadmin->givePermissionTo('Role-View');
        $roleSuperadmin->givePermissionTo('Role-Create');
        $roleSuperadmin->givePermissionTo('Role-Edit');
        $roleSuperadmin->givePermissionTo('Role-Delete');

        $roleSuperadmin->givePermissionTo('Permission-View');
        $roleSuperadmin->givePermissionTo('Permission-Create');
        $roleSuperadmin->givePermissionTo('Permission-Edit');
        $roleSuperadmin->givePermissionTo('Permission-Delete');

        $roleSuperadmin->givePermissionTo('Data-View');
        $roleSuperadmin->givePermissionTo('Data-Create');
        $roleSuperadmin->givePermissionTo('Data-Edit');
        $roleSuperadmin->givePermissionTo('Data-Delete');
    }
}
