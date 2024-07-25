<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'add_users' , 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_users' , 'guard_name' => 'sanctum']);
    }
}
