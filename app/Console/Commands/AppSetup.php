<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * ============================================================================
 * COMANDO: app:setup
 * ============================================================================
 *
 * Configura la aplicación SGDEA desde cero.
 *
 * USO:
 *   php artisan app:setup              # Interactivo (pide confirmaciones en producción)
 *   php artisan app:setup --force      # Sin confirmaciones (para CI/CD)
 *   php artisan app:setup --fresh      # Resetea BD completamente
 *   php artisan app:setup --seed       # Solo ejecutar seeders
 *   php artisan app:setup --migrate    # Solo ejecutar migraciones
 *
 * CARACTERÍSTICAS:
 * - Detecta entorno (local/development vs production)
 * - En producción pide confirmaciones
 * - Configura timezone a America/Bogota
 * - Configura tema oscuro por defecto
 * - Ejecuta migraciones y seeders
 * - Limpia cachés
 *
 * @author SGDEA Team
 * ============================================================================
 */
class AppSetup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:setup
                            {--force : Ejecutar sin confirmaciones (para CI/CD)}
                            {--fresh : Resetear base de datos completamente (migrate:fresh)}
                            {--seed : Solo ejecutar seeders (sin migraciones)}
                            {--migrate : Solo ejecutar migraciones (sin seeders)}
                            {--skip-env : No modificar archivo .env}';

    /**
     * The console command description.
     */
    protected $description = 'Configura la aplicación SGDEA: migraciones, seeders, timezone y tema';

    /**
     * Entorno actual
     */
    protected string $environment;

    /**
     * Si estamos en producción
     */
    protected bool $isProduction;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->environment = app()->environment();
        $this->isProduction = app()->isProduction();

        $this->showHeader();

        // Verificar confirmación en producción
        if ($this->isProduction && !$this->option('force')) {
            $this->warn('⚠️  ATENCIÓN: Estás en entorno de PRODUCCIÓN');
            $this->newLine();

            if (!$this->confirm('¿Estás seguro de que deseas continuar con el setup?', false)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Paso 1: Configurar .env
        if (!$this->option('skip-env')) {
            $this->configureEnvironment();
        }

        // Paso 2: Ejecutar migraciones
        if (!$this->option('seed')) {
            $this->runMigrations();
        }

        // Paso 3: Ejecutar seeders
        if (!$this->option('migrate')) {
            $this->runSeeders();
        }

        // Paso 4: Limpiar cachés
        $this->clearCaches();

        // Paso 5: Configurar tema oscuro por defecto
        $this->configureDarkMode();

        $this->showSummary();

        return Command::SUCCESS;
    }

    /**
     * Mostrar encabezado del comando
     */
    protected function showHeader(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║                    SGDEA - APP SETUP                         ║');
        $this->line('║          Sistema de Gestión Documental Electrónica           ║');
        $this->line('╠══════════════════════════════════════════════════════════════╣');
        $this->line('║  Entorno: ' . str_pad(strtoupper($this->environment), 20) . ($this->isProduction ? '⚠️  PRODUCCIÓN' : '🔧 Desarrollo') . '  ║');
        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Configurar variables de entorno
     */
    protected function configureEnvironment(): void
    {
        $this->info('📝 Paso 1: Configurando entorno...');

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('   ❌ Archivo .env no encontrado');
            return;
        }

        $envContent = File::get($envPath);
        $modified = false;

        // Configurar timezone a America/Bogota
        if (strpos($envContent, 'APP_TIMEZONE=') === false) {
            $envContent .= "\nAPP_TIMEZONE=America/Bogota\n";
            $modified = true;
            $this->line('   ✅ Agregado APP_TIMEZONE=America/Bogota');
        } else {
            $envContent = preg_replace(
                '/APP_TIMEZONE=.*/',
                'APP_TIMEZONE=America/Bogota',
                $envContent
            );
            $modified = true;
            $this->line('   ✅ Configurado APP_TIMEZONE=America/Bogota');
        }

        // Configurar locale a español
        if (strpos($envContent, 'APP_LOCALE=') === false) {
            $envContent .= "APP_LOCALE=es\n";
            $modified = true;
            $this->line('   ✅ Agregado APP_LOCALE=es');
        } else {
            $envContent = preg_replace(
                '/APP_LOCALE=.*/',
                'APP_LOCALE=es',
                $envContent
            );
            $modified = true;
            $this->line('   ✅ Configurado APP_LOCALE=es');
        }

        // En producción, asegurar debug desactivado
        if ($this->isProduction) {
            $envContent = preg_replace(
                '/APP_DEBUG=.*/',
                'APP_DEBUG=false',
                $envContent
            );
            $modified = true;
            $this->line('   ✅ APP_DEBUG=false (producción)');
        }

        if ($modified) {
            File::put($envPath, $envContent);
        }

        $this->newLine();
    }

    /**
     * Ejecutar migraciones
     */
    protected function runMigrations(): void
    {
        $this->info('🗄️  Paso 2: Ejecutando migraciones...');

        $isFresh = $this->option('fresh');

        if ($isFresh) {
            if ($this->isProduction && !$this->option('force')) {
                $this->warn('   ⚠️  migrate:fresh ELIMINARÁ TODOS LOS DATOS');
                if (!$this->confirm('   ¿Continuar con migrate:fresh?', false)) {
                    $this->line('   ⏭️  Migraciones omitidas');
                    $this->newLine();
                    return;
                }
            }

            $this->line('   🔄 Ejecutando migrate:fresh...');
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->line('   ✅ migrate:fresh completado');
        } else {
            if ($this->isProduction && !$this->option('force')) {
                if (!$this->confirm('   ¿Ejecutar migraciones pendientes?', true)) {
                    $this->line('   ⏭️  Migraciones omitidas');
                    $this->newLine();
                    return;
                }
            }

            $this->line('   🔄 Ejecutando migrate...');
            Artisan::call('migrate', ['--force' => true]);
            $this->line('   ✅ Migraciones completadas');
        }

        $this->newLine();
    }

    /**
     * Ejecutar seeders
     */
    protected function runSeeders(): void
    {
        $this->info('🌱 Paso 3: Ejecutando seeders...');

        if ($this->isProduction && !$this->option('force')) {
            $this->warn('   ⚠️  Los seeders insertarán datos iniciales en la BD');
            if (!$this->confirm('   ¿Ejecutar seeders?', false)) {
                $this->line('   ⏭️  Seeders omitidos');
                $this->newLine();
                return;
            }
        }

        $this->line('   🔄 Ejecutando db:seed...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->line('   ✅ Seeders completados');
        $this->newLine();
    }

    /**
     * Limpiar cachés
     */
    protected function clearCaches(): void
    {
        $this->info('🧹 Paso 4: Limpiando cachés...');

        Artisan::call('config:clear');
        $this->line('   ✅ Config cache limpiado');

        Artisan::call('route:clear');
        $this->line('   ✅ Route cache limpiado');

        Artisan::call('view:clear');
        $this->line('   ✅ View cache limpiado');

        Artisan::call('cache:clear');
        $this->line('   ✅ Application cache limpiado');

        // En producción, reconstruir cachés
        if ($this->isProduction) {
            $this->newLine();
            $this->line('   🔄 Reconstruyendo cachés de producción...');

            Artisan::call('config:cache');
            $this->line('   ✅ Config cache generado');

            Artisan::call('route:cache');
            $this->line('   ✅ Route cache generado');

            Artisan::call('view:cache');
            $this->line('   ✅ View cache generado');
        }

        $this->newLine();
    }

    /**
     * Configurar modo oscuro por defecto
     */
    protected function configureDarkMode(): void
    {
        $this->info('🌙 Paso 5: Configurando tema oscuro por defecto...');

        try {
            // Buscar la configuración de tema existente
            $themeConfig = \App\Models\ThemeConfiguration::first();

            if ($themeConfig) {
                $themeConfig->update(['dark_mode_enabled' => true]);
                $this->line('   ✅ Modo oscuro habilitado en configuración del tenant');
            } else {
                $this->line('   ⏭️  No hay configuración de tema (se creará con el primer tenant)');
            }
        } catch (\Exception $e) {
            $this->warn('   ⚠️  No se pudo configurar tema: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Mostrar resumen final
     */
    protected function showSummary(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════════════╗');
        $this->line('║                    ✅ SETUP COMPLETADO                       ║');
        $this->line('╠══════════════════════════════════════════════════════════════╣');
        $this->line('║  Configuraciones aplicadas:                                  ║');
        $this->line('║    • Timezone: America/Bogota                                ║');
        $this->line('║    • Locale: es                                              ║');
        $this->line('║    • Tema oscuro: habilitado                                 ║');
        $this->line('╠══════════════════════════════════════════════════════════════╣');

        if ($this->isProduction) {
            $this->line('║  🔒 MODO PRODUCCIÓN                                          ║');
            $this->line('║    • Solo se crearon: SYSTEM, permisos, roles globales       ║');
            $this->line('║    • NO se crearon tenants ni usuarios de ejemplo            ║');
            $this->line('╠══════════════════════════════════════════════════════════════╣');
            $this->line('║  Próximos pasos:                                             ║');
            $this->line('║    1. Crear superadmin global:                               ║');
            $this->line('║       php artisan app:create-superadmin --global             ║');
            $this->line('║    2. Acceder al sistema y crear tenants                     ║');
            $this->line('║    3. Configurar SSL y dominio                               ║');
            $this->line('║    4. Configurar backups automáticos                         ║');
        } else {
            $this->line('║  Credenciales de acceso (solo desarrollo):                   ║');
            $this->line('║    • admin@demo.sgdea.local / Admin123!                      ║');
            $this->line('║    • Usuario SYSTEM (ID=1) creado para auditoría             ║');
            $this->line('╠══════════════════════════════════════════════════════════════╣');
            $this->line('║  Próximos pasos:                                             ║');
            $this->line('║    1. Acceder a: http://localhost:8080                       ║');
            $this->line('║    2. Login con: admin@demo.sgdea.local / Admin123!          ║');
        }

        $this->line('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }
}

