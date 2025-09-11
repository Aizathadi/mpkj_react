<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Asset</h1>

        <form action="{{ route('assets.update', $asset->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Site Name</label>
                <input type="text" name="site_name" class="w-full border p-2 rounded" value="{{ $asset->site_name }}" required>
            </div>

            <div>
                <label class="block font-medium">Asset No</label>
                <input type="text" name="asset_no" class="w-full border p-2 rounded" value="{{ $asset->asset_no }}" required>
            </div>

            <div>
                <label class="block font-medium">Latitude</label>
                <input type="text" name="latitude" class="w-full border p-2 rounded" value="{{ $asset->latitude }}" required>
            </div>

            <div>
                <label class="block font-medium">Longitude</label>
                <input type="text" name="longitude" class="w-full border p-2 rounded" value="{{ $asset->longitude }}" required>
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
        </form>
    </div>
</x-app-layout>
