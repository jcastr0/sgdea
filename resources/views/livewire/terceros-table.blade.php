<div>
    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 z-50 items-center justify-center bg-black/20 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-xl flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Cargando...</span>
        </div>
    </div>

    {{-- Mensajes Flash de éxito/error --}}
    @if (session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-start gap-3"
         x-data="{ show: true }"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-semibold text-green-800 dark:text-green-300">¡Éxito!</h4>
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="cursor-pointer flex-shrink-0 text-green-500 hover:text-green-700 dark:hover:text-green-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-3"
         x-data="{ show: true }"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-semibold text-red-800 dark:text-red-300">Error</h4>
            <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="cursor-pointer flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Header con estadísticas --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total terceros</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['activos'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Activos</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['inactivos'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Inactivos</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra de acciones y búsqueda --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 mb-6">
        <div class="p-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            {{-- Búsqueda principal --}}
            <div class="flex-1 w-full sm:max-w-md">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar por nombre o email..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Botón filtros --}}
                <button wire:click="toggleFilters"
                        class="cursor-pointer flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ $showFilters ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700' : '' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtros</span>
                    @if($this->activeFiltersCount() > 0)
                        <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full">
                            {{ $this->activeFiltersCount() }}
                        </span>
                    @endif
                </button>

                {{-- Botón merge (solo si hay seleccionados) --}}
                @if(is_array($selectedTerceros) && count($selectedTerceros) >= 2)
                <button wire:click="openMergeModal"
                        class="cursor-pointer flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span class="text-sm font-medium">Fusionar ({{ count($selectedTerceros) }})</span>
                </button>
                @elseif(is_array($selectedTerceros) && count($selectedTerceros) == 1)
                <span class="flex items-center gap-2 px-4 py-2.5 bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Selecciona 1 más para fusionar
                </span>
                @endif

                {{-- Botón nuevo tercero --}}
                <a href="{{ route('terceros.create') }}"
                   class="cursor-pointer flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-sm font-medium">Nuevo Tercero</span>
                </a>
            </div>
        </div>

        {{-- Panel de filtros avanzados --}}
        @if($showFilters)
        <div class="p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- NIT --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIT</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="nit"
                           placeholder="Buscar por NIT..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select wire:model.live="estado"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                {{-- Fecha desde --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Creado desde</label>
                    <input type="date"
                           wire:model.live="fechaDesde"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Fecha hasta --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Creado hasta</label>
                    <input type="date"
                           wire:model.live="fechaHasta"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Botón limpiar filtros --}}
            @if($this->hasActiveFilters())
            <div class="mt-4 flex justify-end">
                <button wire:click="clearFilters"
                        class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </button>
            </div>
            @endif
        </div>
        @endif

        {{-- Indicador de selección --}}
        @if(count($selectedTerceros) > 0)
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border-t border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-700 dark:text-blue-300">
                    <strong>{{ count($selectedTerceros) }}</strong> tercero(s) seleccionado(s) para fusión
                </span>
                <button wire:click="cancelSelection"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Cancelar selección
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Tabla de terceros --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        @if($terceros->count() > 0)
        {{-- Tabla Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <span class="sr-only">Seleccionar</span>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('nit')" class="cursor-pointer flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                NIT
                                @if($sortField === 'nit')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('nombre_razon_social')" class="cursor-pointer flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Nombre/Razón Social
                                @if($sortField === 'nombre_razon_social')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Contacto</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Facturas</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total Facturado</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @foreach($terceros as $tercero)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors {{ in_array($tercero->id, $selectedTerceros) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        <td class="px-4 py-3">
                            <input type="checkbox"
                                   wire:click="toggleSelection({{ $tercero->id }})"
                                   @checked(in_array($tercero->id, $selectedTerceros))
                                   class="cursor-pointer h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $tercero->nit }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('terceros.show', $tercero) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $tercero->nombre_razon_social }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">
                                @if($tercero->email)
                                <div class="text-gray-600 dark:text-gray-400">{{ $tercero->email }}</div>
                                @endif
                                @if($tercero->telefono)
                                <div class="text-gray-500 dark:text-gray-500">{{ $tercero->telefono }}</div>
                                @endif
                                @if(!$tercero->email && !$tercero->telefono)
                                <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $tercero->facturas_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-medium text-gray-900 dark:text-white">
                                ${{ number_format($tercero->facturas_sum_total_pagar ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($tercero->estado === 'activo')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('terceros.show', $tercero) }}"
                                   class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                   title="Ver detalle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('terceros.edit', $tercero) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors"
                                   title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cards Mobile --}}
        <div class="md:hidden divide-y divide-gray-200 dark:divide-slate-700">
            @foreach($terceros as $tercero)
            <div class="p-4 {{ in_array($tercero->id, $selectedTerceros) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex items-start gap-3">
                    <input type="checkbox"
                           wire:click="toggleSelection({{ $tercero->id }})"
                           @checked(in_array($tercero->id, $selectedTerceros))
                           class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('terceros.show', $tercero) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600">
                                    {{ $tercero->nombre_razon_social }}
                                </a>
                                <p class="text-sm text-gray-500 font-mono">{{ $tercero->nit }}</p>
                            </div>
                            @if($tercero->estado === 'activo')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Activo</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600">Inactivo</span>
                            @endif
                        </div>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Facturas:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $tercero->facturas_count ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Total:</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($tercero->facturas_sum_total_pagar ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('terceros.show', $tercero) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('terceros.edit', $tercero) }}" class="text-sm text-amber-600 dark:text-amber-400 hover:underline">Editar</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Mostrar</span>
                    <select wire:model.live="perPage" class="px-2 py-1 border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700 text-sm">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">por página</span>
                </div>
                {{ $terceros->links() }}
            </div>
        </div>
        @else
        {{-- Estado vacío --}}
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay terceros</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                @if($this->hasActiveFilters())
                    No se encontraron terceros con los filtros aplicados.
                @else
                    Comienza agregando tu primer tercero.
                @endif
            </p>
            @if($this->hasActiveFilters())
                <button wire:click="clearFilters" class="text-blue-600 dark:text-blue-400 hover:underline">Limpiar filtros</button>
            @else
                <a href="{{ route('terceros.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Tercero
                </a>
            @endif
        </div>
        @endif
    </div>

    {{-- Modal de Merge --}}
    @if($showMergeModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm cursor-pointer" wire:click="closeMergeModal"></div>

        {{-- Modal --}}
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Fusionar Terceros
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Selecciona el tercero dominante. Los demás serán eliminados y sus facturas reasignadas.
                </p>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4 overflow-y-auto max-h-[50vh]">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    <span class="text-blue-600 dark:text-blue-400">Paso 1:</span> Selecciona el tercero que deseas conservar:
                </p>

                <div class="space-y-2">
                    @foreach($mergeOptions as $option)
                    <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition-all duration-200
                        {{ $mergeTerceroId == $option['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500' : 'border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-gray-300' }}">
                        <input type="radio"
                               wire:model.live="mergeTerceroId"
                               value="{{ $option['id'] }}"
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $option['nombre'] }}</p>
                            <p class="text-sm text-gray-500 font-mono">NIT: {{ $option['nit'] }}</p>
                            <div class="mt-1 flex flex-wrap gap-3 text-xs">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $option['facturas_count'] }} facturas
                                </span>
                                @if($option['email'])
                                <span class="text-gray-500">{{ $option['email'] }}</span>
                                @endif
                            </div>
                        </div>
                        @if($mergeTerceroId == $option['id'])
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                            ✓ Dominante
                        </span>
                        @endif
                    </label>
                    @endforeach
                </div>

                {{-- Resumen de la fusión --}}
                @if($mergeTerceroId)
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <strong>Resumen:</strong> Se eliminarán <strong>{{ count($selectedTerceros) - 1 }}</strong> tercero(s) y sus facturas se reasignarán al tercero seleccionado.
                    </p>
                </div>
                @endif

                <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-amber-700 dark:text-amber-300">
                            <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. Los terceros no seleccionados serán eliminados permanentemente.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex justify-end gap-3">
                <button wire:click="closeMergeModal"
                        type="button"
                        class="cursor-pointer px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                    Cancelar
                </button>
                <button wire:click="executeMerge"
                        type="button"
                        @if(!$mergeTerceroId) disabled @endif
                        class="cursor-pointer px-4 py-2 bg-amber-600 hover:bg-amber-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="executeMerge">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="executeMerge">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="executeMerge">Fusionar Terceros</span>
                    <span wire:loading wire:target="executeMerge">Fusionando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

