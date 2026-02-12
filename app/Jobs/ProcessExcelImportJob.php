<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Models\ImportRecord;
use App\Models\Tercero;
use App\Models\Factura;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProcessExcelImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importLogId;
    protected $tenantId;
    protected $filePath;
    protected $columnMapping;

    public function __construct($importLogId, $tenantId, $filePath, $columnMapping)
    {
        $this->importLogId = $importLogId;
        $this->tenantId = $tenantId;
        $this->filePath = $filePath;
        $this->columnMapping = $columnMapping;
    }

    public function handle()
    {
        $importLog = ImportLog::find($this->importLogId);

        try {
            $spreadsheet = IOFactory::load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $totalRecords = count($rows) - 1; // Excluir header
            $importLog->update(['total_records' => $totalRecords]);

            $successfulCount = 0;
            $failedCount = 0;

            // Procesar por lotes de 100
            $batch = [];
            $batchSize = 100;

            foreach ($rows as $rowIndex => $row) {
                // Skip header
                if ($rowIndex === 0) continue;

                try {
                    $resultado = $this->procesarFila($row, $rowIndex);

                    if ($resultado['status'] === 'new' || $resultado['status'] === 'duplicate') {
                        $successfulCount++;
                    } else {
                        $failedCount++;
                    }

                    // Registrar en import_records
                    ImportRecord::create([
                        'import_log_id' => $this->importLogId,
                        'factura_id' => $resultado['factura_id'] ?? null,
                        'tercero_id' => $resultado['tercero_id'] ?? null,
                        'cufe' => $resultado['cufe'] ?? null,
                        'numero_factura' => $resultado['numero_factura'] ?? '',
                        'nit' => $resultado['nit'] ?? '',
                        'status' => $resultado['status'],
                        'error_message' => $resultado['error'] ?? null,
                    ]);

                } catch (\Exception $e) {
                    $failedCount++;
                    ImportRecord::create([
                        'import_log_id' => $this->importLogId,
                        'numero_factura' => $row[$this->getColumnIndex('numero_factura')] ?? 'N/A',
                        'nit' => $row[$this->getColumnIndex('nit')] ?? 'N/A',
                        'status' => 'error',
                        'error_message' => 'Error procesando fila: ' . $e->getMessage(),
                    ]);
                }

                // Actualizar progreso cada lote
                if (($rowIndex % $batchSize) === 0) {
                    $importLog->update([
                        'successful' => $successfulCount,
                        'failed' => $failedCount,
                    ]);
                }
            }

            // Actualizar con conteos finales
            $importLog->update([
                'status' => 'completed',
                'successful' => $successfulCount,
                'failed' => $failedCount,
            ]);

        } catch (\Exception $e) {
            $importLog->update([
                'status' => 'failed',
                'error_details' => json_encode(['error' => $e->getMessage()]),
            ]);
        } finally {
            // Limpiar archivo temporal
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        }
    }

    private function procesarFila($row, $rowIndex)
    {
        // Extraer valores del Excel (pueden ser null o vacíos)
        $numeroFactura = trim($row[$this->getColumnIndex('numero_factura')] ?? '');
        $nit = trim($row[$this->getColumnIndex('nit')] ?? '');
        $nombreCliente = trim($row[$this->getColumnIndex('nombre_cliente')] ?? '');
        $fechaFactura = trim((string)($row[$this->getColumnIndex('fecha_factura')] ?? ''));
        $fechaVencimiento = trim((string)($row[$this->getColumnIndex('fecha_vencimiento')] ?? ''));

        $subtotal = (float)($row[$this->getColumnIndex('subtotal')] ?? 0);
        $iva = (float)($row[$this->getColumnIndex('iva')] ?? 0);
        $descuento = (float)($row[$this->getColumnIndex('descuento')] ?? 0);

        $motonave = trim($row[$this->getColumnIndex('motonave')] ?? '') ?: null;
        $trb = trim($row[$this->getColumnIndex('trb')] ?? '') ?: null;
        $descripcion = trim($row[$this->getColumnIndex('descripcion')] ?? '') ?: null;
        $direccion = trim($row[$this->getColumnIndex('direccion')] ?? '') ?: null;
        $telefono = trim($row[$this->getColumnIndex('telefono')] ?? '') ?: null;
        $email = trim($row[$this->getColumnIndex('email')] ?? '') ?: null;

        // Validaciones básicas: campos obligatorios
        if (empty($numeroFactura)) {
            throw new \Exception('Número de factura vacío');
        }
        if (empty($nit)) {
            throw new \Exception('NIT vacío');
        }
        if (empty($nombreCliente)) {
            throw new \Exception('Nombre del cliente vacío');
        }
        if (empty($fechaFactura)) {
            throw new \Exception('Fecha de factura vacía');
        }

        // Validar y parsear fecha (CRÍTICO: del Excel, nunca fecha actual)
        $fechaFacturaObj = $this->parsearFecha($fechaFactura);
        if (!$fechaFacturaObj) {
            throw new \Exception('Fecha inválida o no parseable: "' . $fechaFactura . '". Formatos esperados: YYYY-MM-DD, DD/MM/YYYY, MM/DD/YYYY');
        }

        // Validar que la fecha no sea en el futuro (data histórica)
        if ($fechaFacturaObj > Carbon::now()) {
            throw new \Exception('Fecha de factura es futura: ' . $fechaFacturaObj->format('Y-m-d'));
        }

        // Validar fecha de vencimiento si existe
        $fechaVencimientoObj = null;
        if (!empty($fechaVencimiento)) {
            $fechaVencimientoObj = $this->parsearFecha($fechaVencimiento);
            if ($fechaVencimientoObj && $fechaVencimientoObj < $fechaFacturaObj) {
                throw new \Exception('Fecha de vencimiento no puede ser anterior a fecha de factura');
            }
        }

        // Validar NIT antes de usarlo
        $nitLimpio = str_replace(['.', '-', ' '], '', $nit);
        if (!Tercero::validarNitColombia($nitLimpio)) {
            throw new \Exception('NIT inválido (debe ser 6-15 dígitos): ' . $nit);
        }

        // Validar montos (deben ser no negativos)
        if ($subtotal < 0) {
            throw new \Exception('Subtotal no puede ser negativo: ' . $subtotal);
        }
        if ($iva < 0) {
            throw new \Exception('IVA no puede ser negativo: ' . $iva);
        }
        if ($descuento < 0) {
            throw new \Exception('Descuento no puede ser negativo: ' . $descuento);
        }
        if ($descuento > ($subtotal + $iva)) {
            throw new \Exception('Descuento no puede ser mayor que subtotal + IVA');
        }

        // Calcular total
        $total = ($subtotal + $iva) - $descuento;

        // Validar que total no sea negativo
        if ($total < 0) {
            throw new \Exception('Total a pagar resultaría negativo');
        }

        // Generar CUFE (SHA-256 del número de factura + fecha)
        // IMPORTANTE: CUFE determina unicidad, debe ser determinístico
        $cufe = $this->generarCufe($numeroFactura, $nitLimpio, $fechaFacturaObj);

        // Verificar si la factura ya existe (DUPLICADO por CUFE)
        $facturaExistente = Factura::where('tenant_id', $this->tenantId)
            ->where('cufe', $cufe)
            ->first();

        if ($facturaExistente) {
            return [
                'status' => 'duplicate',
                'cufe' => $cufe,
                'numero_factura' => $numeroFactura,
                'nit' => $nitLimpio,
                'factura_id' => $facturaExistente->id,
                'tercero_id' => $facturaExistente->tercero_id,
            ];
        }

        // Buscar tercero existente por NIT
        $tercero = Tercero::where('tenant_id', $this->tenantId)
            ->where('nit', $nitLimpio)
            ->first();

        if (!$tercero) {
            // Crear nuevo tercero
            try {
                $tercero = Tercero::create([
                    'tenant_id' => $this->tenantId,
                    'nit' => $nitLimpio,
                    'nombre_razon_social' => $nombreCliente,
                    'direccion' => $direccion,
                    'telefono' => $telefono,
                    'email' => $email,
                    'estado' => 'activo',
                ]);
            } catch (\Exception $e) {
                throw new \Exception('Error creando tercero: ' . $e->getMessage());
            }
        } else {
            // Tercero existe, actualizar si hay información nueva
            if ($direccion || $telefono || $email) {
                $tercero->update([
                    'direccion' => $direccion ?? $tercero->direccion,
                    'telefono' => $telefono ?? $tercero->telefono,
                    'email' => $email ?? $tercero->email,
                ]);
            }
        }

        // Crear factura
        try {
            $facturaData = [
                'tenant_id' => $this->tenantId,
                'tercero_id' => $tercero->id,
                'cufe' => $cufe,
                'numero_factura' => $numeroFactura,
                'fecha_factura' => $fechaFacturaObj,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'descuento' => $descuento,
                'total_pagar' => $total,
                'motonave' => $motonave,
                'trb' => $trb,
                'servicio_descripcion' => $descripcion,
                'estado' => 'pendiente',
            ];

            // Agregar fecha de vencimiento si existe
            if ($fechaVencimientoObj) {
                $facturaData['fecha_vencimiento'] = $fechaVencimientoObj;
            }

            $factura = Factura::create($facturaData);

            // **IMPORTANTE:** Buscar PDF en staging y asociar automáticamente
            $this->asociarPDFDeStaging($factura, $cufe);

        } catch (\Exception $e) {
            throw new \Exception('Error creando factura: ' . $e->getMessage());
        }

        return [
            'status' => 'new',
            'cufe' => $cufe,
            'numero_factura' => $numeroFactura,
            'nit' => $nitLimpio,
            'factura_id' => $factura->id,
            'tercero_id' => $tercero->id,
        ];
    }

    private function getColumnIndex($columnName)
    {
        if (!$this->columnMapping || !isset($this->columnMapping[$columnName])) {
            return null;
        }

        $mappedName = $this->columnMapping[$columnName];

        // Si es un número, retornar directamente
        if (is_numeric($mappedName)) {
            return (int)$mappedName;
        }

        // Si es letra (A, B, C...), convertir a índice
        return $this->letraAIndice($mappedName);
    }

    private function letraAIndice($letra)
    {
        $letra = strtoupper($letra);
        $indice = 0;

        foreach (str_split($letra) as $char) {
            $indice = $indice * 26 + (ord($char) - ord('A') + 1);
        }

        return $indice - 1; // 0-indexed
    }

    private function parsearFecha($fecha)
    {
        if (empty($fecha)) {
            return null;
        }

        // Convertir a string si es un número
        $fechaStr = (string)$fecha;

        // Si es número de Excel (representación de días desde 1899-12-30)
        if (is_numeric($fechaStr) && strpos($fechaStr, '.') !== false) {
            // Número decimal de Excel
            $excelNumber = (float)$fechaStr;
            $base = new \DateTime('1899-12-30');
            $days = intval($excelNumber);
            $base->modify('+' . $days . ' days');

            // Ajustar por horas/minutos si hay decimales
            $fraction = $excelNumber - $days;
            $hours = intval($fraction * 24);
            $minutes = intval(($fraction * 24 - $hours) * 60);
            $base->modify("+{$hours} hours +{$minutes} minutes");

            return $base;
        } elseif (is_numeric($fechaStr)) {
            // Número entero de Excel
            $excelNumber = (int)$fechaStr;
            $base = new \DateTime('1899-12-30');
            $base->modify('+' . $excelNumber . ' days');
            return $base;
        }

        // Intentar parsearlo como string en múltiples formatos
        // Orden de intentos: más específicos primero
        $formatos = [
            'Y-m-d H:i:s',     // 2025-01-15 14:30:45
            'd/m/Y H:i:s',     // 15/01/2025 14:30:45
            'm/d/Y H:i:s',     // 01/15/2025 14:30:45
            'Y-m-d',           // 2025-01-15
            'd/m/Y',           // 15/01/2025
            'm/d/Y',           // 01/15/2025
            'd-m-Y',           // 15-01-2025
            'Y/m/d',           // 2025/01/15
            'd.m.Y',           // 15.01.2025
            'd M Y',           // 15 Jan 2025
            'd F Y',           // 15 January 2025
        ];

        foreach ($formatos as $formato) {
            try {
                $date = \DateTime::createFromFormat($formato, trim($fechaStr));
                if ($date && $date->format($formato) === trim($fechaStr)) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Último intento: strtotime
        $timestamp = strtotime($fechaStr);
        if ($timestamp !== false) {
            return new \DateTime('@' . $timestamp);
        }

        return null;
    }

    private function generarCufe($numeroFactura, $nit, $fechaFactura)
    {
        // CUFE: SHA-256 del número de factura + NIT + fecha
        // Debe ser determinístico: mismo Excel siempre genera mismo CUFE
        $data = $numeroFactura . '|' . $nit . '|' . $fechaFactura->format('Y-m-d');
        return strtoupper(hash('sha256', $data));
    }

    /**
     * Buscar y asociar PDF de staging cuando se crea una factura
     *
     * Flujo: Si el usuario subió PDFs ANTES del Excel, los PDFs quedaron en staging.
     * Cuando se crea la factura por Excel, buscamos si hay un PDF con ese CUFE en staging
     * y lo asociamos automáticamente.
     */
    private function asociarPDFDeStaging(Factura $factura, $cufe)
    {
        try {
            // Ruta donde se guardan los PDFs en staging
            $stagingPath = "staging/{$this->tenantId}/{$cufe}.pdf";

            // Verificar si existe el PDF en staging
            if (Storage::disk('local')->exists($stagingPath)) {
                // Mover PDF de staging a su ubicación final
                $finalPath = "facturas/{$this->tenantId}/{$factura->id}.pdf";

                // Crear directorio si no existe
                $dir = dirname(storage_path('app/' . $finalPath));
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Copiar de staging a facturas
                $pdfContent = Storage::disk('local')->get($stagingPath);
                Storage::disk('local')->put($finalPath, $pdfContent);

                // Generar hash del PDF
                $pdfHash = hash('sha256', $cufe . '_from_staging');

                // Actualizar factura con la información del PDF
                $factura->update([
                    'pdf_path' => $finalPath,
                    'hash_pdf' => $pdfHash,
                ]);

                // Eliminar archivo de staging (ya está asociado)
                Storage::disk('local')->delete($stagingPath);

                \Log::info("PDF asociado desde staging: {$stagingPath} -> {$finalPath} para factura #{$factura->id} (CUFE: {$cufe})");
            }
        } catch (\Exception $e) {
            // No interrumpir la importación si falla la asociación de PDF
            \Log::warning("Error asociando PDF de staging para CUFE {$cufe}: " . $e->getMessage());
        }
    }
}
