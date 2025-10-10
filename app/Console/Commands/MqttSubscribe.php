<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\AssetRegistration;
use App\Models\StreetLightData;
use App\Models\StreetLightStatus;
use App\Models\Alarm;   // ✅ Import Alarm model
use Carbon\Carbon;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to Lestari broker and insert/update street light data based on msgType in UTC.';

    public function handle()
    {
        $server   = 'mqtt.lestaritech.my';
        $port     = 1883;
        $clientId = 'laravel_shuncom_' . uniqid();

        $settings = (new ConnectionSettings())
            ->setUsername('mqttadmin')
            ->setPassword('Pwd@775151Wrc')
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('streetlight/lastwill')
            ->setLastWillMessage('offline')
            ->setLastWillQualityOfService(0);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($settings, true);

        $this->info("✅ Connected to MQTT broker: {$server}:{$port} with authentication");

        // Alarm mapping
        $alarmMap = [
            100 => 'Overvoltage alarm',
            101 => 'Overcurrent alarm',
            102 => 'Undervoltage alarm',
            103 => 'Undercurrent alarm',
            104 => 'Lights on abnormal alarm',
            105 => 'Lights off abnormal alarm',
            106 => 'Leakage current alarm',
            107 => 'Leakage voltage alarm',
            108 => 'Flood alarm',
            109 => 'Power outage alarm',
            110 => 'Tilt X alarm',
            111 => 'Tilt Y alarm',
            112 => 'Tilt Z alarm',
        ];

        // Subscribe to topic
        $mqtt->subscribe('/shuncom/Report/+', function (string $topic, string $message) use ($alarmMap) {

            $this->info("📩 Received message on {$topic}");

            try {
                $data = json_decode($message, true);
                if (!$data) {
                    $this->error("❌ Invalid JSON payload.");
                    return;
                }

                $msgType = $data['msgType'] ?? '';

                // Extract asset number
                $topicParts = explode('/', $topic);
                $assetNo = $topicParts[3] ?? ($data['sn'] ?? null);

                if (!$assetNo) {
                    $this->error("❌ Asset number not found in topic or message");
                    return;
                }

                // Find registered asset
                $asset = AssetRegistration::where('asset_no', $assetNo)->first();
                if (!$asset) {
                    $this->error("❌ Asset not registered: {$assetNo}");
                    \Log::warning("MQTT data received for unregistered asset: {$assetNo}");
                    return;
                }

                // --- Handle Alarm messages ---
                if ($msgType === 'Alarm') {
                    $alarmId = $data['cmdData']['alarmId'] ?? null;
                    $alarmStatus = $alarmMap[$alarmId] ?? "Unknown Alarm ({$alarmId})";

                    Alarm::updateOrCreate(
                        ['asset_id' => $asset->id],
                        [
                            'site_name'    => $asset->site_name,
                            'asset_no'     => $asset->asset_no,
                            'latitude'     => $asset->latitude,
                            'longitude'    => $asset->longitude,
                            'alarm_status' => $alarmStatus,
                            'timestamp'    => now(),
                        ]
                    );

                    $this->info("🚨 Alarm saved for asset {$asset->asset_no}: {$alarmStatus}");
                    return; // stop here for alarms
                }

                // --- Handle DevStatRpt / LampStChgRpt ---
                if (!in_array($msgType, ['DevStatRpt', 'LampStChgRpt'])) {
                    $this->info("⚠ Skipping message, unsupported msgType: {$msgType}");
                    return;
                }

                $chn = $data['cmdData']['chnList'][0] ?? [];

                $row = [
                    'asset_id'     => $asset->id,
                    'asset_no'     => $asset->asset_no,
                    'status'       => 'Online',
                    'led_status'   => $chn['onoff'] ?? 0,
                    'dimming'      => $chn['bri'] ?? null,
                    'longitude'    => $asset->longitude,
                    'latitude'     => $asset->latitude,
                    'ampere'       => $chn['elec']['current'] ?? null,
                    'volt'         => $chn['elec']['voltage'] ?? null,
                    'frequency'    => $chn['elec']['freq'] ?? null,
                    'power'        => $chn['elec']['actps'] ?? null,
                    'energy'       => $chn['elec']['actes'] ?? null,
                    'alarm_status' => 'NORMAL',
                    'lux'          => $data['lSnsr']['lux'] ?? null,
                    'local_time'   => Carbon::now('Asia/Kuala_Lumpur'),
                    'last_seen_at' => now(),
                ];

                if ($msgType === 'DevStatRpt') {
                    StreetLightData::create($row);
                    StreetLightStatus::updateOrCreate(
                        ['asset_id' => $asset->id],
                        $row
                    );
                    $this->info("✅ DevStatRpt: Data saved for asset: {$asset->asset_no}");
                } elseif ($msgType === 'LampStChgRpt') {
                    StreetLightStatus::updateOrCreate(
                        ['asset_id' => $asset->id],
                        $row
                    );
                    $this->info("✅ LampStChgRpt: Status updated for asset: {$asset->asset_no}");
                }

            } catch (\Exception $e) {
                $this->error("💥 Error saving MQTT data: " . $e->getMessage());
            }

        }, 0);

        // Keep listening for incoming messages
        $mqtt->loop(true);
    }
}
