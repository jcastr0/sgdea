@extends('layouts.sgdea')

@section('title', 'Auditoría del Sistema')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Auditoría', 'active' => true],
]" />
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Auditoría del Sistema</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Registro de todas las acciones realizadas en el sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.auditoria.integridad') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verificar Integridad
            </a>
            <a href="{{ route('admin.auditoria.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <x-card>
        <form method="GET" action="{{ route('admin.auditoria.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Usuario, IP..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Acción --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acción</label>
                    <select name="action"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las acciones</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" {{ ($filters['action'] ?? '') === $accion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $accion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Usuario (si tiene permisos) --}}
                @if($canFilterByUser && $usuarios->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario</label>
                    <select name="user_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ ($filters['user_id'] ?? '') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Tipo de modelo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entidad</label>
                    <select name="model_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las entidades</option>
                        <option value="Factura" {{ ($filters['model_type'] ?? '') === 'Factura' ? 'selected' : '' }}>Factura</option>
                        <option value="Tercero" {{ ($filters['model_type'] ?? '') === 'Tercero' ? 'selected' : '' }}>Tercero</option>
                        <option value="User" {{ ($filters['model_type'] ?? '') === 'User' ? 'selected' : '' }}>Usuario</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Fecha inicio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $filters['fecha_inicio'] ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Fecha fin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $filters['fecha_fin'] ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Botones --}}
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.auditoria.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition-colors cursor-pointer">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabla de resultados --}}
    <x-card padding="none">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acción</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Modelo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">IP</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($logs as $log)
                        @php
                            $actionColors = [
                                'login' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                'logout' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'login_failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'create' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'update' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'delete' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'import' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                            ];
                            $actionColor = $actionColors[strtolower($log->action)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $log->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $log->user?->name ?? 'Sistema' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->model_type)
                                    <span class="text-sm text-gray-900 dark:text-white">{{ class_basename($log->model_type) }}</span>
                                    @if($log->model_id)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">#{{ $log->model_id }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('admin.auditoria.show', $log->id) }}"
                                   class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No se encontraron registros</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajusta los filtros para ver más resultados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection

