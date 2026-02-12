@extends('layouts.sgdea')

@section('title', 'Importación de Facturas')
@section('page-title', 'Importaciones')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('dashboard')],
    ['label' => 'Importaciones'],
]" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'excel' }">
    {{-- Tabs de navegación --}}
    <div class="mb-6 border-b border-gray-200 dark:border-slate-700">
        <nav class="flex gap-4" aria-label="Tabs">
            <button @click="activeTab = 'excel'"
                    :class="activeTab === 'excel'
                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Importar Excel
            </button>
            <button @click="activeTab = 'pdf'"
                    :class="activeTab === 'pdf'
                        ? 'border-red-500 text-red-600 dark:text-red-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Asociar PDFs
            </button>
        </nav>
    </div>

    {{-- Contenido del tab Excel --}}
    <div x-show="activeTab === 'excel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @livewire('import-wizard')
    </div>

    {{-- Contenido del tab PDF --}}
    <div x-show="activeTab === 'pdf'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @livewire('import-pdf-wizard')
    </div>
</div>
@endsection

