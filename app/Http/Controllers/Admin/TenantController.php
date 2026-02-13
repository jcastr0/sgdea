<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    /**
     * Listar todos los tenants (usa componente Livewire)
     */
    public function index()
    {
        return view('admin.tenants.index');
    }

    /**
     * Formulario crear tenant
     */
    public function create()
    {

        return view('admin.tenants.create');
    }

    /**
     * Guardar nuevo tenant (Wizard completo)
     */
    public function store(Request $request)
    {
        // Detectar si es petición AJAX
        $isAjax = $request->expectsJson() || $request->ajax();

        $validated = $request->validate([
            // Step 1 - Información básica
            'company_name' => 'required|string|max:255|unique:tenants,name',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            // Step 2 - Configuración
            'plan' => 'nullable|string|in:basic,professional,enterprise',
            'max_users' => 'nullable|integer|min:0',
            'max_storage' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:active,trial,suspended',
            // Step 3 - Branding
            'color_primary' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',
            'color_secondary' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',
            'color_accent' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',
            'dark_mode_enabled' => 'nullable|boolean',
            // Step 4 - Usuario admin
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'nullable|string|min:8',
            'send_welcome_email' => 'nullable|boolean',
        ], [
            'company_name.unique' => 'El nombre de la empresa ya existe',
            'slug.unique' => 'El identificador ya está en uso',
            'domain.unique' => 'El dominio ya está registrado',
            'admin_email.unique' => 'El email ya está registrado',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Tenant
            $tenantData = [
                'name' => $validated['company_name'],
                'slug' => $validated['slug'],
                'domain' => $validated['domain'],
                'status' => $validated['status'] ?? 'active',
                'created_by' => auth()->id(),
            ];

            // Agregar campos opcionales si existen en la tabla
            if (Schema::hasColumn('tenants', 'plan')) {
                $tenantData['plan'] = $validated['plan'] ?? 'professional';
            }
            if (Schema::hasColumn('tenants', 'max_users')) {
                $tenantData['max_users'] = $validated['max_users'] ?? 0;
            }
            if (Schema::hasColumn('tenants', 'max_storage')) {
                $tenantData['max_storage'] = $validated['max_storage'] ?? 0;
            }

            $tenant = Tenant::create($tenantData);

            // 2. Crear Configuración de Tema
            ThemeConfiguration::create([
                'tenant_id' => $tenant->id,
                'color_primary' => $validated['color_primary'] ?? '#2563eb',
                'color_secondary' => $validated['color_secondary'] ?? '#0f172a',
                'color_accent' => $validated['color_accent'] ?? '#10b981',
                'color_error' => '#ef4444',
                'color_success' => '#10b981',
                'color_warning' => '#f59e0b',
                'color_bg_light' => '#f8fafc',
                'color_bg_dark' => '#0f172a',
                'color_text_primary' => '#1f2937',
                'color_text_secondary' => '#6b7280',
                'color_border' => '#e5e7eb',
                'dark_mode_enabled' => $validated['dark_mode_enabled'] ?? true,
            ]);

            // 3. Obtener o crear rol admin para este tenant
            $adminRole = \App\Models\Role::firstOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'admin'],
                [
                    'name' => 'Administrador',
                    'description' => 'Administrador del tenant con acceso total',
                    'is_system' => true,
                    'priority' => 100,
                ]
            );

            // 4. Generar password si no se proporcionó
            $plainPassword = $validated['admin_password'] ?? Str::random(12);

            // 5. Crear usuario administrador
            $adminUser = \App\Models\User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $adminRole->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($plainPassword),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // 6. Enviar email de bienvenida si está habilitado
            if ($validated['send_welcome_email'] ?? false) {
                // TODO: Implementar envío de email
                // Mail::to($adminUser->email)->send(new WelcomeTenant($tenant, $adminUser, $plainPassword));
            }

            DB::commit();

            // Respuesta según tipo de petición
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => "Tenant '{$tenant->name}' creado exitosamente",
                    'tenant_id' => $tenant->id,
                    'admin_email' => $adminUser->email,
                    'admin_password' => $plainPassword,
                ]);
            }

            return redirect()
                ->route('admin.tenants.show', $tenant->id)
                ->with('success', "Tenant '{$tenant->name}' creado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear tenant: ' . $e->getMessage(),
                ], 500);
            }

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
        $tenant = Tenant::with('themeConfiguration')
            ->findOrFail($id);

        // Usuarios del tenant
        $users = \App\Models\User::where('tenant_id', $tenant->id)
            ->with('role')
            ->orderBy('name')
            ->get();

        // Conteos
        $usersCount = $users->count();
        $facturasCount = DB::table('facturas')->where('tenant_id', $tenant->id)->count();
        $tercerosCount = DB::table('terceros')->where('tenant_id', $tenant->id)->count();

        // Calcular storage usado (archivos PDF)
        $storageUsed = '0 MB';
        $storagePath = storage_path("app/private/facturas/{$tenant->id}");
        if (is_dir($storagePath)) {
            $bytes = 0;
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath)) as $file) {
                if ($file->isFile()) {
                    $bytes += $file->getSize();
                }
            }
            if ($bytes > 1073741824) {
                $storageUsed = round($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes > 1048576) {
                $storageUsed = round($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes > 1024) {
                $storageUsed = round($bytes / 1024, 2) . ' KB';
            } else {
                $storageUsed = $bytes . ' B';
            }
        }

        // Actividad reciente
        $recentActivity = [];
        try {
            $auditLogs = \App\Models\AuditLog::where(function($query) use ($tenant) {
                    $query->where('context->tenant_id', $tenant->id)
                          ->orWhereIn('user_id', $users->pluck('id'));
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($auditLogs as $log) {
                $recentActivity[] = [
                    'action' => $log->action,
                    'user' => $log->usuario->name ?? 'Sistema',
                    'model' => class_basename($log->model_type ?? ''),
                    'time' => $log->created_at->diffForHumans(),
                ];
            }
        } catch (\Exception $e) {
            // Si falla, dejamos vacío
        }

        return view('admin.tenants.show', [
            'tenant' => $tenant,
            'users' => $users,
            'usersCount' => $usersCount,
            'facturasCount' => $facturasCount,
            'tercerosCount' => $tercerosCount,
            'storageUsed' => $storageUsed,
            'recentActivity' => collect($recentActivity),
        ]);
    }

    /**
     * Editar tenant
     */
    public function edit($id)
    {
        $tenant = Tenant::with('themeConfiguration')->findOrFail($id);

        // Usuarios del tenant
        $users = \App\Models\User::where('tenant_id', $tenant->id)
            ->with('role')
            ->orderBy('name')
            ->get();

        return view('admin.tenants.edit', [
            'tenant' => $tenant,
            'users' => $users,
        ]);
    }

    /**
     * Actualizar tenant
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tab = $request->input('tab', 'general');

        try {
            DB::beginTransaction();

            switch ($tab) {
                case 'general':
                    $validated = $request->validate([
                        'name' => 'required|string|max:255|unique:tenants,name,' . $id,
                        'slug' => 'required|string|max:255|unique:tenants,slug,' . $id,
                        'domain' => 'required|string|max:255|unique:tenants,domain,' . $id,
                        'status' => 'required|in:active,trial,suspended',
                    ]);
                    $tenant->update($validated);
                    break;

                case 'plan':
                    $validated = $request->validate([
                        'plan' => 'nullable|string|in:basic,professional,enterprise',
                        'max_users' => 'nullable|integer|min:0',
                        'max_storage' => 'nullable|integer|min:0',
                    ]);

                    $updateData = [];
                    if (Schema::hasColumn('tenants', 'plan') && isset($validated['plan'])) {
                        $updateData['plan'] = $validated['plan'];
                    }
                    if (Schema::hasColumn('tenants', 'max_users') && isset($validated['max_users'])) {
                        $updateData['max_users'] = $validated['max_users'];
                    }
                    if (Schema::hasColumn('tenants', 'max_storage') && isset($validated['max_storage'])) {
                        $updateData['max_storage'] = $validated['max_storage'];
                    }

                    if (!empty($updateData)) {
                        $tenant->update($updateData);
                    }
                    break;

                case 'branding':
                    $validated = $request->validate([
                        'color_primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/i',
                        'color_secondary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/i',
                        'color_accent' => 'required|regex:/^#[0-9A-Fa-f]{6}$/i',
                        'dark_mode_enabled' => 'nullable',
                    ]);

                    $theme = $tenant->themeConfiguration;
                    if ($theme) {
                        $theme->update([
                            'color_primary' => $validated['color_primary'],
                            'color_secondary' => $validated['color_secondary'],
                            'color_accent' => $validated['color_accent'],
                            'dark_mode_enabled' => $request->has('dark_mode_enabled'),
                        ]);
                    } else {
                        ThemeConfiguration::create([
                            'tenant_id' => $tenant->id,
                            'color_primary' => $validated['color_primary'],
                            'color_secondary' => $validated['color_secondary'],
                            'color_accent' => $validated['color_accent'],
                            'dark_mode_enabled' => $request->has('dark_mode_enabled'),
                        ]);
                    }
                    break;

                default:
                    // Tab no reconocido, actualizar solo campos básicos
                    $validated = $request->validate([
                        'name' => 'sometimes|string|max:255|unique:tenants,name,' . $id,
                        'domain' => 'sometimes|string|max:255|unique:tenants,domain,' . $id,
                        'status' => 'sometimes|in:active,trial,suspended',
                    ]);
                    $tenant->update($validated);
            }

            DB::commit();

            return redirect()
                ->route('admin.tenants.edit', $tenant->id)
                ->with('success', 'Tenant actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del tenant (activar/suspender)
     */
    public function toggleStatus($id)
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? "Tenant '{$tenant->name}' activado exitosamente"
            : "Tenant '{$tenant->name}' suspendido exitosamente";

        return back()->with('success', $message);
    }

    /**
     * Eliminar tenant (con confirmación)
     */
    public function destroy($id)
    {
        // El middleware IsSuperadminGlobal ya verificó la autenticación
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

