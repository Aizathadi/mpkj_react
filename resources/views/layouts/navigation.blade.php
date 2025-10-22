<!-- ======================================== -->
<!-- 🔵 FUTURISTIC NAVBAR WITH NEON EFFECT 🔵 -->
<!-- ======================================== -->
<nav x-data="{ open: false }" class="bg-[#0f172a]/95 border-b border-blue-900/30 shadow-lg backdrop-blur-md relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative flex items-center h-20">

            <!-- Left Spacer -->
            <div class="flex-1"></div>

            <!-- Center Title -->
            <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
                <a href="{{ route('dashboard') }}" 
                   class="text-cyan font-extrabold text-xl sm:text-2xl text-center tracking-wide select-none 
                          drop-shadow-[0_0_8px_rgba(0,150,255,0.6)]">
                    MPKj STREETLIGHT COMMAND CENTRE
                </a>
            </div>

            <!-- Right: User Dropdown -->
            <div class="flex-1 flex justify-end items-center relative z-20" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-md text-white 
                           hover:text-cyan-300 focus:outline-none transition-all duration-200">
                    <span>{{ Auth::user()->name }}</span>
                    <svg class="ml-1 h-4 w-4 fill-current transform transition-transform duration-200"
                         :class="{ 'rotate-180': openMenu }"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="openMenu"
                     @click.away="openMenu = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     class="absolute right-0 mt-2 w-52 rounded-xl bg-[#0b1323]/95 border border-blue-800/50 
                            shadow-[0_0_15px_rgba(0,150,255,0.25)] ring-1 ring-blue-500/20 
                            backdrop-blur-md text-gray-200 z-50 overflow-hidden">
                    
                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm hover:text-cyan-300 hover:bg-blue-900/20 transition-all">
                        👤 Profile
                    </a>

                    <div class="border-t border-blue-800/30 my-1"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm hover:text-red-400 hover:bg-blue-900/20 
                                   transition-all">
                            🔓 Log Out
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hamburger Menu (Mobile) -->
            <div class="flex items-center sm:hidden absolute right-4 z-10">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-white 
                               hover:text-blue-400 hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" 
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" 
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': !open}" 
         class="hidden sm:hidden bg-[#0f172a] border-t border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="text-white font-semibold text-lg hover:text-blue-400 text-center">
                MPKj STREETLIGHTING SCADA SYSTEM
            </a>
        </div>

        <!-- Responsive User Settings -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:text-blue-400">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" 
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-white hover:text-blue-400">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- ✅ Add AlpineJS CDN (no npm run dev needed) -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
