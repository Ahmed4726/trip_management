<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boat;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $boats = Boat::all()->keyBy('name');

        $rooms = [
            'Samara I' => [
                ['name' => 'Kanawa', 'deck' => 'Upper', 'bed_type' => 'Double'],
                ['name' => 'Kelor', 'deck' => 'Upper', 'bed_type' => 'Double'],
                ['name' => 'Padar', 'deck' => 'Upper', 'bed_type' => 'Double'],
                ['name' => 'Rinca', 'deck' => 'Lower', 'bed_type' => 'Double + Single'],
                ['name' => 'Komodo', 'deck' => 'Lower', 'bed_type' => 'Double + Single'],
            ],
            'Samara II' => [
                ['name' => 'Sulawesi', 'deck' => 'Middle', 'bed_type' => 'Double'],
                ['name' => 'Rote', 'deck' => 'Middle', 'bed_type' => 'Double'],
                ['name' => 'Bali', 'deck' => 'Lower', 'bed_type' => 'Double + Single'],
                ['name' => 'Flores', 'deck' => 'Lower', 'bed_type' => 'Double + Single'],
            ],
            'Samara Otium' => [
                ['name' => 'Queen Suite', 'deck' => 'Upper', 'bed_type' => 'Double + Extra'],
                ['name' => 'King Suite', 'deck' => 'Main', 'bed_type' => 'Double + 2 Extra'],
                ['name' => 'Stateroom 1', 'deck' => 'Lower', 'bed_type' => 'Flexible'],
                ['name' => 'Stateroom 2', 'deck' => 'Lower', 'bed_type' => 'Flexible'],
                ['name' => 'Stateroom 3', 'deck' => 'Lower', 'bed_type' => 'Flexible'],
                ['name' => 'Stateroom 4', 'deck' => 'Lower', 'bed_type' => 'Flexible'],
            ],
            'Mischief' => [
                ['name' => 'Master Suite', 'deck' => 'Upper', 'bed_type' => 'Double + Extra'],
                ['name' => 'Suite 1', 'deck' => 'Main', 'bed_type' => 'Flexible'],
                ['name' => 'Suite 2', 'deck' => 'Main', 'bed_type' => 'Flexible'],
            ],
        ];

        foreach ($rooms as $boatName => $boatRooms) {
            if (!isset($boats[$boatName])) continue;

            foreach ($boatRooms as $room) {
                Room::firstOrCreate([
                    'boat_id' => $boats[$boatName]->id,
                    'room_name' => $room['name'],
                ], [
                    'deck' => $room['deck'],
                    'bed_type' => $room['bed_type'],
                ]);
            }
        }
    }
}
