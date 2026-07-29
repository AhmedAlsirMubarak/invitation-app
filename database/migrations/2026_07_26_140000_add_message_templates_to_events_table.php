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
            // Null = use the event_type's default WhatsApp wording
            // (WhatsAppLinkService). Set = admin's own text overrides it,
            // with {name}/{event_title}/{date}/{time}/{venue}/
            // {venue_map_url}/{invitation_url} placeholders.
            $table->text('invite_message_template')->nullable()->after('description');
            $table->text('reminder_message_template')->nullable()->after('invite_message_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['invite_message_template', 'reminder_message_template']);
        });
    }
};
