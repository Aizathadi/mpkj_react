<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-white text-center">Alarm Configuration</h1>

        {{-- ✅ Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-600/20 border border-green-400 text-green-300 px-4 py-3 rounded-lg mb-6 shadow-md">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-600/20 border border-red-400 text-red-300 px-4 py-3 rounded-lg mb-6 shadow-md">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔹 Site and Asset Selection --}}
        <section class="mb-10 bg-gray-900/70 border border-gray-700 rounded-lg p-5 shadow-lg backdrop-blur">
            <h2 class="text-xl font-semibold mb-4 text-blue-300">Select Assets for Alarm Configuration</h2>
            <div class="space-y-3">
                @foreach ($sites as $index => $site)
                    <div class="border border-gray-700 rounded-lg p-3 bg-gray-800/70 hover:bg-gray-800/90 transition shadow-sm">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center font-bold text-lg text-gray-100">
                                <input type="checkbox" class="mr-2 site-checkbox accent-blue-500" data-site="{{ $site['site_name'] }}">
                                {{ $site['site_name'] }}
                                <span class="text-sm text-gray-400 ml-2">(Select All)</span>
                            </label>

                            {{-- 🔵 Professional Blue Triangle Toggle Icon --}}
                            <button type="button" class="toggle-assets text-blue-400 hover:text-blue-300 transition" data-target="assets-{{ $loop->index }}">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="triangle-icon w-6 h-6 transition-transform duration-300 ease-in-out" 
                                    style="fill:#38bdf8;" 
                                    viewBox="0 0 24 24">
                                    <polygon points="12,16 6,8 18,8" />
                                </svg>
                            </button>
                        </div>

                        <div id="assets-{{ $loop->index }}" class="hidden flex flex-col pl-6 mt-2 space-y-1">
                            @foreach ($site['assets'] as $asset)
                                <label class="flex items-center text-gray-300 hover:text-blue-400">
                                    <input type="checkbox"
                                           name="assets[]"
                                           value="{{ $asset['asset_no'] }}"
                                           form="alarmForm"
                                           class="mr-2 asset-checkbox accent-green-500"
                                           data-site="{{ $site['site_name'] }}">
                                    {{ $asset['asset_no'] }}
                                    <span class="text-xs text-gray-500 ml-2">
                                        ({{ $asset['longitude'] }}, {{ $asset['latitude'] }})
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Alarm Settings --}}
        <form id="alarmForm" method="POST" action="{{ route('alarm.program') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                {{-- Current Alarm Settings --}}
                <div class="alarm-card p-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-300">Current Alarm Settings</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="transparent-label mb-1">Under Current (mA)</label>
                            <input type="number" name="under_current" class="transparent-input" placeholder="Set Threshold min">
                        </div>
                        <div>
                            <label class="transparent-label mb-1">Over Current (mA)</label>
                            <input type="number" name="over_current" class="transparent-input" placeholder="Set Threshold max">
                        </div>
                    </div>
                </div>

                {{-- Voltage Alarm Settings --}}
                <div class="alarm-card p-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-300">Voltage Alarm Settings</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="transparent-label mb-1">Under Voltage (V)</label>
                            <input type="number" name="under_voltage" class="transparent-input" placeholder="Set Threshold min">
                        </div>
                        <div>
                            <label class="transparent-label mb-1">Over Voltage (V)</label>
                            <input type="number" name="over_voltage" class="transparent-input" placeholder="Set Threshold max">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Hidden container for selected assets --}}
            <div id="asset_container"></div>

            {{-- Submit --}}
            <div class="text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-lg transition">
                    Program
                </button>
            </div>
        </form>
    </div>

    {{-- Custom Styles --}}
    <style>
        body {
            background: radial-gradient(circle at top, #0f172a, #020617);
            color: #e2e8f0;
        }

        .transparent-input {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f9fafb;
            border-radius: 6px;
            padding: 8px;
            width: 100%;
            text-align: center;
            transition: 0.3s;
        }
        .transparent-input:focus {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 6px rgba(59, 130, 246, 0.6);
        }

        .transparent-label {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .alarm-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(6px);
        }

        .triangle-icon {
            transform-origin: center;
            transition: transform 0.3s ease;
        }
        .triangle-icon.open {
            transform: rotate(180deg);
        }
    </style>

    {{-- 🧠 Scripts --}}
    @push('scripts')
    <script>
        // Toggle "Select All" for site assets
        document.querySelectorAll('.site-checkbox').forEach(siteCb => {
            siteCb.addEventListener('change', function () {
                const site = this.dataset.site;
                document.querySelectorAll(`.asset-checkbox[data-site="${site}"]`)
                    .forEach(cb => cb.checked = this.checked);
            });
        });

        // Expand/collapse asset list with smooth icon rotation
        document.querySelectorAll('.toggle-assets').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                const icon = this.querySelector('.triangle-icon');
                target.classList.toggle('hidden');
                icon.classList.toggle('open');
            });
        });

        // Inject selected assets into form before submit
        document.getElementById("alarmForm").addEventListener("submit", function() {
            const container = document.getElementById("asset_container");
            container.innerHTML = "";
            document.querySelectorAll(".asset-checkbox:checked").forEach(cb => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "assets[]";
                input.value = cb.value;
                container.appendChild(input);
            });
        });
    </script>
    @endpush
</x-app-layout>
