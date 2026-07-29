<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('companions_count')->default(0);
            $table->string('table_number')->nullable();
            $table->string('rsvp_status')->default('pending'); // pending | accepted | declined
            $table->timestamp('rsvp_at')->nullable();
            // random, unguessable token used in the public invitation link and QR payload
            $table->string('token', 64)->unique();
            $table->string('qr_code_path')->nullable();
            $table->timestamp('invite_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
