<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFCufeExtractorService;

class TestDebugPDFImportacion extends Command
{
    protected $signature = 'debug:pdf
                            {archivo? : Ruta del archivo PDF (default: docs/desarrollo/900282616_20250827024209.pdf)}
                            {--info : Solo mostrar información del PDF}
                            {--texto : Mostrar texto extraído de cada página}
                            {--cufes : Mostrar CUFEs encontrados}
                            {--datos : Extraer datos adicionales del PDF (motonave, TRB, servicio, etc.)}
                            {--comparar : Comparar CUFEs con BD}
                            {--procesar : Procesar y asociar PDFs a facturas}
                            {--pagina= : Analizar solo una página específica}
                            {--tenant=1 : ID del tenant}
                            {--dry-run : No guardar en BD, solo mostrar qué haría}
                            {--verbose-text : Mostrar texto completo (no truncado)}
                            {--staging : Procesar archivos en staging}';

    protected $description = 'Debug de importación de PDF y extracción de CUFEs';

    private PDFCufeExtractorService $pdfService;

    public function __construct(PDFCufeExtractorService $pdfService)
    {
        parent::__construct();
        $this->pdfService = $pdfService;
    }

    public function handle()
    {
        $archivo = $this->argument('archivo') ?? base_path('docs/desarrollo/900282616_20250827024209.pdf');
        $tenantId = (int) $this->option('tenant');
        $dryRun = $this->option('dry-run');

        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("🔧 DEBUG PDF IMPORTACIÓN");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("📁 Archivo: {$archivo}");
        $this->info("🏢 Tenant ID: {$tenantId}");
        if ($dryRun) {
            $this->warn("🔄 Modo DRY-RUN: No se guardará nada en BD");
        }
        $this->newLine();

        // Verificar archivo
        if (!file_exists($archivo)) {
            $this->error("❌ El archivo no existe: {$archivo}");
            return 1;
        }

        try {
            // Procesar staging si se especifica
            if ($this->option('staging')) {
                $this->procesarStaging($tenantId, $dryRun);
                return 0;
            }

            // Cargar PDF usando el servicio
            $this->info("📖 Cargando PDF...");
            $pdfInfo = $this->pdfService->cargarPDF($archivo);
            $pages = $pdfInfo['pages'];
            $pdf = $pdfInfo['pdf'];

            $this->info("✅ PDF cargado correctamente");
            $this->info("   Total de páginas: {$pdfInfo['total_paginas']}");
            $this->info("   Tamaño: {$pdfInfo['tamaño_formateado']}");
            $this->newLine();

            // Mostrar info básica
            if ($this->option('info') || (!$this->option('procesar') && !$this->option('comparar'))) {
                $this->mostrarInfoPDF($pdf, $archivo);
            }

            // Extraer CUFEs usando el servicio
            $cufesEncontrados = $this->pdfService->extraerCUFEs($pages, $archivo);

            // Mostrar texto por página
            if ($this->option('texto')) {
                $paginaEspecifica = $this->option('pagina');
                $this->mostrarTextoPaginas($pages, $paginaEspecifica);
            }

            // Mostrar CUFEs encontrados
            if ($this->option('cufes') || (!$this->option('procesar') && !$this->option('comparar') && !$this->option('datos'))) {
                $this->mostrarCUFEs($cufesEncontrados);
            }

            // Extraer y mostrar datos adicionales del PDF
            if ($this->option('datos')) {
                $this->mostrarDatosExtraidos($archivo);
            }

            // Comparar con BD
            if ($this->option('comparar') || $this->option('procesar')) {
                $comparacion = $this->pdfService->compararConBD($cufesEncontrados, $tenantId);
                $this->mostrarComparacionBD($comparacion);
            }

            // Procesar
            if ($this->option('procesar')) {
                $resultado = $this->pdfService->procesarPDF($archivo, $cufesEncontrados, $tenantId, $dryRun);
                $this->mostrarResultadoProcesamiento($resultado, $dryRun);
            }

            $this->newLine();
            $this->info("═══════════════════════════════════════════════════════════");
            $this->info("✅ Debug PDF completado");
            $this->info("═══════════════════════════════════════════════════════════");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("   Línea: " . $e->getLine());
            $this->error("   Archivo: " . $e->getFile());
            return 1;
        }

        return 0;
    }

    private function procesarStaging(int $tenantId, bool $dryRun): void
    {
        $this->info("📦 PROCESANDO STAGING:");
        $this->info("───────────────────────────────────────────────────────────");

        $resultado = $this->pdfService->procesarStaging($tenantId, $dryRun);

        if (empty($resultado['procesados']) && empty($resultado['errores'])) {
            $this->warn("   No hay archivos en staging para procesar");
            return;
        }

        foreach ($resultado['procesados'] as $item) {
            if ($dryRun) {
                $this->info("   🔄 [DRY-RUN] Asociaría: {$item['numero_factura']}");
            } else {
                $this->info("   ✅ Asociado: {$item['numero_factura']} → {$item['pdf_path']}");
            }
        }

        foreach ($resultado['errores'] as $item) {
            $this->error("   ❌ Error en factura {$item['factura_id']}: {$item['error']}");
        }

        $this->newLine();
        $this->info("📊 RESULTADO:");
        $this->info("   ✅ Procesados: " . count($resultado['procesados']));
        $this->error("   ❌ Errores: " . count($resultado['errores']));
    }

    private function mostrarInfoPDF($pdf, $archivo)
    {
        $this->info("📋 INFORMACIÓN DEL PDF:");
        $this->info("───────────────────────────────────────────────────────────");

        $details = $pdf->getDetails();

        $tableData = [];
        foreach ($details as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $tableData[] = [
                'Propiedad' => $key,
                'Valor' => mb_substr((string)$value, 0, 60),
            ];
        }

        if (!empty($tableData)) {
            $this->table(['Propiedad', 'Valor'], $tableData);
        } else {
            $this->warn("   Sin metadata disponible");
        }

        $this->newLine();
    }


    private function mostrarTextoPaginas(array $pages, ?string $paginaEspecifica)
    {
        $this->info("📄 TEXTO EXTRAÍDO:");
        $this->info("───────────────────────────────────────────────────────────");

        $verbose = $this->option('verbose-text');
        $pageNum = 1;

        foreach ($pages as $page) {
            if ($paginaEspecifica !== null && (int)$paginaEspecifica !== $pageNum) {
                $pageNum++;
                continue;
            }

            $texto = $page->getText();
            $textoLimpio = preg_replace('/\s+/', ' ', $texto);

            $this->info("📃 Página {$pageNum}:");

            // Buscar CUFE en esta página
            $cufeEncontrado = null;
            if (preg_match('/[a-f0-9]{96}/i', $texto, $match)) {
                $cufeEncontrado = strtoupper($match[0]);
                $this->info("   🔑 CUFE: {$cufeEncontrado}");
            } else {
                $this->warn("   ⚠️  Sin CUFE en esta página");
            }

            // Mostrar texto
            if ($verbose) {
                $this->line("   Texto:");
                $this->line("   " . $textoLimpio);
            } else {
                $preview = mb_substr($textoLimpio, 0, 300);
                $this->line("   Preview: {$preview}...");
            }

            $this->newLine();
            $pageNum++;
        }
    }

    private function mostrarCUFEs(array $cufes)
    {
        $this->info("🔑 CUFEs ENCONTRADOS:");
        $this->info("───────────────────────────────────────────────────────────");

        if (empty($cufes)) {
            $this->warn("   ⚠️  No se encontraron CUFEs en el PDF");
            $this->newLine();
            return;
        }

        $this->info("   Total CUFEs únicos: " . count($cufes));
        $this->newLine();

        $tableData = [];
        foreach ($cufes as $index => $data) {
            $tableData[] = [
                '#' => $index + 1,
                'CUFE (primeros 20 chars)' => mb_substr($data['cufe'], 0, 20) . '...',
                'Páginas' => implode(', ', $data['paginas']),
                '# Págs' => count($data['paginas']),
            ];
        }

        $this->table(['#', 'CUFE (primeros 20 chars)', 'Páginas', '# Págs'], $tableData);
        $this->newLine();
    }

    private function mostrarDatosExtraidos(string $archivo): void
    {
        $this->info("📊 DATOS EXTRAÍDOS DEL PDF (para enriquecer factura):");
        $this->info("───────────────────────────────────────────────────────────");

        // Extraer texto completo del PDF
        $this->line("   📖 Extrayendo texto del PDF...");
        $texto = $this->pdfService->extraerTextoCompleto($archivo);

        if (!$texto) {
            $this->error("   ❌ No se pudo extraer texto del PDF");
            $this->newLine();
            return;
        }

        $this->info("   ✅ Texto extraído: " . strlen($texto) . " caracteres");
        $this->newLine();

        // Extraer datos estructurados
        $datos = $this->pdfService->extraerDatosFactura($texto);

        // Mostrar datos en tabla
        $tableData = [];

        $campos = [
            'motonave' => '🚢 Motonave',
            'trb' => '⚓ TRB',
            'locacion' => '📍 Locación',
            'servicio_descripcion' => '📝 Descripción Servicio',
            'periodo_servicio' => '📅 Período Servicio',
            'fecha_servicio' => '📆 Fecha Servicio',
        ];

        foreach ($campos as $key => $label) {
            $valor = $datos[$key] ?? null;
            $estado = $valor ? '✅' : '❌';

            // Truncar valores largos
            if ($valor && strlen($valor) > 80) {
                $valor = mb_substr($valor, 0, 80) . '...';
            }

            $tableData[] = [
                'Campo' => $label,
                'Estado' => $estado,
                'Valor' => $valor ?? '(no encontrado)',
            ];
        }

        $this->table(['Campo', 'Estado', 'Valor'], $tableData);

        // Mostrar items de descripción si los hay
        if (!empty($datos['descripcion_items'])) {
            $this->newLine();
            $this->info("📋 ITEMS DE DESCRIPCIÓN ENCONTRADOS:");
            foreach (array_slice($datos['descripcion_items'], 0, 10) as $i => $item) {
                $this->line("   " . ($i + 1) . ". " . mb_substr($item, 0, 100));
            }
            if (count($datos['descripcion_items']) > 10) {
                $this->warn("   ... y " . (count($datos['descripcion_items']) - 10) . " items más");
            }
        }

        $this->newLine();

        // Mostrar preview del texto si es verbose
        if ($this->option('verbose-text')) {
            $this->info("📄 TEXTO COMPLETO EXTRAÍDO:");
            $this->info("───────────────────────────────────────────────────────────");
            $this->line($texto);
            $this->newLine();
        } else {
            $this->info("💡 Use --verbose-text para ver el texto completo extraído");
        }

        $this->newLine();
    }

    private function mostrarComparacionBD(array $comparacion): void
    {
        $this->info("🔍 COMPARACIÓN CON BD:");
        $this->info("───────────────────────────────────────────────────────────");

        $totalCufes = count($comparacion['encontradas']) + count($comparacion['no_encontradas']);

        if ($totalCufes === 0) {
            $this->warn("   No hay CUFEs para comparar");
            $this->newLine();
            return;
        }

        $tableData = [];

        // Mostrar encontradas
        foreach ($comparacion['encontradas'] as $data) {
            $estado = $data['tiene_pdf'] ? '📎 Ya tiene PDF' : '✅ Disponible';
            $tableData[] = [
                'CUFE' => mb_substr($data['cufe'], 0, 16) . '...',
                'Estado' => $estado,
                'Factura #' => $data['numero_factura'] ?? '-',
                'ID' => $data['factura_id'],
                'PDF Path' => $data['tiene_pdf'] ? mb_substr($data['pdf_path'], 0, 20) : '-',
            ];
        }

        // Mostrar no encontradas
        foreach ($comparacion['no_encontradas'] as $data) {
            $tableData[] = [
                'CUFE' => mb_substr($data['cufe'], 0, 16) . '...',
                'Estado' => '❌ No en BD',
                'Factura #' => '-',
                'ID' => '-',
                'PDF Path' => '-',
            ];
        }

        $this->table(['CUFE', 'Estado', 'Factura #', 'ID', 'PDF Path'], $tableData);
        $this->newLine();

        $this->info("📊 RESUMEN:");
        $this->line("   Total CUFEs en PDF: " . $totalCufes);
        $this->info("   ✅ En BD (disponibles): " . count($comparacion['disponibles']));
        $this->warn("   📎 Ya con PDF asociado: " . count($comparacion['ya_asociadas']));
        $this->error("   ❌ No encontradas en BD: " . count($comparacion['no_encontradas']));
        $this->newLine();
    }

    private function mostrarResultadoProcesamiento(array $resultado, bool $dryRun): void
    {
        $this->info("🚀 RESULTADO DEL PROCESAMIENTO:");
        $this->info("───────────────────────────────────────────────────────────");

        if ($dryRun) {
            $this->warn("   📍 Los PDFs se guardarán en: storage/app/private/ (acceso autenticado)");
            $this->newLine();
        }

        // Mostrar asociados
        foreach ($resultado['asociados'] as $item) {
            if ($dryRun) {
                $this->info("   🔄 [DRY-RUN] Asociaría: {$item['numero_factura']} (ID: {$item['factura_id']})");
                $paginasStr = implode(', ', $item['paginas']);
                $numPaginas = count($item['paginas']);
                $tenantId = $item['tenant_id'] ?? 1;
                $this->line("      📄 Extraería {$numPaginas} página(s): [{$paginasStr}]");
                $this->line("      📁 Se guardaría en: storage/app/private/facturas/{$tenantId}/{$item['factura_id']}.pdf");

                if (!empty($item['datos_disponibles'])) {
                    $this->line("      📊 Datos disponibles para enriquecer:");
                    foreach (['motonave', 'trb', 'servicio_descripcion', 'locacion'] as $campo) {
                        if (!empty($item['datos_disponibles'][$campo])) {
                            $valor = mb_substr($item['datos_disponibles'][$campo], 0, 50);
                            $this->line("         • {$campo}: {$valor}");
                        }
                    }
                }
            } else {
                $this->info("   ✅ Asociado: {$item['numero_factura']}");
                $paginasStr = implode(', ', $item['paginas']);
                $this->line("      📄 Páginas extraídas: [{$paginasStr}]");
                $this->line("      📁 Guardado en: {$item['pdf_path']}");

                if (!empty($item['datos_enriquecidos'])) {
                    $this->line("      📊 Datos enriquecidos:");
                    foreach ($item['datos_enriquecidos'] as $campo => $info) {
                        $this->line("         • {$campo}: {$info['nuevo']}");
                    }
                }
            }
            $this->newLine();
        }

        // Mostrar staging
        foreach ($resultado['staging'] as $item) {
            $paginasStr = implode(', ', $item['paginas'] ?? []);
            if ($dryRun) {
                $this->warn("   🔄 [DRY-RUN] Guardaría en staging:");
                $this->line("      🔑 CUFE: " . mb_substr($item['cufe'], 0, 32) . '...');
                $this->line("      📄 Páginas a extraer: [{$paginasStr}]");
            } else {
                $this->warn("   📦 Staging: " . mb_substr($item['cufe'], 0, 20) . "...");
                $this->line("      📄 Páginas extraídas: [{$paginasStr}]");
                $this->line("      📁 Guardado en: {$item['staging_path']}");
            }
            $this->newLine();
        }

        // Mostrar omitidos
        foreach ($resultado['omitidos'] as $item) {
            $this->line("   ⏭️  Omitido: {$item['numero_factura']} - {$item['razon']}");
        }

        // Mostrar errores
        foreach ($resultado['errores'] as $item) {
            $this->error("   ❌ Error CUFE " . mb_substr($item['cufe'], 0, 16) . "...: {$item['error']}");
        }

        $this->newLine();
        $this->info("📊 TOTALES:");
        $this->info("   ✅ Asociados: " . count($resultado['asociados']));
        if (!empty($resultado['datos_enriquecidos'])) {
            $this->info("   📊 Facturas enriquecidas: " . count($resultado['datos_enriquecidos']));
        }
        $this->warn("   📦 Staging: " . count($resultado['staging']));
        $this->line("   ⏭️  Omitidos: " . count($resultado['omitidos']));
        $this->error("   ❌ Errores: " . count($resultado['errores']));

        $this->newLine();
        $this->info("📍 UBICACIÓN DE ARCHIVOS:");
        $this->line("   • Facturas: storage/app/private/facturas/{tenant_id}/");
        $this->line("   • Staging: storage/app/private/staging/{tenant_id}/");
        $this->line("   • Acceso: Solo mediante autenticación (no público)");
        $this->newLine();
    }

}
