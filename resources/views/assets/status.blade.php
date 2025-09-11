<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Street Light Status
        </h2>
    </x-slot>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Lighting Status by Site</h3>

        @forelse($groupedStatuses as $siteName => $statuses)
            <h4 class="text-md font-semibold mt-6 mb-2">{{ $siteName }}</h4>
            <table class="min-w-full border mb-6">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Asset No</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">LED Status</th>
                        <th class="border px-4 py-2">Dimming</th>
                        <th class="border px-4 py-2">Power (W)</th>
                        <th class="border px-4 py-2">Energy (kWh)</th>
                        <th class="border px-4 py-2">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statuses as $status)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $status->asset_no }}</td>
                            <td class="border px-4 py-2">{{ $status->status }}</td>
                            <td class="border px-4 py-2">
                                @if ($status->led_status == 1)
                                    <span class="text-green-600 font-bold">ON</span>
                                @else
                                    <span class="text-red-600 font-bold">OFF</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2">{{ $status->dimming ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $status->power ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $status->energy ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $status->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p class="text-gray-500">No data available</p>
        @endforelse
    </div>
</x-app-layout>
