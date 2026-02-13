{{--
    Componente: Status Indicator
    Uso: <x-admin.status-indicator status="active" />
         <x-admin.status-indicator status="pending" label="Pendiente" />
         <x-admin.status-indicator status="suspended" size="lg" pulse />

    Props:
    - status (string): Estado (active, inactive, pending, suspended, trial, blocked, online, offline, away, busy, success, warning, error, info)
    - label (string|null): Texto del badge (si no se proporciona, usa el status como label)
    - size (string): Tamaño (xs, sm, md, lg)
    - variant (string): Variante visual (badge, dot, pill)
    - pulse (bool): Añadir animación de pulso
    - showDot (bool): Mostrar punto de color antes del texto (solo para badge y pill)
    - uppercase (bool): Texto en mayúsculas
--}}

@props([
    'status' => 'active',
    'label' => null,
    'size' => 'md',
    'variant' => 'badge',
    'pulse' => false,
    'showDot' => false,
    'uppercase' => false,
])

@php
    // Mapeo de estados a colores
    $statusConfig = [
        // Estados de usuario/tenant
        'active' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500', 'label' => 'Activo'],
        'inactive' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'dot' => 'bg-gray-400', 'label' => 'Inactivo'],
        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Pendiente'],
        'pending_approval' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Pendiente'],
        'suspended' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Suspendido'],
        'blocked' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Bloqueado'],
        'trial' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400', 'dot' => 'bg-purple-500', 'label' => 'Prueba'],

        // Estados online
        'online' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500', 'label' => 'En línea'],
        'offline' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'dot' => 'bg-gray-400', 'label' => 'Desconectado'],
        'away' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Ausente'],
        'busy' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Ocupado'],

        // Estados de factura
        'aceptado' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500', 'label' => 'Aceptado'],
        'rechazado' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Rechazado'],
        'pendiente' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Pendiente'],

        // Estados genéricos
        'success' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500', 'label' => 'Éxito'],
        'warning' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Advertencia'],
        'error' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Error'],
        'info' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'dot' => 'bg-blue-500', 'label' => 'Info'],
    ];

    $config = $statusConfig[$status] ?? $statusConfig['inactive'];

    // Tamaños
    $sizes = [
        'xs' => ['badge' => 'px-1.5 py-0.5 text-[10px]', 'dot' => 'w-1.5 h-1.5', 'pill' => 'px-2 py-0.5 text-[10px]'],
        'sm' => ['badge' => 'px-2 py-0.5 text-xs', 'dot' => 'w-2 h-2', 'pill' => 'px-2.5 py-0.5 text-xs'],
        'md' => ['badge' => 'px-2.5 py-1 text-xs', 'dot' => 'w-2.5 h-2.5', 'pill' => 'px-3 py-1 text-sm'],
        'lg' => ['badge' => 'px-3 py-1.5 text-sm', 'dot' => 'w-3 h-3', 'pill' => 'px-4 py-1.5 text-sm'],
    ];

    $sizeConfig = $sizes[$size] ?? $sizes['md'];

    // Label final
    $displayLabel = $label ?? $config['label'];
    if ($uppercase) {
        $displayLabel = strtoupper($displayLabel);
    }
@endphp

@if($variant === 'dot')
    {{-- Solo el punto --}}
    <span {{ $attributes->merge(['class' => 'inline-block rounded-full ' . $config['dot'] . ' ' . $sizeConfig['dot'] . ($pulse ? ' animate-pulse' : '')]) }}></span>

@elseif($variant === 'pill')
    {{-- Pill más redondeado --}}
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full font-medium ' . $config['bg'] . ' ' . $config['text'] . ' ' . $sizeConfig['pill']]) }}>
        @if($showDot)
            <span class="rounded-full {{ $config['dot'] }} {{ $sizeConfig['dot'] }} {{ $pulse ? 'animate-pulse' : '' }}"></span>
        @endif
        {{ $displayLabel }}
    </span>

@else
    {{-- Badge estándar --}}
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md font-medium ' . $config['bg'] . ' ' . $config['text'] . ' ' . $sizeConfig['badge']]) }}>
        @if($showDot)
            <span class="rounded-full {{ $config['dot'] }} {{ $sizeConfig['dot'] }} {{ $pulse ? 'animate-pulse' : '' }}"></span>
        @endif
        {{ $displayLabel }}
    </span>
@endif

