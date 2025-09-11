<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alarms', function (Blueprint $table) {
            $table->id();

            // Link to asset
            $table->unsignedBigInteger('asset_id')->unique(); 
            $table->string('site_name');   // ✅ added site_name
            $table->string('asset_no');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);

            // Alarm fields
            $table->string('alarm_status');  
            $table->timestamp('timestamp')->useCurrent();

            // FK constraint
            $table->foreign('asset_id')->references('id')->on('asset_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alarms');
    }
};
