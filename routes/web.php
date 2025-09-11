<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetRegistrationController;
use App\Models\StreetLightStatus; 
use App\Http\Controllers\StreetLightStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MqttPublishController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\AlarmConfigurationController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * 🔹 Alarm Status (viewing/clearing)
     */
    Route::get('/alarms', [AlarmController::class, 'index'])->name('alarms.index');
    Route::get('/alarms/data', [AlarmController::class, 'data'])->name('alarms.data');
    Route::delete('/alarms/{id}', [AlarmController::class, 'destroy'])->name('alarms.destroy');
    Route::post('/alarms/{site}/clear-site', [AlarmController::class, 'clearSite'])->name('alarms.clearSite');
    Route::post('/alarms/clear', [AlarmController::class, 'clear'])->name('alarms.clear');
    Route::get('/alarms/count', [AlarmController::class, 'count'])->name('alarms.count');

    /**
     * 🔹 Alarm Configuration (setup/programming via MQTT)
     */
    Route::get('/alarms/configuration', [AlarmConfigurationController::class, 'index'])
        ->name('alarm.configuration');
    Route::post('/alarms/program', [AlarmConfigurationController::class, 'program'])
        ->name('alarm.program');

    // Streetlight control (POST)
    Route::post('/mqtt/publish', [MqttPublishController::class, 'publish'])
        ->name('mqtt.publish');

    // Asset Registration CRUD routes
    Route::resource('assets', AssetRegistrationController::class);

    // Status for Lighting Asset
    Route::get('/streetlight/status', [StreetLightStatusController::class, 'index'])
        ->name('streetlight.status');

    // Street light status for GIS dashboard (JSON API)
    Route::get('/streetlight/status/data', function () {
        return StreetLightStatus::all()->map(function ($d) {
            return [
                'site_name'  => $d->site_name ?? 'N/A',
                'asset_no'   => $d->asset_no ?? 'N/A',
                'latitude'   => $d->latitude,
                'longitude'  => $d->longitude,
                'status'     => $d->status ?? 'Offline',
                'led_status' => $d->led_status,
                'dimming'    => $d->dimming,
                'volt'       => $d->volt,
                'ampere'     => $d->ampere,
                'power'      => $d->power,
                'energy'     => $d->energy,
                'lux'        => $d->lux,
            ];
        });
    })->name('streetlight.status.data');
});

require __DIR__.'/auth.php';
