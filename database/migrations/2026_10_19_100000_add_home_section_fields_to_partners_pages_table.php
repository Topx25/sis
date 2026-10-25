<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners_pages', function (Blueprint $table) {
            $table->text('home_subtitle')->nullable();
            $table->text('home_title')->nullable();
            $table->text('home_title_highlight')->nullable();
            $table->text('home_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('partners_pages', function (Blueprint $table) {
            $table->dropColumn([
                'home_subtitle',
                'home_title',
                'home_title_highlight',
                'home_description',
            ]);
        });
    }
};
