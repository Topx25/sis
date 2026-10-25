<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeWhySisSection;

class HomeWhySisSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomeWhySisSection::updateOrCreate(
            ['id' => 1],
            [
                'left_title' => [
                    'en' => 'Why SiS',
                    'ar' => 'لماذا SiS',
                ],
                'right_subtitle' => [
                    'en' => 'YOUR NEXT STAGE',
                    'ar' => 'مرحلتك القادمة',
                ],
                'right_title' => [
                    'en' => 'We meet you where you are in your journey with market-leading hardware, software, and services to make you more resilient, agile, and sustainable.',
                    'ar' => 'نحن نلتقي بك حيثما كنت في رحلتك من خلال الأجهزة والبرمجيات والخدمات الرائدة في السوق لجعلك أكثر مرونة ورشاقة واستدامة.',
                ],
                'right_description' => [
                    'en' => 'From first connection to continuous improvement',
                    'ar' => 'من الاتصال الأول إلى التحسين المستمر',
                ],
            ]
        );
    }
}
