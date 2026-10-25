<?php

namespace Database\Seeders;

use App\Models\HomeHeroSlide;
use App\Models\HomeWhySis;
use Illuminate\Database\Seeder;

class HomeSectionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Home Hero Slides
        $heroSlides = [
            [
                'src' => 'images/about-hero.webp',
                'alt' => 'Industrial automation control interface',
                'title' => 'Engineering smarter industrial operations',
                'description' => 'Connect automation, data, and intelligence to build more efficient and reliable operations.',
            ],
            [
                'src' => 'images/about-image.png',
                'alt' => 'Industrial energy management monitoring dashboard',
                'title' => 'Turning industrial data into insight',
                'description' => 'Transform operational data into clearer decisions, better performance, and measurable efficiency.',
            ],
            [
                'src' => 'images/controlling-2.webp',
                'alt' => 'Industrial maintenance management system',
                'title' => 'Reliability built into every operation',
                'description' => 'Keep critical assets performing with proactive monitoring, predictive maintenance, and smarter workflows.',
            ],
            [
                'src' => 'images/factory.jpg',
                'alt' => 'Smart manufacturing industrial facility',
                'title' => 'Building the connected factory',
                'description' => 'Integrate machines, systems, and people to create smarter, more connected manufacturing environments.',
            ],
        ];

        foreach ($heroSlides as $index => $slide) {
            HomeHeroSlide::updateOrCreate(
                ['src' => $slide['src']],
                [
                    'alt' => $slide['alt'],
                    'title' => ['en' => $slide['title'], 'ar' => $slide['title']],
                    'description' => ['en' => $slide['description'], 'ar' => $slide['description']],
                    'sort_order' => $index,
                ]
            );
        }

        // 2. Why SIS Characteristics
        $characteristics = [
            [
                'title' => 'Industrial intelligence',
                'description' => 'Turn operational data into clear decisions across your plant, people, and platforms.',
            ],
            [
                'title' => 'Built to connect',
                'description' => 'Bridge OT and IT with practical systems that work with the technology you already have.',
            ],
            [
                'title' => 'Designed for momentum',
                'description' => 'Create a resilient foundation that can adapt as your goals, markets, and operations evolve.',
            ],
        ];

        foreach ($characteristics as $index => $char) {
            HomeWhySis::updateOrCreate(
                ['title->en' => $char['title']],
                [
                    'title' => ['en' => $char['title'], 'ar' => $char['title']],
                    'description' => ['en' => $char['description'], 'ar' => $char['description']],
                    'sort_order' => $index,
                ]
            );
        }
    }
}
