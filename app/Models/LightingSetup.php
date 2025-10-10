<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightingSetup extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'site_name',
        'asset_no',

        // On/Off times
        'on_time_h', 'on_time_m', 'on_time_s',
        'off_time_h', 'off_time_m', 'off_time_s',

        // Dimming 1
        'dimming1_h', 'dimming1_m', 'dimming1_s', 'dimming1_value',

        // Dimming 2
        'dimming2_h', 'dimming2_m', 'dimming2_s', 'dimming2_value',

        // Dimming 3
        'dimming3_h', 'dimming3_m', 'dimming3_s', 'dimming3_value',

        // Dimming 4
        'dimming4_h', 'dimming4_m', 'dimming4_s', 'dimming4_value',

        // Lux control
        'lux_on', 'lux_off', 'lux_delay',
    ];

    /**
     * Each Lighting Setup belongs to one Asset
     */
    public function asset()
    {
        return $this->belongsTo(AssetRegistration::class, 'asset_id');
    }
}
