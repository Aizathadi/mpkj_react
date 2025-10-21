<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttPublishController extends Controller
{
    public function publish(Request $request)
    {
        \Log::info('Request payload:', $request->all());

        try {
            // Validate incoming payload
            $validated = $request->validate([
                'asset_no' => 'required|string',
                'command'  => 'required|string',
                'onoff'    => 'nullable|integer',
                'dimming'  => 'nullable|integer|min:0|max:100'
            ]);

            $assetNo = $validated['asset_no'];
            $command = $validated['command'];

            // Determine MQTT payload
            if ($command === 'toggle_led') {
                $onoff = isset($validated['onoff']) ? (int) $validated['onoff'] : 1;
                $payload = [
                    "msgType" => "PropOpreation",
                    "msgId"   => "ON/OFF",
                    "sn"      => $assetNo,
                    "op"      => "W",
                    "prop"    => [
                        "chnList" => [
                            [
                                "chnNo" => 1,
                                "onoff" => $onoff
                            ]
                        ]
                    ]
                ];
            } elseif ($command === 'set_dimming') {
                $dimming = isset($validated['dimming']) ? (int) $validated['dimming'] : 0;
                $payload = [
                    "msgType" => "PropOpreation",
                    "msgId"   => "Brightness",
                    "sn"      => $assetNo,
                    "op"      => "W",
                    "prop"    => [
                        "chnList" => [
                            [
                                "chnNo"   => 1,
                                "bri" => $dimming
                            ]
                        ]
                    ]
                ];
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid command'], 400);
            }

            // Convert to JSON message
            $message = json_encode($payload);

            // MQTT connection settings
            $server   = 'mqtt.lestaritech.my';
            $port     = 1883;
            $clientId = 'laravel_publisher_' . uniqid();

            $connectionSettings = (new ConnectionSettings())
                ->setUsername('mqttadmin')
                ->setPassword('Pwd@775151Wrc')
                ->setKeepAliveInterval(60)
                ->setLastWillTopic('streetlight/lastwill')
                ->setLastWillMessage('offline')
                ->setLastWillQualityOfService(0);

            // Build topic dynamically using asset number
            $topic = "/shuncom/CmdInput/" . $assetNo;

            // Publish to broker
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 0);
            usleep(500000); // wait 0.5s to ensure delivery
            $mqtt->disconnect();

            return response()->json([
                'status'  => 'success',
                'message' => "MQTT command published successfully to topic {$topic}",
                'payload' => $payload
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
