<nav x-data="{ open: false }" class="bg-[#0f172a] border-b border-gray-700 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative flex items-center h-20">

            <!-- Left: Empty space to center title -->
            <div class="flex-1"></div>

            <!-- Center: Dashboard Title -->
            <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
                <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl sm:text-2xl text-center select-none">
                    MPKj STREETLIGHTING SCADA SYSTEM
                </a>
            </div>

            <!-- Right: User Dropdown -->
            <div class="flex-1 flex justify-end items-center relative z-10">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-blue-400 focus:outline-none transition ease-in-out duration-150">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-white hover:text-blue-400">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" 
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-white hover:text-blue-400">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Menu -->
            <div class="flex items-center sm:hidden absolute right-4 z-10">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-blue-400 hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-[#0f172a] border-t border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="text-white font-semibold text-lg hover:text-blue-400 text-center">
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
