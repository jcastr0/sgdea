<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConfigurationController;

// Rutas de setup (sin restricciones mientras no esté completo)
Route::get('/setup', [SetupController::class, 'show'])->name('setup.show');
Route::post('/setup/process', [SetupController::class, 'process'])->name('setup.process');
Route::post('/setup/test-db-connection', [SetupController::class, 'testDatabaseConnection'])->name('setup.test-db-connection');
Route::post('/setup/validate-access', [SetupController::class, 'validateStepAccess'])->name('setup.validate-access');
Route::post('/setup/go-back', [SetupController::class, 'goBack'])->name('setup.go-back');

// Redirigir root a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (multi-tenant)
Route::middleware('guest')->group(function () {
    // La ruta 'login' es requerida por el middleware auth de Laravel
    Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('auth.login.post');

    Route::get('/register', [\App\Http\Controllers\Auth\AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->name('auth.register.post');

    // Rutas de recuperación de contraseña
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::get('/pending-approval', [\App\Http\Controllers\Auth\AuthController::class, 'showPendingApproval'])->name('pending-approval');

Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout')->middleware('auth');


// Rutas protegidas (requieren autenticación y verificación de tenant)
Route::middleware(['auth', 'verify.tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/api/dashboard/terceros', [DashboardController::class, 'buscarTerceros'])->name('dashboard.terceros');

    // Rutas de Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Configuración del Tenant
    Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration.index');
    Route::put('/configuration/general', [ConfigurationController::class, 'updateGeneral'])->name('configuration.general');
    Route::put('/configuration/logo', [ConfigurationController::class, 'updateLogo'])->name('configuration.logo');
    Route::get('/configuration/logo/{type}/delete', [ConfigurationController::class, 'deleteLogo'])->name('configuration.delete-logo');
    Route::put('/configuration/theme', [ConfigurationController::class, 'updateTheme'])->name('configuration.theme');
    Route::put('/configuration/notifications', [ConfigurationController::class, 'updateNotifications'])->name('configuration.notifications');
    Route::put('/configuration/import', [ConfigurationController::class, 'updateImport'])->name('configuration.import');
    Route::post('/configuration/export', [ConfigurationController::class, 'exportData'])->name('configuration.export');

    // Rutas de Terceros (CRUD)
    Route::resource('terceros', TerceroController::class);
    Route::get('/api/terceros/search-duplicates', [TerceroController::class, 'searchDuplicates'])->name('terceros.search-duplicates');

    // Rutas de Facturas (CRUD)
    Route::resource('facturas', FacturaController::class);
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'showPdf'])->name('facturas.pdf');
    Route::get('/facturas/{factura}/pdf/download', [FacturaController::class, 'downloadPdf'])->name('facturas.download-pdf');

    // Rutas de Importaciones
    Route::get('/importaciones', [ImportacionController::class, 'wizard'])->name('importaciones.index');
    Route::get('/importaciones/legacy', [ImportacionController::class, 'index'])->name('importaciones.legacy');
    Route::post('/api/import-excel/validate', [ImportacionController::class, 'validateExcel'])->name('import.validate-excel');
    Route::post('/api/import-excel/process', [ImportacionController::class, 'processExcel'])->name('import.process-excel');
    Route::post('/api/import-pdf/validate', [ImportacionController::class, 'validatePDF'])->name('import.validate-pdf');
    Route::post('/api/import-pdf/process', [ImportacionController::class, 'processPDF'])->name('import.process-pdf');
    Route::get('/api/import-progress/{importLogId}', [ImportacionController::class, 'getProgress'])->name('import.progress');
    Route::get('/importaciones/{importLogId}/reporte', [ImportacionController::class, 'getReport'])->name('import.report');

    // Rutas de aprobación de usuarios (admin del tenant)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios-pendientes', [\App\Http\Controllers\Admin\UserApprovalController::class, 'pendientes'])->name('usuarios.pendientes');
        Route::post('/usuarios/{id}/aprobar', [\App\Http\Controllers\Admin\UserApprovalController::class, 'aprobar'])->name('usuarios.aprobar');
        Route::post('/usuarios/{id}/rechazar', [\App\Http\Controllers\Admin\UserApprovalController::class, 'rechazar'])->name('usuarios.rechazar');
        Route::post('/usuarios/{id}/cambiar-estado', [\App\Http\Controllers\Admin\UserApprovalController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');
        Route::get('/usuarios/historial', [\App\Http\Controllers\Admin\UserApprovalController::class, 'historial'])->name('usuarios.historial');

        // Rutas de auditoría
        Route::get('/auditoria', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/export', [\App\Http\Controllers\Admin\AuditController::class, 'export'])->name('auditoria.export');
        Route::get('/auditoria-integridad', [\App\Http\Controllers\Admin\AuditController::class, 'verificarIntegridad'])->name('auditoria.integridad');
        Route::get('/auditoria/{id}', [\App\Http\Controllers\Admin\AuditController::class, 'show'])->name('auditoria.show');
    });
});

// Rutas de administración (solo superadmin global)
Route::middleware(['auth', 'is.superadmin.global'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // APIs del Dashboard
    Route::get('/api/stats', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'getStats'])->name('api.stats');
    Route::get('/api/alerts', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'getAlerts'])->name('api.alerts');
    Route::get('/api/activity', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'getActivity'])->name('api.activity');
    Route::get('/api/growth-trend', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'getGrowthTrend'])->name('api.growth-trend');

    // Gestión de Tenants
    Route::resource('tenants', \App\Http\Controllers\Admin\TenantController::class);
    Route::post('/tenants/{id}/cambiar-estado', [\App\Http\Controllers\Admin\TenantController::class, 'cambiarEstado'])->name('tenants.cambiar-estado');
    Route::post('/tenants/{id}/cambiar-superadmin', [\App\Http\Controllers\Admin\TenantController::class, 'cambiarSuperadmin'])->name('tenants.cambiar-superadmin');
    Route::post('/tenants/{tenant}/toggle-status', [\App\Http\Controllers\Admin\TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
});

// Ruta de Component Library (solo en desarrollo)
if (app()->environment('local', 'development')) {
    Route::get('/components', function () {
        return view('components-showcase');
    })->name('components.showcase');
}
