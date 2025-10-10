<x-app-layout>
    <div class="p-6 max-w-3xl min-h-screen space-y-8 ml-10">

        {{-- 🔹 Page Heading --}}
        <h1 class="text-3xl font-bold text-white text-left mb-6">
            Edit Asset
        </h1>

        {{-- 🔹 Edit Form --}}
        <div class="bg-gray-900/70 border border-gray-700 rounded-lg p-8 shadow-lg backdrop-blur-md transition hover:border-blue-500/50 hover:shadow-blue-500/20 w-full">
            <form action="{{ route('assets.update', $asset->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Site Name --}}
                <div>
                    <label class="block text-blue-300 font-semibold mb-2">Site Name</label>
                    <input type="text" name="site_name" 
                           class="w-full bg-gray-800/80 text-gray-100 border border-gray-700 p-3 rounded-lg 
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-inner" 
                           value="{{ $asset->site_name }}" required>
                </div>

                {{-- Asset No --}}
                <div>
                    <label class="block text-blue-300 font-semibold mb-2">Asset No</label>
                    <input type="text" name="asset_no" 
                           class="w-full bg-gray-800/80 text-gray-100 border border-gray-700 p-3 rounded-lg 
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-inner" 
                           value="{{ $asset->asset_no }}" required>
                </div>

                {{-- Latitude & Longitude side by side --}}
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-blue-300 font-semibold mb-2">Latitude</label>
                        <input type="text" name="latitude" 
                               class="w-full bg-gray-800/80 text-gray-100 border border-gray-700 p-3 rounded-lg 
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-inner" 
                               value="{{ $asset->latitude }}" required>
                    </div>

                    <div>
                        <label class="block text-blue-300 font-semibold mb-2">Longitude</label>
                        <input type="text" name="longitude" 
                               class="w-full bg-gray-800/80 text-gray-100 border border-gray-700 p-3 rounded-lg 
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-inner" 
                               value="{{ $asset->longitude }}" required>
                    </div>
                </div>

                {{-- 🔘 Buttons --}}
                <div class="flex justify-between pt-4">
                    <a href="{{ route('assets.index') }}" 
                       class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg shadow-md 
                              font-semibold transition transform hover:scale-105">
                        ← Back
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg shadow-md 
                                   font-semibold transition transform hover:scale-105">
                        💾 Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🌌 Theme Styles --}}
    <style>
        body {
            background: radial-gradient(circle at top, #0f172a, #020617);
            color: #e2e8f0;
        }

        .bg-gray-900\/70 {
            transition: all 0.3s ease-in-out;
        }

        .bg-gray-900\/70:hover {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.35);
            border-color: rgba(59, 130, 246, 0.5);
        }

        input {
            transition: all 0.2s ease-in-out;
        }

        input:focus {
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
            border-color: rgba(59, 130, 246, 0.6);
        }
    </style>
</x-app-layout>
