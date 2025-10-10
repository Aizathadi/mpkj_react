<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('street_light_data', function (Blueprint $table) {
            // ✅ Add last_seen_at after latitude for clarity
            $table->timestamp('last_seen_at')->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('street_light_data', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });
    }
};
