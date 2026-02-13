<?php

namespace App\Console\Commands;

use App\Services\Admin\TenantService;
use Illuminate\Console\Command;

/**
 * ============================================================================
 * COMANDO: DebugTenantWizard
 * ============================================================================
 *
 * Comando para probar la creación de tenants simulando el wizard.
 * Usa el TenantService para asegurar consistencia con el wizard web.
 *
 * Uso:
 *   php artisan debug:tenant-wizard
 *   php artisan debug:tenant-wizard --dry-run
 *   php artisan debug:tenant-wizard --name="Mi Empresa" --domain="miempresa.com"
 *
 * @author SGDEA Team
 * ============================================================================
 */
class DebugTenantWizard extends Command
{
    protected $signature = 'debug:tenant-wizard
                            {--name= : Nombre de la empresa}
                            {--domain= : Dominio de email}
                            {--admin-name= : Nombre del administrador}
                            {--admin-email= : Email del administrador}
                            {--admin-password= : Contraseña (opcional, se genera si no se provee)}
                            {--plan=professional : Plan (basic, professional, enterprise)}
                            {--status=active : Estado inicial (active, trial, suspended)}
                            {--dry-run : Solo validar, no crear}
                            {--verbose-steps : Mostrar pasos detallados}';

    protected $description = 'Debug: Simular creación de tenant como el wizard';

    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle(): int
    {
        $this->newLine();
        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('🔧 DEBUG TENANT WIZARD');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->newLine();

        // Recopilar datos
        $data = $this->collectData();

        if (empty($data)) {
            return Command::FAILURE;
        }

        // Mostrar resumen de datos
        $this->showDataSummary($data);

        // Validar datos
        $this->info('📋 PASO 1: Validando datos...');
        $this->line('───────────────────────────────────────────────────────────');

        $errors = $this->tenantService->validateTenantData($data);

        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ Se encontraron errores de validación:');
            foreach ($errors as $error) {
                $this->line("   • {$error}");
            }
            $this->newLine();
            return Command::FAILURE;
        }

        $this->info('✅ Validación exitosa');
        $this->newLine();

        // Si es dry-run, terminar aquí
        if ($this->option('dry-run')) {
            $this->warn('🔸 Modo dry-run: No se creó ningún registro');
            $this->info('   Los datos son válidos y se podrían crear sin problemas.');
            $this->newLine();
            return Command::SUCCESS;
        }

        // Crear tenant
        $this->info('📋 PASO 2: Creando tenant...');
        $this->line('───────────────────────────────────────────────────────────');

        $result = $this->tenantService->createTenant($data);

        // Mostrar pasos si verbose
        if ($this->option('verbose-steps')) {
            $this->newLine();
            $this->line('📝 Pasos ejecutados:');
            foreach ($result['steps'] as $step) {
                $icon = isset($step['id']) ? '✅' : '🔄';
                if (str_contains($step['action'] ?? '', '❌')) {
                    $icon = '❌';
                }
                $this->line("   {$icon} [{$step['step']}] {$step['action']}");
            }
            $this->newLine();
        }

        // Mostrar resultado
        if ($result['success']) {
            $this->newLine();
            $this->line('═══════════════════════════════════════════════════════════');
            $this->info('✅ TENANT CREADO EXITOSAMENTE');
            $this->line('═══════════════════════════════════════════════════════════');
            $this->newLine();

            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Tenant ID', $result['tenant']->id],
                    ['Nombre', $result['tenant']->name],
                    ['Slug', $result['tenant']->slug],
                    ['Dominio', $result['tenant']->domain],
                    ['Estado', $result['tenant']->status],
                    ['───────────', '───────────'],
                    ['Usuario ID', $result['user']->id],
                    ['Nombre Admin', $result['user']->name],
                    ['Email Admin', $result['user']->email],
                    ['Contraseña', $result['password']],
                ]
            );

            $this->newLine();
            $this->warn('⚠️  GUARDA ESTA CONTRASEÑA - No se mostrará de nuevo');
            $this->newLine();

            // Verificación extra
            $this->info('📋 PASO 3: Verificando datos creados...');
            $this->line('───────────────────────────────────────────────────────────');

            $this->verifyCreatedData($result['tenant']->id, $result['user']->id);

            return Command::SUCCESS;

        } else {
            $this->newLine();
            $this->error('❌ ERROR AL CREAR TENANT');
            foreach ($result['errors'] as $error) {
                $this->line("   • {$error}");
            }
            $this->newLine();
            return Command::FAILURE;
        }
    }

    /**
     * Recopilar datos del tenant
     */
    protected function collectData(): array
    {
        $name = $this->option('name');
        $domain = $this->option('domain');
        $adminName = $this->option('admin-name');
        $adminEmail = $this->option('admin-email');

        // Si no se proporcionaron opciones, usar datos de prueba
        if (empty($name) && empty($domain)) {
            $this->warn('📝 No se proporcionaron datos. Usando datos de prueba...');
            $this->newLine();

            $timestamp = now()->format('His');
            $name = "Empresa Test {$timestamp}";
            $domain = "test{$timestamp}.local";
            $adminName = "Admin Test";
            $adminEmail = "admin@test{$timestamp}.local";

            $this->line("   Nombre: {$name}");
            $this->line("   Dominio: {$domain}");
            $this->line("   Admin: {$adminName} <{$adminEmail}>");
            $this->newLine();

            if (!$this->confirm('¿Continuar con estos datos de prueba?', true)) {
                return [];
            }
        }

        // Completar datos faltantes
        if (empty($name)) {
            $name = $this->ask('Nombre de la empresa');
        }
        if (empty($domain)) {
            $domain = $this->ask('Dominio de email (ej: miempresa.com)');
        }
        if (empty($adminName)) {
            $adminName = $this->ask('Nombre del administrador', 'Administrador');
        }
        if (empty($adminEmail)) {
            $adminEmail = $this->ask("Email del administrador", "admin@{$domain}");
        }

        return [
            'company_name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'domain' => $domain,
            'admin_name' => $adminName,
            'admin_email' => $adminEmail,
            'admin_password' => $this->option('admin-password'),
            'plan' => $this->option('plan'),
            'status' => $this->option('status'),
            'color_primary' => '#2563eb',
            'color_secondary' => '#0f172a',
            'color_accent' => '#10b981',
            'dark_mode_enabled' => true,
            'created_by' => 1, // System user
        ];
    }

    /**
     * Mostrar resumen de datos
     */
    protected function showDataSummary(array $data): void
    {
        $this->info('📋 RESUMEN DE DATOS:');
        $this->line('───────────────────────────────────────────────────────────');

        $this->table(
            ['Campo', 'Valor'],
            [
                ['Empresa', $data['company_name']],
                ['Slug', $data['slug']],
                ['Dominio', $data['domain']],
                ['Plan', $data['plan'] ?? 'professional'],
                ['Estado', $data['status'] ?? 'active'],
                ['───────────', '───────────'],
                ['Admin Nombre', $data['admin_name']],
                ['Admin Email', $data['admin_email']],
                ['Contraseña', $data['admin_password'] ? '(proporcionada)' : '(se generará)'],
            ]
        );

        $this->newLine();
    }

    /**
     * Verificar datos creados en la BD
     */
    protected function verifyCreatedData(int $tenantId, int $userId): void
    {
        // Verificar tenant
        $tenant = \App\Models\Tenant::find($tenantId);
        $tenantOk = $tenant !== null;
        $this->line("   " . ($tenantOk ? '✅' : '❌') . " Tenant en BD: " . ($tenantOk ? "ID {$tenantId}" : 'NO ENCONTRADO'));

        // Verificar theme
        $theme = \App\Models\ThemeConfiguration::where('tenant_id', $tenantId)->first();
        $themeOk = $theme !== null;
        $this->line("   " . ($themeOk ? '✅' : '❌') . " Theme en BD: " . ($themeOk ? "ID {$theme->id}" : 'NO ENCONTRADO'));

        // Verificar rol
        $role = \App\Models\Role::where('tenant_id', $tenantId)->where('slug', 'administrador')->first();
        $roleOk = $role !== null;
        $this->line("   " . ($roleOk ? '✅' : '❌') . " Rol Admin en BD: " . ($roleOk ? "ID {$role->id}" : 'NO ENCONTRADO'));

        // Verificar usuario
        $user = \App\Models\User::find($userId);
        $userOk = $user !== null;
        $this->line("   " . ($userOk ? '✅' : '❌') . " Usuario en BD: " . ($userOk ? "ID {$userId}" : 'NO ENCONTRADO'));

        // Verificar relaciones
        if ($userOk) {
            $this->line("   " . ($user->tenant_id == $tenantId ? '✅' : '❌') . " Usuario->Tenant: " . ($user->tenant_id == $tenantId ? 'Correcto' : 'INCORRECTO'));
            $this->line("   " . ($user->role_id == $role?->id ? '✅' : '❌') . " Usuario->Rol: " . ($user->role_id == $role?->id ? 'Correcto' : 'INCORRECTO'));
            $this->line("   " . ($user->status == 'active' ? '✅' : '⚠️') . " Usuario Status: {$user->status}");
        }

        $this->newLine();

        if ($tenantOk && $themeOk && $roleOk && $userOk) {
            $this->info('✅ Todos los datos fueron verificados correctamente');
        } else {
            $this->error('❌ Algunos datos no se crearon correctamente');
        }

        $this->newLine();
    }
}

