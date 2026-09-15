<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-departments',
            'manage-locations',
            'manage-categories',
            'manage-assets',
            'manage-assignments',
            'manage-maintenances',
            'view-reports',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'manage-assets',
            'manage-assignments',
            'manage-maintenances',
            'view-reports',
        ]);

        Role::firstOrCreate(['name' => 'staff']);
    }
}
