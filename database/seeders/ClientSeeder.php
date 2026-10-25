<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Delta',
                'logo_path' => 'images/ourClints/delta.webp',
                'sort_order' => 1,
                'subtitle' => 'Our Clients',
                'title' => 'Trusted by',
                'title_highlight' => 'Industry Leaders',
                'description' => 'Partnering with leading organizations across the MENA region to deliver industrial excellence.',
            ],
            ['name' => 'Emisal', 'logo_path' => 'images/ourClints/emitsal.webp', 'sort_order' => 2],
            ['name' => 'Energeya', 'logo_path' => 'images/ourClints/energeya.webp', 'sort_order' => 3],
            ['name' => 'GCF', 'logo_path' => 'images/ourClints/gcf.webp', 'sort_order' => 4],
            ['name' => 'Giza Systems', 'logo_path' => 'images/ourClints/giza_systems.webp', 'sort_order' => 5],
            ['name' => 'Henkel', 'logo_path' => 'images/ourClints/henkel.webp', 'sort_order' => 6],
            ['name' => 'Omis', 'logo_path' => 'images/ourClints/omis.webp', 'sort_order' => 7],
            ['name' => 'Orascom Construction', 'logo_path' => 'images/ourClints/orascom_const.webp', 'sort_order' => 8],
            ['name' => 'Plast Company', 'logo_path' => 'images/ourClints/plast_company.webp', 'sort_order' => 9],
            ['name' => 'Tabreed', 'logo_path' => 'images/ourClints/tabreed.webp', 'sort_order' => 10],
            ['name' => 'Uniha', 'logo_path' => 'images/ourClints/uniha.webp', 'sort_order' => 11],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['name' => $client['name']],
                $client
            );
        }
    }
}
