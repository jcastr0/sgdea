<?php

namespace App\Services\Import;

use App\Models\Factura;
use App\Models\ImportJob;
use App\Models\ImportError;
use App\Models\Tercero;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ExcelImportService
{
    /**
     * Procesar importación de Excel
     */
    public function procesarExcel(string $rutaArchivo, int $usuarioId): ImportJob
    {
        $importJob = ImportJob::create([
            'user_id' => $usuarioId,
            'tipo_importacion' => 'excel',
            'nombre_archivo' => basename($rutaArchivo),
            'ruta_archivo' => $rutaArchivo,
            'estado' => 'procesando',
            'fecha_inicio' => Carbon::now(),
        ]);

        try {
            // Leer archivo Excel
            $spreadsheet = IOFactory::load($rutaArchivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Saltar encabezado (fila 1)
            $datos = array_slice($rows, 1);

            // Actualizar total de registros
            $importJob->update(['total_registros' => count($datos)]);

            // Procesar cada fila
            foreach ($datos as $numeroFila => $fila) {
                $numeroFila += 2; // +2 porque saltamos encabezado y empieza en 0

                // Validar que la fila no esté vacía
                if (empty(array_filter($fila))) {
                    continue;
                }

                // Parsear fila
                $datosFila = $this->parsearFila($fila);

                if (!$datosFila) {
                    continue;
                }

                // Procesar factura
                $resultado = $this->procesarFactura($datosFila, $numeroFila, $importJob);

                // Actualizar contadores
                $importJob->increment('registros_procesados');

                if ($resultado['exito']) {
                    $importJob->increment('registros_exitosos');
                } else {
                    $importJob->increment('registros_error');

                    // Registrar error
                    ImportError::create([
                        'import_job_id' => $importJob->id,
                        'numero_fila' => $numeroFila,
                        'cufe' => $datosFila['cufe'] ?? null,
                        'numero_factura' => $datosFila['numero_factura'] ?? null,
                        'tipo_error' => $resultado['tipo_error'],
                        'mensaje_error' => $resultado['mensaje'],
                        'datos_fila' => $datosFila,
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
            Log::error('Error en importación Excel: ' . $e->getMessage());

            $importJob->update([
                'estado' => 'error',
                'fecha_fin' => Carbon::now(),
                'mensaje_error' => $e->getMessage(),
            ]);
        }

        return $importJob;
    }

    /**
     * Parsear fila del Excel
     */
    private function parsearFila(array $fila): ?array
    {
        // Mapeo de columnas
        // [0] Documento, [1] Prefijo, [2] Tipo Documento, [3] Estado Comercial
        // [4] Tipo Identificación, [5] Identificación, [6] Comprador
        // [7] Fecha de Emisión, [8] Moneda, [9] Forma de Pago
        // [10] Valor Total, [11] UUID

        // Validar que hay datos suficientes
        if (count($fila) < 12) {
            return null;
        }

        // Extraer datos
        $numero_factura = $fila[0] ?? null;
        $prefijo = $fila[1] ?? null;
        $tipo_documento_raw = $fila[2] ?? null;
        $estado_comercial = $fila[3] ?? null;
        $nit_tercero = $fila[5] ?? null;
        $nombre_tercero = $fila[6] ?? null;
        $fecha_emision_raw = $fila[7] ?? null;
        $moneda = $fila[8] ?? 'COP';
        $forma_pago = $fila[9] ?? null;
        $valor_total_raw = $fila[10] ?? null;
        $cufe = $fila[11] ?? null;

        // Validar campos obligatorios
        if (!$numero_factura || !$nit_tercero || !$nombre_tercero || !$cufe || !$valor_total_raw) {
            return null;
        }

        // Mapear tipo de documento
        $tipo_documento = $this->mapearTipoDocumento($tipo_documento_raw);
        if (!$tipo_documento) {
            return null;
        }

        // Mapear estado
        $estado = $this->mapearEstado($estado_comercial);

        // Parsear fecha
        $fecha_factura = $this->parsearFecha($fecha_emision_raw);
        if (!$fecha_factura) {
            return null;
        }

        // Parsear monto
        $total_pagar = $this->parsearMonto($valor_total_raw);
        if ($total_pagar === null) {
            return null;
        }

        return [
            'numero_factura' => trim($numero_factura),
            'prefijo' => trim($prefijo),
            'tipo_documento' => $tipo_documento,
            'estado' => $estado,
            'nit_tercero' => trim($nit_tercero),
            'nombre_tercero' => trim($nombre_tercero),
            'fecha_factura' => $fecha_factura,
            'moneda' => trim($moneda),
            'forma_pago' => trim($forma_pago),
            'total_pagar' => $total_pagar,
            'cufe' => trim($cufe),
        ];
    }

    /**
     * Mapear tipo de documento
     */
    private function mapearTipoDocumento(string $tipo): ?string
    {
        // Normalizar: mayúsculas y quitar tildes
        $tipo = strtoupper($tipo);
        $tipo = $this->quitarTildes($tipo);

        // Factura de Venta
        if (strpos($tipo, 'FACTURA') !== false && strpos($tipo, 'VENTA') !== false) {
            return 'Factura de Venta';
        }

        // Nota Crédito (con o sin tilde)
        if (strpos($tipo, 'NOTA') !== false && strpos($tipo, 'CREDITO') !== false) {
            return 'Nota Crédito';
        }

        // Nota Débito (con o sin tilde)
        if (strpos($tipo, 'NOTA') !== false && strpos($tipo, 'DEBITO') !== false) {
            return 'Nota Débito';
        }

        // Si solo dice FACTURA
        if (strpos($tipo, 'FACTURA') !== false) {
            return 'Factura de Venta';
        }

        // Valor por defecto si no se reconoce
        return $tipo ?: 'Factura de Venta';
    }

    /**
     * Quitar tildes de un string
     */
    private function quitarTildes(string $texto): string
    {
        $buscar = ['Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú', 'Ñ', 'ñ'];
        $reemplazar = ['A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'N', 'n'];
        return str_replace($buscar, $reemplazar, $texto);
    }

    /**
     * Mapear estado comercial
     */
    private function mapearEstado(string $estado): string
    {
        $estado = strtolower($estado);

        if (strpos($estado, 'aceptad') !== false) {
            return 'aceptada';
        }

        if (strpos($estado, 'rechaz') !== false) {
            return 'rechazada';
        }

        return 'pendiente';
    }

    /**
     * Parsear fecha
     */
    private function parsearFecha($fecha): ?string
    {
        if (!$fecha) {
            return null;
        }

        // Si es número (formato Excel)
        if (is_numeric($fecha)) {
            try {
                $fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha);
                return $fechaObj->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Si es string, intentar parsear
        try {
            return Carbon::createFromFormat('Y-m-d', $fecha)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parsear monto (convertir "$ 3.974.956,80" a 3974956.80)
     */
    private function parsearMonto($monto): ?float
    {
        if (!$monto) {
            return null;
        }

        // Si es número
        if (is_numeric($monto)) {
            return (float) $monto;
        }

        // Si es string, limpiar formato
        $monto = (string) $monto;
        // Quitar símbolos de moneda
        $monto = preg_replace('/[^\d,.-]/', '', $monto);
        // Reemplazar coma por punto (formato colombiano)
        $monto = str_replace(',', '.', $monto);
        // Remover puntos de miles
        $monto = preg_replace('/\.(?=\d{1,2}$)/', '', $monto);

        if (is_numeric($monto)) {
            return (float) $monto;
        }

        return null;
    }

    /**
     * Procesar factura individual
     */
    private function procesarFactura(array $datosFila, int $numeroFila, ImportJob $importJob): array
    {
        try {
            DB::beginTransaction();

            // Validar CUFE único
            if (Factura::existeCufe($datosFila['cufe'])) {
                DB::rollBack();
                return [
                    'exito' => false,
                    'tipo_error' => 'duplicado',
                    'mensaje' => "CUFE duplicado: {$datosFila['cufe']}",
                ];
            }

            // Obtener o crear tercero
            $tercero = Tercero::where('nit', $datosFila['nit_tercero'])->first();

            if (!$tercero) {
                $tercero = Tercero::create([
                    'nit' => $datosFila['nit_tercero'],
                    'nombre_razon_social' => $datosFila['nombre_tercero'],
                ]);
            }

            // Crear factura
            $factura = Factura::create([
                'cufe' => $datosFila['cufe'],
                'numero_factura' => $datosFila['numero_factura'],
                'tercero_id' => $tercero->id,
                'fecha_factura' => $datosFila['fecha_factura'],
                'tipo_documento' => $datosFila['tipo_documento'],
                'estado' => $datosFila['estado'],
                'subtotal' => $datosFila['total_pagar'],
                'total_pagar' => $datosFila['total_pagar'],
                'total_impuestos' => 0,
                'total_retenciones' => 0,
            ]);

            DB::commit();

            return [
                'exito' => true,
                'factura_id' => $factura->id,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error procesando factura {$datosFila['numero_factura']}: " . $e->getMessage());

            return [
                'exito' => false,
                'tipo_error' => 'validacion',
                'mensaje' => $e->getMessage(),
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
            'event_type' => 'import_excel_completed',
            'description' => "Importación Excel completada: {$importJob->registros_exitosos} exitosas, {$importJob->registros_error} con error",
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

