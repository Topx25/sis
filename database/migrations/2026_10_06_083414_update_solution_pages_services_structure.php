<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SolutionPage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pages = SolutionPage::all();
        foreach ($pages as $page) {
            $servicesTranslations = $page->getTranslations('services');
            $updated = false;

            foreach ($servicesTranslations as $locale => $services) {
                if (!is_array($services)) continue;

                foreach ($services as $index => $service) {
                    if (!isset($service['focus'])) {
                        $services[$index]['focus'] = 'Expert delivery';
                        $updated = true;
                    }
                    if (!isset($service['approach'])) {
                        $services[$index]['approach'] = 'Practical guidance';
                        $updated = true;
                    }
                    if (!isset($service['outcome'])) {
                        $services[$index]['outcome'] = 'Long-term value';
                        $updated = true;
                    }
                }
                
                $servicesTranslations[$locale] = $services;
            }

            if ($updated) {
                $page->setTranslations('services', $servicesTranslations);
                $page->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $pages = SolutionPage::all();
        foreach ($pages as $page) {
            $servicesTranslations = $page->getTranslations('services');
            $updated = false;

            foreach ($servicesTranslations as $locale => $services) {
                if (!is_array($services)) continue;

                foreach ($services as $index => $service) {
                    if (isset($services[$index]['focus'])) {
                        unset($services[$index]['focus']);
                        $updated = true;
                    }
                    if (isset($services[$index]['approach'])) {
                        unset($services[$index]['approach']);
                        $updated = true;
                    }
                    if (isset($services[$index]['outcome'])) {
                        unset($services[$index]['outcome']);
                        $updated = true;
                    }
                }
                
                $servicesTranslations[$locale] = $services;
            }

            if ($updated) {
                $page->setTranslations('services', $servicesTranslations);
                $page->save();
            }
        }
    }
};
