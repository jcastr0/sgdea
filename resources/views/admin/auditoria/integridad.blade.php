@extends('layouts.sgdea')

@section('title', 'Verificación de Integridad')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Auditoría', 'route' => 'admin.auditoria.index'],
    ['label' => 'Verificación de Integridad', 'active' => true],
]" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Verificación de Integridad</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Validación de los registros de auditoría</p>
        </div>
        <a href="{{ route('admin.auditoria.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    {{-- Resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total --}}
        <x-card padding="md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 dark:bg-slate-700 rounded-xl">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalVerificados }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Verificados</p>
                </div>
            </div>
        </x-card>

        {{-- Íntegros --}}
        <x-card padding="md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalIntegros }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Íntegros</p>
                </div>
            </div>
        </x-card>

        {{-- Comprometidos --}}
        <x-card padding="md">
            <div class="flex items-center gap-4">
                <div class="p-3 {{ $totalComprometidos > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-slate-700' }} rounded-xl">
                    <svg class="w-6 h-6 {{ $totalComprometidos > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold {{ $totalComprometidos > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $totalComprometidos }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Comprometidos</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Resultado general --}}
    @if($totalComprometidos === 0)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-green-800 dark:text-green-300">✓ Auditoría Íntegra</h3>
                    <p class="text-green-700 dark:text-green-400 mt-1">
                        Todos los {{ $totalVerificados }} registros verificados son íntegros. No se detectaron alteraciones.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-300">⚠️ Alteraciones Detectadas</h3>
                    <p class="text-red-700 dark:text-red-400 mt-1">
                        Se encontraron {{ $totalComprometidos }} registro(s) con problemas de integridad.
                    </p>
                </div>
            </div>
        </div>

        {{-- Detalles de registros comprometidos --}}
        <x-card title="Registros con Problemas" padding="none">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acción</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Problema</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($resultados as $resultado)
                            <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">#{{ $resultado['id'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $resultado['fecha'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $resultado['accion'] }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">{{ $resultado['problema'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    {{-- Información --}}
    <x-card>
        <div class="flex items-start gap-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white">¿Qué verifica esta herramienta?</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Esta verificación analiza los últimos 100 registros de auditoría para asegurar que contienen todos los campos requeridos
                    y no han sido alterados desde su creación. Es una medida de seguridad para garantizar la trazabilidad de las acciones en el sistema.
                </p>
            </div>
        </div>
    </x-card>
</div>
@endsection

