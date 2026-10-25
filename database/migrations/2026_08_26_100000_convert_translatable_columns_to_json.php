<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Define the tables and their translatable string columns.
     */
    protected array $translatableMap = [
        'hero_sections' => [
            'tagline',
            'headline',
            'subheadline',
            'cta_primary',
            'cta_secondary',
            'badge_text',
        ],
        'about_sections' => [
            'title',
            'headline',
            'subheadline',
        ],
        'about_heroes' => [
            'tagline',
            'headline',
            'subheadline',
            'cta_text',
        ],
        'it_ot_divides' => [
            'tagline',
            'headline',
            'subheadline',
        ],
        'core_values' => [
            'tagline',
            'headline',
            'subheadline',
        ],
        'empowered_domains' => [
            'tagline',
            'headline',
            'subheadline',
        ],
        'projects' => [
            'title',
            'client_name',
            'location',
            'summary',
            'description',
        ],
        'services' => [
            'title',
            'summary',
            'description',
        ],
        'sectors' => [
            'name',
            'description',
        ],
        'partners' => [
            'name',
        ],
        'clients' => [
            'name',
        ],
        'timeline_events' => [
            'title',
            'description',
        ],
        'pillars' => [
            'title',
            'body',
        ],
        'architecture_stages' => [
            'title',
            'body',
        ],
        'work_samples' => [
            'title',
            'industry',
            'blurb',
            'body',
            'badge',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->translatableMap as $tableName => $columns) {
            $rows = DB::table($tableName)->get();

            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $val = $row->$column ?? null;

                    if ($val !== null && $val !== '') {
                        // Check if already json encoded with translations
                        $decoded = json_decode($val, true);
                        if (!is_array($decoded) || (!isset($decoded['en']) && !isset($decoded['ar']))) {
                            // Convert plain string to translatable json format with 'en' as default
                            $updates[$column] = json_encode(['en' => (string) $val], JSON_UNESCAPED_UNICODE);
                        }
                    }
                }

                if (!empty($updates)) {
                    DB::table($tableName)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->translatableMap as $tableName => $columns) {
            $rows = DB::table($tableName)->get();

            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $val = $row->$column ?? null;

                    if ($val !== null && $val !== '') {
                        $decoded = json_decode($val, true);
                        if (is_array($decoded)) {
                            // Revert back to plain string (preference to 'en', then first available)
                            $updates[$column] = $decoded['en'] ?? (reset($decoded) ?: '');
                        }
                    }
                }

                if (!empty($updates)) {
                    DB::table($tableName)->where('id', $row->id)->update($updates);
                }
            }
        }
    }
};
