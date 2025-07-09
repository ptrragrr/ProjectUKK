<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@gmail.com')->first();

        if (!$user) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '08123456789',
            ])->assignRole('admin');
        }
    }
    //     User::create([
    //         'name' => 'Admin',
    //         'email' => 'admin@gmail.com',
    //         'password' => bcrypt('12345678'),
    //         'phone' => '08123456789',
    //     ])->assignRole('admin');
    // }
}

