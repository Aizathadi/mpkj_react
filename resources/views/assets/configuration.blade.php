<x-app-layout>
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Alarm Configuration</h1>

    <!-- ✅ Flash success/error messages -->
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('alarm.program') }}" method="POST">
        @csrf

        <!-- Loop through sites and show assets -->
        @foreach($sites as $index => $site)
        <div class="mb-6 border border-gray-300 rounded-lg shadow-sm bg-white p-4">
            <h2 class="font-semibold text-lg mb-2">{{ $site['site_name'] }}</h2>
            
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2">
                            <input type="checkbox" 
                                   id="selectAll_{{ $index }}" 
                                   onclick="toggleSelectAll({{ $index }})">
                        </th>
                        <th class="p-2">Asset No</th>
                        <th class="p-2">Longitude</th>
                        <th class="p-2">Latitude</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($site['assets'] as $asset)
                    <tr class="border-t">
                        <td class="p-2">
                            <input type="checkbox" 
                                   name="assets[]" 
                                   value="{{ $asset['asset_no'] }}" 
                                   class="assetCheckbox_{{ $index }}">
                        </td>
                        <td class="p-2">{{ $asset['asset_no'] }}</td>
                        <td class="p-2">{{ $asset['longitude'] }}</td>
                        <td class="p-2">{{ $asset['latitude'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach

        <!-- Alarm Settings -->
        <div class="mb-6 border border-gray-300 rounded-lg shadow-sm bg-white p-4">
            <h3 class="font-semibold text-lg mb-4">Current Alarm Setting</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Under Current Alarm (mA)</label>
                    <input type="number" name="under_current" value="{{ old('under_current') }}" 
                           class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Over Current Alarm (mA)</label>
                    <input type="number" name="over_current" value="{{ old('over_current') }}" 
                           class="w-full border rounded px-2 py-1" required>
                </div>
            </div>
        </div>

        <div class="mb-6 border border-gray-300 rounded-lg shadow-sm bg-white p-4">
            <h3 class="font-semibold text-lg mb-4">Voltage Alarm Setting</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Under Voltage Alarm (V)</label>
                    <input type="number" name="under_voltage" value="{{ old('under_voltage') }}" 
                           class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Over Voltage Alarm (V)</label>
                    <input type="number" name="over_voltage" value="{{ old('over_voltage') }}" 
                           class="w-full border rounded px-2 py-1" required>
                </div>
            </div>
        </div>

        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow">
            Program
        </button>
    </form>
</div>

<script>
function toggleSelectAll(index) {
    const master = document.getElementById('selectAll_' + index);
    const checkboxes = document.querySelectorAll('.assetCheckbox_' + index);
    checkboxes.forEach(cb => cb.checked = master.checked);
}
</script>
</x-app-layout>
