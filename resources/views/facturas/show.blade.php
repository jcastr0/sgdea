@extends('layouts.sgdea')

@section('title', 'Factura ' . $factura->numero_factura)

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Facturas', 'route' => 'facturas.index'],
    ['label' => $factura->numero_factura, 'active' => true],
]" />
@endsection

@section('content')
<div x-data="facturaDetalle()" class="space-y-6">
    {{-- Header con información principal y estado --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                {{-- Info principal --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                            Factura #{{ $factura->numero_factura }}
                        </h1>
                        <x-status-badge :status="$factura->estado" size="md" />
                    </div>

                    {{-- CUFE con botón copiar --}}
                    <div class="flex items-center gap-2 mt-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">CUFE:</span>
                        <code class="text-xs bg-gray-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-gray-700 dark:text-gray-300 max-w-xs truncate">
                            {{ $factura->cufe }}
                        </code>
                        <button type="button"
                                @click="copiarCUFE('{{ $factura->cufe }}')"
                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                title="Copiar CUFE">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <span x-show="cufeCopiado" x-transition class="text-xs text-green-600 dark:text-green-400">¡Copiado!</span>
                    </div>

                    {{-- Cliente --}}
                    <div class="mt-4 flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="font-medium">{{ $factura->tercero->nombre_razon_social ?? 'Sin cliente' }}</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-sm">NIT: {{ $factura->tercero->nit ?? '-' }}</span>
                    </div>
                </div>

                {{-- Total destacado --}}
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total a Pagar</p>
                    <p class="text-3xl lg:text-4xl font-bold text-green-600 dark:text-green-400">
                        ${{ number_format($factura->total_pagar, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $factura->fecha_factura?->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Barra de acciones --}}
        <div class="px-6 py-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-700 flex flex-wrap items-center gap-2">
            @if($factura->pdf_path)
                <x-button variant="primary" size="sm" @click="abrirVisorPDF()">
                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver PDF
                </x-button>
                <x-button variant="outline" size="sm" href="{{ route('facturas.download-pdf', $factura) }}">
                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Descargar
                </x-button>
            @else
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 px-2.5 py-1.5 bg-gray-100 dark:bg-slate-700 rounded-lg">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Sin PDF asociado
                </span>
            @endif

            <div class="flex-1"></div>

            <x-button variant="ghost" size="sm" href="{{ route('facturas.index') }}">
                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </x-button>
        </div>
    </div>

    {{-- Grid de información --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna principal (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Información del Cliente --}}
            <x-card title="Información del Cliente">
                <x-slot:headerActions>
                    @if($factura->tercero)
                        <a href="{{ route('terceros.show', $factura->tercero) }}"
                           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Ver perfil →
                        </a>
                    @endif
                </x-slot:headerActions>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Razón Social</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $factura->tercero->nombre_razon_social ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">NIT / Identificación</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $factura->tercero->nit ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Teléfono</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $factura->tercero->telefono ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Email</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $factura->tercero->email ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-500 dark:text-gray-400">Dirección</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $factura->tercero->direccion ?? '-' }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Descripción del Servicio --}}
            @if($factura->servicio_descripcion)
            <x-card title="Descripción del Servicio">
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($factura->servicio_descripcion)) !!}
                </div>
            </x-card>
            @endif

            {{-- Información de Motonave (si existe) --}}
            @if($factura->motonave || $factura->trb)
            <x-card title="Información de Motonave">
                <x-slot:headerActions>
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </x-slot:headerActions>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Motonave</label>
                        <p class="font-medium text-gray-900 dark:text-white text-lg">{{ $factura->motonave ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">TRB</label>
                        <p class="font-medium text-gray-900 dark:text-white text-lg">{{ $factura->trb ?? '-' }}</p>
                    </div>
                </div>
            </x-card>
            @endif

            {{-- Historial de Cambios usando componente reutilizable --}}
            @if(isset($historial))
            <x-audit-history
                :logs="$historial"
                title="Historial de Cambios"
                subtitle="Registro de todas las modificaciones de esta factura"
                :limit="10"
                :showLink="false"
            />
            @endif
        </div>

        {{-- Columna lateral (1/3) --}}
        <div class="space-y-6">
            {{-- Resumen Financiero --}}
            <x-card title="Resumen Financiero">
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-white">${{ number_format($factura->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">IVA (19%)</span>
                        <span class="font-medium text-gray-900 dark:text-white">${{ number_format($factura->iva, 2, ',', '.') }}</span>
                    </div>
                    @if($factura->descuento > 0)
                    <div class="flex justify-between items-center py-2 text-red-600 dark:text-red-400">
                        <span>Descuento</span>
                        <span class="font-medium">-${{ number_format($factura->descuento, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-200 dark:border-slate-700 pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($factura->total_pagar, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Fechas --}}
            <x-card title="Fechas">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Fecha de Emisión</label>
                        <p class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $factura->fecha_factura?->format('d/m/Y') ?? '-' }}
                        </p>
                    </div>
                    @if($factura->fecha_vencimiento)
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Fecha de Vencimiento</label>
                        <p class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $factura->fecha_vencimiento->format('d/m/Y') }}
                            @php
                                $diasRestantes = now()->diffInDays($factura->fecha_vencimiento, false);
                            @endphp
                            @if($diasRestantes < 0)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Vencida
                                </span>
                            @elseif($diasRestantes <= 7)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                    {{ $diasRestantes }} días
                                </span>
                            @endif
                        </p>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200 dark:border-slate-700">
                        <label class="text-sm text-gray-500 dark:text-gray-400">Registrado</label>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $factura->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Última modificación</label>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $factura->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Información Técnica --}}
            <x-card title="Información Técnica">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">CUFE</label>
                        <p class="text-xs font-mono bg-gray-100 dark:bg-slate-700 px-2 py-2 rounded break-all text-gray-700 dark:text-gray-300">
                            {{ $factura->cufe }}
                        </p>
                    </div>
                    @if($factura->hash_pdf)
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Hash PDF (SHA-256)</label>
                        <p class="text-xs font-mono bg-gray-100 dark:bg-slate-700 px-2 py-2 rounded break-all text-gray-700 dark:text-gray-300">
                            {{ $factura->hash_pdf }}
                        </p>
                        @if($factura->pdf_path)
                            @php
                                $integridadOk = $factura->verificarIntegridadPdf();
                            @endphp
                            <div class="mt-2 flex items-center gap-2 text-sm {{ $integridadOk ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                @if($integridadOk)
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    PDF íntegro
                                @else
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    PDF modificado
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </x-card>

            {{-- Acciones peligrosas --}}
            <x-card padding="sm" class="border-red-200 dark:border-red-900/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-red-700 dark:text-red-400">Zona de peligro</p>
                        <p class="text-sm text-red-600/70 dark:text-red-400/70">Esta acción no se puede deshacer</p>
                    </div>
                    <form method="POST" action="{{ route('facturas.destroy', $factura) }}"
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta factura? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger" size="sm">
                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                        </x-button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Modal Visor PDF --}}
    @if($factura->pdf_path)
    <div x-show="visorPDFAbierto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-hidden"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cerrarVisorPDF()"></div>

        {{-- Modal Content --}}
        <div class="absolute inset-4 lg:inset-8 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Header del modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Factura #{{ $factura->numero_factura }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vista previa del documento PDF</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('facturas.download-pdf', $factura) }}"
                       class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                       title="Descargar PDF">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                    <a href="{{ route('facturas.pdf', $factura) }}"
                       target="_blank"
                       class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                       title="Abrir en nueva pestaña">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                    <button type="button"
                            @click="cerrarVisorPDF()"
                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Contenido del PDF --}}
            <div class="flex-1 bg-gray-900">
                <iframe
                    src="{{ route('facturas.pdf', $factura) }}#toolbar=1&navpanes=0"
                    class="w-full h-full border-0"
                    title="Vista previa PDF">
                </iframe>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function facturaDetalle() {
    return {
        visorPDFAbierto: false,
        cufeCopiado: false,
        historialAbierto: null,

        abrirVisorPDF() {
            this.visorPDFAbierto = true;
            document.body.style.overflow = 'hidden';
        },

        cerrarVisorPDF() {
            this.visorPDFAbierto = false;
            document.body.style.overflow = '';
        },

        async copiarCUFE(cufe) {
            try {
                await navigator.clipboard.writeText(cufe);
                this.cufeCopiado = true;
                setTimeout(() => this.cufeCopiado = false, 2000);
            } catch (err) {
                console.error('Error al copiar:', err);
            }
        },

        toggleHistorialDetalle(id) {
            this.historialAbierto = this.historialAbierto === id ? null : id;
        },

        init() {
            // Cerrar modal con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.visorPDFAbierto) {
                    this.cerrarVisorPDF();
                }
            });
        }
    }
}
</script>
@endpush
@endsection

