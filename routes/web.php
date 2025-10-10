<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetRegistrationController;
use App\Models\StreetLightStatus; 
use App\Http\Controllers\StreetLightStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MqttPublishController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\AlarmConfigurationController;
use App\Http\Controllers\LightingSetupController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


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
     * 🔹 Alarm Status
     */
    Route::get('/alarms', [AlarmController::class, 'index'])->name('alarms.index');
    Route::get('/alarms/data', [AlarmController::class, 'data'])->name('alarms.data');
    Route::get('/alarms/popup', [AlarmController::class, 'popupData'])->name('alarms.popup');
    Route::get('/alarms/popup/all', [AlarmController::class, 'popupAll'])->name('alarms.popup.all');
    Route::delete('/alarms/{id}', [AlarmController::class, 'destroy'])->name('alarms.destroy');
    Route::post('/alarms/{site}/clear-site', [AlarmController::class, 'clearSite'])->name('alarms.clearSite');
    Route::post('/alarms/clear', [AlarmController::class, 'clear'])->name('alarms.clear');
    Route::get('/alarms/count', [AlarmController::class, 'count'])->name('alarms.count');

    /**
     * 🔹 Alarm Configuration
     */
    Route::get('/alarms/configuration', [AlarmConfigurationController::class, 'index'])
        ->name('alarm.configuration');
    Route::post('/alarms/program', [AlarmConfigurationController::class, 'program'])
        ->name('alarm.program');

    // MQTT Control
    Route::post('/mqtt/publish', [MqttPublishController::class, 'publish'])
        ->name('mqtt.publish');

    // Asset Registration CRUD
    Route::resource('assets', AssetRegistrationController::class);

    // Status for Lighting Asset
    Route::get('/streetlight/status', [StreetLightStatusController::class, 'index'])
        ->name('streetlight.status');

    // Street light status for GIS dashboard (JSON API)
    Route::get('/streetlight/status/data', function () {
        return StreetLightStatus::all()->map(function ($d) {
            return [
                'site_name'    => $d->site_name ?? 'N/A',
                'asset_no'     => $d->asset_no ?? 'N/A',
                'latitude'     => $d->latitude,
                'longitude'    => $d->longitude,
                'status'       => $d->is_online ? 'Online' : 'Offline',
                'led_status'   => $d->led_status,
                'dimming'      => $d->dimming,
                'volt'         => $d->volt,
                'ampere'       => $d->ampere,
                'power'        => $d->power,
                'energy'       => $d->energy,
                'lux'          => $d->lux,
                'last_seen_at' => optional($d->last_seen_at)->toDateTimeString(),
            ];
        });
    })->name('streetlight.status.data');

    /**
     * 🔹 Lighting Setup
     */
    Route::get('/lighting-setup', [LightingSetupController::class, 'index'])
    ->name('lighting.setup.index');

   Route::post('/lighting-setup', [LightingSetupController::class, 'store'])
    ->name('lighting.setup.store');

   // Lux Setup (multi-asset, no {assetId})
   Route::post('/lighting-setup/lux', [LightingSetupController::class, 'updateLux'])
    ->name('lighting.setup.lux');

    
    //Telegram
   Route::get('/send-telegram-test', [TelegramController::class, 'sendTest']);
    Route::post('/alarms/{id}/report', [TelegramController::class, 'sendAlarmReport']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
