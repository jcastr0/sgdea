@extends('layouts.sgdea')

@section('title', 'Configuración')
@section('page-title', 'Configuración del Sistema')

@section('content')
<div x-data="{ activeTab: 'general' }">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración</h1>
        <p class="text-gray-500 dark:text-gray-400">Administra la configuración de tu empresa</p>
    </div>

    {{-- Tabs de navegación --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-slate-700">
            <nav class="flex gap-4 overflow-x-auto">
                <button @click="activeTab = 'general'"
                        :class="activeTab === 'general' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        General
                    </span>
                </button>
                <button @click="activeTab = 'branding'"
                        :class="activeTab === 'branding' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Logo y Branding
                    </span>
                </button>
                <button @click="activeTab = 'theme'"
                        :class="activeTab === 'theme' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Tema y Colores
                    </span>
                </button>
                <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notificaciones
                    </span>
                </button>
                <button @click="activeTab = 'import'"
                        :class="activeTab === 'import' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Importación
                    </span>
                </button>
                <button @click="activeTab = 'export'"
                        :class="activeTab === 'export' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="cursor-pointer px-1 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exportación
                    </span>
                </button>
            </nav>
        </div>
    </div>

    {{-- Tab: General --}}
    <div x-show="activeTab === 'general'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Información General</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Información básica de tu empresa</p>
            </div>

            <form action="{{ route('configuration.general') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre de la empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $tenant->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dominio personalizado
                        </label>
                        <input type="text"
                               name="domain"
                               id="domain"
                               value="{{ old('domain', $tenant->domain) }}"
                               placeholder="empresa.sgdea.local"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opcional. Permite acceder al sistema desde un dominio propio.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Identificador (Slug)</label>
                        <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 font-mono">
                            {{ $tenant->slug }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                        <p class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg">
                            @if($tenant->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                Activo
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                {{ ucfirst($tenant->status) }}
                            </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Branding --}}
    <div x-show="activeTab === 'branding'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Logo y Branding</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Personaliza la apariencia visual de tu empresa</p>
            </div>

            <form action="{{ route('configuration.logo') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Logo principal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Logo Principal</label>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-24 h-24 bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-slate-600">
                                @if($tenant->logo_path)
                                <img src="{{ Storage::url($tenant->logo_path) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 file:cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG o SVG. Máximo 2MB.</p>
                                @if($tenant->logo_path)
                                <a href="{{ route('configuration.delete-logo', 'logo') }}"
                                   onclick="return confirm('¿Eliminar este logo?')"
                                   class="inline-block mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400">
                                    Eliminar logo
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Logo para modo claro --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Logo para Modo Oscuro</label>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-24 h-24 bg-slate-800 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-slate-600">
                                @if($tenant->logo_path_light)
                                <img src="{{ Storage::url($tenant->logo_path_light) }}" alt="Logo Light" class="max-w-full max-h-full object-contain">
                                @else
                                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logo_light" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 file:cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Versión clara del logo para fondos oscuros.</p>
                                @if($tenant->logo_path_light)
                                <a href="{{ route('configuration.delete-logo', 'logo_light') }}"
                                   onclick="return confirm('¿Eliminar este logo?')"
                                   class="inline-block mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400">
                                    Eliminar
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Logo para modo oscuro --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Logo para Modo Claro</label>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-24 h-24 bg-white rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300">
                                @if($tenant->logo_path_dark)
                                <img src="{{ Storage::url($tenant->logo_path_dark) }}" alt="Logo Dark" class="max-w-full max-h-full object-contain">
                                @else
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logo_dark" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 file:cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Versión oscura del logo para fondos claros.</p>
                                @if($tenant->logo_path_dark)
                                <a href="{{ route('configuration.delete-logo', 'logo_dark') }}"
                                   onclick="return confirm('¿Eliminar este logo?')"
                                   class="inline-block mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400">
                                    Eliminar
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Favicon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Favicon</label>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-slate-600">
                                @if($tenant->favicon_path)
                                <img src="{{ Storage::url($tenant->favicon_path) }}" alt="Favicon" class="max-w-full max-h-full">
                                @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="favicon" accept=".png,.ico" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 file:cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG o ICO. Tamaño recomendado: 32x32 o 64x64 px.</p>
                                @if($tenant->favicon_path)
                                <a href="{{ route('configuration.delete-logo', 'favicon') }}"
                                   onclick="return confirm('¿Eliminar el favicon?')"
                                   class="inline-block mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400">
                                    Eliminar
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar logos
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Tema y Colores --}}
    <div x-show="activeTab === 'theme'" x-transition x-data="{
        primaryColor: '{{ $theme->color_primary ?? '#2563eb' }}',
        secondaryColor: '{{ $theme->color_secondary ?? '#0f172a' }}',
        accentColor: '{{ $theme->color_accent ?? '#10b981' }}'
    }">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tema y Colores</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Personaliza los colores de la interfaz</p>
            </div>

            <form action="{{ route('configuration.theme') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Color primario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color Primario</label>
                        <div class="flex items-center gap-3">
                            <input type="color"
                                   name="color_primary"
                                   x-model="primaryColor"
                                   class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text"
                                   x-model="primaryColor"
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono text-sm"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Botones, enlaces y elementos activos</p>
                    </div>

                    {{-- Color secundario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color Secundario</label>
                        <div class="flex items-center gap-3">
                            <input type="color"
                                   name="color_secondary"
                                   x-model="secondaryColor"
                                   class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text"
                                   x-model="secondaryColor"
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono text-sm"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sidebar, header y elementos de fondo</p>
                    </div>

                    {{-- Color de acento --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color de Acento</label>
                        <div class="flex items-center gap-3">
                            <input type="color"
                                   name="color_accent"
                                   x-model="accentColor"
                                   class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text"
                                   x-model="accentColor"
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono text-sm"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Badges de éxito y elementos destacados</p>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Vista previa</h3>
                    <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <div class="flex flex-wrap gap-3">
                            <button type="button" :style="{ backgroundColor: primaryColor }" class="px-4 py-2 text-white rounded-lg text-sm">Botón Primario</button>
                            <button type="button" :style="{ backgroundColor: secondaryColor }" class="px-4 py-2 text-white rounded-lg text-sm">Botón Secundario</button>
                            <span :style="{ backgroundColor: accentColor }" class="px-3 py-1 text-white rounded-full text-xs">Badge</span>
                        </div>
                    </div>
                </div>

                {{-- Opciones de tema --}}
                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="dark_mode_enabled"
                               value="1"
                               {{ ($theme->dark_mode_enabled ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Permitir modo oscuro</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Los usuarios podrán cambiar entre modo claro y oscuro</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar tema
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Notificaciones --}}
    <div x-show="activeTab === 'notifications'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuración de Notificaciones</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Define cuándo y cómo recibir notificaciones</p>
            </div>

            <form action="{{ route('configuration.notifications') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Notificaciones por email --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Notificaciones por correo electrónico</h3>
                        <div class="space-y-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       name="email_on_import"
                                       value="1"
                                       {{ ($settings['notifications']['email_on_import'] ?? true) ? 'checked' : '' }}
                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Importación completada</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Recibir email cuando finalice una importación de facturas o PDFs</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       name="email_on_error"
                                       value="1"
                                       {{ ($settings['notifications']['email_on_error'] ?? true) ? 'checked' : '' }}
                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Errores del sistema</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Recibir alertas sobre errores críticos en el sistema</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       name="email_daily_summary"
                                       value="1"
                                       {{ ($settings['notifications']['email_daily_summary'] ?? false) ? 'checked' : '' }}
                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Resumen diario</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Recibir un resumen diario de actividad cada mañana</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-slate-700">

                    {{-- Notificaciones del navegador --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Notificaciones del navegador</h3>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="browser_notifications"
                                   value="1"
                                   {{ ($settings['notifications']['browser_notifications'] ?? true) ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Habilitar notificaciones push</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Mostrar notificaciones en tiempo real en el navegador</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Importación --}}
    <div x-show="activeTab === 'import'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuración de Importación</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Opciones por defecto para importar archivos Excel y PDF</p>
            </div>

            <form action="{{ route('configuration.import') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Modo de importación --}}
                    <div>
                        <label for="default_import_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Modo de importación por defecto
                        </label>
                        <select name="default_import_mode"
                                id="default_import_mode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="create" {{ ($settings['import']['default_import_mode'] ?? 'create') === 'create' ? 'selected' : '' }}>Solo crear nuevos registros</option>
                            <option value="update" {{ ($settings['import']['default_import_mode'] ?? '') === 'update' ? 'selected' : '' }}>Actualizar existentes</option>
                            <option value="skip" {{ ($settings['import']['default_import_mode'] ?? '') === 'skip' ? 'selected' : '' }}>Omitir duplicados</option>
                        </select>
                    </div>

                    {{-- Formato de fecha --}}
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Formato de fecha esperado
                        </label>
                        <select name="date_format"
                                id="date_format"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="d/m/Y" {{ ($settings['import']['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/AAAA (31/12/2024)</option>
                            <option value="Y-m-d" {{ ($settings['import']['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-DD (2024-12-31)</option>
                            <option value="m/d/Y" {{ ($settings['import']['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/AAAA (12/31/2024)</option>
                        </select>
                    </div>

                    {{-- Separador decimal --}}
                    <div>
                        <label for="decimal_separator" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Separador decimal
                        </label>
                        <select name="decimal_separator"
                                id="decimal_separator"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="," {{ ($settings['import']['decimal_separator'] ?? ',') === ',' ? 'selected' : '' }}>Coma (1.234,56)</option>
                            <option value="." {{ ($settings['import']['decimal_separator'] ?? '') === '.' ? 'selected' : '' }}>Punto (1,234.56)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="auto_create_terceros"
                               value="1"
                               {{ ($settings['import']['auto_create_terceros'] ?? true) ? 'checked' : '' }}
                               class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Crear terceros automáticamente</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Si el tercero (cliente) no existe, crearlo automáticamente durante la importación</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="validate_duplicates"
                               value="1"
                               {{ ($settings['import']['validate_duplicates'] ?? true) ? 'checked' : '' }}
                               class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Validar duplicados por CUFE</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Verificar si la factura ya existe basándose en el código CUFE</p>
                        </div>
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Exportación --}}
    <div x-show="activeTab === 'export'" x-transition>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Exportación de Datos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Exporta tus datos en diferentes formatos</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Exportar facturas --}}
                    <div class="p-4 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">Facturas</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Exportar todas las facturas a Excel</p>
                                <form action="{{ route('configuration.export') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="facturas">
                                    <button type="submit" class="cursor-pointer text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                        Exportar →
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Exportar terceros --}}
                    <div class="p-4 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">Terceros</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Exportar directorio de clientes</p>
                                <form action="{{ route('configuration.export') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="terceros">
                                    <button type="submit" class="cursor-pointer text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                        Exportar →
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Exportar todo --}}
                    <div class="p-4 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">Backup Completo</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Exportar todos los datos</p>
                                <form action="{{ route('configuration.export') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="all">
                                    <button type="submit" class="cursor-pointer text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                        Exportar →
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-amber-700 dark:text-amber-300">
                            <strong>Nota:</strong> La funcionalidad de exportación estará disponible próximamente. Los datos se exportarán en formato Excel (.xlsx).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

