<x-app-layout>
    <div class="relative w-full h-screen overflow-hidden bg-[#0f172a] text-gray-100 font-sans">
        <!-- Fullscreen Map -->
        <div id="map" class="absolute inset-0 z-0"></div>

        <!-- Date & Time (Top-Right Corner) -->
        <div id="datetime"
             class="absolute top-4 right-6 z-20 
                    bg-gray-900/90 text-gray-100 
                    px-4 py-2 rounded-2xl text-sm 
                    font-mono font-semibold shadow-lg 
                    border border-gray-700 backdrop-blur-sm">
            Loading time...
        </div>

       <!-- ========================= FUTURISTIC COMMAND CENTRE ========================= -->
<div id="command-centre" class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-50 w-[94%] md:w-[82%]">
    <div class="rounded-3xl overflow-hidden border border-blue-600/20 
                bg-gradient-to-b from-[#0a1628]/80 to-[#060f1e]/70 
                backdrop-blur-xl shadow-[0_25px_80px_rgba(2,6,23,0.85)] 
                transition-all duration-500 ease-in-out">

        <!-- Header Section -->
        <div class="flex items-center justify-between px-6 py-3 border-b border-blue-800/20 bg-[#0b172b]/60">
            <div class="flex items-center gap-3">
                <div class="px-3 py-2 rounded-lg bg-gradient-to-br from-[#0d1b33]/70 to-[#0b1224]/60 
                            border border-blue-500/20 shadow-[0_0_12px_rgba(30,144,255,0.4)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m2 0a8 8 0 11-8-8 8 8 0 018 8z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-blue-300 tracking-wide uppercase">MPKj Command Centre</div>
                    <div class="text-xs text-gray-400">Real-time Streetlight Control Hub</div>
                </div>
            </div>
            <button id="centre-toggle" class="text-gray-300 hover:text-blue-400 text-sm font-semibold 
                                             px-3 py-2 rounded-lg hover:bg-blue-500/10 transition">Hide</button>
        </div>

        <!-- Main Grid Content -->
        <div id="centre-content" class="p-5 grid grid-cols-1 md:grid-cols-4 gap-5">

            <!--  Site Selection Column -->
<div class="bg-[#0b172b]/50 p-4 rounded-2xl border border-blue-700/20 shadow-inner">
    <div class="text-xs text-gray-400 mb-2 font-semibold uppercase">Select Site</div>
    <select id="select-site" class="w-full rounded-lg bg-[#071423]/60 border border-gray-700 
                                   px-2 py-1.5 text-sm text-gray-200 focus:border-blue-500 focus:ring-0">
        <option value="all">All Sites</option>
        @foreach($assets->pluck('site_name')->unique() as $site)
            <option value="{{ $site }}">{{ $site }}</option>
        @endforeach
    </select>

    <div class="mt-4 text-xs text-gray-400 uppercase font-semibold">Select Assets</div>
    <div id="asset-list" class="max-h-36 overflow-auto mt-2 space-y-2 text-sm text-gray-200 pr-1">
        @foreach($assets as $asset)
            <div class="flex items-center gap-2 asset-item" data-site="{{ $asset->site_name }}">
                <input type="checkbox" id="asset-{{ $asset->id }}" value="{{ $asset->id }}" class="accent-blue-400">
                <label for="asset-{{ $asset->id }}" class="text-sm">
                    {{ $asset->asset_no }}
                    <span class="text-xs text-gray-400 ml-1">({{ $asset->site_name }})</span>
                </label>
            </div>
        @endforeach
    </div>
</div>

<!-- 💡 Site + Asset Filter Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const siteSelect = document.getElementById('select-site');
    const assetItems = document.querySelectorAll('.asset-item');

    siteSelect.addEventListener('change', function() {
        const selectedSite = this.value;

        assetItems.forEach(item => {
            // show all assets if 'all' is selected, otherwise filter by site
            if (selectedSite === 'all') {
                item.style.display = 'flex';
            } else {
                item.style.display = item.dataset.site === selectedSite ? 'flex' : 'none';
            }
        });
    });
});
</script>

            <!-- Control Section -->
            <div class="bg-[#0b172b]/50 p-4 rounded-2xl border border-pink-700/20 shadow-inner">
                <div class="text-xs text-gray-400 mb-2 font-semibold uppercase">Per-Asset Control</div>
                <div class="space-y-3">
                    <label class="flex items-center gap-2">
                        <input id="toggle-selected-on" type="checkbox" class="accent-green-400">
                        <span class="text-sm text-gray-200">Turn Selected <span class="font-semibold text-green-400">ON</span></span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input id="toggle-selected-off" type="checkbox" class="accent-red-400">
                        <span class="text-sm text-gray-200">Turn Selected <span class="font-semibold text-red-400">OFF</span></span>
                    </label>

                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Set Brightness</label>
                        <input id="selected-dim" type="range" min="0" max="100" value="60" class="w-full accent-blue-400 mt-1">
                        <div class="text-right text-xs text-gray-300 mt-1">Brightness: 
                            <span id="selected-dim-label" class="text-blue-400 font-semibold">60%</span>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button onclick="applyToSelected()" class="flex-1 py-2 rounded-lg 
                                 bg-gradient-to-br from-blue-600/80 to-blue-700/80 
                                 text-white font-semibold hover:shadow-[0_0_15px_rgba(59,130,246,0.5)] transition">
                            Apply
                        </button>
                        <button onclick="clearSelected()" class="py-2 px-3 rounded-lg border border-gray-700/40 
                                 text-gray-300 hover:text-white hover:bg-gray-700/20 transition">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview Chart -->
            <div class="bg-[#0b172b]/50 p-4 rounded-2xl border border-blue-700/20 shadow-inner">
                <div class="text-xs text-gray-400 mb-2 font-semibold uppercase">System Overview</div>
                <canvas id="overview-doughnut" class="w-full h-36"></canvas>
                <div class="mt-2 text-sm text-gray-300 flex justify-between font-semibold">
                    <div>LED On: <span id="led-on-count" class="text-green-400">0</span></div>
                    <div>LED Off: <span id="led-off-count" class="text-red-400">0</span></div>
                </div>
            </div>

            <!-- Status Totals -->
            <div class="bg-[#0b172b]/50 p-4 rounded-2xl border border-emerald-700/20 shadow-inner">
                <div class="text-xs text-gray-400 mb-2 font-semibold uppercase">Totals</div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-300"><span>Total LED</span><span id="total-led" class="font-bold text-white">0</span></div>
                    <div class="flex justify-between text-gray-300"><span>Online</span><span id="total-online" class="font-bold text-green-400">0</span></div>
                    <div class="flex justify-between text-gray-300"><span>Offline</span><span id="total-offline" class="font-bold text-red-400">0</span></div>
                </div>
                <div class="mt-4 text-xs text-gray-400">Last Update: <span id="last-update" class="text-gray-300 font-semibold">—</span></div>
            </div>
        </div>
    </div>
</div>
<!-- =========================================================================== -->


        @push('scripts')
        <!-- Leaflet -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>
        html, body, #map {
            margin: 0; padding: 0; height: 100%; width: 100%;
            overflow: hidden; background-color: #0f172a !important;
            color: #e2e8f0; font-family: 'Inter', sans-serif;
        }
        .leaflet-popup-content-wrapper {
            background: #1f2937 !important;
            border: 2px solid #60a5fa !important;
            border-radius: 12px !important;
            box-shadow: 0 0 15px rgba(59,130,246,0.4);
            color: #f1f5f9;
        }
        .leaflet-popup-tip { background: #1f2937 !important; }
        .popup-card { background: #2d3748; color: white; border-radius: 10px; padding: 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.6); font-size: 13px; }
        .popup-card h4 { font-size: 14px; font-weight: bold; color: #f1f2f5; margin-bottom: 6px; }
        .status-online { color: #22c55e; font-weight: bold; }
        .status-offline { color: #ef4444; font-weight: bold; }
        .alarm-item { color: #ff5555; font-weight: bold; font-size: 13px; margin-bottom: 4px; }
        button { transition: all 0.2s ease; cursor: pointer; }
        button:hover { transform: scale(1.05); filter: brightness(1.1); }
        /* small highlight for site scroll target */
        .site-highlight { box-shadow: 0 0 0 3px rgba(59,130,246,0.12) inset; border-radius: 6px; transition: box-shadow 0.4s ease; }
        </style>

        <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateDateTime() {
            const now = new Date();
            const options = { weekday:'short', year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false };
            document.getElementById('datetime').textContent = now.toLocaleString('en-MY', options);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        function showNotification(message, type='success') {
            const bg = type==='success' ? '#4CAF50' : '#f44336';
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed; bottom: 20px; right: 20px;
                background: ${bg}; color: white;
                padding: 10px 15px; border-radius: 8px;
                font-size: 14px; z-index: 9999;
                box-shadow: 0 2px 10px rgba(0,0,0,0.4);
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        async function sendControlCommand(payload, successMsg, failMsg) {
            try {
                const res = await fetch("{{ route('mqtt.publish') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') showNotification(successMsg);
                else showNotification(data.message || failMsg, 'error');
            } catch (err) { console.error(err); showNotification('Server error','error'); }
        }

        window.sendLampOff = assetNo => sendControlCommand({ asset_no: assetNo, command:"toggle_led", onoff:0 },
            `LED OFF sent for ${assetNo}`, 'Failed to send OFF command');

        window.sendLampOnWithDimming = assetNo => {
            const dimming = parseInt(document.getElementById(`dimming-${assetNo}`)?.value || 0, 10);
            if(dimming < 10){ showNotification("⚠️ Please set dimming above 10% before turning LED ON.", "error"); return; }
            sendControlCommand({ asset_no: assetNo, command:"set_dimming", dimming:dimming },
                `LED ON with ${dimming}% dimming sent for ${assetNo}`, 'Failed to send LED ON + dimming');
        };

        // ------------------- SITE + ASSET UI LOGIC -------------------
        // We'll populate the Select Site dropdown (unique site names)
        // and the Asset List (grouped by site) based on the JSON returned by streetlight.status.data.
        // Selecting a site in the dropdown will scroll to & briefly highlight that site's section in the asset list.
        function sanitizeId(s){ return String(s).replace(/[^a-z0-9_-]/gi,'_'); }

        function buildSiteAndAssetUI(data) {
            // data expected: array of items with at least asset_no, site_name (or site), led_status etc.
            const siteSelect = document.getElementById('select-site');
            const assetList = document.getElementById('asset-list');

            // build map: siteName => [items]
            const groups = {};
            data.forEach(item => {
                const siteName = item.site_name || item.site || 'Unknown Site';
                if(!groups[siteName]) groups[siteName] = [];
                groups[siteName].push(item);
            });

            // populate select with unique site names (preserve "All Sites" as first)
            // clear existing dynamic options first (but keep the "All Sites" option)
            Array.from(siteSelect.querySelectorAll('option')).forEach(opt => {
                if(opt.value !== 'all') opt.remove();
            });

            Object.keys(groups).forEach(siteName => {
                const opt = document.createElement('option');
                opt.value = siteName;
                opt.textContent = siteName;
                siteSelect.appendChild(opt);
            });

            // populate asset list grouped by site (no redundant site names)
            assetList.innerHTML = ''; // clear
            if(Object.keys(groups).length === 0) {
                assetList.innerHTML = `<div class="text-gray-500 text-xs italic">No assets found</div>`;
                return;
            }

            Object.keys(groups).forEach(siteName => {
                const siteId = 'site-' + sanitizeId(siteName);
                // Site header
                const siteHeader = document.createElement('div');
                siteHeader.id = siteId;
                siteHeader.className = "pt-2 pb-1";
                siteHeader.innerHTML = `<div class="text-xs text-blue-300 font-semibold mb-2">${siteName}</div>`;
                assetList.appendChild(siteHeader);

                // Assets under site
                groups[siteName].forEach(item => {
                    const row = document.createElement('div');
                    row.className = "flex items-center gap-2 ml-2";
                    // status label color
                    const status = (item.led_status ?? item.status ?? '').toString().toLowerCase();
                    const statusClass = ["1","on","true"].includes(status) ? 'text-green-400' : 'text-red-400';
                    const labelText = item.asset_no ?? item.id ?? (`asset_${Math.random().toString(36).slice(2,7)}`);

                    row.innerHTML = `
                        <input type="checkbox" id="chk-${labelText}" value="${labelText}" class="accent-blue-400">
                        <label for="chk-${labelText}" class="text-sm">${labelText}
                            <span class="text-xs ${statusClass} ml-2">[${status ? status.toUpperCase() : 'N/A'}]</span>
                        </label>
                    `;
                    assetList.appendChild(row);
                });
            });
        }

        // When user picks a site, scroll to that group's header and highlight briefly
        function setupSiteSelectScroll() {
            const siteSelect = document.getElementById('select-site');
            const assetList = document.getElementById('asset-list');
            siteSelect.addEventListener('change', (e) => {
                const val = e.target.value;
                if(val === 'all') {
                    // scroll to top of asset list
                    assetList.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                const targetId = 'site-' + sanitizeId(val);
                const targetEl = document.getElementById(targetId);
                if(targetEl) {
                    // scroll the container so target is visible
                    const containerTop = assetList.getBoundingClientRect().top;
                    const targetTop = targetEl.getBoundingClientRect().top;
                    const offset = targetTop - containerTop;
                    assetList.scrollTo({ top: offset + assetList.scrollTop - 8, behavior: 'smooth' });

                    // briefly highlight
                    targetEl.classList.add('site-highlight');
                    setTimeout(()=> targetEl.classList.remove('site-highlight'), 900);
                }
            });
        }

        // ------------------- Existing map & data loading logic below -------------------
        document.addEventListener("DOMContentLoaded", async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedAssetNo = urlParams.get('asset_no');

            const map = L.map('map', { zoomControl:true, attributionControl:false }).setView([2.8852130,101.7904980], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom:19, tileSize:512, zoomOffset:-1, detectRetina:true, attribution:'&copy; OpenStreetMap contributors'
            }).addTo(map);

            const markersLayer = L.layerGroup().addTo(map);
            let allAlarms = {};
            const markersMap = {}; // store markers by asset_no
            let initialLoad = true; // flag to only fit bounds on first load

            function isLedOn(status){ return ["1","on","true"].includes(String(status).toLowerCase()); }
            function getStatusColor(lastSeenAt){
                if(!lastSeenAt) return { text:'Offline', className:'status-offline' };
                const diff = (new Date() - new Date(lastSeenAt))/60000;
                return diff<=15? { text:'Online', className:'status-online'} : { text:'Offline', className:'status-offline'};
            }

            function createLedWithAlarmMarker(status, hasAlarm){
                const ledUrl = isLedOn(status) ? "{{ asset('images/streetlightON.png') }}" : "{{ asset('images/streetlightOFF.png') }}";
                const html = `<div style="position:relative;width:40px;height:40px;">
                    ${hasAlarm?`<div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);z-index:10;">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <polygon points="12,2 22,20 2,20" fill="red"/>
                            <text x="12" y="17" text-anchor="middle" font-size="14" font-weight="bold" fill="black">!</text>
                        </svg></div>`:''}
                    <img src="${ledUrl}" style="width:40px;height:40px;"/>
                </div>`;
                return L.divIcon({ html, className:'', iconSize:[40,52], iconAnchor:[20,40] });
            }

            async function fetchAllAlarms(){
                try{
                    const res = await fetch("{{ route('alarms.popup.all') }}");
                    const data = await res.json();
                    allAlarms = data.reduce((acc,a)=>{ if(!acc[a.asset_no]) acc[a.asset_no]=[]; acc[a.asset_no].push(a); return acc; }, {});
                }catch(err){ console.error(err); allAlarms = {}; }
            }

            function getAlarmListHtml(assetNo){
                const alarms = allAlarms[assetNo] || [];
                if(alarms.length===0) return "<li style='color:#22c55e;font-weight:bold;'>No active alarms</li>";
                return alarms.map(a=>`<li class="alarm-item">${a.alarm}</li>`).join('');
            }

            // MAIN data loader — also updates Site/Asset UI
            async function loadStreetLightStatus(callbackAfterLoad = null){
                await fetchAllAlarms();
                try{
                    const res = await fetch("{{ route('streetlight.status.data') }}",{ credentials:"same-origin" });
                    const data = await res.json();
                    // store for UI functions if needed
                    window.__streetlight_data = data;

                    // Populate Site select and grouped asset list (no filtering)
                    buildSiteAndAssetUI(data);
                    setupSiteSelectScroll();

                    markersLayer.clearLayers();
                    const bounds = [];

                    data.forEach(item=>{
                        const hasAlarm = allAlarms[item.asset_no]?.length>0;
                        const ledMarker = L.marker([item.latitude,item.longitude],{ icon:createLedWithAlarmMarker(item.led_status,hasAlarm) }).addTo(markersLayer);

                        const deviceStatus = getStatusColor(item.last_seen_at);
                        const alarmListHtml = getAlarmListHtml(item.asset_no);
                        const dimming = item.dimming ?? 0;
                        const ledIsOn = isLedOn(item.led_status);

                        const popup = `<div>
                            <div class="popup-card">
                                <h4>⚙️ LED Control</h4>
                                <div style="display:flex;gap:6px;margin-bottom:10px;">
                                    <button onclick="sendLampOff('${item.asset_no}')" style="flex:1;background:#f43f5e;color:white;padding:8px;border:none;border-radius:6px;font-weight:bold;">Turn OFF</button>
                                    <button onclick="sendLampOnWithDimming('${item.asset_no}')" style="flex:1;background:#22c55e;color:white;padding:8px;border:none;border-radius:6px;font-weight:bold;">Turn ON</button>
                                </div>
                                <label>Brightness: <span id="dim-label-${item.asset_no}">${dimming}%</span></label>
                                <input type="range" id="dimming-${item.asset_no}" min="0" max="100" value="${dimming}" oninput="document.getElementById('dim-label-${item.asset_no}').textContent=this.value+'%';" style="width:100%;margin-top:4px;">
                            </div>

                            <div class="popup-card">
                                <h4>📊 Device Status</h4>
                                <div style="display:grid;grid-template-columns:auto 1fr;row-gap:4px;column-gap:10px;">
                                    <div><b>Site</b></div><div>${item.site_name ?? 'N/A'}</div>
                                    <div><b>Asset No</b></div><div>${item.asset_no ?? 'N/A'}</div>
                                    <div><b>Status</b></div><div class="${deviceStatus.className}">${deviceStatus.text}</div>
                                    <div><b>LED</b></div><div>${ledIsOn?'ON':'OFF'}</div>
                                    <div><b>Volt</b></div><div>${item.volt ?? 0} V</div>
                                    <div><b>Ampere</b></div><div>${(item.ampere ?? 0)/1000} A</div>
                                    <div><b>Power</b></div><div>${item.power ?? 0} W</div>
                                    <div><b>Energy</b></div><div>${item.energy ?? 0} kWh</div>
                                    <div><b>Lux</b></div><div>${item.lux ?? 0}</div>
                                    <div><b>Last Data</b></div><div>${item.last_seen_at ?? 'N/A'}</div>
                                </div>
                            </div>

                            <div class="popup-card">
                                <h4>🚨 Active Alarms</h4>
                                <ul>${alarmListHtml}</ul>
                            </div>
                        </div>`;

                        ledMarker.bindPopup(popup);
                        markersMap[item.asset_no] = ledMarker;
                        bounds.push([item.latitude,item.longitude]);
                    });

                    // Only fit bounds on initial load
                    if(initialLoad && bounds.length>0) {
                        map.fitBounds(bounds,{ padding:[80,80] });
                        initialLoad = false;
                    }

                    if(callbackAfterLoad) callbackAfterLoad();
                }catch(err){ console.error(err); }
            }

            window.openLocationPopup = function(assetNo){
                const marker = markersMap[assetNo];
                if(marker){
                    marker.openPopup();
                    map.setView(marker.getLatLng(), 18, { animate:true });
                } else showNotification('Asset not found on map','error');
            }

            // Load map & markers + populate site/asset UI
            loadStreetLightStatus(()=>{ if(selectedAssetNo){ setTimeout(()=>{ openLocationPopup(selectedAssetNo); }, 500); } });

            // Auto-update every 15s without resetting map center
            setInterval(()=>loadStreetLightStatus(), 15000);
        });
        </script>
        @endpush
    </div>
</x-app-layout>
