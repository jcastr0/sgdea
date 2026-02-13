{{-- Menú dependiente de permisos --}}

<nav class="sidebar-menu">
    <div class="menu-section">
        <h4>Operativo</h4>
        <ul>
            @canAccess('factura.ver')
            <li>
                <a href="{{ route('facturas.index') }}" class="menu-item">
                    📄 Facturas
                </a>
            </li>
            @endcanAccess

            @canAccess('tercero.ver')
            <li>
                <a href="{{ route('terceros.index') }}" class="menu-item">
                    🏢 Terceros/Clientes
                </a>
            </li>
            @endcanAccess

            @canAccessAny('importacion.excel', 'importacion.pdf')
            <li>
                <a href="{{ route('importaciones.index') }}" class="menu-item">
                    📥 Importaciones
                </a>
            </li>
            @endcanAccessAny
        </ul>
    </div>

    <div class="menu-section">
        <h4>Análisis</h4>
        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="menu-item">
                    📊 Dashboard
                </a>
            </li>

            @canAccess('auditoria.ver')
            <li>
                <a href="{{ route('admin.auditoria.index') }}" class="menu-item">
                    🔍 Auditoría
                </a>
            </li>
            @endcanAccess
        </ul>
    </div>

    {{-- DESHABILITADO TEMPORALMENTE - Sección Administración con Usuarios Pendientes
    <div class="menu-section">
        <h4>Administración</h4>
        <ul>
            @canAccess('admin.gestionar_usuarios')
            <li>
                <a href="{{ route('admin.usuarios.pendientes') }}" class="menu-item">
                    👥 Usuarios
                </a>
            </li>
            @endcanAccess

            @canAccess('admin.gestionar_roles')
            <li>
                <a href="{{ route('admin.roles.index') }}" class="menu-item">
                    🔐 Roles y Permisos
                </a>
            </li>
            @endcanAccess

            @canAccess('config.editar')
            <li>
                <a href="{{ route('config.edit') }}" class="menu-item">
                    ⚙️ Configuración
                </a>
            </li>
            @endcanAccess
        </ul>
    </div>
    --}}
</nav>

<style>
.sidebar-menu {
    padding: 20px 0;
}

.menu-section {
    margin-bottom: 25px;
}

.menu-section h4 {
    font-size: 12px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0 15px;
    margin: 0 0 10px 0;
}

.menu-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    display: block;
    padding: 12px 15px;
    color: #1F2933;
    text-decoration: none;
    font-size: 14px;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.menu-item:hover {
    background: #F5F7FA;
    border-left-color: #2767C6;
    color: #2767C6;
}

.menu-item.active {
    background: #EFF2F9;
    border-left-color: #2767C6;
    color: #2767C6;
    font-weight: 600;
}
</style>

