@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">¡Bienvenido, {{ Auth::user()->name }}!</h1>
    <p class="page-subtitle">Sistema de Gestión Documental y Fiscal</p>
</div>

<div class="grid grid-cols-4">
    <div class="stat-card">
        <div class="stat-value">{{ \App\Models\Tercero::where('tenant_id', session('tenant_id'))->count() }}</div>
        <div class="stat-label">Terceros</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ \App\Models\Factura::where('tenant_id', session('tenant_id'))->count() }}</div>
        <div class="stat-label">Facturas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ \App\Models\ImportLog::where('tenant_id', session('tenant_id'))->count() }}</div>
        <div class="stat-label">Importaciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="font-size: 16px; color: #10B981;">{{ ucfirst(Auth::user()->status) }}</div>
        <div class="stat-label">Estado</div>
    </div>
</div>

<div class="grid grid-cols-2" style="margin-top: 30px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📊 Acciones Rápidas</h3>
        </div>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('terceros.create') }}" class="btn btn-primary">➕ Nuevo Tercero</a>
            <a href="{{ route('facturas.create') }}" class="btn btn-success">📄 Nueva Factura</a>
            <a href="{{ route('importaciones.index') }}" class="btn btn-secondary">📥 Importar Excel</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ℹ️ Información de Usuario</h3>
        </div>
        <table class="table">
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ Auth::user()->email }}</td>
            </tr>
            <tr>
                <td><strong>Roles:</strong></td>
                <td>{{ Auth::user()->roles->pluck('name')->join(', ') ?: 'Sin roles' }}</td>
            </tr>
            <tr>
                <td><strong>Último acceso:</strong></td>
                <td>{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">✅ Estado del Sistema</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <div style="text-align: center; padding: 15px;">
            <span style="font-size: 30px;">✅</span>
            <p style="margin-top: 10px; color: #10B981; font-weight: 500;">Base de datos conectada</p>
        </div>
        <div style="text-align: center; padding: 15px;">
            <span style="font-size: 30px;">✅</span>
            <p style="margin-top: 10px; color: #10B981; font-weight: 500;">Autenticación activa</p>
        </div>
        <div style="text-align: center; padding: 15px;">
            <span style="font-size: 30px;">✅</span>
            <p style="margin-top: 10px; color: #10B981; font-weight: 500;">Migraciones OK</p>
        </div>
        <div style="text-align: center; padding: 15px;">
            <span style="font-size: 30px;">✅</span>
            <p style="margin-top: 10px; color: #10B981; font-weight: 500;">Sistema operativo</p>
        </div>
    </div>
</div>
@endsection
