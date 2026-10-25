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
        Schema::create('home_why_sis_sections', function (Blueprint $table) {
            $table->id();
            $table->json('left_title')->nullable();
            $table->json('right_subtitle')->nullable();
            $table->json('right_title')->nullable();
            $table->json('right_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_why_sis_sections');
    }
};
