@extends('layouts.sgdea')

@section('title', $tercero->nombre_razon_social)
@section('page-title', 'Detalle de Tercero')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Terceros', 'url' => route('terceros.index')],
    ['label' => $tercero->nombre_razon_social],
]" />
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header con perfil --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                {{-- Avatar --}}
                <div class="w-20 h-20 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($tercero->nombre_razon_social, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-white">{{ $tercero->nombre_razon_social }}</h1>
                    <p class="text-blue-100 font-mono">NIT: {{ $tercero->nit }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($tercero->estado === 'activo')
                        <span class="px-3 py-1 bg-green-500/20 text-green-100 rounded-full text-sm font-medium">
                            ✓ Activo
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-500/20 text-gray-200 rounded-full text-sm font-medium">
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Información de contacto --}}
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-slate-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tercero->email ?? 'No registrado' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-slate-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Teléfono</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tercero->telefono ?? 'No registrado' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-slate-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Dirección</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tercero->direccion ?? 'No registrada' }}</p>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="px-6 py-3 bg-gray-50 dark:bg-slate-700/50 flex flex-wrap gap-3">
            <a href="{{ route('terceros.edit', $tercero) }}"
               class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('facturas.index', ['terceroId' => $tercero->id]) }}"
               class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-600 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ver Facturas
            </a>
            <a href="{{ route('terceros.index') }}"
               class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Documentos --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_facturas'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Documentos</p>
                    <div class="flex gap-2 mt-1 text-xs">
                        <span class="text-blue-600 dark:text-blue-400">FV: {{ $stats['total_facturas_venta'] ?? 0 }}</span>
                        <span class="text-red-600 dark:text-red-400">NC: {{ $stats['total_notas_credito'] ?? 0 }}</span>
                        <span class="text-green-600 dark:text-green-400">ND: {{ $stats['total_notas_debito'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Facturado Neto --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_facturado_neto'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Neto</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">FV + ND - NC</p>
                </div>
            </div>
        </div>

        {{-- Desglose: Notas Crédito/Débito --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700">
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Facturas:</span>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">+${{ number_format($stats['total_facturado'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Notas Débito:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">+${{ number_format($stats['total_notas_debito_monto'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Notas Crédito:</span>
                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">-${{ number_format($stats['total_notas_credito_monto'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Última Factura --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['ultima_factura'] ? \Carbon\Carbon::parse($stats['ultima_factura'])->format('d/m/Y') : '-' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Última Factura</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Notas --}}
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Notas</h3>
            </div>
            <div class="p-5">
                @if($tercero->notas)
                    <p class="text-gray-600 dark:text-gray-400 text-sm whitespace-pre-wrap">{{ $tercero->notas }}</p>
                @else
                    <p class="text-gray-400 dark:text-gray-500 text-sm italic">Sin notas registradas</p>
                @endif
            </div>
        </div>

        {{-- Últimas facturas --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">Últimas Facturas</h3>
                <a href="{{ route('facturas.index', ['terceroId' => $tercero->id]) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver todas →</a>
            </div>
            <div class="overflow-x-auto">
                @if($ultimasFacturas->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Factura</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Total</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($ultimasFacturas as $factura)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('facturas.show', $factura) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $factura->numero_factura }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                ${{ number_format($factura->total_pagar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($factura->estado === 'aceptado')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Aceptado</span>
                                @elseif($factura->estado === 'pendiente')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">Pendiente</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">{{ ucfirst($factura->estado) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No hay facturas registradas</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Información adicional --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Información del Registro</h3>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Fecha de creación</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $tercero->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Última actualización</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $tercero->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Historial de Cambios --}}
    @if(isset($historial))
    <x-audit-history
        :logs="$historial"
        title="Historial de Cambios"
        subtitle="Registro de todas las modificaciones de este tercero"
        :limit="10"
        :showLink="false"
    />
    @endif
</div>
@endsection

