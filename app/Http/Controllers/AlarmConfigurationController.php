<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\AssetRegistration;

class AlarmConfigurationController extends Controller
{
    /**
     * Show the Alarm Configuration page.
     */
    public function index()
    {
        // Fetch all registered assets grouped by site name
        $sites = AssetRegistration::all()
            ->groupBy('site_name')
            ->map(function ($assets, $site) {
                return [
                    'site_name' => $site,
                    'assets'    => $assets->map(function ($a) {
                        return [
                            'asset_no'  => $a->asset_no,
                            'longitude' => $a->longitude,
                            'latitude'  => $a->latitude,
                        ];
                    })->values()
                ];
            })->values();

        // ✅ FIXED: match actual Blade location
        return view('assets.configuration', compact('sites'));
    }

    /**
     * Handle the Program button (publish MQTT alarm configuration).
     */
    public function program(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'assets'        => 'required|array',
            'assets.*'      => 'string',
            'under_current' => 'required|integer',
            'over_current'  => 'required|integer',
            'under_voltage' => 'required|integer',
            'over_voltage'  => 'required|integer',
        ]);

        // MQTT connection setup
        $server   = 'mqtt.lestaritech.my';
        $port     = 1883;

        $connectionSettings = (new ConnectionSettings())
            ->setUsername('mqttadmin')
            ->setPassword('Pwd@775151Wrc')
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('streetlight/lastwill')
            ->setLastWillMessage('offline')
            ->setLastWillQualityOfService(0);

        try {
            // Create one MQTT client for all publishing
            $clientId = 'laravel_publisher_' . uniqid();
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect($connectionSettings, true);

            foreach ($validated['assets'] as $assetNo) {
                $payload = [
                    "msgType" => "AlarmFastCfg",
                    "msgId"   => uniqid(),
                    "sn"      => $assetNo,
                    "op"      => "W",
                    "cmdData" => [
                        "alarms" => [
                            [
                                "chnNo"   => 1,
                                "alarmId" => 103, // Under Current
                                "value"   => $validated['under_current'],
                            ],
                            [
                                "chnNo"   => 1,
                                "alarmId" => 101, // Over Current
                                "value"   => $validated['over_current'],
                            ],
                            [
                                "chnNo"   => 1,
                                "alarmId" => 102, // Under Voltage
                                "value"   => $validated['under_voltage'],
                            ],
                            [
                                "chnNo"   => 1,
                                "alarmId" => 100, // Over Voltage
                                "value"   => $validated['over_voltage'],
                            ],
                        ],
                    ],
                ];

                $message = json_encode($payload);
                $topic   = "/shuncom/CmdInput/" . $assetNo;

                $mqtt->publish($topic, $message, 0);
                usleep(200000); // small delay (0.2s) between publishes
            }

            $mqtt->disconnect();

            // ✅ Auto-detect AJAX or normal form
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Alarm configuration sent successfully.',
                ], 200);
            }

            return redirect()->route('alarm.configuration')
                ->with('success', 'Alarm configuration sent successfully.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('alarm.configuration')
                ->with('error', 'MQTT Error: ' . $e->getMessage());
        }
    }
}
