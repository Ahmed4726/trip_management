<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boat;

class BoatSeeder extends Seeder
{
    public function run(): void
    {
        Boat::firstOrCreate([
            'name' => 'Samara I'
        ], [
            'max_capacity' => 12,
            'total_rooms' => 5,
        ]);

        Boat::firstOrCreate([
            'name' => 'Samara II'
        ], [
            'max_capacity' => 10,
            'total_rooms' => 4,
        ]);

        Boat::firstOrCreate([
            'name' => 'Samara Otium'
        ], [
            'max_capacity' => 17,
            'total_rooms' => 6,
        ]);

        Boat::firstOrCreate([
            'name' => 'Mischief'
        ], [
            'max_capacity' => 7,
            'total_rooms' => 3,
        ]);
    }
}
