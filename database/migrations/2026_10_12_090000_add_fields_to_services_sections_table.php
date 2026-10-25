<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('services_sections', 'subtitle')) {
                $table->string('subtitle')->nullable();
            }
            if (!Schema::hasColumn('services_sections', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('services_sections', 'title_highlight')) {
                $table->string('title_highlight')->nullable();
            }
            if (!Schema::hasColumn('services_sections', 'description')) {
                $table->text('description')->nullable();
            }
        });

        if (\Illuminate\Support\Facades\DB::table('services_sections')->where('id', 1)->doesntExist()) {
            \Illuminate\Support\Facades\DB::table('services_sections')->insert([
                'id' => 1,
                'subtitle' => 'Services & Capabilities',
                'title' => 'One platform,',
                'title_highlight' => 'every',
                'description' => 'industrial capability',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services_sections', function (Blueprint $table) {
            $columns = [];
            foreach (['subtitle', 'title', 'title_highlight', 'description'] as $col) {
                if (Schema::hasColumn('services_sections', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
