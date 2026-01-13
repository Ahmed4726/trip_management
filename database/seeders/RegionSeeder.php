<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Alor',
            'Ambon',
            'Bali',
            'Banda Sea',
            'Banggai Archipelago',
            'Bangka',
            'Bunaken',
            'Cenderawasih Bay',
            'Derawan Islands',
            'Flores',
            'Forgotten Islands',
            'Gorontalo',
            'Halmahera Sea',
            'Komodo',
            'Lembeh Island',
            'Misool',
            'Morotai',
            'Pulau Dua',
            'Raja Ampat',
            'Sangihe',
            'Spice Islands',
            'Togean',
            'Triton Bay',
            'Wakatobi',
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region]);
        }
    }
}
