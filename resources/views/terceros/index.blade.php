@extends('layouts.app')

@section('content')
<div class="terceros-container">
    <div class="terceros-header">
        <div>
            <h1>Gestión de Terceros/Clientes</h1>
            <p>Administra todos los clientes de tu empresa</p>
        </div>
        <a href="{{ route('terceros.create') }}" class="btn btn-primary btn-lg">
            + Nuevo Tercero
        </a>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    {{-- Errores --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <strong>Error:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtros de búsqueda --}}
    <div class="filters-card">
        <form method="GET" action="{{ route('terceros.index') }}" class="filters-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Buscar por nombre:</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        placeholder="Nombre del tercero..."
                        value="{{ $search ?? '' }}"
                        class="form-control"
                    >
                </div>

                <div class="filter-group">
                    <label for="nit">Buscar por NIT:</label>
                    <input
                        type="text"
                        id="nit"
                        name="nit"
                        placeholder="NIT..."
                        value="{{ $nit ?? '' }}"
                        class="form-control"
                    >
                </div>

                <div class="filter-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="activo" {{ ($estado ?? '') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ ($estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                <a href="{{ route('terceros.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Tabla de terceros --}}
    <div class="table-card">
        @if ($terceros->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>NIT</th>
                        <th>Nombre/Razón Social</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($terceros as $tercero)
                        <tr>
                            <td>
                                <strong>{{ $tercero->nit }}</strong>
                            </td>
                            <td>{{ $tercero->nombre_razon_social }}</td>
                            <td>{{ $tercero->telefono ?? '-' }}</td>
                            <td>{{ $tercero->direccion ? substr($tercero->direccion, 0, 40) . '...' : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $tercero->estado === 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($tercero->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('terceros.edit', $tercero) }}" class="btn btn-sm btn-warning" title="Editar">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('terceros.destroy', $tercero) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar este tercero?')"
                                            title="Eliminar"
                                        >
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginación --}}
            <div class="pagination-wrapper">
                {{ $terceros->links() }}
            </div>
        @else
            <div class="empty-state">
                <p>No hay terceros registrados</p>
                <a href="{{ route('terceros.create') }}" class="btn btn-primary">Crear primer tercero</a>
            </div>
        @endif
    </div>
</div>

<style>
.terceros-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.terceros-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.terceros-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.terceros-header p {
    color: #6B7280;
    margin: 5px 0 0 0;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 14px;
    border-radius: 8px;
}

.filters-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-row {
    display: flex;
    gap: 15px;
    width: 100%;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #1F2933;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
    font-size: 13px;
}

.table-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.table thead {
    background: #F5F7FA;
    border-bottom: 2px solid #D4D9E2;
}

.table th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #1F2933;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #D4D9E2;
    font-size: 13px;
    color: #6B7280;
}

.table tbody tr:hover {
    background: #FAFBFC;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background: #E6F5EC;
    color: #009F6B;
}

.badge-secondary {
    background: #E4E7EB;
    color: #6B7280;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-warning {
    background: #FFF3CD;
    color: #B59C00;
}

.btn-danger {
    background: #F8D7DA;
    color: #DC3545;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6B7280;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert-success {
    background: #E6F5EC;
    color: #009F6B;
    border: 1px solid #A8EDD6;
}

.alert-danger {
    background: #F8D7DA;
    color: #DC3545;
    border: 1px solid #F5C2C7;
}

.btn-close {
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: inherit;
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>
@endsection

