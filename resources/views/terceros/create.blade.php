@extends('layouts.sgdea')

@section('title', 'Nuevo Tercero')
@section('page-title', 'Nuevo Tercero')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Terceros', 'url' => route('terceros.index')],
    ['label' => 'Nuevo'],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Crear Nuevo Tercero</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ingresa los datos del cliente o proveedor</p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('terceros.store') }}" method="POST" x-data="terceroForm()">
            @csrf

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
                               value="{{ old('nit', $tercero['nit'] ?? '') }}"
                               x-model="nit"
                               @blur="checkDuplicates"
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
                               value="{{ old('nombre_razon_social', $tercero['nombre_razon_social'] ?? '') }}"
                               x-model="nombre"
                               @blur="checkDuplicates"
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
                               value="{{ old('email', $tercero['email'] ?? '') }}"
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
                               value="{{ old('telefono', $tercero['telefono'] ?? '') }}"
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
                           value="{{ old('direccion', $tercero['direccion'] ?? '') }}"
                           placeholder="Dirección completa"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                    @error('direccion')
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
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white resize-none">{{ old('notas', $tercero['notas'] ?? '') }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alerta de duplicados --}}
                @if(isset($duplicados) && count($duplicados) > 0)
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-amber-800 dark:text-amber-300">Se encontraron posibles duplicados</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Revisa si alguno de estos terceros corresponde al que deseas crear:</p>
                            <ul class="mt-2 space-y-2">
                                @foreach($duplicados as $dup)
                                <li class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                                    <span class="font-mono">{{ $dup['nit'] }}</span>
                                    <span>-</span>
                                    <span>{{ $dup['nombre_razon_social'] }}</span>
                                    <span class="px-1.5 py-0.5 text-xs bg-amber-200 dark:bg-amber-800 rounded">{{ $dup['razon'] }} ({{ $dup['similitud'] }}%)</span>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="confirmar_crear" value="1" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                    <span class="text-sm font-medium text-amber-800 dark:text-amber-300">Confirmo que deseo crear este tercero de todas formas</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer con acciones --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <a href="{{ route('terceros.index') }}"
                   class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Tercero
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function terceroForm() {
    return {
        nit: '{{ old('nit', $tercero['nit'] ?? '') }}',
        nombre: '{{ old('nombre_razon_social', $tercero['nombre_razon_social'] ?? '') }}',

        checkDuplicates() {
            // En una implementación completa, aquí se haría una llamada AJAX
            // para verificar duplicados en tiempo real
            console.log('Verificando duplicados...');
        }
    }
}
</script>
@endsection

