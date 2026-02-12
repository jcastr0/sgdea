<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AuditController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Listar auditoría del tenant actual
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario es admin del tenant
        $this->verificarAdminTenant($tenantId);

        // Construir query
        $query = AuditLog::where('tenant_id', $tenantId);

        // Filtro por usuario
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filtro por tipo de entidad
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        // Filtro por rango de fechas
        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin') && $request->fecha_fin) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Búsqueda de texto libre
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Usuarios para filtro
        $usuarios = AuditLog::where('tenant_id', $tenantId)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->map(function ($id) {
                return \App\Models\User::find($id);
            })
            ->filter();

        return view('admin.auditoria.index', [
            'logs' => $logs,
            'usuarios' => $usuarios,
            'filtrosActivos' => $this->obtenerFiltrosActivos($request),
        ]);
    }

    /**
     * Ver detalles de un evento con auditoría completa y visor de PDF
     */
    public function show($id)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdminTenant($tenantId);

        $log = AuditLog::where('tenant_id', $tenantId)
            ->with('user')
            ->findOrFail($id);

        // Verificar integridad
        $integro = AuditService::verificarIntegridad($log);

        // Obtener auditoría completa de esa entidad
        $auditCompletaEntidad = AuditLog::where('tenant_id', $tenantId)
            ->where('entity_type', $log->entity_type)
            ->where('entity_id', $log->entity_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener la entidad (factura, tercero, etc) si existe
        $entidad = null;
        $pdfPath = null;

        if ($log->entity_type === 'factura' && $log->entity_id) {
            $entidad = \App\Models\Factura::find($log->entity_id);
            if ($entidad) {
                $pdfPath = $entidad->pdf_path ? asset('storage/' . $entidad->pdf_path) : null;
            }
        } elseif ($log->entity_type === 'tercero' && $log->entity_id) {
            $entidad = \App\Models\Tercero::find($log->entity_id);
        }

        return view('admin.auditoria.show', [
            'log' => $log,
            'integro' => $integro,
            'auditCompleta' => $auditCompletaEntidad,
            'entidad' => $entidad,
            'pdfPath' => $pdfPath,
        ]);
    }

    /**
     * Exportar auditoría a CSV
     */
    public function export(Request $request)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdminTenant($tenantId);

        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Obtener logs del período
        $logs = AuditService::obtenerPeriodo(
            $tenantId,
            $validated['fecha_inicio'],
            $validated['fecha_fin']
        );

        // Registrar la exportación
        AuditService::registrarExportacion('auditoria', $logs->count(), 'csv');

        // Generar CSV
        $csv = "FECHA,USUARIO,ACCIÓN,ENTIDAD,DESCRIPCIÓN,IP,INTEGRIDAD\n";

        foreach ($logs as $log) {
            $integro = AuditService::verificarIntegridad($log) ? '✓' : '✗ ALTERADO';
            $csv .= sprintf(
                "%s,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user->name ?? 'Sistema',
                $log->action,
                $log->entity_type,
                str_replace('"', '""', $log->description),
                $log->ip_address,
                $integro
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename=auditoria_' . $tenantId . '_' . date('Y-m-d_H-i-s') . '.csv');
    }

    /**
     * Verificar integridad de todos los logs
     */
    public function verificarIntegridad()
    {
        $tenantId = session('tenant_id');
        $this->verificarAdminTenant($tenantId);

        $logs = AuditLog::where('tenant_id', $tenantId)->get();

        $resultados = [
            'total' => $logs->count(),
            'integros' => 0,
            'alterados' => 0,
            'logsAlterados' => [],
        ];

        foreach ($logs as $log) {
            if (AuditService::verificarIntegridad($log)) {
                $resultados['integros']++;
            } else {
                $resultados['alterados']++;
                $resultados['logsAlterados'][] = [
                    'id' => $log->id,
                    'fecha' => $log->created_at,
                    'descripción' => $log->description,
                ];
            }
        }

        return view('admin.auditoria.integridad', [
            'resultados' => $resultados,
        ]);
    }

    /**
     * Obtener filtros activos
     */
    private function obtenerFiltrosActivos(Request $request): array
    {
        $filtros = [];

        if ($request->has('user_id') && $request->user_id) {
            $filtros['user_id'] = $request->user_id;
        }
        if ($request->has('action') && $request->action) {
            $filtros['action'] = $request->action;
        }
        if ($request->has('entity_type') && $request->entity_type) {
            $filtros['entity_type'] = $request->entity_type;
        }
        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            $filtros['fecha_inicio'] = $request->fecha_inicio;
        }
        if ($request->has('fecha_fin') && $request->fecha_fin) {
            $filtros['fecha_fin'] = $request->fecha_fin;
        }
        if ($request->has('search') && $request->search) {
            $filtros['search'] = $request->search;
        }

        return $filtros;
    }

    /**
     * Verificar que el usuario es admin del tenant
     */
    private function verificarAdminTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant || $tenant->superadmin_id !== auth()->user()->id) {
            abort(403, 'No tienes permiso para acceder a la auditoría');
        }
    }
}

