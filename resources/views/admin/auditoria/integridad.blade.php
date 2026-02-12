@extends('layouts.app')

@section('content')
<div class="integridad-container">
    <div class="header-section">
        <div>
            <h1>Verificación de Integridad</h1>
            <p>Validación de todos los registros de auditoría</p>
        </div>
        <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline">← Volver</a>
    </div>

    {{-- Resumen --}}
    <div class="resumen-cards">
        <div class="resumen-card total">
            <div class="card-icon">📊</div>
            <div class="card-content">
                <div class="card-valor">{{ $resultados['total'] }}</div>
                <div class="card-label">Total de Registros</div>
            </div>
        </div>

        <div class="resumen-card integros">
            <div class="card-icon">✓</div>
            <div class="card-content">
                <div class="card-valor">{{ $resultados['integros'] }}</div>
                <div class="card-label">Registros Íntegros</div>
                <div class="card-porcentaje">{{ $resultados['total'] > 0 ? round(($resultados['integros'] / $resultados['total']) * 100) : 0 }}%</div>
            </div>
        </div>

        <div class="resumen-card alterados">
            <div class="card-icon">⚠️</div>
            <div class="card-content">
                <div class="card-valor">{{ $resultados['alterados'] }}</div>
                <div class="card-label">Registros Alterados</div>
                <div class="card-porcentaje">{{ $resultados['total'] > 0 ? round(($resultados['alterados'] / $resultados['total']) * 100) : 0 }}%</div>
            </div>
        </div>
    </div>

    {{-- Resultado General --}}
    <div class="resultado-card {{ $resultados['alterados'] == 0 ? 'exito' : 'alerta' }}">
        @if($resultados['alterados'] == 0)
            <strong>✓ AUDITORÍA ÍNTEGRA</strong>
            <p>Todos los {{ $resultados['total'] }} registros han sido verificados correctamente. No se detectaron alteraciones.</p>
        @else
            <strong>⚠️ ALTERACIONES DETECTADAS</strong>
            <p>Se encontraron {{ $resultados['alterados'] }} registro(s) con hash no coincidente. Ver detalles abajo.</p>
        @endif
    </div>

    {{-- Tabla de Registros Alterados --}}
    @if($resultados['alterados'] > 0)
    <div class="table-card">
        <h3>Registros con Alteraciones Detectadas</h3>
        <table class="integridad-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados['logsAlterados'] as $log)
                <tr class="alterado">
                    <td><code>{{ $log['id'] }}</code></td>
                    <td>{{ $log['fecha']->format('d/m/Y H:i:s') }}</td>
                    <td>{{ substr($log['descripción'], 0, 60) }}...</td>
                    <td>
                        <a href="{{ route('admin.auditoria.show', $log['id']) }}" class="btn-accion">
                            📋 Ver
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Información de Compliance --}}
    <div class="compliance-card">
        <h3>Compliance Fiscal</h3>
        <p>La integridad de los registros de auditoría es obligatoria según las regulaciones fiscales colombianas. Todos los registros cuentan con:</p>
        <ul>
            <li>✓ Hash SHA-256 para verificar no alteración</li>
            <li>✓ Timestamp inmutable</li>
            <li>✓ Usuario responsable registrado</li>
            <li>✓ IP de origen</li>
            <li>✓ Valores antes y después de cambios</li>
            <li>✓ Sin posibilidad de actualizar o eliminar registros</li>
        </ul>
    </div>
</div>

<style>
.integridad-container {
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

.resumen-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .resumen-cards {
        grid-template-columns: 1fr;
    }
}

.resumen-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid;
    display: flex;
    align-items: center;
    gap: 15px;
}

.resumen-card.total {
    border-left-color: #2767C6;
}

.resumen-card.integros {
    border-left-color: #28A745;
}

.resumen-card.alterados {
    border-left-color: #DC3545;
}

.card-icon {
    font-size: 28px;
}

.card-content {
    flex: 1;
}

.card-valor {
    font-size: 28px;
    font-weight: 700;
    color: #1F2933;
}

.card-label {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
}

.card-porcentaje {
    font-size: 14px;
    font-weight: 600;
    color: #2767C6;
    margin-top: 4px;
}

.resultado-card {
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid;
}

.resultado-card.exito {
    background: #E6F5EC;
    color: #009F6B;
    border-left-color: #28A745;
}

.resultado-card.alerta {
    background: #F8D7DA;
    color: #DC3545;
    border-left-color: #DC3545;
}

.resultado-card strong {
    display: block;
    margin-bottom: 5px;
}

.resultado-card p {
    margin: 0;
}

.table-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.table-card h3 {
    margin: 0 0 20px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
}

.integridad-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.integridad-table thead {
    background: #F5F7FA;
    border-bottom: 2px solid #D4D9E2;
}

.integridad-table th,
.integridad-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #D4D9E2;
}

.integridad-table th {
    font-weight: 700;
    color: #1F2933;
}

.integridad-table tr.alterado {
    background: #F8D7DA;
}

.integridad-table tr.alterado:hover {
    background: #F5C6CB;
}

code {
    background: #F5F7FA;
    padding: 2px 6px;
    border-radius: 4px;
    color: #2767C6;
    font-family: monospace;
    font-size: 12px;
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

.compliance-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.compliance-card h3 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
}

.compliance-card p {
    margin: 0 0 15px 0;
    color: #6B7280;
}

.compliance-card ul {
    margin: 0;
    padding-left: 20px;
}

.compliance-card li {
    color: #6B7280;
    margin-bottom: 8px;
    font-size: 14px;
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

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}
</style>
@endsection

