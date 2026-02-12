<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ImportConfiguration;
use App\Models\ImportLog;
use App\Models\ImportRecord;
use App\Models\Tercero;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportacionService
{
    /**
     * Mapeo de columnas detectado
     */
    private $columnMapping = [];

    /**
     * Índices ya usados (para evitar duplicados)
     */
    private $usedIndices = [];

    /**
     * Log de debug (para el comando)
     */
    private $debugLog = [];

    /**
     * Campos y sus variantes para detección automática
     */
    private $camposYVariantes = [
        // Número de factura - "Documento" es el header del archivo
        'numero_factura' => ['numero_factura', 'factura', 'nro_factura', 'no_factura', 'consecutivo', 'no.', 'nro', 'fact', 'documento'],

        // NIT/Identificación - "Identificación" es el header del archivo
        'nit' => ['nit', 'nit_cliente', 'documento_cliente', 'id_cliente', 'cc', 'cedula', 'rut', 'identificacion'],

        // Nombre cliente - "Comprador" es el header del archivo
        'nombre_cliente' => ['nombre_cliente', 'cliente', 'razon_social', 'comprador', 'nombre', 'empresa', 'tercero', 'proveedor', 'receptor', 'adquiriente'],

        // Fechas - "Fecha de Emision" es el header del archivo
        'fecha_factura' => ['fecha_factura', 'fecha_emision', 'fecha_de_emision', 'fecha_documento', 'fec_documento', 'fec_emision'],
        'fecha_vencimiento' => ['fecha_vencimiento', 'vencimiento', 'fecha_pago', 'fec_vencimiento'],

        // Valores - "Valor total" es el header del archivo
        'subtotal' => ['subtotal', 'base', 'valor_base', 'base_gravable', 'vlr_base', 'sub_total'],
        'iva' => ['iva', 'impuesto', 'valor_iva', 'vlr_iva', 'iva_19', 'tax'],
        'total' => ['total', 'valor_total', 'vlr_total', 'gran_total', 'neto', 'amount'],
        'descuento' => ['descuento', 'dcto', 'desc', 'discount'],

        // Otros
        'descripcion' => ['descripcion', 'concepto', 'detalle', 'observacion', 'obs', 'nota'],
        'cufe' => ['cufe', 'codigo_unico', 'uuid', 'cude'],
        'direccion' => ['direccion', 'dir', 'address', 'domicilio'],
        'telefono' => ['telefono', 'tel', 'phone', 'celular', 'movil'],
        'email' => ['email', 'correo', 'e-mail', 'mail'],
        'prefijo' => ['prefijo', 'serie'],
        'moneda' => ['moneda', 'currency', 'divisa'],
        'forma_pago' => ['forma_de_pago', 'forma_pago', 'metodo_pago', 'payment'],

        // Campos adicionales del archivo DIAN
        'tipo_documento' => ['tipo_documento', 'tipo_doc', 'tipodocumento'],
        'estado_comercial' => ['estado_comercial', 'estadocomercial', 'estado_dian'],
        'tipo_identificacion' => ['tipo_de_identificacion', 'tipo_identificacion', 'tipo_id'],
    ];

    /**
     * Estados comerciales válidos y sus variantes
     */
    private $estadosComerciales = [
        'aceptado' => ['aceptado', 'aceptada', 'accepted', 'aprobado', 'aprobada', 'si', 'yes', '1'],
        'rechazado' => ['rechazado', 'rechazada', 'rejected', 'negado', 'negada', 'no', '0'],
        'pendiente' => ['pendiente', 'pending', 'en_espera', 'por_revisar'],
    ];

    /**
     * Campos críticos (obligatorios)
     */
    private $camposCriticos = ['numero_factura', 'nit', 'nombre_cliente'];

    /**
     * Cargar archivo Excel y retornar datos
     */
    public function cargarExcel(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("El archivo no existe: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Usar toArray con parámetros para obtener valores correctos:
        // - null: valor por defecto para celdas vacías
        // - true: calcular fórmulas
        // - false: NO formatear valores (devuelve valor raw, evita conversión errónea de números a fechas)
        $rows = $sheet->toArray(null, true, false, false);

        return [
            'rows' => $rows,
            'total_filas' => count($rows),
            'total_columnas' => count($rows) > 0 ? count($rows[0]) : 0,
            'headers' => $rows[0] ?? [],
            'file_size' => filesize($filePath),
        ];
    }

    /**
     * Detectar columnas automáticamente basado en headers
     */
    public function detectarColumnas(array $headers): array
    {
        $this->columnMapping = [];
        $this->usedIndices = [];
        $this->debugLog = [];

        // Primera pasada: buscar coincidencias exactas
        foreach ($this->camposYVariantes as $campo => $variantes) {
            foreach ($headers as $index => $header) {
                if (in_array($index, $this->usedIndices)) continue;

                $headerNorm = $this->normalizar($header ?? '');

                foreach ($variantes as $variante) {
                    if ($headerNorm === $variante) {
                        $this->columnMapping[$campo] = $index;
                        $this->usedIndices[] = $index;
                        $this->debugLog[] = "{$campo} = columna {$index} ('{$header}') [exacto]";
                        break 2;
                    }
                }
            }
        }

        // Segunda pasada: coincidencias parciales (solo para campos no mapeados)
        foreach ($this->camposYVariantes as $campo => $variantes) {
            if (isset($this->columnMapping[$campo])) continue;

            foreach ($headers as $index => $header) {
                if (in_array($index, $this->usedIndices)) continue;

                $headerNorm = $this->normalizar($header ?? '');

                foreach ($variantes as $variante) {
                    if (strpos($headerNorm, $variante) !== false) {
                        $this->columnMapping[$campo] = $index;
                        $this->usedIndices[] = $index;
                        $this->debugLog[] = "{$campo} = columna {$index} ('{$header}') [parcial: {$variante}]";
                        break 2;
                    }
                }
            }
        }

        return $this->columnMapping;
    }

    /**
     * Obtener el mapeo actual
     */
    public function getColumnMapping(): array
    {
        return $this->columnMapping;
    }

    /**
     * Obtener log de debug del mapeo
     */
    public function getDebugLog(): array
    {
        return $this->debugLog;
    }

    /**
     * Verificar si todos los campos críticos están mapeados
     */
    public function validarCamposCriticos(): array
    {
        $faltantes = [];
        foreach ($this->camposCriticos as $campo) {
            if (!isset($this->columnMapping[$campo])) {
                $faltantes[] = $campo;
            }
        }
        return $faltantes;
    }

    /**
     * Campos que son fechas (para formateo automático)
     */
    private $camposFecha = ['fecha_factura', 'fecha_vencimiento'];

    /**
     * Extraer valores de una fila según el mapeo
     */
    public function extraerValoresFila(array $row, bool $formatearFechas = true): array
    {
        $valores = [];
        foreach ($this->columnMapping as $campo => $indice) {
            $valor = $row[$indice] ?? null;
            $valor = is_string($valor) ? trim($valor) : $valor;

            // Formatear fechas si está habilitado
            if ($formatearFechas && in_array($campo, $this->camposFecha) && !empty($valor)) {
                $fechaParsed = $this->parsearFecha($valor);
                if ($fechaParsed) {
                    $valor = $fechaParsed->format('Y-m-d');
                }
            }

            $valores[$campo] = $valor;
        }
        return $valores;
    }

    /**
     * Validar una fila de datos
     */
    public function validarFila(array $row): array
    {
        $valores = $this->extraerValoresFila($row);
        $errores = [];
        $warnings = [];

        // Validar campos críticos
        if (empty($valores['numero_factura'])) {
            $errores[] = 'numero_factura vacío';
        }

        if (empty($valores['nit'])) {
            $errores[] = 'nit vacío';
        } else {
            $nitLimpio = $this->limpiarNit($valores['nit']);
            if (strlen($nitLimpio) < 6 || strlen($nitLimpio) > 15) {
                $errores[] = "nit inválido ({$nitLimpio} - " . strlen($nitLimpio) . " dígitos, debe tener 6-15)";
            }
        }

        if (empty($valores['nombre_cliente'])) {
            $errores[] = 'nombre_cliente vacío';
        }

        // Validar fecha
        if (isset($valores['fecha_factura'])) {
            $fechaParsed = $this->parsearFecha($valores['fecha_factura']);
            if (!$fechaParsed) {
                $warnings[] = 'fecha_factura no se pudo parsear';
            }
        }

        // Validar total
        if (isset($valores['total'])) {
            $totalLimpio = $this->limpiarNumero($valores['total']);
            if ($totalLimpio < 0) {
                $errores[] = 'total no puede ser negativo';
            }
        }

        return [
            'valid' => count($errores) === 0,
            'errores' => $errores,
            'warnings' => $warnings,
            'valores' => $valores,
        ];
    }

    /**
     * Procesar una fila e importarla a la base de datos
     */
    public function procesarFila(array $row, int $tenantId, bool $createTerceros = true): array
    {
        $validacion = $this->validarFila($row);

        if (!$validacion['valid']) {
            return [
                'status' => 'error',
                'errores' => $validacion['errores'],
                'valores' => $validacion['valores'],
            ];
        }

        $valores = $validacion['valores'];

        try {
            DB::beginTransaction();

            // Limpiar valores
            $nit = $this->limpiarNit($valores['nit']);
            $fechaFactura = $this->parsearFecha($valores['fecha_factura'] ?? null);
            $total = $this->limpiarNumero($valores['total'] ?? 0);
            $subtotal = $this->limpiarNumero($valores['subtotal'] ?? $total);
            $iva = $this->limpiarNumero($valores['iva'] ?? 0);
            $descuento = $this->limpiarNumero($valores['descuento'] ?? 0);

            // Buscar o crear tercero
            $tercero = Tercero::where('tenant_id', $tenantId)
                ->where('nit', $nit)
                ->first();

            if (!$tercero && $createTerceros) {
                $tercero = Tercero::create([
                    'tenant_id' => $tenantId,
                    'nit' => $nit,
                    'nombre_razon_social' => $valores['nombre_cliente'],
                    'direccion' => $valores['direccion'] ?? null,
                    'telefono' => $valores['telefono'] ?? null,
                    'email' => $valores['email'] ?? null,
                    'estado' => 'activo',
                ]);
            }

            if (!$tercero) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'errores' => ['No se encontró tercero y no se permite crear nuevos'],
                    'valores' => $valores,
                ];
            }

            // Generar o usar CUFE existente
            $cufe = $valores['cufe'] ?? null;
            if (empty($cufe) && $fechaFactura) {
                $cufe = $this->generarCufe($valores['numero_factura'], $nit, $fechaFactura);
            }

            // Verificar duplicados por CUFE
            if ($cufe) {
                $facturaExistente = Factura::where('tenant_id', $tenantId)
                    ->where('cufe', $cufe)
                    ->first();

                if ($facturaExistente) {
                    DB::rollBack();
                    return [
                        'status' => 'duplicate',
                        'factura_id' => $facturaExistente->id,
                        'tercero_id' => $tercero->id,
                        'cufe' => $cufe,
                        'valores' => $valores,
                    ];
                }
            }

            // Crear factura
            // Mapear estado comercial del archivo (Aceptado, Rechazado, vacío -> pendiente)
            $estadoComercial = $this->mapearEstadoComercial($valores['estado_comercial'] ?? null);

            $factura = Factura::create([
                'tenant_id' => $tenantId,
                'tercero_id' => $tercero->id,
                'numero_factura' => $valores['numero_factura'],
                'fecha_factura' => $fechaFactura,
                'fecha_vencimiento' => $this->parsearFecha($valores['fecha_vencimiento'] ?? null),
                'subtotal' => $subtotal,
                'iva' => $iva,
                'descuento' => $descuento,
                'total_pagar' => $total,
                'cufe' => $cufe,
                'estado' => $estadoComercial,
            ]);

            DB::commit();

            return [
                'status' => 'new',
                'factura_id' => $factura->id,
                'tercero_id' => $tercero->id,
                'cufe' => $cufe,
                'valores' => $valores,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error procesando fila de importación: " . $e->getMessage());

            return [
                'status' => 'error',
                'errores' => [$e->getMessage()],
                'valores' => $valores,
            ];
        }
    }

    /**
     * Obtener preview de las primeras N filas
     */
    public function getPreview(array $rows, int $limit = 5): array
    {
        $preview = [];
        $dataRows = array_slice($rows, 1, $limit); // Saltar header

        foreach ($dataRows as $i => $row) {
            $valores = $this->extraerValoresFila($row);
            $preview[] = [
                'fila' => $i + 2, // +2 porque fila 1 es header, y array es 0-based
                'valores' => $valores,
            ];
        }

        return $preview;
    }

    /**
     * Normalizar string para comparación
     */
    public function normalizar(string $str): string
    {
        $str = mb_strtolower($str);
        $str = preg_replace('/[áàäâ]/u', 'a', $str);
        $str = preg_replace('/[éèëê]/u', 'e', $str);
        $str = preg_replace('/[íìïî]/u', 'i', $str);
        $str = preg_replace('/[óòöô]/u', 'o', $str);
        $str = preg_replace('/[úùüû]/u', 'u', $str);
        $str = preg_replace('/[ñ]/u', 'n', $str);
        $str = preg_replace('/[^a-z0-9]/u', '_', $str);
        $str = preg_replace('/_+/', '_', $str);
        $str = trim($str, '_');
        return $str;
    }

    /**
     * Mapear estado comercial del archivo a estado válido
     *
     * @param string|null $estadoComercial El valor del campo Estado Comercial del archivo
     * @return string Estado mapeado: 'aceptado', 'rechazado' o 'pendiente'
     */
    public function mapearEstadoComercial(?string $estadoComercial): string
    {
        // Si está vacío o nulo, retornar pendiente
        if (empty($estadoComercial) || trim($estadoComercial) === '') {
            return 'pendiente';
        }

        $estadoNorm = $this->normalizar($estadoComercial);

        // Buscar coincidencia en los estados definidos
        foreach ($this->estadosComerciales as $estadoFinal => $variantes) {
            foreach ($variantes as $variante) {
                if ($estadoNorm === $variante || strpos($estadoNorm, $variante) !== false) {
                    return $estadoFinal;
                }
            }
        }

        // Si no se encuentra coincidencia, retornar pendiente
        return 'pendiente';
    }

    /**
     * Parsear fecha en múltiples formatos
     */
    public function parsearFecha($fecha): ?\DateTime
    {
        if (empty($fecha)) return null;

        // Si es número Excel (días desde 1900-01-01)
        if (is_numeric($fecha)) {
            $unixTime = ($fecha - 25569) * 86400;
            return (new \DateTime())->setTimestamp((int)$unixTime);
        }

        // Intentar varios formatos
        $formatos = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd/m/Y H:i:s',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'Y/m/d',
            'd.m.Y',
        ];

        foreach ($formatos as $formato) {
            $parsed = \DateTime::createFromFormat($formato, $fecha);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        // Fallback strtotime
        $timestamp = strtotime($fecha);
        if ($timestamp !== false) {
            return (new \DateTime())->setTimestamp($timestamp);
        }

        return null;
    }

    /**
     * Generar CUFE determinístico
     */
    public function generarCufe(string $numeroFactura, string $nit, \DateTime $fecha): string
    {
        $data = $numeroFactura . '|' . $nit . '|' . $fecha->format('Y-m-d');
        return strtoupper(substr(hash('sha256', $data), 0, 64));
    }

    /**
     * Limpiar NIT (solo números)
     */
    public function limpiarNit(string $nit): string
    {
        return preg_replace('/[^0-9]/', '', $nit);
    }

    /**
     * Limpiar número (remover símbolos de moneda, comas, etc)
     */
    public function limpiarNumero($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        if (is_string($valor)) {
            // Remover símbolos de moneda y espacios
            $limpio = preg_replace('/[^0-9.,\-]/', '', $valor);
            // Manejar formato con comas como separador de miles
            if (preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $limpio)) {
                $limpio = str_replace(',', '', $limpio);
            }
            // Manejar formato europeo (punto como miles, coma como decimal)
            elseif (preg_match('/^\d{1,3}(\.\d{3})*(,\d+)?$/', $limpio)) {
                $limpio = str_replace('.', '', $limpio);
                $limpio = str_replace(',', '.', $limpio);
            }
            return (float) $limpio;
        }

        return 0.0;
    }

    /**
     * Convertir índice a letra de columna Excel
     */
    public function indiceALetra(int $indice): string
    {
        $letra = '';
        while ($indice >= 0) {
            $letra = chr(65 + ($indice % 26)) . $letra;
            $indice = intval($indice / 26) - 1;
        }
        return $letra;
    }

    /**
     * Formatear bytes a unidad legible
     */
    public function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener campos y variantes (para debug/documentación)
     */
    public function getCamposYVariantes(): array
    {
        return $this->camposYVariantes;
    }

    /**
     * Obtener campos críticos
     */
    public function getCamposCriticos(): array
    {
        return $this->camposCriticos;
    }
}
