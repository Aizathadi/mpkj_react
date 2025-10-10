<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    /**
     * Show alarms grouped by site (from Alarm table).
     */
    public function index()
    {
        $alarms = Alarm::orderBy('site_name')->get()->groupBy('site_name');
        return view('assets.alarmstatus', compact('alarms'));
    }

    /**
     * Return all alarms as JSON.
     */
    public function data()
    {
        $alarms = Alarm::orderBy('timestamp', 'desc')->get();
        return response()->json($alarms);
    }

    /**
     * Return active alarms for popup (per asset or per site).
     */
    public function popupData(Request $request)
    {
        $query = Alarm::where('alarm_status', '!=', 'Normal')
            ->orderBy('timestamp', 'desc')
            ->select(['id', 'asset_no', 'site_name', 'alarm_status', 'timestamp']);

        if ($request->filled('site')) {
            $query->where('site_name', $request->site);
        }

        if ($request->filled('asset_no')) {
            $query->where('asset_no', $request->asset_no);
        }

        $alarms = $query->get()->map(function ($a) {
            return [
                'id'           => $a->id,
                'asset_no'     => $a->asset_no,
                'site_name'    => $a->site_name,
                'alarm'        => $a->alarm_status, // ✅ use alarm_status
                'alarm_status' => $a->alarm_status,
                'timestamp'    => optional($a->timestamp)->toDateTimeString(),
            ];
        });

        return response()->json($alarms->values());
    }

    /**
     * Return all active alarms as a flat array for dashboard popups.
     */
    public function popupAll()
    {
        $alarms = Alarm::where('alarm_status', '!=', 'Normal')
            ->orderBy('timestamp', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id'           => $a->id,
                    'asset_no'     => $a->asset_no,
                    'site_name'    => $a->site_name,
                    'alarm'        => $a->alarm_status, // ✅ use alarm_status
                    'alarm_status' => $a->alarm_status,
                    'timestamp'    => optional($a->timestamp)->toDateTimeString(),
                ];
            });

        return response()->json($alarms);
    }

    /**
     * Clear a single alarm by ID.
     */
    public function destroy($id)
    {
        $alarm = Alarm::find($id);
        if ($alarm) {
            $alarm->delete();
            return response()->json([
                'success' => true,
                'message' => 'Alarm cleared',
                'count'   => $this->getActiveCount()
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Alarm not found'], 404);
    }

    /**
     * Clear all alarms for a given site.
     */
    public function clearSite($site)
    {
        $deleted = Alarm::where('site_name', $site)->delete();
        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} alarms for site {$site}",
            'count'   => $this->getActiveCount()
        ]);
    }

    /**
     * Clear ALL alarms.
     */
    public function clear()
    {
        $count = Alarm::count();
        Alarm::truncate();
        return response()->json([
            'success' => true,
            'message' => "Cleared all {$count} alarms",
            'count'   => 0
        ]);
    }

    /**
     * Return alarm count (for badge refresh).
     */
    public function count()
    {
        return response()->json(['count' => $this->getActiveCount()]);
    }

    /**
     * Helper: count active alarms (not "Normal").
     */
    private function getActiveCount()
    {
        return Alarm::where('alarm_status', '!=', 'Normal')->count();
    }
}
