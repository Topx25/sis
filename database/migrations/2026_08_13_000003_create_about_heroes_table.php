<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->default('PIONEERING INDUSTRIAL EVOLUTION');
            $table->string('headline')->default('Architects of the 4th Industrial Revolution');
            $table->text('subheadline')->nullable();
            $table->string('cta_text')->nullable()->default('Discover Our Expertise');
            $table->string('cta_link')->nullable()->default('/projects');
            $table->string('image')->nullable()->default('images/about-hero.webp');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_heroes');
    }
};
