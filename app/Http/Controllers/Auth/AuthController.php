<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        // Middleware se configura en las rutas en Laravel 12+
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        $tenant = $this->authService->detectTenant();

        if (!$tenant) {
            abort(404, 'Empresa no encontrada');
        }

        $theme = $this->authService->getTenantTheme($tenant);
        $logoLight = $this->authService->getTenantLogo($tenant, false); // Logo para modo claro (con colores)
        $logoDark = $this->authService->getTenantLogo($tenant, true);   // Logo para modo oscuro (blanco)

        return view('auth.login', [
            'tenant' => $tenant,
            'theme' => $theme,
            'logo' => $logoLight,
            'logoLight' => $logoLight,
            'logoDark' => $logoDark,
        ]);
    }

    /**
     * Procesar login
     *
     * Flujo simplificado:
     * 1. Buscar usuario por email en tabla users
     * 2. Si tiene tenant_id=NULL y rol superadmin_global -> es admin global
     * 3. Si tiene tenant_id -> es usuario de tenant
     */
    public function login(Request $request)
    {
        // Validar entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario por email (todos están en tabla users)
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Credenciales inválidas']);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['email' => 'Credenciales inválidas']);
        }

        // Verificar estado del usuario
        if ($user->status === 'inactive' || $user->status === 'blocked') {
            return redirect()->back()->withErrors(['email' => 'Tu cuenta está deshabilitada']);
        }

        if ($user->status === 'pending_approval') {
            return redirect()->route('pending-approval');
        }

        // Login exitoso
        Auth::login($user, $request->remember ?? false);

        // Registrar login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Si es superadmin global (tenant_id = NULL y rol superadmin_global)
        if ($user->isSuperadminGlobal()) {
            return redirect()->route('admin.dashboard');
        }

        // Usuario de tenant - guardar en sesión
        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            session(['tenant_id' => $user->tenant_id, 'tenant' => $tenant]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        $tenant = $this->authService->detectTenant();

        if (!$tenant) {
            abort(404, 'Empresa no encontrada');
        }

        $theme = $this->authService->getTenantTheme($tenant);
        $logo = $this->authService->getTenantLogo($tenant);
        $emailDomain = $this->authService->getTenantEmailDomain($tenant);

        return view('auth.register', [
            'tenant' => $tenant,
            'theme' => $theme,
            'logo' => $logo,
            'email_domain' => $emailDomain,
        ]);
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $tenant = $this->authService->detectTenant();

        if (!$tenant) {
            return redirect()->back()->with('error', 'Empresa no encontrada');
        }

        // Validar entrada
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Registrar usuario
        $result = $this->authService->registerUser(
            $request->name,
            $request->email,
            $request->password,
            $tenant
        );

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => $result['message']]);
        }

        $user = $result['user'];

        // Si es superadmin, auto-login
        if ($result['is_superadmin']) {
            Auth::login($user);
            session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);
            return redirect()->intended(route('dashboard'));
        }

        // Si no es superadmin, mostrar página de pendiente de aprobación
        return redirect()->route('pending-approval')
            ->with('success', 'Tu registro está pendiente de aprobación del administrador');
    }

    /**
     * Mostrar página de pendiente de aprobación
     */
    public function showPendingApproval()
    {
        $tenant = $this->authService->detectTenant();
        $theme = $this->authService->getTenantTheme($tenant);
        $logo = $this->authService->getTenantLogo($tenant);

        return view('auth.pending-approval', [
            'tenant' => $tenant,
            'theme' => $theme,
            'logo' => $logo,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        session()->forget(['tenant_id', 'tenant']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

