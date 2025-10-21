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
  <div id="command-centre" class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-50 w-[100%] md:w-[82%]">
    <div class="rounded-3xl overflow-hidden border border-blue-600/20 
                bg-gradient-to-b from-[#0a1628]/80 to-[#060f1e]/70 
                backdrop-blur-xl shadow-[0_25px_80px_rgba(2,6,23,0.85)] 
                transition-all duration-500 ease-in-out">

        <!-- Header Section -->
        <div class="flex items-center justify-between px-6 py-3 border-b border-blue-800/20 bg-[#0b172b]/60">
            <div class="flex items-center gap-3">
                <!-- Icon / Logo -->
                <div class="flex items-center gap-2">
                    <!-- MPKJ Logo dengan white background -->
                    <div class="bg-white p-1 rounded-full">
                        <img src="{{ asset('images/MPKJ.png') }}" alt="MPKJ Logo" class="h-8 w-8 rounded-full">
                    </div>

                    <!-- Text -->
                    <div>
                        <div class="text-sm font-bold text-blue-300 tracking-wide">MPKj STREETLIGHT COMMAND CENTRE</div>
                        <div class="text-xs text-gray-400">Real-time Streetlight Control Hub</div>
                    </div>
                </div>
            </div>

            <!-- Toggle Button -->
            <button id="centre-toggle" class="text-gray-300 hover:text-blue-400 text-sm font-semibold 
                                             px-3 py-2 rounded-lg hover:bg-blue-500/10 transition">
                <span id="toggle-text" class="text-green-400 font-bold animate-pulse">Show</span>
            </button>
        </div>

        <!-- Main Grid Content ( hidden) -->
        <div id="centre-content" class="hidden p-5 grid grid-cols-1 md:grid-cols-4 gap-5">

 <!-- Site Selection -->
 <div class="relative overflow-hidden 
            bg-gradient-to-b from-[#1a1f2c]/90 to-[#0e121b]/80 
            p-5 rounded-2xl border border-gray-600/30 
            shadow-[inset_0_0_25px_rgba(0,150,255,0.12),0_0_25px_rgba(0,0,0,0.8)] 
            backdrop-blur-md transition-all duration-500 ease-in-out">

  <!-- subtle glow overlay -->
  <div class="absolute inset-0 pointer-events-none 
              bg-[radial-gradient(circle_at_40%_30%,rgba(0,150,255,0.08),transparent_70%)]"></div>

  <div class="relative">
    <!-- HEADER -->
 <div class="text-xs text-blue-400 mb-3 font-bold uppercase tracking-wider flex items-center gap-2">
  <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
  Site Selection
 </div>

    <!-- Select Site -->
    <div class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wide">Select Site</div>
    <select id="select-site"
        class="w-full rounded-lg bg-[#1f2737]/70 border border-gray-700 
               px-2 py-1.5 text-sm text-gray-100 focus:border-blue-500 focus:ring-0">
        <option value="">Loading sites...</option>
    </select>

    <!-- Select Assets -->
    <div class="mt-4 text-xs text-gray-400 uppercase font-semibold tracking-wide">Select Assets</div>

    <!-- Select All Checkbox -->
    <div id="select-all-container" class="hidden mt-1 mb-2 flex items-center gap-2 text-sm text-blue-400">
        <input id="select-all-assets" type="checkbox" class="accent-blue-500 focus:ring-0">
        <label for="select-all-assets" class="cursor-pointer select-none hover:text-blue-300 transition">
            Select All Assets
        </label>
    </div>

    <!-- Asset List -->
    <div id="asset-list" 
         class="h-48 overflow-y-auto mt-2 space-y-2 text-sm text-gray-200 pr-2 
                scrollbar-thin scrollbar-thumb-blue-700/50 scrollbar-track-[#1a1f2c] 
                rounded-lg border border-gray-700/40 bg-[#1f2737]/60 shadow-inner">
        <div class="text-gray-500 text-xs italic">Select a site to view assets.</div>
    </div>
  </div>
 </div>


 <script>
 document.addEventListener("DOMContentLoaded", function () {
    const siteSelect = document.getElementById("select-site");
    const assetList = document.getElementById("asset-list");
    const selectAllContainer = document.getElementById("select-all-container");
    const selectAllCheckbox = document.getElementById("select-all-assets");

    // Load sites from Laravel API
    fetch("/api/sites")
        .then(response => response.json())
        .then(data => {
            siteSelect.innerHTML = `<option value="all">All Sites</option>`;
            data.forEach(site => {
                siteSelect.innerHTML += `<option value="${site.site_name}">${site.site_name}</option>`;
            });
        })
        .catch(err => {
            console.error("Error loading sites:", err);
            siteSelect.innerHTML = `<option value="">Error loading sites</option>`;
        });

    // When a site is selected load its assets
    siteSelect.addEventListener("change", function () {
        const site = siteSelect.value;
        assetList.innerHTML = "<div class='text-gray-400 text-xs'>Loading assets...</div>";
        selectAllContainer.classList.add("hidden");

        if (site === "" || site === "all") {
            assetList.innerHTML = "<div class='text-gray-400 text-xs'>Select a site to view assets.</div>";
            return;
        }

        fetch(`/api/assets/${site}`)
            .then(response => response.json())
            .then(data => {
                assetList.innerHTML = "";
                if (data.length === 0) {
                    assetList.innerHTML = "<div class='text-gray-400 text-xs'>No assets found for this site.</div>";
                    return;
                }

                // Show the Select All checkbox
                selectAllContainer.classList.remove("hidden");

                // Create asset checkboxes
                data.forEach(asset => {
                    assetList.innerHTML += `
                        <label class="flex items-center space-x-2 bg-[#071423]/40 rounded-md px-2 py-1 
                                       hover:bg-[#0d203a]/60 cursor-pointer transition">
                            <input type="checkbox" value="${asset.asset_no}" 
                                   class="asset-checkbox accent-blue-500 focus:ring-0">
                            <span>${asset.asset_no}</span>
                        </label>
                    `;
                });

                // Reset Select All when loading new assets
                selectAllCheckbox.checked = false;
            })
            .catch(err => {
                console.error("Error loading assets:", err);
                assetList.innerHTML = "<div class='text-red-400 text-xs'>Error loading assets.</div>";
            });
    });

    // Handle "Select All" checkbox behavior
    selectAllCheckbox.addEventListener("change", function () {
        const assetCheckboxes = assetList.querySelectorAll(".asset-checkbox");
        assetCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });
 });
 </script>

 <style>
 #asset-list::-webkit-scrollbar {
    width: 6px;
 }
 #asset-list::-webkit-scrollbar-track {
    background: #071423;
    border-radius: 10px;
 }
 #asset-list::-webkit-scrollbar-thumb {
    background-color: #1e40af;
    border-radius: 10px;
    transition: background-color 0.3s;
 }
 #asset-list::-webkit-scrollbar-thumb:hover {
    background-color: #3b82f6;
 }
 /* Firefox */
 #asset-list {
    scrollbar-width: thin;
    scrollbar-color: #1e40af #071423; 
 }
 </style>

 <!-- ==================Control Section ========================-->
 <div class="relative overflow-hidden 
            bg-gradient-to-b from-[#1a1f2c]/90 to-[#0e121b]/80 
            p-5 rounded-2xl border border-gray-600/30 
            shadow-[inset_0_0_25px_rgba(0,150,255,0.12),0_0_25px_rgba(0,0,0,0.8)] 
            backdrop-blur-md transition-all duration-500 ease-in-out">

  <!-- subtle glow overlay -->
  <div class="absolute inset-0 pointer-events-none 
              bg-[radial-gradient(circle_at_40%_30%,rgba(0,150,255,0.08),transparent_70%)]"></div>

  <div class="relative">
    <!-- HEADER -->
    <div class="text-xs text-blue-400 mb-3 font-bold uppercase tracking-wider flex items-center gap-2">
      <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
      Command Control
    </div>

    <div class="space-y-4">
      <!-- ON -->
      <label class="flex items-center gap-3 
                    bg-[#1f2737]/60 hover:bg-[#263043]/70 
                    transition-all px-3 py-2 rounded-lg 
                    border border-green-400/30 hover:border-green-400/50 
                    shadow-[0_0_15px_rgba(0,255,100,0.08)] cursor-pointer">
        <input id="toggle-selected-on" type="checkbox" class="accent-green-400 scale-110 cursor-pointer">
        <span class="text-sm text-gray-200">
          Turn Selected <span class="font-semibold text-green-400">ON</span>
        </span>
      </label>

      <!-- OFF -->
      <label class="flex items-center gap-3 
                    bg-[#1f2737]/60 hover:bg-[#3a1f1f]/60 
                    transition-all px-3 py-2 rounded-lg 
                    border border-red-400/30 hover:border-red-400/50 
                    shadow-[0_0_15px_rgba(255,0,100,0.08)] cursor-pointer">
        <input id="toggle-selected-off" type="checkbox" class="accent-red-400 scale-110 cursor-pointer">
        <span class="text-sm text-gray-200">
          Turn Selected <span class="font-semibold text-red-400">OFF</span>
        </span>
      </label>

   <!-- BRIGHTNESS CONTROL -->
 <div class="pt-3">
  <div class="text-xs text-blue-300 uppercase font-semibold mb-2 flex justify-between tracking-wide">
    <span>Set Brightness</span>
   <span id="selected-dim-label" 
      class="text-blue-400 font-semibold drop-shadow-[0_0_6px_rgba(0,255,255,0.6)] text-lg md:text-xl">
  100%
  </span>
 </div>

  <!-- Slider Container -->
  <div class="relative group">
    <input id="selected-dim" type="range" min="0" max="100" value="0"
  class="w-full h-3 rounded-full appearance-none cursor-pointer transition-all duration-300 outline-none
         shadow-[inset_0_0_10px_rgba(0,255,255,0.3),0_0_10px_rgba(0,0,0,0.8)]">

    <!-- 3D glowing overlay -->
    <div class="absolute inset-0 rounded-full bg-gradient-to-b from-[#1e293b]/40 to-[#020617]/80 pointer-events-none
                shadow-[inset_0_1px_4px_rgba(255,255,255,0.2),0_2px_6px_rgba(0,0,0,0.7)]"></div>
  </div>
 </div> 

  <style>
 /* === CUSTOM SLIDER STYLING === */
 #selected-dim::-webkit-slider-thumb {
  appearance: none;
  height: 24px;
  width: 24px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #38bdf8, #0ea5e9 60%, #082f49 100%);
  border: 2px solid #67e8f9;
  box-shadow:
    0 2px 8px rgba(0,0,0,0.6),
    0 0 18px rgba(56,189,248,0.9),
    inset 0 2px 4px rgba(255,255,255,0.25);
  transition: all 0.3s ease;
 }
 #selected-dim::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow:
    0 3px 8px rgba(0,0,0,0.6),
    0 0 28px rgba(56,189,248,1),
    inset 0 3px 6px rgba(255,255,255,0.3);
 }
 #selected-dim::-moz-range-thumb {
  height: 24px;
  width: 24px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #38bdf8, #0ea5e9 60%, #082f49 100%);
  border: 2px solid #67e8f9;
  box-shadow:
    0 2px 8px rgba(0,0,0,0.6),
    0 0 18px rgba(56,189,248,0.9),
    inset 0 2px 4px rgba(255,255,255,0.25);
  transition: all 0.3s ease;
 }
 #selected-dim::-moz-range-thumb:hover {
  transform: scale(1.1);
  box-shadow:
    0 3px 8px rgba(0,0,0,0.6),
    0 0 28px rgba(56,189,248,1),
    inset 0 3px 6px rgba(255,255,255,0.3);
 }

 /* === CUSTOM SLIDER STYLING === */
 #selected-dim::-webkit-slider-thumb {
  appearance: none;
  height: 24px;
  width: 24px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #38bdf8, #0ea5e9 60%, #082f49 100%);
  border: 2px solid #67e8f9;
  box-shadow:
    0 2px 8px rgba(0,0,0,0.6),
    0 0 18px rgba(56,189,248,0.9),
    inset 0 2px 4px rgba(255,255,255,0.25);
  transition: all 0.3s ease;
 }
 #selected-dim::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow:
    0 3px 8px rgba(0,0,0,0.6),
    0 0 28px rgba(56,189,248,1),
    inset 0 3px 6px rgba(255,255,255,0.3);
 }
 #selected-dim::-moz-range-thumb {
  height: 24px;
  width: 24px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #38bdf8, #0ea5e9 60%, #082f49 100%);
  border: 2px solid #67e8f9;
  box-shadow:
    0 2px 8px rgba(0,0,0,0.6),
    0 0 18px rgba(56,189,248,0.9),
    inset 0 2px 4px rgba(255,255,255,0.25);
  transition: all 0.3s ease;
 }
 #selected-dim::-moz-range-thumb:hover {
  transform: scale(1.1);
  box-shadow:
    0 3px 8px rgba(0,0,0,0.6),
    0 0 28px rgba(56,189,248,1),
    inset 0 3px 6px rgba(255,255,255,0.3);
 }
 </style>

 <script>
 const slider = document.getElementById("selected-dim");
 const label = document.getElementById("selected-dim-label");
 const toggleOn = document.getElementById("toggle-selected-on");
 const toggleOff = document.getElementById("toggle-selected-off");
 const clearBtn = document.getElementById("clear-btn");
 const applyBtn = document.getElementById("apply-btn");

 // === Update slider gradient fill ===
 function updateSliderFill(forceValue = null) {
  const value = forceValue !== null ? forceValue : slider.value;
  const fill = `linear-gradient(90deg, #00bfff ${value}%, #1e293b ${value}%)`;
  slider.style.background = fill;
  label.textContent = value + "%";
 }

 // === ON/OFF logic ===
 function handleToggle() {
  if (this === toggleOn && toggleOn.checked) {
    toggleOff.checked = false;
    slider.value = 100;
    updateSliderFill(100);
  } else if (this === toggleOff && toggleOff.checked) {
    toggleOn.checked = false;
    slider.value = 0;
    updateSliderFill(0);
  }
 }

 // === Event Listeners ===
 slider.addEventListener("input", () => updateSliderFill());
 toggleOn.addEventListener("change", handleToggle);
 toggleOff.addEventListener("change", handleToggle);

  // === Clear Button (optional) ===
  if (clearBtn) {
  clearBtn.addEventListener("click", () => {
    toggleOn.checked = false;
    toggleOff.checked = false;
    slider.value = 0;
    requestAnimationFrame(() => updateSliderFill(0));
   });
 }

 // === Apply Button ===
 applyBtn.addEventListener("click", () => {
  const brightness = slider.value;
  alert(`Applied brightness: ${brightness}%`);
 });

 // === Initial ===
 updateSliderFill(0);
  </script>

  <!-- BUTTONS -->
      <div class="pt-3 flex justify-center">
        <button onclick="applyToSelected()" 
                class="flex-1 py-2 rounded-lg 
                       bg-gradient-to-br from-blue-600/90 to-blue-800/80 
                       text-white font-semibold tracking-wide uppercase text-sm 
                       hover:shadow-[0_0_25px_rgba(59,130,246,0.6)] hover:scale-[1.04] 
                       transition-all duration-300">
          Apply
        </button>
        
      </div>
    </div>
  </div>
 </div>
 <script>
     document.addEventListener("DOMContentLoaded", function () {
    const toggleOn = document.getElementById("toggle-selected-on");
    const toggleOff = document.getElementById("toggle-selected-off");
    const brightnessSlider = document.getElementById("selected-dim");
    const brightnessLabel = document.getElementById("selected-dim-label");

    // === Update brightness label live ===
    brightnessSlider.addEventListener("input", function() {
        brightnessLabel.textContent = brightnessSlider.value + "%";
    });

    // === Mutual exclusive ON/OFF ===
    toggleOn.addEventListener("change", () => {
        if (toggleOn.checked) {
            toggleOff.checked = false;
            brightnessSlider.value = 100;
            brightnessLabel.textContent = "100%";
        }
    });

    toggleOff.addEventListener("change", () => {
        if (toggleOff.checked) {
            toggleOn.checked = false;
            brightnessSlider.value = 0;
            brightnessLabel.textContent = "0%";
        }
    });

    // === Custom popup ===
    function showPopup(message) {
        const popup = document.createElement("div");
        popup.className = "fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-green-600/90 text-white px-6 py-3 rounded-xl shadow-lg border border-green-400 text-sm font-semibold z-[9999] transition-all duration-500 ease-in-out";
        popup.textContent = message;
        document.body.appendChild(popup);
        setTimeout(() => {
            popup.style.opacity = "0";
            popup.style.transform = "translate(-50%, 40px)";
            setTimeout(() => popup.remove(), 500);
        }, 2500);
    }

    // === Clear button ===
    window.clearSelected = function() {
        toggleOn.checked = false;
        toggleOff.checked = false;
        brightnessSlider.value =100;
        brightnessLabel.textContent ="100%";
        document.querySelectorAll(".asset-checkbox").forEach(cb => cb.checked = false);
    }

    // === Apply button ===
    window.applyToSelected = async function() {
        const selectedAssets = Array.from(document.querySelectorAll(".asset-checkbox:checked"))
                                   .map(cb => cb.value);

        if (selectedAssets.length === 0) {
            showPopup("⚠️ Please select at least one asset.");
            return;
        }

        const isOn = toggleOn.checked;
        const isOff = toggleOff.checked;
        const brightness = parseInt(brightnessSlider.value);

        if (!isOn && !isOff) {
            showPopup("⚠️ Please select ON or OFF command.");
            return;
        }

        const commandType = "toggle_led";
        const onoffValue = isOn ? 1 : 0;

        // === Loop send command ===
        for (const asset of selectedAssets) {
            try {
                // ON/OFF
                await fetch("/mqtt/publish", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        asset_no: asset,
                        command: commandType,
                        onoff: onoffValue
                    })
                });

                // Brightness
                await fetch("/mqtt/publish", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        asset_no: asset,
                        command: "set_dimming",
                        dimming: brightness
                    })
                });

                await new Promise(res => setTimeout(res, 300)); // Delay sedikit
            } catch (err) {
                console.error(`Error sending for ${asset}:`, err);
            }
        }

        // === Green popup confirmation ===
        const mode = isOn ? "ON" : "OFF";
        showPopup(`✅ Command sent to ${selectedAssets.length} assets (${mode} + Brightness ${brightness}%)`);
    }
 });
 </script>
<!-- ==================== SYSTEM OVERVIEW: ALARM + SCHEDULE ==================== -->
<div class="relative overflow-hidden 
            bg-gradient-to-b from-[#1a1f2c]/90 to-[#0e121b]/80 
            p-5 rounded-2xl border border-gray-600/30 
            shadow-[inset_0_0_25px_rgba(0,150,255,0.12),0_0,0,0.8)] 
            backdrop-blur-md transition-all duration-500 ease-in-out">

  <div class="absolute inset-0 pointer-events-none 
              bg-[radial-gradient(circle_at_40%_30%,rgba(0,150,255,0.08),transparent_70%)]"></div>

  <div class="relative">

    <!-- HEADER -->
    <div class="text-xs text-blue-400 mb-3 font-bold uppercase tracking-wider flex items-center gap-2">
      <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
      System Overview
    </div>

    <!-- ================= ALARM LIST TABLE ================= -->
    <div class="mb-6">
      <div class="text-sm text-gray-400 font-semibold mb-2">Alarm List</div>
      <div class="overflow-y-auto max-h-64 rounded-xl border border-gray-700/50 
                  scrollbar-thin scrollbar-thumb-blue-700/40 scrollbar-track-[#0e121b]/60">
        <table id="alarm-table" class="min-w-full text-xs text-gray-200">
          <thead class="bg-[#0f172a] text-gray-400 uppercase sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 border-b border-gray-700 text-left">Site Name</th>
              <th class="px-3 py-2 border-b border-gray-700 text-left">Asset No</th>
              <th class="px-3 py-2 border-b border-gray-700 text-left">Alarm Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="3" class="text-gray-500 text-center py-3">Loading alarms...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= SCHEDULE LIST TABLE ================= -->
    <div>
      <div class="text-sm text-gray-400 font-semibold mb-2">Schedule (One Per Site)</div>

      <div class="overflow-x-auto rounded-xl border border-gray-700/50 
                  scrollbar-thin scrollbar-thumb-blue-700/40 scrollbar-track-[#0e121b]/60">
        <table id="schedule-table" class="min-w-[1000px] text-xs text-gray-200">
          <thead class="bg-[#0f172a] text-gray-400 uppercase sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 border-b border-gray-700">Site Name</th>
              <th class="px-3 py-2 border-b border-gray-700">On Time</th>
              <th class="px-3 py-2 border-b border-gray-700">Off Time</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim1 Start</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim1 Stop</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim1 Bright</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim2 Start</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim2 Stop</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim2 Bright</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim3 Start</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim3 Stop</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim3 Bright</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim4 Start</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim4 Stop</th>
              <th class="px-3 py-2 border-b border-gray-700">Dim4 Bright</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="15" class="text-gray-500 text-center py-3">Loading schedules...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- ==================== STYLE ==================== -->
<style>
.scrollbar-thin::-webkit-scrollbar {
  height: 6px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: linear-gradient(90deg, #2563eb80, #38bdf880);
  border-radius: 10px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: #0f172a;
  border-radius: 10px;
}
</style>

<!-- ==================== SCRIPT ==================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const alarmTableBody = document.querySelector("#alarm-table tbody");
    const scheduleTableBody = document.querySelector("#schedule-table tbody");
    const siteSelect = document.getElementById("select-site");
    let currentSite = "all";

    // === LOAD ALARMS ===
    async function loadAlarms(site = "all") {
        try {
            let url = "/alarms/popupAll";
            if (site && site !== "all" && site !== "") url = `/alarms/popupData?site=${encodeURIComponent(site)}`;
            const res = await fetch(url);
            const alarms = await res.json();

            alarmTableBody.innerHTML = "";
            if (!alarms.length) {
                alarmTableBody.innerHTML = `<tr><td colspan="3" class="text-gray-500 text-center py-3">No alarms for ${site}</td></tr>`;
                return;
            }

            alarms.forEach(alarm => {
    const status = (alarm.alarm_status || "").toLowerCase();
    let colorClass = "text-gray-300";

    // === ACTIVE / FAULT / ABNORMAL ALARMS (RED & BLINK) ===
    if (
        status.includes("active") ||
        status.includes("overvoltage") ||
        status.includes("undervoltage") ||
        status.includes("overcurrent") ||
        status.includes("undercurrent") ||
        status.includes("lights on abnormal alarm") ||
        status.includes("lights off abnormal alarm") ||
        status.includes("fault") ||
        status.includes("abnormal")
    ) {
        colorClass = "text-red-500 animate-pulse font-semibold";
    }

    // === CLEARED / NORMAL (GREEN) ===
    else if (
        status.includes("cleared") ||
        status.includes("normal") ||
        status.includes("ok")
    ) {
        colorClass = "text-green-400 font-semibold";
    }

    // === WARNING / STANDBY (YELLOW) ===
    else if (
        status.includes("warning") ||
        status.includes("pending") ||
        status.includes("standby")
    ) {
        colorClass = "text-yellow-400 font-semibold";
    }

    alarmTableBody.innerHTML += `
        <tr class="hover:bg-[#1e293b]/60 transition duration-200">
            <td class="px-3 py-1 border-b border-gray-700">${alarm.site_name || '-'}</td>
            <td class="px-3 py-1 border-b border-gray-700">${alarm.asset_no || '-'}</td>
            <td class="px-3 py-1 border-b border-gray-700 ${colorClass}">${alarm.alarm_status || '-'}</td>
        </tr>`;
});

        } catch (err) {
            console.error("Alarm load error:", err);
            alarmTableBody.innerHTML = `<tr><td colspan="3" class="text-red-400 text-center py-3">Failed to load alarms</td></tr>`;
        }
    }

    // === LOAD ONE SCHEDULE PER SITE ===
    async function loadSchedules(site = "all") {
        try {
            let url = `/api/scheduleBySite?site=${encodeURIComponent(site)}`;
            const res = await fetch(url);
            const schedules = await res.json();

            scheduleTableBody.innerHTML = "";
            if (!schedules.length) {
                scheduleTableBody.innerHTML = `<tr><td colspan="15" class="text-gray-500 text-center py-3">No schedules for ${site}</td></tr>`;
                return;
            }

            schedules.forEach(s => {
                scheduleTableBody.innerHTML += `
                <tr class="hover:bg-[#1e293b]/60 transition duration-200">
                    <td class="px-3 py-1 border-b border-gray-700">${s.site_name || '-'}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.on_time}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.off_time}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim1_start}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim1_stop}</td>
                    <td class="px-3 py-1 border-b border-gray-700 text-yellow-300 font-semibold">${s.dim1_brightness}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim2_start}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim2_stop}</td>
                    <td class="px-3 py-1 border-b border-gray-700 text-yellow-300 font-semibold">${s.dim2_brightness}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim3_start}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim3_stop}</td>
                    <td class="px-3 py-1 border-b border-gray-700 text-yellow-300 font-semibold">${s.dim3_brightness}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim4_start}</td>
                    <td class="px-3 py-1 border-b border-gray-700">${s.dim4_stop}</td>
                    <td class="px-3 py-1 border-b border-gray-700 text-yellow-300 font-semibold">${s.dim4_brightness}</td>
                </tr>`;
            });
        } catch (err) {
            console.error("Schedule load error:", err);
            scheduleTableBody.innerHTML = `<tr><td colspan="15" class="text-red-400 text-center py-3">Failed to load schedules</td></tr>`;
        }
    }

    // === SITE CHANGE HANDLER ===
    if (siteSelect) {
        siteSelect.addEventListener("change", function() {
            currentSite = siteSelect.value;
            loadAlarms(currentSite);
            loadSchedules(currentSite);
        });
    }

    // === AUTO REFRESH ===
    loadAlarms(currentSite);
    loadSchedules(currentSite);
    setInterval(() => {
        loadAlarms(currentSite);
        loadSchedules(currentSite);
    }, 30000);
});
</script>

 <!-- ==================== SYSTEM OVERVIEW: TOTALS ASSET ==================== -->
<!-- ==================== SYSTEM OVERVIEW: TOTALS ASSET (WITH PIE CHART) ==================== -->
<div class="relative overflow-hidden 
            bg-gradient-to-b from-[#1a1f2c]/90 to-[#0e121b]/80 
            p-6 rounded-3xl border border-blue-500/30 
            shadow-[0_0_25px_rgba(0,200,255,0.1),inset_0_0_25px_rgba(0,150,255,0.15)] 
            backdrop-blur-md transition-all duration-500 ease-in-out">

  <!-- Glow overlay -->
  <div class="absolute inset-0 pointer-events-none 
              bg-[radial-gradient(circle_at_50%_20%,rgba(0,180,255,0.1),transparent_70%)]"></div>

  <div class="relative z-10">
    <!-- HEADER -->
    <div class="text-xs text-blue-400 mb-4 font-bold uppercase tracking-wider flex items-center gap-2">
      <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
      Totals LED Status
    </div>

    <!-- CHARTS + TOTALS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
      <!-- 🟢 LED STATUS PIE -->
      <div class="flex flex-col items-center">
        <canvas id="ledChart" width="150" height="150"></canvas>
        <div class="mt-2 text-sm text-gray-300">LED ON / OFF</div>
      </div>

      <!-- 🔵 ONLINE/OFFLINE PIE -->
      <div class="flex flex-col items-center">
        <canvas id="onlineChart" width="150" height="150"></canvas>
        <div class="mt-2 text-sm text-gray-300">Online / Offline</div>
      </div>
    </div>

    <!-- TOTALS TEXT -->
    <div class="mt-6 text-sm space-y-2 text-gray-300">
      <div class="flex justify-between">
        <span>Total LED Light</span>
        <span id="total-led" class="font-bold text-white">0</span>
      </div>
      <div class="flex justify-between">
        <span>Online</span>
        <span id="total-online" class="font-bold text-green-400">0</span>
      </div>
      <div class="flex justify-between">
        <span>Offline</span>
        <span id="total-offline" class="font-bold text-red-400">0</span>
      </div>
      <div class="flex justify-between">
        <span>LED ON</span>
        <span id="total-led-on" class="font-bold text-green-400">0</span>
      </div>
      <div class="flex justify-between">
        <span>LED OFF</span>
        <span id="total-led-off" class="font-bold text-red-400">0</span>
      </div>
    </div>
  </div>
</div>

<!-- ==================== SCRIPT ==================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const siteSelect = document.getElementById("select-site");
  let currentSite = "all";

  const totalLED     = document.getElementById("total-led");
  const totalOnline  = document.getElementById("total-online");
  const totalOffline = document.getElementById("total-offline");
  const totalLEDOn   = document.getElementById("total-led-on");
  const totalLEDOff  = document.getElementById("total-led-off");

  // ==== Create charts ====
  const ledCtx = document.getElementById("ledChart").getContext("2d");
  const onlineCtx = document.getElementById("onlineChart").getContext("2d");

  const ledChart = new Chart(ledCtx, {
    type: "doughnut",
    data: {
      labels: ["ON", "OFF"],
      datasets: [{
        data: [0, 0],
        backgroundColor: ["#22c55e", "#ef4444"],
        borderWidth: 0,
      }],
    },
    options: {
      plugins: { legend: { display: false } },
      cutout: "70%",
    },
  });

  const onlineChart = new Chart(onlineCtx, {
    type: "doughnut",
    data: {
      labels: ["Online", "Offline"],
      datasets: [{
        data: [0, 0],
        backgroundColor: ["#3b82f6", "#f87171"],
        borderWidth: 0,
      }],
    },
    options: {
      plugins: { legend: { display: false } },
      cutout: "70%",
    },
  });

  async function loadTotals(site = "all") {
    try {
      const res = await fetch("/statusData");
      const data = await res.json();

      let filtered = data;
      if (site && site !== "all" && site !== "") {
        filtered = data.filter(item => (item.site_name || "").toLowerCase() === site.toLowerCase());
      }

      // === Count Totals ===
      const totalCount   = filtered.length;
      const onlineCount  = filtered.filter(l => l.status === "Online").length;
      const offlineCount = totalCount - onlineCount;
      const ledOnCount   = filtered.filter(l => Number(l.led_status) === 1).length;
      const ledOffCount  = totalCount - ledOnCount;

      // === Update Charts ===
      ledChart.data.datasets[0].data = [ledOnCount, ledOffCount];
      ledChart.update();

      onlineChart.data.datasets[0].data = [onlineCount, offlineCount];
      onlineChart.update();

      // === Update Totals ===
      totalLED.textContent     = totalCount;
      totalOnline.textContent  = onlineCount;
      totalOffline.textContent = offlineCount;
      totalLEDOn.textContent   = ledOnCount;
      totalLEDOff.textContent  = ledOffCount;

    } catch (err) {
      console.error("Failed to load totals:", err);
    }
  }

  // === Site selection listener ===
  if (siteSelect) {
    siteSelect.addEventListener("change", function () {
      currentSite = siteSelect.value;
      loadTotals(currentSite);
    });
  }

  // === Auto refresh ===
  loadTotals(currentSite);
  setInterval(() => loadTotals(currentSite), 30000);
});
</script>



<script>
    const centreToggle = document.getElementById('centre-toggle');
    const centreContent = document.getElementById('centre-content');
    const toggleText = document.getElementById('toggle-text');

    // Start page hidden => toggleText = 'Show'
    toggleText.innerText = 'Show';

    centreToggle.addEventListener('click', () => {
        if (centreContent.classList.contains('hidden')) {
            centreContent.classList.remove('hidden');
            toggleText.innerText = 'Hide';
        } else {
            centreContent.classList.add('hidden');
            toggleText.innerText = 'Show';
        }
    });
</script>

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

            async function loadStreetLightStatus(callbackAfterLoad = null){
                await fetchAllAlarms();
                try{
                    const res = await fetch("{{ route('streetlight.status.data') }}",{ credentials:"same-origin" });
                    const data = await res.json();
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

            // Load map & markers
            loadStreetLightStatus(()=>{
                if(selectedAssetNo) {
                    setTimeout(()=>{ openLocationPopup(selectedAssetNo); }, 500);
                }
            });

            // Auto-update every 15s without resetting map center
            setInterval(()=>loadStreetLightStatus(), 15000);
        });
        </script>
        @endpush
    </div>
</x-app-layout>
