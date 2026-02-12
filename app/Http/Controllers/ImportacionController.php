<?php

namespace App\Http\Controllers;

use App\Models\ImportConfiguration;
use App\Models\ImportLog;
use App\Models\ImportRecord;
use App\Models\Factura;
use App\Jobs\ProcessExcelImportJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportacionController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Mostrar vista de importaciones (legacy)
     */
    public function index()
    {
        $tenantId = session('tenant_id');
        $configuration = ImportConfiguration::where('tenant_id', $tenantId)->first();
        $importLogs = ImportLog::byTenant($tenantId)->orderBy('created_at', 'desc')->paginate(10);

        return view('importaciones.index', [
            'configuration' => $configuration,
            'importLogs' => $importLogs,
        ]);
    }

    /**
     * Mostrar wizard de importación (nueva UI con Livewire)
     */
    public function wizard()
    {
        return view('importaciones.wizard', [
            'usesLivewire' => true,
        ]);
    }

    /**
     * Cargar y validar Excel
     */
    public function validateExcel(Request $request)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'Debe seleccionar un archivo Excel',
            'excel_file.mimes' => 'El archivo debe ser .xlsx, .xls o .csv',
            'excel_file.max' => 'El archivo no debe exceder 10MB',
        ]);

        try {
            $file = $request->file('excel_file');

            // Validar que el archivo fue subido correctamente
            if (!$file->isValid()) {
                return response()->json(['success' => false, 'error' => 'El archivo no se subió correctamente'], 400);
            }

            // Validar tamaño mínimo (no vacío)
            if ($file->getSize() === 0) {
                return response()->json(['success' => false, 'error' => 'El archivo está vacío'], 400);
            }

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || (count($rows) === 1)) {
                return response()->json(['success' => false, 'error' => 'El archivo está vacío o solo contiene encabezados'], 400);
            }

            // Validar que hay columnas
            $headerRow = $rows[0];
            if (empty(array_filter($headerRow))) {
                return response()->json(['success' => false, 'error' => 'El archivo no tiene encabezados válidos'], 400);
            }

            // Obtener configuración o crear una por defecto
            $config = ImportConfiguration::where('tenant_id', $tenantId)->first();
            if (!$config) {
                $columnMapping = $this->detectarColumnas($headerRow);

                // Validar que se detectaron las columnas críticas
                if (empty($columnMapping['numero_factura']) || empty($columnMapping['nit']) || empty($columnMapping['nombre_cliente'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'No se detectaron columnas críticas. Se esperan: Número Factura, NIT, Nombre Cliente',
                        'detected_mapping' => $columnMapping,
                        'file' => $file->getClientOriginalName(),
                    ], 400);
                }

                $config = ImportConfiguration::create([
                    'tenant_id' => $tenantId,
                    'excel_column_mapping' => $columnMapping,
                ]);
            }

            // Validar que hay datos (mínimo 1 fila)
            if (count($rows) < 2) {
                return response()->json(['success' => false, 'error' => 'El Excel debe contener al menos 1 fila de datos'], 400);
            }

            // Mostrar primeras 5 filas como preview
            $preview = array_slice($rows, 1, 5);

            // Limpiar preview: remover columnas vacías
            $previewLimpio = [];
            foreach ($preview as $rowData) {
                $rowFiltrada = [];
                foreach ($config->excel_column_mapping as $fieldName => $colIndex) {
                    if (is_numeric($colIndex) && isset($rowData[$colIndex])) {
                        $rowFiltrada[$fieldName] = $rowData[$colIndex];
                    }
                }
                if (!empty($rowFiltrada)) {
                    $previewLimpio[] = $rowFiltrada;
                }
            }

            $filePath = $file->store('imports/temp', 'local');

            return response()->json([
                'success' => true,
                'total_rows' => count($rows) - 1,
                'column_mapping' => $config->excel_column_mapping,
                'preview' => !empty($previewLimpio) ? $previewLimpio : $preview,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
            ]);

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error leyendo archivo Excel: ' . $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error procesando archivo: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Procesar importación (inicia job async)
     */
    public function processExcel(Request $request)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'file_path' => 'required|string',
            'validate_duplicates' => 'boolean',
            'create_terceros' => 'boolean',
        ]);

        try {
            // Validar que el archivo existe
            $storagePath = storage_path('app/' . $request->file_path);
            if (!file_exists($storagePath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Archivo no encontrado. Por favor intenta de nuevo.'
                ], 404);
            }

            // Crear log de importación
            $importLog = ImportLog::create([
                'tenant_id' => $tenantId,
                'import_type' => 'excel',
                'file_name' => basename($request->file_path),
                'status' => 'processing',
                'total_records' => 0,
                'successful' => 0,
                'failed' => 0,
            ]);

            // Obtener configuración
            $config = ImportConfiguration::where('tenant_id', $tenantId)->first();
            $columnMapping = $config ? $config->excel_column_mapping : [];

            // Validar que tenemos mapeo de columnas
            if (empty($columnMapping)) {
                $importLog->update([
                    'status' => 'failed',
                    'error_details' => json_encode(['error' => 'No hay configuración de columnas']),
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'No hay configuración de columnas. Por favor intenta de nuevo.'
                ], 400);
            }

            // Dispatch job asíncrono
            ProcessExcelImportJob::dispatch(
                $importLog->id,
                $tenantId,
                $storagePath,
                $columnMapping
            );

            return response()->json([
                'success' => true,
                'import_log_id' => $importLog->id,
                'message' => 'Importación iniciada. Por favor espera a que se complete.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error iniciando importación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener progreso de importación (polling)
     */
    public function getProgress($importLogId)
    {
        $tenantId = session('tenant_id');

        $importLog = ImportLog::where('id', $importLogId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Calcular porcentaje
        $totalProcessed = $importLog->successful + $importLog->failed;
        $percentage = $importLog->total_records > 0
            ? min(100, round(($totalProcessed / $importLog->total_records) * 100))
            : 0;

        // Obtener últimos registros para log en vivo (ordenados de más recientes primero)
        $records = $importLog->records()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($record) {
                return [
                    'numero_factura' => $record->numero_factura,
                    'nit' => $record->nit,
                    'cliente' => $record->nit, // Alias para mostrar en frontend
                    'status' => $record->status,
                    'error_message' => $record->error_message,
                    'factura_id' => $record->factura_id,
                    'tercero_id' => $record->tercero_id,
                ];
            });

        // Contar duplicados
        $duplicates = $importLog->records()->where('status', 'duplicate')->count();

        // Obtener registro actual (el último procesado)
        $currentRecord = $importLog->records()
            ->orderBy('created_at', 'desc')
            ->first();

        $currentRecordData = null;
        if ($currentRecord) {
            $currentRecordData = [
                'numero_factura' => $currentRecord->numero_factura,
                'cliente' => $currentRecord->nit,
            ];
        }

        return response()->json([
            'status' => $importLog->status,
            'percentage' => $percentage,
            'total' => $importLog->total_records,
            'successful' => $importLog->successful,
            'duplicates' => $duplicates,
            'failed' => $importLog->failed,
            'processed' => $totalProcessed,
            'records' => $records,
            'current_record' => $currentRecordData,
            'completed_at' => $importLog->status === 'completed' ? $importLog->updated_at : null,
        ]);
    }

    /**
     * Obtener reporte de importación (CSV)
     */
    /**
     * Descargar reporte detallado de importación
     * Incluye: PDF, CUFE, estado, factura_id, errores específicos
     */
    public function getReport($importLogId)
    {
        $tenantId = session('tenant_id');

        $importLog = ImportLog::where('id', $importLogId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$importLog) {
            return response()->json(['error' => 'Importación no encontrada'], 404);
        }

        $records = $importLog->records()->orderBy('created_at', 'asc')->get();

        // Generar CSV mejorado
        $csv = "ARCHIVO/PDF,CUFE,NÚMERO FACTURA,NIT,ESTADO,FACTURA_ID,ERROR DETALLADO,FECHA PROCESAMIENTO\n";

        $estadosConteo = [
            'new' => 0,
            'duplicate' => 0,
            'associated' => 0,
            'staging' => 0,
            'error' => 0,
        ];

        foreach ($records as $record) {
            // Contar estados
            $estadosConteo[$record->status] = ($estadosConteo[$record->status] ?? 0) + 1;

            // Determinar el tipo de importación y formato de salida
            $tipoImportacion = $importLog->import_type === 'pdf' ? 'PDF' : 'Excel';

            // Para PDFs, mostrar CUFE en lugar de solo número factura
            $nombreArchivo = $tipoImportacion;
            if ($importLog->import_type === 'pdf') {
                $nombreArchivo = 'PDF: ' . ($record->cufe ?? $record->numero_factura);
            }

            // Estado legible
            $estadoLegible = $this->obtenerEstadoLegible($record->status);

            // Mensaje de error o estado
            $mensajeError = '';
            if ($record->status === 'error') {
                $mensajeError = $record->error_message ?? 'Error desconocido';
            } elseif ($record->status === 'associated') {
                $mensajeError = 'Asociada exitosamente a factura #' . $record->factura_id;
            } elseif ($record->status === 'staging') {
                $mensajeError = 'En staging - esperando factura en BD';
            } elseif ($record->status === 'duplicate') {
                $mensajeError = 'Duplicada - ya existe en el sistema';
            } elseif ($record->status === 'new') {
                $mensajeError = 'Creada nueva - ' . ($record->factura_id ? 'Factura #' . $record->factura_id : '');
            }

            $csv .= "\"" . str_replace('"', '""', $nombreArchivo) . "\"," .
                    "\"" . ($record->cufe ?? '') . "\"," .
                    "{$record->numero_factura}," .
                    "{$record->nit}," .
                    "{$estadoLegible}," .
                    ($record->factura_id ?? '-') . "," .
                    "\"" . str_replace('"', '""', $mensajeError) . "\"," .
                    ($record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '') . "\n";
        }

        // Agregar resumen al final del CSV
        $csv .= "\n\n=== RESUMEN DE IMPORTACIÓN ===\n";
        $csv .= "Tipo de importación," . ($importLog->import_type === 'pdf' ? 'PDF' : 'Excel') . "\n";
        $csv .= "Archivo," . $importLog->file_name . "\n";
        $csv .= "Fecha inicio," . ($importLog->created_at ? $importLog->created_at->format('Y-m-d H:i:s') : '') . "\n";
        $csv .= "Fecha fin," . ($importLog->updated_at ? $importLog->updated_at->format('Y-m-d H:i:s') : '') . "\n";
        $csv .= "Estado," . ucfirst($importLog->status) . "\n";
        $csv .= "\n=== ESTADÍSTICAS ===\n";
        $csv .= "Total procesados," . $importLog->total_records . "\n";
        $csv .= "Exitosos," . ($importLog->successful ?? 0) . "\n";
        $csv .= "Fallidos," . ($importLog->failed ?? 0) . "\n";
        $csv .= "Creados/Asociados," . $estadosConteo['new'] . "\n";
        $csv .= "Duplicados," . $estadosConteo['duplicate'] . "\n";
        $csv .= "En staging," . $estadosConteo['staging'] . "\n";
        $csv .= "Errores," . $estadosConteo['error'] . "\n";
        $csv .= "Tasa de éxito," . ($importLog->total_records > 0 ? round(($importLog->successful / $importLog->total_records * 100), 2) . '%' : '0%') . "\n";

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename=reporte_importacion_' . $importLog->id . '_' . date('Y-m-d_H-i-s') . '.csv');
    }

    /**
     * Obtener descripción legible del estado
     */
    private function obtenerEstadoLegible($status)
    {
        $estados = [
            'new' => '✓ Creada/Nueva',
            'duplicate' => '⊕ Duplicada',
            'associated' => '✓ Asociada',
            'staging' => '⏳ En Staging',
            'error' => '✗ Error',
        ];

        return $estados[$status] ?? $status;
    }

    /**
     * Detectar columnas automáticamente del encabezado Excel
     */
    private function detectarColumnas($headerRow)
    {
        $columnasEsperadas = [
            'numero_factura' => ['FACTURA', 'NUMERO', 'NUM_FACTURA', 'N_FACTURA', 'NUM', '#FACTURA'],
            'nit' => ['NIT', 'RFC', 'CEDULA', 'CÉDULA', 'RUT', 'IDENTIFICACION', 'IDENTI'],
            'nombre_cliente' => ['CLIENTE', 'NOMBRE', 'RAZON_SOCIAL', 'RAZÓN_SOCIAL', 'NOMBRE_CLIENTE', 'RAZONSOCIAL', 'EMPRESA', 'PROVEEDOR'],
            'fecha_factura' => ['FECHA', 'FECHA_FACTURA', 'FECHA_EMISION', 'FECHAFACTURA', 'FECHAVENCIMIENTO', 'FECHA Y HORA', 'DATE', 'FECHACREACION'],
            'fecha_vencimiento' => ['VENCIMIENTO', 'FECHA_VENCIMIENTO', 'DUE_DATE', 'FECHA_VENCIMIENTO', 'FECHAVENCIMIENTO'],
            'subtotal' => ['SUBTOTAL', 'SUB_TOTAL', 'MONTO', 'SUBTOT', 'VALOR', 'BASEGRAV'],
            'iva' => ['IVA', 'IMPUESTO', 'TAX', 'IMPUESTO IVA', 'IMPUESTOTOTAL'],
            'descuento' => ['DESCUENTO', 'REBAJA', 'DISCOUNT', 'DESC', 'DESCTO'],
            'motonave' => ['MOTONAVE', 'NAVE', 'BARCO', 'VESSEL', 'EMBARQUE'],
            'trb' => ['TRB', 'TONELADAS', 'TONELAGE', 'TONELADAS_REGISTRO', 'TONELADAS REGISTRO'],
            'descripcion' => ['DESCRIPCION', 'DESCRIPCIÓN', 'SERVICIO', 'DESCRIPTION', 'CONCEPTO', 'OBSERVACION'],
            'direccion' => ['DIRECCION', 'DIRECCIÓN', 'ADDRESS', 'DOMICILIO'],
            'telefono' => ['TELEFONO', 'TELÉFONO', 'PHONE', 'TEL'],
            'email' => ['EMAIL', 'CORREO', 'E-MAIL', 'MAIL'],
        ];

        $mapping = [];
        $usedIndices = [];

        foreach ($columnasEsperadas as $columnKey => $variantes) {
            foreach ($headerRow as $indice => $header) {
                // Saltar índices ya asignados
                if (in_array($indice, $usedIndices)) {
                    continue;
                }

                // Normalizar header: mayúsculas, sin acentos
                $headerNorm = strtoupper(trim($header ?? ''));
                $headerNorm = $this->removerAcentos($headerNorm);

                foreach ($variantes as $variante) {
                    $varianteNorm = strtoupper(trim($variante));

                    // Búsqueda: contiene exacto o es substring
                    if ($headerNorm === $varianteNorm || stripos($headerNorm, $varianteNorm) !== false) {
                        $mapping[$columnKey] = $indice;
                        $usedIndices[] = $indice;
                        break 2; // Salir de ambos loops
                    }
                }
            }
        }

        return $mapping;
    }

    /**
     * Remover acentos de un string
     */
    private function removerAcentos($str)
    {
        $acentos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
        ];
        return str_replace(array_keys($acentos), array_values($acentos), $str);
    }

    /**
     * Validar y analizar PDFs
     */
    public function validatePDF(Request $request)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'pdf_files' => 'required|array',
            'pdf_files.*' => 'mimes:pdf|max:52428800', // 50MB por archivo
        ], [
            'pdf_files.required' => 'Debes seleccionar archivos PDF',
            'pdf_files.*.mimes' => 'Todos los archivos deben ser PDF',
            'pdf_files.*.max' => 'Cada archivo no debe exceder 50MB',
        ]);

        try {
            $filePaths = [];
            $files = [];

            foreach ($request->file('pdf_files') as $file) {
                if (!$file->isValid()) {
                    return response()->json(['success' => false, 'error' => 'Archivo inválido: ' . $file->getClientOriginalName()], 400);
                }

                // Guardar archivo temporal
                $path = $file->store('imports/temp', 'local');
                $filePaths[] = $path;

                // Obtener info del PDF
                $pdfInfo = $this->analizarPDF(storage_path('app/' . $path));

                $files[] = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $this->formatearTamaño($file->getSize()),
                    'cufes' => $pdfInfo['cufes'] ?? [],
                    'pages' => $pdfInfo['pages'] ?? 0,
                ];
            }

            return response()->json([
                'success' => true,
                'files' => $files,
                'file_paths' => $filePaths,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error procesando PDFs: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Procesar importación de PDFs (inicia job)
     */
    public function processPDF(Request $request)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'file_paths' => 'required|array',
            'extract_cufe' => 'boolean',
            'selected_cufes' => 'array', // CUFEs seleccionados por usuario
        ]);

        try {
            // Validar que los archivos existen
            foreach ($request->file_paths as $filePath) {
                $fullPath = storage_path('app/' . $filePath);
                if (!file_exists($fullPath)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Archivo no encontrado. Por favor intenta de nuevo.'
                    ], 404);
                }
            }

            // Crear log de importación
            $importLog = ImportLog::create([
                'tenant_id' => $tenantId,
                'import_type' => 'pdf',
                'file_name' => 'PDF Batch (' . count($request->file_paths) . ' archivos)',
                'status' => 'processing',
                'total_records' => count($request->file_paths),
                'successful' => 0,
                'failed' => 0,
            ]);

            // Dispatch job asíncrono con CUFEs seleccionados
            \App\Jobs\ProcessPDFImportJob::dispatch(
                $importLog->id,
                $tenantId,
                $request->file_paths,
                $request->extract_cufe ?? true,
                $request->selected_cufes ?? [] // Pasar CUFEs seleccionados
            );

            return response()->json([
                'success' => true,
                'import_log_id' => $importLog->id,
                'message' => 'Importación de PDFs iniciada',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error iniciando importación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analizar información básica del PDF usando el servicio
     */
    private function analizarPDF($filePath)
    {
        $info = [
            'cufes' => [],
            'pages' => 0,
        ];

        try {
            $pdfService = app(\App\Services\PDFCufeExtractorService::class);
            $resultado = $pdfService->extractCufes($filePath);

            if ($resultado['success']) {
                $info['pages'] = $resultado['pages'] ?? 0;

                // Extraer solo los CUFEs del resultado
                foreach ($resultado['cufes'] ?? [] as $cufeData) {
                    $info['cufes'][] = $cufeData['cufe'];
                }
            }
        } catch (\Exception $e) {
            // Continuar si hay error
        }

        return $info;
    }

    /**
     * Formatear tamaño de archivo
     */
    private function formatearTamaño($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
