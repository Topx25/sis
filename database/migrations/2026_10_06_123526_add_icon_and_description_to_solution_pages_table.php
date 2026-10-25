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
        Schema::table('solution_pages', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('slug');
            $table->text('description')->nullable()->after('subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solution_pages', function (Blueprint $table) {
            $table->dropColumn(['icon', 'description']);
        });
    }
};
