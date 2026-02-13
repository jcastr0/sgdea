<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportacionService;

class TestDebugImportacion extends Command
{
    protected $signature = 'debug:importacion
                            {archivo? : Ruta del archivo Excel (default: public/test.xlsx)}
                            {--headers : Solo mostrar headers}
                            {--preview : Mostrar preview de primeras 5 filas}
                            {--mapeo : Mostrar mapeo de columnas detectado}
                            {--procesar : Procesar el archivo completo}
                            {--fila= : Procesar solo una fila específica}
                            {--desde= : Procesar desde esta fila}
                            {--hasta= : Procesar hasta esta fila}
                            {--tenant=1 : ID del tenant}
                            {--dry-run : No guardar en BD, solo validar}';

    protected $description = 'Debug de importación de Excel usando ImportacionService';

    private ImportacionService $service;

    public function __construct(ImportacionService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $archivo = $this->argument('archivo') ?? public_path('test.xlsx');
        $tenantId = (int) $this->option('tenant');
        $dryRun = $this->option('dry-run');

        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("🔧 DEBUG IMPORTACIÓN (usando ImportacionService)");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("📁 Archivo: {$archivo}");
        $this->info("🏢 Tenant ID: {$tenantId}");
        if ($dryRun) {
            $this->warn("🔄 Modo DRY-RUN: No se guardará nada en BD");
        }
        $this->newLine();

        try {
            // Cargar Excel usando el servicio
            $this->info("📖 Cargando archivo Excel...");
            $datos = $this->service->cargarExcel($archivo);

            $this->info("✅ Excel cargado correctamente");
            $this->info("   Total de filas: " . $datos['total_filas']);
            $this->info("   Total de columnas: " . $datos['total_columnas']);
            $this->info("   Tamaño: " . $this->service->formatBytes($datos['file_size']));
            $this->newLine();

            $headers = $datos['headers'];
            $rows = $datos['rows'];

            // Mostrar headers
            if ($this->option('headers') || !$this->option('procesar')) {
                $this->mostrarHeaders($headers);
            }

            // Detectar y mostrar mapeo
            $this->info("🔍 Detectando columnas...");
            $mapping = $this->service->detectarColumnas($headers);

            // Mostrar log de debug del mapeo
            foreach ($this->service->getDebugLog() as $log) {
                $this->line("   → {$log}");
            }
            $this->newLine();

            if ($this->option('mapeo') || !$this->option('procesar')) {
                $this->mostrarMapeo($mapping);
            }

            // Validar campos críticos
            $faltantes = $this->service->validarCamposCriticos();
            if (count($faltantes) > 0) {
                $this->error("⚠️  CAMPOS CRÍTICOS FALTANTES: " . implode(', ', $faltantes));
            } else {
                $this->info("✅ Todos los campos críticos detectados");
            }
            $this->newLine();

            // Preview
            if ($this->option('preview') || !$this->option('procesar')) {
                $this->mostrarPreview($rows);
            }

            // Procesar fila específica
            if ($this->option('fila')) {
                $filaNum = (int) $this->option('fila');
                $this->procesarFilaDebug($rows, $filaNum, $tenantId, $dryRun);
            }

            // Procesar rango de filas
            if ($this->option('desde') || $this->option('hasta')) {
                $desde = (int) ($this->option('desde') ?? 1);
                $hasta = (int) ($this->option('hasta') ?? count($rows) - 1);
                $this->procesarRangoDebug($rows, $desde, $hasta, $tenantId, $dryRun);
            }

            // Procesar todo
            if ($this->option('procesar')) {
                $this->procesarTodo($rows, $tenantId, $dryRun);
            }

            $this->newLine();
            $this->info("═══════════════════════════════════════════════════════════");
            $this->info("✅ Debug completado");
            $this->info("═══════════════════════════════════════════════════════════");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("   Línea: " . $e->getLine());
            $this->error("   Archivo: " . $e->getFile());
            return 1;
        }

        return 0;
    }

    private function mostrarHeaders(array $headers)
    {
        $this->info("📋 HEADERS ENCONTRADOS:");
        $this->info("───────────────────────────────────────────────────────────");

        $tableData = [];
        foreach ($headers as $index => $header) {
            $tableData[] = [
                'Índice' => $index,
                'Col' => $this->service->indiceALetra($index),
                'Header' => $header ?? '(vacío)',
                'Normalizado' => $this->service->normalizar($header ?? ''),
            ];
        }

        $this->table(['Índice', 'Col', 'Header', 'Normalizado'], $tableData);
        $this->newLine();
    }

    private function mostrarMapeo(array $mapping)
    {
        $this->info("🗺️  MAPEO DE COLUMNAS:");
        $this->info("───────────────────────────────────────────────────────────");

        $camposYVariantes = $this->service->getCamposYVariantes();
        $tableData = [];

        foreach ($camposYVariantes as $campo => $variantes) {
            $indice = $mapping[$campo] ?? null;
            $estado = $indice !== null ? '✅' : '❌';
            $col = $indice !== null ? $this->service->indiceALetra($indice) : '-';

            $tableData[] = [
                'Campo' => $campo,
                'OK' => $estado,
                'Idx' => $indice ?? '-',
                'Col' => $col,
            ];
        }

        $this->table(['Campo', 'OK', 'Idx', 'Col'], $tableData);
        $this->newLine();
    }

    private function mostrarPreview(array $rows)
    {
        $this->info("👁️  PREVIEW (primeras 5 filas):");
        $this->info("───────────────────────────────────────────────────────────");

        $preview = $this->service->getPreview($rows, 5);

        if (empty($preview)) {
            $this->warn("   No hay filas de datos");
            return;
        }

        $mapping = $this->service->getColumnMapping();
        $columnasAMostrar = array_filter(
            ['numero_factura', 'nit', 'nombre_cliente', 'fecha_factura', 'total'],
            fn($c) => isset($mapping[$c])
        );

        $tableData = [];
        foreach ($preview as $item) {
            $fila = ['#' => $item['fila']];
            foreach ($columnasAMostrar as $campo) {
                $valor = $item['valores'][$campo] ?? '';
                $fila[$campo] = mb_substr((string)$valor, 0, 25);
            }
            $tableData[] = $fila;
        }

        $this->table(array_merge(['#'], $columnasAMostrar), $tableData);
        $this->newLine();

        // Mostrar mapeo de tipo_documento
        if (isset($mapping['tipo_documento'])) {
            $this->info("📄 MAPEO TIPO DOCUMENTO (primeras 10 filas):");
            $this->info("───────────────────────────────────────────────────────────");

            $previewTipoDoc = $this->service->getPreview($rows, 10);
            $tipoDocData = [];

            foreach ($previewTipoDoc as $item) {
                $tipoOriginal = $item['valores']['tipo_documento'] ?? '(vacío)';
                $tipoMapeado = $this->service->mapearTipoDocumento($tipoOriginal);
                $tipoDocData[] = [
                    '#' => $item['fila'],
                    'Original' => mb_substr((string)$tipoOriginal, 0, 25),
                    'Mapeado' => $tipoMapeado,
                ];
            }

            $this->table(['#', 'Original', 'Mapeado'], $tipoDocData);
            $this->newLine();
        }
    }

    private function procesarFilaDebug(array $rows, int $filaNum, int $tenantId, bool $dryRun)
    {
        $this->info("🔍 FILA #{$filaNum}:");
        $this->info("───────────────────────────────────────────────────────────");

        if (!isset($rows[$filaNum])) {
            $this->error("   La fila {$filaNum} no existe");
            return;
        }

        $row = $rows[$filaNum];
        $valores = $this->service->extraerValoresFila($row);

        $this->info("📝 Valores:");
        foreach ($valores as $campo => $valor) {
            $this->line("   {$campo}: " . ($valor ?? '(vacío)'));
        }
        $this->newLine();

        $validacion = $this->service->validarFila($row);

        $this->info("✔️  Validación:");
        if ($validacion['valid']) {
            $this->info("   ✅ Fila válida");
        } else {
            foreach ($validacion['errores'] as $error) {
                $this->error("   ❌ {$error}");
            }
        }

        if (!$dryRun && $validacion['valid']) {
            $resultado = $this->service->procesarFila($row, $tenantId);
            $this->newLine();
            $this->info("💾 Resultado: {$resultado['status']}");
            if ($resultado['status'] === 'new') {
                $this->info("   Factura ID: {$resultado['factura_id']}");
                $this->info("   Tercero ID: {$resultado['tercero_id']}");
            } elseif ($resultado['status'] === 'duplicate') {
                $this->warn("   Factura duplicada ID: {$resultado['factura_id']}");
            } elseif ($resultado['status'] === 'error') {
                $this->error("   Errores: " . implode(', ', $resultado['errores'] ?? ['Desconocido']));
            }
        }
        $this->newLine();
    }

    private function procesarTodo(array $rows, int $tenantId, bool $dryRun)
    {
        $this->info("🚀 PROCESANDO TODO:");
        $this->info("───────────────────────────────────────────────────────────");

        $total = count($rows) - 1;
        $exitosos = 0;
        $errores = 0;
        $duplicados = 0;

        $this->output->progressStart($total);

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if ($dryRun) {
                $validacion = $this->service->validarFila($row);
                $validacion['valid'] ? $exitosos++ : $errores++;
            } else {
                $resultado = $this->service->procesarFila($row, $tenantId);
                match($resultado['status']) {
                    'new' => $exitosos++,
                    'duplicate' => $duplicados++,
                    default => $errores++,
                };
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->newLine();

        $this->info("📊 RESUMEN:");
        $this->line("   Total: {$total}");
        $this->info("   ✅ Exitosos: {$exitosos}");
        $this->warn("   ⊕ Duplicados: {$duplicados}");
        $this->error("   ❌ Errores: {$errores}");
        $this->newLine();
    }

    private function procesarRangoDebug(array $rows, int $desde, int $hasta, int $tenantId, bool $dryRun)
    {
        $this->info("🔍 ANALIZANDO FILAS {$desde} a {$hasta}:");
        $this->info("═══════════════════════════════════════════════════════════");

        $mapping = $this->service->getColumnMapping();

        for ($i = $desde; $i <= $hasta; $i++) {
            if (!isset($rows[$i])) {
                $this->warn("   La fila {$i} no existe");
                continue;
            }

            $row = $rows[$i];
            $this->newLine();
            $this->info("───────────────────────────────────────────────────────────");
            $this->info("📄 FILA #{$i}:");
            $this->info("───────────────────────────────────────────────────────────");

            // Mostrar datos crudos relevantes
            $this->warn("🔢 DATOS CRUDOS DE LA FILA:");
            foreach ($row as $idx => $valor) {
                $col = $this->service->indiceALetra($idx);
                $this->line("   [{$idx}] Col {$col}: " . json_encode($valor, JSON_UNESCAPED_UNICODE));
            }
            $this->newLine();

            // Mostrar específicamente tipo_documento
            if (isset($mapping['tipo_documento'])) {
                $idxTipo = $mapping['tipo_documento'];
                $valorTipo = $row[$idxTipo] ?? null;
                $tipoMapeado = $this->service->mapearTipoDocumento($valorTipo);

                $this->info("📋 ANÁLISIS TIPO DOCUMENTO:");
                $this->line("   Índice columna tipo: {$idxTipo}");
                $this->line("   Valor crudo: " . json_encode($valorTipo, JSON_UNESCAPED_UNICODE));
                $this->line("   Tipo PHP: " . gettype($valorTipo));
                $this->line("   Valor normalizado: " . $this->service->normalizar((string)$valorTipo));
                $this->line("   Resultado mapeado: {$tipoMapeado}");
            } else {
                $this->error("   ⚠️ Columna tipo_documento NO mapeada");
            }
            $this->newLine();

            // Mostrar valores extraídos
            $valores = $this->service->extraerValoresFila($row);
            $this->info("📝 VALORES EXTRAÍDOS:");
            foreach ($valores as $campo => $valor) {
                $valorStr = is_array($valor) ? json_encode($valor) : ($valor ?? '(vacío)');
                $this->line("   {$campo}: {$valorStr}");
            }
            $this->newLine();

            // Validación
            $validacion = $this->service->validarFila($row);
            $this->info("✔️  VALIDACIÓN:");
            if ($validacion['valid']) {
                $this->info("   ✅ Fila válida");
            } else {
                foreach ($validacion['errores'] as $error) {
                    $this->error("   ❌ {$error}");
                }
            }
        }

        $this->newLine();
    }
}
