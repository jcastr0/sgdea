@extends('layouts.app')

@section('content')
<div class="auditoria-detalles-layout">
    <div class="header-section">
        <div>
            <h1>Auditoría Completa</h1>
            <p>{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }} - Historial y Verificación</p>
        </div>
        <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline">← Volver</a>
    </div>

    {{-- Layout de 2 columnas: PDF + Auditoría --}}
    <div class="audit-layout">
        {{-- Columna 1: Visor de PDF --}}
        <div class="pdf-column">
            <div class="pdf-container">
                @if($pdfPath)
                    <div class="pdf-viewer">
                        <iframe src="{{ $pdfPath }}" class="pdf-iframe" type="application/pdf"></iframe>
                    </div>
                @else
                    <div class="sin-pdf">
                        <div class="sin-pdf-icon">📄</div>
                        <p>No hay PDF asociado a este registro</p>
                        <small>{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Columna 2: Auditoría Completa --}}
        <div class="audit-column">
            {{-- Alerta de Integridad --}}
            <div class="alerta-integridad {{ $integro ? 'alerta-exito' : 'alerta-peligro' }}">
                <strong>{{ $integro ? '✓ Integridad Verificada' : '⚠️ ADVERTENCIA DE INTEGRIDAD' }}</strong>
                <p>{{ $integro ? 'Este registro no ha sido alterado desde su creación.' : 'El hash NO coincide. Posible alteración detectada.' }}</p>
            </div>

            {{-- Evento Actual --}}
            <div class="event-card current">
                <div class="event-header">
                    <span class="event-badge badge-{{ $log->action }}">{{ ucfirst($log->action) }}</span>
                    <span class="event-time">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="event-user">
                    @if($log->user)
                        <strong>{{ $log->user->name }}</strong><br>
                        <small>{{ $log->user->email }}</small>
                    @else
                        <strong>Sistema</strong>
                    @endif
                </div>
                <div class="event-description">{{ $log->description }}</div>
                <div class="event-ip">
                    <code>{{ $log->ip_address }}</code>
                </div>

                {{-- Cambios en este evento --}}
                @if($log->old_values || $log->new_values)
                <div class="event-changes">
                    @if($log->old_values)
                    <div class="change-item">
                        <strong>Antes:</strong>
                        <pre class="json-mini">{{ json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                    @if($log->new_values)
                    <div class="change-item">
                        <strong>Después:</strong>
                        <pre class="json-mini">{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Hash de integridad --}}
                <div class="event-hash">
                    <strong>Hash SHA-256:</strong>
                    <code class="hash-code">{{ $log->hash }}</code>
                </div>
            </div>

            {{-- Historial Completo de la Entidad --}}
            <div class="audit-history">
                <h3>📜 Historial Completo</h3>
                <p class="history-subtitle">{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</p>

                <div class="timeline">
                    @foreach($auditCompleta as $evento)
                    <div class="timeline-item {{ $evento->id === $log->id ? 'current-event' : '' }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-action badge badge-{{ $evento->action }}">{{ ucfirst($evento->action) }}</span>
                                <span class="timeline-time">{{ $evento->created_at->format('d/m/Y H:i:s') }}</span>
                                @if($evento->id === $log->id)
                                <span class="timeline-current">ACTUAL</span>
                                @endif
                            </div>
                            <div class="timeline-user">
                                @if($evento->user)
                                    {{ $evento->user->name }}
                                @else
                                    Sistema
                                @endif
                            </div>
                            <div class="timeline-ip">{{ $evento->ip_address ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auditoria-detalles-layout {
    max-width: 1600px;
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

.audit-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 1200px) {
    .audit-layout {
        grid-template-columns: 1fr;
    }
}

{{-- COLUMNA 1: PDF --}}
.pdf-column {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.pdf-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.pdf-viewer {
    width: 100%;
    height: 600px;
    background: #F5F7FA;
}

.pdf-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.sin-pdf {
    padding: 60px 20px;
    text-align: center;
    background: #F5F7FA;
}

.sin-pdf-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.sin-pdf p {
    color: #6B7280;
    margin: 10px 0;
}

.sin-pdf small {
    color: #9CA3AF;
}

{{-- COLUMNA 2: AUDITORÍA --}}
.audit-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.alerta-integridad {
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid;
}

.alerta-integridad strong {
    display: block;
    margin-bottom: 3px;
    font-size: 14px;
}

.alerta-integridad p {
    margin: 0;
    font-size: 13px;
}

.alerta-exito {
    background: #E6F5EC;
    color: #009F6B;
    border-left-color: #28A745;
}

.alerta-peligro {
    background: #F8D7DA;
    color: #DC3545;
    border-left-color: #DC3545;
}

{{-- EVENTO ACTUAL --}}
.event-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #2767C6;
}

.event-card.current {
    background: linear-gradient(135deg, #F0F4FF 0%, #FFFFFF 100%);
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    gap: 10px;
}

.event-badge {
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

.event-time {
    font-size: 13px;
    color: #6B7280;
}

.event-user {
    margin-bottom: 10px;
    font-size: 13px;
}

.event-user strong {
    color: #1F2933;
}

.event-user small {
    color: #6B7280;
}

.event-description {
    margin-bottom: 10px;
    font-size: 13px;
    color: #1F2933;
}

.event-ip {
    margin-bottom: 15px;
}

.event-ip code {
    background: #F5F7FA;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    color: #2767C6;
}

.event-changes {
    border-top: 1px solid #D4D9E2;
    padding-top: 15px;
    margin-bottom: 15px;
}

.change-item {
    margin-bottom: 10px;
}

.change-item strong {
    display: block;
    font-size: 12px;
    color: #1F2933;
    margin-bottom: 5px;
}

.json-mini {
    background: #F5F7FA;
    border: 1px solid #D4D9E2;
    border-radius: 4px;
    padding: 8px;
    margin: 0;
    font-size: 11px;
    line-height: 1.4;
    max-height: 150px;
    overflow: auto;
}

.event-hash {
    border-top: 1px solid #D4D9E2;
    padding-top: 15px;
}

.event-hash strong {
    display: block;
    font-size: 12px;
    margin-bottom: 5px;
}

.hash-code {
    background: #F5F7FA;
    padding: 4px 8px;
    border-radius: 4px;
    display: block;
    word-break: break-all;
    font-size: 10px;
    color: #2767C6;
}

{{-- HISTORIAL --}}
.audit-history {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.audit-history h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 700;
    color: #1F2933;
}

.history-subtitle {
    margin: 0 0 20px 0;
    font-size: 12px;
    color: #6B7280;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #D4D9E2;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
}

.timeline-item.current-event {
    background: #F0F4FF;
    padding: 10px;
    border-radius: 4px;
    margin-left: -10px;
    padding-left: 20px;
}

.timeline-dot {
    position: absolute;
    left: -27px;
    top: 3px;
    width: 12px;
    height: 12px;
    background: white;
    border: 2px solid #2767C6;
    border-radius: 50%;
}

.timeline-item.current-event .timeline-dot {
    background: #2767C6;
    width: 14px;
    height: 14px;
    left: -28px;
    top: 1px;
}

.timeline-content {
    font-size: 12px;
}

.timeline-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 3px;
}

.timeline-action {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

.timeline-time {
    color: #6B7280;
    font-size: 11px;
}

.timeline-current {
    background: #2767C6;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 600;
    margin-left: auto;
}

.timeline-user {
    color: #1F2933;
    font-weight: 500;
}

.timeline-ip {
    color: #9CA3AF;
    font-size: 10px;
    font-family: monospace;
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

