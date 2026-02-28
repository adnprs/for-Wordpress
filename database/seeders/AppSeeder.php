<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Ye line zaroori hai

class AppSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Safety Lock kholo
        Schema::disableForeignKeyConstraints();

        // 2. Data saaf karo
        DB::table('apps')->truncate();

        // 3. Data taiyar karo
        $apps = [
            [
                'name' => 'JOBS WALE BHAIYA',
                'type' => 'wordpress',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JobShorts',
                'type' => 'android',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BPSC Teacher News',
                'type' => 'wordpress',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ADSDELHI',
                'type' => 'wordpress',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 4. Data dalo
        DB::table('apps')->insert($apps);

        // 5. Safety Lock wapas lagao
        Schema::enableForeignKeyConstraints();
    }
}