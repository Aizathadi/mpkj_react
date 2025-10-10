<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('street_light_status', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('local_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('street_light_status', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });
    }
};
