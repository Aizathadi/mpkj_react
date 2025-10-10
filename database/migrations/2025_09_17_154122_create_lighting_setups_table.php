<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighting_setups', function (Blueprint $table) {
            $table->id();

            // 🔗 Asset link (unique - one setup per asset)
            $table->foreignId('asset_id')
                  ->constrained('asset_registrations')
                  ->onDelete('cascade')
                  ->unique()
                  ->index();

            // Time Schedule (On/Off)
            $table->unsignedTinyInteger('on_time_h')->nullable();  // 0–23
            $table->unsignedTinyInteger('on_time_m')->nullable();  // 0–59
            $table->unsignedTinyInteger('on_time_s')->nullable();  // 0–59
            $table->unsignedTinyInteger('off_time_h')->nullable();
            $table->unsignedTinyInteger('off_time_m')->nullable();
            $table->unsignedTinyInteger('off_time_s')->nullable();

            // Dimming 1
            $table->unsignedTinyInteger('dimming1_h')->nullable();
            $table->unsignedTinyInteger('dimming1_m')->nullable();
            $table->unsignedTinyInteger('dimming1_s')->nullable();
            $table->unsignedSmallInteger('dimming1_value')->nullable(); // 0–100 %

            // Dimming 2
            $table->unsignedTinyInteger('dimming2_h')->nullable();
            $table->unsignedTinyInteger('dimming2_m')->nullable();
            $table->unsignedTinyInteger('dimming2_s')->nullable();
            $table->unsignedSmallInteger('dimming2_value')->nullable();

            // Lux Control
            $table->unsignedSmallInteger('lux_on')->nullable();   // lux threshold ON
            $table->unsignedSmallInteger('lux_off')->nullable();  // lux threshold OFF
            $table->unsignedSmallInteger('lux_delay')->nullable(); // seconds delay

            // Extra fields
            $table->string('msg_id')->nullable();   // store msgId
            $table->boolean('active')->default(true);

            // Laravel auto timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighting_setups');
    }
};
