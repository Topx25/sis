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
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->string('copyright_text')->nullable();
            $table->boolean('dynamic_year')->default(true);
            $table->boolean('floating_contact_enabled')->default(true);
            $table->string('floating_contact_url')->nullable();
            $table->string('floating_contact_icon')->default('whatsapp');
            $table->string('floating_contact_label')->default('Contact us on WhatsApp');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->default('navigation')->index(); // navigation | contact
            $table->string('type', 50)->default('custom')->index(); // navigation, social, phone, whatsapp, email, custom
            $table->string('label');
            $table->string('url', 1000);
            $table->string('target', 20)->default('_self'); // _self | _blank
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('footer_settings');
    }
};
