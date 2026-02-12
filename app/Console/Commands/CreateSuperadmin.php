<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\SystemUser;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * ============================================================================
 * COMANDO: app:create-superadmin
 * ============================================================================
 *
 * Crea un usuario superadministrador para un tenant.
 *
 * USO:
 *   php artisan app:create-superadmin                    # Interactivo
 *   php artisan app:create-superadmin --tenant=1         # Para tenant específico
 *   php artisan app:create-superadmin --global           # Superadmin global (system_users)
 *   php artisan app:create-superadmin --force            # Sin confirmaciones
 *
 * OPCIONES:
 *   --tenant=ID     : ID del tenant donde crear el usuario
 *   --name=         : Nombre del usuario
 *   --email=        : Email del usuario
 *   --password=     : Contraseña (si no se provee, se genera una)
 *   --global        : Crear superadmin global (tabla system_users)
 *   --force         : Sin confirmaciones
 *
 * @author SGDEA Team
 * ============================================================================
 */
class CreateSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:create-superadmin
                            {--tenant= : ID del tenant donde crear el usuario}
                            {--name= : Nombre completo del usuario}
                            {--email= : Email del usuario}
                            {--password= : Contraseña (opcional, se genera si no se provee)}
                            {--global : Crear como superadmin global (system_users)}
                            {--force : Ejecutar sin confirmaciones}';

    /**
     * The console command description.
     */
    protected $description = 'Crear un usuario superadministrador para el sistema';

    /**
     * Si estamos en producción
     */
    protected bool $isProduction;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->isProduction = app()->isProduction();

        $this->showHeader();

        // Determinar si es global o por tenant
        if ($this->option('global')) {
            return $this->createGlobalSuperadmin();
        }

        return $this->createTenantSuperadmin();
    }

    /**
     * Mostrar encabezado
     */
    protected function showHeader(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║              SGDEA - CREAR SUPERADMINISTRADOR                ║');
        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Crear superadmin global (system_users)
     */
    protected function createGlobalSuperadmin(): int
    {
        $this->info('🌐 Creando Superadmin GLOBAL (acceso a todos los tenants)...');
        $this->newLine();

        // Obtener datos
        $name = $this->option('name') ?? $this->ask('Nombre completo', 'Super Admin');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password');

        $generatePassword = false;
        if (!$password) {
            $generatePassword = $this->confirm('¿Generar contraseña automáticamente?', true);
            if ($generatePassword) {
                $password = Str::random(16);
            } else {
                $password = $this->secret('Contraseña (mínimo 8 caracteres)');
                $passwordConfirm = $this->secret('Confirmar contraseña');

                if ($password !== $passwordConfirm) {
                    $this->error('❌ Las contraseñas no coinciden');
                    return Command::FAILURE;
                }
            }
        }

        // Validar datos
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:system_users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Errores de validación:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("   • $error");
            }
            return Command::FAILURE;
        }

        // Confirmación en producción
        if ($this->isProduction && !$this->option('force')) {
            $this->warn('⚠️  Estás en PRODUCCIÓN');
            $this->table(['Campo', 'Valor'], [
                ['Nombre', $name],
                ['Email', $email],
                ['Tipo', 'Superadmin Global'],
            ]);

            if (!$this->confirm('¿Crear este usuario?', false)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Crear usuario
        try {
            $user = SystemUser::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->showSuccess($user, $password, $generatePassword, true);

        } catch (\Exception $e) {
            $this->error('❌ Error al crear usuario: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Crear superadmin de tenant
     */
    protected function createTenantSuperadmin(): int
    {
        $this->info('🏢 Creando Superadmin de TENANT...');
        $this->newLine();

        // Obtener tenant
        $tenantId = $this->option('tenant');

        if (!$tenantId) {
            // Listar tenants disponibles
            $tenants = Tenant::where('status', 'active')->get(['id', 'name', 'slug']);

            if ($tenants->isEmpty()) {
                $this->error('❌ No hay tenants disponibles. Ejecuta primero: php artisan app:setup --fresh');
                return Command::FAILURE;
            }

            $this->table(['ID', 'Nombre', 'Slug'], $tenants->toArray());
            $tenantId = $this->ask('ID del tenant');
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("❌ Tenant con ID {$tenantId} no encontrado");
            return Command::FAILURE;
        }

        $this->info("   📍 Tenant seleccionado: {$tenant->name}");
        $this->newLine();

        // Obtener datos del usuario
        $name = $this->option('name') ?? $this->ask('Nombre completo', 'Administrador');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password');

        $generatePassword = false;
        if (!$password) {
            $generatePassword = $this->confirm('¿Generar contraseña automáticamente?', true);
            if ($generatePassword) {
                $password = Str::random(16);
            } else {
                $password = $this->secret('Contraseña (mínimo 8 caracteres)');
                $passwordConfirm = $this->secret('Confirmar contraseña');

                if ($password !== $passwordConfirm) {
                    $this->error('❌ Las contraseñas no coinciden');
                    return Command::FAILURE;
                }
            }
        }

        // Validar datos
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Errores de validación:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("   • $error");
            }
            return Command::FAILURE;
        }

        // Obtener rol de administrador
        $adminRole = Role::where('tenant_id', $tenant->id)
            ->where('slug', 'administrador')
            ->first();

        if (!$adminRole) {
            $this->warn('⚠️  No existe el rol "administrador" para este tenant.');
            if ($this->confirm('¿Crear usuario sin rol asignado?', false)) {
                $adminRole = null;
            } else {
                $this->info('Ejecuta primero los seeders para crear los roles.');
                return Command::FAILURE;
            }
        }

        // Confirmación en producción
        if ($this->isProduction && !$this->option('force')) {
            $this->warn('⚠️  Estás en PRODUCCIÓN');
            $this->table(['Campo', 'Valor'], [
                ['Nombre', $name],
                ['Email', $email],
                ['Tenant', $tenant->name],
                ['Rol', $adminRole?->name ?? 'Sin rol'],
            ]);

            if (!$this->confirm('¿Crear este usuario?', false)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Crear usuario
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'tenant_id' => $tenant->id,
                'role_id' => $adminRole?->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'approved_at' => now(),
            ]);

            // Asignar rol en tabla pivote
            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
            }

            $this->showSuccess($user, $password, $generatePassword, false, $tenant);

        } catch (\Exception $e) {
            $this->error('❌ Error al crear usuario: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Mostrar mensaje de éxito
     */
    protected function showSuccess($user, string $password, bool $generated, bool $isGlobal, ?Tenant $tenant = null): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║              ✅ USUARIO CREADO EXITOSAMENTE                  ║');
        $this->line('╠══════════════════════════════════════════════════════════════╣');
        $this->line('║  Datos de acceso:                                            ║');
        $this->line('║    • Email: ' . str_pad($user->email, 46) . '║');

        if ($generated) {
            $this->line('║    • Contraseña: ' . str_pad($password, 41) . '║');
            $this->line('║                                                              ║');
            $this->line('║  ⚠️  GUARDA ESTA CONTRASEÑA - No se mostrará de nuevo       ║');
        } else {
            $this->line('║    • Contraseña: (la que ingresaste)                         ║');
        }

        $this->line('╠══════════════════════════════════════════════════════════════╣');

        if ($isGlobal) {
            $this->line('║  Tipo: Superadmin GLOBAL                                     ║');
            $this->line('║  Acceso: Panel de administración global                      ║');
        } else {
            $this->line('║  Tenant: ' . str_pad($tenant->name, 48) . '║');
            $this->line('║  URL: ' . str_pad(config('app.url'), 51) . '║');
        }

        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }
}

