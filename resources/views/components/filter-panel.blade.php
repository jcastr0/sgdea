{{--
    Componente: Filter Panel (Panel colapsable de filtros avanzados)

    Props:
    - title: Título del panel
    - open: Estado inicial (abierto/cerrado)
    - activeCount: Número de filtros activos
--}}

@props([
    'title' => 'Filtros Avanzados',
    'open' => false,
    'activeCount' => 0,
])

<div x-data="{ isOpen: {{ $open ? 'true' : 'false' }} }"
     {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden']) }}>

    {{-- Header (siempre visible) --}}
    <button type="button"
            @click="isOpen = !isOpen"
            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span class="font-medium text-gray-900 dark:text-white">{{ $title }}</span>
            @if($activeCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
                    {{ $activeCount }}
                </span>
            @endif
        </div>
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200"
             :class="{ 'rotate-180': isOpen }"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenido colapsable --}}
    <div x-show="isOpen"
         x-collapse
         x-cloak>
        <div class="px-4 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $slot }}
        </div>

        {{-- Footer con acciones --}}
        @isset($actions)
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>

