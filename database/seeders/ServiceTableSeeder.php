<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // could use factories here
        DB::table('services')->insert([
            [
                'name' => 'hair',
                'slug' => 'hair',
                'duration' => 30,
                'price' => 1000,
            ],
            [
                'name' => 'nipples',
                'slug' => 'nipples',
                'duration' => 20,
                'price' => 1000,
            ],
        ]);
    }
}
