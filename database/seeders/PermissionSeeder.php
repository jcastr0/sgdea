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
 * Convención de nombres: {recurso}.{accion}
 * Ejemplos: factura.ver, factura.crear, usuario.aprobar
 */
class PermissionSeeder extends Seeder
{
    /**
     * Definición de todos los permisos del sistema
     */
    private array $permissions = [
        // ========== FACTURAS ==========
        [
            'name' => 'factura.ver',
            'display_name' => 'Ver facturas',
            'description' => 'Permite ver el listado y detalle de facturas',
            'resource' => 'factura',
            'action' => 'ver',
        ],
        [
            'name' => 'factura.crear',
            'display_name' => 'Crear facturas',
            'description' => 'Permite crear nuevas facturas manualmente',
            'resource' => 'factura',
            'action' => 'crear',
        ],
        [
            'name' => 'factura.editar',
            'display_name' => 'Editar facturas',
            'description' => 'Permite modificar facturas existentes',
            'resource' => 'factura',
            'action' => 'editar',
        ],
        [
            'name' => 'factura.eliminar',
            'display_name' => 'Eliminar facturas',
            'description' => 'Permite eliminar facturas del sistema',
            'resource' => 'factura',
            'action' => 'eliminar',
        ],
        [
            'name' => 'factura.exportar',
            'display_name' => 'Exportar facturas',
            'description' => 'Permite exportar facturas a Excel/PDF',
            'resource' => 'factura',
            'action' => 'exportar',
        ],

        // ========== TERCEROS ==========
        [
            'name' => 'tercero.ver',
            'display_name' => 'Ver terceros',
            'description' => 'Permite ver el listado y detalle de terceros',
            'resource' => 'tercero',
            'action' => 'ver',
        ],
        [
            'name' => 'tercero.crear',
            'display_name' => 'Crear terceros',
            'description' => 'Permite crear nuevos terceros/clientes',
            'resource' => 'tercero',
            'action' => 'crear',
        ],
        [
            'name' => 'tercero.editar',
            'display_name' => 'Editar terceros',
            'description' => 'Permite modificar terceros existentes',
            'resource' => 'tercero',
            'action' => 'editar',
        ],
        [
            'name' => 'tercero.eliminar',
            'display_name' => 'Eliminar terceros',
            'description' => 'Permite eliminar terceros del sistema',
            'resource' => 'tercero',
            'action' => 'eliminar',
        ],
        [
            'name' => 'tercero.fusionar',
            'display_name' => 'Fusionar terceros',
            'description' => 'Permite fusionar terceros duplicados',
            'resource' => 'tercero',
            'action' => 'fusionar',
        ],

        // ========== IMPORTACIÓN ==========
        [
            'name' => 'importacion.ver',
            'display_name' => 'Ver importaciones',
            'description' => 'Permite ver el historial de importaciones',
            'resource' => 'importacion',
            'action' => 'ver',
        ],
        [
            'name' => 'importacion.ejecutar',
            'display_name' => 'Ejecutar importaciones',
            'description' => 'Permite importar archivos Excel y PDF',
            'resource' => 'importacion',
            'action' => 'ejecutar',
        ],
        [
            'name' => 'importacion.configurar',
            'display_name' => 'Configurar importaciones',
            'description' => 'Permite configurar mapeo de columnas y opciones',
            'resource' => 'importacion',
            'action' => 'configurar',
        ],

        // ========== USUARIOS ==========
        [
            'name' => 'usuario.ver',
            'display_name' => 'Ver usuarios',
            'description' => 'Permite ver el listado de usuarios',
            'resource' => 'usuario',
            'action' => 'ver',
        ],
        [
            'name' => 'usuario.crear',
            'display_name' => 'Crear usuarios',
            'description' => 'Permite crear nuevos usuarios',
            'resource' => 'usuario',
            'action' => 'crear',
        ],
        [
            'name' => 'usuario.editar',
            'display_name' => 'Editar usuarios',
            'description' => 'Permite modificar usuarios existentes',
            'resource' => 'usuario',
            'action' => 'editar',
        ],
        [
            'name' => 'usuario.eliminar',
            'display_name' => 'Eliminar usuarios',
            'description' => 'Permite eliminar usuarios del sistema',
            'resource' => 'usuario',
            'action' => 'eliminar',
        ],
        [
            'name' => 'usuario.aprobar',
            'display_name' => 'Aprobar usuarios',
            'description' => 'Permite aprobar o rechazar solicitudes de registro',
            'resource' => 'usuario',
            'action' => 'aprobar',
        ],
        [
            'name' => 'usuario.cambiar_rol',
            'display_name' => 'Cambiar rol de usuarios',
            'description' => 'Permite asignar/cambiar el rol de usuarios',
            'resource' => 'usuario',
            'action' => 'cambiar_rol',
        ],

        // ========== ROLES Y PERMISOS ==========
        [
            'name' => 'rol.ver',
            'display_name' => 'Ver roles',
            'description' => 'Permite ver el listado de roles',
            'resource' => 'rol',
            'action' => 'ver',
        ],
        [
            'name' => 'rol.gestionar',
            'display_name' => 'Gestionar roles',
            'description' => 'Permite crear, editar y eliminar roles',
            'resource' => 'rol',
            'action' => 'gestionar',
        ],

        // ========== AUDITORÍA ==========
        [
            'name' => 'auditoria.ver',
            'display_name' => 'Ver auditoría',
            'description' => 'Permite ver los logs de auditoría',
            'resource' => 'auditoria',
            'action' => 'ver',
        ],
        [
            'name' => 'auditoria.exportar',
            'display_name' => 'Exportar auditoría',
            'description' => 'Permite exportar logs de auditoría',
            'resource' => 'auditoria',
            'action' => 'exportar',
        ],

        // ========== CONFIGURACIÓN ==========
        [
            'name' => 'config.ver',
            'display_name' => 'Ver configuración',
            'description' => 'Permite ver la configuración del sistema',
            'resource' => 'config',
            'action' => 'ver',
        ],
        [
            'name' => 'config.editar',
            'display_name' => 'Editar configuración',
            'description' => 'Permite modificar la configuración del sistema',
            'resource' => 'config',
            'action' => 'editar',
        ],

        // ========== DASHBOARD ==========
        [
            'name' => 'dashboard.ver',
            'display_name' => 'Ver dashboard',
            'description' => 'Permite ver el dashboard con estadísticas',
            'resource' => 'dashboard',
            'action' => 'ver',
        ],
        [
            'name' => 'dashboard.exportar',
            'display_name' => 'Exportar reportes',
            'description' => 'Permite exportar reportes del dashboard',
            'resource' => 'dashboard',
            'action' => 'exportar',
        ],

        // ========== ADMINISTRACIÓN ==========
        [
            'name' => 'admin.gestionar_roles',
            'display_name' => 'Gestionar roles',
            'description' => 'Acceso completo a la gestión de roles y permisos',
            'resource' => 'admin',
            'action' => 'gestionar_roles',
        ],
        [
            'name' => 'admin.gestionar_usuarios',
            'display_name' => 'Gestionar usuarios',
            'description' => 'Acceso completo a la gestión de usuarios',
            'resource' => 'admin',
            'action' => 'gestionar_usuarios',
        ],
    ];

    public function run(): void
    {
        $this->command->info('📋 Creando permisos del sistema...');

        foreach ($this->permissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                [
                    'slug' => $permissionData['name'],
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                    'resource' => $permissionData['resource'],
                    'action' => $permissionData['action'],
                ]
            );
        }

        $this->command->info('   ✅ ' . count($this->permissions) . ' permisos creados');
    }
}

