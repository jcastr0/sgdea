{{--
    Componente: Livewire Filter Bar

    Barra de filtros reutilizable para componentes Livewire.
    Este es un componente de PRESENTACIÓN ÚNICAMENTE - no contiene lógica.
    Todos los wire: bindings se pasan como slots desde el componente padre.

    Uso:
    <x-livewire.filter-bar
        :showFilters="$showFilters"
        :activeFiltersCount="$this->activeFiltersCount()"
        :hasActiveFilters="$this->hasActiveFilters()"
        searchPlaceholder="Buscar por nombre o NIT..."
    >
        -- Slot search: Input de búsqueda personalizado --
        <x-slot:search>
            <input type="text" wire:model.live.debounce.300ms="search" ...>
        </x-slot:search>

        -- Slot filters: Panel de filtros avanzados --
        <x-slot:filters>
            <div>
                <label>Estado</label>
                <select wire:model.live="estado">...</select>
            </div>
        </x-slot:filters>

        -- Slot actions: Botones de acción --
        <x-slot:actions>
            <a href="..." class="...">Nuevo</a>
        </x-slot:actions>

        -- Slot default: Contenido adicional (indicadores de selección, etc)  --
        ...
    </x-livewire.filter-bar>

    Props:
    - showFilters: Boolean - estado del panel de filtros (abierto/cerrado)
    - activeFiltersCount: Int - número de filtros activos (para badge)
    - hasActiveFilters: Boolean - si hay filtros activos (para mostrar "limpiar")
    - searchPlaceholder: String - placeholder del input de búsqueda
    - toggleFiltersMethod: String - nombre del metodo Livewire para toggle (default: 'toggleFilters')
    - clearFiltersMethod: String - nombre del metodo Livewire para limpiar (default: 'clearFilters')

    Slots:
    - search: Input de búsqueda (opcional, se provee uno por defecto)
    - filters: Panel de filtros avanzados
    - actions: Botones de acción adicionales
    - default: Contenido adicional debajo de la barra
--}}

@props([
    'showFilters' => false,
    'activeFiltersCount' => 0,
    'hasActiveFilters' => false,
    'searchPlaceholder' => 'Buscar...',
    'toggleFiltersMethod' => 'toggleFilters',
    'clearFiltersMethod' => 'clearFilters',
])

<div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 mb-6">
    {{-- Barra principal --}}
    <div class="p-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        {{-- Búsqueda principal --}}
        <div class="flex-1 w-full sm:max-w-md">
            @if(isset($search))
                {{ $search }}
            @else
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="{{ $searchPlaceholder }}"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            @endif
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-3 w-full sm:w-auto flex-wrap">
            {{-- Botón filtros (solo si hay slot filters) --}}
            @if(isset($filters))
            <button wire:click="{{ $toggleFiltersMethod }}"
                    class="cursor-pointer flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ $showFilters ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700' : '' }}">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtros</span>
                @if($activeFiltersCount > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full">
                        {{ $activeFiltersCount }}
                    </span>
                @endif
            </button>
            @endif

            {{-- Slot para acciones adicionales --}}
            {{ $actions ?? '' }}
        </div>
    </div>

    {{-- Panel de filtros avanzados --}}
    @if(isset($filters) && $showFilters)
    <div class="border-t border-gray-200 dark:border-slate-700">
        <div class="p-4 bg-gray-50 dark:bg-slate-700/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{ $filters }}
            </div>

            {{-- Botón limpiar filtros --}}
            @if($hasActiveFilters)
            <div class="mt-4 flex justify-end">
                <button wire:click="{{ $clearFiltersMethod }}"
                        class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Slot para contenido adicional (selección, mensajes, etc) --}}
    {{ $slot }}
</div>

