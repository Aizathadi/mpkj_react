<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreetLightStatus extends Model
{
    use HasFactory;

    protected $table = 'street_light_status';

    protected $fillable = [
        'asset_no',
        'site_name', 
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

    public function asset()
    {
        return $this->belongsTo(AssetRegistration::class, 'asset_no', 'asset_no');
    }

    protected static function booted()
    {
        static::saving(function ($status) {
            // Match using asset_no
            if ($status->asset_no) {
                $asset = \App\Models\AssetRegistration::where('asset_no', $status->asset_no)->first();
                if ($asset) {
                    $status->site_name = $asset->site_name;
                }
            }
        });
    }
}
