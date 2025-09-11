
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Alarm Status
            </h2>
            <!-- 🔴 Clear All Alarms (global) -->
            <button 
                onclick="clearAllAlarms()" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Clear All Alarms
            </button>
        </div>
    </x-slot>

    <div class="space-y-6" id="alarmContainer">
        @forelse ($alarms as $site => $siteAlarms)
            <div class="bg-white shadow rounded-lg p-4 site-block" data-site="{{ $site }}">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold text-gray-700">
                        {{ $site }}
                    </h3>
                    <!-- 🔴 Clear Site Alarms -->
                    <button 
                        onclick="clearSiteAlarms('{{ $site }}')" 
                        class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">
                        Clear Site
                    </button>
                </div>

                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Asset No</th>
                            <th class="px-4 py-2 border">Latitude</th>
                            <th class="px-4 py-2 border">Longitude</th>
                            <th class="px-4 py-2 border">Alarm Status</th>
                            <th class="px-4 py-2 border">Alarm Time</th>
                            <th class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siteAlarms as $alarm)
                            <tr data-id="{{ $alarm->id }}">
                                <td class="px-4 py-2 border">{{ $alarm->asset_no }}</td>
                                <td class="px-4 py-2 border">{{ $alarm->latitude }}</td>
                                <td class="px-4 py-2 border">{{ $alarm->longitude }}</td>
                                <td class="px-4 py-2 border">
                                    <span class="font-bold text-red-600">
                                        {{ $alarm->alarm_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">
                                    {{ \Carbon\Carbon::parse($alarm->timestamp)->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-4 py-2 border text-center">
                                    <!-- 🔴 Clear Row Alarm -->
                                    <button 
                                        onclick="deleteAlarm({{ $alarm->id }})" 
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Clear
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p class="text-gray-600">No alarms found.</p>
        @endforelse
    </div>

    @push('scripts')
    <script>
        // ✅ Update sidebar badge dynamically
        function updateAlarmBadge(count) {
            let badge = document.querySelector("#alarm-badge");
            if (!badge) {
                // create badge if not exists
                const link = document.querySelector("a[href='{{ route('alarms.index') }}']");
                if (link) {
                    badge = document.createElement("span");
                    badge.id = "alarm-badge";
                    badge.className = "bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full";
                    link.appendChild(badge);
                }
            }
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = "inline-block";
                } else {
                    badge.style.display = "none";
                }
            }
        }

        // 🔴 Delete a single alarm row
        async function deleteAlarm(id) {
            if (!confirm("Clear this alarm?")) return;

            try {
                const response = await fetch(`/alarms/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                });

                const data = await response.json();
                if (data.success) {
                    const row = document.querySelector(`tr[data-id='${id}']`);
                    if (row) {
                        row.remove();

                        // if table becomes empty, remove site block
                        const tbody = row.closest("tbody");
                        if (tbody && tbody.children.length === 0) {
                            row.closest(".site-block").remove();
                        }
                    }
                    updateAlarmBadge(data.count);
                }
            } catch (err) {
                console.error("Error deleting alarm:", err);
            }
        }

        // 🔴 Clear all alarms in a site
        async function clearSiteAlarms(site) {
            if (!confirm(`Clear all alarms for site: ${site}?`)) return;

            try {
                const response = await fetch(`/alarms/clear-site/${site}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                });

                const data = await response.json();
                if (data.success) {
                    const block = document.querySelector(`.site-block[data-site='${site}']`);
                    if (block) block.remove();
                    updateAlarmBadge(data.count);
                }
            } catch (err) {
                console.error("Error clearing site alarms:", err);
            }
        }

        // 🔴 Clear all alarms (global)
        async function clearAllAlarms() {
            if (!confirm("Clear ALL alarms?")) return;

            try {
                const response = await fetch("{{ route('alarms.clear') }}", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById("alarmContainer").innerHTML = "<p class='text-gray-600'>No alarms found.</p>";
                    updateAlarmBadge(data.count);
                }
            } catch (err) {
                console.error("Error clearing alarms:", err);
            }
        }
    </script>
    @endpush
</x-app-layout>
