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
        Schema::create('work_samples', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique();
            $table->string('title');
            $table->string('industry')->nullable();
            $table->string('category')->nullable();
            $table->text('blurb')->nullable();
            $table->text('body')->nullable();
            $table->boolean('free')->default(false);
            $table->string('badge')->nullable()->default('DEMO ACCESS');
            $table->string('demo_url')->nullable();
            $table->string('image')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_samples');
    }
};
