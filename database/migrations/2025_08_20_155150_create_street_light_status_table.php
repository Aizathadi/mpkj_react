<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('street_light_status', function (Blueprint $table) {
            $table->id();

            // Foreign key to asset registrations
            $table->foreignId('asset_id')->constrained('asset_registrations')->onDelete('cascade');
            $table->string('asset_no');

            $table->enum('status', ['Online', 'Offline']);
            $table->boolean('led_status');
            $table->integer('dimming')->nullable();

            $table->decimal('ampere', 8, 3)->nullable();
            $table->decimal('volt', 8, 2)->nullable();
            $table->decimal('frequency', 5, 2)->nullable();
            $table->decimal('power', 10, 2)->nullable();
            $table->decimal('energy', 12, 3)->nullable();

            $table->enum('alarm_status', ['NORMAL', 'WARNING', 'ALARM'])->default('NORMAL');
            $table->integer('lux')->nullable();

            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();

            $table->timestamp('local_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('street_light_status');
    }
};
