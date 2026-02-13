{{--
    Componente: Tenant Badge
    Uso: <x-admin.tenant-badge :tenant="$tenant" />
         <x-admin.tenant-badge :tenant="$tenant" size="lg" showStatus />

    Props:
    - tenant (object): Objeto tenant con name, slug, status, logo_path (opcional)
    - size (string): Tamaño del badge (sm, md, lg)
    - showStatus (bool): Mostrar indicador de estado
    - showDomain (bool): Mostrar dominio debajo del nombre
    - href (string|null): Link opcional
    - clickable (bool): Añadir estilos de hover
--}}

@props([
    'tenant' => null,
    'size' => 'md',
    'showStatus' => false,
    'showDomain' => false,
    'href' => null,
    'clickable' => false,
])

@php
    // Tamaños
    $sizes = [
        'sm' => [
            'container' => 'gap-2',
            'logo' => 'w-8 h-8 text-xs',
            'name' => 'text-sm',
            'domain' => 'text-xs',
        ],
        'md' => [
            'container' => 'gap-3',
            'logo' => 'w-10 h-10 text-sm',
            'name' => 'text-base',
            'domain' => 'text-xs',
        ],
        'lg' => [
            'container' => 'gap-4',
            'logo' => 'w-12 h-12 text-base',
            'name' => 'text-lg',
            'domain' => 'text-sm',
        ],
    ];

    $sizeConfig = $sizes[$size] ?? $sizes['md'];

    // Colores de estado
    $statusColors = [
        'active' => 'bg-emerald-500',
        'suspended' => 'bg-red-500',
        'trial' => 'bg-amber-500',
        'inactive' => 'bg-gray-400',
    ];

    $status = $tenant->status ?? 'active';
    $statusColor = $statusColors[$status] ?? $statusColors['inactive'];

    // Iniciales del tenant
    $name = $tenant->name ?? 'Tenant';
    $initials = collect(explode(' ', $name))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->implode('');

    // Gradiente para el fondo del logo
    $gradients = [
        'bg-gradient-to-br from-blue-500 to-blue-700',
        'bg-gradient-to-br from-emerald-500 to-emerald-700',
        'bg-gradient-to-br from-purple-500 to-purple-700',
        'bg-gradient-to-br from-amber-500 to-amber-700',
        'bg-gradient-to-br from-pink-500 to-pink-700',
    ];
    $gradientIndex = $tenant ? (($tenant->id ?? 0) % count($gradients)) : 0;
    $gradient = $gradients[$gradientIndex];
@endphp

@php
    $wrapperClasses = 'inline-flex items-center ' . $sizeConfig['container'];
    if ($clickable || $href) {
        $wrapperClasses .= ' cursor-pointer hover:opacity-80 transition-opacity';
    }
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $wrapperClasses]) }}>
@else
<div {{ $attributes->merge(['class' => $wrapperClasses]) }}>
@endif

    {{-- Logo o iniciales --}}
    <div class="relative flex-shrink-0">
        @if($tenant && $tenant->logo_path && Storage::disk('public')->exists($tenant->logo_path))
            <img
                src="{{ Storage::url($tenant->logo_path) }}"
                alt="{{ $name }}"
                class="rounded-lg object-cover {{ $sizeConfig['logo'] }}"
            >
        @else
            <div class="flex items-center justify-center rounded-lg font-bold text-white {{ $gradient }} {{ $sizeConfig['logo'] }}">
                {{ strtoupper($initials) }}
            </div>
        @endif

        {{-- Indicador de estado --}}
        @if($showStatus)
            <span class="absolute -bottom-0.5 -right-0.5 block rounded-full ring-2 ring-white dark:ring-slate-800 {{ $statusColor }}"
                  style="width: {{ $size === 'sm' ? '8px' : ($size === 'lg' ? '14px' : '10px') }}; height: {{ $size === 'sm' ? '8px' : ($size === 'lg' ? '14px' : '10px') }};">
            </span>
        @endif
    </div>

    {{-- Información del tenant --}}
    <div class="min-w-0 flex-1">
        <p class="font-semibold text-gray-900 dark:text-white truncate {{ $sizeConfig['name'] }}">
            {{ $name }}
        </p>
        @if($showDomain && $tenant && $tenant->domain)
            <p class="text-gray-500 dark:text-gray-400 truncate {{ $sizeConfig['domain'] }}">
                {{ '@' . $tenant->domain }}
            </p>
        @endif
    </div>

@if($href)
</a>
@else
</div>
@endif

