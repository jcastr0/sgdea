@extends('layouts.sgdea', ['usesLivewire' => true])

@section('title', 'Terceros')
@section('page-title', 'Gestión de Terceros')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('dashboard')],
    ['label' => 'Terceros'],
]" />
@endsection

@section('content')
    @livewire('terceros-table')
@endsection

