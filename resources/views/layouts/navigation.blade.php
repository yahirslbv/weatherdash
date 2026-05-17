<nav x-data="{ open: false }" class="bg-[#15203D] border-b border-[#1E2D56] shadow-md relative z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-extrabold text-white tracking-tight">
                        Weather<span class="text-blue-500">Dash</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:text-white hover:border-[#1E2D56]' }}">
                        {{ __('Dashboard') }}
                    </a>
                    
                    <a href="{{ route('forecast') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('forecast') ? 'border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:text-white hover:border-[#1E2D56]' }}">
                        {{ __('Pronóstico') }}
                    </a>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:text-white hover:border-[#1E2D56]' }}">
                        {{ __('Mis Lugares') }}
                    </a>

                    <a href="{{ route('settings') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('settings') ? 'border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:text-white hover:border-[#1E2D56]' }}">
                        {{ __('Configuración') }}
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                    <div @click="open = ! open">
                        <button class="inline-flex items-center px-3 py-2 border border-[#1E2D56] text-sm leading-4 font-bold rounded-xl text-white bg-[#0B132B]/50 hover:bg-[#0B132B] focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2 text-[#829AB1]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-48 rounded-xl shadow-xl bg-[#15203D] border border-[#1E2D56] py-1" style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-200 hover:bg-[#0B132B] hover:text-white transition duration-150 ease-in-out">
                            {{ __('Perfil') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-red-400 hover:bg-[#0B132B] hover:text-red-300 transition duration-150 ease-in-out cursor-pointer">
                                {{ __('Cerrar Sesión') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-[#829AB1] hover:text-white hover:bg-[#0B132B] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#15203D] border-t border-[#1E2D56]">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block w-full pl-3 pr-4 py-2 border-l-4 text-base font-bold transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'bg-[#0B132B] border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:bg-[#0B132B] hover:text-white' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('forecast') }}" class="block w-full pl-3 pr-4 py-2 border-l-4 text-base font-bold transition duration-150 ease-in-out {{ request()->routeIs('forecast') ? 'bg-[#0B132B] border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:bg-[#0B132B] hover:text-white' }}">
                {{ __('Pronóstico') }}
            </a>
            <a href="{{ route('dashboard') }}" class="block w-full pl-3 pr-4 py-2 border-l-4 text-base font-bold transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-[#0B132B] border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:bg-[#0B132B] hover:text-white' }}">
                {{ __('Mis Lugares') }}
            </a>
            <a href="{{ route('settings') }}" class="block w-full pl-3 pr-4 py-2 border-l-4 text-base font-bold transition duration-150 ease-in-out {{ request()->routeIs('settings') ? 'bg-[#0B132B] border-blue-500 text-white' : 'border-transparent text-[#829AB1] hover:bg-[#0B132B] hover:text-white' }}">
                {{ __('Configuración') }}
            </a>
        </div>

        <div class="pt-4 pb-1 border-t border-[#1E2D56] bg-[#0B132B]/40">
            <div class="px-4">
                <div class="font-bold text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-[#829AB1]">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block w-full pl-3 pr-4 py-2 text-base font-semibold text-[#829AB1] hover:text-white hover:bg-[#0B132B]">
                    {{ __('Perfil') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full pl-3 pr-4 py-2 text-base font-semibold text-red-400 hover:text-red-300 hover:bg-[#0B132B] text-left w-full">
                        {{ __('Cerrar Sesión') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>