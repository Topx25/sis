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
        if (Schema::hasTable('contact_pages') && !Schema::hasColumn('contact_pages', 'emails')) {
            Schema::table('contact_pages', function (Blueprint $table) {
                $table->json('emails')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contact_pages') && Schema::hasColumn('contact_pages', 'emails')) {
            Schema::table('contact_pages', function (Blueprint $table) {
                $table->dropColumn('emails');
            });
        }
    }
};
