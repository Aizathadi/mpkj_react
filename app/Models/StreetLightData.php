<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreetLightData extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',      // FK to asset registration
        'asset_no',      // store asset number directly
        'status',
        'led_status',
        'dimming',
        'longitude',
        'latitude',
        'ampere',
        'volt',
        'frequency',
        'power',
        'energy',
        'alarm_status',
        'lux',
        'local_time',
    ];

    // Optional: relationship to AssetRegistration
    public function asset()
    {
        return $this->belongsTo(AssetRegistration::class, 'asset_id', 'id');
    }
}
