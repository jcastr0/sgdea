@extends('layouts.sgdea')

@section('title', 'Editar Perfil')
@section('page-title', 'Editar Perfil')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('dashboard')],
    ['label' => 'Mi Perfil', 'url' => route('profile.show')],
    ['label' => 'Editar'],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Perfil</h1>
        <p class="text-gray-500 dark:text-gray-400">Actualiza tu información personal</p>
    </div>

    {{-- Formulario de edición --}}
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            {{-- Avatar section --}}
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Foto de perfil</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Las iniciales de tu nombre se mostrarán como avatar</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Próximamente: Carga de foto personalizada</p>
                    </div>
                </div>
            </div>

            {{-- Form fields --}}
            <div class="p-6 space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Correo electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Teléfono --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+57 300 123 4567"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Departamento --}}
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Departamento
                        </label>
                        <input type="text"
                               name="department"
                               id="department"
                               value="{{ old('department', $user->department) }}"
                               placeholder="Ej: Contabilidad, Ventas, IT..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('department')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Información de solo lectura --}}
                <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Información de la cuenta (solo lectura)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Rol</label>
                            <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300">
                                {{ $user->role->name ?? 'Sin rol asignado' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Empresa</label>
                            <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300">
                                {{ $user->tenant->name ?? 'No asignada' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                            <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300">
                                {{ ucfirst($user->status ?? 'Pendiente') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Miembro desde</label>
                            <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300">
                                {{ $user->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer con botones --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('profile.show') }}"
                   class="cursor-pointer inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="cursor-pointer inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </div>
    </form>

    {{-- Sección de contraseña (separada) --}}
    <div class="mt-8 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cambiar Contraseña</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Actualiza tu contraseña de acceso</p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contraseña actual
                    </label>
                    <input type="password"
                           name="current_password"
                           id="current_password"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                           required>
                    @error('current_password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nueva contraseña
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                               required>
                        @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Confirmar nueva contraseña
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.
                </p>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Cambiar contraseña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

