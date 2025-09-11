<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('street_light_status', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('asset_no'); // New column
        });
    }

    public function down()
    {
        Schema::table('street_light_status', function (Blueprint $table) {
            $table->dropColumn('site_name');
        });
    }
};