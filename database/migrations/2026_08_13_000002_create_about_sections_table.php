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
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Who We Are');
            $table->string('headline')->default('Our Mission & Vision');
            $table->text('subheadline')->nullable();
            $table->json('paragraphs')->nullable();
            $table->json('mission')->nullable();
            $table->json('vision')->nullable();
            $table->json('why_choose_us')->nullable();
            $table->json('pillars')->nullable();
            $table->json('principles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
