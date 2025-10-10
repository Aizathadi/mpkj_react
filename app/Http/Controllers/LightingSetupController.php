<?php

namespace App\Http\Controllers;

use App\Models\LightingSetup;
use App\Models\AssetRegistration;
use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class LightingSetupController extends Controller
{
    /** Show Lighting Setup page */
    public function index()
    {
        // Group assets by site_name
        $sites = AssetRegistration::all()
            ->groupBy('site_name')
            ->map(function ($assets, $site) {
                return [
                    'site_name' => $site,
                    'assets' => $assets->map(function ($a) {
                        return [
                            'id'       => $a->id,
                            'asset_no' => $a->asset_no,
                            'longitude'=> $a->longitude,
                            'latitude' => $a->latitude,
                        ];
                    })->values()
                ];
            })->values();

        return view('assets.lightingsetup', compact('sites'));
    }

    /** Store or update lighting setup (On/Off + Dimming) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_ids'   => 'required|array',
            'asset_ids.*' => 'exists:asset_registrations,id',

            // On/Off times
            'on_time_h'   => 'nullable|integer',
            'on_time_m'   => 'nullable|integer',
            'on_time_s'   => 'nullable|integer',
            'off_time_h'  => 'nullable|integer',
            'off_time_m'  => 'nullable|integer',
            'off_time_s'  => 'nullable|integer',

            // Dimming 1–4
            'dimming1_h'      => 'nullable|integer',
            'dimming1_m'      => 'nullable|integer',
            'dimming1_s'      => 'nullable|integer',
            'dimming1_value'  => 'nullable|integer',

            'dimming2_h'      => 'nullable|integer',
            'dimming2_m'      => 'nullable|integer',
            'dimming2_s'      => 'nullable|integer',
            'dimming2_value'  => 'nullable|integer',

            'dimming3_h'      => 'nullable|integer',
            'dimming3_m'      => 'nullable|integer',
            'dimming3_s'      => 'nullable|integer',
            'dimming3_value'  => 'nullable|integer',

            'dimming4_h'      => 'nullable|integer',
            'dimming4_m'      => 'nullable|integer',
            'dimming4_s'      => 'nullable|integer',
            'dimming4_value'  => 'nullable|integer',
        ]);

        foreach ($validated['asset_ids'] as $assetId) {
            $asset = AssetRegistration::find($assetId);

            // Save in DB
            LightingSetup::updateOrCreate(
                ['asset_id' => $assetId],
                array_merge($validated, [
                    'site_name' => $asset->site_name ?? 'unknown',
                    'asset_no'  => $asset->asset_no ?? 'unknown',
                ])
            );

            // MQTT Payload
            $payload = $this->buildMqttPayload($validated, $asset->asset_no);
            $this->publishMqtt($asset->asset_no, $payload);
        }

        return redirect()->back()->with('success', 'Time/Dimming program sent to controllers.');
    }

    /** Lux update for multiple assets */
    public function updateLux(Request $request)
    {
        $validated = $request->validate([
            'asset_ids'   => 'required|array',
            'asset_ids.*' => 'exists:asset_registrations,id',
            'lux_on'      => 'required|integer',
            'lux_off'     => 'required|integer',
            'lux_delay'   => 'required|integer',
        ]);

        foreach ($validated['asset_ids'] as $assetId) {
            $asset = AssetRegistration::find($assetId);

            // Save Lux config
            LightingSetup::updateOrCreate(
                ['asset_id' => $assetId],
                [
                    'site_name' => $asset->site_name ?? 'unknown',
                    'asset_no'  => $asset->asset_no ?? 'unknown',
                    'lux_on'    => $validated['lux_on'],
                    'lux_off'   => $validated['lux_off'],
                    'lux_delay' => $validated['lux_delay'],
                ]
            );

            // MQTT Payload for Lux
            $payload = [
                "msgType" => "WorkPlan",
                "msgId"   => uniqid('lux_'),
                "sn"      => $asset->asset_no,   // ✅ Always asset_no
                "op"      => "W",
                "cmdData" => [
                    "type"  => 2,
                    "valid" => 1,
                    "delay" => intval($validated['lux_delay']),
                    "lux"   => [
                        "on"  => intval($validated['lux_on']),
                        "off" => intval($validated['lux_off']),
                    ]
                ]
            ];

            $this->publishMqtt($asset->asset_no, $payload);
        }

        return redirect()->back()->with('success', 'Lux control sent to controllers.');
    }

    /** Build MQTT payload for schedule & dimming */
    private function buildMqttPayload($validated, $assetNo)
    {
        return [
            "msgType" => "WorkPlan",
            "msgId"   => uniqid('Work_Plan_'),
            "sn"      => $assetNo,   // ✅ Using asset_no for device SN
            "err"     => 0,
            "cmdData" => [
                "type"  => 1,
                "valid" => 1,
                "chnList" => [
                    // On/Off
                    [
                        "chnNoT" => 1,
                        "validT" => 1,
                        "schn"   => 1,
                        "sH"     => intval($validated['on_time_h'] ?? 0),
                        "sM"     => intval($validated['on_time_m'] ?? 0),
                        "sS"     => intval($validated['on_time_s'] ?? 0),
                        "sonoff" => 1,
                        "echn"   => 1,
                        "eH"     => intval($validated['off_time_h'] ?? 0),
                        "eM"     => intval($validated['off_time_m'] ?? 0),
                        "eS"     => intval($validated['off_time_s'] ?? 0),
                        "eonoff" => 0,
                    ],
                    // Dimming 1–2
                    [
                        "chnNoT" => 2,
                        "validT" => 1,
                        "schn"   => 1,
                        "sH"     => intval($validated['dimming1_h'] ?? 0),
                        "sM"     => intval($validated['dimming1_m'] ?? 0),
                        "sS"     => intval($validated['dimming1_s'] ?? 0),
                        "sbri"   => intval($validated['dimming1_value'] ?? 0),
                        "echn"   => 1,
                        "eH"     => intval($validated['dimming2_h'] ?? 0),
                        "eM"     => intval($validated['dimming2_m'] ?? 0),
                        "eS"     => intval($validated['dimming2_s'] ?? 0),
                        "ebri"   => intval($validated['dimming2_value'] ?? 0),
                    ],
                    // Dimming 3–4
                    [
                        "chnNoT" => 3,
                        "validT" => 1,
                        "schn"   => 1,
                        "sH"     => intval($validated['dimming3_h'] ?? 0),
                        "sM"     => intval($validated['dimming3_m'] ?? 0),
                        "sS"     => intval($validated['dimming3_s'] ?? 0),
                        "sbri"   => intval($validated['dimming3_value'] ?? 0),
                        "echn"   => 1,
                        "eH"     => intval($validated['dimming4_h'] ?? 0),
                        "eM"     => intval($validated['dimming4_m'] ?? 0),
                        "eS"     => intval($validated['dimming4_s'] ?? 0),
                        "ebri"   => intval($validated['dimming4_value'] ?? 0),
                    ],
                ]
            ]
        ];
    }

    /** Publish MQTT */
    private function publishMqtt($assetNo, $payload)
    {
        $server   = 'mqtt.lestaritech.my';
        $port     = 1883;
        $clientId = 'lighting-setup-' . uniqid();
        $username = 'mqttadmin';
        $password = 'Pwd@775151Wrc';

        $connectionSettings = (new ConnectionSettings())
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('streetlight/lastwill')
            ->setLastWillMessage('offline')
            ->setLastWillQualityOfService(0);

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect($connectionSettings, true);
            $topic   = "/shuncom/CmdInput/" . $assetNo;
            $message = json_encode($payload, JSON_UNESCAPED_SLASHES);
            $mqtt->publish($topic, $message, 0);
            $mqtt->disconnect();
        } catch (\Exception $e) {
            \Log::error("MQTT publish failed for asset {$assetNo}: " . $e->getMessage());
        }
    }
}
