@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Crear Nuevo Tenant')
@section('page-title', 'Crear Nuevo Tenant')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Panel Global', 'url' => route('admin.dashboard')],
    ['label' => 'Tenants', 'url' => route('admin.tenants.index')],
    ['label' => 'Crear'],
]" />
@endsection

@section('content')
    @livewire('admin.create-tenant-wizard')
@endsection

