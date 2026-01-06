<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'vacancy_id' => NULL,
                'post_id' => NULL,
                'role' => 'admin',
                'enabled' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'Jane Smith',
            //     'email' => 'janesmith@example.com',
            //     'email_verified_at' => null,
            //     'password' => Hash::make('password'),
            //     'vacancy_id' => 2,
            //     'post_id' => 2,
            //     'role' => 'applicant',
            //     'enabled' => true,
            //     'remember_token' => Str::random(10),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // Add more entries as needed
        ]);
    }
}
