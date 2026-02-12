<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfigurationController extends Controller
{
    /**
     * Mostrar panel de configuración
     */
    public function index()
    {
        $tenant = $this->getCurrentTenant();

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'No se encontró el tenant.');
        }

        $theme = $tenant->themeConfiguration ?? new ThemeConfiguration();

        return view('configuration.index', [
            'tenant' => $tenant,
            'theme' => $theme,
            'settings' => $this->getSettings($tenant),
        ]);
    }

    /**
     * Actualizar información general del tenant
     */
    public function updateGeneral(Request $request)
    {
        $tenant = $this->getCurrentTenant();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
        ]);

        $tenant->update($validated);

        return redirect()->route('configuration.index')
            ->with('success', 'Información general actualizada correctamente.');
    }

    /**
     * Actualizar logo del tenant
     */
    public function updateLogo(Request $request)
    {
        $tenant = $this->getCurrentTenant();

        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'logo_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
        ]);

        $uploadPath = "tenants/{$tenant->id}/branding";

        // Procesar logo principal
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($tenant->logo_path && Storage::disk('public')->exists($tenant->logo_path)) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            $path = $request->file('logo')->store($uploadPath, 'public');
            $tenant->logo_path = $path;
        }

        // Procesar logo light (para fondos oscuros)
        if ($request->hasFile('logo_light')) {
            if ($tenant->logo_path_light && Storage::disk('public')->exists($tenant->logo_path_light)) {
                Storage::disk('public')->delete($tenant->logo_path_light);
            }
            $path = $request->file('logo_light')->store($uploadPath, 'public');
            $tenant->logo_path_light = $path;
        }

        // Procesar logo dark (para fondos claros)
        if ($request->hasFile('logo_dark')) {
            if ($tenant->logo_path_dark && Storage::disk('public')->exists($tenant->logo_path_dark)) {
                Storage::disk('public')->delete($tenant->logo_path_dark);
            }
            $path = $request->file('logo_dark')->store($uploadPath, 'public');
            $tenant->logo_path_dark = $path;
        }

        // Procesar favicon
        if ($request->hasFile('favicon')) {
            if ($tenant->favicon_path && Storage::disk('public')->exists($tenant->favicon_path)) {
                Storage::disk('public')->delete($tenant->favicon_path);
            }
            $path = $request->file('favicon')->store($uploadPath, 'public');
            $tenant->favicon_path = $path;
        }

        $tenant->save();

        return redirect()->route('configuration.index')
            ->with('success', 'Logos actualizados correctamente.');
    }

    /**
     * Eliminar un logo específico
     */
    public function deleteLogo(Request $request, string $type)
    {
        $tenant = $this->getCurrentTenant();

        $field = match($type) {
            'logo' => 'logo_path',
            'logo_light' => 'logo_path_light',
            'logo_dark' => 'logo_path_dark',
            'favicon' => 'favicon_path',
            default => null,
        };

        if ($field && $tenant->$field) {
            if (Storage::disk('public')->exists($tenant->$field)) {
                Storage::disk('public')->delete($tenant->$field);
            }
            $tenant->$field = null;
            $tenant->save();
        }

        return redirect()->route('configuration.index')
            ->with('success', 'Logo eliminado correctamente.');
    }

    /**
     * Actualizar tema/colores
     */
    public function updateTheme(Request $request)
    {
        $tenant = $this->getCurrentTenant();

        $validated = $request->validate([
            'color_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_accent' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'dark_mode_enabled' => ['boolean'],
        ]);

        $theme = $tenant->themeConfiguration ?? new ThemeConfiguration(['tenant_id' => $tenant->id]);

        $theme->fill([
            'color_primary' => $validated['color_primary'],
            'color_secondary' => $validated['color_secondary'],
            'color_accent' => $validated['color_accent'] ?? '#10b981',
            'dark_mode_enabled' => $request->boolean('dark_mode_enabled'),
        ]);

        $theme->tenant_id = $tenant->id;
        $theme->save();

        return redirect()->route('configuration.index')
            ->with('success', 'Tema actualizado correctamente.');
    }

    /**
     * Actualizar configuración de notificaciones
     */
    public function updateNotifications(Request $request)
    {
        $tenant = $this->getCurrentTenant();

        $settings = $this->getSettings($tenant);

        $settings['notifications'] = [
            'email_on_import' => $request->boolean('email_on_import'),
            'email_on_error' => $request->boolean('email_on_error'),
            'email_daily_summary' => $request->boolean('email_daily_summary'),
            'browser_notifications' => $request->boolean('browser_notifications'),
        ];

        $this->saveSettings($tenant, $settings);

        return redirect()->route('configuration.index')
            ->with('success', 'Configuración de notificaciones actualizada.');
    }

    /**
     * Actualizar configuración de importación
     */
    public function updateImport(Request $request)
    {
        $tenant = $this->getCurrentTenant();

        $validated = $request->validate([
            'default_import_mode' => ['required', 'in:create,update,skip'],
            'auto_create_terceros' => ['boolean'],
            'validate_duplicates' => ['boolean'],
            'date_format' => ['required', 'in:d/m/Y,Y-m-d,m/d/Y'],
            'decimal_separator' => ['required', 'in:.,'],
        ]);

        $settings = $this->getSettings($tenant);
        $settings['import'] = $validated;
        $settings['import']['auto_create_terceros'] = $request->boolean('auto_create_terceros');
        $settings['import']['validate_duplicates'] = $request->boolean('validate_duplicates');

        $this->saveSettings($tenant, $settings);

        return redirect()->route('configuration.index')
            ->with('success', 'Configuración de importación actualizada.');
    }

    /**
     * Exportar datos del tenant
     */
    public function exportData(Request $request)
    {
        $tenant = $this->getCurrentTenant();
        $type = $request->input('type', 'all');

        // TODO: Implementar exportación real según el tipo
        // Por ahora, retornar un mensaje de que está en desarrollo

        return redirect()->route('configuration.index')
            ->with('info', 'La funcionalidad de exportación estará disponible próximamente.');
    }

    /**
     * Obtener el tenant actual del usuario
     */
    private function getCurrentTenant(): ?Tenant
    {
        $tenantId = session('tenant_id');
        return Tenant::find($tenantId);
    }

    /**
     * Obtener configuración del tenant
     */
    private function getSettings(Tenant $tenant): array
    {
        // Obtener settings de la sesión o valores por defecto
        $defaults = [
            'notifications' => [
                'email_on_import' => true,
                'email_on_error' => true,
                'email_daily_summary' => false,
                'browser_notifications' => true,
            ],
            'import' => [
                'default_import_mode' => 'create',
                'auto_create_terceros' => true,
                'validate_duplicates' => true,
                'date_format' => 'd/m/Y',
                'decimal_separator' => ',',
            ],
        ];

        // TODO: En el futuro, guardar en tabla tenant_settings
        return session("tenant_settings_{$tenant->id}", $defaults);
    }

    /**
     * Guardar configuración del tenant
     */
    private function saveSettings(Tenant $tenant, array $settings): void
    {
        // TODO: En el futuro, guardar en tabla tenant_settings
        session(["tenant_settings_{$tenant->id}" => $settings]);
    }
}

