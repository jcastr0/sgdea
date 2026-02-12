{{-- Navbar principal con soporte para dark mode --}}
<header class="sticky top-0 z-30 h-16 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 shadow-sm">
    <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        {{-- Lado izquierdo: Toggle y búsqueda --}}
        <div class="flex items-center gap-4">
            {{-- Botón hamburger para móvil --}}
            <button
                @click="open()"
                class="lg:hidden flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
            >
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Título de la página / Breadcrumb --}}
            <div class="hidden sm:block">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
        </div>

        {{-- Lado derecho: Acciones --}}
        <div class="flex items-center gap-2 sm:gap-4">
            {{-- Búsqueda global (solo desktop) --}}
            <div class="hidden md:block relative">
                <input
                    type="search"
                    placeholder="Buscar..."
                    class="w-64 pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Toggle dark mode --}}
            <button
                @click="darkMode = !darkMode"
                class="flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                title="Cambiar tema"
            >
                {{-- Ícono sol (light mode) --}}
                <svg x-show="darkMode" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{-- Ícono luna (dark mode) --}}
                <svg x-show="!darkMode" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- Notificaciones --}}
            <div x-data="dropdown()" class="relative">
                <button
                    @click="toggle()"
                    class="relative flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{-- Badge de notificaciones --}}
                    <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500"></span>
                </button>

                {{-- Dropdown de notificaciones --}}
                <div
                    x-show="isOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    @click.away="close()"
                    class="absolute right-0 mt-2 w-80 rounded-xl bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                >
                    <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <div class="p-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                            No hay notificaciones nuevas
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selector de tenant (si hay múltiples) --}}
            @if(isset($userTenants) && count($userTenants) > 1)
            <div x-data="dropdown()" class="relative hidden sm:block">
                <button
                    @click="toggle()"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                >
                    <span class="max-w-32 truncate">{{ $tenant->nombre ?? 'Empresa' }}</span>
                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div
                    x-show="isOpen"
                    x-transition
                    @click.away="close()"
                    class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                >
                    <div class="py-2">
                        @foreach($userTenants as $t)
                        <a
                            href="{{ route('auth.switch-tenant', $t->id) }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ $t->id === $tenant->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                        >
                            {{ $t->nombre }}
                            @if($t->id === $tenant->id)
                            <svg class="ml-auto h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Menú de usuario --}}
            <div x-data="dropdown()" class="relative">
                <button
                    @click="toggle()"
                    class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                >
                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-32 truncate">
                        {{ Auth::user()->name ?? 'Usuario' }}
                    </span>
                    <svg class="hidden sm:block h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown de usuario --}}
                <div
                    x-show="isOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    @click.away="close()"
                    class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                >
                    <div class="p-3 border-b border-gray-200 dark:border-slate-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <div class="py-2">
                        <a href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mi Perfil
                        </a>
                        @canAccess('config.editar')
                        <a href="{{ route('configuration.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Configuración
                        </a>
                        @endcanAccess
                    </div>
                    <div class="py-2 border-t border-gray-200 dark:border-slate-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
