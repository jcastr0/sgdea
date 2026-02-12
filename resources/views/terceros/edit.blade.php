@extends('layouts.sgdea')

@section('title', 'Editar Tercero')
@section('page-title', 'Editar Tercero')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('dashboard')],
    ['label' => 'Terceros', 'url' => route('terceros.index')],
    ['label' => $tercero->nombre_razon_social, 'url' => route('terceros.show', $tercero)],
    ['label' => 'Editar'],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Editar Tercero</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tercero->nombre_razon_social }}</p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('terceros.update', $tercero) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                {{-- Información básica --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- NIT --}}
                    <div>
                        <label for="nit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            NIT <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nit"
                               id="nit"
                               value="{{ old('nit', $tercero->nit) }}"
                               placeholder="Ej: 900123456"
                               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white
                                      @error('nit') border-red-500 @else border-gray-300 @enderror"
                               required>
                        @error('nit')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre/Razón Social --}}
                    <div>
                        <label for="nombre_razon_social" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre/Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nombre_razon_social"
                               id="nombre_razon_social"
                               value="{{ old('nombre_razon_social', $tercero->nombre_razon_social) }}"
                               placeholder="Nombre del tercero"
                               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white
                                      @error('nombre_razon_social') border-red-500 @else border-gray-300 @enderror"
                               required>
                        @error('nombre_razon_social')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Información de contacto --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $tercero->email) }}"
                               placeholder="correo@ejemplo.com"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teléfono
                        </label>
                        <input type="tel"
                               name="telefono"
                               id="telefono"
                               value="{{ old('telefono', $tercero->telefono) }}"
                               placeholder="Ej: 601 123 4567"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Dirección --}}
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dirección
                    </label>
                    <input type="text"
                           name="direccion"
                           id="direccion"
                           value="{{ old('direccion', $tercero->direccion) }}"
                           placeholder="Dirección completa"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                    @error('direccion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado"
                            id="estado"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="activo" {{ old('estado', $tercero->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $tercero->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notas --}}
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notas
                    </label>
                    <textarea name="notas"
                              id="notas"
                              rows="3"
                              placeholder="Notas o comentarios adicionales..."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white resize-none">{{ old('notas', $tercero->notas) }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Información del registro --}}
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Información del registro</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Creado:</span>
                            <span class="text-gray-900 dark:text-white ml-1">{{ $tercero->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Actualizado:</span>
                            <span class="text-gray-900 dark:text-white ml-1">{{ $tercero->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer con acciones --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <a href="{{ route('terceros.show', $tercero) }}"
                   class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                    Cancelar
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('terceros.show', $tercero) }}"
                       class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors">
                        Ver Perfil
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

