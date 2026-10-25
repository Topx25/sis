<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false);
            $table->text('tagline')->nullable()->after('is_featured');
            $table->text('description')->nullable()->after('tagline');
            $table->string('solutions_link')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'tagline', 'description', 'solutions_link']);
        });
    }
};
