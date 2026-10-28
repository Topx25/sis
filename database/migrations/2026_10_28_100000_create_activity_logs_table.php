<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();   // denormalized so it survives user deletion
            $table->string('method', 10);               // POST / PUT / PATCH / DELETE
            $table->string('path');                     // e.g. v1/admin/projects/5
            $table->string('ip', 45)->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
