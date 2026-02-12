<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserApprovalController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Listar usuarios pendientes de aprobación del tenant actual
     */
    public function pendientes(Request $request)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario actual es admin del tenant
        $this->verificarAdminTenant($tenantId);

        // Obtener usuarios pendientes del tenant
        $usuariosPendientes = User::where('tenant_id', $tenantId)
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        // Contar totales
        $stats = [
            'pendientes' => User::where('tenant_id', $tenantId)->where('status', 'pending_approval')->count(),
            'activos' => User::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'rechazados' => User::where('tenant_id', $tenantId)->where('status', 'rejected')->count(),
        ];

        return view('admin.usuarios.pendientes', [
            'usuarios' => $usuariosPendientes,
            'stats' => $stats,
        ]);
    }

    /**
     * Aprobar usuario
     */
    public function aprobar(Request $request, $userId)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario actual es admin del tenant
        $this->verificarAdminTenant($tenantId);

        $usuario = User::where('id', $userId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        if ($usuario->status !== 'pending_approval') {
            return back()->with('error', 'Este usuario no está pendiente de aprobación');
        }

        // Cambiar estado
        $usuarioAnterior = $usuario->status;
        $usuario->update([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Registrar en auditoría
        $this->registrarAuditoria(
            'user_approved',
            $usuario,
            $adminId,
            ['estado_anterior' => $usuarioAnterior, 'estado_nuevo' => 'active']
        );

        // Enviar email al usuario
        // Mail::send(new UserApprovedMail($usuario));

        return back()->with('success', "Usuario {$usuario->name} aprobado exitosamente");
    }

    /**
     * Rechazar usuario
     */
    public function rechazar(Request $request, $userId)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario actual es admin del tenant
        $this->verificarAdminTenant($tenantId);

        $validated = $request->validate([
            'razon_rechazo' => 'nullable|string|max:500',
        ]);

        $usuario = User::where('id', $userId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        if ($usuario->status !== 'pending_approval') {
            return back()->with('error', 'Este usuario no está pendiente de aprobación');
        }

        // Cambiar estado
        $usuarioAnterior = $usuario->status;
        $razonRechazo = $validated['razon_rechazo'] ?? 'Sin especificar';

        $usuario->update([
            'status' => 'rejected',
        ]);

        // Registrar en auditoría
        $this->registrarAuditoria(
            'user_rejected',
            $usuario,
            $adminId,
            [
                'estado_anterior' => $usuarioAnterior,
                'estado_nuevo' => 'rejected',
                'razon_rechazo' => $razonRechazo
            ]
        );

        // Enviar email al usuario
        // Mail::send(new UserRejectedMail($usuario, $razonRechazo));

        return back()->with('success', "Usuario {$usuario->name} rechazado");
    }

    /**
     * Historial de aprobaciones/rechazos
     */
    public function historial(Request $request)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario actual es admin del tenant
        $this->verificarAdminTenant($tenantId);

        // Obtener auditoría de aprobaciones/rechazos
        $historial = AuditLog::where('tenant_id', $tenantId)
            ->whereIn('action', ['user_approved', 'user_rejected'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.usuarios.historial', [
            'historial' => $historial,
        ]);
    }

    /**
     * Cambiar estado de usuario (activo/inactivo)
     */
    public function cambiarEstado(Request $request, $userId)
    {
        $tenantId = session('tenant_id');
        $adminId = auth()->user()->id;

        // Verificar que el usuario actual es admin del tenant
        $this->verificarAdminTenant($tenantId);

        $usuario = User::where('id', $userId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // No permitir cambiar estado del admin a sí mismo
        if ($usuario->id === $adminId) {
            return back()->with('error', 'No puedes cambiar tu propio estado');
        }

        $estadoAnterior = $usuario->status;
        $estadoNuevo = $usuario->status === 'active' ? 'inactive' : 'active';

        $usuario->update(['status' => $estadoNuevo]);

        // Registrar en auditoría
        $this->registrarAuditoria(
            'user_status_changed',
            $usuario,
            $adminId,
            ['estado_anterior' => $estadoAnterior, 'estado_nuevo' => $estadoNuevo]
        );

        return back()->with('success', "Estado del usuario actualizado");
    }

    /**
     * Verificar que el usuario actual es admin del tenant
     */
    private function verificarAdminTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant || $tenant->superadmin_id !== auth()->user()->id) {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }
    }

    /**
     * Registrar cambio en auditoría
     */
    private function registrarAuditoria($action, $usuario, $adminId, $datos = [])
    {
        AuditLog::create([
            'user_id' => $adminId,
            'tenant_id' => session('tenant_id'),
            'action' => $action,
            'model_type' => 'User',
            'model_id' => $usuario->id,
            'changes' => json_encode($datos),
            'ip_address' => request()->ip(),
        ]);
    }
}

