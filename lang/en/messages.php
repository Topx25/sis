<?php

/**
 * English translations for SiS API.
 *
 * These are the default/fallback text values used across API Resources
 * when the corresponding database record does not override them.
 * They are also used for static UI strings and API response messages.
 */
return [

    // =========================================================================
    // HERO SECTION
    // =========================================================================
    'hero' => [
        'tagline'      => 'LEADING INDUSTRIAL INTEGRATION',
        'headline'     => 'Bridging OT & IT for a Smarter Industrial Future',
        'subheadline'  => 'We deliver end-to-end industrial automation and digital transformation solutions across the MENA region.',
        'badge_text'   => 'Trusted by Industry Leaders',
        'cta_primary'  => 'View Our Projects',
        'cta_secondary'=> 'Contact Us',
    ],

    // =========================================================================
    // ABOUT SECTION
    // =========================================================================
    'about' => [
        'mission' => [
            'tagline'     => 'OUR MISSION',
            'title'       => 'Our Mission',
            'description' => 'To bridge Operational Technology (OT) and Information Technology (IT) through real-time data-driven decision-making.',
            'icon'        => 'flag',
        ],
        'vision' => [
            'tagline'     => 'OUR VISION',
            'title'       => 'Our Vision',
            'description' => 'To establish a future standard of predictive maintenance and complete process optimization across the region\'s heaviest industries.',
            'icon'        => 'visibility',
        ],
        'why_choose_us' => [
            'title'       => 'Why Choose SiS?',
            'description' => 'We combine deep industry expertise with cutting-edge technology to deliver sustainable, future-proof industrial solutions across the MENA region.',
        ],
    ],

    // =========================================================================
    // IT / OT DIVIDE SECTION
    // =========================================================================
    'it_ot' => [
        'ot' => [
            'title'       => 'Operational Technology (OT)',
            'description' => 'The hardware and software that detects or causes a change through the direct monitoring and/or control of industrial equipment, assets, processes, and events.',
            'tags'        => ['SCADA', 'PLCs', 'Sensors', 'DCS'],
            'icon'        => 'precision_manufacturing',
        ],
        'it' => [
            'title'       => 'Information Technology (IT)',
            'description' => 'The use of computers, storage, networking, and other physical devices to create, process, store, secure, and exchange all forms of electronic data.',
            'tags'        => ['ERP', 'Cloud', 'Analytics', 'MES'],
            'icon'        => 'cloud',
        ],
    ],

    // =========================================================================
    // API RESPONSE MESSAGES
    // =========================================================================
    'responses' => [
        'retrieved'      => 'Retrieved successfully.',
        'not_found'      => 'Resource not found.',
        'created'        => 'Created successfully.',
        'updated'        => 'Updated successfully.',
        'deleted'        => 'Deleted successfully.',
        'hero_retrieved' => 'Hero section retrieved successfully.',
        'hero_not_found' => 'Hero section data not found.',
    ],

];
