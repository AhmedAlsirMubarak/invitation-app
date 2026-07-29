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
        Schema::table('events', function (Blueprint $table) {
            // basic: invitation page only, no RSVP buttons and no WhatsApp
            // sending. premium: full service — RSVP accept/decline plus
            // WhatsApp invite/reminder sending.
            $table->string('package')->default('premium')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('package');
        });
    }
};
