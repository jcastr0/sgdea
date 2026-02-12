<!DOCTYPE html>
<html lang="es" x-data x-bind:class="{ 'dark': $store.darkMode.on }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Component Library') - SGDEA</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts (incluye Alpine.js) -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">

    <div x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
    }"
    x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))"
    class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside
            x-show="sidebarOpen"
            x-transition:enter="transition-transform duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            :class="sidebarCollapsed ? 'w-20' : 'w-64'"
            class="fixed inset-y-0 left-0 z-40 flex flex-col bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 transition-all duration-300 shadow-lg lg:shadow-none lg:translate-x-0"
        >
            {{-- Logo y toggle --}}
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-slate-700">
                <a href="/" class="flex items-center gap-3">
                    <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-blue-600 text-white font-bold">
                        SG
                    </div>
                    <span
                        x-show="!sidebarCollapsed"
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
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                >
                    <svg x-show="!sidebarCollapsed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <svg x-show="sidebarCollapsed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Close button (solo móvil) --}}
                <button
                    @click="sidebarOpen = false"
                    class="lg:hidden flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Navegación --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                {{-- Sección: Principal --}}
                <div class="mb-6">
                    <h4
                        x-show="!sidebarCollapsed"
                        class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
                    >
                        Principal
                    </h4>
                    <ul class="space-y-1">
                        <li>
                            <a
                                href="/"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Facturas</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Terceros</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Importar</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Reportes</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Sección: Administración --}}
                <div class="mb-6">
                    <h4
                        x-show="!sidebarCollapsed"
                        class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
                    >
                        Administración
                    </h4>
                    <ul class="space-y-1">
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Usuarios</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Roles</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Configuración</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Sección: Desarrollo --}}
                <div class="mb-6">
                    <h4
                        x-show="!sidebarCollapsed"
                        class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider"
                    >
                        Desarrollo
                    </h4>
                    <ul class="space-y-1">
                        <li>
                            <a
                                href="/components"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                            >
                                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span x-show="!sidebarCollapsed">Componentes</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Sección: Navegación Rápida (Componentes) --}}
                <div class="mb-6" x-show="!sidebarCollapsed">
                    <h4 class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Secciones de Componentes
                    </h4>
                    <ul class="space-y-1 text-xs">
                        <li><a href="#tipografia" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Tipografía</a></li>
                        <li><a href="#colores" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Colores</a></li>
                        <li><a href="#botones" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Botones</a></li>
                        <li><a href="#formularios" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Formularios</a></li>
                        <li><a href="#cards" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Cards</a></li>
                        <li><a href="#modales" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Modales</a></li>
                        <li><a href="#alertas" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Alertas</a></li>
                        <li><a href="#tablas" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Tablas</a></li>
                        <li><a href="#paginacion" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Paginación</a></li>
                        <li><a href="#badges" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Badges</a></li>
                        <li><a href="#loading" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Loading</a></li>
                        <li><a href="#iconos" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">Iconos</a></li>
                    </ul>
                </div>
            </nav>

            {{-- Footer del sidebar --}}
            <div class="border-t border-gray-200 dark:border-slate-700 p-4">
                <div class="flex items-center gap-3" x-show="!sidebarCollapsed">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Modo</span>
                    <button @click="$store.darkMode.toggle()"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            :class="$store.darkMode.on ? 'bg-blue-600' : 'bg-gray-200'">
                        <span class="sr-only">Toggle dark mode</span>
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"
                              :class="$store.darkMode.on ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="$store.darkMode.on ? 'Oscuro' : 'Claro'"></span>
                </div>
                {{-- Toggle modo oscuro colapsado --}}
                <button x-show="sidebarCollapsed" @click="$store.darkMode.toggle()"
                        class="w-full flex justify-center items-center h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg x-show="!$store.darkMode.on" class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.darkMode.on" class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
            </div>
        </aside>

        {{-- Overlay para móvil --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-50"
            x-transition:leave="transition-opacity duration-300"
            x-transition:leave-start="opacity-50"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black opacity-50 lg:hidden"
        ></div>

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
            {{-- Header / Navbar completo --}}
            <header class="bg-white dark:bg-slate-800 shadow-sm border-b border-gray-200 dark:border-slate-700 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        {{-- Botón hamburguesa (móvil) --}}
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                        >
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Búsqueda global --}}
                        <div class="hidden md:flex items-center flex-1 max-w-lg mx-4">
                            <div class="relative w-full">
                                <input
                                    type="text"
                                    placeholder="Buscar facturas, terceros, documentos..."
                                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Acciones header --}}
                        <div class="flex items-center gap-3">
                            {{-- Notificaciones --}}
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    class="relative flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="absolute top-1 right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                                </button>
                                {{-- Dropdown notificaciones --}}
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-2"
                                >
                                    <div class="px-4 py-2 border-b border-gray-200 dark:border-slate-700">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                            <span class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900 dark:text-white">Nueva factura importada</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Hace 5 minutos</p>
                                            </div>
                                        </a>
                                        <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                            <span class="flex-shrink-0 h-8 w-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900 dark:text-white">PDF procesado correctamente</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Hace 15 minutos</p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-200 dark:border-slate-700">
                                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver todas las notificaciones</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Menú de usuario --}}
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <img
                                        src="https://ui-avatars.com/api/?name=Usuario+Demo&background=2767C6&color=fff"
                                        alt="Avatar"
                                        class="h-8 w-8 rounded-full"
                                    >
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium">Usuario Demo</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Administrador</p>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                {{-- Dropdown menú usuario --}}
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-2"
                                >
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Usuario Demo</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">demo@sgdea.com</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Mi Perfil
                                        </a>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                            Cambiar Contraseña
                                        </a>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Configuración
                                        </a>
                                    </div>
                                    <div class="py-1 border-t border-gray-200 dark:border-slate-700">
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Cerrar Sesión
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Breadcrumbs --}}
                <div class="bg-gray-50 dark:bg-slate-900/50 border-t border-gray-200 dark:border-slate-700 px-4 sm:px-6 lg:px-8 py-2">
                    <nav class="flex items-center text-sm">
                        <a href="/" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">Desarrollo</span>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-900 dark:text-white font-medium">Componentes</span>
                    </nav>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        SGDEA Component Library - Solo para desarrollo
                    </p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
