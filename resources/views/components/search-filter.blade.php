{{--
    Componente: Search Filter
    Uso: <x-search-filter wire:model.live.debounce.300ms="search" placeholder="Buscar..." />

    Props:
    - placeholder: Texto placeholder del input
    - size: sm, md, lg (default: md)
    - icon: Mostrar icono de búsqueda (default: true)
    - clearable: Mostrar botón para limpiar (default: true)
--}}

@props([
    'placeholder' => 'Buscar...',
    'size' => 'md',
    'icon' => true,
    'clearable' => true,
])

@php
    $sizeClasses = [
        'sm' => 'py-1.5 text-sm',
        'md' => 'py-2 text-sm',
        'lg' => 'py-3 text-base',
    ][$size] ?? 'py-2 text-sm';

    $iconSizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
    ][$size] ?? 'h-5 w-5';
@endphp

<div class="relative w-full" x-data="{ focused: false }">
    {{-- Icono de búsqueda --}}
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="{{ $iconSizes }} text-gray-400 dark:text-gray-500 transition-colors"
                 :class="{ 'text-blue-500 dark:text-blue-400': focused }"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    @endif

    {{-- Input de búsqueda --}}
    <input
        type="search"
        {{ $attributes->merge([
            'class' => 'w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg ' .
                       ($icon ? 'pl-10 ' : 'pl-4 ') .
                       ($clearable ? 'pr-10 ' : 'pr-4 ') .
                       $sizeClasses . ' ' .
                       'text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 ' .
                       'focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 ' .
                       'transition-all duration-200 ease-in-out'
        ]) }}
        placeholder="{{ $placeholder }}"
        @focus="focused = true"
        @blur="focused = false"
    />

    {{-- Botón para limpiar (solo visible cuando hay contenido) --}}
    @if($clearable)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center"
             x-data="{ hasValue: $wire?.{{ $attributes->whereStartsWith('wire:model')->first() ?? 'search' }} !== '' }"
             x-show="hasValue || $el.previousElementSibling.value !== ''"
             x-transition>
            <button type="button"
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                    @click="$el.closest('.relative').querySelector('input').value = ''; $el.closest('.relative').querySelector('input').dispatchEvent(new Event('input'))">
                <svg class="{{ $iconSizes }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
</div>

