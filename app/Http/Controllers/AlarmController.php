<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    /**
     * Show alarms grouped by site.
     */
    public function index()
    {
        $alarms = Alarm::orderBy('site_name')->get()->groupBy('site_name');
        return view('assets.alarmstatus', compact('alarms'));
    }

    /**
     * Return alarms as JSON for AJAX.
     */
    public function data()
    {
        $alarms = Alarm::orderBy('timestamp', 'desc')->get();
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
                'count'   => $this->getActiveCount() // return updated count
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
        Alarm::truncate(); // ⚡ deletes all rows quickly
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
