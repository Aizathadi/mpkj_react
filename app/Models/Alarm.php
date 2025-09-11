<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    use HasFactory;

    // Table name (optional, Laravel will guess "alarms")
    protected $table = 'alarms';

    // Allow mass assignment
    protected $fillable = [
        'asset_id',
        'site_name',
        'asset_no',
        'latitude',
        'longitude',
        'alarm_status',
        'timestamp',
    ];

    // Relationship: an alarm belongs to an asset
    public function asset()
    {
        return $this->belongsTo(AssetRegistration::class, 'asset_id');
    }
}
