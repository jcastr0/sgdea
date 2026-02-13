@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Gestión Global de Usuarios')
@section('page-title', 'Usuarios del Sistema')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Panel Global', 'url' => route('admin.dashboard')],
    ['label' => 'Usuarios'],
]" />
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión Global de Usuarios</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Administra todos los usuarios del sistema desde un solo lugar.
            </p>
        </div>
    </div>

    {{-- Componente Livewire --}}
    @livewire('admin.global-users-table')
</div>
@endsection

