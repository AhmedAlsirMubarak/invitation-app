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
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            // e.g. 'hero_title', 'contact_phone', 'hero_image' — one row per
            // known landing-page field, looked up by key.
            $table->string('key')->unique();
            $table->string('type')->default('text'); // text | textarea | image
            $table->json('value')->nullable(); // {"ar": "...", "en": "..."}
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_settings');
    }
};
