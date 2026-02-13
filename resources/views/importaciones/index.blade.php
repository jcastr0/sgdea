@extends('layouts.sgdea')

@section('page-title', 'Importaciones')

@section('content')
<div class="importaciones-container">
    <div class="importaciones-header">
        <div>
            <h1>Centro de Importaciones</h1>
            <p>Importa facturas desde Excel o archivos PDF</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-button active" onclick="cambiarTab('excel')">
                📊 Importar Excel
            </button>
            <button class="tab-button" onclick="cambiarTab('pdf')">
                📄 Importar PDFs
            </button>
            <button class="tab-button" onclick="cambiarTab('historial')">
                📜 Historial
            </button>
        </div>

        {{-- TAB 1: IMPORTAR EXCEL --}}
        <div id="tab-excel" class="tab-content active">
            <div class="card">
                <h3>Importar Facturas desde Excel</h3>

                <div class="form-group">
                    <label>Archivo Excel (.xlsx, .xls)</label>
                    <div class="file-input-wrapper">
                        <input
                            type="file"
                            id="excelFile"
                            accept=".xlsx,.xls"
                            class="file-input"
                            onchange="validarExcel()"
                        >
                        <label for="excelFile" class="file-label">
                            📁 Seleccionar archivo o arrastrar aquí
                        </label>
                    </div>
                    <small>Máximo 10MB</small>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="validateDuplicates" checked>
                        Validar duplicados
                    </label>
                    <small>Previene importar facturas que ya existen</small>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="createTerceros" checked>
                        Crear nuevos terceros si no existen
                    </label>
                    <small>Si el cliente no está registrado, se crea automáticamente</small>
                </div>

                {{-- Preview --}}
                <div id="previewContainer" class="preview-section" style="display: none;">
                    <div class="preview-header">
                        <h4>📋 Vista Previa del Archivo</h4>
                        <span class="preview-badge">Primeras 5 filas</span>
                    </div>
                    <div class="table-preview">
                        <table id="previewTable">
                            <thead>
                                <tr id="previewHeader"></tr>
                            </thead>
                            <tbody id="previewBody"></tbody>
                        </table>
                    </div>
                    <p id="previewInfo" class="preview-info"></p>
                </div>

                {{-- Progreso --}}
                <div id="progressContainer" style="display: none;">
                    <h4>Importando...</h4>

                    {{-- Registro actual siendo procesado --}}
                    <div id="currentRecordContainer" class="current-record">
                        <div class="current-record-label">Procesando registro:</div>
                        <div id="currentRecordInfo" class="current-record-data">
                            <span class="record-number">-</span>
                            <span class="record-detail">Esperando...</span>
                        </div>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <p id="progressText">0% - Procesando fila <span id="currentRow">0</span> de <span id="totalRows">0</span></p>

                    <div class="stats">
                        <div class="stat">
                            <span class="stat-label">✓ Exitosas</span>
                            <span class="stat-value success-color" id="statSuccessful">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">⊕ Duplicados</span>
                            <span class="stat-value warning-color" id="statDuplicates">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">✗ Errores</span>
                            <span class="stat-value error-color" id="statFailed">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Total</span>
                            <span class="stat-value" id="statTotal">0</span>
                        </div>
                    </div>

                    <div class="import-log">
                        <h5>📋 Log de Importación (últimos 15 registros)</h5>
                        <div id="importLogContent" class="log-content"></div>
                    </div>
                </div>

                {{-- Resultado --}}
                <div id="resultContainer" style="display: none;">
                    <h4>Importación Completada</h4>
                    <div class="result-summary">
                        <div class="result-item success">
                            <span class="result-icon">✓</span>
                            <div>
                                <strong id="resultSuccessful">0</strong>
                                <p>Facturas creadas</p>
                            </div>
                        </div>
                        <div class="result-item duplicate">
                            <span class="result-icon">⊕</span>
                            <div>
                                <strong id="resultDuplicate">0</strong>
                                <p>Duplicadas (no importadas)</p>
                            </div>
                        </div>
                        <div class="result-item error">
                            <span class="result-icon">✗</span>
                            <div>
                                <strong id="resultError">0</strong>
                                <p>Errores</p>
                            </div>
                        </div>
                    </div>

                    <a id="downloadReportBtn" href="#" class="btn btn-secondary" target="_blank">
                        📥 Descargar Reporte (CSV)
                    </a>
                    <button onclick="resetImport()" class="btn btn-outline">Importar otro archivo</button>
                </div>

                {{-- Botones de acción --}}
                <div id="actionButtons" class="form-actions">
                    <button onclick="iniciarImportacion()" class="btn btn-primary" id="importButton">
                        Iniciar Importación
                    </button>
                    <button onclick="cancelarImport()" class="btn btn-outline" id="cancelButton" style="display: none;">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 2: IMPORTAR PDFs --}}
        <div id="tab-pdf" class="tab-content">
            <div class="card">
                <h3>Importar PDFs de Facturas</h3>
                <p style="color: #6B7280; margin: 10px 0 20px 0;">
                    Carga PDFs de facturas. El sistema detectará CUFEs, separará por factura y asociará automáticamente.
                </p>

                <div class="form-group">
                    <label>Archivos PDF (uno o múltiples)</label>
                    <div class="file-input-wrapper" id="pdfInputWrapper">
                        <input
                            type="file"
                            id="pdfFiles"
                            accept=".pdf"
                            multiple
                            class="file-input"
                            onchange="validarPDFs()"
                        >
                        <label for="pdfFiles" class="file-label">
                            📄 Seleccionar PDFs o arrastrar aquí
                        </label>
                    </div>
                    <small>Máximo 50MB por archivo, sin límite de cantidad</small>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="extractCUFE" checked>
                        Extraer CUFEs desde QR del PDF
                    </label>
                    <small>Si está desmarcado, usa el nombre del archivo</small>
                </div>

                {{-- Preview PDFs --}}
                <div id="pdfPreviewContainer" style="display: none;">
                    <h4>Vista Previa de PDFs (máximo 10)</h4>
                    <div class="table-preview">
                        <table id="pdfPreviewTable">
                            <thead>
                                <tr>
                                    <th>Archivo</th>
                                    <th>Tamaño</th>
                                    <th>CUFEs Detectados</th>
                                    <th>Páginas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="pdfPreviewBody"></tbody>
                        </table>
                    </div>
                    <p id="pdfPreviewInfo"></p>
                </div>

                {{-- Progreso PDFs --}}
                <div id="pdfProgressContainer" style="display: none;">
                    <h4>Procesando PDFs...</h4>
                    <div class="progress-bar">
                        <div class="progress-fill" id="pdfProgressFill"></div>
                    </div>
                    <p id="pdfProgressText">0%</p>

                    <div class="import-log">
                        <h5>Log de Importación</h5>
                        <div id="pdfLogContent" class="log-content"></div>
                    </div>

                    <div class="stats">
                        <div class="stat">
                            <span class="stat-label">Asociados</span>
                            <span class="stat-value" id="pdfStatAssociated">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">En Staging</span>
                            <span class="stat-value" id="pdfStatStaging">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Errores</span>
                            <span class="stat-value" id="pdfStatFailed">0</span>
                        </div>
                    </div>
                </div>

                {{-- Resultado PDFs --}}
                <div id="pdfResultContainer" style="display: none;">
                    <h4>Importación de PDFs Completada</h4>
                    <div class="result-summary">
                        <div class="result-item success">
                            <span class="result-icon">✓</span>
                            <div>
                                <strong id="pdfResultAssociated">0</strong>
                                <p>PDFs asociados</p>
                            </div>
                        </div>
                        <div class="result-item duplicate">
                            <span class="result-icon">⏳</span>
                            <div>
                                <strong id="pdfResultStaging">0</strong>
                                <p>En staging (sin factura)</p>
                            </div>
                        </div>
                        <div class="result-item error">
                            <span class="result-icon">✗</span>
                            <div>
                                <strong id="pdfResultFailed">0</strong>
                                <p>Errores</p>
                            </div>
                        </div>
                    </div>

                    <a id="pdfDownloadReportBtn" href="#" class="btn btn-secondary" target="_blank">
                        📥 Descargar Reporte (CSV)
                    </a>
                    <button onclick="resetPDFImport()" class="btn btn-outline">Importar más PDFs</button>
                </div>

                {{-- Botones de acción --}}
                <div id="pdfActionButtons" class="form-actions">
                    <button onclick="iniciarImportacionPDF()" class="btn btn-primary" id="pdfImportButton">
                        Iniciar Importación PDF
                    </button>
                    <button onclick="cancelarImportPDF()" class="btn btn-outline" id="pdfCancelButton" style="display: none;">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 3: HISTORIAL --}}
        <div id="tab-historial" class="tab-content">
            <div class="card">
                <h3>Historial de Importaciones</h3>

                @if($importLogs->count() > 0)
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Archivo</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Exitosas</th>
                                <th>Errores</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importLogs as $log)
                                <tr>
                                    <td>{{ ucfirst($log->import_type) }}</td>
                                    <td>{{ $log->file_name }}</td>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->total_records }}</td>
                                    <td><span class="badge badge-success">{{ $log->successful }}</span></td>
                                    <td><span class="badge badge-danger">{{ $log->failed }}</span></td>
                                    <td>
                                        @if($log->status === 'completed')
                                            <span class="badge badge-success">Completado</span>
                                        @elseif($log->status === 'processing')
                                            <span class="badge badge-warning">Procesando</span>
                                        @else
                                            <span class="badge badge-danger">Error</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->status === 'completed')
                                            <a href="{{ route('import.report', $log) }}" class="btn btn-sm btn-info">📥</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $importLogs->links() }}
                @else
                    <p style="text-align: center; color: #6B7280; padding: 40px;">
                        No hay importaciones registradas
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.importaciones-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.importaciones-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #D4D9E2;
    padding-bottom: 20px;
}

.importaciones-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1F2933;
    margin: 0;
}

.importaciones-header p {
    color: #6B7280;
    margin: 5px 0 0 0;
}

.tabs-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tabs {
    display: flex;
    border-bottom: 2px solid #D4D9E2;
    background: #F5F7FA;
}

.tab-button {
    flex: 1;
    padding: 15px;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 600;
    color: #6B7280;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    font-size: 14px;
}

.tab-button.active {
    color: #2767C6;
    border-bottom-color: #2767C6;
    background: white;
}

.tab-button:hover {
    background: white;
}

.tab-content {
    display: none;
    padding: 30px;
}

.tab-content.active {
    display: block;
}

.card {
    background: white;
}

.card h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1F2933;
    margin: 0 0 20px 0;
}

.card h4 {
    font-size: 14px;
    font-weight: 700;
    color: #1F2933;
    margin: 20px 0 15px 0;
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

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
}

.checkbox-label input {
    margin-right: 8px;
    cursor: pointer;
}

small {
    display: block;
    color: #6B7280;
    font-size: 12px;
    margin-top: 4px;
}

.file-input-wrapper {
    position: relative;
    border: 2px dashed #2767C6;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-input-wrapper:hover {
    background: #F0F4FF;
}

.file-input {
    display: none;
}

.file-label {
    display: block;
    cursor: pointer;
    color: #2767C6;
    font-weight: 600;
    margin: 0;
}

.table-preview {
    overflow-x: auto;
    margin: 15px 0;
    border: 1px solid #D4D9E2;
    border-radius: 6px;
}

.table-preview table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.table-preview th,
.table-preview td {
    padding: 10px;
    border-bottom: 1px solid #D4D9E2;
    text-align: left;
}

.table-preview th {
    background: #F5F7FA;
    font-weight: 600;
    color: #1F2933;
}

/* Preview Section mejorado */
.preview-section {
    background: #f0f9ff;
    border: 2px solid #2767C6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.preview-header h4 {
    margin: 0;
    color: #2767C6;
    font-size: 16px;
}

.preview-badge {
    background: #2767C6;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.preview-info {
    background: #d1fae5;
    color: #065f46;
    padding: 12px 15px;
    border-radius: 6px;
    margin-top: 15px;
    font-size: 14px;
}

/* Registro actual siendo procesado */
.current-record {
    background: linear-gradient(135deg, #2767C6 0%, #102544 100%);
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 15px;
    color: white;
}

.current-record-label {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.8;
    margin-bottom: 5px;
}

.current-record-data {
    display: flex;
    align-items: center;
    gap: 15px;
}

.record-number {
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 14px;
}

.record-detail {
    font-size: 14px;
    font-weight: 500;
}

/* Colores de estadísticas */
.success-color { color: #10B981 !important; }
.warning-color { color: #F59E0B !important; }
.error-color { color: #EF4444 !important; }

.progress-bar {
    width: 100%;
    height: 12px;
    background: #E4E7EB;
    border-radius: 6px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #2767C6, #10B981);
    transition: width 0.3s ease;
    width: 0%;
}

.import-log {
    background: #F5F7FA;
    padding: 15px;
    border-radius: 6px;
    margin: 15px 0;
}

.import-log h5 {
    font-size: 12px;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: #1F2933;
}

.log-content {
    background: white;
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 11px;
    max-height: 200px;
    overflow-y: auto;
    color: #6B7280;
}

.log-entry {
    padding: 4px 0;
    border-bottom: 1px solid #E4E7EB;
}

.log-entry.success {
    color: #009F6B;
}

.log-entry.warning {
    color: #F59E0B;
}

.log-entry.error {
    color: #DC3545;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin: 20px 0;
}

.stat {
    background: #F5F7FA;
    padding: 15px;
    border-radius: 6px;
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #6B7280;
    font-weight: 600;
    margin-bottom: 5px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: #2767C6;
}

.result-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin: 20px 0;
}

.result-item {
    padding: 20px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.result-item.success {
    background: #E6F5EC;
    color: #009F6B;
}

.result-item.duplicate {
    background: #FFF3CD;
    color: #B59C00;
}

.result-item.error {
    background: #F8D7DA;
    color: #DC3545;
}

.result-icon {
    font-size: 28px;
}

.result-item strong {
    display: block;
    font-size: 20px;
}

.result-item p {
    font-size: 12px;
    margin: 0;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
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

.btn-secondary {
    background: #6B7280;
    color: white;
}

.btn-outline {
    background: white;
    color: #6B7280;
    border: 2px solid #D4D9E2;
}

.btn-outline:hover {
    background: #F5F7FA;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-info {
    background: #D1ECF1;
    color: #0C5460;
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

.badge-warning {
    background: #FFF3CD;
    color: #B59C00;
}

.badge-info {
    background: #D1ECF1;
    color: #0C5460;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    margin-top: 15px;
}

.history-table thead {
    background: #F5F7FA;
}

.history-table th,
.history-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #D4D9E2;
}

.history-table th {
    font-weight: 600;
    color: #1F2933;
}

.history-table tbody tr:hover {
    background: #FAFBFC;
}
</style>

<script>
// Rutas de Laravel (se definen aquí para evitar conflictos con template strings)
const ROUTES = {
    validateExcel: '{{ route('import.validate-excel') }}',
    processExcel: '{{ route('import.process-excel') }}',
    validatePdf: '{{ route('import.validate-pdf') }}',
    processPdf: '{{ route('import.process-pdf') }}',
    progress: '/api/import-progress/',
    report: '/importaciones/'
};
const CSRF_TOKEN = '{{ csrf_token() }}';

let currentImportLogId = null;
let pollingInterval = null;

function cambiarTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('active');
    });

    // Mostrar tab seleccionado
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}

async function validarExcel() {
    const file = document.getElementById('excelFile').files[0];
    if (!file) return;

    // Mostrar que está cargando
    const fileLabel = document.querySelector('.file-label');
    fileLabel.innerHTML = '⏳ Validando archivo...';

    // Limpiar previamente
    document.getElementById('previewContainer').style.display = 'none';
    document.getElementById('progressContainer').style.display = 'none';
    document.getElementById('resultContainer').style.display = 'none';
    document.getElementById('importButton').style.display = 'none';

    const formData = new FormData();
    formData.append('excel_file', file);

    try {
        const response = await fetch(ROUTES.validateExcel, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        });

        const data = await response.json();

        // Restaurar label
        fileLabel.innerHTML = '📁 Archivo: <strong>' + file.name + '</strong>';

        if (data.success) {
            mostrarPreview(data);
        } else {
            fileLabel.innerHTML = '📁 Seleccionar archivo o arrastrar aquí';
            const errorMsg = data.error || 'Error desconocido';
            alert('❌ Error al validar archivo:\n\n' + errorMsg);
            console.error('Error validando Excel:', data);
        }
    } catch (error) {
        document.querySelector('.file-label').innerHTML = '📁 Seleccionar archivo o arrastrar aquí';
        alert('❌ Error al validar archivo:\n\n' + error.message);
        console.error('Error:', error);
    }
}

function mostrarPreview(data) {
    // Mostrar container de preview
    document.getElementById('previewContainer').style.display = 'block';
    document.getElementById('importButton').style.display = 'inline-block';
    document.getElementById('importButton').disabled = false;
    document.getElementById('importButton').textContent = '🚀 Iniciar Importación (' + data.total_rows + ' registros)';

    // Headers de la tabla
    const headerRow = document.getElementById('previewHeader');
    headerRow.innerHTML = '';
    if (data.preview.length > 0) {
        Object.keys(data.preview[0]).forEach(key => {
            const th = document.createElement('th');
            th.textContent = key;
            headerRow.appendChild(th);
        });
    }

    // Filas de datos
    const bodyTable = document.getElementById('previewBody');
    bodyTable.innerHTML = '';
    data.preview.forEach((row, index) => {
        const tr = document.createElement('tr');
        Object.values(row).forEach(value => {
            const td = document.createElement('td');
            td.textContent = value || '-';
            tr.appendChild(td);
        });
        bodyTable.appendChild(tr);
    });

    // Info del archivo
    document.getElementById('previewInfo').innerHTML =
        '<strong>✅ Archivo válido</strong> - Total de registros a importar: <strong>' + data.total_rows + '</strong>';

    // Guardar ruta del archivo para usarla al importar
    document.getElementById('excelFile').dataset.filePath = data.file_path;

    // Scroll hacia el preview
    document.getElementById('previewContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function iniciarImportacion() {
    const filePath = document.getElementById('excelFile').dataset.filePath;
    const validateDuplicates = document.getElementById('validateDuplicates').checked;
    const createTerceros = document.getElementById('createTerceros').checked;

    if (!filePath) {
        alert('❌ Por favor selecciona un archivo válido');
        return;
    }

    // Mostrar progreso
    document.getElementById('previewContainer').style.display = 'none';
    document.getElementById('progressContainer').style.display = 'block';
    document.getElementById('importButton').style.display = 'none';
    document.getElementById('cancelButton').style.display = 'inline-block';

    try {
        const response = await fetch(ROUTES.processExcel, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                file_path: filePath,
                validate_duplicates: validateDuplicates,
                create_terceros: createTerceros
            })
        });

        const data = await response.json();

        if (data.success) {
            currentImportLogId = data.import_log_id;
            // Iniciar polling de progreso
            pollingInterval = setInterval(() => obtenerProgreso(data.import_log_id), 2000);
        } else {
            document.getElementById('previewContainer').style.display = 'block';
            document.getElementById('progressContainer').style.display = 'none';
            document.getElementById('importButton').style.display = 'inline-block';
            document.getElementById('cancelButton').style.display = 'none';

            const errorMsg = data.error || 'Error desconocido';
            alert('❌ Error al iniciar importación:\n\n' + errorMsg);
            console.error('Error iniciando importación:', data);
        }
    } catch (error) {
        document.getElementById('previewContainer').style.display = 'block';
        document.getElementById('progressContainer').style.display = 'none';
        document.getElementById('importButton').style.display = 'inline-block';
        document.getElementById('cancelButton').style.display = 'none';

        alert('❌ Error al iniciar importación:\n\n' + error.message);
        console.error('Error:', error);
    }
}

async function obtenerProgreso(importLogId) {
    try {
        const response = await fetch(ROUTES.progress + importLogId);

        if (!response.ok) {
            console.error('Error en respuesta:', response.status);
            return;
        }

        const data = await response.json();

        // Actualizar progreso
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        progressFill.style.width = data.percentage + '%';

        // Actualizar texto de progreso con fila actual
        var processed = data.processed || 0;
        var total = data.total || 0;
        progressText.innerHTML = data.percentage + '% - Procesando fila <span id="currentRow">' + processed + '</span> de <span id="totalRows">' + total + '</span>';

        document.getElementById('statSuccessful').textContent = data.successful || 0;
        document.getElementById('statDuplicates').textContent = data.duplicates || 0;
        document.getElementById('statFailed').textContent = data.failed || 0;
        document.getElementById('statTotal').textContent = data.total || 0;

        // Actualizar registro actual siendo procesado
        if (data.current_record) {
            const recordNumber = document.querySelector('.record-number');
            const recordDetail = document.querySelector('.record-detail');
            recordNumber.textContent = '#' + (data.processed || 0);
            recordDetail.textContent = (data.current_record.numero_factura || 'Procesando...') + ' - ' + (data.current_record.cliente || '');
        }

        // Actualizar log (máximo últimos 15 registros)
        const logContent = document.getElementById('importLogContent');

        if (data.records && data.records.length > 0) {
            logContent.innerHTML = '';

            // Mostrar registros del más reciente al más antiguo
            data.records.slice(0, 15).forEach(record => {
                const div = document.createElement('div');
                let statusClass = 'success';
                let statusIcon = '✓';

                if (record.status === 'error') {
                    statusClass = 'error';
                    statusIcon = '✗';
                } else if (record.status === 'duplicate') {
                    statusClass = 'warning';
                    statusIcon = '⊕';
                }

                div.className = 'log-entry ' + statusClass;

                var logText = statusIcon + ' ' + (record.numero_factura || 'N/A');
                if (record.cliente) {
                    logText += ' | ' + record.cliente;
                }
                logText += ' → ' + record.status;
                if (record.error_message) {
                    logText += ': ' + record.error_message;
                }

                div.textContent = logText;
                logContent.appendChild(div);
            });
        }

        // Si completó
        if (data.status === 'completed') {
            clearInterval(pollingInterval);
            mostrarResultado(data, importLogId);
        } else if (data.status === 'failed') {
            clearInterval(pollingInterval);
            document.getElementById('progressContainer').style.display = 'none';
            document.getElementById('cancelButton').style.display = 'none';
            alert('❌ Importación fallida. Verifica los errores en el log anterior.');
        }
    } catch (error) {
        console.error('Error obteniendo progreso:', error);
    }
}

function mostrarResultado(data, importLogId) {
    document.getElementById('progressContainer').style.display = 'none';
    document.getElementById('resultContainer').style.display = 'block';
    document.getElementById('cancelButton').style.display = 'none';

    // Contar resultados basados en status
    const newCount = data.records ? data.records.filter(r => r.status === 'new').length : 0;
    const duplicateCount = data.records ? data.records.filter(r => r.status === 'duplicate').length : 0;
    const errorCount = data.records ? data.records.filter(r => r.status === 'error').length : 0;

    document.getElementById('resultSuccessful').textContent = newCount;
    document.getElementById('resultDuplicate').textContent = duplicateCount;
    document.getElementById('resultError').textContent = errorCount;

    // Botón de descarga
    const reportBtn = document.getElementById('downloadReportBtn');
    reportBtn.href = ROUTES.report + importLogId + '/reporte';
    reportBtn.style.display = 'inline-block';
}

function resetImport() {
    document.getElementById('excelFile').value = '';
    document.getElementById('previewContainer').style.display = 'none';
    document.getElementById('progressContainer').style.display = 'none';
    document.getElementById('resultContainer').style.display = 'none';
    document.getElementById('importButton').style.display = 'inline-block';
    document.getElementById('cancelButton').style.display = 'none';
}

function cancelarImport() {
    if (pollingInterval) clearInterval(pollingInterval);
    resetImport();
}

// Drag and drop
const fileInput = document.getElementById('excelFile');
const fileInputWrapper = document.querySelector('.file-input-wrapper');

fileInputWrapper.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileInputWrapper.style.background = '#F0F4FF';
});

fileInputWrapper.addEventListener('dragleave', () => {
    fileInputWrapper.style.background = '';
});

fileInputWrapper.addEventListener('drop', (e) => {
    e.preventDefault();
    fileInputWrapper.style.background = '';
    fileInput.files = e.dataTransfer.files;
    validarExcel();
});

// ========== PDF FUNCTIONS ==========

let currentPDFImportLogId = null;
let pdfPollingInterval = null;

async function validarPDFs() {
    const files = document.getElementById('pdfFiles').files;
    if (files.length === 0) return;

    // Limpiar previamente
    document.getElementById('pdfPreviewContainer').style.display = 'none';
    document.getElementById('pdfImportButton').style.display = 'none';

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('pdf_files[]', files[i]);
    }

    try {
        const response = await fetch(ROUTES.validatePdf, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            mostrarPreviewPDF(data);
        } else {
            const errorMsg = data.error || 'Error desconocido';
            alert('❌ Error al validar PDFs:\n\n' + errorMsg);
            console.error('Error validando PDFs:', data);
        }
    } catch (error) {
        alert('❌ Error al validar PDFs:\n\n' + error.message);
        console.error('Error:', error);
    }
}

function mostrarPreviewPDF(data) {
    // Mostrar container
    document.getElementById('pdfPreviewContainer').style.display = 'block';
    document.getElementById('pdfImportButton').style.display = 'inline-block';

    // Tabla preview
    const bodyTable = document.getElementById('pdfPreviewBody');
    bodyTable.innerHTML = '';

    if (data.files && data.files.length > 0) {
        data.files.forEach(file => {
            const tr = document.createElement('tr');
            var cufeCount = file.cufes ? file.cufes.length : 0;
            tr.innerHTML =
                '<td>' + file.name + '</td>' +
                '<td>' + file.size + '</td>' +
                '<td><span class="badge badge-info">' + cufeCount + '</span></td>' +
                '<td>' + (file.pages || 0) + '</td>' +
                '<td><span class="badge badge-warning">Listo</span></td>';
            bodyTable.appendChild(tr);
        });
    }

    // Info
    document.getElementById('pdfPreviewInfo').textContent = 'Total de archivos: ' + data.files.length;

    // Guardar paths
    document.getElementById('pdfFiles').dataset.filePaths = JSON.stringify(data.file_paths);
}

async function iniciarImportacionPDF() {
    const filePathsStr = document.getElementById('pdfFiles').dataset.filePaths;
    const extractCUFE = document.getElementById('extractCUFE').checked;

    if (!filePathsStr) {
        alert('❌ Por favor selecciona archivos PDF válidos');
        return;
    }

    const filePaths = JSON.parse(filePathsStr);

    // Mostrar progreso
    document.getElementById('pdfPreviewContainer').style.display = 'none';
    document.getElementById('pdfProgressContainer').style.display = 'block';
    document.getElementById('pdfImportButton').style.display = 'none';
    document.getElementById('pdfCancelButton').style.display = 'inline-block';

    try {
        const response = await fetch(ROUTES.processPdf, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                file_paths: filePaths,
                extract_cufe: extractCUFE
            })
        });

        const data = await response.json();

        if (data.success) {
            currentPDFImportLogId = data.import_log_id;
            // Iniciar polling de progreso
            pdfPollingInterval = setInterval(() => obtenerProgresoPDF(data.import_log_id), 2000);
        } else {
            document.getElementById('pdfPreviewContainer').style.display = 'block';
            document.getElementById('pdfProgressContainer').style.display = 'none';
            document.getElementById('pdfImportButton').style.display = 'inline-block';
            document.getElementById('pdfCancelButton').style.display = 'none';

            const errorMsg = data.error || 'Error desconocido';
            alert('❌ Error al iniciar importación:\n\n' + errorMsg);
            console.error('Error:', data);
        }
    } catch (error) {
        document.getElementById('pdfPreviewContainer').style.display = 'block';
        document.getElementById('pdfProgressContainer').style.display = 'none';
        document.getElementById('pdfImportButton').style.display = 'inline-block';
        document.getElementById('pdfCancelButton').style.display = 'none';

        alert('❌ Error al iniciar importación:\n\n' + error.message);
        console.error('Error:', error);
    }
}

async function obtenerProgresoPDF(importLogId) {
    try {
        const response = await fetch(ROUTES.progress + importLogId);

        if (!response.ok) {
            console.error('Error en respuesta:', response.status);
            return;
        }

        const data = await response.json();

        // Actualizar progreso
        const progressFill = document.getElementById('pdfProgressFill');
        const progressText = document.getElementById('pdfProgressText');

        progressFill.style.width = data.percentage + '%';
        progressText.textContent = data.percentage + '%';

        document.getElementById('pdfStatAssociated').textContent = data.associated || 0;
        document.getElementById('pdfStatStaging').textContent = data.staging || 0;
        document.getElementById('pdfStatFailed').textContent = data.failed;

        // Actualizar log
        const logContent = document.getElementById('pdfLogContent');

        if (data.records && data.records.length > 0) {
            logContent.innerHTML = '';
            data.records.slice(0, 10).forEach(record => {
                const div = document.createElement('div');
                div.className = 'log-entry ' + (record.status === 'error' ? 'error' : 'success');

                var logText = (record.numero_factura || record.cufe || 'PDF') + ' - ' + record.status;
                if (record.error_message) {
                    logText += ': ' + record.error_message;
                }

                div.textContent = logText;
                logContent.insertBefore(div, logContent.firstChild);
            });
        }

        // Si completó
        if (data.status === 'completed') {
            clearInterval(pdfPollingInterval);
            mostrarResultadoPDF(data, importLogId);
        } else if (data.status === 'failed') {
            clearInterval(pdfPollingInterval);
            document.getElementById('pdfProgressContainer').style.display = 'none';
            document.getElementById('pdfCancelButton').style.display = 'none';
            alert('❌ Importación fallida. Verifica los errores en el log anterior.');
        }
    } catch (error) {
        console.error('Error obteniendo progreso:', error);
    }
}

function mostrarResultadoPDF(data, importLogId) {
    document.getElementById('pdfProgressContainer').style.display = 'none';
    document.getElementById('pdfResultContainer').style.display = 'block';
    document.getElementById('pdfCancelButton').style.display = 'none';

    // Contar resultados
    const associatedCount = data.records ? data.records.filter(r => r.status === 'associated').length : 0;
    const stagingCount = data.records ? data.records.filter(r => r.status === 'staging').length : 0;
    const errorCount = data.records ? data.records.filter(r => r.status === 'error').length : 0;

    document.getElementById('pdfResultAssociated').textContent = associatedCount;
    document.getElementById('pdfResultStaging').textContent = stagingCount;
    document.getElementById('pdfResultFailed').textContent = errorCount;

    // Botón de descarga
    const reportBtn = document.getElementById('pdfDownloadReportBtn');
    reportBtn.href = ROUTES.report + importLogId + '/reporte';
    reportBtn.style.display = 'inline-block';
}

function resetPDFImport() {
    document.getElementById('pdfFiles').value = '';
    document.getElementById('pdfPreviewContainer').style.display = 'none';
    document.getElementById('pdfProgressContainer').style.display = 'none';
    document.getElementById('pdfResultContainer').style.display = 'none';
    document.getElementById('pdfImportButton').style.display = 'inline-block';
    document.getElementById('pdfCancelButton').style.display = 'none';
}

function cancelarImportPDF() {
    if (pdfPollingInterval) clearInterval(pdfPollingInterval);
    resetPDFImport();
}

// Drag and drop para PDFs
const pdfInputWrapper = document.getElementById('pdfInputWrapper');
const pdfFileInput = document.getElementById('pdfFiles');

if (pdfInputWrapper) {
    pdfInputWrapper.addEventListener('dragover', (e) => {
        e.preventDefault();
        pdfInputWrapper.style.background = '#F0F4FF';
    });

    pdfInputWrapper.addEventListener('dragleave', () => {
        pdfInputWrapper.style.background = '';
    });

    pdfInputWrapper.addEventListener('drop', (e) => {
        e.preventDefault();
        pdfInputWrapper.style.background = '';
        pdfFileInput.files = e.dataTransfer.files;
        validarPDFs();
    });
}
</script>
@endsection

