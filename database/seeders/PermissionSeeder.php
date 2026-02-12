<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * PermissionSeeder
 *
 * Crea todos los permisos del sistema.
 * Los permisos son globales y se asignan a roles.
 *
 * Convención de nombres: {recurso_plural}.{accion}
 * Ejemplos: facturas.view, facturas.create, usuarios.approve
 */
class PermissionSeeder extends Seeder
{
    /**
     * Definición de todos los permisos del sistema
     * Formato compatible con el sidebar y el sistema anterior
     */
    private array $permissions = [
        // ========== FACTURAS ==========
        [
            'name' => 'facturas.view',
            'slug' => 'facturas-view',
            'display_name' => 'Ver facturas',
            'description' => 'Permite ver el listado y detalle de facturas',
            'resource' => 'facturas',
            'action' => 'view',
        ],
        [
            'name' => 'facturas.create',
            'slug' => 'facturas-create',
            'display_name' => 'Crear facturas',
            'description' => 'Permite crear nuevas facturas manualmente',
            'resource' => 'facturas',
            'action' => 'create',
        ],
        [
            'name' => 'facturas.edit',
            'slug' => 'facturas-edit',
            'display_name' => 'Editar facturas',
            'description' => 'Permite modificar facturas existentes',
            'resource' => 'facturas',
            'action' => 'edit',
        ],
        [
            'name' => 'facturas.delete',
            'slug' => 'facturas-delete',
            'display_name' => 'Eliminar facturas',
            'description' => 'Permite eliminar facturas del sistema',
            'resource' => 'facturas',
            'action' => 'delete',
        ],
        [
            'name' => 'facturas.export',
            'slug' => 'facturas-export',
            'display_name' => 'Exportar facturas',
            'description' => 'Permite exportar facturas a Excel/PDF',
            'resource' => 'facturas',
            'action' => 'export',
        ],

        // ========== TERCEROS ==========
        [
            'name' => 'terceros.view',
            'slug' => 'terceros-view',
            'display_name' => 'Ver terceros',
            'description' => 'Permite ver el listado y detalle de terceros',
            'resource' => 'terceros',
            'action' => 'view',
        ],
        [
            'name' => 'terceros.create',
            'slug' => 'terceros-create',
            'display_name' => 'Crear terceros',
            'description' => 'Permite crear nuevos terceros/clientes',
            'resource' => 'terceros',
            'action' => 'create',
        ],
        [
            'name' => 'terceros.edit',
            'slug' => 'terceros-edit',
            'display_name' => 'Editar terceros',
            'description' => 'Permite modificar terceros existentes',
            'resource' => 'terceros',
            'action' => 'edit',
        ],
        [
            'name' => 'terceros.delete',
            'slug' => 'terceros-delete',
            'display_name' => 'Eliminar terceros',
            'description' => 'Permite eliminar terceros del sistema',
            'resource' => 'terceros',
            'action' => 'delete',
        ],
        [
            'name' => 'terceros.merge',
            'slug' => 'terceros-merge',
            'display_name' => 'Fusionar terceros',
            'description' => 'Permite fusionar terceros duplicados',
            'resource' => 'terceros',
            'action' => 'merge',
        ],

        // ========== IMPORTACIONES ==========
        [
            'name' => 'importaciones.view',
            'slug' => 'importaciones-view',
            'display_name' => 'Ver importaciones',
            'description' => 'Permite ver el historial de importaciones',
            'resource' => 'importaciones',
            'action' => 'view',
        ],
        [
            'name' => 'importaciones.execute',
            'slug' => 'importaciones-execute',
            'display_name' => 'Ejecutar importaciones',
            'description' => 'Permite importar archivos Excel y PDF',
            'resource' => 'importaciones',
            'action' => 'execute',
        ],
        [
            'name' => 'importaciones.configure',
            'slug' => 'importaciones-configure',
            'display_name' => 'Configurar importaciones',
            'description' => 'Permite configurar mapeo de columnas y opciones',
            'resource' => 'importaciones',
            'action' => 'configure',
        ],

        // ========== USUARIOS ==========
        [
            'name' => 'usuarios.view',
            'slug' => 'usuarios-view',
            'display_name' => 'Ver usuarios',
            'description' => 'Permite ver el listado de usuarios',
            'resource' => 'usuarios',
            'action' => 'view',
        ],
        [
            'name' => 'usuarios.create',
            'slug' => 'usuarios-create',
            'display_name' => 'Crear usuarios',
            'description' => 'Permite crear nuevos usuarios',
            'resource' => 'usuarios',
            'action' => 'create',
        ],
        [
            'name' => 'usuarios.edit',
            'slug' => 'usuarios-edit',
            'display_name' => 'Editar usuarios',
            'description' => 'Permite modificar usuarios existentes',
            'resource' => 'usuarios',
            'action' => 'edit',
        ],
        [
            'name' => 'usuarios.delete',
            'slug' => 'usuarios-delete',
            'display_name' => 'Eliminar usuarios',
            'description' => 'Permite eliminar usuarios del sistema',
            'resource' => 'usuarios',
            'action' => 'delete',
        ],
        [
            'name' => 'usuarios.approve',
            'slug' => 'usuarios-approve',
            'display_name' => 'Aprobar usuarios',
            'description' => 'Permite aprobar usuarios pendientes',
            'resource' => 'usuarios',
            'action' => 'approve',
        ],
        [
            'name' => 'usuarios.manage',
            'slug' => 'usuarios-manage',
            'display_name' => 'Gestionar usuarios',
            'description' => 'Permite gestionar usuarios (cambiar rol, bloquear)',
            'resource' => 'usuarios',
            'action' => 'manage',
        ],

        // ========== ROLES ==========
        [
            'name' => 'roles.view',
            'slug' => 'roles-view',
            'display_name' => 'Ver roles',
            'description' => 'Permite ver roles y sus permisos',
            'resource' => 'roles',
            'action' => 'view',
        ],
        [
            'name' => 'roles.manage',
            'slug' => 'roles-manage',
            'display_name' => 'Gestionar roles',
            'description' => 'Permite crear, editar y eliminar roles',
            'resource' => 'roles',
            'action' => 'manage',
        ],

        // ========== AUDITORÍA ==========
        [
            'name' => 'auditoria.view',
            'slug' => 'auditoria-view',
            'display_name' => 'Ver auditoría',
            'description' => 'Permite ver los registros de auditoría',
            'resource' => 'auditoria',
            'action' => 'view',
        ],
        [
            'name' => 'auditoria.export',
            'slug' => 'auditoria-export',
            'display_name' => 'Exportar auditoría',
            'description' => 'Permite exportar registros de auditoría',
            'resource' => 'auditoria',
            'action' => 'export',
        ],

        // ========== CONFIGURACIÓN ==========
        [
            'name' => 'configuracion.view',
            'slug' => 'configuracion-view',
            'display_name' => 'Ver configuración',
            'description' => 'Permite ver la configuración del sistema',
            'resource' => 'configuracion',
            'action' => 'view',
        ],
        [
            'name' => 'configuracion.edit',
            'slug' => 'configuracion-edit',
            'display_name' => 'Editar configuración',
            'description' => 'Permite modificar la configuración del sistema',
            'resource' => 'configuracion',
            'action' => 'edit',
        ],

        // ========== DASHBOARD ==========
        [
            'name' => 'dashboard.view',
            'slug' => 'dashboard-view',
            'display_name' => 'Ver dashboard',
            'description' => 'Permite ver el dashboard principal',
            'resource' => 'dashboard',
            'action' => 'view',
        ],
        [
            'name' => 'dashboard.export',
            'slug' => 'dashboard-export',
            'display_name' => 'Exportar dashboard',
            'description' => 'Permite exportar datos del dashboard',
            'resource' => 'dashboard',
            'action' => 'export',
        ],

        // ========== ADMINISTRACIÓN GLOBAL ==========
        [
            'name' => 'admin.tenants',
            'slug' => 'admin-tenants',
            'display_name' => 'Gestionar tenants',
            'description' => 'Permite gestionar tenants/empresas',
            'resource' => 'admin',
            'action' => 'tenants',
        ],
        [
            'name' => 'admin.system',
            'slug' => 'admin-system',
            'display_name' => 'Administración del sistema',
            'description' => 'Acceso completo a la administración del sistema',
            'resource' => 'admin',
            'action' => 'system',
        ],
    ];

    public function run(): void
    {
        $this->command->info('📋 Creando permisos del sistema...');

        $count = 0;
        foreach ($this->permissions as $permData) {
            Permission::updateOrCreate(
                ['name' => $permData['name']],
                [
                    'slug' => $permData['slug'],
                    'display_name' => $permData['display_name'],
                    'description' => $permData['description'],
                    'resource' => $permData['resource'],
                    'action' => $permData['action'],
                ]
            );
            $count++;
        }

        $this->command->info("   ✅ {$count} permisos creados");
    }
}

