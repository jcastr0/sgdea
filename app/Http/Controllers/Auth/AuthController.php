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
     */
    public function login(Request $request)
    {
        // Validar entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. PRIMERO: Verificar si es admin global (SystemUser)
        $systemUser = \App\Models\SystemUser::where('email', $request->email)->first();

        if ($systemUser) {
            // Verificar contraseña del admin global
            if (Hash::check($request->password, $systemUser->password)) {
                // Login exitoso como admin global
                Auth::guard('system')->login($systemUser, $request->remember ?? false);

                // Redirigir al panel del admin global
                return redirect()->route('admin.dashboard');
            } else {
                // Contraseña incorrecta
                return redirect()->back()->withErrors(['email' => 'Credenciales inválidas']);
            }
        }

        // 2. SI NO ES ADMIN GLOBAL: Intentar login en tenant
        $tenant = $this->authService->detectTenant();

        if (!$tenant) {
            return redirect()->back()->with('error', 'Empresa no encontrada');
        }

        // Validar credenciales en el contexto del tenant
        $result = $this->authService->validateLogin(
            $request->email,
            $request->password,
            $tenant
        );

        if (!$result['success']) {
            if ($result['pending_approval'] ?? false) {
                return redirect()->route('pending-approval');
            }
            return redirect()->back()->withErrors(['email' => $result['message']]);
        }

        // Login exitoso en tenant
        $user = $result['user'];

        // Registrar login
        $this->authService->recordLogin($user);

        // Autenticar usuario
        Auth::login($user, $request->remember ?? false);

        // Guardar tenant_id en sesión
        session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);

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

