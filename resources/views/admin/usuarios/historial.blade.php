@extends('layouts.sgdea')

@section('page-title', 'Historial de Aprobaciones')

@section('content')
<div class="admin-historial-container">
    <div class="header-section">
        <div>
            <h1>Historial de Aprobaciones</h1>
            <p>Registro de todos los usuarios aprobados y rechazados</p>
        </div>
        <a href="{{ route('admin.usuarios.pendientes') }}" class="btn btn-outline">
            ← Volver
        </a>
    </div>

    {{-- Filtros --}}
    <div class="filtros-card">
        <form method="GET" action="{{ route('admin.usuarios.historial') }}" class="filtros-form">
            <div class="filtro-grupo">
                <select name="action" class="filtro-input">
                    <option value="">Todas las acciones</option>
                    <option value="user_approved" {{ request('action') === 'user_approved' ? 'selected' : '' }}>
                        ✓ Aprobaciones
                    </option>
                    <option value="user_rejected" {{ request('action') === 'user_rejected' ? 'selected' : '' }}>
                        ✗ Rechazos
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
            <a href="{{ route('admin.usuarios.historial') }}" class="btn btn-outline">Limpiar</a>
        </form>
    </div>

    {{-- Tabla de Historial --}}
    <div class="table-card">
        <h3>Registro de Aprobaciones y Rechazos</h3>

        @if($historial->count() > 0)
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Aprobado por</th>
                        <th>Fecha</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $registro)
                        <tr>
                            <td>
                                @if($registro->action === 'user_approved')
                                    <span class="badge badge-success">✓ Aprobado</span>
                                @elseif($registro->action === 'user_rejected')
                                    <span class="badge badge-danger">✗ Rechazado</span>
                                @else
                                    <span class="badge">{{ $registro->action }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $registro->user ? $registro->user->name : 'Usuario eliminado' }}</strong>
                            </td>
                            <td>
                                <code>{{ $registro->user ? $registro->user->email : 'N/A' }}</code>
                            </td>
                            <td>
                                {{ auth()->user()->name }}
                                <br>
                                <small class="text-muted">ID: {{ $registro->user_id }}</small>
                            </td>
                            <td>
                                {{ $registro->created_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">
                                    hace {{ $registro->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($registro->changes)
                                    @php
                                        $changes = json_decode($registro->changes, true);
                                    @endphp
                                    @if(isset($changes['razon_rechazo']))
                                        <small>Razón: {{ $changes['razon_rechazo'] }}</small>
                                    @else
                                        <small class="text-muted">Sin detalles</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $historial->links() }}
        @else
            <p style="text-align: center; color: #6B7280; padding: 40px;">
                No hay registros de aprobaciones o rechazos
            </p>
        @endif
    </div>
</div>

<style>
.admin-historial-container {
    max-width: 1200px;
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
}

.filtros-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filtros-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.filtro-grupo {
    flex: 1;
}

.filtro-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
    font-size: 14px;
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

.historial-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.historial-table thead {
    background: #F5F7FA;
    border-bottom: 2px solid #D4D9E2;
}

.historial-table th,
.historial-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #D4D9E2;
}

.historial-table th {
    font-weight: 700;
    color: #1F2933;
}

.historial-table tbody tr:hover {
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

.badge-danger {
    background: #F8D7DA;
    color: #DC3545;
}

code {
    background: #F5F7FA;
    padding: 2px 6px;
    border-radius: 4px;
    color: #2767C6;
    font-family: monospace;
    font-size: 12px;
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

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}

.text-muted {
    color: #6B7280;
    font-size: 12px;
}

small {
    display: block;
    color: #6B7280;
    font-size: 12px;
    margin-top: 3px;
}
</style>
@endsection

