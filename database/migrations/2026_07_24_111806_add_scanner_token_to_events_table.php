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
            // unguessable link for door staff to open the check-in scanner
            // without an admin login — separate from client_token so
            // handing it out doesn't also grant guest-list management
            $table->string('scanner_token', 64)->unique()->nullable()->after('client_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('scanner_token');
        });
    }
};
