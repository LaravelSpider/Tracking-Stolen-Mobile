<?php

namespace Database\Seeders;

use Database\Seeders\AuthSeeder;
use Database\Seeders\DeviceSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AuthSeeder::class,   // if you’re using the auth/roles seeder
            DeviceSeeder::class, // add this line
        ]);
    }    
}