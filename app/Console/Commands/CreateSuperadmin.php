<?php

namespace App\Console\Commands;

use App\Models\Role;
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
 * Crea un usuario superadministrador.
 *
 * USO:
 *   php artisan app:create-superadmin                    # Interactivo
 *   php artisan app:create-superadmin --global           # Superadmin global
 *   php artisan app:create-superadmin --tenant=1         # Admin para tenant
 *   php artisan app:create-superadmin --force            # Sin confirmaciones
 *
 * TIPOS:
 *   --global        : Crea en tabla users con tenant_id=NULL y rol superadmin_global
 *   --tenant=ID     : Crea admin para un tenant específico
 *
 * @author SGDEA Team
 * ============================================================================
 */
class CreateSuperadmin extends Command
{
    protected $signature = 'app:create-superadmin
                            {--global : Crear superadmin global (acceso a todos los tenants)}
                            {--tenant= : ID del tenant donde crear el admin}
                            {--name= : Nombre completo del usuario}
                            {--email= : Email del usuario}
                            {--password= : Contraseña (opcional, se genera si no se provee)}
                            {--force : Ejecutar sin confirmaciones}';

    protected $description = 'Crear un usuario superadministrador';

    public function handle(): int
    {
        $this->showHeader();

        if ($this->option('global')) {
            return $this->createGlobalSuperadmin();
        }

        return $this->createTenantAdmin();
    }

    protected function showHeader(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║              SGDEA - CREAR SUPERADMINISTRADOR                ║');
        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Crear superadmin global (tenant_id = NULL, rol = superadmin_global)
     */
    protected function createGlobalSuperadmin(): int
    {
        $this->info('🌐 Creando Superadmin GLOBAL...');
        $this->newLine();

        // Obtener o crear el rol superadmin_global
        $role = Role::where('slug', 'superadmin_global')
            ->whereNull('tenant_id')
            ->first();

        if (!$role) {
            $this->warn('   ⚠️ Rol superadmin_global no existe. Creándolo...');
            $role = Role::create([
                'tenant_id' => null,
                'name' => 'Superadmin Global',
                'slug' => 'superadmin_global',
                'description' => 'Acceso total a todos los tenants',
                'is_system' => true,
                'priority' => 1000,
            ]);
            $this->info('   ✅ Rol superadmin_global creado (ID: ' . $role->id . ')');
        }

        // Obtener datos del usuario
        $name = $this->option('name') ?? $this->ask('Nombre completo', 'Super Admin');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->getPassword();

        if (!$password) {
            return Command::FAILURE;
        }

        // Validar
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password['plain'],
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

        // Confirmación en producción
        if (app()->isProduction() && !$this->option('force')) {
            $this->warn('⚠️  Estás en PRODUCCIÓN');
            if (!$this->confirm('¿Crear este usuario?', false)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Crear usuario
        try {
            $user = User::create([
                'tenant_id' => null, // Sin tenant = global
                'role_id' => $role->id,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password['plain']),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->showSuccess($user, $password, 'Superadmin GLOBAL');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Crear admin para un tenant
     */
    protected function createTenantAdmin(): int
    {
        $this->info('🏢 Creando Admin de TENANT...');
        $this->newLine();

        // Obtener tenant
        $tenantId = $this->option('tenant');

        if (!$tenantId) {
            $tenants = Tenant::all(['id', 'name', 'slug']);

            if ($tenants->isEmpty()) {
                $this->error('❌ No hay tenants. Cree uno primero.');
                return Command::FAILURE;
            }

            $this->table(['ID', 'Nombre', 'Slug'], $tenants->toArray());
            $tenantId = $this->ask('ID del tenant');
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error('❌ Tenant no encontrado');
            return Command::FAILURE;
        }

        $this->info("   📍 Tenant: {$tenant->name}");

        // Obtener rol admin del tenant
        $role = Role::where('slug', 'administrador')
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$role) {
            $this->error('❌ Rol administrador no existe para este tenant. Ejecute RoleSeeder.');
            return Command::FAILURE;
        }

        // Obtener datos del usuario
        $name = $this->option('name') ?? $this->ask('Nombre completo', 'Admin ' . $tenant->name);
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->getPassword();

        if (!$password) {
            return Command::FAILURE;
        }

        // Validar
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password['plain'],
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

        // Crear usuario
        try {
            $user = User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password['plain']),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->showSuccess($user, $password, "Admin de {$tenant->name}");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Obtener contraseña (generada o ingresada)
     */
    protected function getPassword(): ?array
    {
        $password = $this->option('password');
        $generated = false;

        if (!$password) {
            $generate = $this->confirm('¿Generar contraseña automáticamente?', true);

            if ($generate) {
                $password = Str::random(16);
                $generated = true;
            } else {
                $password = $this->secret('Contraseña (mínimo 8 caracteres)');
                $confirm = $this->secret('Confirmar contraseña');

                if ($password !== $confirm) {
                    $this->error('❌ Las contraseñas no coinciden');
                    return null;
                }
            }
        }

        return [
            'plain' => $password,
            'generated' => $generated,
        ];
    }

    /**
     * Mostrar mensaje de éxito
     */
    protected function showSuccess(User $user, array $password, string $tipo): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║              ✅ USUARIO CREADO EXITOSAMENTE                  ║');
        $this->line('╠══════════════════════════════════════════════════════════════╣');
        $this->line('║  Datos de acceso:                                            ║');
        $this->line('║    • Email: ' . str_pad($user->email, 45) . '║');
        $this->line('║    • Contraseña: ' . str_pad($password['plain'], 39) . '║');
        $this->line('║                                                              ║');

        if ($password['generated']) {
            $this->line('║  ⚠️  GUARDA ESTA CONTRASEÑA - No se mostrará de nuevo       ║');
        }

        $this->line('╠══════════════════════════════════════════════════════════════╣');
        $this->line('║  Tipo: ' . str_pad($tipo, 50) . '║');
        $this->line('║  URL: ' . str_pad(url('/login'), 51) . '║');
        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }
}

