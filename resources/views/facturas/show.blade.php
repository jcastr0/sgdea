@extends('layouts.app')

@section('content')
<div class="factura-detalle-container">
    {{-- Header con estado --}}
    <div class="factura-header">
        <div class="header-left">
            <h1>Factura #{{ $factura->numero_factura }}</h1>
            <p class="cufe-info">
                CUFE: <code>{{ substr($factura->cufe, 0, 30) }}...</code>
                <span class="copy-icon" onclick="copiarAlPortapapeles('{{ $factura->cufe }}')">📋</span>
            </p>
        </div>
        <div class="header-right">
            @php
                $badge = $factura->getEstadoBadge();
            @endphp
            <span class="estado-badge badge-{{ $badge['clase'] }}">
                {{ $badge['texto'] }}
            </span>
        </div>
    </div>

    {{-- Botones de acción --}}
    <div class="action-buttons-top">
        @if($factura->tienePdf())
            <button class="btn btn-primary" onclick="abrirModalPDF()">
                👁️ Ver PDF
            </button>
            <a href="{{ route('facturas.download-pdf', $factura) }}" class="btn btn-secondary">
                📥 Descargar PDF
            </a>
        @endif
        <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-info">
            ✏️ Editar
        </a>
        <a href="{{ route('facturas.index') }}" class="btn btn-outline">
            ← Volver
        </a>
    </div>

    {{-- Modal visor PDF --}}
    @if($factura->tienePdf())
        <div id="pdfModal" class="modal-pdf" style="display: none;">
            <div class="modal-pdf-content">
                <button class="modal-close" onclick="cerrarModalPDF()">✕</button>
                <div class="pdf-viewer-controls">
                    <button onclick="previousPage()">← Anterior</button>
                    <span id="pageInfo">Página 1</span>
                    <button onclick="nextPage()">Siguiente →</button>
                    <button onclick="zoomIn()">🔍+</button>
                    <button onclick="zoomOut()">🔍-</button>
                    <button onclick="toggleFullscreen()">⛶</button>
                </div>
                <div class="pdf-viewer" id="pdfViewer">
                    <iframe id="pdfIframe" src="{{ $factura->getPdfUrl() }}#toolbar=1" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    @endif

    {{-- Cards de información --}}
    <div class="factura-grid">
        {{-- Sección: Cliente --}}
        <div class="card">
            <h3>Cliente</h3>
            <div class="info-group">
                <div class="info-item">
                    <label>Nombre/Razón Social</label>
                    <p>{{ $factura->tercero->nombre_razon_social }}</p>
                </div>
                <div class="info-item">
                    <label>NIT</label>
                    <p>{{ $factura->tercero->nit }}</p>
                </div>
                <div class="info-item">
                    <label>Teléfono</label>
                    <p>{{ $factura->tercero->telefono ?? '-' }}</p>
                </div>
                <div class="info-item">
                    <label>Dirección</label>
                    <p>{{ $factura->tercero->direccion ?? '-' }}</p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p>{{ $factura->tercero->email ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Sección: Fechas --}}
        <div class="card">
            <h3>Fechas</h3>
            <div class="info-group">
                <div class="info-item">
                    <label>Fecha Factura</label>
                    <p>{{ $factura->fecha_factura->format('d/m/Y H:i') }}</p>
                </div>
                <div class="info-item">
                    <label>Fecha Vencimiento</label>
                    <p>{{ $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('d/m/Y') : '-' }}</p>
                </div>
                <div class="info-item">
                    <label>Creado</label>
                    <p>{{ $factura->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <p>{{ $factura->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Sección: Motonave --}}
        @if($factura->motonave || $factura->trb)
            <div class="card">
                <h3>Información de Motonave</h3>
                <div class="info-group">
                    <div class="info-item">
                        <label>Motonave</label>
                        <p>{{ $factura->motonave ?? '-' }}</p>
                    </div>
                    <div class="info-item">
                        <label>TRB</label>
                        <p>{{ $factura->trb ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Sección: Servicios/Descripción --}}
    @if($factura->servicio_descripcion)
        <div class="card card-full">
            <h3>Descripción del Servicio</h3>
            <div class="descripcion-box">
                {{ $factura->servicio_descripcion }}
            </div>
        </div>
    @endif

    {{-- Sección: Financiera --}}
    <div class="card card-full">
        <h3>Resumen Financiero</h3>
        <div class="financial-summary">
            <div class="financial-row">
                <span>Subtotal</span>
                <strong>${{ number_format($factura->subtotal, 2) }}</strong>
            </div>
            <div class="financial-row">
                <span>IVA (19%)</span>
                <strong>${{ number_format($factura->iva, 2) }}</strong>
            </div>
            @if($factura->descuento > 0)
                <div class="financial-row">
                    <span>Descuento</span>
                    <strong>-${{ number_format($factura->descuento, 2) }}</strong>
                </div>
            @endif
            <div class="financial-row total">
                <span>Total a Pagar</span>
                <strong>${{ number_format($factura->total_pagar, 2) }}</strong>
            </div>
        </div>
    </div>

    {{-- Sección: Metadata --}}
    <div class="card card-full metadata-card">
        <h3>Información Técnica</h3>
        <div class="metadata-grid">
            <div class="metadata-item">
                <label>CUFE</label>
                <code class="code-block">{{ $factura->cufe }}</code>
            </div>
            @if($factura->hash_pdf)
                <div class="metadata-item">
                    <label>Hash PDF (SHA-256)</label>
                    <code class="code-block">{{ $factura->hash_pdf }}</code>
                    <small class="integrity-check">
                        @if($factura->verificarIntegridadPdf())
                            ✅ PDF íntegro
                        @else
                            ⚠️ PDF modificado
                        @endif
                    </small>
                </div>
            @endif
            <div class="metadata-item">
                <label>Estado</label>
                <p>{{ ucfirst($factura->estado) }}</p>
            </div>
        </div>
    </div>

    {{-- Botones finales --}}
    <div class="action-buttons-bottom">
        <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-info btn-lg">
            ✏️ Editar Factura
        </a>
        <form method="POST" action="{{ route('facturas.destroy', $factura) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('¿Estás seguro de que quieres eliminar esta factura?')">
                🗑️ Eliminar
            </button>
        </form>
        <a href="{{ route('facturas.index') }}" class="btn btn-outline btn-lg">
            ← Volver al Listado
        </a>
    </div>
</div>

<style>
.factura-detalle-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.factura-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.header-left h1 {
    font-size: 32px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.cufe-info {
    color: #6B7280;
    font-size: 12px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cufe-info code {
    background: #F5F7FA;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
}

.copy-icon {
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.copy-icon:hover {
    opacity: 1;
}

.header-right {
    text-align: right;
}

.estado-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
}

.badge-badge-warning {
    background: #FFF3CD;
    color: #B59C00;
}

.badge-badge-success {
    background: #E6F5EC;
    color: #009F6B;
}

.badge-badge-secondary {
    background: #E4E7EB;
    color: #6B7280;
}

.action-buttons-top {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    flex-wrap: wrap;
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
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39, 103, 198, 0.3);
}

.btn-secondary {
    background: #6B7280;
    color: white;
}

.btn-secondary:hover {
    background: #4B5563;
}

.btn-info {
    background: #D1ECF1;
    color: #0C5460;
}

.btn-info:hover {
    background: #B8E0E8;
}

.btn-danger {
    background: #F8D7DA;
    color: #DC3545;
}

.btn-danger:hover {
    background: #F5C2C7;
}

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}

.factura-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
    margin: 0 0 15px 0;
    border-bottom: 2px solid #E4E7EB;
    padding-bottom: 10px;
}

.info-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-size: 11px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.info-item p {
    font-size: 14px;
    color: #1F2933;
    margin: 0;
    word-break: break-word;
}

.card-full {
    grid-column: 1 / -1;
}

.descripcion-box {
    background: #F5F7FA;
    padding: 15px;
    border-radius: 6px;
    color: #1F2933;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.financial-summary {
    background: #F5F7FA;
    padding: 20px;
    border-radius: 6px;
}

.financial-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #D4D9E2;
    font-size: 14px;
}

.financial-row.total {
    border-bottom: none;
    border-top: 2px solid #2767C6;
    padding-top: 15px;
    margin-top: 10px;
    font-size: 16px;
    color: #2767C6;
}

.financial-row strong {
    font-weight: 700;
    color: #1F2933;
}

.financial-row.total strong {
    color: #2767C6;
}

.metadata-card {
    background: #F9FAFB;
    border: 1px solid #E4E7EB;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.metadata-item {
    padding: 15px;
    background: white;
    border-radius: 6px;
    border: 1px solid #E4E7EB;
}

.metadata-item label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.code-block {
    display: block;
    background: #F5F7FA;
    padding: 10px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 11px;
    color: #1F2933;
    word-break: break-all;
    margin-bottom: 8px;
    border: 1px solid #D4D9E2;
}

.integrity-check {
    display: block;
    font-size: 12px;
    margin-top: 8px;
    padding: 6px;
    border-radius: 4px;
    background: #E6F5EC;
    color: #009F6B;
}

.action-buttons-bottom {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    justify-content: center;
    flex-wrap: wrap;
    padding-top: 20px;
    border-top: 2px solid #D4D9E2;
}

/* Modal PDF */
.modal-pdf {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-pdf-content {
    background: white;
    width: 90%;
    height: 90vh;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #DC3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 24px;
    cursor: pointer;
    z-index: 10000;
}

.pdf-viewer-controls {
    background: #F5F7FA;
    padding: 15px;
    display: flex;
    gap: 10px;
    align-items: center;
    border-bottom: 1px solid #D4D9E2;
}

.pdf-viewer-controls button {
    padding: 8px 12px;
    background: #2767C6;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
}

.pdf-viewer {
    flex: 1;
    overflow: auto;
}

#pageInfo {
    font-size: 12px;
    color: #6B7280;
    font-weight: 600;
}

@media (max-width: 768px) {
    .factura-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .action-buttons-top {
        width: 100%;
    }

    .factura-grid {
        grid-template-columns: 1fr;
    }

    .modal-pdf-content {
        width: 95%;
        height: 95vh;
    }
}
</style>

<script>
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        alert('CUFE copiado al portapapeles');
    });
}

function abrirModalPDF() {
    document.getElementById('pdfModal').style.display = 'flex';
}

function cerrarModalPDF() {
    document.getElementById('pdfModal').style.display = 'none';
}

function previousPage() {
    const iframe = document.getElementById('pdfIframe');
    iframe.src = iframe.src.split('#')[0] + '#page=1&toolbar=1';
}

function nextPage() {
    const iframe = document.getElementById('pdfIframe');
    iframe.src = iframe.src.split('#')[0] + '#page=2&toolbar=1';
}

function zoomIn() {
    const iframe = document.getElementById('pdfIframe');
    iframe.style.transform = 'scale(1.1)';
}

function zoomOut() {
    const iframe = document.getElementById('pdfIframe');
    iframe.style.transform = 'scale(0.9)';
}

function toggleFullscreen() {
    const viewer = document.getElementById('pdfViewer');
    if (viewer.requestFullscreen) {
        viewer.requestFullscreen();
    }
}

// Cerrar modal al hacer click fuera
document.addEventListener('click', function(event) {
    const modal = document.getElementById('pdfModal');
    const content = document.querySelector('.modal-pdf-content');
    if (modal && event.target === modal) {
        cerrarModalPDF();
    }
});

// Cerrar con tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('pdfModal');
        if (modal && modal.style.display !== 'none') {
            cerrarModalPDF();
        }
    }
});
</script>
@endsection

