<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // could use factories here
        DB::table('employees')->insert([
            [
                'name' => 'Alex',
                'slug' => 'alex',
                'profile_photo_url' => null,
            ],
            [
                'name' => 'Mabel',
                'slug' => 'mabel',
                'profile_photo_url' => null,
            ],
        ]);
    }
}
