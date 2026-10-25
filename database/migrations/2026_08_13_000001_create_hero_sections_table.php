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
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->default('Superior Integrated Solutions');
            $table->string('headline');
            $table->text('subheadline');
            $table->string('image')->nullable()->default('images/factory.webp');
            $table->string('cta_primary')->default('Explore Our Work');
            $table->string('cta_primary_link')->nullable()->default('/projects');
            $table->string('cta_secondary')->default('Get In Touch');
            $table->string('cta_secondary_link')->nullable()->default('/contact');
            $table->json('stats')->nullable();
            $table->string('badge_text')->nullable()->default('Operational since 2020 · 12+ industrial deployments live');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
