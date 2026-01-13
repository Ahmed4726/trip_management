<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     UserSeeder::class,
        // ]);
        // $this->call(CompanySeeder::class);


            $this->call([
                RegionSeeder::class,
                PortSeeder::class,
                BoatSeeder::class,
                RoomSeeder::class,
            ]);
    }
}
