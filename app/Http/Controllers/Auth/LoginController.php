<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginAttempt;
use App\Models\SecurityEvent;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Registrar intento de login
        $this->logLoginAttempt($request->email, $request->ip(), $request->userAgent(), false);

        // Intentar autenticar
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verificar que el usuario esté activo
            if ($user->status !== 'activo') {
                Auth::logout();

                $this->logSecurityEvent($user->id, 'login_failed_inactive',
                    'Intento de login con usuario inactivo', $request);

                return back()->withErrors([
                    'email' => 'Tu cuenta está inactiva. Contacta al administrador.',
                ]);
            }

            // Login exitoso
            $request->session()->regenerate();

            $this->logLoginAttempt($request->email, $request->ip(), $request->userAgent(), true);
            $this->logSecurityEvent($user->id, 'login_success', 'Login exitoso', $request);

            return redirect()->intended('dashboard');
        }

        // Login fallido
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->logSecurityEvent($user->id, 'logout', 'Usuario cerró sesión', $request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Registrar intento de login
     */
    private function logLoginAttempt($email, $ip, $userAgent, $success)
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'success' => $success,
        ]);
    }

    /**
     * Registrar evento de seguridad
     */
    private function logSecurityEvent($userId, $eventType, $description, Request $request)
    {
        SecurityEvent::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}

