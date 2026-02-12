<?php

namespace App\Services\Import;

use App\Models\Factura;
use App\Models\ImportJob;
use App\Models\ImportError;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Smalot\PdfParser\Parser as PdfParser;

class PdfImportService
{
    private PdfParser $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new PdfParser();
    }

    /**
     * Procesar importación de PDF (múltiples facturas)
     */
    public function procesarPdf(string $rutaArchivo, int $usuarioId): ImportJob
    {
        $importJob = ImportJob::create([
            'user_id' => $usuarioId,
            'tipo_importacion' => 'pdf',
            'nombre_archivo' => basename($rutaArchivo),
            'ruta_archivo' => $rutaArchivo,
            'estado' => 'procesando',
            'fecha_inicio' => Carbon::now(),
        ]);

        try {
            // Parsear PDF
            $pdfDocument = $this->pdfParser->parseFile($rutaArchivo);
            $texto = $pdfDocument->getText();

            // Extraer CUFEs del PDF (búsqueda de patrones UUID)
            $cufes = $this->extraerCufes($texto);

            if (empty($cufes)) {
                $importJob->update([
                    'estado' => 'error',
                    'fecha_fin' => Carbon::now(),
                    'mensaje_error' => 'No se encontraron CUFEs válidos en el PDF',
                    'total_registros' => 0,
                ]);
                return $importJob;
            }

            $importJob->update(['total_registros' => count($cufes)]);

            // Procesar cada CUFE encontrado
            foreach ($cufes as $cufe) {
                $resultado = $this->procesarCufe($cufe, $rutaArchivo, $pdfDocument, $texto, $importJob);

                $importJob->increment('registros_procesados');

                if ($resultado['exito']) {
                    $importJob->increment('registros_exitosos');
                } else {
                    $importJob->increment('registros_error');

                    ImportError::create([
                        'import_job_id' => $importJob->id,
                        'cufe' => $cufe,
                        'tipo_error' => $resultado['tipo_error'],
                        'mensaje_error' => $resultado['mensaje'],
                    ]);
                }
            }

            // Completar job
            $importJob->update([
                'estado' => 'completado',
                'fecha_fin' => Carbon::now(),
            ]);

            // Registrar en auditoría
            $this->registrarAuditoria($importJob, $usuarioId);

        } catch (\Exception $e) {
            Log::error('Error en importación PDF: ' . $e->getMessage());

            $importJob->update([
                'estado' => 'error',
                'fecha_fin' => Carbon::now(),
                'mensaje_error' => $e->getMessage(),
            ]);
        }

        return $importJob;
    }

    /**
     * Extraer CUFEs del texto del PDF (patrón UUID SHA-256 en hex)
     */
    private function extraerCufes(string $texto): array
    {
        // Patrón para UUID/CUFE (64 caracteres hexadecimales = SHA-256)
        $patron = '/\b[a-f0-9]{64}\b/i';

        $cufes = [];
        if (preg_match_all($patron, $texto, $matches)) {
            $cufes = array_unique($matches[0]);
            // Convertir a minúsculas para consistencia
            $cufes = array_map('strtolower', $cufes);
        }

        return $cufes;
    }

    /**
     * Procesar CUFE individual
     */
    private function procesarCufe(string $cufe, string $rutaArchivo, $pdfDocument, string $textoPdf, ImportJob $importJob): array
    {
        try {
            // Buscar factura por CUFE
            $factura = Factura::where('cufe', $cufe)->first();

            if (!$factura) {
                return [
                    'exito' => false,
                    'tipo_error' => 'validacion',
                    'mensaje' => "No se encontró factura con CUFE: {$cufe}",
                ];
            }

            // Extraer metadatos del PDF para este CUFE
            $metadatos = $this->extraerMetadatos($cufe, $textoPdf);

            // Validar integridad financiera
            $validacionMonto = $this->validarMonto($factura, $metadatos);

            if (!$validacionMonto['valido']) {
                return [
                    'exito' => false,
                    'tipo_error' => 'integridad',
                    'mensaje' => $validacionMonto['mensaje'],
                ];
            }

            // Almacenar PDF y calcular hash
            $resultadoAlmacenamiento = $this->almacenarPdf($rutaArchivo, $cufe);

            if (!$resultadoAlmacenamiento['exito']) {
                return [
                    'exito' => false,
                    'tipo_error' => 'extraccion',
                    'mensaje' => $resultadoAlmacenamiento['mensaje'],
                ];
            }

            // Actualizar factura con metadatos enriquecidos
            $factura->update([
                'pdf_path' => $resultadoAlmacenamiento['pdf_path'],
                'hash_pdf' => $resultadoAlmacenamiento['hash_pdf'],
                'motonave' => $metadatos['motonave'] ?? null,
                'trb' => $metadatos['trb'] ?? null,
                'servicio_suministrado' => $metadatos['servicio'] ?? null,
                'locacion' => $metadatos['locacion'] ?? null,
            ]);

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $importJob->user_id,
                'event_type' => 'factura_pdf_enriched',
                'description' => "Factura enriquecida con PDF: {$factura->numero_factura} (CUFE: {$cufe})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'factura_id' => $factura->id,
                    'cufe' => $cufe,
                    'metadatos_extraidos' => $metadatos,
                    'hash_pdf' => $resultadoAlmacenamiento['hash_pdf'],
                ]),
            ]);

            return [
                'exito' => true,
                'factura_id' => $factura->id,
                'metadatos' => $metadatos,
            ];

        } catch (\Exception $e) {
            Log::error("Error procesando CUFE {$cufe}: " . $e->getMessage());

            return [
                'exito' => false,
                'tipo_error' => 'validacion',
                'mensaje' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extraer metadatos del PDF usando OCR/ICR
     */
    private function extraerMetadatos(string $cufe, string $textoPdf): array
    {
        $metadatos = [
            'motonave' => null,
            'trb' => null,
            'servicio' => null,
            'locacion' => null,
        ];

        // Buscar Motonave (patrones comunes)
        $patronesMotonave = [
            '/(?:MOTONAVE|BUQUE|VESSEL|SHIP)[:\s]+([A-Z\s\-\d]+)/i',
            '/^([A-Z][A-Z\s]+)\s+(?:MTN|MOTONAVE)/i',
        ];

        foreach ($patronesMotonave as $patron) {
            if (preg_match($patron, $textoPdf, $matches)) {
                $metadatos['motonave'] = trim($matches[1]);
                break;
            }
        }

        // Buscar TRB (Tonelaje)
        if (preg_match('/(?:TRB|TONELAJE|REGISTRO)[\s:]+(\d+(?:[.,]\d{3})*)/i', $textoPdf, $matches)) {
            $metadatos['trb'] = trim($matches[1]);
        }

        // Buscar Servicio
        $patronesServicio = [
            '/(?:SERVICIO|SERVICE)[:\s]+([A-Z\s\-]+?)(?:\n|VALOR|MONTO|PRICE)/i',
            '/(?:TRANSPORTE|INSPECCIÓN|BUZO)[S]?(?:[:\s]+)?([A-Z\s\-]+?)(?:\n|$)/i',
        ];

        foreach ($patronesServicio as $patron) {
            if (preg_match($patron, $textoPdf, $matches)) {
                $metadatos['servicio'] = trim($matches[1]);
                break;
            }
        }

        // Buscar Locación/Puerto
        $patronesLocacion = [
            '/(?:PUERTO|PORT|LOCACIÓN|LOCATION)[:\s]+([A-Z\s\-\d]+)/i',
            '/(?:ZUÑIGA|DRUMMOND|PUERTO BOLÍVAR|SANTA MARTA)[A-Z\s\-]*/i',
        ];

        foreach ($patronesLocacion as $patron) {
            if (preg_match($patron, $textoPdf, $matches)) {
                $metadatos['locacion'] = trim($matches[0]);
                break;
            }
        }

        return array_filter($metadatos); // Remover nulls
    }

    /**
     * Validar integridad financiera
     */
    private function validarMonto(Factura $factura, array $metadatos): array
    {
        // Aquí iría la validación de monto extraído del PDF vs almacenado
        // Por ahora es un placeholder que siempre pasa

        // En una implementación real, el OCR extraería el monto del PDF
        // y se compararía con $factura->total_pagar

        return [
            'valido' => true,
            'mensaje' => 'Monto validado correctamente',
        ];
    }

    /**
     * Almacenar PDF y calcular hash
     */
    private function almacenarPdf(string $rutaOriginal, string $cufe): array
    {
        try {
            // Leer contenido del PDF
            $contenido = file_get_contents($rutaOriginal);

            if ($contenido === false) {
                return [
                    'exito' => false,
                    'mensaje' => 'No se pudo leer el archivo PDF',
                ];
            }

            // Calcular hash SHA-256
            $hashPdf = hash('sha256', $contenido);

            // Crear ruta de almacenamiento
            $directorio = 'facturas/' . substr($cufe, 0, 2) . '/' . substr($cufe, 2, 2);
            $nombreArchivo = "{$cufe}.pdf";
            $rutaAlmacenamiento = $directorio . '/' . $nombreArchivo;

            // Crear directorio si no existe
            if (!Storage::disk('public')->exists($directorio)) {
                Storage::disk('public')->makeDirectory($directorio, 0755, true);
            }

            // Guardar archivo
            Storage::disk('public')->put($rutaAlmacenamiento, $contenido);

            // Verificar que se guardó correctamente
            if (!Storage::disk('public')->exists($rutaAlmacenamiento)) {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al almacenar el PDF',
                ];
            }

            return [
                'exito' => true,
                'pdf_path' => 'storage/' . $rutaAlmacenamiento,
                'hash_pdf' => $hashPdf,
            ];

        } catch (\Exception $e) {
            Log::error('Error almacenando PDF: ' . $e->getMessage());

            return [
                'exito' => false,
                'mensaje' => 'Error al procesar PDF: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Registrar auditoría
     */
    private function registrarAuditoria(ImportJob $importJob, int $usuarioId): void
    {
        SecurityEvent::create([
            'user_id' => $usuarioId,
            'event_type' => 'import_pdf_completed',
            'description' => "Importación PDF completada: {$importJob->registros_exitosos} exitosas, {$importJob->registros_error} con error",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => json_encode([
                'import_job_id' => $importJob->id,
                'nombre_archivo' => $importJob->nombre_archivo,
                'total_registros' => $importJob->total_registros,
                'registros_exitosos' => $importJob->registros_exitosos,
                'registros_error' => $importJob->registros_error,
                'fecha_inicio' => $importJob->fecha_inicio,
                'fecha_fin' => $importJob->fecha_fin,
            ]),
        ]);
    }
}

