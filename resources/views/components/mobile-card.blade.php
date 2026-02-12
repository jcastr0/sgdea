{{--
    Componente: Mobile Card (para mostrar datos en formato card en móvil)

    Props:
    - title: Título principal de la card
    - subtitle: Subtítulo
    - href: URL al hacer click
    - badge: Slot para badge de estado
--}}

@props([
    'title' => '',
    'subtitle' => '',
    'href' => null,
])

<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm ' .
               ($href ? 'cursor-pointer hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all ' : '')
]) }}
    @if($href)
        onclick="window.location='{{ $href }}'"
    @endif
>
    {{-- Header con título y badge --}}
    <div class="flex items-start justify-between gap-3 mb-3">
        <div class="flex-1 min-w-0">
            @if($title)
                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($badge)
            <div class="flex-shrink-0">
                {{ $badge }}
            </div>
        @endisset
    </div>

    {{-- Contenido --}}
    <div class="space-y-2">
        {{ $slot }}
    </div>

    {{-- Acciones (footer) --}}
    @isset($actions)
        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-slate-700 flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>

