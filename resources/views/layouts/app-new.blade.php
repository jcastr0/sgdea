<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SGDEA') }} - @yield('title', 'Dashboard')</title>

    {{-- Favicon dinámico --}}
    @if(isset($tenant) && $tenant->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset($tenant->favicon_path) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Colores del tema dinámicos --}}
    <style>
        :root {
            --tenant-primary: {{ $theme->color_primary ?? '#3b82f6' }};
            --tenant-primary-dark: {{ $theme->color_primary_dark ?? '#2563eb' }};
            --tenant-primary-light: {{ $theme->color_primary_light ?? '#60a5fa' }};
            --tenant-secondary: {{ $theme->color_secondary ?? '#0f172a' }};
        }
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gray-50"
      x-data="{
          sidebarOpen: window.innerWidth >= 1024,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
          mobileSidebarOpen: false,
          userMenuOpen: false,
          notificationsOpen: false,
          toggleCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
          }
      }">

    <div class="min-h-full">
        {{-- Mobile sidebar overlay --}}
        <div x-show="mobileSidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"
             @click="mobileSidebarOpen = false"
             x-cloak>
        </div>

        {{-- Sidebar Mobile --}}
        <div x-show="mobileSidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-72 lg:hidden"
             x-cloak>
            @include('partials.sidebar-content', ['mobile' => true])
        </div>

        {{-- Sidebar Desktop --}}
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:flex-col transition-all duration-300"
             :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'">
            @include('partials.sidebar-content', ['mobile' => false])
        </div>

        {{-- Main Content Area --}}
        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'">
            {{-- Top Navbar --}}
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    {{-- Left side --}}
                    <div class="flex items-center gap-4">
                        {{-- Mobile menu button --}}
                        <button @click="mobileSidebarOpen = true"
                                class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Collapse button (desktop) --}}
                        <button @click="toggleCollapse()"
                                class="hidden lg:flex p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </button>

                        {{-- Breadcrumb --}}
                        <nav class="hidden sm:flex items-center text-sm">
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </a>
                            @yield('breadcrumb')
                        </nav>
                    </div>

                    {{-- Right side --}}
                    <div class="flex items-center gap-3">
                        {{-- Search (optional) --}}
                        <div class="hidden md:block">
                            <div class="relative">
                                <input type="text"
                                       placeholder="Buscar..."
                                       class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50
                                              focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                              transition-all">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Notifications --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                {{-- Badge de notificación --}}
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            {{-- Dropdown de notificaciones --}}
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 py-2"
                                 x-cloak>
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <p class="px-4 py-8 text-center text-gray-500 text-sm">
                                        No hay notificaciones nuevas
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-3 p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-medium text-sm"
                                     style="background: linear-gradient(135deg, var(--tenant-primary), var(--tenant-secondary));">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->roles->first()->name ?? 'Sin rol' }}</p>
                                </div>
                                <svg class="hidden sm:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- User Dropdown --}}
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2"
                                 x-cloak>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Mi Perfil
                                </a>
                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Configuración
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form action="{{ route('auth.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- Page Content --}}
            <main class="py-6 px-4 sm:px-6 lg:px-8">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 animate-slide-in-right"
                         x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="text-green-500 hover:text-green-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-slide-in-right"
                         x-data="{ show: true }"
                         x-show="show">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Page Content --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 bg-white mt-auto">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                        <p>© {{ date('Y') }} {{ session('tenant_name', config('app.name', 'SGDEA')) }}. Todos los derechos reservados.</p>
                        <p>Versión 1.0.0</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
