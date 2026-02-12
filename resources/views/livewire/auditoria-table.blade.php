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

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total registros</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['acciones_count'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tipos de acción</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['usuarios_count'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Usuarios activos</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['ultima_accion'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Última acción</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra de filtros usando el componente reutilizable --}}
    <x-livewire.filter-bar
        :showFilters="$showFilters"
        :activeFiltersCount="$this->activeFiltersCount()"
        :hasActiveFilters="$this->hasActiveFilters()"
        searchPlaceholder="Buscar por usuario, IP..."
    >
        {{-- Slot search: Input de búsqueda --}}
        <x-slot:search>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por usuario, IP..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </x-slot:search>

        {{-- Slot filters: Panel de filtros avanzados --}}
        <x-slot:filters>
            {{-- Acción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acción</label>
                <select wire:model.live="action"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las acciones</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion }}">{{ ucfirst(str_replace('_', ' ', $accion)) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Usuario (si tiene permisos) --}}
            @if($canFilterByUser && $usuarios->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario</label>
                <select wire:model.live="userId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Tipo de modelo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entidad</label>
                <select wire:model.live="modelType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las entidades</option>
                    <option value="Factura">Factura</option>
                    <option value="Tercero">Tercero</option>
                    <option value="User">Usuario</option>
                </select>
            </div>

            {{-- Fecha desde --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
                <input type="date"
                       wire:model.live="fechaDesde"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Fecha hasta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
                <input type="date"
                       wire:model.live="fechaHasta"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
        </x-slot:filters>

        {{-- Slot actions: Botones adicionales --}}
        <x-slot:actions>
            <a href="{{ route('admin.auditoria.integridad') }}"
               class="cursor-pointer flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-sm font-medium hidden sm:inline">Integridad</span>
            </a>
            <a href="{{ route('admin.auditoria.export', ['search' => $search, 'action' => $action, 'user_id' => $userId, 'model_type' => $modelType, 'fecha_inicio' => $fechaDesde, 'fecha_fin' => $fechaHasta]) }}"
               class="cursor-pointer flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span class="text-sm font-medium">Exportar</span>
            </a>
        </x-slot:actions>
    </x-livewire.filter-bar>

    {{-- Tabla de resultados --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        @if($logs->count() > 0)
        {{-- Tabla Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('created_at')" class="cursor-pointer flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Fecha
                                @if($sortField === 'created_at')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('action')" class="cursor-pointer flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Acción
                                @if($sortField === 'action')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Modelo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">IP</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @foreach($logs as $log)
                        @php
                            $actionColors = [
                                'login' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                'logout' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'login_failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'create' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'update' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'delete' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'import' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                            ];
                            $actionColor = $actionColors[strtolower($log->action)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $log->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $log->user?->name ?? 'Sistema' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->model_type)
                                    <span class="text-sm text-gray-900 dark:text-white">{{ class_basename($log->model_type) }}</span>
                                    @if($log->model_id)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">#{{ $log->model_id }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('admin.auditoria.show', $log->id) }}"
                                   class="cursor-pointer inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cards Mobile --}}
        <div class="md:hidden divide-y divide-gray-200 dark:divide-slate-700">
            @foreach($logs as $log)
                @php
                    $actionColors = [
                        'login' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'logout' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'login_failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'create' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'update' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        'delete' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'import' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                    ];
                    $actionColor = $actionColors[strtolower($log->action)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-300">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $log->user?->name ?? 'Sistema' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $actionColor }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-4 text-gray-500 dark:text-gray-400">
                            @if($log->model_type)
                                <span>{{ class_basename($log->model_type) }}@if($log->model_id) #{{ $log->model_id }}@endif</span>
                            @endif
                            <span class="font-mono">{{ $log->ip_address ?? '-' }}</span>
                        </div>
                        <a href="{{ route('admin.auditoria.show', $log->id) }}"
                           class="cursor-pointer text-blue-600 dark:text-blue-400 hover:underline">
                            Ver detalle
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Mostrar</span>
                    <select wire:model.live="perPage" class="cursor-pointer px-2 py-1 border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700 text-sm">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">por página</span>
                </div>
                {{ $logs->links() }}
            </div>
        </div>
        @else
        {{-- Estado vacío --}}
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay registros de auditoría</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                @if($this->hasActiveFilters())
                    No se encontraron registros con los filtros aplicados.
                @else
                    Aún no hay acciones registradas en el sistema.
                @endif
            </p>
            @if($this->hasActiveFilters())
                <button wire:click="clearFilters" class="cursor-pointer text-blue-600 dark:text-blue-400 hover:underline">
                    Limpiar filtros
                </button>
            @endif
        </div>
        @endif
    </div>
</div>

