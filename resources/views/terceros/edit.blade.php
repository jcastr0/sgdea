@extends('layouts.app')

@section('content')
    {{-- Esta vista es igual a create.blade.php pero con el tercero editando --}}

<div class="form-container">
    <div class="form-header">
        <h1>Editar Tercero</h1>
        <a href="{{ route('terceros.index') }}" class="btn btn-secondary">← Volver</a>
    </div>

    {{-- Modal de duplicados --}}
    @if (isset($mostrar_duplicados) && $duplicados)
        <div class="modal-overlay" id="duplicadosModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>⚠️ Posibles Duplicados Encontrados</h2>
                    <button type="button" class="modal-close" onclick="document.getElementById('duplicadosModal').style.display='none'">×</button>
                </div>
                <div class="modal-body">
                    <p>Encontramos los siguientes terceros que podrían ser duplicados:</p>

                    <table class="duplicates-table">
                        <thead>
                            <tr>
                                <th>NIT</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Similitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($duplicados as $dup)
                                <tr>
                                    <td><strong>{{ $dup['nit'] }}</strong></td>
                                    <td>{{ $dup['nombre_razon_social'] }}</td>
                                    <td>{{ $dup['telefono'] ?? '-' }}</td>
                                    <td>
                                        <span class="similitud-badge">{{ $dup['similitud'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="modal-actions">
                        <form method="POST" action="{{ route('terceros.update', $tercero) }}" style="display: inline;">
                            @csrf
                            @method('PUT')
                            @foreach ($tercero->only(['nit', 'nombre_razon_social', 'telefono', 'email', 'direccion', 'notas', 'estado']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="confirmar_actualizar" value="1">
                            <button type="submit" class="btn btn-primary">
                                Actualizar de Todas Formas
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Formulario --}}
    <form
        method="POST"
        action="{{ route('terceros.update', $tercero) }}"
        class="form-card"
    >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nit" class="required">NIT</label>
            <input
                type="text"
                id="nit"
                name="nit"
                class="form-control @error('nit') is-invalid @enderror"
                placeholder="Ej: 123456789"
                value="{{ old('nit', $tercero->nit) }}"
                required
            >
            @error('nit')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <small class="form-text">Solo números, entre 6 y 15 dígitos</small>
        </div>

        <div class="form-group">
            <label for="nombre_razon_social" class="required">Nombre/Razón Social</label>
            <input
                type="text"
                id="nombre_razon_social"
                name="nombre_razon_social"
                class="form-control @error('nombre_razon_social') is-invalid @enderror"
                placeholder="Nombre completo del tercero"
                value="{{ old('nombre_razon_social', $tercero->nombre_razon_social) }}"
                required
            >
            @error('nombre_razon_social')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    class="form-control"
                    placeholder="Ej: 3001234567"
                    value="{{ old('telefono', $tercero->telefono) }}"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="correo@ejemplo.com"
                    value="{{ old('email', $tercero->email) }}"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input
                type="text"
                id="direccion"
                name="direccion"
                class="form-control"
                placeholder="Dirección completa"
                value="{{ old('direccion', $tercero->direccion) }}"
            >
        </div>

        <div class="form-group">
            <label for="notas">Notas</label>
            <textarea
                id="notas"
                name="notas"
                class="form-control"
                placeholder="Notas adicionales..."
                rows="4"
            >{{ old('notas', $tercero->notas) }}</textarea>
        </div>

        <div class="form-group">
            <label for="estado" class="required">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="activo" {{ old('estado', $tercero->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $tercero->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                Actualizar Tercero
            </button>
            <a href="{{ route('terceros.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 600px;
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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
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
    display: block;
}

.form-text {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
    display: block;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

textarea.form-control {
    font-family: monospace;
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    justify-content: space-between;
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

.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}

.btn-outline-secondary {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline-secondary:hover {
    background: #F5F7FA;
}

.btn-secondary {
    background: #6B7280;
    color: white;
}

/* Modal de duplicados */
.modal-overlay {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 2px solid #D4D9E2;
}

.modal-header h2 {
    margin: 0;
    font-size: 18px;
    color: #DC3545;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6B7280;
}

.modal-body {
    padding: 20px;
}

.duplicates-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    font-size: 13px;
}

.duplicates-table thead {
    background: #F5F7FA;
}

.duplicates-table th {
    padding: 10px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #D4D9E2;
}

.duplicates-table td {
    padding: 10px;
    border-bottom: 1px solid #D4D9E2;
}

.similitud-badge {
    background: #FFE5E5;
    color: #DC3545;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 11px;
}

.modal-actions {
    padding: 20px;
    border-top: 2px solid #D4D9E2;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
</style>
@endsection

