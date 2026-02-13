<?php

namespace App\Http\Controllers;

use App\Models\SetupCheckpoint;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use App\Services\SetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
    protected $setupService;

    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }

    /**
     * Mostrar wizard de setup
     */
    public function show()
    {
        // Verificar si setup ya está completado
        if (file_exists(storage_path('.setup_completed'))) {
            abort(403, 'El sistema ya ha sido configurado.');
        }

        // Inicializar checkpoints si no existen
        if (SetupCheckpoint::count() === 0) {
            $this->setupService->initializeCheckpoints();
        }

        $allSteps = $this->setupService->getAllSteps();
        $progress = $this->setupService->getProgress();

        // Transformar los pasos al formato esperado por la vista
        $steps = collect($allSteps)->map(function ($step) {
            return [
                'paso_clave' => $step->step_key,
                'nombre' => $step->step_name,
                'descripcion' => $step->component ?? 'Complete este paso para continuar',
                'status' => $step->status,
                'orden' => $step->step_order,
            ];
        })->sortBy('orden')->values()->toArray();

        return view('setup.wizard', [
            'steps' => $steps,
            'totalSteps' => count($steps),
            'progress' => $progress,
            'systemLogo' => '/images/logo/logo_sgdea_blanco.png',
            'systemName' => 'SGDEA',
            'systemSubtitle' => 'Sistema de Gestión Documental y Fiscal',
        ]);
    }

    /**
     * Procesar paso del setup
     */
    public function process(Request $request)
    {
        try {
            $stepKey = $request->input('step_key');
            $data = $request->input();
            if (!$stepKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'step_key no proporcionado',
                ], 400);
            }

            // Mapear nombres de campos del formulario a nombres esperados por los métodos
            $mappedData = $this->mapFormData($data, $stepKey);

            // En pasos 1-5: solo VALIDAR, no crear
            // En paso 6: EJECUTAR todo lo validado

            if ($stepKey === 'setup_step_verification_passed') {
                // PASO FINAL: Crear todo basado en datos validados
                $result = $this->executeSetup($mappedData);
            } else {
                // PASOS 1-5: Solo validar
                $result = $this->validateStep($stepKey, $mappedData);
            }

            if ($result['success']) {
                $this->setupService->completeStep($stepKey);

                // Guardar datos en sesión para uso posterior
                session(['setup_step_' . $stepKey => $mappedData]);
            } else {
                $this->setupService->failStep($stepKey, $result['message']);
            }

            $nextStep = $this->setupService->getNextStep();
            $progress = $this->setupService->getProgress();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? [],
                'nextStep' => $nextStep,
                'progress' => $progress,
                'setupComplete' => $this->setupService->isSetupComplete(),
            ]);
        } catch (\Exception $e) {
            Log::error('SetupController.process error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'step_key' => $stepKey ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'error_detail' => $e->getFile() . ':' . $e->getLine(),
                'error_trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }

    }

    /**
     * Validar un paso específico (Pasos 1-5)
     * SIN CREAR NADA EN BD
     */
    private function validateStep($stepKey, $data)
    {
        return match ($stepKey) {
            'setup_step_superadmin_created' => $this->validateSuperadminStep($data),
            'setup_step_mysql_connected' => $this->validateMySQLStep($data),
            'setup_step_first_tenant_and_theme' => $this->validateTenantAndThemeStep($data),
            'setup_step_email_configured' => $this->validateEmailStep($data),
            'setup_step_ldap_configured' => $this->validateLDAPStep($data),
            'setup_step_verification_passed' => ['success' => true, 'message' => 'Ready to execute'],
            default => ['success' => false, 'message' => 'Paso no reconocido'],
        };
    }

    /**
     * PASO 1: Validar Superadmin
     */
    private function validateSuperadminStep($data)
    {
        try {
            // Validar formato y contenido
            if (empty($data['email']) || empty($data['name']) || empty($data['password'])) {
                throw new \Exception('Datos incompletos para superadmin');
            }

            if (strlen($data['password']) < 8) {
                throw new \Exception('Contraseña debe tener mínimo 8 caracteres');
            }

            if ($data['password'] !== ($data['password_confirmation'] ?? null)) {
                throw new \Exception('Las contraseñas no coinciden');
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email inválido');
            }

            return [
                'success' => true,
                'message' => 'Datos del superadmin validados correctamente',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO 2: Validar MySQL
     * Solo valida campos, la conexión se prueba con el botón de test
     */
    private function validateMySQLStep($data)
    {
        try {
            // Validar datos del servidor MySQL (solo lectura, no se guardan)
            if (empty($data['host']) || empty($data['port']) || empty($data['root_user'])) {
                throw new \Exception('Host, puerto y usuario root son requeridos');
            }

            // Validar datos de la BD a crear
            if (empty($data['database_name']) || empty($data['database_user']) || empty($data['database_password'])) {
                throw new \Exception('Nombre BD, usuario y contraseña son requeridos');
            }

            if ($data['database_password'] !== ($data['database_password_confirmation'] ?? null)) {
                throw new \Exception('Las contraseñas de BD no coinciden');
            }

            if (strlen($data['database_password']) < 8) {
                throw new \Exception('Contraseña BD debe tener mínimo 8 caracteres');
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['database_name'])) {
                throw new \Exception('Nombre BD inválido');
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['database_user'])) {
                throw new \Exception('Usuario BD inválido');
            }

            return [
                'success' => true,
                'message' => 'Configuración de BD validada correctamente',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO 3: Validar Sistema BD
     * (Solo validar que no hay errores previos)
     */
    private function validateSystemDBStep($data)
    {
        try {
            // Validar que tenemos datos de MySQL de pasos anteriores
            $mysqlData = session('setup_step_setup_step_mysql_connected');

            if (!$mysqlData) {
                throw new \Exception('Datos de MySQL no encontrados. Completa el paso anterior.');
            }

            return [
                'success' => true,
                'message' => 'Sistema listo para crear BD',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO 3 combinado: Validar Primer Tenant Y Tema
     */
    private function validateTenantAndThemeStep($data)
    {
        try {
            // Tenant
            if (empty($data['company_name']) || empty($data['domain'])) {
                throw new \Exception('Nombre de empresa y dominio son requeridos');
            }

            if (!preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/', strtolower($data['domain']))) {
                throw new \Exception('Dominio inválido');
            }

            // Theme (color optional but if present validate format)
            if (!empty($data['color_primary'])) {
                if (!preg_match('/^#[0-9A-F]{6}$/i', $data['color_primary'])) {
                    throw new \Exception('Color primario inválido');
                }
            }

            // logo_file optional => no validation here (handled on upload later)

            return [
                'success' => true,
                'message' => 'Tenant y Tema validados correctamente',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO 5: Validar Tema
     */
    private function validateThemeStep($data)
    {
        try {
            // Validar color
            if (!empty($data['color_primary'])) {
                if (!preg_match('/^#[0-9A-F]{6}$/i', $data['color_primary'])) {
                    throw new \Exception('Color primario inválido');
                }
            }

            return [
                'success' => true,
                'message' => 'Configuración de tema validada correctamente',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO (Opcional): Validar Configuración Email
     */
    private function validateEmailStep($data)
    {
        try {
            // Si usuario decide no configurar, permitir campos vacíos
            if (empty($data['mail_host']) && empty($data['mail_username']) && empty($data['mail_from_address'])) {
                return [
                    'success' => true,
                    'message' => 'Email no configurado (opcional)'
                ];
            }

            if (empty($data['mail_host']) || empty($data['mail_port']) || empty($data['mail_username']) || empty($data['mail_from_address'])) {
                throw new \Exception('Host, puerto, usuario SMTP y email de notificaciones son requeridos');
            }

            if (!filter_var($data['mail_from_address'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email de notificaciones inválido');
            }

            if (!is_numeric($data['mail_port']) || $data['mail_port'] < 1 || $data['mail_port'] > 65535) {
                throw new \Exception('Puerto SMTP inválido');
            }

            return [
                'success' => true,
                'message' => 'Configuración de email validada correctamente',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO (Opcional): Validar Configuración LDAP
     */
    private function validateLDAPStep($data)
    {
        try {
            if (empty($data['ldap_host']) && empty($data['ldap_base_dn'])) {
                return [
                    'success' => true,
                    'message' => 'LDAP no configurado (opcional)'
                ];
            }

            if (empty($data['ldap_host']) || empty($data['ldap_port']) || empty($data['ldap_base_dn'])) {
                throw new \Exception('Host LDAP, puerto y Base DN son requeridos');
            }

            if (!is_numeric($data['ldap_port']) || $data['ldap_port'] < 1 || $data['ldap_port'] > 65535) {
                throw new \Exception('Puerto LDAP inválido');
            }

            return [
                'success' => true,
                'message' => 'Configuración LDAP validada correctamente',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * PASO FINAL (6): Ejecutar Setup Completo
     * Crear TODO en BD basado en datos validados
     */
    private function executeSetup($data)
    {
        try {
            // Obtener todos los datos validados de sesión
            $superadminGlobalData = session('setup_step_setup_step_superadmin_created') ?? session('setup_step_superadmin_created');
            $mysqlData = session('setup_step_setup_step_mysql_connected') ?? session('setup_step_mysql_connected');
            $tenantAndThemeData = session('setup_step_setup_step_first_tenant_and_theme') ?? session('setup_step_first_tenant_and_theme') ?? session('setup_step_first_tenant_created') ?? session('setup_step_theme_applied');

            if (!$superadminGlobalData || !$mysqlData || !$tenantAndThemeData) {
                throw new \Exception('Datos incompletos. Completa todos los pasos.');
            }

            // 1. CREAR BD Y USUARIO
            $this->createDatabaseAndUser($mysqlData);

            // 2. EJECUTAR MIGRACIONES
            $this->executeMigrations($mysqlData);

            // 3. CREAR SUPERADMIN GLOBAL EN BD (tabla users con tenant_id=NULL)
            // Primero obtener o crear el rol superadmin_global
            $superadminRole = Role::where('slug', 'superadmin_global')->whereNull('tenant_id')->first();

            if (!$superadminRole) {
                $superadminRole = Role::create([
                    'tenant_id' => null,
                    'name' => 'Superadmin Global',
                    'slug' => 'superadmin_global',
                    'description' => 'Acceso total a todos los tenants',
                    'is_system' => true,
                    'priority' => 1000,
                ]);
            }

            $superadminGlobal = User::create([
                'email' => $superadminGlobalData['email'],
                'name' => $superadminGlobalData['name'],
                'password' => Hash::make($superadminGlobalData['password']),
                'tenant_id' => null, // NULL = superadmin global
                'role_id' => $superadminRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // 4. CREAR TENANT EN BD
            $tenant = Tenant::create([
                'name' => $tenantAndThemeData['company_name'],
                'slug' => Str::slug($tenantAndThemeData['company_name']),
                'domain' => $tenantAndThemeData['domain'],
                'status' => 'active',
                'database_name' => $mysqlData['database_name'] ?? null,
                'created_by' => $superadminGlobal->id,
            ]);

            // 6. CREAR CONFIGURACIÓN DE TEMA (usar por defecto si no se proporciona)
            ThemeConfiguration::create([
                'tenant_id' => $tenant->id,
                'color_primary' => $tenantAndThemeData['color_primary'] ?? '#2767C6',
                'color_primary_dark' => $tenantAndThemeData['color_primary_dark'] ?? '#0F3F5F',
                'color_primary_darker' => $tenantAndThemeData['color_primary_darker'] ?? '#102544',
                'color_accent' => $tenantAndThemeData['color_accent'] ?? '#B23A3A',
                'color_neutral_warm' => $tenantAndThemeData['color_neutral_warm'] ?? '#E3D2B5',
                'color_bg_light' => $tenantAndThemeData['color_bg_light'] ?? '#F5F7FA',
                'color_border' => $tenantAndThemeData['color_border'] ?? '#D4D9E2',
                'color_text_primary' => $tenantAndThemeData['color_text_primary'] ?? '#1F2933',
                'color_text_secondary' => $tenantAndThemeData['color_text_secondary'] ?? '#6B7280',
                'logo_path' => $tenantAndThemeData['logo_path'] ?? null,
                'is_custom' => (!empty($tenantAndThemeData['color_primary']) || !empty($tenantAndThemeData['logo_path'])) ? true : false,
            ]);

            // 7. MARCAR SETUP COMO COMPLETADO
            file_put_contents(storage_path('.setup_completed'), json_encode([
                'completed_at' => now()->toIso8601String(),
                'version' => config('app.version', '1.0.0'),
                'tenant_id' => $tenant->id,
                'superadmin_global_id' => $superadminGlobal->id,
                'tenant_admin_email' => $tenantAndThemeData['tenant_admin_email'] ?? null, // registrar el email indicado para referencia
            ]));

            // Limpiar sesión de setup
            session()->forget('setup_step_*');

            return [
                'success' => true,
                'message' => 'Setup completado exitosamente. Sistema inicializado.',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'superadmin_global_id' => $superadminGlobal->id,
                ],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al completar setup: ' . $e->getMessage()];
        }
    }

    /**
     * Crear BD y Usuario MySQL
     */
    private function createDatabaseAndUser($data)
    {
        try {
            $dsn = "mysql:host={$data['host']};port={$data['port']}";
            $pdo = new \PDO($dsn, $data['root_user'], $data['root_password'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $dbName = $data['database_name'];
            $dbUser = $data['database_user'];
            $dbPassword = $data['database_password'];

            // Crear BD
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Crear usuario
            $pdo->exec("CREATE USER IF NOT EXISTS '{$dbUser}'@'%' IDENTIFIED BY '{$dbPassword}'");

            // Otorgar permisos
            $pdo->exec("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$dbUser}'@'%'");

            // Flush privileges
            $pdo->exec("FLUSH PRIVILEGES");

        } catch (\Exception $e) {
            throw new \Exception('Error al crear BD y usuario: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar Migraciones
     */
    private function executeMigrations($mysqlData)
    {
        // Configurar variables de entorno
        putenv("DB_HOST={$mysqlData['host']}");
        putenv("DB_PORT={$mysqlData['port']}");
        putenv("DB_DATABASE={$mysqlData['database_name']}");
        putenv("DB_USERNAME={$mysqlData['database_user']}");
        putenv("DB_PASSWORD={$mysqlData['database_password']}");

        // Ejecutar migraciones
        Artisan::call('migrate', ['--force' => true]);

        // Verificar tablas críticas
        $tablesNeeded = [
            'setup_checkpoints',
            'setup_progress',
            'tenants',
            'system_users',
            'theme_configurations',
            'users',
            'terceros',
            'facturas',
            'import_configurations',
            'import_logs',
            'import_records',
        ];

        foreach ($tablesNeeded as $table) {
            if (!Schema::hasTable($table)) {
                throw new \Exception("Tabla $table no existe después de migraciones");
            }
        }
    }

    /**
     * Mapear nombres de campos del formulario
     */
    private function mapFormData($data, $stepKey)
    {
        $mapped = $data;

        if ($stepKey === 'setup_step_superadmin_created') {
            // El formulario usa admin_name, admin_email, etc.
            $mapped['email'] = $data['admin_email'] ?? null;
            $mapped['name'] = $data['admin_name'] ?? null;
            $mapped['password'] = $data['admin_password'] ?? null;
            $mapped['password_confirmation'] = $data['admin_password_confirmation'] ?? null;
        }

        if ($stepKey === 'setup_step_mysql_connected') {
            // El formulario usa db_* para la conexión root, y db_* para la BD
            $mapped['host'] = $data['db_host'] ?? null;
            $mapped['port'] = $data['db_port'] ?? null;
            $mapped['root_user'] = $data['db_root_user'] ?? null;
            $mapped['root_password'] = $data['db_root_password'] ?? null;
            $mapped['database_name'] = $data['db_name'] ?? null;
            $mapped['database_user'] = $data['db_user'] ?? null;
            $mapped['database_password'] = $data['db_user_password'] ?? null;
            $mapped['database_password_confirmation'] = $data['db_user_password_confirm'] ?? null;
        }

        if ($stepKey === 'setup_step_first_tenant_and_theme') {
            $mapped['company_name'] = $data['company_name'] ?? null;
            $mapped['domain'] = $data['company_domain'] ?? ($data['domain'] ?? null);
            $mapped['tenant_admin_name'] = $data['tenant_admin_name'] ?? null;
            $mapped['tenant_admin_email'] = $data['tenant_admin_email'] ?? null;
            $mapped['color_primary'] = $data['color_primary'] ?? '#2767C6';
            $mapped['logo_file'] = $data['logo_file'] ?? null;
        }

        if ($stepKey === 'setup_step_theme_applied') {
            $mapped['color_primary'] = $data['color_primary'] ?? '#2767C6';
            $mapped['logo_file'] = $data['logo_file'] ?? null;
        }

        return $mapped;
    }

    /**
     * Métodos auxiliares
     */
    private function testMySQLConnection($host, $port, $username, $password)
    {
        try {
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Endpoint para testear conexión a BD y validar permisos
     */
    public function testDatabaseConnection(Request $request)
    {
        $host = $request->input('db_host');
        $port = $request->input('db_port');
        $user = $request->input('db_root_user');
        $pass = $request->input('db_root_password', '');

        $result = [
            'connection' => false,
            'create_database' => false,
            'create_user' => false,
            'grant_privileges' => false,
            'messages' => [
                'connection' => '',
                'create_database' => '',
                'create_user' => '',
                'grant_privileges' => ''
            ],
            'debug' => []
        ];

        // Validar entrada básica
        if (!$host || !$port || !$user) {
            $result['messages']['connection'] = 'Host, Puerto y Usuario son requeridos';
            return response()->json($result, 200);
        }

        try {
            // Conectar con PDO
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5
            ]);

            $result['connection'] = true;
            $result['messages']['connection'] = 'Conexión exitosa ✓';

            // Obtener usuario actual
            $stmt = $pdo->query("SELECT USER()");
            $userRow = $stmt->fetch(\PDO::FETCH_NUM);
            $result['debug']['current_user'] = $userRow[0] ?? 'desconocido';

            // Obtener GRANTS
            $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
            $grants = '';

            if ($stmt) {
                while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                    $grants .= $row[0] . ' ';
                }
                $result['debug']['grants_found'] = true;
            } else {
                $result['debug']['grants_found'] = false;
            }

            // Determinar permisos
            $has_all = (strpos($grants, 'ALL PRIVILEGES') !== false);
            $has_create = (strpos($grants, 'CREATE') !== false);
            $has_create_user = (strpos($grants, 'CREATE USER') !== false);
            $has_grant_option = (strpos($grants, 'GRANT OPTION') !== false);

            // Verificar CREATE DATABASE
            if ($has_all || $has_create) {
                $result['create_database'] = true;
                $result['messages']['create_database'] = 'Permiso CREATE DATABASE ✓';
            } else {
                $result['messages']['create_database'] = 'Sin permiso CREATE';
            }

            // Verificar CREATE USER
            if ($has_all || $has_create_user) {
                $result['create_user'] = true;
                $result['messages']['create_user'] = 'Permiso CREATE USER ✓';
            } else {
                $result['messages']['create_user'] = 'Sin permiso CREATE USER';
            }

            // Verificar GRANT OPTION
            if ($has_all || $has_grant_option) {
                $result['grant_privileges'] = true;
                $result['messages']['grant_privileges'] = 'Permiso GRANT OPTION ✓';
            } else {
                $result['messages']['grant_privileges'] = 'Sin permiso GRANT';
            }

        } catch (\Exception $e) {
            $result['messages']['connection'] = 'Error de conexión: ' . $e->getMessage();
            $result['debug'] = [
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, 200);
    }

    /**
     * Volver al paso anterior
     * Revierte el paso actual a "pending" para poder editarlo de nuevo
     */
    public function goBack(Request $request)
    {
        try {
            $stepKey = $request->input('step_key');
            $currentStepOrder = $request->input('current_step_order');

            if (!$stepKey || !$currentStepOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
            }

            // Revertir el paso actual a "pending"
            $result = $this->setupService->revertStep($stepKey);

            if ($result['success']) {
                // Obtener el paso anterior
                $previousStep = $this->setupService->getPreviousStep((int)$currentStepOrder);

                return response()->json([
                    'success' => true,
                    'message' => 'Volviendo al paso anterior',
                    'previousStep' => $previousStep ? $previousStep->step_order : null,
                    'previousStepKey' => $previousStep ? $previousStep->step_key : null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar acceso a un paso específico
     * Verifica que el usuario pueda acceder a este paso según su progreso
     */
    public function validateStepAccess(Request $request)
    {
        try {
            $stepOrder = $request->input('step_order');

            if (!$stepOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paso no especificado'
                ]);
            }

            $canAccess = $this->setupService->canAccessStep((int)$stepOrder);
            $currentAllowed = $this->setupService->getCurrentAllowedStep();

            if ($canAccess) {
                return response()->json([
                    'success' => true,
                    'message' => 'Acceso permitido',
                    'currentAllowedStep' => $currentAllowed,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes acceder a este paso. Completa el paso ' . ($currentAllowed - 1) . ' primero.',
                    'currentAllowedStep' => $currentAllowed,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}

