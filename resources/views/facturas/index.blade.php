@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Facturas')
@section('page-title', 'Gestión de Facturas')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Facturas'],
]" />
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header de página --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Facturas</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Administra y consulta todas tus facturas electrónicas</p>
        </div>
    </div>

    {{-- Componente Livewire de la tabla --}}
    @livewire('facturas-table')
</div>
@endsection

