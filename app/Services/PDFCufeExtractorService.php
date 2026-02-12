<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use App\Models\Factura;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PDFCufeExtractorService
{
    /**
     * Parser de PDF
     */
    private Parser $parser;

    /**
     * Log de operaciones para debug
     */
    private array $debugLog = [];

    /**
     * Estadísticas de procesamiento
     */
    private array $stats = [
        'total_cufes' => 0,
        'asociados' => 0,
        'staging' => 0,
        'omitidos' => 0,
        'errores' => 0,
        'no_encontrados' => 0,
    ];

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Obtener log de debug
     */
    public function getDebugLog(): array
    {
        return $this->debugLog;
    }

    /**
     * Obtener estadísticas
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Resetear estadísticas y log
     */
    public function reset(): void
    {
        $this->debugLog = [];
        $this->stats = [
            'total_cufes' => 0,
            'asociados' => 0,
            'staging' => 0,
            'omitidos' => 0,
            'errores' => 0,
            'no_encontrados' => 0,
        ];
    }

    /**
     * Cargar y parsear un archivo PDF
     *
     * @param string $filePath Ruta absoluta al archivo PDF
     * @return array Información del PDF cargado
     * @throws \Exception Si el archivo no existe o no se puede leer
     */
    public function cargarPDF(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("El archivo PDF no existe: {$filePath}");
        }

        $pdf = $this->parser->parseFile($filePath);
        $pages = $pdf->getPages();

        $info = [
            'path' => $filePath,
            'nombre' => basename($filePath),
            'tamaño' => filesize($filePath),
            'tamaño_formateado' => $this->formatBytes(filesize($filePath)),
            'total_paginas' => count($pages),
            'metadata' => $pdf->getDetails(),
            'pdf' => $pdf,
            'pages' => $pages,
        ];

        $this->addLog('info', "PDF cargado: {$info['nombre']} ({$info['total_paginas']} páginas)");

        return $info;
    }

    /**
     * Extraer todos los CUFEs de un PDF
     *
     * @param array $pages Páginas del PDF
     * @param string|null $filePath Ruta del archivo PDF (para fallback con pdftotext)
     * @return array Lista de CUFEs encontrados con sus páginas
     */
    public function extraerCUFEs(array $pages, ?string $filePath = null): array
    {
        $cufes = [];
        $pageNum = 1;
        $textoTotal = '';

        foreach ($pages as $page) {
            $texto = $page->getText();
            $textoTotal .= $texto;

            // Buscar CUFE (96 caracteres hexadecimales)
            // El CUFE de la DIAN Colombia siempre tiene 96 caracteres
            if (preg_match_all('/[a-f0-9]{96}/i', $texto, $matches)) {
                foreach ($matches[0] as $cufe) {
                    $cufeUpper = strtoupper($cufe);
                    if (!isset($cufes[$cufeUpper])) {
                        $cufes[$cufeUpper] = [
                            'cufe' => $cufeUpper,
                            'paginas' => [],
                            'primera_pagina' => $pageNum,
                        ];
                    }
                    if (!in_array($pageNum, $cufes[$cufeUpper]['paginas'])) {
                        $cufes[$cufeUpper]['paginas'][] = $pageNum;
                    }
                }
            }

            $pageNum++;
        }

        // Si no se encontraron CUFEs con la librería PHP, intentar con pdftotext
        if (empty($cufes) && $filePath && file_exists($filePath)) {
            $this->addLog('warning', "No se encontraron CUFEs con smalot/pdfparser, intentando con pdftotext...");
            $cufes = $this->extraerCUFEsConPdftotext($filePath);
        }

        $resultado = array_values($cufes);
        $this->stats['total_cufes'] = count($resultado);
        $this->addLog('info', "CUFEs extraídos: " . count($resultado));

        return $resultado;
    }

    /**
     * Extraer CUFEs usando pdftotext (Poppler) - mejor para PDFs firmados
     * Extrae página por página para identificar en qué página está cada CUFE
     *
     * @param string $filePath Ruta del archivo PDF
     * @return array Lista de CUFEs encontrados con su página exacta
     */
    private function extraerCUFEsConPdftotext(string $filePath): array
    {
        $cufes = [];

        // Verificar si pdftotext está disponible
        $pdftotextPath = $this->findPdftotext();
        if (!$pdftotextPath) {
            $this->addLog('error', "pdftotext no está instalado. Instálalo con: apt-get install poppler-utils");
            return $cufes;
        }

        try {
            // Primero obtener el número total de páginas usando pdfinfo
            $totalPaginas = $this->obtenerNumeroPaginasPDF($filePath);

            if ($totalPaginas <= 0) {
                $this->addLog('warning', "No se pudo determinar el número de páginas, intentando extracción completa");
                return $this->extraerCUFEsConPdftotextCompleto($filePath, $pdftotextPath);
            }

            $this->addLog('info', "PDF tiene {$totalPaginas} páginas, extrayendo página por página...");

            // Extraer texto página por página
            for ($pagina = 1; $pagina <= $totalPaginas; $pagina++) {
                $tempFile = sys_get_temp_dir() . '/pdf_page_' . uniqid() . '.txt';

                // Usar -f (first page) y -l (last page) para extraer una sola página
                $command = sprintf(
                    '%s -layout -f %d -l %d %s %s 2>&1',
                    escapeshellarg($pdftotextPath),
                    $pagina,
                    $pagina,
                    escapeshellarg($filePath),
                    escapeshellarg($tempFile)
                );

                exec($command, $output, $returnCode);

                if ($returnCode !== 0 || !file_exists($tempFile)) {
                    // Intentar sin -layout
                    $command = sprintf(
                        '%s -f %d -l %d %s %s 2>&1',
                        escapeshellarg($pdftotextPath),
                        $pagina,
                        $pagina,
                        escapeshellarg($filePath),
                        escapeshellarg($tempFile)
                    );
                    exec($command, $output, $returnCode);
                }

                if (file_exists($tempFile)) {
                    $texto = file_get_contents($tempFile);
                    unlink($tempFile);

                    // Buscar CUFEs en esta página
                    if (preg_match_all('/[a-f0-9]{96}/i', $texto, $matches)) {
                        foreach ($matches[0] as $cufe) {
                            $cufeUpper = strtoupper($cufe);
                            if (!isset($cufes[$cufeUpper])) {
                                $cufes[$cufeUpper] = [
                                    'cufe' => $cufeUpper,
                                    'paginas' => [],
                                    'primera_pagina' => $pagina,
                                    'metodo_extraccion' => 'pdftotext_por_pagina',
                                ];
                            }
                            if (!in_array($pagina, $cufes[$cufeUpper]['paginas'])) {
                                $cufes[$cufeUpper]['paginas'][] = $pagina;
                            }
                        }
                        $this->addLog('info', "Página {$pagina}: " . count($matches[0]) . " CUFE(s) encontrado(s)");
                    }
                }
            }

            $this->addLog('success', "CUFEs encontrados con pdftotext (página por página): " . count($cufes));

        } catch (\Exception $e) {
            $this->addLog('error', "Error al usar pdftotext: " . $e->getMessage());
        }

        return $cufes;
    }

    /**
     * Obtener el número de páginas de un PDF usando pdfinfo
     *
     * @param string $filePath Ruta del archivo PDF
     * @return int Número de páginas o 0 si falla
     */
    private function obtenerNumeroPaginasPDF(string $filePath): int
    {
        $pdfinfo = $this->findPopplerTool('pdfinfo');

        if (!$pdfinfo) {
            // Intentar con pdftotext para contar saltos de página
            return 0;
        }

        $command = sprintf('%s %s 2>&1', escapeshellarg($pdfinfo), escapeshellarg($filePath));
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            foreach ($output as $line) {
                if (preg_match('/^Pages:\s*(\d+)/i', $line, $matches)) {
                    return (int)$matches[1];
                }
            }
        }

        return 0;
    }

    /**
     * Fallback: Extraer CUFEs del PDF completo (sin identificar página)
     *
     * @param string $filePath Ruta del archivo PDF
     * @param string $pdftotextPath Ruta al ejecutable pdftotext
     * @return array Lista de CUFEs encontrados
     */
    private function extraerCUFEsConPdftotextCompleto(string $filePath, string $pdftotextPath): array
    {
        $cufes = [];
        $tempFile = sys_get_temp_dir() . '/pdf_extract_' . uniqid() . '.txt';

        $command = sprintf(
            '%s -layout %s %s 2>&1',
            escapeshellarg($pdftotextPath),
            escapeshellarg($filePath),
            escapeshellarg($tempFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($tempFile)) {
            $command = sprintf(
                '%s %s %s 2>&1',
                escapeshellarg($pdftotextPath),
                escapeshellarg($filePath),
                escapeshellarg($tempFile)
            );
            exec($command, $output, $returnCode);
        }

        if (file_exists($tempFile)) {
            $texto = file_get_contents($tempFile);
            unlink($tempFile);

            $this->addLog('info', "Texto extraído con pdftotext (completo): " . strlen($texto) . " caracteres");

            if (preg_match_all('/[a-f0-9]{96}/i', $texto, $matches)) {
                foreach ($matches[0] as $cufe) {
                    $cufeUpper = strtoupper($cufe);
                    if (!isset($cufes[$cufeUpper])) {
                        $cufes[$cufeUpper] = [
                            'cufe' => $cufeUpper,
                            'paginas' => [1], // Sin página exacta
                            'primera_pagina' => 1,
                            'metodo_extraccion' => 'pdftotext_completo',
                        ];
                    }
                }
                $this->addLog('warning', "CUFEs encontrados sin página exacta: " . count($cufes));
            }
        }

        return $cufes;
    }

    /**
     * Buscar el ejecutable pdftotext
     *
     * @return string|null Ruta del ejecutable o null si no existe
     */
    private function findPdftotext(): ?string
    {
        // Rutas comunes en Linux/Docker
        $paths = [
            '/usr/bin/pdftotext',
            '/usr/local/bin/pdftotext',
            'pdftotext', // En PATH
        ];

        foreach ($paths as $path) {
            if ($path === 'pdftotext') {
                // Verificar si está en PATH
                exec('which pdftotext 2>/dev/null', $output, $returnCode);
                if ($returnCode === 0 && !empty($output)) {
                    return trim($output[0]);
                }
            } elseif (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Extraer texto de una página específica
     *
     * @param array $pages Páginas del PDF
     * @param int $numeroPagina Número de página (1-based)
     * @return string|null Texto de la página o null si no existe
     */
    public function extraerTextoPagina(array $pages, int $numeroPagina): ?string
    {
        $index = $numeroPagina - 1;
        if (!isset($pages[$index])) {
            return null;
        }

        return $pages[$index]->getText();
    }

    /**
     * Comparar CUFEs extraídos con la base de datos
     *
     * @param array $cufes Lista de CUFEs extraídos
     * @param int $tenantId ID del tenant
     * @return array Resultado de la comparación
     */
    public function compararConBD(array $cufes, int $tenantId): array
    {
        $resultado = [
            'encontradas' => [],
            'no_encontradas' => [],
            'ya_asociadas' => [],
            'disponibles' => [],
        ];

        foreach ($cufes as $data) {
            $cufe = $data['cufe'];

            $factura = Factura::where('tenant_id', $tenantId)
                ->where('cufe', $cufe)
                ->first();

            if ($factura) {
                $facturaData = [
                    'cufe' => $cufe,
                    'paginas' => $data['paginas'],
                    'factura_id' => $factura->id,
                    'numero_factura' => $factura->numero_factura,
                    'tiene_pdf' => !empty($factura->pdf_path),
                    'pdf_path' => $factura->pdf_path,
                ];

                $resultado['encontradas'][] = $facturaData;

                if (!empty($factura->pdf_path)) {
                    $resultado['ya_asociadas'][] = $facturaData;
                } else {
                    $resultado['disponibles'][] = $facturaData;
                }
            } else {
                $resultado['no_encontradas'][] = [
                    'cufe' => $cufe,
                    'paginas' => $data['paginas'],
                ];
            }
        }

        $this->addLog('info', sprintf(
            "Comparación BD: %d encontradas, %d disponibles, %d ya asociadas, %d no encontradas",
            count($resultado['encontradas']),
            count($resultado['disponibles']),
            count($resultado['ya_asociadas']),
            count($resultado['no_encontradas'])
        ));

        return $resultado;
    }

    /**
     * Procesar PDF y asociar a facturas
     *
     * @param string $archivoPath Ruta del archivo PDF
     * @param array $cufes Lista de CUFEs extraídos
     * @param int $tenantId ID del tenant
     * @param bool $dryRun Si es true, no guarda en BD
     * @param bool $guardarEnStaging Si guardar CUFEs no encontrados en staging
     * @param bool $enriquecerDatos Si true, extrae datos adicionales del PDF para enriquecer la factura
     * @return array Resultado del procesamiento
     */
    public function procesarPDF(
        string $archivoPath,
        array $cufes,
        int $tenantId,
        bool $dryRun = false,
        bool $guardarEnStaging = true,
        bool $enriquecerDatos = true
    ): array {
        $resultado = [
            'asociados' => [],
            'staging' => [],
            'omitidos' => [],
            'errores' => [],
            'datos_enriquecidos' => [],
        ];

        // Extraer texto completo del PDF una sola vez si vamos a enriquecer datos
        $textoPDF = null;
        $datosExtraidosPDF = null;
        if ($enriquecerDatos && !$dryRun) {
            $textoPDF = $this->extraerTextoCompleto($archivoPath);
            if ($textoPDF) {
                $datosExtraidosPDF = $this->extraerDatosFactura($textoPDF);
                $this->addLog('info', "Datos extraídos del PDF para enriquecimiento");
            }
        }

        foreach ($cufes as $data) {
            $cufe = $data['cufe'];
            $paginas = $data['paginas'];

            try {
                $factura = Factura::where('tenant_id', $tenantId)
                    ->where('cufe', $cufe)
                    ->first();

                if ($factura) {
                    // Factura existe en BD
                    if (!empty($factura->pdf_path)) {
                        // Ya tiene PDF asignado
                        $resultado['omitidos'][] = [
                            'cufe' => $cufe,
                            'factura_id' => $factura->id,
                            'numero_factura' => $factura->numero_factura,
                            'razon' => 'Ya tiene PDF asignado',
                            'pdf_actual' => $factura->pdf_path,
                        ];
                        $this->stats['omitidos']++;
                        continue;
                    }

                    // Asociar PDF a la factura
                    if (!$dryRun) {
                        $pdfPath = $this->guardarPDFParaFactura($archivoPath, $paginas, $tenantId, $factura->id);
                        $pdfHash = hash('sha256', $cufe . json_encode($paginas));

                        $factura->update([
                            'pdf_path' => $pdfPath,
                            'hash_pdf' => $pdfHash,
                        ]);

                        // Enriquecer factura con datos extraídos del PDF
                        $datosEnriquecidos = [];
                        if ($datosExtraidosPDF) {
                            $datosEnriquecidos = $this->enriquecerFactura($factura, $datosExtraidosPDF, false);
                            if (!empty($datosEnriquecidos)) {
                                $resultado['datos_enriquecidos'][$factura->id] = $datosEnriquecidos;
                            }
                        }

                        $resultado['asociados'][] = [
                            'cufe' => $cufe,
                            'factura_id' => $factura->id,
                            'numero_factura' => $factura->numero_factura,
                            'pdf_path' => $pdfPath,
                            'paginas' => $paginas,
                            'datos_enriquecidos' => $datosEnriquecidos,
                        ];
                    } else {
                        $resultado['asociados'][] = [
                            'cufe' => $cufe,
                            'factura_id' => $factura->id,
                            'numero_factura' => $factura->numero_factura,
                            'paginas' => $paginas,
                            'tenant_id' => $tenantId,
                            'dry_run' => true,
                            'datos_disponibles' => $datosExtraidosPDF,
                        ];
                    }

                    $this->stats['asociados']++;
                    $this->addLog('success', "Asociado: {$factura->numero_factura} (CUFE: " . substr($cufe, 0, 16) . "...)");

                } else {
                    // Factura no existe - guardar en staging si está habilitado
                    if ($guardarEnStaging) {
                        if (!$dryRun) {
                            $stagingPath = $this->guardarEnStaging($archivoPath, $paginas, $tenantId, $cufe);
                            $resultado['staging'][] = [
                                'cufe' => $cufe,
                                'paginas' => $paginas,
                                'staging_path' => $stagingPath,
                            ];
                        } else {
                            $resultado['staging'][] = [
                                'cufe' => $cufe,
                                'paginas' => $paginas,
                                'dry_run' => true,
                            ];
                        }
                        $this->stats['staging']++;
                    } else {
                        $this->stats['no_encontrados']++;
                    }

                    $this->addLog('warning', "No encontrado en BD: " . substr($cufe, 0, 16) . "...");
                }

            } catch (\Exception $e) {
                $resultado['errores'][] = [
                    'cufe' => $cufe,
                    'error' => $e->getMessage(),
                ];
                $this->stats['errores']++;
                $this->addLog('error', "Error procesando CUFE " . substr($cufe, 0, 16) . "...: " . $e->getMessage());

                Log::error("PDFCufeExtractor: Error procesando CUFE", [
                    'cufe' => $cufe,
                    'tenant_id' => $tenantId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->addLog('info', sprintf(
            "Procesamiento completado: %d asociados, %d staging, %d omitidos, %d errores",
            $this->stats['asociados'],
            $this->stats['staging'],
            $this->stats['omitidos'],
            $this->stats['errores']
        ));

        return $resultado;
    }

    /**
     * Guardar PDF asociado a una factura (solo páginas específicas)
     * Guarda en storage privado para acceso autenticado
     *
     * @param string $archivoOrigen Ruta del PDF original
     * @param array $paginas Páginas que contienen la factura
     * @param int $tenantId ID del tenant
     * @param int $facturaId ID de la factura
     * @return string Ruta relativa en storage privado
     */
    private function guardarPDFParaFactura(string $archivoOrigen, array $paginas, int $tenantId, int $facturaId): string
    {
        // Ruta en storage privado (no accesible públicamente)
        $ruta = "facturas/{$tenantId}/{$facturaId}.pdf";
        $rutaCompleta = storage_path("app/private/{$ruta}");

        // Crear directorio si no existe
        $dir = dirname($rutaCompleta);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Si hay páginas específicas, extraer solo esas
        if (!empty($paginas)) {
            $exito = $this->extraerPaginasPDF($archivoOrigen, $paginas, $rutaCompleta);
            if (!$exito) {
                // Fallback: copiar PDF completo si falla la extracción
                $this->addLog('warning', "No se pudieron extraer páginas específicas, copiando PDF completo");
                copy($archivoOrigen, $rutaCompleta);
            }
        } else {
            // Sin páginas específicas, copiar completo
            copy($archivoOrigen, $rutaCompleta);
        }

        $this->addLog('info', "PDF guardado en storage privado: {$ruta}");

        return $ruta;
    }

    /**
     * Extraer páginas específicas de un PDF usando poppler-utils
     *
     * @param string $archivoOrigen Ruta del PDF original
     * @param array $paginas Números de páginas a extraer (1-based)
     * @param string $archivoDestino Ruta donde guardar el nuevo PDF
     * @return bool True si se extrajo correctamente
     */
    private function extraerPaginasPDF(string $archivoOrigen, array $paginas, string $archivoDestino): bool
    {
        // Buscar pdfseparate y pdfunite (poppler-utils)
        $pdfseparate = $this->findPopplerTool('pdfseparate');
        $pdfunite = $this->findPopplerTool('pdfunite');

        if (!$pdfseparate || !$pdfunite) {
            $this->addLog('warning', "poppler-utils no está completo. Necesario: pdfseparate y pdfunite");
            return false;
        }

        $tempDir = sys_get_temp_dir() . '/pdf_extract_' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            // 1. Separar todas las páginas del PDF
            $command = sprintf(
                '%s %s %s 2>&1',
                escapeshellarg($pdfseparate),
                escapeshellarg($archivoOrigen),
                escapeshellarg($tempDir . '/page-%d.pdf')
            );
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->addLog('error', "Error al separar PDF: " . implode("\n", $output));
                $this->limpiarDirectorioTemp($tempDir);
                return false;
            }

            // 2. Seleccionar solo las páginas que necesitamos
            $paginasArchivos = [];
            sort($paginas); // Ordenar para mantener el orden correcto

            foreach ($paginas as $numPagina) {
                $archivoPagina = $tempDir . '/page-' . $numPagina . '.pdf';
                if (file_exists($archivoPagina)) {
                    $paginasArchivos[] = $archivoPagina;
                } else {
                    $this->addLog('warning', "Página {$numPagina} no encontrada en el PDF");
                }
            }

            if (empty($paginasArchivos)) {
                $this->addLog('error', "No se encontraron las páginas solicitadas");
                $this->limpiarDirectorioTemp($tempDir);
                return false;
            }

            // 3. Unir las páginas seleccionadas en un nuevo PDF
            if (count($paginasArchivos) === 1) {
                // Solo una página, copiar directamente
                copy($paginasArchivos[0], $archivoDestino);
            } else {
                // Múltiples páginas, unir con pdfunite
                $archivosEscaped = array_map('escapeshellarg', $paginasArchivos);
                $command = sprintf(
                    '%s %s %s 2>&1',
                    escapeshellarg($pdfunite),
                    implode(' ', $archivosEscaped),
                    escapeshellarg($archivoDestino)
                );
                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    $this->addLog('error', "Error al unir PDF: " . implode("\n", $output));
                    $this->limpiarDirectorioTemp($tempDir);
                    return false;
                }
            }

            $this->addLog('success', sprintf(
                "PDF extraído: %d página(s) de %s",
                count($paginasArchivos),
                implode(', ', $paginas)
            ));

            $this->limpiarDirectorioTemp($tempDir);
            return true;

        } catch (\Exception $e) {
            $this->addLog('error', "Error extrayendo páginas: " . $e->getMessage());
            $this->limpiarDirectorioTemp($tempDir);
            return false;
        }
    }

    /**
     * Buscar una herramienta de poppler-utils
     *
     * @param string $tool Nombre de la herramienta (pdfseparate, pdfunite, etc.)
     * @return string|null Ruta del ejecutable o null
     */
    private function findPopplerTool(string $tool): ?string
    {
        $paths = [
            "/usr/bin/{$tool}",
            "/usr/local/bin/{$tool}",
            $tool, // En PATH
        ];

        foreach ($paths as $path) {
            if ($path === $tool) {
                exec("which {$tool} 2>/dev/null", $output, $returnCode);
                if ($returnCode === 0 && !empty($output)) {
                    return trim($output[0]);
                }
            } elseif (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Limpiar directorio temporal
     *
     * @param string $dir Directorio a limpiar
     */
    private function limpiarDirectorioTemp(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $archivos = glob($dir . '/*');
        foreach ($archivos as $archivo) {
            if (is_file($archivo)) {
                unlink($archivo);
            }
        }
        rmdir($dir);
    }

    /**
     * Guardar PDF en staging para CUFEs sin factura asociada
     * Guarda en storage privado y extrae solo las páginas relevantes
     *
     * @param string $archivoOrigen Ruta del PDF original
     * @param array $paginas Páginas relevantes
     * @param int $tenantId ID del tenant
     * @param string $cufe CUFE del documento
     * @return string Ruta relativa en storage privado
     */
    private function guardarEnStaging(string $archivoOrigen, array $paginas, int $tenantId, string $cufe): string
    {
        // Guardar en storage privado
        $ruta = "staging/{$tenantId}/{$cufe}.pdf";
        $rutaCompleta = storage_path("app/private/{$ruta}");

        // Crear directorio si no existe
        $dir = dirname($rutaCompleta);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Si hay páginas específicas, extraer solo esas
        if (!empty($paginas)) {
            $exito = $this->extraerPaginasPDF($archivoOrigen, $paginas, $rutaCompleta);
            if (!$exito) {
                // Fallback: copiar PDF completo
                copy($archivoOrigen, $rutaCompleta);
            }
        } else {
            copy($archivoOrigen, $rutaCompleta);
        }

        $this->addLog('info', "PDF staging guardado en storage privado: {$ruta}");

        return $ruta;
    }

    /**
     * Buscar facturas pendientes de PDF que coincidan con CUFEs en staging
     *
     * @param int $tenantId ID del tenant
     * @return array Facturas encontradas con sus PDFs en staging
     */
    public function buscarFacturasEnStaging(int $tenantId): array
    {
        $stagingPath = storage_path("app/private/staging/{$tenantId}");

        if (!is_dir($stagingPath)) {
            return [];
        }

        $archivos = glob($stagingPath . '/*.pdf');
        $coincidencias = [];

        foreach ($archivos as $archivo) {
            $cufe = pathinfo($archivo, PATHINFO_FILENAME);

            $factura = Factura::where('tenant_id', $tenantId)
                ->where('cufe', $cufe)
                ->whereNull('pdf_path')
                ->first();

            if ($factura) {
                $coincidencias[] = [
                    'staging_path' => $archivo,
                    'cufe' => $cufe,
                    'factura_id' => $factura->id,
                    'numero_factura' => $factura->numero_factura,
                ];
            }
        }

        return $coincidencias;
    }

    /**
     * Procesar archivos en staging y asociar a facturas existentes
     *
     * @param int $tenantId ID del tenant
     * @param bool $dryRun Si es true, no modifica nada
     * @return array Resultado del procesamiento
     */
    public function procesarStaging(int $tenantId, bool $dryRun = false): array
    {
        $coincidencias = $this->buscarFacturasEnStaging($tenantId);
        $resultado = [
            'procesados' => [],
            'errores' => [],
        ];

        foreach ($coincidencias as $item) {
            try {
                if (!$dryRun) {
                    $pdfPath = $this->guardarPDFParaFactura(
                        $item['staging_path'],
                        [],
                        $tenantId,
                        $item['factura_id']
                    );

                    $factura = Factura::find($item['factura_id']);
                    $factura->update([
                        'pdf_path' => $pdfPath,
                        'hash_pdf' => hash('sha256', $item['cufe']),
                    ]);

                    // Eliminar archivo de staging
                    unlink($item['staging_path']);

                    $resultado['procesados'][] = [
                        'factura_id' => $item['factura_id'],
                        'numero_factura' => $item['numero_factura'],
                        'pdf_path' => $pdfPath,
                    ];
                } else {
                    $resultado['procesados'][] = [
                        'factura_id' => $item['factura_id'],
                        'numero_factura' => $item['numero_factura'],
                        'dry_run' => true,
                    ];
                }
            } catch (\Exception $e) {
                $resultado['errores'][] = [
                    'factura_id' => $item['factura_id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $resultado;
    }

    /**
     * Extraer datos adicionales del PDF para enriquecer la factura
     *
     * Extrae: motonave, TRB, locación, descripción de servicio, etc.
     *
     * @param string $texto Texto extraído del PDF
     * @return array Datos extraídos
     */
    public function extraerDatosFactura(string $texto): array
    {
        $datos = [
            'motonave' => null,
            'trb' => null,
            'locacion' => null,
            'servicio_descripcion' => null,
            'descripcion_items' => [],
            'periodo_servicio' => null,
            'fecha_servicio' => null,
        ];

        // Normalizar texto (eliminar múltiples espacios y saltos de línea)
        $textoNormalizado = preg_replace('/\s+/', ' ', $texto);
        $lineas = preg_split('/[\r\n]+/', $texto);

        // 1. Extraer MOTONAVE - Buscar patrones comunes
        $patronesMotonave = [
            '/M(?:OTO)?NAVE[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/BUQUE[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/EMBARCACI[OÓ]N[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/NAVE[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/VESSEL[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/M\/N[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/B\/M[:\s]+([A-Z0-9\s\-\.]+)/i',
        ];

        foreach ($patronesMotonave as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                $datos['motonave'] = trim(preg_replace('/\s+/', ' ', $matches[1]));
                // Limpiar si termina con palabras comunes que no son parte del nombre
                $datos['motonave'] = preg_replace('/\s+(TRB|LOCACION|SERVICIO|FECHA|PERIODO).*$/i', '', $datos['motonave']);
                $datos['motonave'] = trim($datos['motonave']);
                if (strlen($datos['motonave']) > 2) {
                    break;
                }
            }
        }

        // 2. Extraer TRB (Tonelaje de Registro Bruto)
        $patronesTRB = [
            '/TRB[:\s]+([0-9\.,]+)/i',
            '/TONELAJE[:\s]+([0-9\.,]+)/i',
            '/GT[:\s]+([0-9\.,]+)/i',
            '/GRT[:\s]+([0-9\.,]+)/i',
            '/REGISTRO\s+BRUTO[:\s]+([0-9\.,]+)/i',
        ];

        foreach ($patronesTRB as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                $datos['trb'] = trim($matches[1]);
                break;
            }
        }

        // 3. Extraer LOCACIÓN
        $patronesLocacion = [
            '/LOCACI[OÓ]N[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/MUELLE[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/PUERTO[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/TERMINAL[:\s]+([A-Z0-9\s\-\.]+)/i',
            '/UBICACI[OÓ]N[:\s]+([A-Z0-9\s\-\.]+)/i',
        ];

        foreach ($patronesLocacion as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                $datos['locacion'] = trim(preg_replace('/\s+/', ' ', $matches[1]));
                // Limpiar si termina con palabras comunes
                $datos['locacion'] = preg_replace('/\s+(FECHA|SERVICIO|MOTONAVE|TRB).*$/i', '', $datos['locacion']);
                $datos['locacion'] = trim($datos['locacion']);
                if (strlen($datos['locacion']) > 2) {
                    break;
                }
            }
        }

        // 4. Extraer DESCRIPCIÓN DE SERVICIO
        $patronesServicio = [
            '/DESCRIPCI[OÓ]N(?:\s+DEL?\s+SERVICIO)?[:\s]+(.+?)(?=CANTIDAD|VALOR|PRECIO|\$|IVA)/is',
            '/SERVICIO(?:\s+SUMINISTRADO)?[:\s]+(.+?)(?=CANTIDAD|VALOR|PRECIO|\$|IVA)/is',
            '/CONCEPTO[:\s]+(.+?)(?=CANTIDAD|VALOR|PRECIO|\$|IVA)/is',
            '/DETALLE[:\s]+(.+?)(?=CANTIDAD|VALOR|PRECIO|\$|IVA)/is',
        ];

        foreach ($patronesServicio as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                $descripcion = trim(preg_replace('/\s+/', ' ', $matches[1]));
                if (strlen($descripcion) > 10 && strlen($descripcion) < 1000) {
                    $datos['servicio_descripcion'] = $descripcion;
                    break;
                }
            }
        }

        // 5. Extraer items de descripción (líneas que parecen ser items de factura)
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            // Buscar líneas que parezcan items (contienen descripción y posiblemente precio)
            if (preg_match('/^(.+?)\s+[\$]?\s*([0-9\.,]+)$/', $linea, $matches)) {
                $item = trim($matches[1]);
                // Filtrar líneas muy cortas o que sean encabezados
                if (strlen($item) > 20 && !preg_match('/^(SUBTOTAL|IVA|TOTAL|DESCUENTO|FECHA|NIT|CUFE)/i', $item)) {
                    $datos['descripcion_items'][] = $item;
                }
            }
        }

        // 6. Extraer PERÍODO DE SERVICIO
        $patronesPeriodo = [
            '/PER[IÍ]ODO[:\s]+(.+?)(?=VALOR|PRECIO|\$|TOTAL)/is',
            '/FECHA\s+(?:DE\s+)?SERVICIO[:\s]+(.+?)(?=VALOR|PRECIO|\$|TOTAL)/is',
            '/DEL\s+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\s+AL\s+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i',
        ];

        foreach ($patronesPeriodo as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                if (isset($matches[2])) {
                    $datos['periodo_servicio'] = "Del {$matches[1]} al {$matches[2]}";
                } else {
                    $datos['periodo_servicio'] = trim(preg_replace('/\s+/', ' ', $matches[1]));
                }
                if (strlen($datos['periodo_servicio']) > 3) {
                    break;
                }
            }
        }

        // 7. Buscar fecha de servicio específica
        if (preg_match('/FECHA\s+(?:DE\s+)?SERVICIO[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $textoNormalizado, $matches)) {
            $datos['fecha_servicio'] = $matches[1];
        }

        // Si no encontramos descripción de servicio, usar los items
        if (empty($datos['servicio_descripcion']) && !empty($datos['descripcion_items'])) {
            $datos['servicio_descripcion'] = implode('; ', array_slice($datos['descripcion_items'], 0, 5));
        }

        $this->addLog('info', "Datos extraídos del PDF: motonave=" . ($datos['motonave'] ?? 'N/A') .
            ", trb=" . ($datos['trb'] ?? 'N/A') .
            ", locacion=" . ($datos['locacion'] ?? 'N/A'));

        return $datos;
    }

    /**
     * Extraer texto de páginas específicas del PDF usando pdftotext
     *
     * @param string $filePath Ruta del archivo PDF
     * @param array $paginas Números de páginas a extraer (1-based)
     * @return string|null Texto extraído o null si falla
     */
    public function extraerTextoPaginas(string $filePath, array $paginas): ?string
    {
        $pdftotextPath = $this->findPdftotext();

        if (!$pdftotextPath || empty($paginas)) {
            return null;
        }

        // Obtener la primera y última página del rango
        $primera = min($paginas);
        $ultima = max($paginas);

        $tempFile = sys_get_temp_dir() . '/pdf_text_pages_' . uniqid() . '.txt';
        $command = sprintf(
            '%s -f %d -l %d -layout %s %s 2>&1',
            escapeshellarg($pdftotextPath),
            $primera,
            $ultima,
            escapeshellarg($filePath),
            escapeshellarg($tempFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($tempFile)) {
            $texto = file_get_contents($tempFile);
            unlink($tempFile);
            return $texto;
        }

        return null;
    }

    /**
     * Extraer texto completo del PDF usando pdftotext para mejor calidad
     *
     * @param string $filePath Ruta del archivo PDF
     * @return string|null Texto extraído o null si falla
     */
    public function extraerTextoCompleto(string $filePath): ?string
    {
        $pdftotextPath = $this->findPdftotext();

        if ($pdftotextPath) {
            // Usar pdftotext para mejor extracción
            $tempFile = sys_get_temp_dir() . '/pdf_text_' . uniqid() . '.txt';
            $command = sprintf(
                '%s -layout %s %s 2>&1',
                escapeshellarg($pdftotextPath),
                escapeshellarg($filePath),
                escapeshellarg($tempFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($tempFile)) {
                $texto = file_get_contents($tempFile);
                unlink($tempFile);
                return $texto;
            }
        }

        // Fallback a smalot/pdfparser
        try {
            $pdfData = $this->cargarPDF($filePath);
            $texto = '';
            foreach ($pdfData['pages'] as $page) {
                $texto .= $page->getText() . "\n";
            }
            return $texto;
        } catch (\Exception $e) {
            $this->addLog('error', "Error extrayendo texto: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Enriquecer factura con datos extraídos del PDF
     *
     * @param Factura $factura Factura a enriquecer
     * @param array $datosExtraidos Datos extraídos del PDF
     * @param bool $sobreescribir Si true, sobreescribe valores existentes
     * @return array Campos actualizados
     */
    public function enriquecerFactura(Factura $factura, array $datosExtraidos, bool $sobreescribir = false): array
    {
        $actualizados = [];

        // Campos a actualizar
        $mapeo = [
            'motonave' => 'motonave',
            'trb' => 'trb',
            'servicio_descripcion' => 'servicio_descripcion',
        ];

        foreach ($mapeo as $campoOrigen => $campoDestino) {
            if (!empty($datosExtraidos[$campoOrigen])) {
                $valorActual = $factura->$campoDestino;

                // Solo actualizar si el campo está vacío o si se permite sobreescribir
                if (empty($valorActual) || $sobreescribir) {
                    $factura->$campoDestino = $datosExtraidos[$campoOrigen];
                    $actualizados[$campoDestino] = [
                        'anterior' => $valorActual,
                        'nuevo' => $datosExtraidos[$campoOrigen],
                    ];
                }
            }
        }

        if (!empty($actualizados)) {
            $factura->save();
            $this->addLog('success', "Factura {$factura->numero_factura} enriquecida con " . count($actualizados) . " campos");
        }

        return $actualizados;
    }

    /**
     * Añadir entrada al log de debug
     */
    private function addLog(string $level, string $message): void
    {
        $this->debugLog[] = [
            'level' => $level,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Formatear bytes a unidad legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Obtener información de un PDF sin procesarlo completamente
     *
     * @param string $filePath Ruta del archivo PDF
     * @return array Información básica del PDF
     */
    public function obtenerInfoPDF(string $filePath): array
    {
        $pdfData = $this->cargarPDF($filePath);
        $cufes = $this->extraerCUFEs($pdfData['pages']);

        return [
            'nombre' => $pdfData['nombre'],
            'tamaño' => $pdfData['tamaño_formateado'],
            'paginas' => $pdfData['total_paginas'],
            'cufes_encontrados' => count($cufes),
            'cufes' => array_map(function ($c) {
                return [
                    'cufe' => substr($c['cufe'], 0, 20) . '...',
                    'cufe_completo' => $c['cufe'],
                    'paginas' => $c['paginas'],
                ];
            }, $cufes),
        ];
    }

    /**
     * Flujo completo: cargar PDF, extraer CUFEs, comparar y procesar
     *
     * @param string $filePath Ruta del archivo PDF
     * @param int $tenantId ID del tenant
     * @param bool $dryRun Si es true, no guarda cambios
     * @return array Resultado completo del procesamiento
     */
    public function procesarArchivoPDF(string $filePath, int $tenantId, bool $dryRun = false): array
    {
        $this->reset();

        // 1. Cargar PDF
        $pdfInfo = $this->cargarPDF($filePath);

        // 2. Extraer CUFEs
        $cufes = $this->extraerCUFEs($pdfInfo['pages']);

        // 3. Comparar con BD
        $comparacion = $this->compararConBD($cufes, $tenantId);

        // 4. Procesar y asociar
        $procesamiento = $this->procesarPDF($filePath, $cufes, $tenantId, $dryRun);

        return [
            'archivo' => [
                'nombre' => $pdfInfo['nombre'],
                'tamaño' => $pdfInfo['tamaño_formateado'],
                'paginas' => $pdfInfo['total_paginas'],
            ],
            'cufes_encontrados' => count($cufes),
            'comparacion' => [
                'en_bd' => count($comparacion['encontradas']),
                'disponibles' => count($comparacion['disponibles']),
                'ya_asociadas' => count($comparacion['ya_asociadas']),
                'no_encontradas' => count($comparacion['no_encontradas']),
            ],
            'procesamiento' => $procesamiento,
            'stats' => $this->stats,
            'log' => $this->debugLog,
            'dry_run' => $dryRun,
        ];
    }
}
