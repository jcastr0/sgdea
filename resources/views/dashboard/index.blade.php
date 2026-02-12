@extends('layouts.sgdea')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span class="text-gray-900 dark:text-white font-medium">Dashboard</span>
</div>
@endsection

@section('content')
<div x-data="dashboardData()" x-init="init()">
    {{-- Header con bienvenida y filtros --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Bienvenida --}}
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    Aquí tienes el resumen de tu gestión documental y fiscal
                </p>
            </div>

            {{-- Filtros --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Selector de período --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="periodoLabel">Este año</span>
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-50">
                        <button @click="setPeriodo('todo'); open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-medium">📊 Todo</button>
                        <hr class="my-1 border-gray-200 dark:border-slate-700">
                        <button @click="setPeriodo('hoy'); open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Hoy</button>
                        <button @click="setPeriodo('semana'); open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Esta semana</button>
                        <button @click="setPeriodo('mes'); open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Este mes</button>
                        <button @click="setPeriodo('year'); open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Este año</button>
                        <hr class="my-1 border-gray-200 dark:border-slate-700">
                        <button @click="showCustomDates = true; open = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">📅 Personalizado...</button>
                    </div>
                </div>

                {{-- Botón refrescar --}}
                <button @click="loadData()" :disabled="loading"
                    class="inline-flex items-center justify-center p-2.5 text-gray-500 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors disabled:opacity-50">
                    <svg :class="{ 'animate-spin': loading }" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Alertas para admin --}}
    @if($isAdmin && $usuariosPendientes > 0)
    <div class="mb-6">
        <x-alert type="warning" dismissible>
            <div class="flex items-center justify-between">
                <span>
                    <strong>Atención:</strong> Tienes {{ $usuariosPendientes }} usuario(s) pendiente(s) de aprobación.
                </span>
                <a href="{{ route('admin.usuarios.pendientes') }}" class="ml-4 text-sm font-medium underline hover:no-underline">
                    Revisar ahora →
                </a>
            </div>
        </x-alert>
    </div>
    @endif

    {{-- Cards de estadísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        {{-- Card: Total Facturas --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Facturas</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white" x-text="formatNumber(kpis.total_facturas)">-</p>
                </div>
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500 dark:text-gray-400">Período seleccionado</span>
            </div>
        </div>

        {{-- Card: Ventas Netas --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ventas Netas</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white" x-text="formatCurrency(kpis.ventas_netas?.valor || 0)">-</p>
                </div>
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <template x-if="kpis.ventas_netas?.cambio > 0">
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span x-text="'+' + kpis.ventas_netas?.cambio + '%'"></span>
                    </span>
                </template>
                <template x-if="kpis.ventas_netas?.cambio < 0">
                    <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        <span x-text="kpis.ventas_netas?.cambio + '%'"></span>
                    </span>
                </template>
                <template x-if="kpis.ventas_netas?.cambio == 0">
                    <span class="text-gray-500 dark:text-gray-400">Sin cambios</span>
                </template>
                <span class="ml-2 text-gray-500 dark:text-gray-400">vs período anterior</span>
            </div>
        </div>

        {{-- Card: Pendiente de pago --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendiente de Pago</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white" x-text="formatCurrency(kpis.total_pendiente)">-</p>
                </div>
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-900/30">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-amber-600 dark:text-amber-400" x-text="kpis.facturas_morosas + ' facturas vencidas'"></span>
            </div>
        </div>

        {{-- Card: Tasa de morosidad --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tasa de Morosidad</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white" x-text="kpis.tasa_morosidad + '%'">-</p>
                </div>
                <div class="flex items-center justify-center h-12 w-12 rounded-xl"
                    :class="{
                        'bg-emerald-50 dark:bg-emerald-900/30': kpis.tasa_morosidad < 10,
                        'bg-amber-50 dark:bg-amber-900/30': kpis.tasa_morosidad >= 10 && kpis.tasa_morosidad < 20,
                        'bg-red-50 dark:bg-red-900/30': kpis.tasa_morosidad >= 20
                    }">
                    <svg class="h-6 w-6"
                        :class="{
                            'text-emerald-600 dark:text-emerald-400': kpis.tasa_morosidad < 10,
                            'text-amber-600 dark:text-amber-400': kpis.tasa_morosidad >= 10 && kpis.tasa_morosidad < 20,
                            'text-red-600 dark:text-red-400': kpis.tasa_morosidad >= 20
                        }"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500 dark:text-gray-400"
                    x-text="kpis.tasa_morosidad < 10 ? 'Buen indicador' : (kpis.tasa_morosidad < 20 ? 'Revisar cartera' : 'Atención requerida')"></span>
            </div>
        </div>
    </div>

    {{-- Grid principal: Gráficos y Acciones rápidas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Gráfico de evolución (2 columnas) --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Evolución de Ventas</h3>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                        <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                        Ventas Netas
                    </span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="chartEvolucion"></canvas>
            </div>
        </div>

        {{-- Acciones rápidas --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Acciones Rápidas</h3>
            <div class="space-y-3">
                @canAccess('factura.crear')
                <a href="{{ route('facturas.create') }}"
                    class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                    <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-600 text-white">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Nueva Factura</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Registrar factura manualmente</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endcanAccess

                @canAccess('tercero.crear')
                <a href="{{ route('terceros.create') }}"
                    class="flex items-center gap-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors group">
                    <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-emerald-600 text-white">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Nuevo Tercero</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Agregar proveedor o cliente</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endcanAccess

                @canAccess('importacion.ver')
                <a href="{{ route('importaciones.index') }}"
                    class="flex items-center gap-4 p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group">
                    <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-purple-600 text-white">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Importar Archivos</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Excel y PDF de facturas</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endcanAccess

                @if($isAdmin)
                <a href="{{ route('admin.usuarios.pendientes') }}"
                    class="flex items-center gap-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors group">
                    <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-amber-600 text-white relative">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        @if($usuariosPendientes > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center text-xs font-bold text-white bg-red-500 rounded-full">{{ $usuariosPendientes }}</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Aprobar Usuarios</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $usuariosPendientes }} pendiente(s)</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Segunda fila: Distribución, Tendencia y Top Terceros --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Distribución por estados (Dona) --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Facturas por Estado</h3>
            <div class="h-64">
                <canvas id="chartEstados"></canvas>
            </div>
            {{-- Leyenda personalizada --}}
            <div class="mt-4 flex flex-wrap justify-center gap-4">
                <template x-for="(label, index) in graficos.distribucion_estados?.labels || []" :key="index">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full"
                            :class="{
                                'bg-emerald-500': index === 0,
                                'bg-amber-500': index === 1,
                                'bg-red-500': index === 2
                            }"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400" x-text="label + ': ' + (graficos.distribucion_estados?.data?.[index] || 0)"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Tendencia mensual (Líneas - cantidad de facturas) --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tendencia Mensual</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Cantidad de facturas</span>
            </div>
            <div class="h-64">
                <canvas id="chartTendencia"></canvas>
            </div>
        </div>

        {{-- Top Terceros --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top 5 Terceros</h3>
            <div class="space-y-4">
                <template x-for="(tercero, index) in graficos.top_terceros?.data || []" :key="index">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-gray-100 dark:bg-slate-700 font-semibold text-gray-600 dark:text-gray-300" x-text="index + 1"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate" x-text="graficos.top_terceros?.labels?.[index]"></p>
                            <div class="mt-1 h-2 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full"
                                    :class="{
                                        'bg-blue-500': index === 0,
                                        'bg-blue-400': index === 1,
                                        'bg-blue-300': index === 2,
                                        'bg-blue-200 dark:bg-blue-600': index === 3,
                                        'bg-blue-100 dark:bg-blue-700': index === 4
                                    }"
                                    :style="{ width: ((tercero / (graficos.top_terceros?.data?.[0] || 1)) * 100) + '%' }">
                                </div>
                            </div>
                        </div>
                        <span class="flex-shrink-0 font-medium text-gray-900 dark:text-white" x-text="formatCurrency(tercero)"></span>
                    </div>
                </template>
                <template x-if="!graficos.top_terceros?.data?.length">
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="h-12 w-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>No hay datos suficientes</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Actividad reciente --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actividad Reciente</h3>
            <a href="{{ route('facturas.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                Ver todas →
            </a>
        </div>

        {{-- Tabla de actividad --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="pb-3 pr-4">Documento</th>
                        <th class="pb-3 pr-4">Tercero</th>
                        <th class="pb-3 pr-4">Fecha</th>
                        <th class="pb-3 pr-4">Valor</th>
                        <th class="pb-3 pr-4">Estado</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse(\App\Models\Factura::where('tenant_id', session('tenant_id'))->with('tercero')->orderBy('created_at', 'desc')->take(5)->get() as $factura)
                    <tr class="text-sm">
                        <td class="py-3 pr-4">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $factura->prefijo }}{{ $factura->numero_factura }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $factura->tercero?->nombre_razon_social ?? '-' }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-gray-500 dark:text-gray-400">{{ $factura->fecha_factura?->format('d/m/Y') ?? '-' }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($factura->total_pagar, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            @php
                                $estadoClasses = [
                                    'pagada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'pendiente' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'anulada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'vencida' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoClasses[$factura->estado] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($factura->estado ?? 'Sin estado') }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('facturas.show', $factura) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="h-12 w-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>No hay facturas registradas aún</p>
                            @canAccess('factura.crear')
                            <a href="{{ route('facturas.create') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:underline">
                                Crear primera factura →
                            </a>
                            @endcanAccess
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Fechas Personalizadas --}}
    <div x-show="showCustomDates"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/70 backdrop-blur-sm flex items-center justify-center z-50"
         @click.self="showCustomDates = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl w-full max-w-md mx-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Seleccionar período personalizado</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha inicio</label>
                    <input type="date" x-model="customFechaInicio"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha fin</label>
                    <input type="date" x-model="customFechaFin"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showCustomDates = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button @click="applyCustomDates()"
                        :disabled="!customFechaInicio || !customFechaFin"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Aplicar
                </button>
            </div>
        </div>
    </div>

    {{-- Estado de carga --}}
    <div x-show="loading" class="fixed inset-0 bg-gray-900/20 dark:bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium text-gray-900 dark:text-white">Cargando datos...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function dashboardData() {
    return {
        loading: false,
        kpis: {
            total_facturas: 0,
            ventas_netas: { valor: 0, cambio: 0 },
            total_pendiente: 0,
            total_pagadas: 0,
            tasa_morosidad: 0,
            facturas_morosas: 0
        },
        graficos: {
            evolucion: { labels: [], data: [] },
            top_terceros: { labels: [], data: [] },
            distribucion_estados: { labels: [], data: [] },
            facturas_mes: { labels: [], data: [] },
        },
        periodoLabel: 'Todo',
        fechaInicio: null,
        fechaFin: null,
        showCustomDates: false,
        customFechaInicio: null,
        customFechaFin: null,
        chartEvolucion: null,
        chartEstados: null,
        chartTendencia: null,

        init() {
            // Por defecto cargar todo (sin filtro de fechas)
            this.fechaInicio = '';
            this.fechaFin = '';
            this.loadData();
        },

        setPeriodo(tipo) {
            const now = new Date();
            switch(tipo) {
                case 'todo':
                    this.fechaInicio = '';
                    this.fechaFin = '';
                    this.periodoLabel = 'Todo';
                    break;
                case 'hoy':
                    this.fechaInicio = now.toISOString().split('T')[0];
                    this.fechaFin = now.toISOString().split('T')[0];
                    this.periodoLabel = 'Hoy';
                    break;
                case 'semana':
                    const startOfWeek = new Date(now);
                    startOfWeek.setDate(now.getDate() - now.getDay());
                    this.fechaInicio = startOfWeek.toISOString().split('T')[0];
                    this.fechaFin = now.toISOString().split('T')[0];
                    this.periodoLabel = 'Esta semana';
                    break;
                case 'mes':
                    this.fechaInicio = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                    this.fechaFin = now.toISOString().split('T')[0];
                    this.periodoLabel = 'Este mes';
                    break;
                case 'year':
                    this.fechaInicio = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
                    this.fechaFin = now.toISOString().split('T')[0];
                    this.periodoLabel = 'Este año';
                    break;
            }
            this.loadData();
        },

        applyCustomDates() {
            if (this.customFechaInicio && this.customFechaFin) {
                this.fechaInicio = this.customFechaInicio;
                this.fechaFin = this.customFechaFin;
                this.periodoLabel = 'Personalizado';
                this.showCustomDates = false;
                this.loadData();
            }
        },

        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.fechaInicio) params.append('fecha_inicio', this.fechaInicio);
                if (this.fechaFin) params.append('fecha_fin', this.fechaFin);

                const response = await fetch(`{{ route('dashboard.data') }}?${params}`);
                const data = await response.json();

                if (data.success) {
                    this.kpis = data.kpis;
                    this.graficos = data.graficos;
                    // Actualizar label con período real si viene del servidor
                    if (data.periodo && this.periodoLabel === 'Todo') {
                        this.periodoLabel = `Todo (${data.periodo.inicio} - ${data.periodo.fin})`;
                    }
                    this.updateCharts();
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
            } finally {
                this.loading = false;
            }
        },

        updateCharts() {
            // Destruir charts existentes
            if (this.chartEvolucion) this.chartEvolucion.destroy();
            if (this.chartEstados) this.chartEstados.destroy();
            if (this.chartTendencia) this.chartTendencia.destroy();

            // Detectar modo oscuro
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? '#334155' : '#e2e8f0';

            // Chart de Evolución (Ventas Netas)
            const ctxEvolucion = document.getElementById('chartEvolucion');
            if (ctxEvolucion) {
                this.chartEvolucion = new Chart(ctxEvolucion.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: this.graficos.evolucion?.labels || [],
                        datasets: [{
                            label: 'Ventas Netas',
                            data: this.graficos.evolucion?.data || [],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                ticks: { color: textColor }
                            },
                            y: {
                                grid: { color: gridColor },
                                ticks: {
                                    color: textColor,
                                    callback: (value) => this.formatCompact(value)
                                }
                            }
                        }
                    }
                });
            }

            // Chart de Estados (Dona)
            const ctxEstados = document.getElementById('chartEstados');
            if (ctxEstados) {
                this.chartEstados = new Chart(ctxEstados.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: this.graficos.distribucion_estados?.labels || [],
                        datasets: [{
                            data: this.graficos.distribucion_estados?.data || [],
                            backgroundColor: [
                                '#10b981', // Pagadas - verde
                                '#f59e0b', // Pendientes - ámbar
                                '#ef4444', // Canceladas - rojo
                            ],
                            borderWidth: 0,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Usamos leyenda personalizada
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%',
                    }
                });
            }

            // Chart de Tendencia Mensual (Cantidad de facturas)
            const ctxTendencia = document.getElementById('chartTendencia');
            if (ctxTendencia) {
                this.chartTendencia = new Chart(ctxTendencia.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: this.graficos.facturas_mes?.labels || [],
                        datasets: [{
                            label: 'Facturas',
                            data: this.graficos.facturas_mes?.data || [],
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#8b5cf6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                ticks: { color: textColor, maxRotation: 45 }
                            },
                            y: {
                                grid: { color: gridColor },
                                ticks: {
                                    color: textColor,
                                    stepSize: 1,
                                    precision: 0
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        },

        formatNumber(value) {
            if (value === null || value === undefined) return '-';
            return new Intl.NumberFormat('es-CO').format(value);
        },

        formatCurrency(value) {
            if (value === null || value === undefined) return '-';
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        formatCompact(value) {
            if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return (value / 1000).toFixed(1) + 'K';
            }
            return value;
        }
    }
}
</script>
@endpush
