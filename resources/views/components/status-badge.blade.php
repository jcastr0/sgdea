{{--
    Componente: Status Badge
    Uso: <x-status-badge status="pendiente" /> o <x-status-badge type="success">Completado</x-status-badge>

    Props:
    - status: Estado predefinido (pendiente, pagada, cancelada, activo, inactivo, etc.)
    - type: Tipo de badge (success, danger, warning, info, neutral)
    - size: Tamaño del badge (xs, sm, md)
    - dot: Mostrar punto indicador (default: true)
    - pulse: Añadir animación de pulso (default: false)
--}}

@props([
    'status' => null,
    'type' => null,
    'size' => 'sm',
    'dot' => true,
    'pulse' => false,
])

@php
    // Mapeo de estados a tipos y textos
    $statusConfig = [
        'pendiente' => ['type' => 'warning', 'text' => 'Pendiente', 'icon' => '⏳'],
        'pagada' => ['type' => 'success', 'text' => 'Pagada', 'icon' => '✓'],
        'pagado' => ['type' => 'success', 'text' => 'Pagado', 'icon' => '✓'],
        'cancelada' => ['type' => 'danger', 'text' => 'Cancelada', 'icon' => '✗'],
        'cancelado' => ['type' => 'danger', 'text' => 'Cancelado', 'icon' => '✗'],
        'activo' => ['type' => 'success', 'text' => 'Activo', 'icon' => '●'],
        'inactivo' => ['type' => 'neutral', 'text' => 'Inactivo', 'icon' => '○'],
        'procesando' => ['type' => 'info', 'text' => 'Procesando', 'icon' => '⟳'],
        'completado' => ['type' => 'success', 'text' => 'Completado', 'icon' => '✓'],
        'error' => ['type' => 'danger', 'text' => 'Error', 'icon' => '!'],
        'borrador' => ['type' => 'neutral', 'text' => 'Borrador', 'icon' => '📝'],
        'aceptado' => ['type' => 'success', 'text' => 'Aceptado', 'icon' => '✓'],
        'rechazado' => ['type' => 'danger', 'text' => 'Rechazado', 'icon' => '✗'],
    ];

    // Determinar configuración
    if ($status) {
        $config = $statusConfig[strtolower($status)] ?? ['type' => 'neutral', 'text' => ucfirst($status), 'icon' => '●'];
        $badgeType = $type ?? $config['type'];
        $text = $slot->isEmpty() ? $config['text'] : $slot;
    } else {
        $badgeType = $type ?? 'neutral';
        $text = $slot;
    }

    // Clases de tipo
    $typeClasses = [
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'neutral' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ][$badgeType] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';

    // Colores del punto indicador
    $dotColors = [
        'success' => 'bg-green-500',
        'danger' => 'bg-red-500',
        'warning' => 'bg-amber-500',
        'info' => 'bg-blue-500',
        'neutral' => 'bg-gray-500',
    ][$badgeType] ?? 'bg-gray-500';

    // Clases de tamaño
    $sizeClasses = [
        'xs' => 'text-xs px-2 py-0.5',
        'sm' => 'text-xs px-2.5 py-1',
        'md' => 'text-sm px-3 py-1.5',
    ][$size] ?? 'text-xs px-2.5 py-1';

    $dotSizes = [
        'xs' => 'w-1.5 h-1.5',
        'sm' => 'w-2 h-2',
        'md' => 'w-2.5 h-2.5',
    ][$size] ?? 'w-2 h-2';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-full $typeClasses $sizeClasses"]) }}>
    @if($dot)
        <span class="relative flex {{ $dotSizes }}">
            @if($pulse)
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $dotColors }} opacity-75"></span>
            @endif
            <span class="relative inline-flex rounded-full h-full w-full {{ $dotColors }}"></span>
        </span>
    @endif
    {{ $text }}
</span>

