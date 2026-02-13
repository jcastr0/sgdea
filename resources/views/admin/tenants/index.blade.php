@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Gestión de Tenants')
@section('page-title', 'Gestión de Tenants')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Panel Global', 'url' => route('admin.dashboard')],
    ['label' => 'Tenants'],
]" />
@endsection

@section('content')
    @livewire('admin.tenants-table')
@endsection

