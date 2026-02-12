@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Auditoría del Sistema')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Auditoría', 'active' => true],
]" />
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Auditoría del Sistema</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Registro de todas las acciones realizadas en el sistema</p>
        </div>
    </div>

    {{-- Componente Livewire de la tabla --}}
    @livewire('auditoria-table')
</div>
@endsection

