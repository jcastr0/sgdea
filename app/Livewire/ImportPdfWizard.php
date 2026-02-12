<?php

namespace App\Livewire;

use App\Services\PDFCufeExtractorService;
use App\Models\ImportLog;
use App\Models\Factura;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportPdfWizard extends Component
{
    use WithFileUploads;

    // Paso actual del wizard
    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Archivos subidos
    public $pdfFiles = [];
    public array $uploadedFiles = [];
    public array $storedPaths = []; // Rutas de archivos ya guardados
    public int $totalFilesSize = 0;

    // Opciones de importación
    public bool $extractAdditionalData = true;
    public bool $saveToStaging = true;
    public bool $overwriteExisting = false;

    // Datos del análisis
    public array $analysisResults = [];
    public int $totalCufesFound = 0;
    public int $cufesWithFactura = 0;
    public int $cufesAlreadyAssociated = 0;
    public int $cufesAvailable = 0;
    public int $cufesNotFound = 0;

    // Estado del procesamiento
    public bool $isProcessing = false;
    public bool $isAnalyzing = false;
    public int $processedFiles = 0;
    public int $totalFiles = 0;
    public int $successCount = 0;
    public int $stagingCount = 0;
    public int $skippedCount = 0;
    public int $errorCount = 0;
    public string $currentFile = '';
    public string $currentCufe = '';
    public array $importErrors = [];
    public array $processingLog = [];

    // Resultados finales
    public ?int $importLogId = null;
    public array $summary = [];

    // Preview de PDF
    public ?string $previewPdfPath = null;
    public ?string $previewPdfName = null;

    // Listeners
    protected $listeners = ['resetWizard'];

    /**
     * Reglas de validación
     */
    protected function rules()
    {
        return [
            'pdfFiles.*' => 'required|file|mimes:pdf|max:51200', // 50MB max por archivo
        ];
    }

    protected $messages = [
        'pdfFiles.*.required' => 'Debe seleccionar al menos un archivo PDF',
        'pdfFiles.*.mimes' => 'Solo se permiten archivos PDF',
        'pdfFiles.*.max' => 'Cada archivo no debe exceder 50MB',
    ];

    /**
     * Cuando se suben archivos - guardar inmediatamente
     */
    public function updatedPdfFiles()
    {
        $this->uploadedFiles = [];
        $this->storedPaths = [];
        $this->totalFilesSize = 0;

        if (!$this->pdfFiles || !is_array($this->pdfFiles)) {
            Log::warning("pdfFiles está vacío o no es array", ['pdfFiles' => $this->pdfFiles]);
            return;
        }

        Log::info("Procesando archivos PDF", ['count' => count($this->pdfFiles)]);

        foreach ($this->pdfFiles as $index => $file) {
            try {
                // Verificar que el archivo sea válido
                if (!$file || !method_exists($file, 'store')) {
                    Log::warning("Archivo inválido en índice {$index}");
                    continue;
                }

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                // Guardar archivo inmediatamente para evitar que expire
                $storedPath = $file->store('imports/temp-pdf', 'local');
                $fullPath = Storage::disk('local')->path($storedPath);

                Log::info("PDF guardado", [
                    'index' => $index,
                    'name' => $fileName,
                    'storedPath' => $storedPath,
                    'exists' => file_exists($fullPath),
                    'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                ]);

                if (file_exists($fullPath)) {
                    $this->uploadedFiles[] = [
                        'name' => $fileName,
                        'size' => $fileSize,
                        'stored_path' => $storedPath,
                    ];
                    $this->storedPaths[] = $storedPath;
                    $this->totalFilesSize += $fileSize;
                }
            } catch (\Exception $e) {
                Log::error("Error guardando PDF", [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ]);
                // NO agregar error aquí para no interrumpir los demás archivos
            }
        }

        $this->totalFiles = count($this->uploadedFiles);

        Log::info("Archivos procesados", [
            'totalFiles' => $this->totalFiles,
            'uploadedFiles' => count($this->uploadedFiles),
            'storedPaths' => count($this->storedPaths),
        ]);

        // Si no se guardó ningún archivo, mostrar error
        if ($this->totalFiles === 0) {
            $this->addError('pdfFiles', 'No se pudo cargar ningún archivo PDF. Intente de nuevo.');
        }
    }

    /**
     * Paso 1 -> 2: Analizar PDFs y mostrar CUFEs encontrados
     */
    public function analyzeFiles()
    {
        Log::info('ImportPdfWizard::analyzeFiles iniciado', [
            'uploadedFiles_count' => count($this->uploadedFiles),
            'storedPaths_count' => count($this->storedPaths),
        ]);

        if (empty($this->uploadedFiles)) {
            $this->addError('pdfFiles', 'Debe seleccionar al menos un archivo PDF');
            return;
        }

        $this->isAnalyzing = true;
        $this->analysisResults = [];
        $this->totalCufesFound = 0;
        $this->cufesWithFactura = 0;
        $this->cufesAlreadyAssociated = 0;
        $this->cufesAvailable = 0;
        $this->cufesNotFound = 0;

        $tenantId = session('tenant_id');
        $service = app(PDFCufeExtractorService::class);

        try {
            foreach ($this->uploadedFiles as $index => $fileData) {
                $this->currentFile = $fileData['name'];
                $tempPath = $fileData['stored_path'];
                $fullPath = Storage::disk('local')->path($tempPath);

                Log::info("Analizando archivo PDF #{$index}", [
                    'name' => $fileData['name'],
                    'tempPath' => $tempPath,
                    'fullPath' => $fullPath,
                    'exists' => file_exists($fullPath),
                ]);

                if (!file_exists($fullPath)) {
                    throw new \Exception("El archivo no existe: {$fullPath}");
                }

                // Cargar y analizar PDF
                $pdfInfo = $service->cargarPDF($fullPath);
                $cufes = $service->extraerCUFEs($pdfInfo['pages'], $fullPath);

                // Comparar con BD
                $comparacion = $service->compararConBD($cufes, $tenantId);

                $fileResult = [
                    'name' => $fileData['name'],
                    'size' => $fileData['size'],
                    'pages' => $pdfInfo['total_paginas'],
                    'temp_path' => $tempPath,
                    'cufes' => $cufes,
                    'comparacion' => $comparacion,
                    'total_cufes' => count($cufes),
                    'encontradas' => count($comparacion['encontradas']),
                    'disponibles' => count($comparacion['disponibles']),
                    'ya_asociadas' => count($comparacion['ya_asociadas']),
                    'no_encontradas' => count($comparacion['no_encontradas']),
                ];

                $this->analysisResults[] = $fileResult;

                // Acumular totales
                $this->totalCufesFound += $fileResult['total_cufes'];
                $this->cufesWithFactura += $fileResult['encontradas'];
                $this->cufesAvailable += $fileResult['disponibles'];
                $this->cufesAlreadyAssociated += $fileResult['ya_asociadas'];
                $this->cufesNotFound += $fileResult['no_encontradas'];
            }

            $this->isAnalyzing = false;
            $this->currentStep = 2;

        } catch (\Exception $e) {
            $this->isAnalyzing = false;
            $this->addError('pdfFiles', 'Error analizando PDFs: ' . $e->getMessage());
            Log::error('Error en ImportPdfWizard::analyzeFiles', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Paso 2 -> 3: Confirmar e iniciar procesamiento
     */
    public function confirmAndProcess()
    {
        $this->currentStep = 3;
        $this->isProcessing = true;
        $this->processedFiles = 0;
        $this->successCount = 0;
        $this->stagingCount = 0;
        $this->skippedCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];
        $this->processingLog = [];

        $this->processImport();
    }

    /**
     * Procesar la importación de PDFs
     */
    public function processImport()
    {
        $tenantId = session('tenant_id');
        $service = app(PDFCufeExtractorService::class);

        try {
            // Crear log de importación
            $importLog = ImportLog::create([
                'tenant_id' => $tenantId,
                'import_type' => 'pdf',
                'file_name' => count($this->analysisResults) . ' archivos PDF',
                'total_records' => $this->totalCufesFound,
                'status' => 'processing',
            ]);
            $this->importLogId = $importLog->id;

            foreach ($this->analysisResults as $fileIndex => $fileData) {
                $this->processedFiles = $fileIndex + 1;
                $this->currentFile = $fileData['name'];

                $fullPath = Storage::disk('local')->path($fileData['temp_path']);

                // Procesar cada CUFE del archivo
                foreach ($fileData['cufes'] as $cufeData) {
                    $this->currentCufe = substr($cufeData['cufe'], 0, 20) . '...';

                    // Dispatch evento para actualizar UI
                    $this->dispatch('progress-updated', [
                        'file' => $this->currentFile,
                        'cufe' => $this->currentCufe,
                        'fileIndex' => $fileIndex + 1,
                        'totalFiles' => count($this->analysisResults),
                    ]);
                }

                // Procesar el archivo completo
                $resultado = $service->procesarPDF(
                    $fullPath,
                    $fileData['cufes'],
                    $tenantId,
                    false, // No es dry-run
                    $this->saveToStaging,
                    $this->extractAdditionalData
                );

                // Actualizar contadores
                $this->successCount += count($resultado['asociados']);
                $this->stagingCount += count($resultado['staging']);
                $this->skippedCount += count($resultado['omitidos']);
                $this->errorCount += count($resultado['errores']);

                // Agregar al log de procesamiento
                foreach ($resultado['asociados'] as $item) {
                    $this->processingLog[] = [
                        'file' => $fileData['name'],
                        'factura' => $item['numero_factura'],
                        'status' => 'success',
                        'message' => 'PDF asociado correctamente',
                    ];
                }

                foreach ($resultado['staging'] as $item) {
                    $this->processingLog[] = [
                        'file' => $fileData['name'],
                        'factura' => substr($item['cufe'], 0, 16) . '...',
                        'status' => 'staging',
                        'message' => 'Guardado en staging (factura no encontrada)',
                    ];
                }

                foreach ($resultado['omitidos'] as $item) {
                    $this->processingLog[] = [
                        'file' => $fileData['name'],
                        'factura' => $item['numero_factura'],
                        'status' => 'skipped',
                        'message' => $item['razon'],
                    ];
                }

                foreach ($resultado['errores'] as $item) {
                    $this->importErrors[] = "Archivo {$fileData['name']}: " . $item['error'];
                    $this->processingLog[] = [
                        'file' => $fileData['name'],
                        'factura' => substr($item['cufe'], 0, 16) . '...',
                        'status' => 'error',
                        'message' => $item['error'],
                    ];
                }

                // Limpiar archivo temporal
                Storage::disk('local')->delete($fileData['temp_path']);

                // Pequeña pausa para actualizar UI
                usleep(100000); // 100ms
            }

            // Actualizar log de importación
            $importLog->update([
                'successful' => $this->successCount,
                'failed' => $this->errorCount + $this->stagingCount,
                'status' => 'completed',
                'error_details' => count($this->importErrors) > 0 ? ['errors' => $this->importErrors] : null,
            ]);

            // Preparar resumen
            $this->summary = [
                'total_files' => count($this->analysisResults),
                'total_cufes' => $this->totalCufesFound,
                'success' => $this->successCount,
                'staging' => $this->stagingCount,
                'skipped' => $this->skippedCount,
                'errors' => $this->errorCount,
                'import_log_id' => $this->importLogId,
            ];

            $this->isProcessing = false;
            $this->currentStep = 4;

        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->importErrors[] = 'Error fatal: ' . $e->getMessage();
            Log::error('Error en ImportPdfWizard::processImport', ['error' => $e->getMessage()]);

            $this->summary = [
                'total_files' => count($this->analysisResults),
                'total_cufes' => $this->totalCufesFound,
                'success' => $this->successCount,
                'staging' => $this->stagingCount,
                'skipped' => $this->skippedCount,
                'errors' => $this->errorCount + 1,
                'fatal_error' => $e->getMessage(),
            ];
            $this->currentStep = 4;
        }
    }

    /**
     * Mostrar preview de un PDF
     */
    public function showPreview(int $fileIndex)
    {
        if (isset($this->analysisResults[$fileIndex])) {
            $this->previewPdfName = $this->analysisResults[$fileIndex]['name'];
            $tempPath = $this->analysisResults[$fileIndex]['temp_path'];
            // Generar URL temporal para preview (si es necesario)
            $this->previewPdfPath = Storage::disk('local')->url($tempPath);
        }
    }

    /**
     * Cerrar preview
     */
    public function closePreview()
    {
        $this->previewPdfPath = null;
        $this->previewPdfName = null;
    }

    /**
     * Volver al paso anterior
     */
    public function previousStep()
    {
        if ($this->currentStep > 1 && !$this->isProcessing && !$this->isAnalyzing) {
            $this->currentStep--;
        }
    }

    /**
     * Reiniciar el wizard
     */
    public function resetWizard()
    {
        // Limpiar archivos temporales
        foreach ($this->analysisResults as $fileData) {
            if (isset($fileData['temp_path'])) {
                Storage::disk('local')->delete($fileData['temp_path']);
            }
        }

        // Limpiar archivos guardados en storedPaths
        foreach ($this->storedPaths as $path) {
            Storage::disk('local')->delete($path);
        }

        $this->reset([
            'currentStep',
            'pdfFiles',
            'uploadedFiles',
            'storedPaths',
            'totalFilesSize',
            'analysisResults',
            'totalCufesFound',
            'cufesWithFactura',
            'cufesAlreadyAssociated',
            'cufesAvailable',
            'cufesNotFound',
            'isProcessing',
            'isAnalyzing',
            'processedFiles',
            'totalFiles',
            'successCount',
            'stagingCount',
            'skippedCount',
            'errorCount',
            'currentFile',
            'currentCufe',
            'importErrors',
            'processingLog',
            'importLogId',
            'summary',
            'previewPdfPath',
            'previewPdfName',
        ]);

        $this->extractAdditionalData = true;
        $this->saveToStaging = true;
        $this->overwriteExisting = false;
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
        if (count($this->analysisResults) === 0) return 0;
        return (int) round(($this->processedFiles / count($this->analysisResults)) * 100);
    }

    /**
     * Renderizar el componente
     */
    public function render()
    {
        return view('livewire.import-pdf-wizard');
    }
}

