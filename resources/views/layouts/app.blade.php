<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LESTARI SOLUTION') }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Axios (for AJAX requests) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col fixed h-full shadow-lg">
            
            <!-- Logo / Title -->
            <div class="p-6 flex items-center justify-center border-b border-gray-700">
                <span class="text-2xl font-bold tracking-wide">MPKJ TEST</span>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 flex-1 space-y-2">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-6 py-3 rounded-md transition 
                          hover:bg-gray-700 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white font-semibold' : '' }}">
                    🏠 <span class="ml-3">Dashboard</span>
                </a>

                <!-- Divider -->
                <div class="px-6 mt-4 mb-2 text-xs uppercase tracking-wide text-gray-400">
                    Monitoring
                </div>

                <!-- Asset List -->
                <a href="{{ route('assets.index') }}" 
                   class="flex items-center px-6 py-3 rounded-md transition 
                          hover:bg-gray-700 hover:text-white {{ request()->routeIs('assets.index') ? 'bg-gray-800 text-white font-semibold' : '' }}">
                    📋 <span class="ml-3">Asset List</span>
                </a>

                <!-- Street Light Status -->
                <a href="{{ route('streetlight.status') }}" 
                   class="flex items-center px-6 py-3 rounded-md transition 
                          hover:bg-gray-700 hover:text-white {{ request()->routeIs('streetlight.status') ? 'bg-gray-800 text-white font-semibold' : '' }}">
                    💡 <span class="ml-3">Street Light Status</span>
                </a>

                <!-- Alarm Status -->
                <a href="{{ route('alarms.index') }}" 
                   class="flex items-center justify-between px-6 py-3 rounded-md transition 
                          hover:bg-gray-700 hover:text-white {{ request()->routeIs('alarms.index') ? 'bg-gray-800 text-white font-semibold' : '' }}">
                    <div class="flex items-center">
                        🚨 <span class="ml-3">Alarm Status</span>
                    </div>
                    <span id="alarm-count" 
                          class="hidden bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        0
                    </span>
                </a>

               <!-- Alarm Configuration -->
                     <a href="{{ route('alarm.configuration') }}" 
                      class="flex items-center px-6 py-3 rounded-md transition 
                        hover:bg-gray-700 hover:text-white {{ request()->routeIs('alarm.configuration') ? 'bg-gray-800 text-white font-semibold' : '' }}">
                      ⚙️ <span class="ml-3">Alarm Configuration</span>
                    </a>


            <!-- Footer -->
            <div class="p-4 border-t border-gray-700 text-sm text-gray-400">
                &copy; {{ date('Y') }} LESTARI SOLUTION
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 ml-64 overflow-auto">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        // Function to fetch alarm count
        function fetchAlarmCount() {
            axios.get("{{ route('alarms.count') }}")
                .then(response => {
                    let count = response.data.count;
                    let badge = document.getElementById("alarm-count");

                    if (count > 0) {
                        badge.innerText = count;
                        badge.classList.remove("hidden");
                    } else {
                        badge.classList.add("hidden");
                    }
                })
                .catch(error => {
                    console.error("Error fetching alarm count:", error);
                });
        }

        // Run immediately and then every 5 seconds
        fetchAlarmCount();
        setInterval(fetchAlarmCount, 5000);
    </script>

</body>
</html>
