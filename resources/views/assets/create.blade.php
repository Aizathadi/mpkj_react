<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Register New Asset</h1>

        <form action="{{ route('assets.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Site Name</label>
                <input type="text" name="site_name" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block font-medium">Asset No</label>
                <input type="text" name="asset_no" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block font-medium">Latitude</label>
                <input type="text" name="latitude" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block font-medium">Longitude</label>
                <input type="text" name="longitude" class="w-full border p-2 rounded" required>
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
        </form>
    </div>
</x-app-layout>
