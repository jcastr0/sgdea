<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemUser;
use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TenantController extends Controller
{
    /**
     * Listar todos los tenants
     */
    public function index()
    {
        // Verificar que es superadmin global
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenants = Tenant::with('systemUser')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tenants.index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Formulario crear tenant
     */
    public function create()
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        return view('admin.tenants.create');
    }

    /**
     * Guardar nuevo tenant
     */
    public function store(Request $request)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255|unique:tenants,name',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'color_primary' => 'nullable|regex:/^#[0-9A-F]{6}$/i',
        ], [
            'company_name.unique' => 'El nombre de la empresa ya existe',
            'domain.unique' => 'El dominio ya está registrado',
            'admin_email.unique' => 'El email ya está registrado',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Tenant
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']),
                'domain' => $validated['domain'],
                'status' => 'active',
                'superadmin_id' => auth('system')->id(),
            ]);

            // 2. Crear Configuración de Tema
            ThemeConfiguration::create([
                'tenant_id' => $tenant->id,
                'color_primary' => $validated['color_primary'] ?? '#2767C6',
                'color_secondary' => '#102544',
                'color_error' => '#B23A3A',
                'color_success' => '#009F6B',
                'color_warning' => '#F5B400',
                'color_bg_light' => '#F5F7FA',
                'color_bg_dark' => '#102544',
                'color_text_primary' => '#1F2933',
                'color_text_secondary' => '#6B7280',
                'color_border' => '#D4D9E2',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tenants.show', $tenant->id)
                ->with('success', "Tenant '{$validated['company_name']}' creado exitosamente. El administrador puede registrarse accediendo a su dominio.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear tenant: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalles del tenant
     */
    public function show($id)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenant = Tenant::with('systemUser', 'themeConfiguration')
            ->findOrFail($id);

        // Contar usuarios del tenant
        $usersCount = DB::table('users')
            ->where('tenant_id', $tenant->id)
            ->count();

        // Contar facturas del tenant
        $invoicesCount = DB::table('facturas')
            ->where('tenant_id', $tenant->id)
            ->count();

        // Contar clientes del tenant
        $clientsCount = DB::table('terceros')
            ->where('tenant_id', $tenant->id)
            ->count();

        return view('admin.tenants.show', [
            'tenant' => $tenant,
            'usersCount' => $usersCount,
            'invoicesCount' => $invoicesCount,
            'clientsCount' => $clientsCount,
        ]);
    }

    /**
     * Editar tenant
     */
    public function edit($id)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenant = Tenant::findOrFail($id);

        return view('admin.tenants.edit', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Actualizar tenant
     */
    public function update(Request $request, $id)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tenants,name,' . $id,
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $tenant->update($validated);

            return redirect()
                ->route('admin.tenants.show', $tenant->id)
                ->with('success', 'Tenant actualizado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del tenant
     */
    public function toggleStatus($id)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->status === 'active' ? 'inactive' : 'active';
        $tenant->update(['status' => $newStatus]);

        return back()->with('success', "Estado del tenant actualizado a: {$newStatus}");
    }

    /**
     * Eliminar tenant (con confirmación)
     */
    public function destroy($id)
    {
        if (!auth('system')->check() || !auth('system')->user()->is_superadmin) {
            abort(403, 'No autorizado');
        }

        $tenant = Tenant::findOrFail($id);
        $tenantName = $tenant->name;

        try {
            DB::beginTransaction();

            // Eliminar usuarios del tenant
            DB::table('users')->where('tenant_id', $tenant->id)->delete();

            // Eliminar tema del tenant
            ThemeConfiguration::where('tenant_id', $tenant->id)->delete();

            // Eliminar datos del tenant (facturas, clientes, etc)
            DB::table('facturas')->where('tenant_id', $tenant->id)->delete();
            DB::table('terceros')->where('tenant_id', $tenant->id)->delete();

            // Eliminar tenant
            $tenant->delete();

            DB::commit();

            return redirect()
                ->route('admin.tenants.index')
                ->with('success', "Tenant '{$tenantName}' eliminado exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}

