<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighting_setups', function (Blueprint $table) {
            // Add Dimming 3 fields
            $table->unsignedTinyInteger('dimming3_h')->nullable()->after('dimming2_value');
            $table->unsignedTinyInteger('dimming3_m')->nullable()->after('dimming3_h');
            $table->unsignedTinyInteger('dimming3_s')->nullable()->after('dimming3_m');
            $table->unsignedSmallInteger('dimming3_value')->nullable()->after('dimming3_s');
        });
    }

    public function down(): void
    {
        Schema::table('lighting_setups', function (Blueprint $table) {
            $table->dropColumn(['dimming3_h', 'dimming3_m', 'dimming3_s', 'dimming3_value']);
        });
    }
};
