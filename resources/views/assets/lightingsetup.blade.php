<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-white text-center">Lighting Setup</h1>

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

        {{-- 🔹 Asset Selection --}}
        <section class="mb-10 bg-gray-900/70 border border-gray-700 rounded-lg p-5 shadow-lg backdrop-blur">
            <h2 class="text-xl font-semibold mb-4 text-blue-300">Select Assets</h2>
            <div class="space-y-3">
                @foreach ($sites as $site)
                    <div class="border border-gray-700 rounded-lg p-3 bg-gray-800/70 hover:bg-gray-800/90 transition shadow-sm">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center font-bold text-lg text-gray-100">
                                <input type="checkbox" class="mr-2 site-checkbox accent-blue-500" data-site="{{ $site['site_name'] }}">
                                {{ $site['site_name'] }}
                                <span class="text-sm text-gray-400 ml-2">(Select All)</span>
                            </label>

                            {{-- 🔹 Animated Blue Triangle Button --}}
                            <button type="button" 
                                    class="toggle-assets text-gray-400 hover:text-white transition" 
                                    data-target="assets-{{ $loop->index }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="triangle-icon w-6 h-6 transition-transform duration-500 ease-in-out"
                                     fill="#7DD3FC"
                                     viewBox="0 0 24 24">
                                    <polygon points="12,16 6,8 18,8" />
                                </svg>
                            </button>
                        </div>

                        {{-- 🔹 Asset List --}}
                        <div id="assets-{{ $loop->index }}" class="hidden flex flex-col pl-6 mt-2 space-y-1">
                            @foreach ($site['assets'] as $asset)
                                <label class="flex items-center text-gray-300 hover:text-blue-400 transition">
                                    <input type="checkbox"
                                           name="asset_ids[]"
                                           value="{{ $asset['id'] }}"
                                           form="timeDimmingForm"
                                           class="mr-2 asset-checkbox accent-green-500"
                                           data-site="{{ $site['site_name'] }}">
                                    {{ $asset['asset_no'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 🔹 On/Off & Dimming --}}
        <div class="bg-gray-900/70 border border-gray-700 rounded-lg shadow-lg p-6 mb-10 backdrop-blur">
            <form id="timeDimmingForm" method="POST" action="{{ route('lighting.setup.store') }}">
                @csrf
                <h2 class="text-xl font-bold mb-6 text-blue-300">On/Off & Dimming Schedule</h2>

                {{-- On/Off --}}
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block mb-2 font-medium text-gray-200">On Time</label>
                        <div class="flex gap-2">
                            <input type="number" name="on_time_h" placeholder="HH" class="transparent-input w-1/3">
                            <input type="number" name="on_time_m" placeholder="MM" class="transparent-input w-1/3">
                            <input type="number" name="on_time_s" placeholder="SS" class="transparent-input w-1/3">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 font-medium text-gray-200">Off Time</label>
                        <div class="flex gap-2">
                            <input type="number" name="off_time_h" placeholder="HH" class="transparent-input w-1/3">
                            <input type="number" name="off_time_m" placeholder="MM" class="transparent-input w-1/3">
                            <input type="number" name="off_time_s" placeholder="SS" class="transparent-input w-1/3">
                        </div>
                    </div>
                </div>

                {{-- Dimming Section --}}
                <div class="space-y-5">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="dimming-card p-5">
                            <label class="block font-semibold mb-4 text-blue-200 text-lg">Dimming {{ $i }}</label>
                            <div class="grid grid-cols-4 md:grid-cols-8 gap-4 items-center">
                                <div class="flex flex-col">
                                    <label class="transparent-label mb-1">H</label>
                                    <input type="number" name="dimming{{ $i }}_h" class="transparent-input" placeholder="HH">
                                </div>
                                <div class="flex flex-col">
                                    <label class="transparent-label mb-1">M</label>
                                    <input type="number" name="dimming{{ $i }}_m" class="transparent-input" placeholder="MM">
                                </div>
                                <div class="flex flex-col">
                                    <label class="transparent-label mb-1">S</label>
                                    <input type="number" name="dimming{{ $i }}_s" class="transparent-input" placeholder="SS">
                                </div>
                                <div class="flex flex-col col-span-2 md:col-span-2">
                                    <label class="transparent-label mb-1">Brightness (%)</label>
                                    <input type="number" name="dimming{{ $i }}_value" class="transparent-input" placeholder="%">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div id="time_asset_container"></div>

                <div class="mt-8 text-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-lg transition">
                        Program
                    </button>
                </div>
            </form>
        </div>

        {{-- 🔹 Lux Control --}}
        <div class="bg-gray-900/70 border border-gray-700 rounded-lg shadow-lg p-6 backdrop-blur">
            <form id="luxForm" method="POST" action="{{ route('lighting.setup.lux') }}">
                @csrf
                <h2 class="text-xl font-bold mb-6 text-blue-300">Lux Control</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="transparent-label mb-1">Lux ON</label>
                        <input type="number" name="lux_on" class="transparent-input w-full" placeholder="On">
                    </div>
                    <div>
                        <label class="transparent-label mb-1">Lux OFF</label>
                        <input type="number" name="lux_off" class="transparent-input w-full" placeholder="Off">
                    </div>
                    <div>
                        <label class="transparent-label mb-1">Delay (fixed)</label>
                        <input type="number" name="lux_delay" value="59" readonly class="transparent-input w-full opacity-70">
                    </div>
                </div>

                <div id="lux_asset_container"></div>

                <div class="mt-8 text-center">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-lg transition">
                        Program
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🪄 Custom Styles --}}
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
            text-align: center;
            transition: 0.3s;
        }
        .transparent-input:focus {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 6px rgba(59, 130, 246, 0.6);
        }

        .transparent-label { color: #94a3b8; font-size: 0.9rem; }

        .dimming-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        /* 🔹 Triangle Animation */
        .triangle-icon {
            filter: drop-shadow(0 0 5px rgba(96, 165, 250, 0.7));
            transform: rotate(0deg);
        }
        .rotate-180 {
            transform: rotate(180deg);
        }
        .toggle-assets:hover .triangle-icon {
            filter: drop-shadow(0 0 10px rgba(96, 165, 250, 1));
        }
    </style>

    {{-- 🔸 Scripts --}}
    @push('scripts')
    <script>
        // ✅ Select All per Site
        document.querySelectorAll('.site-checkbox').forEach(siteCb => {
            siteCb.addEventListener('change', function () {
                const site = this.dataset.site;
                document.querySelectorAll(`.asset-checkbox[data-site="${site}"]`)
                    .forEach(cb => cb.checked = this.checked);
            });
        });

        // 🔽 Smooth Expand/Collapse + Clean Rotation
        document.querySelectorAll('.toggle-assets').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                const icon = this.querySelector('.triangle-icon');
                target.classList.toggle('hidden');
                icon.classList.toggle('rotate-180', !target.classList.contains('hidden'));
            });
        });

        // Inject selected assets into form
        function injectAssets(formId, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = "";
            document.querySelectorAll(".asset-checkbox:checked").forEach(cb => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "asset_ids[]";
                input.value = cb.value;
                container.appendChild(input);
            });
        }

        document.getElementById("timeDimmingForm").addEventListener("submit", function() {
            injectAssets("timeDimmingForm", "time_asset_container");
        });
        document.getElementById("luxForm").addEventListener("submit", function() {
            injectAssets("luxForm", "lux_asset_container");
        });
    </script>
    @endpush
</x-app-layout>
