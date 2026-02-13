<div class="space-y-6">
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

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Facturas --}}
        <x-card class="!p-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($estadisticas['total']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Facturas</p>
                </div>
            </div>
        </x-card>

        {{-- Total Neto Facturado --}}
        <x-card class="!p-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($estadisticas['total_neto'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Neto Facturado</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500" title="Facturas + Notas Débito - Notas Crédito">
                        (+FV +ND -NC)
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Promedio --}}
        <x-card class="!p-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($estadisticas['promedio_por_factura'], 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Promedio</p>
                </div>
            </div>
        </x-card>

        {{-- Por Estado --}}
        <x-card class="!p-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach($estadisticas['por_estado'] as $estado => $count)
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $estado === 'aceptado' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $estado === 'pendiente' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                {{ $estado === 'rechazado' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                {{ ucfirst($estado) }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Por Estado</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Desglose de Totales por Tipo de Documento --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Facturas de Venta (+) --}}
        <x-card class="!p-3 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold text-blue-600">+</span>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Facturas de Venta</p>
                        <p class="text-xs text-gray-500">{{ $estadisticas['por_tipo']['FACTURA DE VENTA'] ?? 0 }} documentos</p>
                    </div>
                </div>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">${{ number_format($estadisticas['total_facturas_venta'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </x-card>

        {{-- Notas Débito (+) --}}
        <x-card class="!p-3 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold text-green-600">+</span>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Notas Débito</p>
                        <p class="text-xs text-gray-500">{{ $estadisticas['por_tipo']['NOTA DEBITO'] ?? 0 }} documentos</p>
                    </div>
                </div>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">${{ number_format($estadisticas['total_notas_debito'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </x-card>

        {{-- Notas Crédito (-) --}}
        <x-card class="!p-3 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold text-red-600">−</span>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Notas Crédito</p>
                        <p class="text-xs text-gray-500">{{ $estadisticas['por_tipo']['NOTA CREDITO'] ?? 0 }} documentos</p>
                    </div>
                </div>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">−${{ number_format($estadisticas['total_notas_credito'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </x-card>
    </div>

    {{-- Barra de filtros usando el componente reutilizable --}}
    <x-livewire.filter-bar
        :showFilters="$showFilters"
        :activeFiltersCount="$this->activeFiltersCount()"
        :hasActiveFilters="$this->hasActiveFilters()"
        searchPlaceholder="Buscar por número, CUFE, cliente..."
    >
        {{-- Slot search: Input de búsqueda --}}
        <x-slot:search>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por número, CUFE, cliente..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </x-slot:search>

        {{-- Slot filters: Panel de filtros avanzados --}}
        <x-slot:filters>
            {{-- Número de Factura --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nº Factura</label>
                <input type="text"
                       wire:model.live.debounce.300ms="numeroFactura"
                       placeholder="Ej: FAC-001"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- CUFE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CUFE</label>
                <input type="text"
                       wire:model.live.debounce.300ms="cufe"
                       placeholder="Código CUFE..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tercero/Cliente --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cliente</label>
                <select wire:model.live="terceroId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los clientes</option>
                    @foreach($terceros as $tercero)
                        <option value="{{ $tercero->id }}">{{ $tercero->nombre_razon_social }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                <select wire:model.live="estado"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="aceptado">✓ Aceptado</option>
                    <option value="pendiente">⏳ Pendiente</option>
                    <option value="rechazado">✗ Rechazado</option>
                </select>
            </div>

            {{-- Fecha Desde --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Desde</label>
                <input type="date"
                       wire:model.live="fechaDesde"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Fecha Hasta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Hasta</label>
                <input type="date"
                       wire:model.live="fechaHasta"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tiene PDF --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Documentación</label>
                <select wire:model.live="tienePdf"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Cualquiera</option>
                    <option value="1">📄 Con PDF</option>
                    <option value="0">⚠️ Sin PDF</option>
                </select>
            </div>

            {{-- Motonave --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motonave</label>
                <input type="text"
                       wire:model.live.debounce.300ms="motonave"
                       placeholder="Nombre motonave..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
        </x-slot:filters>

        {{-- Slot actions: vacío por ahora, no hay acciones especiales --}}
        <x-slot:actions>
            {{-- Las facturas se crean desde importación, no hay botón de crear --}}
        </x-slot:actions>
    </x-livewire.filter-bar>

    {{-- Tabla Desktop --}}
    <div class="hidden lg:block" wire:loading.class="opacity-50">
        <x-card padding="none" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700"
                                wire:click="sortBy('numero_factura')">
                                <div class="flex items-center gap-2">
                                    Nº Factura
                                    @if($sortField === 'numero_factura')
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                            @else
                                                <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700"
                                wire:click="sortBy('fecha_factura')">
                                <div class="flex items-center gap-2">
                                    Fecha
                                    @if($sortField === 'fecha_factura')
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                            @else
                                                <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700"
                                wire:click="sortBy('total_pagar')">
                                <div class="flex items-center justify-end gap-2">
                                    Total
                                    @if($sortField === 'total_pagar')
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                            @else
                                                <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                PDF
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($facturas as $factura)
                            @php
                                $tipoDoc = $factura->tipo_documento ?? 'Factura de Venta';
                                $esNC = str_contains(strtolower($tipoDoc), 'crédito') || str_contains(strtolower($tipoDoc), 'credito');
                                $esND = str_contains(strtolower($tipoDoc), 'débito') || str_contains(strtolower($tipoDoc), 'debito');
                                $badgeColor = $esNC ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' :
                                             ($esND ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' :
                                             'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400');
                                $tipoCorto = $esNC ? 'NC' : ($esND ? 'ND' : 'FV');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                {{-- Número de Factura --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded {{ $badgeColor }}">{{ $tipoCorto }}</span>
                                            <a href="{{ route('facturas.show', $factura) }}"
                                               class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                {{ $factura->numero_factura }}
                                            </a>
                                        </div>
                                        @if($factura->cufe)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate max-w-[150px]" title="{{ $factura->cufe }}">
                                                {{ Str::limit($factura->cufe, 20) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Cliente --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $factura->tercero->nombre_razon_social ?? 'Sin cliente' }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $factura->tercero->nit ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Fecha --}}
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $factura->fecha_factura?->format('d/m/Y') ?? '-' }}
                                </td>

                                {{-- Total --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    @if($esNC)
                                        <span class="text-sm font-semibold text-red-600 dark:text-red-400" title="Nota Crédito - Resta del total">
                                            −${{ number_format($factura->total_pagar, 0, ',', '.') }}
                                        </span>
                                    @elseif($esND)
                                        <span class="text-sm font-semibold text-green-600 dark:text-green-400" title="Nota Débito - Suma al total">
                                            +${{ number_format($factura->total_pagar, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white" title="Factura de Venta - Suma al total">
                                            ${{ number_format($factura->total_pagar, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <x-status-badge :status="$factura->estado" />
                                </td>

                                {{-- PDF --}}
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($factura->pdf_path)
                                        <a href="{{ route('facturas.pdf', $factura) }}"
                                           target="_blank"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors"
                                           title="Ver PDF">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500" title="Sin PDF">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('facturas.show', $factura) }}"
                                           class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                           title="Ver detalle">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        {{-- Botón Editar deshabilitado - Las facturas no se editan manualmente
                                        <a href="{{ route('facturas.edit', $factura) }}"
                                           class="p-2 text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                           title="Editar">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No se encontraron facturas</p>
                                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Cards Móvil --}}
    <div class="lg:hidden space-y-4" wire:loading.class="opacity-50">
        @forelse($facturas as $factura)
            @php
                $tipoDocMobile = $factura->tipo_documento ?? 'Factura de Venta';
                $esNCMobile = str_contains(strtolower($tipoDocMobile), 'crédito') || str_contains(strtolower($tipoDocMobile), 'credito');
                $esNDMobile = str_contains(strtolower($tipoDocMobile), 'débito') || str_contains(strtolower($tipoDocMobile), 'debito');
                $tipoCortoMobile = $esNCMobile ? 'NC' : ($esNDMobile ? 'ND' : 'FV');
            @endphp
            <x-mobile-card
                :title="$tipoCortoMobile . ' ' . $factura->numero_factura"
                :subtitle="$factura->tercero->nombre_razon_social ?? 'Sin cliente'"
                :href="route('facturas.show', $factura)"
            >
                <x-slot:badge>
                    <x-status-badge :status="$factura->estado" size="xs" />
                </x-slot:badge>

                <x-data-line label="Fecha" :value="$factura->fecha_factura?->format('d/m/Y') ?? '-'" />
                <x-data-line label="NIT" :value="$factura->tercero->nit ?? '-'" />
                <x-data-line label="Total">
                    @if($esNCMobile)
                        <span class="font-bold text-red-600 dark:text-red-400" title="Nota Crédito - Resta">
                            −${{ number_format($factura->total_pagar, 0, ',', '.') }}
                        </span>
                    @elseif($esNDMobile)
                        <span class="font-bold text-green-600 dark:text-green-400" title="Nota Débito - Suma">
                            +${{ number_format($factura->total_pagar, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="font-bold text-blue-600 dark:text-blue-400">
                            ${{ number_format($factura->total_pagar, 0, ',', '.') }}
                        </span>
                    @endif
                </x-data-line>

                <x-slot:actions>
                    @if($factura->pdf_path)
                        <a href="{{ route('facturas.pdf', $factura) }}"
                           target="_blank"
                           class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-700">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Ver PDF
                        </a>
                    @endif
                    {{-- Botón Editar deshabilitado - Las facturas no se editan manualmente
                    <a href="{{ route('facturas.edit', $factura) }}"
                       class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 ml-auto">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    --}}
                </x-slot:actions>
            </x-mobile-card>
        @empty
            <x-card class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No se encontraron facturas</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
            </x-card>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($facturas->hasPages() || $facturas->total() > 0)
        <x-pagination :paginator="$facturas" />
    @endif
</div>

