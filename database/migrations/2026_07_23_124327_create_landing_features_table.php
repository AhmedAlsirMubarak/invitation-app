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
        Schema::create('landing_features', function (Blueprint $table) {
            $table->id();
            // core: the main 6 service cards. highlight: multi-organizer /
            // replacement invites / WhatsApp+SMS delivery. how_it_works: the
            // 3-step process cards.
            $table->string('group');
            $table->string('icon')->nullable(); // a single emoji, admin-typed
            $table->json('title'); // {"ar": "...", "en": "..."}
            $table->json('body'); // {"ar": "...", "en": "..."}
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_features');
    }
};
