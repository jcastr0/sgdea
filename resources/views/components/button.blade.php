@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
])

@php
    // Clases base del botón
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    // Variantes de color
    $variants = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600',
        'secondary' => 'bg-slate-600 hover:bg-slate-700 text-white focus:ring-slate-500 dark:bg-slate-500 dark:hover:bg-slate-600',
        'success' => 'bg-green-500 hover:bg-green-600 text-white focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-500 dark:bg-amber-600 dark:hover:bg-amber-700',
        'info' => 'bg-cyan-500 hover:bg-cyan-600 text-white focus:ring-cyan-500 dark:bg-cyan-600 dark:hover:bg-cyan-700',
        'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500 dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white',
        'outline-secondary' => 'border-2 border-slate-300 text-slate-700 hover:bg-slate-100 focus:ring-slate-400 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800',
        'ghost' => 'text-slate-600 hover:bg-slate-100 focus:ring-slate-400 dark:text-slate-300 dark:hover:bg-slate-800',
        'link' => 'text-blue-600 hover:text-blue-800 hover:underline focus:ring-blue-500 dark:text-blue-400 dark:hover:text-blue-300',
    ];

    // Tamaños
    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs gap-1',
        'sm' => 'px-3 py-2 text-sm gap-1.5',
        'md' => 'px-4 py-2.5 text-sm gap-2',
        'lg' => 'px-5 py-3 text-base gap-2',
        'xl' => 'px-6 py-3.5 text-lg gap-2.5',
    ];

    // Construir clases finales
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);

    if ($fullWidth) {
        $classes .= ' w-full';
    }
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif

        <span>{{ $slot }}</span>

        @if($icon && $iconPosition === 'right' && !$loading)
            {!! $icon !!}
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $disabled || $loading ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($loading)
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif

        <span>{{ $slot }}</span>

        @if($icon && $iconPosition === 'right' && !$loading)
            {!! $icon !!}
        @endif
    </button>
@endif
