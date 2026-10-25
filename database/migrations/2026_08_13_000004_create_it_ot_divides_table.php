<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_ot_divides', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->default('THE CONVERGENCE');
            $table->string('headline')->default('Bridging the IT-OT Divide');
            $table->text('subheadline')->nullable();
            $table->json('ot_data')->nullable();
            $table->json('it_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_ot_divides');
    }
};
