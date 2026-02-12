<?php

namespace App\Services;

use App\Models\Tercero;
use App\Models\Factura;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TerceroService
{
    /**
     * Validar si existe un tercero con el mismo NIT
     */
    public function validarUnicidadNit(string $nit, ?int $exceptoId = null): array
    {
        $existe = Tercero::existeDuplicadoPorNit($nit, $exceptoId);

        return [
            'valido' => !$existe,
            'mensaje' => $existe ? "Ya existe un tercero con el NIT $nit" : null,
            'tercero_existente' => $existe ? Tercero::porNit($nit)->first() : null,
        ];
    }

    /**
     * Buscar terceros con nombres similares
     * Para prevención de duplicados
     */
    public function buscarTercerosSimilares(string $nombre, ?int $exceptoId = null): array
    {
        $similares = Tercero::buscarSimilares($nombre);

        // Filtrar el tercero actual si se está editando
        if ($exceptoId) {
            $similares = $similares->filter(function ($tercero) use ($exceptoId) {
                return $tercero->id != $exceptoId;
            });
        }

        return [
            'encontrados' => $similares->count() > 0,
            'cantidad' => $similares->count(),
            'terceros' => $similares->values(),
            'mensaje' => $similares->count() > 0
                ? "Se encontraron {$similares->count()} tercero(s) con nombres similares. Revise antes de continuar."
                : null,
        ];
    }

    /**
     * Crear tercero con validaciones
     */
    public function crearTercero(array $datos, int $usuarioId): array
    {
        // Validar unicidad de NIT
        $validacionNit = $this->validarUnicidadNit($datos['nit']);
        if (!$validacionNit['valido']) {
            return [
                'success' => false,
                'error' => $validacionNit['mensaje'],
                'tipo' => 'nit_duplicado',
                'tercero_existente' => $validacionNit['tercero_existente'],
            ];
        }

        // Buscar similares por nombre
        $similares = $this->buscarTercerosSimilares($datos['nombre_razon_social']);
        if ($similares['encontrados']) {
            return [
                'success' => false,
                'warning' => $similares['mensaje'],
                'tipo' => 'nombre_similar',
                'terceros_similares' => $similares['terceros'],
                'datos_pendientes' => $datos, // Para que el usuario pueda confirmar
            ];
        }

        // Crear tercero
        try {
            $tercero = Tercero::create($datos);

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'tercero_created',
                'description' => "Tercero creado: {$tercero->nombre_razon_social} (NIT: {$tercero->nit})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'tercero_id' => $tercero->id,
                    'nit' => $tercero->nit,
                    'nombre' => $tercero->nombre_razon_social,
                ]),
            ]);

            return [
                'success' => true,
                'tercero' => $tercero,
                'mensaje' => 'Tercero creado exitosamente',
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear tercero: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al crear el tercero. Intente nuevamente.',
            ];
        }
    }

    /**
     * Actualizar tercero con validaciones
     */
    public function actualizarTercero(Tercero $tercero, array $datos, int $usuarioId): array
    {
        // Validar unicidad de NIT (excepto el actual)
        if (isset($datos['nit']) && $datos['nit'] !== $tercero->nit) {
            $validacionNit = $this->validarUnicidadNit($datos['nit'], $tercero->id);
            if (!$validacionNit['valido']) {
                return [
                    'success' => false,
                    'error' => $validacionNit['mensaje'],
                ];
            }
        }

        try {
            // Guardar valores antiguos para auditoría
            $valoresAntiguos = $tercero->only(['nit', 'nombre_razon_social', 'direccion', 'telefono']);

            // Actualizar
            $tercero->update($datos);

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'tercero_updated',
                'description' => "Tercero actualizado: {$tercero->nombre_razon_social} (NIT: {$tercero->nit})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'tercero_id' => $tercero->id,
                    'cambios' => array_diff_assoc($datos, $valoresAntiguos),
                    'valores_anteriores' => $valoresAntiguos,
                ]),
            ]);

            return [
                'success' => true,
                'tercero' => $tercero->fresh(),
                'mensaje' => 'Tercero actualizado exitosamente',
            ];
        } catch (\Exception $e) {
            Log::error('Error al actualizar tercero: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al actualizar el tercero. Intente nuevamente.',
            ];
        }
    }

    /**
     * Fusionar terceros (Merge)
     * Combina dos o más terceros en uno solo (maestro)
     */
    public function fusionarTerceros(int $terceroMaestroId, array $tercerosDuplicadosIds, int $usuarioId): array
    {
        try {
            DB::beginTransaction();

            // Obtener tercero maestro
            $terceroMaestro = Tercero::findOrFail($terceroMaestroId);

            // Obtener terceros duplicados
            $tercerosDuplicados = Tercero::whereIn('id', $tercerosDuplicadosIds)->get();

            if ($tercerosDuplicados->isEmpty()) {
                return [
                    'success' => false,
                    'error' => 'No se encontraron terceros duplicados para fusionar',
                ];
            }

            $totalFacturasReasociadas = 0;
            $tercerosFusionados = [];

            // Reasociar todas las facturas al tercero maestro
            foreach ($tercerosDuplicados as $duplicado) {
                $facturasReasociadas = Factura::where('tercero_id', $duplicado->id)
                    ->update(['tercero_id' => $terceroMaestro->id]);

                $totalFacturasReasociadas += $facturasReasociadas;

                $tercerosFusionados[] = [
                    'id' => $duplicado->id,
                    'nit' => $duplicado->nit,
                    'nombre' => $duplicado->nombre_razon_social,
                    'facturas_reasociadas' => $facturasReasociadas,
                ];

                // Eliminar tercero duplicado
                $duplicado->delete();
            }

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'terceros_merged',
                'description' => "Fusión de terceros: {$tercerosDuplicados->count()} tercero(s) fusionados en '{$terceroMaestro->nombre_razon_social}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'tercero_maestro_id' => $terceroMaestro->id,
                    'tercero_maestro_nit' => $terceroMaestro->nit,
                    'tercero_maestro_nombre' => $terceroMaestro->nombre_razon_social,
                    'terceros_fusionados' => $tercerosFusionados,
                    'total_facturas_reasociadas' => $totalFacturasReasociadas,
                ]),
            ]);

            DB::commit();

            return [
                'success' => true,
                'tercero_maestro' => $terceroMaestro,
                'terceros_fusionados' => $tercerosFusionados,
                'total_facturas_reasociadas' => $totalFacturasReasociadas,
                'mensaje' => "Se fusionaron {$tercerosDuplicados->count()} tercero(s) y se reasociaron {$totalFacturasReasociadas} factura(s) exitosamente",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al fusionar terceros: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al fusionar terceros. Intente nuevamente.',
            ];
        }
    }

    /**
     * Eliminar tercero (solo si no tiene facturas)
     */
    public function eliminarTercero(Tercero $tercero, int $usuarioId): array
    {
        if (!$tercero->puedeSerEliminado()) {
            return [
                'success' => false,
                'error' => 'No se puede eliminar el tercero porque tiene facturas asociadas. Considere fusionarlo con otro tercero.',
            ];
        }

        try {
            $terceroData = [
                'id' => $tercero->id,
                'nit' => $tercero->nit,
                'nombre' => $tercero->nombre_razon_social,
            ];

            $tercero->delete();

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'tercero_deleted',
                'description' => "Tercero eliminado: {$terceroData['nombre']} (NIT: {$terceroData['nit']})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode($terceroData),
            ]);

            return [
                'success' => true,
                'mensaje' => 'Tercero eliminado exitosamente',
            ];
        } catch (\Exception $e) {
            Log::error('Error al eliminar tercero: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al eliminar el tercero. Intente nuevamente.',
            ];
        }
    }

    /**
     * Buscar terceros con autocompletar
     */
    public function buscarConAutocompletar(string $termino, int $limite = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Tercero::busquedaAvanzada($termino)
            ->limit($limite)
            ->get(['id', 'nit', 'nombre_razon_social', 'ciudad']);
    }

    /**
     * Obtener estadísticas de un tercero
     */
    public function obtenerEstadisticas(Tercero $tercero): array
    {
        return $tercero->getEstadisticas();
    }
}

