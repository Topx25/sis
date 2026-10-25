<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sector;
use App\Models\Service;
use App\Models\Project;
use App\Models\Partner;
use App\Models\TimelineEvent;
use App\Models\Pillar;
use App\Models\ArchitectureStage;
use App\Models\WorkSample;
use App\Models\Client;
use App\Models\HeroSection;
use App\Models\AboutSection;
use App\Models\AboutHero;
use App\Models\ItOtDivide;
use App\Models\CoreValue;
use App\Models\EmpoweredDomain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@sis-automation.com'],
            [
                'name' => 'SiS System Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Sectors (Real Data matching backend data requirements.md)
        $sectorsData = [
            [
                'name' => 'Oil & Gas',
                'slug' => 'oil-gas',
                'icon' => 'oil',
                'description' => 'Upstream, midstream, and downstream digital oilfield instrumentation and safety systems.',
                'relevant_programs' => [],
                'typical_scope' => [],
            ],
            [
                'name' => 'Water & Waste Water',
                'slug' => 'water-waste-water',
                'icon' => 'water',
                'description' => 'Desalination plants, wastewater treatment, and regional water distribution automation.',
                'relevant_programs' => [],
                'typical_scope' => [],
            ],
            [
                'name' => 'Chemical Industries',
                'slug' => 'chemical-industries',
                'icon' => 'science',
                'description' => 'Chemical process optimization, safety valve control, and hazardous environment automation.',
                'relevant_programs' => [],
                'typical_scope' => [],
            ],
            [
                'name' => 'Food, Beverage & FMCG',
                'slug' => 'food-beverage-fmcg',
                'icon' => 'fastfood',
                'description' => 'High-speed packaging, chemical mixing, ERP traceability, and thermal process control.',
                'relevant_programs' => [],
                'typical_scope' => [],
            ],
            [
                'name' => 'Heavy Industries',
                'slug' => 'heavy-industries',
                'icon' => 'factory',
                'description' => 'Cement manufacturing, steel foundries, kiln control, and conveyor systems.',
                'relevant_programs' => [],
                'typical_scope' => [],
            ],
        ];

        $sectorsMap = [];
        foreach ($sectorsData as $sec) {
            $sector = Sector::updateOrCreate(['slug' => $sec['slug']], $sec);
            $sectorsMap[$sec['name']] = $sector->id;
        }

        // 3. Services (Real Data)
        $servicesData = [
            [
                'title' => 'Industrial Automation',
                'slug' => 'industrial-automation',
                'summary' => 'Pioneering technology solutions for the modern factory and large-scale infrastructure implementation.',
                'description' => 'Pioneering technology solutions for the modern factory and large-scale infrastructure implementation.',
                'icon' => 'robot-arm'
            ],
            [
                'title' => 'SCADA & Dashboard',
                'slug' => 'scada-dashboard',
                'summary' => 'Delivering real-time monitoring and robust control of industrial processes via Supervisory Control and Data Acquisition.',
                'description' => 'Delivering real-time monitoring and robust control of industrial processes via Supervisory Control and Data Acquisition.',
                'icon' => 'monitor'
            ],
            [
                'title' => 'IIoT & Data Analytics',
                'slug' => 'iiot-data-analytics',
                'summary' => 'Connecting physical assets to digital ecosystems to transform raw telemetry into actionable intelligence.',
                'description' => 'Connecting physical assets to digital ecosystems to transform raw telemetry into actionable intelligence.',
                'icon' => 'cpu'
            ],
            [
                'title' => 'Custom Solutions & Outsourcing',
                'slug' => 'custom-solutions-outsourcing',
                'summary' => 'Tailored engineering services and AI-powered technologies that drive efficiency, agility, and measurable results.',
                'description' => 'Tailored engineering services and AI-powered technologies that drive efficiency, agility, and measurable results.',
                'icon' => 'settings'
            ],
        ];

        $servicesMap = [];
        foreach ($servicesData as $srv) {
            $service = Service::updateOrCreate(['slug' => $srv['slug']], $srv);
            $servicesMap[$srv['title']] = $service->id;
        }

        // 4. Partners (Real Data)
        $partnersData = [
            ['name' => 'Siemens', 'logo_path' => 'partners/Siemens.png', 'website_url' => 'https://www.siemens.com', 'type' => 'technology_partner', 'sort_order' => 1],
            ['name' => 'Rockwell Automation', 'logo_path' => 'partners/Rockwell_Automation_Logo.png', 'website_url' => 'https://www.rockwellautomation.com', 'type' => 'technology_partner', 'sort_order' => 2],
            ['name' => 'Schneider Electric', 'logo_path' => 'partners/schnider.webp', 'website_url' => 'https://www.se.com', 'type' => 'technology_partner', 'sort_order' => 3],
            ['name' => 'ABB', 'logo_path' => 'partners/ABB.png', 'website_url' => 'https://www.abb.com', 'type' => 'technology_partner', 'sort_order' => 4],
            ['name' => 'Mitsubishi Electric', 'logo_path' => 'partners/Mitsubishi_Electric.png', 'website_url' => 'https://www.mitsubishielectric.com', 'type' => 'technology_partner', 'sort_order' => 5],
            ['name' => 'Huawei', 'logo_path' => 'partners/Huawei.png', 'website_url' => 'https://www.huawei.com', 'type' => 'technology_partner', 'sort_order' => 6],
            ['name' => 'AVEVA', 'logo_path' => 'partners/aveva.webp', 'website_url' => 'https://www.aveva.com', 'type' => 'ecosystem_network', 'sort_order' => 7],
            ['name' => 'PTC', 'logo_path' => 'partners/PTC.png', 'website_url' => 'https://www.ptc.com', 'type' => 'ecosystem_network', 'sort_order' => 8],
            ['name' => 'Egypt 4 Industry', 'logo_path' => 'partners/Egypt_4_Industry.jpg', 'website_url' => 'https://egypt4industry.com', 'type' => 'ecosystem_network', 'sort_order' => 9],
            ['name' => 'Odoo', 'logo_path' => 'partners/odoo.svg', 'website_url' => 'https://www.odoo.com', 'type' => 'ecosystem_network', 'sort_order' => 10],
        ];

        foreach ($partnersData as $part) {
            Partner::updateOrCreate(['name' => $part['name']], $part);
        }

        // 5. Timeline Events (Real Data)
        $timelineData = [
            ['year' => '2020', 'title' => 'Foundation', 'description' => 'Industrial engineering foundations and initial operations.', 'icon' => 'flag'],
            ['year' => '2021', 'title' => 'Validation', 'description' => 'Refined the offer and established market fit.', 'icon' => 'verified'],
            ['year' => '22–23', 'title' => 'Regional presence', 'description' => 'International expansion and larger contracts.', 'icon' => 'public'],
            ['year' => '2024', 'title' => 'Scaling', 'description' => 'Team expansion and broader delivery capacity.', 'icon' => 'trending_up'],
            ['year' => '2025+', 'title' => 'Industrial intelligence', 'description' => 'Emerging technology and continuous innovation.', 'icon' => 'psychology'],
        ];

        foreach ($timelineData as $event) {
            TimelineEvent::updateOrCreate(['year' => $event['year']], $event);
        }

        // 6. Projects (Real Data from backend data requirements.md)
        $projectsData = [
            [
                'client_name' => 'Khalda Petroleum',
                'sector_name' => 'Oil & Gas',
                'location' => 'Marsa Matrouh, Egypt',
                'title' => 'Digital oil-field instrumentation',
                'summary' => 'Deployment of Well Head Wireless HART Transmitters integrated into SCADA for real-time field control and remote monitoring across multiple well sites.',
                'description' => 'Deployment of Well Head Wireless HART Transmitters integrated into SCADA for real-time field control and remote monitoring across multiple well sites.',
                'tags' => ['SCADA', 'HART', 'Wireless'],
                'cover_image' => 'images/ourClints/khalad-Petroleum.webp',
            ],
            [
                'client_name' => 'Saudi Aramco',
                'sector_name' => 'Oil & Gas',
                'location' => 'KSA',
                'title' => 'SIL-2 wellhead safety-system upgrades',
                'summary' => 'Well Heads Safety System Upgrades using SIEMENS S7-400H, PCS7, Schneider Electric Triconex, and Foxboro technologies to meet SIL-2 safety standards.',
                'description' => 'Well Heads Safety System Upgrades using SIEMENS S7-400H, PCS7, Schneider Electric Triconex, and Foxboro technologies to meet SIL-2 safety standards.',
                'tags' => ['Safety Systems', 'SIL-2', 'Siemens PCS7'],
                'cover_image' => 'images/ourClints/Saudi-Aramco.webp',
            ],
            [
                'client_name' => 'Béjaïa RO Plant',
                'sector_name' => 'Water & Waste Water',
                'location' => 'Algeria',
                'title' => 'Resilient desalination control',
                'summary' => 'Full commissioning of PLC and SCADA systems with network cybersecurity hardening for a 50,000 m³/day Reverse Osmosis desalination facility.',
                'description' => 'Full commissioning of PLC and SCADA systems with network cybersecurity hardening for a 50,000 m³/day Reverse Osmosis desalination facility.',
                'tags' => ['PLC', 'SCADA', 'Cybersecurity'],
                'cover_image' => null,
            ],
            [
                'client_name' => 'SWCC',
                'sector_name' => 'Water & Waste Water',
                'location' => 'KSA',
                'title' => 'Large-scale desalination commissioning',
                'summary' => 'Hardware commissioning, software logic validation, and field integration for large-scale desalination operations serving 1.2M residents.',
                'description' => 'Hardware commissioning, software logic validation, and field integration for large-scale desalination operations serving 1.2M residents.',
                'tags' => ['Hardware', 'Commissioning', 'DCS'],
                'cover_image' => 'images/ourClints/SWCC.webp',
            ],
            [
                'client_name' => 'Qaliobia Waste Water',
                'sector_name' => 'Water & Waste Water',
                'location' => 'Egypt',
                'title' => 'End-to-end wastewater delivery',
                'summary' => 'Full 6-phase project spanning design, civil, electrical, instrumentation, automation, and startup for a regional waste water treatment plant.',
                'description' => 'Full 6-phase project spanning design, civil, electrical, instrumentation, automation, and startup for a regional waste water treatment plant.',
                'tags' => ['End-to-End', 'Automation', 'IIoT'],
                'cover_image' => 'images/ourClints/Qaliobia-Waste-Water.webp',
            ],
            [
                'client_name' => 'UNIHA Water Technology',
                'sector_name' => 'Water & Waste Water',
                'location' => 'Al Arish, Egypt',
                'title' => 'Five-stage RO process automation',
                'summary' => 'Full automation of a 5-stage RO water treatment process over an architectural Ethernet backbone, enabling remote diagnostics.',
                'description' => 'Full automation of a 5-stage RO water treatment process over an architectural Ethernet backbone, enabling remote diagnostics.',
                'tags' => ['Ethernet', 'RO Automation'],
                'cover_image' => 'images/ourClints/uniha.webp',
            ],
            [
                'client_name' => 'Gas Cool',
                'sector_name' => 'Chemical Industries',
                'location' => 'Fayoum, Egypt',
                'title' => 'Chemical process optimisation',
                'summary' => 'Advanced automation solutions designed for process optimisation within chemical manufacturing plants.',
                'description' => 'Advanced automation solutions designed for process optimisation within chemical manufacturing plants.',
                'tags' => ['Process Control', 'Automation'],
                'cover_image' => 'images/ourClints/Gas-Cool.webp',
            ],
            [
                'client_name' => 'Emisal',
                'sector_name' => 'Chemical Industries',
                'location' => 'Fayoum, Egypt',
                'title' => 'Centralised safety and valve control',
                'summary' => 'Centralised SCADA fire alarm system with WinCC SCADA and Siemens PLC, plus high-pressure steam and chemical valve control for chemical manufacturing.',
                'description' => 'Centralised SCADA fire alarm system with WinCC SCADA and Siemens PLC, plus high-pressure steam and chemical valve control for chemical manufacturing.',
                'tags' => ['SCADA', 'Fire Safety', 'Siemens'],
                'cover_image' => 'images/ourClints/emitsal.webp',
            ],
            [
                'client_name' => 'Henkel',
                'sector_name' => 'Food, Beverage & FMCG',
                'location' => '6th of October, Egypt',
                'title' => 'Advanced process automation and live validation',
                'summary' => 'SCADA upgrade, process automation for chemical mixing, Siemens TIA thermal control, chemical dosing, and AR-assisted live system validation.',
                'description' => 'SCADA upgrade, process automation for chemical mixing, Siemens TIA thermal control, chemical dosing, and AR-assisted live system validation.',
                'tags' => ['SCADA Upgrade', 'AR Validation', 'TIA'],
                'cover_image' => 'images/ourClints/henkel.webp',
            ],
            [
                'client_name' => 'BariQ',
                'sector_name' => 'Food, Beverage & FMCG',
                'location' => 'Cairo, Egypt',
                'title' => 'ERP-connected production intelligence',
                'summary' => 'Specialised automation with ODOO ERP integration and smart SCADA reporting for real-time granular process monitoring and traceability.',
                'description' => 'Specialised automation with ODOO ERP integration and smart SCADA reporting for real-time granular process monitoring and traceability.',
                'tags' => ['ERP Integration', 'Smart Reporting'],
                'cover_image' => 'images/ourClints/BariQ.webp',
            ],
            [
                'client_name' => 'TAK Foods Factory',
                'sector_name' => 'Food, Beverage & FMCG',
                'location' => 'Menofia, Egypt',
                'title' => 'Production-line electrical and controls renewal',
                'summary' => 'Standardised Siemens PLC platform, modern HMI dashboards, variable frequency drives, and complete electrical panel renewal for production lines.',
                'description' => 'Standardised Siemens PLC platform, modern HMI dashboards, variable frequency drives, and complete electrical panel renewal for production lines.',
                'tags' => ['Siemens PLC', 'VFD', 'HMI'],
                'cover_image' => 'images/ourClints/TAK-Foods-Factory.webp',
            ],
            [
                'client_name' => 'Al Takamol Cement',
                'sector_name' => 'Heavy Industries',
                'location' => 'Sudan',
                'title' => 'Cement-production automation support',
                'summary' => 'Industrial automation and support services for large-scale cement manufacturing, including kiln control and conveyor belt management systems.',
                'description' => 'Industrial automation and support services for large-scale cement manufacturing, including kiln control and conveyor belt management systems.',
                'tags' => ['Kiln Control', 'PLC', 'SCADA'],
                'cover_image' => 'images/ourClints/Al-Takamol-Cement.webp',
            ],
            [
                'client_name' => 'Greater Cairo Foundries (GCF)',
                'sector_name' => 'Heavy Industries',
                'location' => 'Sharqia, Egypt',
                'title' => 'Foundry-wide automation modernisation',
                'summary' => 'New Siemens automation system, SCADA WinCC upgrade, and full power/control panel supply, assembly and startup for metal foundry operations.',
                'description' => 'New Siemens automation system, SCADA WinCC upgrade, and full power/control panel supply, assembly and startup for metal foundry operations.',
                'tags' => ['WinCC', 'Power Panels', 'Siemens'],
                'cover_image' => 'images/ourClints/gcf.webp',
            ],
        ];

        foreach ($projectsData as $proj) {
            $sectorId = $sectorsMap[$proj['sector_name']] ?? array_values($sectorsMap)[0];
            $project = Project::updateOrCreate(
                ['slug' => Str::slug($proj['title'])],
                [
                    'sector_id' => $sectorId,
                    'title' => $proj['title'],
                    'client_name' => $proj['client_name'],
                    'location' => $proj['location'],
                    'summary' => $proj['summary'],
                    'description' => $proj['description'],
                    'tags' => $proj['tags'],
                    'cover_image' => $proj['cover_image'],
                ]
            );

            // Attach services
            $project->services()->sync(array_values($servicesMap));
        }

        // 7. Pillars (How It Works)
        $pillarsData = [
            [
                'step' => "01 \u00b7 Connect",
                'title' => "Connect any asset",
                'body' => "PLCs, sensors, drives and legacy equipment onto one secure network over OPC UA, MQTT and Modbus.",
                'icon' => "connect",
                'sort_order' => 1
            ],
            [
                'step' => "02 \u00b7 Monitor",
                'title' => "Monitor in real time",
                'body' => "Live SCADA dashboards, alarms and trends \u2014 every metric visible the moment it moves.",
                'icon' => "pulse",
                'sort_order' => 2
            ],
            [
                'step' => "03 \u00b7 Optimize",
                'title' => "Optimize & predict",
                'body' => "Analytics, digital twins and predictive maintenance turn data into fewer stoppages and higher output.",
                'icon' => "chart",
                'sort_order' => 3
            ],
        ];

        foreach ($pillarsData as $pillar) {
            Pillar::updateOrCreate(['step' => $pillar['step']], $pillar);
        }

        // 8. Architecture Stages (IT/OT Lifecycle)
        $stagesData = [
            [
                'stage' => 'STAGE 01',
                'title' => 'Mind',
                'body' => 'Strategic intent and high-level vision. Where corporate goals are translated into actionable digital roadmaps.',
                'layer' => 'STRATEGIC LAYER',
                'icon' => 'mind',
                'sort_order' => 1
            ],
            [
                'stage' => 'STAGE 02',
                'title' => 'AI',
                'body' => 'Intelligent processing and advanced analytics. Transforming new data streams into predictive operational insights.',
                'layer' => 'ANALYTIC LAYER',
                'icon' => 'ai',
                'sort_order' => 2
            ],
            [
                'stage' => 'STAGE 03',
                'title' => 'PC',
                'body' => 'Digital orchestration and control interfaces. The mission control center for real-time monitoring and command.',
                'layer' => 'ORCHESTRATION',
                'icon' => 'pc',
                'sort_order' => 3
            ],
            [
                'stage' => 'STAGE 04',
                'title' => 'Factory',
                'body' => 'Physical production and OT integration. High-precision hardware executing digital commands on the shop floor.',
                'layer' => 'PHYSICAL LAYER',
                'icon' => 'factory',
                'sort_order' => 4
            ],
            [
                'stage' => 'STAGE 05',
                'title' => 'Human',
                'body' => 'Empowered workforce and oversight. Experts leveraging systems to make critical decisions and drive safety.',
                'layer' => 'EXPERT LAYER',
                'icon' => 'human',
                'sort_order' => 5
            ],
        ];

        foreach ($stagesData as $stage) {
            ArchitectureStage::updateOrCreate(['stage' => $stage['stage']], $stage);
        }

        // 9. Work Samples (Interactive Demos)
        $workSamplesData = [
            [
                'slug' => 'cement-process-automation',
                'title' => 'Cement Plant Process Automation',
                'industry' => 'Cement',
                'category' => 'Cement',
                'image' => 'cement-process-automation.png',
                'image_path' => 'work-samples/cement-process-automation.png',
                'free' => true,
                'blurb' => 'Process flow, equipment status, energy consumption and alarm summary for cement production.',
                'body' => 'Process flow, equipment status, energy consumption and alarm summary for cement production.',
                'badge' => 'FREE DEMO',
                'demo_url' => 'work-samples/cement-process-automation/index.html',
                'sort_order' => 1,
            ],
            [
                'slug' => 'ems-energy-management',
                'title' => 'EMS – Energy Management System',
                'industry' => 'Energy & Utilities',
                'category' => 'Energy & Utilities',
                'image' => 'ems-energy-management.png',
                'image_path' => 'work-samples/ems-energy-management.png',
                'free' => false,
                'blurb' => 'Energy breakdown, real-time electrical measurements, shift consumption and monthly savings.',
                'body' => 'Energy breakdown, real-time electrical measurements, shift consumption and monthly savings.',
                'badge' => 'DEMO ACCESS',
                'demo_url' => 'work-samples/ems-energy-management/index.html',
                'sort_order' => 2,
            ],
            [
                'slug' => 'bms-building-management',
                'title' => 'BMS – Building Management System',
                'industry' => 'Infrastructure & Buildings',
                'category' => 'Infrastructure & Buildings',
                'image' => 'bms-building-management.png',
                'image_path' => 'work-samples/bms-building-management.png',
                'free' => false,
                'blurb' => 'HVAC, floor monitoring, lighting management and building utilities in one control view.',
                'body' => 'HVAC, floor monitoring, lighting management and building utilities in one control view.',
                'badge' => 'DEMO ACCESS',
                'demo_url' => 'work-samples/bms-building-management/index.html',
                'sort_order' => 3,
            ],
            [
                'slug' => 'cmms-maintenance',
                'title' => 'CMMS – Maintenance Management',
                'industry' => 'Maintenance',
                'category' => 'Maintenance',
                'image' => 'cmms-maintenance.png',
                'image_path' => 'work-samples/cmms-maintenance.png',
                'free' => false,
                'blurb' => 'Work orders, asset health, preventive maintenance and technician performance tracking.',
                'body' => 'Work orders, asset health, preventive maintenance and technician performance tracking.',
                'badge' => 'DEMO ACCESS',
                'demo_url' => 'work-samples/cmms-maintenance/index.html',
                'sort_order' => 4,
            ],
            [
                'slug' => 'ai-analysis',
                'title' => 'AI Analysis Dashboard',
                'industry' => 'AI & Analytics',
                'category' => 'AI & Analytics',
                'image' => 'ai-analysis.png',
                'image_path' => 'work-samples/ai-analysis.png',
                'free' => false,
                'blurb' => 'Anomaly detection, RUL, equipment risk ranking, failure probability and AI recommendations.',
                'body' => 'Anomaly detection, RUL, equipment risk ranking, failure probability and AI recommendations.',
                'badge' => 'DEMO ACCESS',
                'demo_url' => 'work-samples/ai-analysis/index.html',
                'sort_order' => 5,
            ],
            [
                'slug' => 'process-automation',
                'title' => 'Process Automation Dashboard',
                'industry' => 'Manufacturing',
                'category' => 'Manufacturing',
                'image' => 'process-automation.png',
                'image_path' => 'work-samples/process-automation.png',
                'free' => false,
                'blurb' => 'Industrial process automation across cement, oil and gas plants in a single view.',
                'body' => 'Industrial process automation across cement, oil and gas plants in a single view.',
                'badge' => 'DEMO ACCESS',
                'demo_url' => 'work-samples/process-automation/index.html',
                'sort_order' => 6,
            ],
        ];

        foreach ($workSamplesData as $sample) {
            WorkSample::updateOrCreate(['slug' => $sample['slug']], $sample);
        }

        // 10. Clients
        $clientsData = [
            ['name' => 'Delta', 'logo_path' => 'images/ourClints/delta.jpg', 'sort_order' => 1],
            ['name' => 'Emisal', 'logo_path' => 'images/ourClints/emitsal.jpg', 'sort_order' => 2],
            ['name' => 'Energeya', 'logo_path' => 'images/ourClints/energeya.jpg', 'sort_order' => 3],
            ['name' => 'GCF', 'logo_path' => 'images/ourClints/gcf.jpg', 'sort_order' => 4],
            ['name' => 'Giza Systems', 'logo_path' => 'images/ourClints/giza_systems.jpg', 'sort_order' => 5],
            ['name' => 'Henkel', 'logo_path' => 'images/ourClints/henkel.jpg', 'sort_order' => 6],
            ['name' => 'Omis', 'logo_path' => 'images/ourClints/omis.jpg', 'sort_order' => 7],
            ['name' => 'Orascom Construction', 'logo_path' => 'images/ourClints/orascom_const.jpg', 'sort_order' => 8],
            ['name' => 'Plast Company', 'logo_path' => 'images/ourClints/plast_company.jpg', 'sort_order' => 9],
            ['name' => 'Tabreed', 'logo_path' => 'images/ourClints/tabreed.jpg', 'sort_order' => 10],
            ['name' => 'Uniha', 'logo_path' => 'images/ourClints/uniha.jpg', 'sort_order' => 11],
        ];

        foreach ($clientsData as $client) {
            Client::updateOrCreate(['name' => $client['name']], $client);
        }

        // 11. Hero Section (Home Section 1)
        HeroSection::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'Superior Integrated Solutions',
                'headline' => 'Where operational meets information technology',
                'subheadline' => 'Delivering world-class PLC, SCADA, EMS, BMS, and IIoT solutions across the MENA region, powered by Siemens, Schneider Electric, and AVEVA technologies.',
                'image' => 'images/factory.webp',
                'cta_primary' => 'Explore Our Work',
                'cta_primary_link' => '/projects',
                'cta_secondary' => 'Get In Touch',
                'cta_secondary_link' => '/contact',
                'stats' => [
                    ['value' => 5, 'suffix' => '+', 'label' => 'Years of Excellence'],
                    ['value' => 12, 'suffix' => '+', 'label' => 'Industrial Projects'],
                    ['value' => 5, 'suffix' => '', 'label' => 'Sectors Served'],
                    ['value' => 10, 'suffix' => '+', 'label' => 'Technology Partners'],
                ],
                'badge_text' => 'Operational since 2020 · 12+ industrial deployments live',
                'is_active' => true,
            ]
        );

        // 12. About Section (Mission, Vision, etc.)
        AboutSection::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Who We Are',
                'headline' => 'Our Mission & Vision',
                'subheadline' => 'Driving industrial excellence through intelligent IT-OT convergence across the MENA region.',
                'paragraphs' => [
                    'Superior Integrated Solutions (SiS) is a specialist provider of industrial automation, IIoT, and data-driven engineering solutions tailored to heavy industries across MENA.',
                    'Our approach combines field-proven OT engineering with modern data science and cloud-first analytics to deliver measurable business outcomes such as reduced downtime and optimized throughput.',
                    'Our multidisciplinary team includes control systems engineers, data scientists, and systems integrators who work alongside client teams from feasibility through sustained operations.',
                ],
                'mission' => [
                    'tagline' => 'OUR MISSION',
                    'title' => 'Our Mission',
                    'description' => 'To bridge Operational Technology (OT) and Information Technology (IT) through real-time data-driven decision-making.',
                    'icon' => 'flag',
                ],
                'vision' => [
                    'tagline' => 'OUR VISION',
                    'title' => 'Our Vision',
                    'description' => 'To establish a future standard of predictive maintenance and complete process optimization across the region\'s heaviest industries.',
                    'icon' => 'visibility',
                ],
                'why_choose_us' => [
                    'title' => 'Why Choose SiS?',
                    'description' => 'We combine deep industry expertise with cutting-edge technology to deliver sustainable, future-proof industrial solutions across the MENA region.',
                ],
                'pillars' => [
                    [
                        'icon' => 'mission',
                        'title' => 'Mission',
                        'body' => 'To bridge Operational Technology (OT) and Information Technology (IT) through real-time data-driven decision-making.',
                    ],
                    [
                        'icon' => 'vision',
                        'title' => 'Vision',
                        'body' => 'To establish a future standard of predictive maintenance and complete process optimization across the region\'s heaviest industries.',
                    ],
                    [
                        'icon' => 'values',
                        'title' => 'Core Values',
                        'body' => 'Delivering excellence through a team of highly skilled engineers, blending deep industrial legacy with modern data science.',
                    ],
                ],
                'principles' => [
                    [
                        'title' => 'Precision',
                        'body' => 'Execution of highly complex industrial automation and data science with exactitude.',
                    ],
                    [
                        'title' => 'Innovation',
                        'body' => 'Pioneering custom software and IIoT approaches to solve legacy challenges.',
                    ],
                    [
                        'title' => 'Reliability',
                        'body' => 'Successfully implementing and sustaining large-scale solutions for critical regional industries.',
                    ],
                ],
                'is_active' => true,
            ]
        );

        // 13. About Hero
        AboutHero::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'PIONEERING INDUSTRIAL EVOLUTION',
                'headline' => 'Architects of the 4th Industrial Revolution',
                'subheadline' => 'Superior Integrated Solutions (SiS) bridges the critical divide between Operational Technology and Information Technology, engineering the intelligent ecosystems of tomorrow.',
                'cta_text' => 'Discover Our Expertise',
                'cta_link' => '/projects',
                'image' => 'images/about-hero.webp',
                'is_active' => true,
            ]
        );

        // 14. IT-OT Divide
        ItOtDivide::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'THE CONVERGENCE',
                'headline' => 'Bridging the IT-OT Divide',
                'subheadline' => 'Historically, the shop floor and the top floor spoke different languages. SiS acts as the master translator, unifying these domains into a single, cohesive nervous system.',
                'ot_data' => [
                    'title' => 'Operational Technology (OT)',
                    'description' => 'The hardware and software that detects or causes a change through the direct monitoring and/or control of industrial equipment, assets, processes, and events.',
                    'tags' => ['SCADA', 'PLCs', 'Sensors', 'DCS'],
                    'icon' => 'precision_manufacturing',
                ],
                'it_data' => [
                    'title' => 'Information Technology (IT)',
                    'description' => 'The use of computers, storage, networking, and other physical devices to create, process, store, secure, and exchange all forms of electronic data.',
                    'tags' => ['ERP', 'Cloud', 'Analytics', 'MES'],
                    'icon' => 'cloud',
                ],
                'is_active' => true,
            ]
        );

        // 15. Core Values & Principles
        CoreValue::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'WHAT WE STAND FOR',
                'headline' => 'Core Values & Principles',
                'subheadline' => 'Delivering excellence through a team of highly skilled engineers, blending deep industrial legacy with modern data science.',
                'principles' => [
                    [
                        'step' => '01',
                        'title' => 'Precision',
                        'description' => 'Execution of highly complex industrial automation and data science with exactitude.',
                        'icon' => 'verified_user',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Innovation',
                        'description' => 'Pioneering custom software and IIoT approaches to solve legacy challenges.',
                        'icon' => 'lightbulb',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Reliability',
                        'description' => 'Successfully implementing and sustaining large-scale solutions for critical regional industries.',
                        'icon' => 'verified',
                    ],
                ],
                'is_active' => true,
            ]
        );

        // 16. Empowered Domains
        EmpoweredDomain::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'OUR DOMAIN EXPERTISE',
                'headline' => 'Industries We Empower',
                'subheadline' => 'Deep technical understanding applied across the most demanding industrial sectors.',
                'domains' => [
                    [
                        'icon' => 'oil',
                        'title' => 'Oil & Gas',
                        'description' => 'Deployment of Well Head Wireless HART Transmitters, SCADA integration, and Well Heads Safety System Upgrades for upstream oil and gas operations.',
                    ],
                    [
                        'icon' => 'science',
                        'title' => 'Chemical Industries',
                        'description' => 'Advanced automation solutions for process optimisation within chemical manufacturing plants, including centralised SCADA systems.',
                    ],
                    [
                        'icon' => 'engineering',
                        'title' => 'Heavy Industries',
                        'description' => 'Industrial automation and support services for large-scale cement manufacturing and metal foundry operations.',
                    ],
                    [
                        'icon' => 'water',
                        'title' => 'Water & Waste Water',
                        'description' => 'PLC and SCADA commissioning, network cybersecurity hardening, and full treatment process automation for water infrastructure.',
                    ],
                    [
                        'icon' => 'factory',
                        'title' => 'Food, Beverage & FMCG',
                        'description' => 'SCADA upgrades, ERP integration, AR-assisted validation, and specialised automation for food production and chemical mixing.',
                    ],
                ],
                'is_active' => true,
            ]
        );

        // 17. Footer Settings & Links
        $this->call(FooterSeeder::class);

        // 18. Global SEO Settings
        $this->call(SeoSettingsSeeder::class);
    }
}
