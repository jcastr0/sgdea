<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ $tenant->name ?? config('app.name', 'SGDEA') }}</title>

    {{-- Favicon dinámico --}}
    @if(isset($tenant) && $tenant->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset($tenant->favicon_path) }}">
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
    </style>
</head>
<body class="font-sans antialiased" x-data="{ showPassword: false }">
    {{-- Background con gradiente y patrón --}}
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden"
         style="background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-secondary) 100%);">

        {{-- Elementos decorativos de fondo --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            {{-- Círculos decorativos --}}
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-2xl animate-float"></div>

            {{-- Grid pattern --}}
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.3) 1px, transparent 0);
                        background-size: 40px 40px;">
            </div>
        </div>

        {{-- Container principal --}}
        <div class="relative z-10 w-full max-w-md px-4 animate-fade-in">
            {{-- Card de login con glassmorphism --}}
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-8 space-y-6">

                {{-- Header con logo --}}
                <div class="text-center space-y-4">
                    {{-- Logo --}}
                    <div class="flex justify-center">
                        @if(isset($logo) && $logo)
                            <img src="{{ asset($logo) }}"
                                 alt="{{ $tenant->name ?? 'Logo' }}"
                                 class="h-16 w-auto object-contain animate-float"
                                 style="animation-duration: 4s;">
                        @else
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg"
                                 style="background: linear-gradient(135deg, var(--tenant-primary), var(--tenant-secondary));">
                                {{ strtoupper(substr($tenant->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Nombre de empresa --}}
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $tenant->name ?? 'SGDEA' }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Sistema de Gestión Documental
                        </p>
                    </div>
                </div>

                {{-- Mensajes de error --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-slide-in-right">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Session messages --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 animate-slide-in-right">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Formulario --}}
                <form method="POST" action="{{ route('auth.login.post') }}" class="space-y-5">
                    @csrf

                    {{-- Campo Email --}}
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50
                                          text-gray-900 placeholder-gray-400
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white
                                          transition-all duration-200
                                          @error('email') border-red-500 @enderror"
                                   placeholder="tu.email@ejemplo.com"
                                   required
                                   autocomplete="email"
                                   autofocus>
                        </div>
                    </div>

                    {{-- Campo Contraseña --}}
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   class="block w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl bg-gray-50/50
                                          text-gray-900 placeholder-gray-400
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white
                                          transition-all duration-200
                                          @error('password') border-red-500 @enderror"
                                   placeholder="••••••••"
                                   required
                                   autocomplete="current-password">
                            {{-- Toggle password visibility --}}
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Recordarme y olvidé contraseña --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox"
                                   name="remember"
                                   {{ old('remember') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600
                                          focus:ring-blue-500 focus:ring-offset-0
                                          transition-colors cursor-pointer">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                                Recuérdame
                            </span>
                        </label>
                        {{-- <a href="#" class="text-sm font-medium hover:underline transition-colors"
                           style="color: var(--tenant-primary);">
                            ¿Olvidaste tu contraseña?
                        </a> --}}
                    </div>

                    {{-- Botón Submit --}}
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl font-semibold text-white shadow-lg
                                   transform transition-all duration-200
                                   hover:shadow-xl hover:scale-[1.02] hover:brightness-110
                                   active:scale-[0.98]
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            style="background: linear-gradient(135deg, var(--tenant-primary), var(--tenant-primary-dark));">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Iniciar Sesión
                        </span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">¿Nuevo usuario?</span>
                    </div>
                </div>

                {{-- Link a registro --}}
                <a href="{{ route('auth.register') }}"
                   class="block w-full py-3 px-4 rounded-xl font-medium text-center
                          border-2 transition-all duration-200
                          hover:shadow-md hover:scale-[1.01]"
                   style="border-color: var(--tenant-primary); color: var(--tenant-primary);">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Crear una cuenta
                    </span>
                </a>
            </div>

            {{-- Footer --}}
            <p class="text-center text-white/60 text-sm mt-6">
                © {{ date('Y') }} {{ $tenant->name ?? config('app.name', 'SGDEA') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>

    {{-- Style para x-cloak --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
