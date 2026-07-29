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
            // unguessable link the client uses to manage their own guest
            // list without an admin login — scoped to this one event only
            $table->string('client_token', 64)->unique()->nullable()->after('status');
            // max guests the client is allowed to add themselves; null = unlimited
            $table->unsignedInteger('guest_quota')->nullable()->after('client_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['client_token', 'guest_quota']);
        });
    }
};
