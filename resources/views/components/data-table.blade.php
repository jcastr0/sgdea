{{--
    Componente: Data Table
    Uso:
    <x-data-table :columns="$columns" :sortField="$sortField" :sortDirection="$sortDirection">
        @foreach($items as $item)
            <x-data-table-row :item="$item" :columns="$columns">
                <!-- custom content -->
            </x-data-table-row>
        @endforeach
    </x-data-table>

    Props:
    - columns: Array de columnas ['field' => 'Título', ...]
    - sortField: Campo actual de ordenamiento
    - sortDirection: Dirección de ordenamiento (asc/desc)
    - sortable: Permitir ordenamiento (default: true)
    - hoverable: Efecto hover en filas (default: true)
    - striped: Filas alternadas (default: false)
    - loading: Mostrar estado de carga (default: false)
--}}

@props([
    'columns' => [],
    'sortField' => null,
    'sortDirection' => 'asc',
    'sortable' => true,
    'hoverable' => true,
    'striped' => false,
    'loading' => false,
])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    {{-- Vista de Tabla (Desktop) --}}
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-50 dark:bg-slate-800">
                <tr>
                    @foreach($columns as $field => $config)
                        @php
                            $label = is_array($config) ? ($config['label'] ?? $field) : $config;
                            $isSortable = is_array($config) ? ($config['sortable'] ?? $sortable) : $sortable;
                            $width = is_array($config) ? ($config['width'] ?? 'auto') : 'auto';
                            $align = is_array($config) ? ($config['align'] ?? 'left') : 'left';
                            $alignClass = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'][$align] ?? 'text-left';
                        @endphp
                        <th scope="col"
                            class="px-4 py-3 {{ $alignClass }} text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider {{ $width !== 'auto' ? 'w-' . $width : '' }}"
                            @if($isSortable && $sortable)
                                wire:click="sortBy('{{ $field }}')"
                                style="cursor: pointer"
                            @endif>
                            <div class="flex items-center gap-2 {{ $align === 'right' ? 'justify-end' : ($align === 'center' ? 'justify-center' : '') }}">
                                <span>{{ $label }}</span>
                                @if($isSortable && $sortable)
                                    <span class="flex flex-col">
                                        <svg class="w-3 h-3 {{ $sortField === $field && $sortDirection === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                        </svg>
                                        <svg class="w-3 h-3 -mt-1 {{ $sortField === $field && $sortDirection === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700"
                   @if($loading) wire:loading.class="opacity-50" @endif>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Vista de Cards (Móvil) --}}
    <div class="lg:hidden space-y-4" @if($loading) wire:loading.class="opacity-50" @endif>
        {{ $mobileSlot ?? $slot }}
    </div>

    {{-- Loading overlay --}}
    @if($loading)
        <div wire:loading class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 flex items-center justify-center">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    @endif
</div>

