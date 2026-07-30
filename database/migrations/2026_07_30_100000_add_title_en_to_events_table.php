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
            // Optional English spelling of the event title, shown on the
            // guest-facing invitation page when viewed in English. Falls
            // back to `title` when left blank. Admin panel always shows
            // the primary `title`.
            $table->string('title_en')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });
    }
};
