{{-- Sidebar responsive con soporte para dark mode --}}
<aside
    x-show="isOpen || window.innerWidth >= 1024"
    :class="{
        'translate-x-0': isOpen,
        '-translate-x-full lg:translate-x-0': !isOpen,
        'w-64': !isCollapsed,
        'w-20': isCollapsed
    }"
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 transition-all duration-300 shadow-lg lg:shadow-none"
>
    {{-- Logo y toggle --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-slate-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            @if(isset($tenant) && $tenant->logo_path)
                <img src="{{ asset($tenant->logo_path) }}" alt="Logo" class="h-8 w-auto">
            @else
                <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-blue-600 text-white font-bold">
                    S
                </div>
            @endif
            <span
                x-show="!isCollapsed"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="text-lg font-semibold text-gray-900 dark:text-white"
            >
                SGDEA
            </span>
        </a>

        {{-- Toggle collapse (solo desktop) --}}
        <button
            @click="toggle()"
            class="hidden lg:flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
        >
            <svg x-show="!isCollapsed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="isCollapsed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Close button (solo móvil) --}}
        <button
            @click="close()"
            class="lg:hidden flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        {{-- Sección: Operativo --}}
        <div class="mb-6">
            <h4
                x-show="!isCollapsed"
                class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
            >
                Operativo
            </h4>
            <ul class="space-y-1">
                @canAccess('facturas.view')
                <li>
                    <a
                        href="{{ route('facturas.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('facturas.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Facturas' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="!isCollapsed">Facturas</span>
                    </a>
                </li>
                @endcanAccess

                @canAccess('terceros.view')
                <li>
                    <a
                        href="{{ route('terceros.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('terceros.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Terceros' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-show="!isCollapsed">Terceros</span>
                    </a>
                </li>
                @endcanAccess

                @canAccessAny('importaciones.view', 'importaciones.execute')
                <li>
                    <a
                        href="{{ route('importaciones.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('importaciones.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Importaciones' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span x-show="!isCollapsed">Importaciones</span>
                    </a>
                </li>
                @endcanAccessAny
            </ul>
        </div>

        {{-- Sección: Análisis --}}
        <div class="mb-6">
            <h4
                x-show="!isCollapsed"
                class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
            >
                Análisis
            </h4>
            <ul class="space-y-1">
                <li>
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('dashboard')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Dashboard' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-show="!isCollapsed">Dashboard</span>
                    </a>
                </li>

                @canAccess('auditoria.view')
                <li>
                    <a
                        href="{{ route('admin.auditoria.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('admin.auditoria.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Auditoría' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span x-show="!isCollapsed">Auditoría</span>
                    </a>
                </li>
                @endcanAccess
            </ul>
        </div>

        {{-- Sección: Administración --}}
        <div class="mb-6">
            <h4
                x-show="!isCollapsed"
                class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
            >
                Administración
            </h4>
            <ul class="space-y-1">
                @canAccess('usuarios.manage')
                <li>
                    <a
                        href="{{ route('admin.usuarios.pendientes') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('admin.usuarios.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Usuarios' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span x-show="!isCollapsed">Usuarios</span>
                    </a>
                </li>
                @endcanAccess

                @if(Route::has('config.edit'))
                @canAccess('configuracion.edit')
                <li>
                    <a
                        href="{{ route('config.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('config.*')
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Configuración' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="!isCollapsed">Configuración</span>
                    </a>
                </li>
                @endcanAccess
                @endif
            </ul>
        </div>

        {{-- Sección: Superadmin Global (solo para superadmins) --}}
        @if(auth()->user() && auth()->user()->role && auth()->user()->role->slug === 'superadmin_global')
        <div class="mb-6">
            <h4
                x-show="!isCollapsed"
                class="px-3 mb-2 text-xs font-semibold text-red-400 dark:text-red-500 uppercase tracking-wider"
            >
                Superadmin
            </h4>
            <ul class="space-y-1">
                <li>
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('admin.dashboard')
                                ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Panel Global' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="!isCollapsed">Panel Global</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.tenants.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                            {{ request()->routeIs('admin.tenants.*')
                                ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}"
                        x-tooltip="isCollapsed ? 'Tenants' : null"
                    >
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-show="!isCollapsed">Tenants</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif
    </nav>

    {{-- User info en el footer del sidebar --}}
    <div class="border-t border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>
            <div x-show="!isCollapsed" class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ Auth::user()->name ?? 'Usuario' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ Auth::user()->email ?? '' }}
                </p>
            </div>
        </div>
    </div>
</aside>
