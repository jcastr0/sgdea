{{--
    Componente: Pagination
    Uso: <x-pagination :paginator="$items" />

    Props:
    - paginator: Instancia del paginador de Laravel
    - showInfo: Mostrar información de registros (default: true)
    - showPerPage: Mostrar selector de items por página (default: true)
    - perPageOptions: Array de opciones para items por página
    - simple: Usar paginación simple sin números (default: false)
--}}

@props([
    'paginator',
    'showInfo' => true,
    'showPerPage' => true,
    'perPageOptions' => [10, 15, 25, 50, 100],
    'simple' => false,
])

@if($paginator->hasPages() || $showInfo || $showPerPage)
<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row items-center justify-between gap-4 py-4']) }}>

    {{-- Info y selector de items por página (izquierda) --}}
    <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
        {{-- Información de registros --}}
        @if($showInfo)
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
                Mostrando
                <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span>
                a
                <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span>
                de
                <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
                resultados
            </p>
        @endif

        {{-- Selector de items por página --}}
        @if($showPerPage)
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm text-gray-600 dark:text-gray-400">Mostrar</label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-md text-sm py-1 pl-2 pr-8 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Controles de paginación (derecha) --}}
    @if($paginator->hasPages())
        <nav class="flex items-center gap-1" aria-label="Paginación">
            {{-- Botón Anterior --}}
            @if($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-10 h-10 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <button
                    wire:click="previousPage"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            {{-- Números de página (no en modo simple) --}}
            @if(!$simple)
                <div class="hidden sm:flex items-center gap-1">
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $window = 2; // Páginas a mostrar a cada lado de la actual

                        // Calcular rango de páginas a mostrar
                        $start = max(1, $currentPage - $window);
                        $end = min($lastPage, $currentPage + $window);

                        // Ajustar para mostrar al menos 5 páginas si es posible
                        if ($end - $start < 4 && $lastPage >= 5) {
                            if ($start == 1) {
                                $end = min($lastPage, 5);
                            } elseif ($end == $lastPage) {
                                $start = max(1, $lastPage - 4);
                            }
                        }
                    @endphp

                    {{-- Primera página --}}
                    @if($start > 1)
                        <button
                            wire:click="gotoPage(1)"
                            class="inline-flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm font-medium"
                        >
                            1
                        </button>
                        @if($start > 2)
                            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-500 dark:text-gray-400">...</span>
                        @endif
                    @endif

                    {{-- Páginas del rango --}}
                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $currentPage)
                            <span class="inline-flex items-center justify-center w-10 h-10 text-white bg-blue-600 dark:bg-blue-500 rounded-lg font-medium text-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }})"
                                class="inline-flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm font-medium"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endfor

                    {{-- Última página --}}
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="inline-flex items-center justify-center w-10 h-10 text-gray-500 dark:text-gray-400">...</span>
                        @endif
                        <button
                            wire:click="gotoPage({{ $lastPage }})"
                            class="inline-flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm font-medium"
                        >
                            {{ $lastPage }}
                        </button>
                    @endif
                </div>

                {{-- Indicador de página actual en móvil --}}
                <div class="flex sm:hidden items-center px-3">
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Página <span class="font-semibold">{{ $currentPage }}</span> de <span class="font-semibold">{{ $lastPage }}</span>
                    </span>
                </div>
            @endif

            {{-- Botón Siguiente --}}
            @if($paginator->hasMorePages())
                <button
                    wire:click="nextPage"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-10 h-10 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span class="inline-flex items-center justify-center w-10 h-10 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </nav>
    @endif
</div>
@endif

