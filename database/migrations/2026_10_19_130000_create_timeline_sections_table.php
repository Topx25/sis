<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline_sections', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->nullable()->default('Our journey');
            $table->string('title')->nullable()->default('A trajectory of');
            $table->string('title_highlight')->nullable()->default('growth.');
            $table->timestamps();
        });

        if (DB::table('timeline_sections')->where('id', 1)->doesntExist()) {
            DB::table('timeline_sections')->insert([
                'id' => 1,
                'tagline' => 'Our journey',
                'title' => 'A trajectory of',
                'title_highlight' => 'growth.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_sections');
    }
};
