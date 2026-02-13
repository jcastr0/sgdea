@extends('layouts.sgdea')

@section('page-title', 'Aprobación de Usuarios')

@section('content')
<div class="admin-usuarios-container">
    <div class="header-section">
        <div>
            <h1>Aprobación de Usuarios</h1>
            <p>Usuarios pendientes de aprobación para acceder al sistema</p>
        </div>
        <a href="{{ route('admin.usuarios.historial') }}" class="btn btn-outline">
            📜 Ver Historial
        </a>
    </div>

    {{-- Estadísticas --}}
    <div class="stats-container">
        <div class="stat-card pending">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['pendientes'] }}</div>
                <div class="stat-label">Pendientes de aprobación</div>
            </div>
        </div>
        <div class="stat-card active">
            <div class="stat-icon">✓</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['activos'] }}</div>
                <div class="stat-label">Usuarios activos</div>
            </div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon">✗</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['rechazados'] }}</div>
                <div class="stat-label">Usuarios rechazados</div>
            </div>
        </div>
    </div>

    {{-- Tabla de Usuarios Pendientes --}}
    <div class="table-card">
        <h3>Usuarios Esperando Aprobación</h3>

        @if($usuarios->count() > 0)
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>
                                <strong>{{ $usuario->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $usuario->email }}</code>
                            </td>
                            <td>
                                {{ $usuario->created_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">
                                    hace {{ $usuario->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="acciones">
                                <form action="{{ route('admin.usuarios.aprobar', $usuario) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Aprobar usuario">
                                        ✓ Aprobar
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-danger" title="Rechazar usuario" onclick="abrirRechazar({{ $usuario->id }})">
                                    ✗ Rechazar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $usuarios->links() }}
        @else
            <p style="text-align: center; color: #6B7280; padding: 40px;">
                ✓ No hay usuarios pendientes de aprobación
            </p>
        @endif
    </div>
</div>

{{-- Modal Rechazar Usuario --}}
<div id="rechazarModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rechazar Usuario</h3>
            <button onclick="cerrarModal()" class="modal-close">✕</button>
        </div>
        <form id="rechazarForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Razón del rechazo (opcional)</label>
                <textarea
                    name="razon_rechazo"
                    class="form-textarea"
                    placeholder="Explica por qué se rechaza este usuario..."
                    maxlength="500"
                    rows="4"
                ></textarea>
                <small>Máximo 500 caracteres. El usuario recibirá esta información por email.</small>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-outline">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-danger">Rechazar Usuario</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-usuarios-container {
    max-width: 1000px;
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

.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
}

.stat-card {
    padding: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-card.pending {
    background: #FFF3CD;
    border-left: 4px solid #FFC107;
}

.stat-card.active {
    background: #E6F5EC;
    border-left: 4px solid #28A745;
}

.stat-card.rejected {
    background: #F8D7DA;
    border-left: 4px solid #DC3545;
}

.stat-icon {
    font-size: 28px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1F2933;
}

.stat-label {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
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

.usuarios-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.usuarios-table thead {
    background: #F5F7FA;
    border-bottom: 2px solid #D4D9E2;
}

.usuarios-table th,
.usuarios-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #D4D9E2;
}

.usuarios-table th {
    font-weight: 700;
    color: #1F2933;
}

.usuarios-table tbody tr:hover {
    background: #FAFBFC;
}

.usuarios-table code {
    background: #F5F7FA;
    padding: 2px 6px;
    border-radius: 4px;
    color: #2767C6;
}

.acciones {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    font-size: 13px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-success {
    background: #28A745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-danger {
    background: #DC3545;
    color: white;
}

.btn-danger:hover {
    background: #C82333;
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

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    min-width: 450px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #D4D9E2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 16px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6B7280;
}

.form-group {
    padding: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #1F2933;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
    font-size: 13px;
    font-family: inherit;
    resize: vertical;
}

.form-textarea:focus {
    outline: none;
    border-color: #2767C6;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
}

small {
    display: block;
    color: #6B7280;
    font-size: 12px;
    margin-top: 4px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #D4D9E2;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<script>
function abrirRechazar(usuarioId) {
    const modal = document.getElementById('rechazarModal');
    const form = document.getElementById('rechazarForm');

    form.action = `/admin/usuarios/${usuarioId}/rechazar`;
    form.querySelector('textarea').value = '';

    modal.style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('rechazarModal').style.display = 'none';
}

// Cerrar modal al hacer click fuera
document.addEventListener('click', function(event) {
    const modal = document.getElementById('rechazarModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});
</script>
@endsection

