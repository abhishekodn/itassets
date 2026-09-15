<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@odndigital.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@odndigital.com'],
            ['name' => 'Asset Manager', 'password' => bcrypt('password')]
        );
        $manager->assignRole('manager');

        $staff = User::firstOrCreate(
            ['email' => 'staff@odndigital.com'],
            ['name' => 'Staff User', 'password' => bcrypt('password')]
        );
        $staff->assignRole('staff');

        $it = Department::firstOrCreate(['code' => 'IT'], ['name' => 'Information Technology']);
        Department::firstOrCreate(['code' => 'HR'], ['name' => 'Human Resources']);
        Department::firstOrCreate(['code' => 'FIN'], ['name' => 'Finance']);

        Location::firstOrCreate(
            ['name' => 'Head Office - 3rd Floor'],
            ['address' => 'Head Office', 'department_id' => $it->id]
        );
        Location::firstOrCreate(
            ['name' => 'Warehouse'],
            ['address' => 'Warehouse']
        );

        foreach ([
            ['name' => 'Laptop', 'useful_life_years' => 3],
            ['name' => 'Desktop', 'useful_life_years' => 4],
            ['name' => 'Monitor', 'useful_life_years' => 5],
            ['name' => 'Printer', 'useful_life_years' => 5],
            ['name' => 'Mobile Phone', 'useful_life_years' => 2],
            ['name' => 'Furniture', 'useful_life_years' => 7],
            ['name' => 'Networking Equipment', 'useful_life_years' => 5],
        ] as $category) {
            AssetCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
