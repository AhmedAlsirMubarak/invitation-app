<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->string('title'); // e.g. "Ahmed & Fatima's Wedding"
            $table->string('event_type'); // wedding | birthday | corporate | graduation | other
            $table->dateTime('event_date');
            $table->string('venue_name')->nullable();
            $table->string('venue_map_url')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            // per-event overrides of the template's default colors/fonts
            $table->json('design_overrides')->nullable();
            $table->string('status')->default('draft'); // draft | active | completed | archived
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
