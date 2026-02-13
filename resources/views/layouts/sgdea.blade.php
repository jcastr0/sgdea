<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGDEA') }} - @yield('title', 'Sistema de Gestión')</title>

    {{-- Favicon dinámico por tenant --}}
    @if(isset($tenant) && $tenant->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset($tenant->favicon_path) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Google Fonts - Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Estilos compilados --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Variables CSS del tenant/tema - SIEMPRE se inyectan --}}
    <style>
        :root {
            --tenant-primary: {{ $tenantPrimaryColor ?? '#1a56db' }};
            --tenant-secondary: {{ $tenantSecondaryColor ?? '#1e3a5f' }};
            --tenant-primary-hover: {{ $tenantPrimaryColor ?? '#1a56db' }}dd;
        }

        /* Clases de utilidad para usar colores del tenant */
        .bg-tenant-primary { background-color: var(--tenant-primary); }
        .bg-tenant-secondary { background-color: var(--tenant-secondary); }
        .text-tenant-primary { color: var(--tenant-primary); }
        .text-tenant-secondary { color: var(--tenant-secondary); }
        .border-tenant-primary { border-color: var(--tenant-primary); }
        .border-tenant-secondary { border-color: var(--tenant-secondary); }

        /* Estado activo del sidebar usando colores del tenant */
        .sidebar-active {
            background-color: color-mix(in srgb, var(--tenant-primary) 15%, transparent);
            color: var(--tenant-primary);
        }
        .dark .sidebar-active {
            background-color: color-mix(in srgb, var(--tenant-primary) 25%, transparent);
            color: color-mix(in srgb, var(--tenant-primary) 100%, white 30%);
        }
    </style>

    {{-- Estilos adicionales de la página --}}
    @stack('styles')

    {{-- Livewire Styles (si usa Livewire) --}}
    @if($usesLivewire ?? false)
        @livewireStyles
    @endif
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    {{-- Contenedor principal con soporte para sidebar --}}
    <div x-data="sidebar()" class="min-h-screen flex">

        {{-- Overlay para móvil cuando el sidebar está abierto --}}
        <div x-show="isOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="close()"
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden">
        </div>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class="{ 'lg:ml-64': !isCollapsed, 'lg:ml-20': isCollapsed }">

            {{-- Navbar --}}
            @include('partials.navbar')

            {{-- Contenido de la página --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{-- Breadcrumbs --}}
                @hasSection('breadcrumbs')
                    <nav class="mb-6">
                        @yield('breadcrumbs')
                    </nav>
                @endif

                {{-- Alertas flash --}}
                @if(session('success'))
                    <x-alert type="success" class="mb-6" dismissible>
                        {{ session('success') }}
                    </x-alert>
                @endif

                @if(session('error'))
                    <x-alert type="danger" class="mb-6" dismissible>
                        {{ session('error') }}
                    </x-alert>
                @endif

                @if(session('warning'))
                    <x-alert type="warning" class="mb-6" dismissible>
                        {{ session('warning') }}
                    </x-alert>
                @endif

                @if(session('info'))
                    <x-alert type="info" class="mb-6" dismissible>
                        {{ session('info') }}
                    </x-alert>
                @endif

                {{-- Errores de validación --}}
                @if($errors->any())
                    <x-alert type="danger" class="mb-6" dismissible>
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                {{-- Contenido principal --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('partials.footer')
        </div>
    </div>

    {{-- Livewire Scripts (si usa Livewire) --}}
    @if($usesLivewire ?? false)
        @livewireScripts
    @endif

    {{-- Funciones Alpine del layout - SIEMPRE se carga --}}
    <script src="{{ asset('js/sidebar.js') }}"></script>

    {{-- Scripts compilados - NO cargar si usa Livewire (evita doble instancia Alpine) --}}
    @if(!($usesLivewire ?? false))
        <script src="{{ asset('js/app.js') }}" defer></script>
    @endif

    {{-- Scripts adicionales de la página --}}
    @stack('scripts')

    {{-- Toast notifications container --}}
    <div id="toast-container"
         x-data="toast()"
         class="fixed bottom-4 right-4 z-50 space-y-2">
    </div>
</body>
</html>
