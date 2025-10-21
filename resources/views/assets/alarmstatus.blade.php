<x-app-layout>
{{-- 🔹 Flash Messages Container --}}
<div class="max-w-7xl mx-auto px-6">
    <div id="flash-container" class="space-y-2 mb-6"></div>
</div>

<div class="p-6 max-w-7xl mx-auto space-y-8 min-h-screen">
    {{-- 🔹 Page Heading --}}
    <h1 class="text-center text-3xl font-bold text-white mb-6">
        Active & Historical Alarms
    </h1>

    {{-- 🔹 Alarm Site Blocks --}}
    @forelse ($alarms as $site => $siteAlarms)
        <div class="bg-gray-900/80 border border-gray-700 rounded-2xl p-6 shadow-xl backdrop-blur-md site-block"
             data-site="{{ $site }}">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-blue-400">{{ $site }}</h3>
                <button onclick="clearSiteAlarms('{{ $site }}')"
                        class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold px-4 py-2 rounded-xl shadow-md transition duration-200">
                    Clear Site
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-700 shadow-inner">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-gray-800/90 border-b border-gray-700">
                        <tr class="text-gray-300 uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 border-r border-gray-700">Asset No</th>
                            <th class="px-4 py-3 border-r border-gray-700">Latitude</th>
                            <th class="px-4 py-3 border-r border-gray-700">Longitude</th>
                            <th class="px-4 py-3 border-r border-gray-700">Alarm Status</th>
                            <th class="px-4 py-3 border-r border-gray-700">Alarm Time</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-900/60">
                        @foreach ($siteAlarms as $alarm)
                            <tr data-id="{{ $alarm->id }}" class="hover:bg-gray-700/50 transition duration-200">
                                <td class="px-4 py-2 border-t border-gray-800">{{ $alarm->asset_no }}</td>
                                <td class="px-4 py-2 border-t border-gray-800">{{ $alarm->latitude }}</td>
                                <td class="px-4 py-2 border-t border-gray-800">{{ $alarm->longitude }}</td>
                                <td class="px-4 py-2 border-t border-gray-800 text-center font-bold">
                                    @php
                                        $status = strtolower(trim($alarm->alarm_status));
                                        $color = match (true) {
                                            str_contains($status, 'overvoltage') => 'text-red-500 animate-pulse',
                                            str_contains($status, 'undervoltage') => 'text-red-500 animate-pulse',
                                            str_contains($status, 'overcurrent') => 'text-red-500 animate-pulse',
                                            str_contains($status, 'Undercurrent') => 'text-red-500 animate-pulse',
                                            str_contains($status, 'LIGHTS ON ABNORMAL ALARM') =>'text-red-500 animate-pulse',
                                            str_contains($status, 'LIGHTS OFF ABNORMAL ALARM') =>'text-red-500 animate-pulse',
                                            str_contains($status, 'active') => 'text-red-500 animate-pulse font-bold',
                                            str_contains($status, 'cleared') => 'text-green-400 font-bold',
                                            str_contains($status, 'warning') => 'text-yellow-400 font-bold',
                                            default => 'text-gray-300',
                                        };
                                    @endphp
                                    <span class="{{ $color }} drop-shadow-sm uppercase">
                                        {{ strtoupper($alarm->alarm_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border-t border-gray-800 text-center">
                                    {{ \Carbon\Carbon::parse($alarm->timestamp)->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-4 py-2 border-t border-gray-800 text-center flex justify-center gap-2">
                                    <button onclick="sendReport({{ $alarm->id }})"
                                            class="report-btn bg-amber-500 hover:bg-amber-400 text-gray-900 px-3 py-1.5 rounded-md shadow-md font-semibold transition">
                                        Report
                                    </button>

                                    <a href="{{ route('dashboard', ['lat' => $alarm->latitude, 'lng' => $alarm->longitude]) }}"
                                       class="bg-green-600 hover:bg-green-500 text-white px-3 py-1.5 rounded-md shadow-md font-semibold transition">
                                       Location
                                    </a>

                                    <button onclick="deleteAlarm({{ $alarm->id }})"
                                            class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-md shadow-md font-semibold transition">
                                        Clear
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p class="text-gray-400 text-center py-10 text-lg"> No alarms found.</p>
    @endforelse
</div>

<style>
    body { background: radial-gradient(circle at top, #0f172a, #020617); color: #e2e8f0; }
    .site-block { transition: all 0.3s ease; }
    .site-block:hover { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); border-color: rgba(59, 130, 246, 0.7); }
    th, td { text-align: center; }
    .animate-pulse { animation: pulse 1.5s infinite; }
    /* Flash-style messages */
    .flash-success { background-color: #16a34a/20; border: 1px solid #4ade80; color: #86efac; padding: 0.75rem 1rem; border-radius: 0.75rem; font-weight: 600; shadow-md; transition: opacity 0.3s; }
    .flash-error { background-color: #dc2626/20; border: 1px solid #f87171; color: #fca5a5; padding: 0.75rem 1rem; border-radius: 0.75rem; font-weight: 600; shadow-md; transition: opacity 0.3s; }
</style>

@push('scripts')
<script>
    //  Flash-style messages
    function showFlash(message, type = 'success', duration = 2500) {
        const container = document.getElementById('flash-container');
        const div = document.createElement('div');
        div.className = `${type === 'success' ? 'flash-success' : 'flash-error'} opacity-0`;
        div.textContent = message;
        container.appendChild(div);
        setTimeout(() => div.classList.add('opacity-100'), 50);
        setTimeout(() => { div.classList.remove('opacity-100'); setTimeout(() => div.remove(), 300); }, duration);
    }

    // 🔹 Send Telegram report
    async function sendReport(id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        const btn = row?.querySelector('.report-btn');
        const assetNo = row?.children[0]?.textContent.trim() || "N/A";
        const latitude = row?.children[1]?.textContent.trim() || "N/A";
        const longitude = row?.children[2]?.textContent.trim() || "N/A";
        const status = row?.children[3]?.textContent.trim() || "N/A";
        const time = row?.children[4]?.textContent.trim() || "N/A";
        const site = row.closest('.site-block')?.getAttribute('data-site') || "Unknown Site";
        const wazeLink = `https://www.waze.com/ul?ll=${latitude},${longitude}&navigate=yes`;

        try {
            btn.disabled = true;
            btn.textContent = 'Sending...';
            const message = `
🚨 *ST ALARM NOTIFICATION* 🚨  

📍 Site Name:  *${site}*
🔢 Asset No:   *${assetNo}*

⚠️ Alarm Status: *${status}*
⏰ Alarm Time:   *${time}*

📍 Latitude:  *${latitude}*
📍 Longitude: *${longitude}*

🚗 [Drive to Location>>🚗](${wazeLink})

            `;
            const res = await fetch(`/alarms/${id}/report`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json", "Content-Type": "application/json" },
                body: JSON.stringify({ message })
            });
            const data = await res.json();
            if (res.ok && data.success) showFlash('Telegram report sent successfully ', 'success');
            else showFlash('Failed to send Telegram report ❌', 'error');
        } catch (err) {
            console.error(err);
            showFlash('Error sending Telegram report ❌', 'error');
        } finally { btn.disabled = false; btn.textContent = 'Report'; }
    }

    // 🔹 Delete alarm with flash
    async function deleteAlarm(id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        const asset = row?.children[0]?.textContent || 'N/A';
        try {
            const res = await fetch(`/alarms/${id}`, {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
            });
            const data = await res.json();
            if (res.ok && data.success) { row?.remove(); showFlash(`Alarm ${asset} cleared ✅`, 'success'); }
            else showFlash(data.message || 'Failed to delete alarm ❌', 'error');
        } catch (err) { console.error(err); showFlash('Error deleting alarm ❌', 'error'); }
    }

    // 🔹 Clear all alarms for site with flash
    async function clearSiteAlarms(site) {
        const block = document.querySelector(`.site-block[data-site='${site}']`);
        try {
            const res = await fetch(`/alarms/clear-site/${site}`, {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
            });
            const data = await res.json();
            if (res.ok && data.success) { block?.remove(); showFlash(`All alarms for ${site} cleared ✅`, 'success'); }
            else showFlash(data.message || `Failed to clear alarms for ${site} ❌`, 'error');
        } catch (err) { console.error(err); showFlash(`Error clearing alarms for ${site} ❌`, 'error'); }
    }
</script>
@endpush
</x-app-layout>
