@extends('layouts.app')

@section('content')
<div class="auditoria-container">
    <div class="header-section">
        <div>
            <h1>Auditoría y Compliance</h1>
            <p>Registro inalterable de todas las acciones en el sistema</p>
        </div>
        <a href="{{ route('admin.auditoria.integridad') }}" class="btn btn-primary">
            🔍 Verificar Integridad
        </a>
    </div>

    {{-- Filtros --}}
    <div class="filtros-card">
        <form method="GET" action="{{ route('admin.auditoria.index') }}" class="filtros-form">
            <div class="filtro-grupo">
                <input
                    type="text"
                    name="search"
                    placeholder="Buscar en descripción, IP, usuario..."
                    value="{{ request('search') }}"
                    class="filtro-input"
                >
            </div>

            <div class="filtro-grupo">
                <select name="action" class="filtro-input">
                    <option value="">Todas las acciones</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Crear</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Editar</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Eliminar</option>
                    <option value="approve" {{ request('action') === 'approve' ? 'selected' : '' }}>Aprobar</option>
                    <option value="reject" {{ request('action') === 'reject' ? 'selected' : '' }}>Rechazar</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="download" {{ request('action') === 'download' ? 'selected' : '' }}>Descargar</option>
                    <option value="export" {{ request('action') === 'export' ? 'selected' : '' }}>Exportar</option>
                </select>
            </div>

            <div class="filtro-grupo">
                <select name="entity_type" class="filtro-input">
                    <option value="">Todas las entidades</option>
                    <option value="factura" {{ request('entity_type') === 'factura' ? 'selected' : '' }}>Factura</option>
                    <option value="tercero" {{ request('entity_type') === 'tercero' ? 'selected' : '' }}>Tercero</option>
                    <option value="usuario" {{ request('entity_type') === 'usuario' ? 'selected' : '' }}>Usuario</option>
                    <option value="importacion" {{ request('entity_type') === 'importacion' ? 'selected' : '' }}>Importación</option>
                    <option value="login" {{ request('entity_type') === 'login' ? 'selected' : '' }}>Acceso</option>
                </select>
            </div>

            <div class="filtro-grupo">
                <input
                    type="date"
                    name="fecha_inicio"
                    value="{{ request('fecha_inicio') }}"
                    class="filtro-input"
                >
            </div>

            <div class="filtro-grupo">
                <input
                    type="date"
                    name="fecha_fin"
                    value="{{ request('fecha_fin') }}"
                    class="filtro-input"
                >
            </div>

            <div class="filtro-grupo">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline">Limpiar</a>
            </div>

            <div class="filtro-grupo" style="flex-basis: 100%;">
                <form action="{{ route('admin.auditoria.export') }}" method="GET" style="display: inline;">
                    @csrf
                    <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio', now()->subMonth()->format('Y-m-d')) }}">
                    <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
                    <button type="submit" class="btn btn-success">📥 Descargar CSV</button>
                </form>
            </div>
        </form>
    </div>

    {{-- Tabla de Auditoría --}}
    <div class="table-card">
        <h3>Registro de Auditoría</h3>

        @if($logs->count() > 0)
            <table class="auditoria-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>Descripción</th>
                        <th>IP</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
                            </td>
                            <td>
                                @if($log->user)
                                    {{ $log->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="badge badge-gray">Sistema</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $this->badgeAccion($log->action) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <code>{{ ucfirst($log->entity_type) }}</code>
                                @if($log->entity_id)
                                    <br>
                                    <small class="text-muted">ID: {{ $log->entity_id }}</small>
                                @endif
                            </td>
                            <td>
                                {{ substr($log->description, 0, 60) }}
                                @if(strlen($log->description) > 60)...@endif
                            </td>
                            <td>
                                <code class="ip-address">{{ $log->ip_address }}</code>
                            </td>
                            <td>
                                <a href="{{ route('admin.auditoria.show', $log) }}" class="btn-accion">
                                    📋 Detalles
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $logs->links() }}
        @else
            <p style="text-align: center; color: #6B7280; padding: 40px;">
                No hay registros de auditoría que coincidan con los filtros
            </p>
        @endif
    </div>
</div>

<style>
.auditoria-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.header-section h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.header-section p {
    color: #6B7280;
    margin: 5px 0 0 0;
    font-size: 14px;
}

.filtros-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filtros-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: flex-end;
}

.filtro-grupo {
    display: flex;
    flex-direction: column;
}

.filtro-input {
    padding: 10px;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
    font-size: 14px;
}

.filtro-input:focus {
    outline: none;
    border-color: #2767C6;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
}

.table-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table-card h3 {
    margin: 0 0 20px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
}

.auditoria-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.auditoria-table thead {
    background: #F5F7FA;
    border-bottom: 2px solid #D4D9E2;
}

.auditoria-table th,
.auditoria-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #D4D9E2;
}

.auditoria-table th {
    font-weight: 700;
    color: #1F2933;
}

.auditoria-table tbody tr:hover {
    background: #FAFBFC;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.badge-create {
    background: #E6F5EC;
    color: #009F6B;
}

.badge-update {
    background: #FFF3CD;
    color: #856404;
}

.badge-delete {
    background: #F8D7DA;
    color: #DC3545;
}

.badge-approve {
    background: #D4EDDA;
    color: #155724;
}

.badge-reject {
    background: #F8D7DA;
    color: #721C24;
}

.badge-login {
    background: #D1ECF1;
    color: #0C5460;
}

.badge-logout {
    background: #E2E3E5;
    color: #383D41;
}

.badge-download {
    background: #B8DAFF;
    color: #084298;
}

.badge-export {
    background: #CFE2FF;
    color: #0A66CC;
}

.badge-gray {
    background: #E4E7EB;
    color: #6B7280;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: #2767C6;
    color: white;
}

.btn-primary:hover {
    background: #0F3F5F;
}

.btn-success {
    background: #28A745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}

.btn-accion {
    padding: 6px 12px;
    background: #E4E7EB;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-accion:hover {
    background: #2767C6;
    color: white;
}

code {
    background: #F5F7FA;
    padding: 2px 6px;
    border-radius: 4px;
    color: #2767C6;
    font-family: monospace;
    font-size: 12px;
}

.ip-address {
    font-size: 11px;
    color: #6B7280;
}

.text-muted {
    color: #6B7280;
    font-size: 12px;
}
</style>

@php
function badgeAccion($action) {
    $map = [
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
        'approve' => 'approve',
        'reject' => 'reject',
        'login' => 'login',
        'logout' => 'logout',
        'download' => 'download',
        'export' => 'export',
    ];
    return $map[$action] ?? 'gray';
}
$this->badgeAccion = 'badgeAccion';
@endphp

@endsection

