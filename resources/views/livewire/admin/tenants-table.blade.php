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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Activos</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Suspendidos</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['suspended'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">En Prueba</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['trial'] }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <x-livewire.filter-bar
        :showFilters="$showFilters"
        :activeFiltersCount="$this->activeFiltersCount()"
        :hasActiveFilters="$this->hasActiveFilters()"
        searchPlaceholder="Buscar por nombre, dominio..."
    >
        {{-- Slot search: Input de búsqueda --}}
        <x-slot:search>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por nombre, dominio..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </x-slot:search>

        {{-- Slot filters: Panel de filtros avanzados --}}
        <x-slot:filters>
            {{-- Filtro por Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                <select wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="active">Activos</option>
                    <option value="suspended">Suspendidos</option>
                    <option value="trial">En Prueba</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>

            {{-- Filtro por Fecha Desde --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Creado desde</label>
                <input type="date"
                       wire:model.live="fechaDesde"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Filtro por Fecha Hasta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Creado hasta</label>
                <input type="date"
                       wire:model.live="fechaHasta"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
        </x-slot:filters>

        {{-- Slot actions: Botón de nuevo tenant --}}
        <x-slot:actions>
            <a href="{{ route('admin.tenants.create') }}"
               class="cursor-pointer flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-sm font-medium">Nuevo Tenant</span>
            </a>
        </x-slot:actions>
    </x-livewire.filter-bar>

    {{-- Tabla Desktop --}}
    <div class="hidden md:block bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                Empresa
                                @if($sortField === 'name')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Dominio
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('users_count')">
                            <div class="flex items-center justify-center gap-1">
                                Usuarios
                                @if($sortField === 'users_count')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('facturas_count')">
                            <div class="flex items-center justify-center gap-1">
                                Facturas
                                @if($sortField === 'facturas_count')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('created_at')">
                            <div class="flex items-center gap-1">
                                Creado
                                @if($sortField === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                            {{-- Empresa --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($tenant->logo_path)
                                        <img src="{{ asset($tenant->logo_path) }}" alt="{{ $tenant->name }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Dominio --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-slate-700 px-2 py-1 rounded">{{ $tenant->domain }}</code>
                            </td>

                            {{-- Usuarios --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->users_count }}</span>
                            </td>

                            {{-- Facturas --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($tenant->facturas_count) }}</span>
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'suspended' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                        'trial' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                        'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                                    ];
                                    $statusLabels = [
                                        'active' => 'Activo',
                                        'suspended' => 'Suspendido',
                                        'trial' => 'Prueba',
                                        'inactive' => 'Inactivo',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tenant->status] ?? $statusColors['inactive'] }}">
                                    {{ $statusLabels[$tenant->status] ?? 'Desconocido' }}
                                </span>
                            </td>

                            {{-- Fecha Creación --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900 dark:text-white">{{ $tenant->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->created_at->diffForHumans() }}</p>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Ver --}}
                                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="cursor-pointer p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="Ver detalle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="cursor-pointer p-2 text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 transition-colors" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Toggle Estado --}}
                                    <button wire:click="toggleStatus({{ $tenant->id }})" wire:confirm="{{ $tenant->status === 'active' ? '¿Seguro que deseas suspender este tenant?' : '¿Seguro que deseas activar este tenant?' }}" class="cursor-pointer p-2 transition-colors {{ $tenant->status === 'active' ? 'text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400' : 'text-gray-500 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400' }}" title="{{ $tenant->status === 'active' ? 'Suspender' : 'Activar' }}">
                                        @if($tenant->status === 'active')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>

                                    {{-- Eliminar --}}
                                    <button wire:click="deleteTenant({{ $tenant->id }})" wire:confirm="¿Seguro que deseas eliminar '{{ $tenant->name }}'? Esta acción no se puede deshacer." class="cursor-pointer p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No se encontraron tenants</p>
                                    @if($this->hasActiveFilters())
                                        <button wire:click="clearFilters" class="cursor-pointer mt-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                                            Limpiar filtros
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Cards Mobile --}}
    <div class="md:hidden space-y-4">
        @forelse($tenants as $tenant)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        @if($tenant->logo_path)
                            <img src="{{ asset($tenant->logo_path) }}" alt="{{ $tenant->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($tenant->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</p>
                            <code class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->domain }}</code>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'suspended' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            'trial' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                        ];
                        $statusLabels = [
                            'active' => 'Activo',
                            'suspended' => 'Suspendido',
                            'trial' => 'Prueba',
                            'inactive' => 'Inactivo',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tenant->status] ?? $statusColors['inactive'] }}">
                        {{ $statusLabels[$tenant->status] ?? 'Desconocido' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 py-3 border-t border-b border-gray-100 dark:border-slate-700">
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tenant->users_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Usuarios</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($tenant->facturas_count) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Facturas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Creado</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->created_at->diffForHumans() }}</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="cursor-pointer p-2 text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="cursor-pointer p-2 text-amber-600 dark:text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <button wire:click="toggleStatus({{ $tenant->id }})" wire:confirm="{{ $tenant->status === 'active' ? '¿Suspender?' : '¿Activar?' }}" class="cursor-pointer p-2 {{ $tenant->status === 'active' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            @if($tenant->status === 'active')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm">No se encontraron tenants</p>
                @if($this->hasActiveFilters())
                    <button wire:click="clearFilters" class="cursor-pointer mt-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                        Limpiar filtros
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($tenants->hasPages())
        <div class="mt-6 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 px-4 py-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                {{-- Info de resultados --}}
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Mostrando {{ $tenants->firstItem() }} a {{ $tenants->lastItem() }} de {{ $tenants->total() }} resultados
                </div>

                {{-- Selector de items por página --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="perPage" class="text-sm text-gray-500 dark:text-gray-400">Mostrar:</label>
                        <select wire:model.live="perPage" id="perPage" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm py-1 focus:border-blue-500 focus:ring-blue-500">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    {{-- Links de paginación --}}
                    <div>
                        {{ $tenants->links('vendor.livewire.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

