@extends('layouts.app')

@section('title', 'Dashboard Admin Global')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="bi bi-speedometer2"></i> Dashboard Admin Global
            </h1>
            <p class="text-muted">Bienvenido, {{ auth('system')->user()->name }}</p>
        </div>
        <div class="col-md-4 text-end">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Tenants</h6>
                    <h2 class="mb-0 text-primary">{{ $totalTenants }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title text-muted">Tenants Activos</h6>
                    <h2 class="mb-0 text-success">{{ $activeTenants }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Usuarios</h6>
                    <h2 class="mb-0 text-info">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title text-muted">Acciones</h6>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-plus"></i> Nuevo Tenant
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Menú Principal -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Administración</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.tenants.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-building"></i> Gestionar Tenants
                        <span class="badge bg-primary float-end">{{ $totalTenants }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenants Recientes -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tenants Recientes</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Dominio</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTenants as $tenant)
                            <tr>
                                <td><strong>{{ $tenant->name }}</strong></td>
                                <td><code>{{ $tenant->domain }}</code></td>
                                <td>
                                    @if($tenant->status === 'active')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $tenant->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No hay tenants aún
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

