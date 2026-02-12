<?php

namespace App\Livewire;

use App\Services\ImportacionService;
use App\Models\ImportLog;
use App\Models\Factura;
use App\Models\Tercero;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportWizard extends Component
{
    use WithFileUploads;

    // Paso actual del wizard
    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Archivo subido
    public $excelFile;
    public string $fileName = '';
    public int $fileSize = 0;

    // Opciones de importación
    public bool $validateDuplicates = true;
    public bool $createTerceros = true;

    // Datos del archivo cargado
    public array $headers = [];
    public array $columnMapping = [];
    public array $previewRows = [];
    public int $totalRows = 0;
    public array $camposFaltantes = [];
    public string $tempFilePath = '';

    // Estado del procesamiento
    public bool $isProcessing = false;
    public int $processedRows = 0;
    public int $successCount = 0;
    public int $duplicateCount = 0;
    public int $errorCount = 0;
    public string $currentRecord = '';
    public array $importErrors = [];
    public array $processingLog = [];

    // Resultados finales
    public ?int $importLogId = null;
    public array $summary = [];

    // Listeners para eventos
    protected $listeners = ['resetWizard'];

    /**
     * Reglas de validación para el archivo
     */
    protected function rules()
    {
        return [
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ];
    }

    protected $messages = [
        'excelFile.required' => 'Debe seleccionar un archivo Excel',
        'excelFile.mimes' => 'El archivo debe ser .xlsx, .xls o .csv',
        'excelFile.max' => 'El archivo no debe exceder 10MB',
    ];

    /**
     * Cuando se sube un archivo
     */
    public function updatedExcelFile()
    {
        $this->validateOnly('excelFile');

        if ($this->excelFile) {
            $this->fileName = $this->excelFile->getClientOriginalName();
            $this->fileSize = $this->excelFile->getSize();
        }
    }

    /**
     * Paso 1 -> 2: Validar archivo y mostrar preview
     */
    public function validateAndPreview()
    {
        $this->validate();

        try {
            // Guardar archivo temporalmente
            $this->tempFilePath = $this->excelFile->store('imports/temp', 'local');
            $fullPath = Storage::disk('local')->path($this->tempFilePath);

            // Usar el servicio de importación
            $service = app(ImportacionService::class);
            $datos = $service->cargarExcel($fullPath);

            $this->headers = $datos['headers'];
            $this->totalRows = $datos['total_filas'] - 1; // Menos el header

            // Detectar columnas
            $this->columnMapping = $service->detectarColumnas($this->headers);

            // Validar campos críticos
            $this->camposFaltantes = $service->validarCamposCriticos();

            if (count($this->camposFaltantes) > 0) {
                $this->addError('excelFile', 'Campos críticos faltantes: ' . implode(', ', $this->camposFaltantes));
                return;
            }

            // Obtener preview
            $this->previewRows = $service->getPreview($datos['rows'], 5);

            // Avanzar al paso 2
            $this->currentStep = 2;

        } catch (\Exception $e) {
            $this->addError('excelFile', 'Error procesando archivo: ' . $e->getMessage());
            Log::error('Error en ImportWizard::validateAndPreview', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Paso 2 -> 3: Confirmar e iniciar procesamiento
     */
    public function confirmAndProcess()
    {
        $this->currentStep = 3;
        $this->isProcessing = true;
        $this->processedRows = 0;
        $this->successCount = 0;
        $this->duplicateCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];
        $this->processingLog = [];

        // Iniciar procesamiento
        $this->processImport();
    }

    /**
     * Procesar la importación fila por fila
     */
    public function processImport()
    {
        $tenantId = session('tenant_id');

        try {
            $fullPath = Storage::disk('local')->path($this->tempFilePath);
            $service = app(ImportacionService::class);
            $datos = $service->cargarExcel($fullPath);
            $rows = $datos['rows'];

            // Crear log de importación
            $importLog = ImportLog::create([
                'tenant_id' => $tenantId,
                'import_type' => 'excel',
                'file_name' => $this->fileName,
                'total_records' => $this->totalRows,
                'status' => 'processing',
            ]);
            $this->importLogId = $importLog->id;

            // Re-detectar columnas
            $service->detectarColumnas($this->headers);

            // Procesar cada fila (empezando desde 1, ya que 0 es el header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $this->processedRows = $i;

                // Extraer valores para mostrar progreso
                $valores = $service->extraerValoresFila($row);
                $this->currentRecord = $valores['numero_factura'] ?? "Fila {$i}";

                // Actualizar UI
                $this->dispatch('progress-updated', [
                    'current' => $i,
                    'total' => $this->totalRows,
                    'record' => $this->currentRecord,
                ]);

                // Procesar la fila
                $resultado = $service->procesarFila($row, $tenantId, $this->createTerceros);

                switch ($resultado['status']) {
                    case 'new':
                        $this->successCount++;
                        $this->processingLog[] = [
                            'fila' => $i + 1,
                            'factura' => $this->currentRecord,
                            'status' => 'success',
                            'message' => 'Importada correctamente',
                        ];
                        break;

                    case 'duplicate':
                        $this->duplicateCount++;
                        $this->processingLog[] = [
                            'fila' => $i + 1,
                            'factura' => $this->currentRecord,
                            'status' => 'duplicate',
                            'message' => 'Factura duplicada (CUFE existente)',
                        ];
                        break;

                    case 'error':
                        $this->errorCount++;
                        $errorMsg = implode(', ', $resultado['errores'] ?? ['Error desconocido']);
                        $this->importErrors[] = "Fila " . ($i + 1) . " ({$this->currentRecord}): {$errorMsg}";
                        $this->processingLog[] = [
                            'fila' => $i + 1,
                            'factura' => $this->currentRecord,
                            'status' => 'error',
                            'message' => $errorMsg,
                        ];
                        break;
                }

                // Pequeña pausa para actualizar UI (cada 10 registros)
                if ($i % 10 === 0) {
                    usleep(50000); // 50ms
                }
            }

            // Actualizar log de importación
            $importLog->update([
                'successful' => $this->successCount,
                'failed' => $this->errorCount + $this->duplicateCount,
                'status' => 'completed',
                'error_details' => count($this->importErrors) > 0 ? ['errors' => $this->importErrors] : null,
            ]);

            // Preparar resumen
            $this->summary = [
                'total' => $this->totalRows,
                'processed' => $this->processedRows,
                'success' => $this->successCount,
                'duplicates' => $this->duplicateCount,
                'errors' => $this->errorCount,
                'import_log_id' => $this->importLogId,
            ];

            // Limpiar archivo temporal
            Storage::disk('local')->delete($this->tempFilePath);

            // Pasar al paso 4 (resultados)
            $this->isProcessing = false;
            $this->currentStep = 4;

        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->importErrors[] = 'Error fatal: ' . $e->getMessage();
            Log::error('Error en ImportWizard::processImport', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // Aún así avanzar al paso 4 para mostrar los errores
            $this->summary = [
                'total' => $this->totalRows,
                'processed' => $this->processedRows,
                'success' => $this->successCount,
                'duplicates' => $this->duplicateCount,
                'errors' => $this->errorCount + 1,
                'fatal_error' => $e->getMessage(),
            ];
            $this->currentStep = 4;
        }
    }

    /**
     * Volver al paso anterior
     */
    public function previousStep()
    {
        if ($this->currentStep > 1 && !$this->isProcessing) {
            $this->currentStep--;
        }
    }

    /**
     * Reiniciar el wizard
     */
    public function resetWizard()
    {
        $this->reset([
            'currentStep',
            'excelFile',
            'fileName',
            'fileSize',
            'headers',
            'columnMapping',
            'previewRows',
            'totalRows',
            'camposFaltantes',
            'tempFilePath',
            'isProcessing',
            'processedRows',
            'successCount',
            'duplicateCount',
            'errorCount',
            'currentRecord',
            'importErrors',
            'processingLog',
            'importLogId',
            'summary',
        ]);
        $this->validateDuplicates = true;
        $this->createTerceros = true;
    }

    /**
     * Formatear bytes
     */
    public function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Obtener el porcentaje de progreso
     */
    public function getProgressPercentage(): int
    {
        if ($this->totalRows === 0) return 0;
        return (int) round(($this->processedRows / $this->totalRows) * 100);
    }

    /**
     * Renderizar el componente
     */
    public function render()
    {
        return view('livewire.import-wizard');
    }
}

