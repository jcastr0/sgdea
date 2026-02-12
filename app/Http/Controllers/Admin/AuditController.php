<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Listar auditoría del tenant actual
     * - Admin de tenant ve solo su tenant
     * - Usuario normal ve solo sus propias acciones
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        // Base query - filtrar por tenant
        $query = AuditLog::where('tenant_id', $tenantId);

        // Si no es admin del tenant, solo ve sus propias acciones
        if (!$user->isAdminTenant()) {
            $query->where('user_id', $user->id);
        }

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por tipo de modelo
        if ($request->filled('model_type')) {
            $query->where('model_type', 'LIKE', '%' . $request->model_type . '%');
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Búsqueda de texto libre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $logs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Datos para filtros
        $acciones = AuditLog::where('tenant_id', $tenantId)
            ->distinct()
            ->pluck('action')
            ->filter()
            ->sort()
            ->values();

        // Usuarios disponibles para filtrar (solo admin ve todos)
        $usuarios = collect();
        if ($user->isAdminTenant()) {
            $usuarios = User::where('tenant_id', $tenantId)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('admin.auditoria.index', [
            'logs' => $logs,
            'acciones' => $acciones,
            'usuarios' => $usuarios,
            'filters' => $request->only(['action', 'model_type', 'user_id', 'fecha_inicio', 'fecha_fin', 'search']),
            'canFilterByUser' => $user->isAdminTenant(),
        ]);
    }

    /**
     * Ver detalles de un registro de auditoría
     */
    public function show($id)
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        $log = AuditLog::where('tenant_id', $tenantId)
            ->with('user')
            ->findOrFail($id);

        // Verificar permisos
        if (!$user->isAdminTenant() && $log->user_id !== $user->id) {
            abort(403, 'No tiene permisos para ver este registro.');
        }

        // Obtener auditoría completa de ese modelo (si tiene model_type y model_id)
        $auditCompleta = collect();
        if ($log->model_type && $log->model_id) {
            $auditCompleta = AuditLog::where('tenant_id', $tenantId)
                ->where('model_type', $log->model_type)
                ->where('model_id', $log->model_id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        // Obtener la entidad si existe
        $entidad = null;
        $pdfPath = null;

        if ($log->model_type && $log->model_id && class_exists($log->model_type)) {
            try {
                $entidad = $log->model_type::find($log->model_id);

                // Si es una factura, obtener el PDF
                if ($entidad && $log->model_type === \App\Models\Factura::class) {
                    $pdfPath = $entidad->pdf_path ? route('facturas.pdf', $entidad) : null;
                }
            } catch (\Exception $e) {
                // Modelo no encontrado o error
            }
        }

        return view('admin.auditoria.show', [
            'log' => $log,
            'auditCompleta' => $auditCompleta,
            'entidad' => $entidad,
            'pdfPath' => $pdfPath,
        ]);
    }

    /**
     * Exportar auditoría a CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        if (!$user->isAdminTenant()) {
            abort(403, 'Solo los administradores pueden exportar la auditoría.');
        }

        // Base query
        $query = AuditLog::where('tenant_id', $tenantId)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $logs = $query->limit(10000)->get();

        $filename = 'auditoria_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'Fecha',
                'Hora',
                'Acción',
                'Usuario',
                'Email',
                'Modelo',
                'ID',
                'IP',
                'Método',
            ], ';');

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d'),
                    $log->created_at->format('H:i:s'),
                    $log->action,
                    $log->user?->name ?? 'Sistema',
                    $log->user?->email ?? 'system@sgdea.local',
                    class_basename($log->model_type ?? ''),
                    $log->model_id ?? '',
                    $log->ip_address ?? '',
                    $log->method ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Verificar integridad de registros de auditoría
     */
    public function verificarIntegridad(Request $request)
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        if (!$user->isAdminTenant()) {
            abort(403, 'Solo los administradores pueden verificar la integridad.');
        }

        // Obtener logs recientes para verificar
        $logs = AuditLog::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $resultados = [];
        $totalVerificados = 0;
        $totalIntegros = 0;
        $totalComprometidos = 0;

        foreach ($logs as $log) {
            $totalVerificados++;

            // Verificar integridad básica (campos requeridos presentes)
            $integro = !empty($log->action) &&
                       !empty($log->user_id) &&
                       !empty($log->created_at);

            if ($integro) {
                $totalIntegros++;
            } else {
                $totalComprometidos++;
                $resultados[] = [
                    'id' => $log->id,
                    'fecha' => $log->created_at->format('Y-m-d H:i:s'),
                    'accion' => $log->action,
                    'problema' => 'Campos requeridos faltantes',
                ];
            }
        }

        return view('admin.auditoria.integridad', [
            'totalVerificados' => $totalVerificados,
            'totalIntegros' => $totalIntegros,
            'totalComprometidos' => $totalComprometidos,
            'resultados' => $resultados,
        ]);
    }
}

