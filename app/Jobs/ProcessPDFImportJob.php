<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Models\ImportRecord;
use App\Models\Factura;
use App\Services\PDFCufeExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessPDFImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importLogId;
    protected $tenantId;
    protected $filePaths;
    protected $extractCUFE;
    protected $selectedCufes;

    public function __construct($importLogId, $tenantId, $filePaths, $extractCUFE = true, $selectedCufes = [])
    {
        $this->importLogId = $importLogId;
        $this->tenantId = $tenantId;
        $this->filePaths = $filePaths;
        $this->extractCUFE = $extractCUFE;
        $this->selectedCufes = $selectedCufes;
    }

    public function handle()
    {
        $importLog = ImportLog::find($this->importLogId);
        $pdfService = app(PDFCufeExtractorService::class);

        try {
            $successfulCount = 0;
            $stagingCount = 0;
            $failedCount = 0;
            $totalCufes = 0;

            foreach ($this->filePaths as $filePath) {
                $fullPath = storage_path('app/' . $filePath);

                try {
                    // 1. Cargar el PDF usando el método correcto del servicio
                    $pdfInfo = $pdfService->cargarPDF($fullPath);

                    // 2. Extraer CUFEs usando el método correcto del servicio
                    $cufesEncontrados = $pdfService->extraerCUFEs($pdfInfo['pages'], $fullPath);

                    // Filtrar por CUFEs seleccionados si se especificaron
                    if (!empty($this->selectedCufes)) {
                        $cufesEncontrados = array_filter($cufesEncontrados, function($cufeData) {
                            return in_array($cufeData['cufe'], $this->selectedCufes);
                        });
                        $cufesEncontrados = array_values($cufesEncontrados);
                    }

                    $totalCufes += count($cufesEncontrados);

                    if (!empty($cufesEncontrados)) {
                        // 3. Procesar y asociar el PDF a las facturas usando el método correcto
                        $resultadoProceso = $pdfService->procesarPDF(
                            $fullPath,
                            $cufesEncontrados,
                            $this->tenantId,
                            false, // dryRun = false, queremos guardar
                            true   // guardarEnStaging = true
                        );

                        // Registrar resultados de asociaciones exitosas
                        foreach ($resultadoProceso['asociados'] as $asociado) {
                            $successfulCount++;
                            ImportRecord::create([
                                'import_log_id' => $this->importLogId,
                                'factura_id' => $asociado['factura_id'] ?? null,
                                'cufe' => $asociado['cufe'],
                                'numero_factura' => $asociado['numero_factura'] ?? $asociado['cufe'],
                                'nit' => '',
                                'status' => 'associated',
                                'error_message' => null,
                            ]);
                        }

                        // Registrar resultados de staging
                        foreach ($resultadoProceso['staging'] as $staging) {
                            $stagingCount++;
                            ImportRecord::create([
                                'import_log_id' => $this->importLogId,
                                'factura_id' => null,
                                'cufe' => $staging['cufe'],
                                'numero_factura' => $staging['cufe'],
                                'nit' => '',
                                'status' => 'staging',
                                'error_message' => 'CUFE no encontrado en facturas, guardado en staging',
                            ]);
                        }

                        // Registrar omitidos (ya tenían PDF)
                        foreach ($resultadoProceso['omitidos'] as $omitido) {
                            ImportRecord::create([
                                'import_log_id' => $this->importLogId,
                                'factura_id' => $omitido['factura_id'] ?? null,
                                'cufe' => $omitido['cufe'],
                                'numero_factura' => $omitido['numero_factura'] ?? $omitido['cufe'],
                                'nit' => '',
                                'status' => 'skipped',
                                'error_message' => $omitido['razon'] ?? 'Ya tiene PDF asignado',
                            ]);
                        }

                        // Registrar errores
                        foreach ($resultadoProceso['errores'] as $error) {
                            $failedCount++;
                            ImportRecord::create([
                                'import_log_id' => $this->importLogId,
                                'factura_id' => null,
                                'cufe' => $error['cufe'],
                                'numero_factura' => $error['cufe'],
                                'nit' => '',
                                'status' => 'error',
                                'error_message' => $error['error'] ?? 'Error desconocido',
                            ]);
                        }

                    } else {
                        $failedCount++;
                        ImportRecord::create([
                            'import_log_id' => $this->importLogId,
                            'numero_factura' => basename($filePath),
                            'nit' => '',
                            'status' => 'error',
                            'error_message' => 'No se encontraron CUFEs en el PDF',
                        ]);
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    ImportRecord::create([
                        'import_log_id' => $this->importLogId,
                        'numero_factura' => basename($filePath),
                        'nit' => '',
                        'status' => 'error',
                        'error_message' => 'Error procesando PDF: ' . $e->getMessage(),
                    ]);
                }

                $importLog->update([
                    'total_records' => $totalCufes > 0 ? $totalCufes : count($this->filePaths),
                    'successful' => $successfulCount,
                    'failed' => $failedCount,
                ]);
            }

            $importLog->update([
                'status' => 'completed',
                'total_records' => $totalCufes > 0 ? $totalCufes : count($this->filePaths),
                'successful' => $successfulCount,
                'failed' => $failedCount,
            ]);

        } catch (\Exception $e) {
            $importLog->update([
                'status' => 'failed',
                'error_details' => json_encode(['error' => $e->getMessage()]),
            ]);
        } finally {
            foreach ($this->filePaths as $filePath) {
                $fullPath = storage_path('app/' . $filePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
    }
}
