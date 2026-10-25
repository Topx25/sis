<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solution_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->text('title');
            $table->text('subtitle');
            $table->string('hero_image')->nullable();
            $table->json('badges')->nullable();
            $table->json('services')->nullable();
            $table->text('approach_title')->nullable();
            $table->json('approach_steps')->nullable();
            $table->text('why_us_title')->nullable();
            $table->json('why_us_points')->nullable();
            $table->text('industries_title')->nullable();
            $table->json('industries')->nullable();
            $table->text('cta_headline')->nullable();
            $table->text('cta_subheadline')->nullable();
            $table->text('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable()->default('/contact');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solution_pages');
    }
};
