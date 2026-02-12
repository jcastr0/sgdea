@extends('layouts.app')

@section('content')
<div class="facturas-container">
    <div class="facturas-header">
        <div>
            <h1>Gestión de Facturas</h1>
            <p>Administra todas tus facturas y documentos</p>
        </div>
        <a href="{{ route('facturas.create') }}" class="btn btn-primary btn-lg">
            + Nueva Factura
        </a>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        </div>
    @endif

    {{-- Búsqueda Avanzada --}}
    <div class="search-advanced-card">
        <div class="search-header">
            <h3>🔍 Búsqueda Avanzada</h3>
            <button type="button" class="btn-toggle-search" onclick="toggleAdvancedSearch()">
                <span id="search-toggle-text">Expandir</span> ▼
            </button>
        </div>

        <form method="GET" action="{{ route('facturas.index') }}" id="advanced-search-form" class="advanced-search-form" style="display: none;">

            {{-- Fila 1: Identificadores --}}
            <div class="search-section">
                <h4>Identificadores</h4>
                <div class="search-row">
                    <div class="search-group">
                        <label for="numero_factura">Nº Factura</label>
                        <input type="text" id="numero_factura" name="numero_factura"
                            placeholder="Ej: FAC-001" value="{{ $filters['numero_factura'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="cufe">CUFE</label>
                        <input type="text" id="cufe" name="cufe"
                            placeholder="CUFE de la factura..." value="{{ $filters['cufe'] ?? '' }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Fila 2: Cliente/Tercero --}}
            <div class="search-section">
                <h4>Cliente</h4>
                <div class="search-row">
                    <div class="search-group">
                        <label for="tercero_search">Nombre o NIT</label>
                        <input type="text" id="tercero_search" name="tercero_search"
                            placeholder="Buscar por nombre o NIT..." value="{{ $filters['tercero_search'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="tercero_id">Seleccionar Cliente</label>
                        <select id="tercero_id" name="tercero_id" class="form-control">
                            <option value="">-- Todos --</option>
                            @foreach($terceros as $tercero)
                                <option value="{{ $tercero->id }}"
                                    {{ ($filters['tercero_id'] ?? '') == $tercero->id ? 'selected' : '' }}>
                                    {{ $tercero->nombre_razon_social }} ({{ $tercero->nit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Fila 3: Información de Envío --}}
            <div class="search-section">
                <h4>Información de Envío</h4>
                <div class="search-row">
                    <div class="search-group">
                        <label for="motonave">Motonave</label>
                        <input type="text" id="motonave" name="motonave"
                            placeholder="Nombre de la motonave..." value="{{ $filters['motonave'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="trb">TRB</label>
                        <input type="text" id="trb" name="trb"
                            placeholder="TRB..." value="{{ $filters['trb'] ?? '' }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Fila 4: Fechas y Montos --}}
            <div class="search-section">
                <h4>Período y Montos</h4>
                <div class="search-row">
                    <div class="search-group">
                        <label for="fecha_desde">Desde</label>
                        <input type="date" id="fecha_desde" name="fecha_desde"
                            value="{{ $filters['fecha_desde'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="fecha_hasta">Hasta</label>
                        <input type="date" id="fecha_hasta" name="fecha_hasta"
                            value="{{ $filters['fecha_hasta'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="total_min">Total Mínimo</label>
                        <input type="number" id="total_min" name="total_min" step="0.01"
                            placeholder="0.00" value="{{ $filters['total_min'] ?? '' }}" class="form-control">
                    </div>

                    <div class="search-group">
                        <label for="total_max">Total Máximo</label>
                        <input type="number" id="total_max" name="total_max" step="0.01"
                            placeholder="999999.99" value="{{ $filters['total_max'] ?? '' }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Fila 5: Estado y PDF --}}
            <div class="search-section">
                <h4>Estado y Documentación</h4>
                <div class="search-row">
                    <div class="search-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            <option value="">-- Todos --</option>
                            <option value="pendiente" {{ ($filters['estado'] ?? '') === 'pendiente' ? 'selected' : '' }}>
                                ⏳ Pendiente
                            </option>
                            <option value="pagada" {{ ($filters['estado'] ?? '') === 'pagada' ? 'selected' : '' }}>
                                ✓ Pagada
                            </option>
                            <option value="cancelada" {{ ($filters['estado'] ?? '') === 'cancelada' ? 'selected' : '' }}>
                                ✗ Cancelada
                            </option>
                        </select>
                    </div>

                    <div class="search-group">
                        <label for="tiene_pdf">Documentación</label>
                        <select id="tiene_pdf" name="tiene_pdf" class="form-control">
                            <option value="">-- Cualquiera --</option>
                            <option value="1" {{ ($filters['tiene_pdf'] ?? '') === '1' ? 'selected' : '' }}>
                                📄 Con PDF
                            </option>
                            <option value="0" {{ ($filters['tiene_pdf'] ?? '') === '0' ? 'selected' : '' }}>
                                ⚠️ Sin PDF
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="search-actions">
                <button type="submit" class="btn btn-primary">
                    🔍 Buscar
                </button>
                <a href="{{ route('facturas.index') }}" class="btn btn-outline">
                    🔄 Limpiar Filtros
                </a>
            </div>
        </form>

        {{-- Búsqueda rápida (siempre visible) --}}
        <form method="GET" action="{{ route('facturas.index') }}" class="quick-search-form">
            <input type="text" name="search" placeholder="Busca rápido por Nº Factura o CUFE..."
                value="{{ $filters['search'] ?? '' }}" class="quick-search-input">
            <button type="submit" class="btn btn-small">Buscar</button>
        </form>
    </div>
                    >
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Estadísticas --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Facturas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">${{ number_format($estadisticas['suma_total_pagar'], 2) }}</div>
            <div class="stat-label">Total a Pagar</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">${{ number_format($estadisticas['promedio_por_factura'], 2) }}</div>
            <div class="stat-label">Promedio</div>
        </div>
        <div class="stat-card">
            <div class="stat-badges">
                @foreach($estadisticas['por_estado'] as $estado => $cantidad)
                    <span class="badge badge-{{ $estado === 'pendiente' ? 'warning' : ($estado === 'pagada' ? 'success' : 'secondary') }}">
                        {{ ucfirst($estado) }}: {{ $cantidad }}
                    </span>
                @endforeach
            </div>
            <div class="stat-label">Por Estado</div>
        </div>
    </div>

    {{-- Tabla de facturas --}}
    <div class="table-card">
        @if ($facturas->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Número Factura</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facturas as $factura)
                        <tr>
                            <td>
                                <strong>{{ $factura->numero_factura }}</strong>
                                <br>
                                <small class="text-muted">{{ substr($factura->cufe, 0, 20) }}...</small>
                            </td>
                            <td>{{ $factura->tercero->nombre_razon_social }}</td>
                            <td>{{ $factura->fecha_factura->format('d/m/Y') }}</td>
                            <td>
                                <strong>${{ number_format($factura->total_pagar, 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $badge = $factura->getEstadoBadge();
                                @endphp
                                <span class="badge {{ $badge['clase'] }}">
                                    {{ $badge['texto'] }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('facturas.show', $factura) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                        👁️
                                    </a>
                                    <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-sm btn-warning" title="Editar">
                                        ✏️
                                    </a>
                                    @if($factura->tienePdf())
                                        <a href="{{ route('facturas.download-pdf', $factura) }}" class="btn btn-sm btn-success" title="Descargar PDF">
                                            📥
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('facturas.destroy', $factura) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar esta factura?')"
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
                {{ $facturas->links() }}
            </div>
        @else
            <div class="empty-state">
                <p>No hay facturas registradas</p>
                <a href="{{ route('facturas.create') }}" class="btn btn-primary">Crear primera factura</a>
            </div>
        @endif
    </div>
</div>

<style>
.facturas-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

{{-- BÚSQUEDA AVANZADA --}}
.search-advanced-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.search-header {
    padding: 20px;
    background: linear-gradient(135deg, #F0F4FF 0%, #FFFFFF 100%);
    border-bottom: 2px solid #E4E7EB;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.search-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
}

.btn-toggle-search {
    background: none;
    border: 1px solid #D4D9E2;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #2767C6;
    transition: all 0.3s ease;
}

.btn-toggle-search:hover {
    background: #2767C6;
    color: white;
}

.advanced-search-form {
    padding: 20px;
}

.search-section {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #E4E7EB;
}

.search-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.search-section h4 {
    font-size: 13px;
    font-weight: 700;
    color: #1F2933;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2767C6;
}

.search-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.search-group {
    display: flex;
    flex-direction: column;
}

.search-group label {
    font-size: 13px;
    font-weight: 600;
    color: #1F2933;
    margin-bottom: 6px;
}

.form-control {
    padding: 10px;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
    font-size: 13px;
}

.form-control:focus {
    outline: none;
    border-color: #2767C6;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
}

.search-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #E4E7EB;
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

{{-- BÚSQUEDA RÁPIDA --}}
.quick-search-form {
    padding: 0 20px 20px 20px;
    display: flex;
    gap: 10px;
}

.quick-search-input {
    flex: 1;
    padding: 12px;
    border: 2px solid #2767C6;
    border-radius: 6px;
    font-size: 14px;
}

.quick-search-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
}

.btn-small {
    padding: 10px 20px;
    background: #2767C6;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 13px;
}

.btn-small:hover {
    background: #0F3F5F;
}
</style>

<script>
function toggleAdvancedSearch() {
    const form = document.getElementById('advanced-search-form');
    const toggle = document.getElementById('search-toggle-text');

    if (form.style.display === 'none') {
        form.style.display = 'block';
        toggle.textContent = 'Contraer';
    } else {
        form.style.display = 'none';
        toggle.textContent = 'Expandir';
    }
}

// Si hay filtros activos, mostrar la búsqueda avanzada por defecto
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('advanced-search-form');
    const inputs = form.querySelectorAll('input, select');
    let hasFilters = false;

    inputs.forEach(input => {
        if (input.value && input.value !== '' && input.name !== 'search') {
            hasFilters = true;
        }
    });

    if (hasFilters) {
        form.style.display = 'block';
        document.getElementById('search-toggle-text').textContent = 'Contraer';
    }
});
</script>

.facturas-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.facturas-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.facturas-header p {
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
    flex-direction: column;
    gap: 15px;
}

.filter-row {
    display: flex;
    gap: 15px;
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

.filter-actions {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #2767C6;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: #6B7280;
    font-weight: 600;
}

.stat-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.badge-warning {
    background: #FFF3CD;
    color: #B59C00;
}

.badge-success {
    background: #E6F5EC;
    color: #009F6B;
}

.badge-secondary {
    background: #E4E7EB;
    color: #6B7280;
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

.btn-info {
    background: #D1ECF1;
    color: #0C5460;
}

.btn-warning {
    background: #FFF3CD;
    color: #B59C00;
}

.btn-success {
    background: #D4EDDA;
    color: #155724;
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

.text-muted {
    color: #6B7280;
}
</style>
@endsection

