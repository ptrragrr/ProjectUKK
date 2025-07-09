<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!Role::where('full_name','name', 'admin')->where('guard_name', 'api')->exists()) {
    Role::create(['full_name' => 'adminnnnnn','name' => 'admin', 'guard_name' => 'api']);
}

        // Role::create([
        //     'name' => 'admin',
        //     'guard_name' => 'api',
        //     'full_name' => 'Administrator',
        // ]);
    }
}
