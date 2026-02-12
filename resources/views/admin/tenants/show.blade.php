@extends('layouts.app')

@section('title', $tenant->name)

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="bi bi-building"></i> {{ $tenant->name }}
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Información del Tenant
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 200px;">Nombre:</th>
                            <td><strong>{{ $tenant->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Dominio:</th>
                            <td><code>{{ $tenant->domain }}</code></td>
                        </tr>
                        <tr>
                            <th>Slug:</th>
                            <td><code>{{ $tenant->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Superadmin Global:</th>
                            <td>{{ $tenant->systemUser->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $tenant->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td>{{ $tenant->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Configuración de Tema -->
            @if($tenant->themeConfiguration)
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-palette"></i> Configuración Visual
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Color Primario:</strong></p>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 50px; height: 50px; background: {{ $tenant->themeConfiguration->color_primary }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                <code>{{ $tenant->themeConfiguration->color_primary }}</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Color Secundario:</strong></p>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 50px; height: 50px; background: {{ $tenant->themeConfiguration->color_secondary }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                <code>{{ $tenant->themeConfiguration->color_secondary }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Estadísticas -->
        <div class="col-md-4">
            <div class="card shadow mb-4 bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $usersCount }}</h3>
                    <p class="mb-0">Usuarios</p>
                </div>
            </div>

            <div class="card shadow mb-4 bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $invoicesCount }}</h3>
                    <p class="mb-0">Facturas</p>
                </div>
            </div>

            <div class="card shadow mb-4 bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $clientsCount }}</h3>
                    <p class="mb-0">Clientes</p>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Acciones</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tenants.toggle-status', $tenant->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100 {{ $tenant->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                            <i class="bi bi-toggle2-{{ $tenant->status === 'active' ? 'on' : 'off' }}"></i>
                            {{ $tenant->status === 'active' ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('¿Eliminar este tenant?')">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

