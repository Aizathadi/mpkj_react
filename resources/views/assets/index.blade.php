<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-8 min-h-screen">

        {{-- 🔹 Page Heading --}}
        <h1 class="text-center text-3xl font-bold text-white mb-6">
             Asset Registrations
        </h1>

        {{-- 🔹 Register Asset Button --}}
        <div class="text-left mb-6">
            <a href="{{ route('assets.create') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105">
               + Register Asset
            </a>
        </div>

        {{-- ✅ Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-700/40 text-green-200 border border-green-600 rounded-lg shadow-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- 🔹 Assets by Site --}}
        @foreach($assetsBySite as $siteName => $assets)
            <div class="bg-gray-900/70 border border-gray-700 rounded-lg p-6 shadow-lg backdrop-blur-md site-block">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-blue-300">{{ $siteName }}</h2>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-700 shadow-inner">
                    <table class="min-w-full text-sm text-gray-200 border-collapse">
                        <thead class="bg-gray-800/80 border-b border-gray-700">
                            <tr class="text-gray-300 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 border-r border-gray-700">ID</th>
                                <th class="px-4 py-3 border-r border-gray-700">Asset No</th>
                                <th class="px-4 py-3 border-r border-gray-700">Latitude</th>
                                <th class="px-4 py-3 border-r border-gray-700">Longitude</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900/50">
                            @foreach($assets as $asset)
                                <tr class="hover:bg-gray-700/40 transition duration-200">
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">{{ $asset->id }}</td>
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">{{ $asset->asset_no }}</td>
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">{{ $asset->latitude }}</td>
                                    <td class="px-4 py-2 border-t border-gray-800 text-center">{{ $asset->longitude }}</td>
                                    <td class="px-4 py-2 border-t border-gray-800 flex justify-center gap-3">

                                     {{-- Location Button --}}
                                        <a href="{{ route('dashboard') }}?lat={{ $asset->latitude }}&lng={{ $asset->longitude }}&asset_no={{ $asset->asset_no }}" 
                                           class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-md shadow-md font-semibold text-xs transition transform hover:scale-105">
                                           Location
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('assets.edit', $asset->id) }}" 
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-400 text-gray-900 rounded-md shadow-md font-semibold text-xs transition transform hover:scale-105">
                                           Edit
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="px-3 py-1 bg-red-600 hover:bg-red-500 text-white rounded-md shadow-md font-semibold text-xs transition transform hover:scale-105"
                                                onclick="return confirm('Are you sure you want to delete this asset?')">
                                                Delete
                                            </button>
                                        </form>

                                       

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 🌌 Theme & Styling --}}
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
