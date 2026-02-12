@extends('layouts.sgdea')

@section('title', 'Detalle de Auditoría')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Auditoría', 'route' => 'admin.auditoria.index'],
    ['label' => 'Detalle #' . $log->id, 'active' => true],
]" />
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registro de Auditoría #{{ $log->id }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
        </div>
        <a href="{{ route('admin.auditoria.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    {{-- Grid principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Información del Evento --}}
            <x-card title="Información del Evento">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Acción</label>
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
                            ];
                            $actionColor = $actionColors[strtolower($log->action)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        <p class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $actionColor }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Fecha y Hora</label>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                            <span class="text-sm text-gray-500 dark:text-gray-400">({{ $log->created_at->diffForHumans() }})</span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Usuario</label>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'Sistema' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user?->email ?? 'system@sgdea.local' }}</p>
                            </div>
                        </div>
                    </div>
                    @if($log->model_type)
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Modelo Afectado</label>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">
                            {{ class_basename($log->model_type) }}
                            @if($log->model_id)
                                <span class="text-gray-500 dark:text-gray-400">#{{ $log->model_id }}</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </x-card>

            {{-- Valores anteriores --}}
            @if($log->old_values)
            <x-card title="Valores Anteriores">
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-red-800 dark:text-red-300 font-mono whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-card>
            @endif

            {{-- Valores nuevos --}}
            @if($log->new_values)
            <x-card title="Valores Nuevos">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-green-800 dark:text-green-300 font-mono whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-card>
            @endif

            {{-- Contexto adicional --}}
            @if($log->context)
            <x-card title="Contexto Adicional">
                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-800 dark:text-gray-300 font-mono whitespace-pre-wrap">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-card>
            @endif

            {{-- Historial completo del modelo --}}
            @if($auditCompleta->count() > 1)
            <x-card title="Historial Completo del Registro">
                <x-slot:headerActions>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $auditCompleta->count() }} eventos</span>
                </x-slot:headerActions>

                <div class="space-y-3">
                    @foreach($auditCompleta as $item)
                        @php
                            $itemColor = $actionColors[strtolower($item->action)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            $isCurrent = $item->id === $log->id;
                        @endphp
                        <div class="flex items-start gap-3 p-3 rounded-lg {{ $isCurrent ? 'bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500' : 'bg-gray-50 dark:bg-slate-700/50' }}">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-3 h-3 rounded-full {{ $isCurrent ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $itemColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->action)) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->created_at->format('d/m/Y H:i:s') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ $item->user?->name ?? 'Sistema' }}
                                </p>
                            </div>
                            @if(!$isCurrent)
                            <a href="{{ route('admin.auditoria.show', $item->id) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                Ver
                            </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-6">
            {{-- Información técnica --}}
            <x-card title="Información de la Solicitud">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Dirección IP</label>
                        <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $log->ip_address ?? 'No registrada' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Método HTTP</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $log->method ?? 'N/A' }}</p>
                    </div>
                    @if($log->url)
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">URL</label>
                        <p class="font-medium text-gray-600 dark:text-gray-400 text-xs font-mono break-all">{{ $log->url }}</p>
                    </div>
                    @endif
                    @if($log->user_agent)
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">User Agent</label>
                        <p class="font-medium text-gray-600 dark:text-gray-400 text-xs break-all">{{ $log->user_agent }}</p>
                    </div>
                    @endif
                </div>
            </x-card>

            {{-- Visor de PDF (si aplica) --}}
            @if($pdfPath)
            <x-card title="Documento Asociado">
                <div class="aspect-[3/4] bg-gray-100 dark:bg-slate-700 rounded-lg overflow-hidden">
                    <iframe src="{{ $pdfPath }}" class="w-full h-full" type="application/pdf"></iframe>
                </div>
                <div class="mt-3">
                    <a href="{{ $pdfPath }}" target="_blank"
                       class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Abrir en nueva ventana
                    </a>
                </div>
            </x-card>
            @endif

            {{-- Ir al registro --}}
            @if($entidad)
            <x-card title="Registro Relacionado">
                <div class="text-center py-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                    </p>
                    @if($log->model_type === \App\Models\Factura::class)
                        <a href="{{ route('facturas.show', $log->model_id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors cursor-pointer">
                            Ver Factura
                        </a>
                    @elseif($log->model_type === \App\Models\Tercero::class)
                        <a href="{{ route('terceros.show', $log->model_id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors cursor-pointer">
                            Ver Tercero
                        </a>
                    @endif
                </div>
            </x-card>
            @endif
        </div>
    </div>
</div>
@endsection

