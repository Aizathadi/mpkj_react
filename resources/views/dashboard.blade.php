<x-app-layout>
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Street Light GIS Dashboard</h1>
    <div id="map" style="height: 600px; width: 100%;"></div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// --- Notification ---
function showNotification(message, type = 'success') {
    const bg = type === 'success' ? '#4CAF50' : '#f44336';
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; bottom: 20px; right: 20px;
        background: ${bg}; color: white;
        padding: 10px 15px; border-radius: 6px;
        font-size: 14px; z-index: 9999;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// --- Generic fetch helper ---
async function sendControlCommand(payload, successMsg, failMsg) {
    try {
        const res = await fetch("{{ route('mqtt.publish') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(payload)
        });

        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch { throw new Error("Invalid JSON: " + text); }

        if (res.ok && data.status === 'success') {
            showNotification(successMsg);
        } else {
            showNotification(data.message || failMsg, 'error');
        }
    } catch (err) {
        console.error('Control command error:', err);
        showNotification('Server error', 'error');
    }
}

// --- Global control functions ---
window.sendLampOff = assetNo => {
    sendControlCommand(
        { asset_no: assetNo, command: "toggle_led", onoff: 0 },
        `LED OFF sent for ${assetNo}`,
        'Failed to send OFF command'
    );
};

window.sendLampOnWithDimming = assetNo => {
    const dimming = parseInt(document.getElementById(`dimming-${assetNo}`)?.value || 0, 10);
    if (dimming < 10) {
        showNotification("⚠️ Please set dimming above 10% before turning LED ON.", "error");
        return;
    }
    sendControlCommand(
        { asset_no: assetNo, command: "set_dimming", dimming: dimming },
        `LED ON with ${dimming}% dimming sent for ${assetNo}`,
        'Failed to send LED ON + dimming'
    );
};

// --- Map initialization ---
document.addEventListener("DOMContentLoaded", () => {
    const map = L.map('map').setView([2.8852130, 101.7904980], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);

    function isLedOn(status) {
        return ["1", "on", "true"].includes(String(status).toLowerCase());
    }

    function createLedMarker(status) {
        return L.icon({
            iconUrl: isLedOn(status) ? '/images/streetlightON.png' : '/images/streetlightOFF.png',
            iconSize: [40, 40], iconAnchor: [20, 20], popupAnchor: [0, -25]
        });
    }

    async function loadStreetLightStatus() {
        try {
            const response = await fetch("{{ route('streetlight.status.data') }}", { credentials: "same-origin" });
            const data = await response.json();
            markersLayer.clearLayers();

            data.forEach(item => {
                const ledIsOn = isLedOn(item.led_status);
                const dimming = item.dimming ?? 0;

                const popup = `
                    <div style="font-family: Arial, sans-serif; font-size: 13px; min-width: 270px;">

                        <!-- Control Card -->
                        <div style="background:#f9f9f9; border:1px solid #ddd; border-radius:8px; padding:10px; margin-bottom:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                            <h4 style="margin:0 0 8px 0; font-size:14px; font-weight:bold; color:#333;">⚙️ LED Control</h4>
                            <button type="button" 
                                style="background:#f44336;color:white;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;margin-right:6px;"
                                onclick="sendLampOff('${item.asset_no}')">Turn OFF</button>
                            
                            <div style="margin-top:10px;">
                                <label style="font-weight:bold;">Dimming: 
                                    <span id="dim-label-${item.asset_no}">${dimming}%</span>
                                </label><br>
                                <input type="range" id="dimming-${item.asset_no}" min="0" max="100" value="${dimming}"
                                    oninput="document.getElementById('dim-label-${item.asset_no}').textContent=this.value+'%';"
                                    style="width: 100%; margin: 6px 0;">
                                <button type="button" 
                                    style="background:#4CAF50;color:white;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;"
                                    onclick="sendLampOnWithDimming('${item.asset_no}')">Turn ON</button>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div style="background:#fff; border:1px solid #ddd; border-radius:8px; padding:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                            <h4 style="margin:0 0 8px 0; font-size:14px; font-weight:bold; color:#333;">📊 Device Status</h4>
                            <table style="width:100%; font-size:12px; border-collapse: collapse;">
                                <tr><td><b>📍 Site</b></td><td>${item.site_name ?? 'N/A'}</td></tr>
                                <tr><td><b>🆔 Asset No</b></td><td>${item.asset_no ?? 'N/A'}</td></tr>
                                <tr><td><b>📶 Status</b></td><td style="color:${item.status === 'Online' ? 'green' : 'red'}">${item.status ?? 'Offline'}</td></tr>
                                <tr><td><b>💡 LED</b></td><td>${ledIsOn ? 'ON' : 'OFF'}</td></tr>
                                <tr><td><b>🔌 Volt</b></td><td>${item.volt ?? 0} V</td></tr>
                                <tr><td><b>🔋 Ampere</b></td><td>${(item.ampere ?? 0) / 1000} A</td></tr>
                                <tr><td><b>⚡ Power</b></td><td>${item.power ?? 0} W</td></tr>
                                <tr><td><b>📈 Energy</b></td><td>${item.energy ?? 0} kWh</td></tr>
                                <tr><td><b>🌞 Lux</b></td><td>${item.lux ?? 0}</td></tr>
                            </table>
                        </div>
                    </div>
                `;

                L.marker([item.latitude, item.longitude], { icon: createLedMarker(item.led_status) })
                    .bindPopup(popup)
                    .addTo(markersLayer);
            });
        } catch (err) {
            console.error("Error loading street light status:", err);
        }
    }

    loadStreetLightStatus();
    setInterval(loadStreetLightStatus, 15000);
});
</script>
@endpush
</x-app-layout>
