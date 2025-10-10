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
        Schema::table('lighting_setups', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('asset_id');
            $table->string('asset_no')->nullable()->after('site_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lighting_setups', function (Blueprint $table) {
            $table->dropColumn(['site_name', 'asset_no']);
        });
    }
};
