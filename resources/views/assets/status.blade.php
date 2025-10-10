<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-8 min-h-screen">
        {{-- 🔹 Page Heading --}}
        <h1 class="text-center text-3xl font-bold text-white mb-6">
            Street Light Status
        </h1>

        {{-- 🔹 Lighting Status by Site --}}
        @forelse($groupedStatuses as $siteName => $statuses)
            <div class="bg-gray-900/70 border border-gray-700 rounded-lg p-6 shadow-lg backdrop-blur-md site-block">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-blue-300">{{ $siteName }}</h3>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-700 shadow-inner">
                    <table class="min-w-full text-sm text-gray-200 border-collapse">
                        <thead class="bg-gray-800/80 border-b border-gray-700">
                            <tr class="text-gray-300 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 border-r border-gray-700">Asset No</th>
                                <th class="px-4 py-3 border-r border-gray-700">Status</th>
                                <th class="px-4 py-3 border-r border-gray-700">LED Status</th>
                                <th class="px-4 py-3 border-r border-gray-700">Dimming</th>
                                <th class="px-4 py-3 border-r border-gray-700">Power (W)</th>
                                <th class="px-4 py-3 border-r border-gray-700">Energy (kWh)</th>
                                <th class="px-4 py-3 border-r border-gray-700">Last Data Recorded</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900/50">
                            @foreach($statuses as $status)
                                <tr class="hover:bg-gray-700/40 transition duration-200">
                                    <td class="px-4 py-2 border-t border-gray-800">{{ $status->asset_no }}</td>

                                    {{-- 🔹 Online / Offline --}}
                                    <td class="px-4 py-2 border-t border-gray-800 text-center font-bold">
                                        @if ($status->is_online)
                                            <span class="text-green-400 font-bold drop-shadow-sm">ONLINE</span>
                                        @else
                                            <span class="text-red-500 font-bold animate-pulse drop-shadow-sm">OFFLINE</span>
                                        @endif
                                    </td>

                                    {{-- 🔹 LED Status --}}
                                    <td class="px-4 py-2 border-t border-gray-800 text-center font-bold">
                                        @if ($status->led_status == 1)
                                            <span class="text-green-400">ON</span>
                                        @else
                                            <span class="text-red-500">OFF</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 border-t border-gray-800 text-center">
                                        {{ $status->dimming ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">
                                        {{ $status->power ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">
                                        {{ $status->energy ?? 'N/A' }}
                                    </td>

                                    <td class="px-4 py-2 border-t border-gray-800 text-center">
                                        {{ $status->last_seen_at ? $status->last_seen_at->format('Y-m-d H:i:s') : 'Never' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-center py-10 text-lg">✅ No lighting status data found.</p>
        @endforelse
    </div>

    {{-- 🌌 Background + Styling --}}
    <style>
        body {
            background: radial-gradient(circle at top, #0f172a, #020617);
            color: #e2e8f0;
        }

        .site-block {
            transition: all 0.3s ease;
        }

        .site-block:hover {
            box-shadow: 0 0 18px rgba(59, 130, 246, 0.3);
            border-color: rgba(59, 130, 246, 0.5);
        }

        table {
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            text-align: center;
        }

        .animate-pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</x-app-layout>
