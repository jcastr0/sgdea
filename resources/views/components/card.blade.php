@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'md',
    'shadow' => 'md',
    'rounded' => 'lg',
    'hover' => false,
    'border' => true,
    'headerActions' => null,
    'footer' => null,
])

@php
    // Padding
    $paddings = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];

    // Sombras
    $shadows = [
        'none' => '',
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
    ];

    // Bordes redondeados
    $roundeds = [
        'none' => '',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
    ];

    $baseClasses = 'bg-white dark:bg-slate-800 transition-all duration-200';
    $baseClasses .= ' ' . ($paddings[$padding] ?? $paddings['md']);
    $baseClasses .= ' ' . ($shadows[$shadow] ?? $shadows['md']);
    $baseClasses .= ' ' . ($roundeds[$rounded] ?? $roundeds['lg']);

    if ($border) {
        $baseClasses .= ' border border-gray-200 dark:border-slate-700';
    }

    if ($hover) {
        $baseClasses .= ' hover:shadow-lg hover:border-gray-300 dark:hover:border-slate-600 cursor-pointer';
    }
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{-- Header con título --}}
    @if($title || $headerActions)
        <div class="flex items-center justify-between {{ ($title || $subtitle) ? 'mb-4 pb-4 border-b border-gray-200 dark:border-slate-700' : '' }}">
            <div>
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h3>
                @endif
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            @if($headerActions)
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    {{-- Contenido principal --}}
    <div class="card-body">
        {{ $slot }}
    </div>

    {{-- Footer opcional --}}
    @if($footer)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700">
            {{ $footer }}
        </div>
    @endif
</div>
