<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // category groups templates so you can filter the library fast
            $table->string('category'); // wedding | birthday | corporate | graduation | other
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            // maps to a blade partial in resources/views/invitations/templates/{slug}.blade.php
            $table->string('blade_view');
            // default colors/fonts, can be overridden per-event
            $table->json('default_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
