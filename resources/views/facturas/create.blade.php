@extends('layouts.app')

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>{{ isset($factura) ? 'Editar Factura' : 'Nueva Factura' }}</h1>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">← Volver</a>
    </div>

    <form
        method="POST"
        action="{{ isset($factura) ? route('facturas.update', $factura) : route('facturas.store') }}"
        enctype="multipart/form-data"
        class="form-card"
        id="facturaForm"
    >
        @csrf
        @method(isset($factura) ? 'PUT' : 'POST')

        {{-- Información Básica --}}
        <div class="form-section">
            <h3>Información Básica</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="numero_factura" class="required">Número de Factura</label>
                    <input
                        type="text"
                        id="numero_factura"
                        name="numero_factura"
                        class="form-control @error('numero_factura') is-invalid @enderror"
                        placeholder="Ej: FAC-001-2025"
                        value="{{ old('numero_factura', $factura->numero_factura ?? '') }}"
                        required
                    >
                    @error('numero_factura')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tercero_id" class="required">Cliente</label>
                    <select id="tercero_id" name="tercero_id" class="form-control @error('tercero_id') is-invalid @enderror" required>
                        <option value="">Selecciona un cliente</option>
                        @foreach($terceros as $tercero)
                            <option value="{{ $tercero->id }}" {{ (old('tercero_id', $factura->tercero_id ?? '') == $tercero->id) ? 'selected' : '' }}>
                                {{ $tercero->nombre_razon_social }}
                            </option>
                        @endforeach
                    </select>
                    @error('tercero_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Fechas --}}
        <div class="form-section">
            <h3>Fechas</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_factura" class="required">Fecha de Factura</label>
                    <input
                        type="datetime-local"
                        id="fecha_factura"
                        name="fecha_factura"
                        class="form-control @error('fecha_factura') is-invalid @enderror"
                        value="{{ old('fecha_factura', $factura->fecha_factura ? $factura->fecha_factura->format('Y-m-d\TH:i') : '') }}"
                        required
                    >
                    @error('fecha_factura')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                    <input
                        type="date"
                        id="fecha_vencimiento"
                        name="fecha_vencimiento"
                        class="form-control"
                        value="{{ old('fecha_vencimiento', $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('Y-m-d') : '') }}"
                    >
                </div>
            </div>
        </div>

        {{-- Información Financiera --}}
        <div class="form-section">
            <h3>Información Financiera</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="subtotal" class="required">Subtotal</label>
                    <input
                        type="number"
                        id="subtotal"
                        name="subtotal"
                        class="form-control @error('subtotal') is-invalid @enderror"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        value="{{ old('subtotal', $factura->subtotal ?? '0') }}"
                        required
                        onchange="calcularTotal()"
                    >
                    @error('subtotal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="iva">IVA (19%)</label>
                    <input
                        type="number"
                        id="iva"
                        name="iva"
                        class="form-control"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        value="{{ old('iva', $factura->iva ?? '0') }}"
                        onchange="calcularTotal()"
                    >
                </div>

                <div class="form-group">
                    <label for="descuento">Descuento</label>
                    <input
                        type="number"
                        id="descuento"
                        name="descuento"
                        class="form-control"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        value="{{ old('descuento', $factura->descuento ?? '0') }}"
                        onchange="calcularTotal()"
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Total a Pagar</label>
                <div class="total-display">
                    <span>$</span>
                    <span id="totalPagar">{{ number_format($factura->total_pagar ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Información de Motonave --}}
        <div class="form-section">
            <h3>Información de Motonave</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="motonave">Nombre de Motonave</label>
                    <input
                        type="text"
                        id="motonave"
                        name="motonave"
                        class="form-control"
                        placeholder="Ej: MN Marítimos"
                        value="{{ old('motonave', $factura->motonave ?? '') }}"
                    >
                </div>

                <div class="form-group">
                    <label for="trb">TRB</label>
                    <input
                        type="text"
                        id="trb"
                        name="trb"
                        class="form-control"
                        placeholder="Ej: 12345"
                        value="{{ old('trb', $factura->trb ?? '') }}"
                    >
                </div>
            </div>
        </div>

        {{-- Descripción del Servicio --}}
        <div class="form-section">
            <h3>Descripción del Servicio</h3>

            <div class="form-group">
                <label for="servicio_descripcion">Descripción</label>
                <textarea
                    id="servicio_descripcion"
                    name="servicio_descripcion"
                    class="form-control"
                    placeholder="Detalla los servicios prestados..."
                    rows="6"
                >{{ old('servicio_descripcion', $factura->servicio_descripcion ?? '') }}</textarea>
            </div>
        </div>

        {{-- Archivo PDF --}}
        <div class="form-section">
            <h3>Documento PDF</h3>

            <div class="form-group">
                <label for="pdf">Archivo PDF</label>
                <input
                    type="file"
                    id="pdf"
                    name="pdf"
                    class="form-control"
                    accept=".pdf"
                >
                <small class="form-text">Máximo 5MB. Formato: PDF</small>
                @if(isset($factura) && $factura->tienePdf())
                    <div class="pdf-info">
                        ✓ PDF actual: <a href="{{ route('facturas.download-pdf', $factura) }}">Descargar</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estado --}}
        <div class="form-section">
            <h3>Estado</h3>

            <div class="form-group">
                <label for="estado" class="required">Estado de la Factura</label>
                <select id="estado" name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                    <option value="pendiente" {{ old('estado', $factura->estado ?? 'pendiente') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagada" {{ old('estado', $factura->estado ?? '') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="cancelada" {{ old('estado', $factura->estado ?? '') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                @error('estado')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                {{ isset($factura) ? '✓ Actualizar Factura' : '✓ Crear Factura' }}
            </button>
            <a href="{{ route('facturas.index') }}" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.form-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.form-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #E4E7EB;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1F2933;
    margin: 0 0 15px 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #1F2933;
    margin-bottom: 8px;
    font-size: 14px;
}

.required::after {
    content: " *";
    color: #DC3545;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #D4D9E2;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-control:focus {
    border-color: #2767C6;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
    outline: none;
}

.form-control.is-invalid {
    border-color: #DC3545;
}

.invalid-feedback {
    color: #DC3545;
    font-size: 12px;
    margin-top: 4px;
}

textarea.form-control {
    font-family: monospace;
    resize: vertical;
    min-height: 100px;
}

.form-text {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
}

.pdf-info {
    margin-top: 10px;
    padding: 10px;
    background: #E6F5EC;
    border-left: 4px solid #009F6B;
    color: #009F6B;
    font-size: 13px;
}

.pdf-info a {
    color: #009F6B;
    text-decoration: underline;
}

.total-display {
    background: #F5F7FA;
    padding: 15px;
    border-radius: 6px;
    font-size: 20px;
    font-weight: 700;
    color: #2767C6;
    border: 2px solid #D4D9E2;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    justify-content: flex-end;
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

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}

.btn-secondary {
    background: #6B7280;
    color: white;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}
</style>

<script>
function calcularTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
    const iva = parseFloat(document.getElementById('iva').value) || 0;
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;

    const total = (subtotal + iva) - descuento;

    document.getElementById('totalPagar').textContent = total.toFixed(2);
}

// Calcular total al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    calcularTotal();
});
</script>
@endsection

