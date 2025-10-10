<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighting_setups', function (Blueprint $table) {
            // Add Dimming 4 fields
            $table->unsignedTinyInteger('dimming4_h')->nullable()->after('dimming3_value');
            $table->unsignedTinyInteger('dimming4_m')->nullable()->after('dimming4_h');
            $table->unsignedTinyInteger('dimming4_s')->nullable()->after('dimming4_m');
            $table->unsignedSmallInteger('dimming4_value')->nullable()->after('dimming4_s');
        });
    }

    public function down(): void
    {
        Schema::table('lighting_setups', function (Blueprint $table) {
            $table->dropColumn(['dimming4_h', 'dimming4_m', 'dimming4_s', 'dimming4_value']);
        });
    }
};
