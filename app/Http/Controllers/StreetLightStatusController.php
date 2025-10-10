<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StreetLightStatus;
use App\Models\AssetRegistration;
use Carbon\Carbon;

class StreetLightStatusController extends Controller
{
    public function index()
    {
        // Eager load the asset to access site name
        $statuses = StreetLightStatus::with('asset')->get();

        // Group by site name (from StreetLightStatus table)
        $groupedStatuses = $statuses->groupBy(function ($status) {
            return $status->site_name ?? 'Unknown Site';
        });

        return view('assets.status', compact('groupedStatuses'));
    }

    public function store(Request $request)
    {
        // Find the asset by asset_id
        $asset = AssetRegistration::find($request->asset_id);

        // If the asset exists, take its site_name; otherwise set as 'Unknown'
        $siteName = $asset ? $asset->site_name : 'Unknown';

        // Create a new streetlight status with site_name auto-filled
        StreetLightStatus::create([
            'asset_id'     => $request->asset_id,
            'asset_no'     => $request->asset_no,
            'site_name'    => $siteName,
            'status'       => $request->status,
            'led_status'   => $request->led_status,
            'dimming'      => $request->dimming,
            'longitude'    => $request->longitude,
            'latitude'     => $request->latitude,
            'ampere'       => $request->ampere,
            'volt'         => $request->volt,
            'frequency'    => $request->frequency,
            'power'        => $request->power,
            'energy'       => $request->energy,
            'alarm_status' => $request->alarm_status,
            'lux'          => $request->lux,
            'local_time'   => $request->local_time,
            'last_seen_at' => Carbon::now(),   // ✅ Add this
        ]);

        return redirect()->back()->with('success', 'Streetlight status saved successfully!');
    }

    public function update(Request $request, $id)
    {
        $status = StreetLightStatus::findOrFail($id);
        $asset  = AssetRegistration::find($request->asset_id);

        $status->update([
            'asset_id'     => $request->asset_id,
            'asset_no'     => $request->asset_no,
            'site_name'    => $asset ? $asset->site_name : 'Unknown',
            'status'       => $request->status,
            'led_status'   => $request->led_status,
            'dimming'      => $request->dimming,
            'longitude'    => $request->longitude,
            'latitude'     => $request->latitude,
            'ampere'       => $request->ampere,
            'volt'         => $request->volt,
            'frequency'    => $request->frequency,
            'power'        => $request->power,
            'energy'       => $request->energy,
            'alarm_status' => $request->alarm_status,
            'lux'          => $request->lux,
            'local_time'   => $request->local_time,
            'last_seen_at' => Carbon::now(),   // ✅ Add this
        ]);

        return redirect()->back()->with('success', 'Streetlight status updated successfully!');
    }

    /**
     * ✅ API endpoint for dashboard markers
     * Returns calculated Online/Offline (without overwriting DB)
     */
    public function getStatusData()
    {
        $lights = StreetLightStatus::all()->map(function ($light) {
            $isOnline = $light->last_seen_at && $light->last_seen_at->gt(now()->subMinutes(15));

            return [
                'id'         => $light->id,
                'asset_no'   => $light->asset_no,
                'site_name'  => $light->site_name,
                'status'     => $isOnline ? 'Online' : 'Offline',   // ✅ calculated here
                'led_status' => $isOnline ? $light->led_status : 0, // ✅ force OFF if offline
                'dimming'    => $light->dimming,
                'volt'       => $light->volt,
                'ampere'     => $light->ampere,
                'frequency'  => $light->frequency,
                'power'      => $light->power,
                'energy'     => $light->energy,
                'alarm_status' => $light->alarm_status,
                'lux'        => $light->lux,
                'longitude'  => $light->longitude,
                'latitude'   => $light->latitude,
                'local_time' => $light->local_time,
                'last_seen_at' => $light->last_seen_at,
            ];
        });

        return response()->json($lights);
    }
}
