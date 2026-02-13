{{--
    Componente: Admin Stat Card
    Uso: <x-admin.stat-card
            title="Total Facturas"
            :value="$totalFacturas"
            icon="document"
            color="blue"
            :change="12.5"
            changeLabel="vs mes anterior"
         />

    Props:
    - title (string): Etiqueta/título de la estadística
    - value (string|number): Valor principal a mostrar
    - icon (string): Nombre del icono (document, users, currency, chart, folder, clock)
    - color (string): Color del icono (blue, green, yellow, red, purple, indigo, pink, gray)
    - change (number|null): Porcentaje de cambio (positivo o negativo)
    - changeLabel (string): Texto descriptivo del cambio
    - href (string|null): Link opcional al hacer clic
    - loading (bool): Mostrar estado de carga
--}}

@props([
    'title' => 'Estadística',
    'value' => '0',
    'icon' => 'chart',
    'color' => 'blue',
    'change' => null,
    'changeLabel' => '',
    'href' => null,
    'loading' => false,
])

@php
    // Colores de fondo para el icono
    $bgColors = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/30',
        'green' => 'bg-emerald-100 dark:bg-emerald-900/30',
        'yellow' => 'bg-amber-100 dark:bg-amber-900/30',
        'red' => 'bg-red-100 dark:bg-red-900/30',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30',
        'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30',
        'pink' => 'bg-pink-100 dark:bg-pink-900/30',
        'gray' => 'bg-gray-100 dark:bg-gray-700',
    ];

    // Colores del icono
    $iconColors = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-emerald-600 dark:text-emerald-400',
        'yellow' => 'text-amber-600 dark:text-amber-400',
        'red' => 'text-red-600 dark:text-red-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        'pink' => 'text-pink-600 dark:text-pink-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
    ];

    $bgColor = $bgColors[$color] ?? $bgColors['blue'];
    $iconColor = $iconColors[$color] ?? $iconColors['blue'];

    // Determinar si el cambio es positivo o negativo
    $isPositive = $change !== null && $change >= 0;
    $changeColor = $isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400';
    $changeIcon = $isPositive ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 transition-all duration-200 hover:shadow-md']) }}>
    @if($href)
    <a href="{{ $href }}" class="block">
    @endif

    <div class="flex items-center justify-between">
        {{-- Contenido principal --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                {{ $title }}
            </p>

            @if($loading)
                {{-- Estado de carga --}}
                <div class="mt-2 h-8 w-24 bg-gray-200 dark:bg-slate-700 rounded animate-pulse"></div>
            @else
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white truncate">
                    {{ $value }}
                </p>
            @endif

            {{-- Indicador de cambio --}}
            @if($change !== null && !$loading)
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center {{ $changeColor }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $changeIcon }}"/>
                        </svg>
                        <span class="text-sm font-medium">{{ abs($change) }}%</span>
                    </span>
                    @if($changeLabel)
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $changeLabel }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Icono --}}
        <div class="flex-shrink-0 ml-4">
            <div class="p-3 rounded-xl {{ $bgColor }}">
                @switch($icon)
                    @case('document')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        @break
                    @case('users')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        @break
                    @case('currency')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @break
                    @case('chart')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        @break
                    @case('folder')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        @break
                    @case('clock')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @break
                    @case('building')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        @break
                    @case('database')
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                        @break
                    @default
                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                @endswitch
            </div>
        </div>
    </div>

    @if($href)
    </a>
    @endif
</div>

