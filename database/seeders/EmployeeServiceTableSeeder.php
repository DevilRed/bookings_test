<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // could use factories here
        DB::table('employee_service')->insert([
            [
                'employee_id' => 1,
                'service_id' => 1,
            ],
            [
                'employee_id' => 1,
                'service_id' => 2,
            ],
            [
                'employee_id' => 2,
                'service_id' => 1,
            ],
        ]);
    }
}
