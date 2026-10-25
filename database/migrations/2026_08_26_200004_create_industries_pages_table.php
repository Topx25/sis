<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industries_pages', function (Blueprint $table) {
            $table->id();
            $table->text('tagline');
            $table->text('headline');
            $table->text('description');
            $table->text('button_text')->nullable();
            $table->string('button_link')->nullable()->default('/sectors');
            $table->string('image')->nullable();
            $table->json('stats')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industries_pages');
    }
};
