<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use Illuminate\Database\Seeder;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Footer Settings
        FooterSetting::firstOrCreate([], [
            'description' => 'Bridging OT and IT across the MENA region with industrial automation and IIoT excellence.',
            'copyright_text' => 'Superior Integrated Solutions. All rights reserved.',
            'dynamic_year' => true,
            'floating_contact_enabled' => true,
            'floating_contact_url' => 'https://wa.me/201007568651',
            'floating_contact_icon' => 'whatsapp',
            'floating_contact_label' => 'Contact us on WhatsApp',
        ]);

        // 2. Navigation Links
        $navLinks = [
            [
                'category' => FooterLink::CATEGORY_NAVIGATION,
                'type' => 'navigation',
                'label' => 'Home',
                'url' => '/',
                'target' => FooterLink::TARGET_SELF,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => FooterLink::CATEGORY_NAVIGATION,
                'type' => 'navigation',
                'label' => 'About',
                'url' => '/about',
                'target' => FooterLink::TARGET_SELF,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => FooterLink::CATEGORY_NAVIGATION,
                'type' => 'navigation',
                'label' => 'Services',
                'url' => '/services',
                'target' => FooterLink::TARGET_SELF,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category' => FooterLink::CATEGORY_NAVIGATION,
                'type' => 'navigation',
                'label' => 'Contact',
                'url' => '/contact',
                'target' => FooterLink::TARGET_SELF,
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($navLinks as $nav) {
            FooterLink::firstOrCreate(
                ['category' => $nav['category'], 'label' => $nav['label']],
                $nav
            );
        }

        // 3. Social & Contact Links
        $contactLinks = [
            [
                'category' => FooterLink::CATEGORY_CONTACT,
                'type' => 'social',
                'label' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/company/superior-for-integrated-solutions/',
                'icon' => 'linkedin',
                'target' => FooterLink::TARGET_BLANK,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => FooterLink::CATEGORY_CONTACT,
                'type' => 'whatsapp',
                'label' => 'WhatsApp',
                'url' => 'https://wa.me/201007568651',
                'icon' => 'whatsapp',
                'target' => FooterLink::TARGET_BLANK,
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($contactLinks as $contact) {
            FooterLink::firstOrCreate(
                ['category' => $contact['category'], 'label' => $contact['label']],
                $contact
            );
        }
    }
}
