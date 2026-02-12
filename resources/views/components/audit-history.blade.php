{{--
    Componente: Audit History
    Uso: <x-audit-history :logs="$auditLogs" :title="'Historial de Cambios'" />

    Props:
    - logs: Colección de registros de auditoría
    - title: Título del card (opcional)
    - subtitle: Subtítulo (opcional)
    - limit: Límite de registros a mostrar (default: 10)
    - showLink: Mostrar link a auditoría completa (default: true)
    - linkUrl: URL para ver auditoría completa (opcional)
    - compact: Modo compacto (default: false)
--}}

@props([
    'logs' => collect(),
    'title' => 'Historial de Cambios',
    'subtitle' => null,
    'limit' => 10,
    'showLink' => true,
    'linkUrl' => null,
    'compact' => false
])

@php
    // Mapeo de acciones a estilos
    $actionStyles = [
        // Login/Logout
        'login' => ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'badge' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300', 'label' => 'Inicio de Sesión', 'icon' => 'login'],
        'logout' => ['border' => 'border-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'badge' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300', 'label' => 'Cierre de Sesión', 'icon' => 'logout'],
        'login_failed' => ['border' => 'border-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'badge' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300', 'label' => 'Login Fallido', 'icon' => 'x-circle'],

        // CRUD
        'create' => ['border' => 'border-green-500', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'badge' => 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300', 'label' => 'Creado', 'icon' => 'plus-circle'],
        'created' => ['border' => 'border-green-500', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'badge' => 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300', 'label' => 'Creado', 'icon' => 'plus-circle'],
        'update' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'badge' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300', 'label' => 'Actualizado', 'icon' => 'pencil'],
        'updated' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'badge' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300', 'label' => 'Actualizado', 'icon' => 'pencil'],
        'delete' => ['border' => 'border-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'badge' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300', 'label' => 'Eliminado', 'icon' => 'trash'],
        'deleted' => ['border' => 'border-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'badge' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300', 'label' => 'Eliminado', 'icon' => 'trash'],
        'restore' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'badge' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300', 'label' => 'Restaurado', 'icon' => 'refresh'],
        'restored' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'badge' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300', 'label' => 'Restaurado', 'icon' => 'refresh'],

        // Importación
        'import' => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'badge' => 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300', 'label' => 'Importado', 'icon' => 'upload'],
        'pdf_associated' => ['border' => 'border-rose-500', 'bg' => 'bg-rose-50 dark:bg-rose-900/20', 'badge' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300', 'label' => 'PDF Asociado', 'icon' => 'document'],

        // Otros
        'view' => ['border' => 'border-gray-300', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'badge' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400', 'label' => 'Consultado', 'icon' => 'eye'],
        'execute' => ['border' => 'border-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'badge' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300', 'label' => 'Ejecutado', 'icon' => 'lightning-bolt'],
    ];

    $defaultStyle = ['border' => 'border-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'badge' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300', 'label' => 'Acción', 'icon' => 'clock'];

    // Helper para tiempo relativo
    function auditTimeAgo($date) {
        if (!$date) return '';
        return \Carbon\Carbon::parse($date)->locale('es')->diffForHumans();
    }

    // Helper para detectar navegador
    function auditDetectBrowser($userAgent) {
        if (empty($userAgent)) return ['name' => 'Desconocido', 'icon' => 'globe'];
        $userAgent = strtolower($userAgent);
        if (str_contains($userAgent, 'edg')) return ['name' => 'Edge', 'icon' => 'edge'];
        if (str_contains($userAgent, 'chrome')) return ['name' => 'Chrome', 'icon' => 'chrome'];
        if (str_contains($userAgent, 'firefox')) return ['name' => 'Firefox', 'icon' => 'firefox'];
        if (str_contains($userAgent, 'safari')) return ['name' => 'Safari', 'icon' => 'safari'];
        return ['name' => 'Navegador', 'icon' => 'globe'];
    }
@endphp

<x-card class="overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if($logs->count() > 0)
            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-700 px-2 py-1 rounded-full">
                {{ $logs->count() }} {{ $logs->count() === 1 ? 'registro' : 'registros' }}
            </span>
        @endif
    </div>

    {{-- Contenido --}}
    @if($logs->isEmpty())
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No hay registros de auditoría</p>
        </div>
    @else
        <div class="space-y-3" x-data="{ expanded: null }">
            @foreach($logs->take($limit) as $index => $log)
                @php
                    $action = strtolower($log->action ?? 'action');
                    $style = $actionStyles[$action] ?? $defaultStyle;
                    $browser = auditDetectBrowser($log->user_agent ?? '');

                    // Usuario que realizó la acción
                    $usuario = $log->user ?? null;
                    if ($usuario && $usuario->id != 1) {
                        $nombreUsuario = $usuario->name ?? $usuario->nombre_completo ?? 'Usuario';
                    } elseif ($log->user_id == 1) {
                        $nombreUsuario = 'Sistema';
                    } else {
                        $nombreUsuario = 'Usuario desconocido';
                    }

                    // Descripción
                    $descripcion = null;
                    if (!empty($log->context) && is_array($log->context) && isset($log->context['description'])) {
                        $descripcion = $log->context['description'];
                    }

                    $hasDetails = (!empty($log->old_values) && !empty($log->new_values)) ||
                                  (in_array($action, ['create', 'created']) && !empty($log->new_values)) ||
                                  $log->ip_address || $log->user_agent;
                @endphp

                <div class="border-l-4 {{ $style['border'] }} {{ $style['bg'] }} rounded-r-lg overflow-hidden transition-all duration-200"
                     :class="{ 'ring-2 ring-blue-300 dark:ring-blue-600': expanded === {{ $index }} }">

                    {{-- Cabecera clickeable --}}
                    <div class="p-3 cursor-pointer hover:bg-white/50 dark:hover:bg-white/5 transition-colors"
                         @click="expanded = expanded === {{ $index }} ? null : {{ $index }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                {{-- Icono --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    @switch($style['icon'])
                                        @case('plus-circle')
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @break
                                        @case('pencil')
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            @break
                                        @case('trash')
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            @break
                                        @case('upload')
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            @break
                                        @case('document')
                                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            @break
                                        @case('eye')
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                    @endswitch
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                            {{ $style['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            por {{ $nombreUsuario }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '' }}
                                        <span class="text-gray-400 dark:text-gray-500">({{ auditTimeAgo($log->created_at) }})</span>
                                    </p>
                                    @if($descripcion && !$compact)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 italic">
                                            "{{ Str::limit($descripcion, 80) }}"
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Badge y expand --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $style['badge'] }}">
                                    {{ $style['label'] }}
                                </span>
                                @if($hasDetails)
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                         :class="{ 'rotate-180': expanded === {{ $index }} }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Detalles expandibles --}}
                    @if($hasDetails)
                        <div x-show="expanded === {{ $index }}"
                             x-collapse
                             class="border-t border-gray-200 dark:border-gray-700">
                            <div class="p-3 space-y-3 bg-white/50 dark:bg-black/20">

                                {{-- Cambios (para updates) --}}
                                @if(in_array($action, ['update', 'updated']) && !empty($log->old_values) && !empty($log->new_values))
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Cambios realizados:</p>
                                        <div class="space-y-2">
                                            @php
                                                $oldValues = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true) ?? [];
                                                $newValues = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true) ?? [];
                                            @endphp
                                            @foreach($newValues as $key => $newValue)
                                                @if(isset($oldValues[$key]) && $oldValues[$key] !== $newValue)
                                                    <div class="text-xs bg-gray-50 dark:bg-slate-800 rounded p-2">
                                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="text-red-600 dark:text-red-400 line-through">{{ is_array($oldValues[$key]) ? json_encode($oldValues[$key]) : Str::limit($oldValues[$key], 50) }}</span>
                                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                            </svg>
                                                            <span class="text-green-600 dark:text-green-400 font-medium">{{ is_array($newValue) ? json_encode($newValue) : Str::limit($newValue, 50) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Valores iniciales (para create) --}}
                                @if(in_array($action, ['create', 'created']) && !empty($log->new_values))
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Valores iniciales:</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            @php
                                                $values = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true) ?? [];
                                                $excludeFields = ['password', 'remember_token', 'api_token', 'two_factor_secret'];
                                            @endphp
                                            @foreach($values as $key => $value)
                                                @if(!in_array($key, $excludeFields) && !empty($value))
                                                    <div class="text-xs">
                                                        <span class="font-semibold text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="text-gray-800 dark:text-gray-200 ml-1">{{ is_array($value) ? json_encode($value) : Str::limit($value, 40) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Info de la solicitud --}}
                                @if($log->ip_address || $log->user_agent)
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            @if($log->ip_address)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                                    </svg>
                                                    {{ $log->ip_address }}
                                                </span>
                                            @endif
                                            @if($log->user_agent)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $browser['name'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        @if($showLink && $linkUrl)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                <a href="{{ $linkUrl }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium inline-flex items-center gap-1 transition-colors">
                    Ver auditoría completa
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        @elseif($logs->count() > $limit)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Mostrando {{ $limit }} de {{ $logs->count() }} registros
                </p>
            </div>
        @endif
    @endif
</x-card>

