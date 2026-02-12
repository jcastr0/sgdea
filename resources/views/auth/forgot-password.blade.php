<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Recuperar Contraseña - {{ config('app.name', 'SGDEA') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Fonts - Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Estilos compilados --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        :root {
            --color-primary: #2563eb;
            --color-primary-dark: #1d4ed8;
            --color-secondary: #0f172a;
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

        /* Progress indicator */
        .progress-step {
            transition: all 0.3s ease;
        }

        .progress-step.active {
            background: var(--color-primary);
            color: white;
        }

        .progress-step.completed {
            background: #10b981;
            color: white;
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
             isLoading: false,
             email: '{{ old('email') }}',
             hasError: {{ $errors->any() ? 'true' : 'false' }},
             emailSent: {{ session('status') ? 'true' : 'false' }}
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

        {{-- Card Principal --}}
        <div class="login-card w-full max-w-md rounded-2xl shadow-2xl p-8 md:p-10 relative z-10 fade-in-up mx-auto"
             :class="{ 'shake': hasError }"
             x-init="setTimeout(() => hasError = false, 500)">

            {{-- Progress Indicator --}}
            <div class="flex justify-center mb-8 opacity-0 fade-in-up delay-100" style="animation-fill-mode: forwards;">
                <div class="flex items-center gap-3">
                    <div class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold"
                         :class="emailSent ? 'completed' : 'active'">
                        <template x-if="emailSent">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="!emailSent">
                            <span>1</span>
                        </template>
                    </div>
                    <div class="w-12 h-1 rounded-full transition-colors" :class="emailSent ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'"></div>
                    <div class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        2
                    </div>
                    <div class="w-12 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                    <div class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        3
                    </div>
                </div>
            </div>

            {{-- Logo del Sistema --}}
            <div class="text-center mb-8 opacity-0 fade-in-up delay-100" style="animation-fill-mode: forwards;">
                <div class="flex justify-center mb-4">
                    <div class="transition-transform duration-300 hover:scale-105">
                        {{-- Logo para modo claro --}}
                        <img src="{{ asset('images/logo-dark.svg') }}"
                             alt="SGDEA"
                             class="h-16 w-auto object-contain dark:hidden">
                        {{-- Logo para modo oscuro --}}
                        <img src="{{ asset('images/logo-light.svg') }}"
                             alt="SGDEA"
                             class="h-16 w-auto object-contain hidden dark:block">
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    Recuperar Contraseña
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    <template x-if="!emailSent">
                        <span>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</span>
                    </template>
                    <template x-if="emailSent">
                        <span>Hemos enviado el enlace de recuperación a tu correo.</span>
                    </template>
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

            {{-- Mensaje de éxito --}}
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 opacity-0 fade-in-up delay-200"
                     style="animation-fill-mode: forwards;">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">{{ session('status') }}</p>
                            <p class="text-xs text-green-500 dark:text-green-500 mt-1">
                                Revisa tu bandeja de entrada y la carpeta de spam.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST"
                  action="{{ route('password.email') }}"
                  @submit="isLoading = true"
                  class="space-y-5"
                  x-show="!emailSent"
                  x-transition>
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

                {{-- Botón Submit --}}
                <div class="opacity-0 fade-in-up delay-300" style="animation-fill-mode: forwards;">
                    <button type="submit"
                            class="btn-shine w-full py-3.5 px-4 rounded-xl text-white font-semibold text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
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
                        <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="isLoading ? 'Enviando...' : 'Enviar Enlace de Recuperación'"></span>
                    </button>
                </div>
            </form>

            {{-- Acciones después de enviar email --}}
            <div x-show="emailSent" x-cloak class="space-y-4">
                <div class="text-center opacity-0 fade-in-up delay-200" style="animation-fill-mode: forwards;">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Si el correo existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.
                    </p>
                </div>

                {{-- Reenviar enlace --}}
                <form method="POST" action="{{ route('password.email') }}" class="opacity-0 fade-in-up delay-300" style="animation-fill-mode: forwards;">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email') }}">
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reenviar enlace
                    </button>
                </form>
            </div>

            {{-- Separador --}}
            <div class="relative my-6 opacity-0 fade-in-up delay-400" style="animation-fill-mode: forwards;">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-400">
                        ¿Recordaste tu contraseña?
                    </span>
                </div>
            </div>

            {{-- Link a Login --}}
            <div class="text-center opacity-0 fade-in-up delay-500" style="animation-fill-mode: forwards;">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al inicio de sesión
                </a>
            </div>

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
                © {{ date('Y') }} {{ config('app.name', 'SGDEA') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
