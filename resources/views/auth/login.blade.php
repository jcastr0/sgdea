<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - {{ $tenant->name ?? config('app.name', 'SGDEA') }}</title>

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

    <style>
        :root {
            --color-primary: {{ $tenantPrimaryColor ?? '#1a56db' }};
            --color-primary-dark: {{ $theme->color_primary_dark ?? '#1657c2' }};
            --color-secondary: {{ $tenantSecondaryColor ?? '#1e3a5f' }};
            --tenant-primary: {{ $tenantPrimaryColor ?? '#1a56db' }};
            --tenant-secondary: {{ $tenantSecondaryColor ?? '#1e3a5f' }};
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Animated gradient background */
        .login-bg {
            background: linear-gradient(-45deg, var(--color-primary), var(--color-secondary), #1e3a5f, var(--color-primary-dark));
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glass morphism card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .dark .login-card {
            background: rgba(30, 41, 59, 0.95);
        }

        /* Floating animation for decorative elements */
        .float-element {
            animation: float 6s ease-in-out infinite;
        }

        .float-element-delayed {
            animation: float 6s ease-in-out infinite;
            animation-delay: -3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Input focus animation */
        .input-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--color-primary);
            transition: all 0.3s ease;
        }

        .input-wrapper:focus-within::after {
            left: 0;
            width: 100%;
        }

        /* Button shine effect */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: left 0.5s ease;
        }

        .btn-shine:hover::before {
            left: 100%;
        }

        /* Checkbox custom style */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .custom-checkbox:checked {
            background: var(--color-primary);
            border-color: var(--color-primary);
        }

        .custom-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .dark .custom-checkbox {
            border-color: #4b5563;
        }

        /* Shake animation for errors */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Fade in animation */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger delay for form elements */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen login-bg flex items-center justify-center p-4 relative overflow-hidden"
         x-data="{
             showPassword: false,
             isLoading: false,
             email: '{{ old('email') }}',
             hasError: {{ $errors->any() ? 'true' : 'false' }}
         }">

        {{-- Decorative floating elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="float-element absolute top-20 left-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="float-element-delayed absolute bottom-20 right-10 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="float-element absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>

            {{-- Geometric shapes --}}
            <div class="float-element absolute top-32 right-1/4 w-20 h-20 border-2 border-white/10 rounded-lg rotate-45"></div>
            <div class="float-element-delayed absolute bottom-1/4 left-1/3 w-16 h-16 border-2 border-white/10 rounded-full"></div>
        </div>

        {{-- Login Card --}}
        <div class="login-card w-full max-w-md rounded-2xl shadow-2xl p-8 md:p-10 relative z-10 fade-in-up mx-auto"
             :class="{ 'shake': hasError }"
             x-init="setTimeout(() => hasError = false, 500)">

            {{-- Logo del Tenant o Sistema --}}
            <div class="text-center mb-8 opacity-0 fade-in-up delay-100" style="animation-fill-mode: forwards;">
                <div class="flex justify-center mb-4">
                    <div class="transition-transform duration-300 hover:scale-105">
                        @if(isset($tenant) && $tenant->logo_path)
                            {{-- Logo del tenant si existe --}}
                            <img src="{{ asset('storage/' . $tenant->logo_path) }}"
                                 alt="{{ $tenant->name ?? 'SGDEA' }}"
                                 class="h-20 w-auto object-contain dark:hidden">
                            {{-- Logo para modo oscuro: usar logo_path_light si existe, si no el mismo logo --}}
                            <img src="{{ asset('storage/' . ($tenant->logo_path_light ?? $tenant->logo_path)) }}"
                                 alt="{{ $tenant->name ?? 'SGDEA' }}"
                                 class="h-20 w-auto object-contain hidden dark:block">
                        @else
                            {{-- Logo del sistema por defecto --}}
                            <img src="{{ asset('images/logo-dark.svg') }}"
                                 alt="SGDEA"
                                 class="h-20 w-auto object-contain dark:hidden">
                            <img src="{{ asset('images/logo-light.svg') }}"
                                 alt="SGDEA"
                                 class="h-20 w-auto object-contain hidden dark:block">
                        @endif
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $tenant->name ?? 'SGDEA' }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    {{ $tenant->description ?? 'Sistema de Gestión Documental y Electrónica' }}
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                    Ingresa tus credenciales para continuar
                </p>
            </div>

            {{-- Mensajes de Error --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 opacity-0 fade-in-up delay-200"
                     style="animation-fill-mode: forwards;"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                        <button @click="show = false" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Mensaje de éxito (para cuando viene de registro o reset password) --}}
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 opacity-0 fade-in-up delay-200"
                     style="animation-fill-mode: forwards;"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-green-600 dark:text-green-400">{{ session('status') }}</p>
                        <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Formulario de Login --}}
            <form method="POST"
                  action="{{ route('auth.login.post') }}"
                  @submit="isLoading = true"
                  class="space-y-5">
                @csrf

                {{-- Campo Email --}}
                <div class="opacity-0 fade-in-up delay-200" style="animation-fill-mode: forwards;">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Correo Electrónico
                    </label>
                    <div class="input-wrapper relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email"
                               id="email"
                               name="email"
                               x-model="email"
                               class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 outline-none @error('email') border-red-500 dark:border-red-500 @enderror"
                               placeholder="tu.email@ejemplo.com"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Campo Contraseña --}}
                <div class="opacity-0 fade-in-up delay-300" style="animation-fill-mode: forwards;">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contraseña
                    </label>
                    <div class="input-wrapper relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'"
                               id="password"
                               name="password"
                               class="w-full pl-11 pr-12 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 outline-none @error('password') border-red-500 dark:border-red-500 @enderror"
                               placeholder="Ingresa tu contraseña"
                               required
                               autocomplete="current-password">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="cursor-pointer absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Recordarme y Olvidé Contraseña --}}
                <div class="flex items-center justify-between opacity-0 fade-in-up delay-400" style="animation-fill-mode: forwards;">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                               name="remember"
                               class="custom-checkbox"
                               {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors select-none">
                            Recordarme
                        </span>
                    </label>

                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium hover:underline transition-colors"
                           style="color: var(--color-primary);">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                {{-- Botón Submit --}}
                <div class="opacity-0 fade-in-up delay-500" style="animation-fill-mode: forwards;">
                    <button type="submit"
                            class="cursor-pointer btn-shine w-full py-3.5 px-4 rounded-xl text-white font-semibold text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));"
                            :disabled="isLoading">
                        {{-- Loading spinner --}}
                        <svg x-show="isLoading"
                             x-cloak
                             class="animate-spin h-5 w-5 text-white"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Iniciando sesión...' : 'Iniciar Sesión'"></span>
                    </button>
                </div>
            </form>

            {{-- Separador y registro (ocultado temporalmente) --}}
            {{--
            @if(Route::has('auth.register'))
                <div class="relative my-6 opacity-0 fade-in-up delay-500" style="animation-fill-mode: forwards;">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-400">
                            ¿Nuevo aquí?
                        </span>
                    </div>
                </div>

                <div class="text-center opacity-0 fade-in-up delay-500" style="animation-fill-mode: forwards;">
                    <a href="{{ route('auth.register') }}"
                       class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Crear una cuenta nueva
                    </a>
                </div>
            @endif
            --}}

            {{-- Toggle Dark Mode --}}
            <div class="mt-6 flex justify-center opacity-0 fade-in-up delay-500" style="animation-fill-mode: forwards;">
                <button @click="$store.darkMode.toggle()"
                        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                        type="button">
                    <template x-if="!$store.darkMode.on">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </template>
                    <template x-if="$store.darkMode.on">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </template>
                    <span x-text="$store.darkMode.on ? 'Modo claro' : 'Modo oscuro'"></span>
                </button>
            </div>
        </div>

        {{-- Footer con copyright --}}
        <div class="absolute bottom-4 left-0 right-0 text-center">
            <p class="text-white/50 text-xs">
                © {{ date('Y') }} {{ $tenant->name ?? config('app.name', 'SGDEA') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>


