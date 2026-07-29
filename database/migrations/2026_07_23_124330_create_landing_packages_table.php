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
        Schema::create('landing_packages', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "...", "en": "..."}
            $table->json('price_label')->nullable(); // {"ar": "...", "en": "..."} — display text, no real checkout
            $table->json('description')->nullable(); // {"ar": "...", "en": "..."}
            $table->json('features'); // [{"ar": "...", "en": "..."}, ...] bullet list
            $table->json('badge')->nullable(); // {"ar": "...", "en": "..."} e.g. "Most popular"
            $table->json('cta_label')->nullable(); // {"ar": "...", "en": "..."}
            $table->string('cta_url')->nullable();
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
        Schema::dropIfExists('landing_packages');
    }
};
