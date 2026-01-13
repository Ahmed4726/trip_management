<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            'Alor','Ambon','Amed','Ampana','Babang','Bali','Balikpapan','Batam',
            'Bau-Bau','Benoa','Berau','Biak','Bima','Bira','Bitung','Bungus',
            'Denpasar','Dili','Fakfak','Gili Trawangan','Gorontalo','Kaimana',
            'Kalabahi','Kendari','Komodo','Kupang','Labuan Bajo','Lembeh Strait',
            'Lombok','Luwuk','Manado','Manokwari','Maumere','Morotai','Nabire',
            'Padang','Pulau Derawan','Pulau Weh','Sanur','Saumlaki','Senggigi',
            'Serangan','Sibolga','Sorong','Sumba','Sumbawa Besar','Tarakan',
            'Ternate','Timika','Tual','Waisai','Wakatobi','Togean',
        ];

        foreach ($ports as $port) {
            Port::firstOrCreate(['name' => $port]);
        }
    }
}
