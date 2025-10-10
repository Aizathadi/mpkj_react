<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramService;
use App\Models\Alarm;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    // ✅ Test message
    public function sendTest()
    {
        $message = "Test message from Laravel Telegram integration.";
        $success = $this->telegram->sendMessage($message);

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Message sent to Telegram!' : 'Failed to send Telegram message.',
        ]);
    }

    // ✅ Alarm report with Waze link
    public function sendAlarmReport(Request $request, $id)
    {
        try {
            $alarm = Alarm::find($id);

            if (!$alarm) {
                return response()->json(['success' => false, 'message' => 'Alarm not found.'], 404);
            }

            // Get alarm details
            $assetNo = $alarm->asset_no ?? 'N/A';
            $latitude = $alarm->latitude ?? 'N/A';
            $longitude = $alarm->longitude ?? 'N/A';
            $status = $alarm->alarm_status ?? 'N/A';
            $time = $alarm->timestamp ?? 'N/A';

            // 🗺️ Build Waze link
            $wazeLink = "https://waze.com/ul?ll={$latitude},{$longitude}&navigate=yes";

            // If message from Blade is provided, use it; otherwise fallback to this format
            $message = $request->input('message') ?? "
🚨 *ST ALARM NOTIFICATION* 🚨  

📍 Site Name:  *${site}*
🔢 Asset No:   *${assetNo}*

⚠️ Alarm Status: *${status}*
⏰ Alarm Time:   *${time}*

📍 Latitude:  *${latitude}*
📍 Longitude: *${longitude}*

🚗 [Drive to Location>>🚗](${wazeLink})

            ";

            $success = $this->telegram->sendMessage($message);

            if ($success) {
                return response()->json(['success' => true, 'message' => 'Telegram report sent.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send Telegram report.']);
            }

        } catch (\Exception $e) {
            Log::error('Telegram report error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error.']);
        }
    }
}
