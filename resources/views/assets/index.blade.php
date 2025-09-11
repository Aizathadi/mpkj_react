<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Asset Registrations</h1>

        <!-- Register Asset Button -->
        <a href="{{ route('assets.create') }}" 
           class="inline-block mb-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow">
           + Register Asset
        </a>

        <!-- Success message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- Asset List by Site -->
        @foreach($assetsBySite as $siteName => $assets)
            <h2 class="text-xl font-semibold mt-6 mb-2">{{ $siteName }}</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Asset No</th>
                            <th class="px-4 py-2 border">Latitude</th>
                            <th class="px-4 py-2 border">Longitude</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $asset->id }}</td>
                            <td class="px-4 py-2 border">{{ $asset->asset_no }}</td>
                            <td class="px-4 py-2 border">{{ $asset->latitude }}</td>
                            <td class="px-4 py-2 border">{{ $asset->longitude }}</td>
                            <td class="px-4 py-2 border flex gap-2">
                                <a href="{{ route('assets.edit', $asset->id) }}" 
                                   class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow text-sm">
                                   Edit
                                </a>
                                <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded shadow text-sm"
                                            onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-app-layout>
