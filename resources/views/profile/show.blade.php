@extends('layouts.sgdea')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('dashboard')],
    ['label' => 'Mi Perfil'],
]" />
@endsection

@section('content')
<div x-data="{
    activeTab: 'info',
    showDeleteModal: false
}">
    {{-- Header del perfil --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-32 relative">
            <div class="absolute -bottom-16 left-6">
                <div class="w-32 h-32 rounded-full border-4 border-white dark:border-slate-800 bg-blue-100 dark:bg-blue-900 flex items-center justify-center shadow-lg">
                    <span class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="pt-20 pb-6 px-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if($user->role)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            {{ $user->role->name }}
                        </span>
                        @endif
                        @if($user->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            Activo
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                            {{ ucfirst($user->status ?? 'Pendiente') }}
                        </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs de navegación --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-slate-700">
            <nav class="flex gap-4">
                <button @click="activeTab = 'info'"
                        :class="activeTab === 'info' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors">
                    Información Personal
                </button>
                <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors">
                    Seguridad
                </button>
                <button @click="activeTab = 'preferences'"
                        :class="activeTab === 'preferences' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors">
                    Preferencias
                </button>
                <button @click="activeTab = 'activity'"
                        :class="activeTab === 'activity' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors">
                    Actividad
                </button>
            </nav>
        </div>
    </div>

    {{-- Tab: Información Personal --}}
    <div x-show="activeTab === 'info'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Información Personal</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nombre completo</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Correo electrónico</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Teléfono</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->phone ?? 'No especificado' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Departamento</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->department ?? 'No especificado' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Rol</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->role->name ?? 'Sin rol asignado' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Empresa</label>
                    <p class="text-gray-900 dark:text-white">{{ $user->tenant->name ?? 'No asignada' }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Información de la cuenta</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de registro</label>
                        <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Último acceso</label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Seguridad --}}
    <div x-show="activeTab === 'security'" x-transition>
        <div class="space-y-6">
            {{-- Cambiar contraseña --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cambiar Contraseña</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Actualiza tu contraseña para mantener tu cuenta segura.</p>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña actual</label>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva contraseña</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números.</p>
                        @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar nueva contraseña</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sesiones activas --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sesiones Activas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Información sobre tu sesión actual.</p>

                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Sesión actual</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            IP: {{ $user->last_login_ip ?? request()->ip() }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Último acceso: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Ahora' }}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full">
                        Activa
                    </span>
                </div>
            </div>

            {{-- Eliminar cuenta --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                <h2 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">Zona de Peligro</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Una vez eliminada tu cuenta, no hay vuelta atrás. Por favor, asegúrate de que esto es lo que deseas.</p>

                <button @click="showDeleteModal = true"
                        class="cursor-pointer px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Eliminar mi cuenta
                </button>
            </div>
        </div>
    </div>

    {{-- Tab: Preferencias --}}
    <div x-show="activeTab === 'preferences'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Preferencias</h2>

            <form action="{{ route('profile.preferences') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Tema --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tema de la aplicación</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="light" class="sr-only peer" {{ ($preferences['theme'] ?? 'system') === 'light' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-gray-200 dark:border-slate-600 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-colors text-center">
                                <svg class="w-6 h-6 mx-auto text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Claro</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="dark" class="sr-only peer" {{ ($preferences['theme'] ?? 'system') === 'dark' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-gray-200 dark:border-slate-600 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-colors text-center">
                                <svg class="w-6 h-6 mx-auto text-slate-600 dark:text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Oscuro</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="system" class="sr-only peer" {{ ($preferences['theme'] ?? 'system') === 'system' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-gray-200 dark:border-slate-600 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-colors text-center">
                                <svg class="w-6 h-6 mx-auto text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Sistema</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Idioma --}}
                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Idioma</label>
                    <select name="language" id="language" class="w-full md:w-64 px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="es" {{ ($preferences['language'] ?? 'es') === 'es' ? 'selected' : '' }}>Español</option>
                        <option value="en" {{ ($preferences['language'] ?? 'es') === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>

                {{-- Notificaciones --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Notificaciones</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="notifications_email"
                                   value="1"
                                   {{ ($preferences['notifications_email'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Notificaciones por correo</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Recibir alertas y actualizaciones por email</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="notifications_browser"
                                   value="1"
                                   {{ ($preferences['notifications_browser'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Notificaciones del navegador</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Mostrar notificaciones en tiempo real</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Interfaz --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Interfaz</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="compact_sidebar"
                                   value="1"
                                   {{ ($preferences['compact_sidebar'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Sidebar compacto por defecto</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Iniciar con el menú lateral colapsado</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar preferencias
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Actividad --}}
    <div x-show="activeTab === 'activity'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Actividad Reciente</h2>

            <div class="space-y-4">
                @if($user->last_login_at)
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Inicio de sesión</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $user->last_login_at->format('d/m/Y H:i') }} · IP: {{ $user->last_login_ip ?? 'Desconocida' }}
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Cuenta creada</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($user->approved_at)
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Cuenta aprobada</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->approved_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>

            @if(!$user->last_login_at && !$user->approved_at)
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No hay actividad reciente para mostrar</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>

        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">¿Eliminar tu cuenta?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
                    Esta acción es permanente y no se puede deshacer. Todos tus datos serán eliminados.
                </p>

                <form action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="mb-4">
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ingresa tu contraseña para confirmar
                        </label>
                        <input type="password"
                               name="password"
                               id="delete_password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               required>
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="cursor-pointer flex-1 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="cursor-pointer flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

