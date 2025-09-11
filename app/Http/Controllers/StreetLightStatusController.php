<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StreetLightStatus;
use App\Models\AssetRegistration;

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
            'site_name'    => $siteName,  // <-- Auto-fill site name
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
        ]);

        return redirect()->back()->with('success', 'Streetlight status updated successfully!');
    }
}
