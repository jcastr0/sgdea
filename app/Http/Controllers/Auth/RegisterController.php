<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verificar que el email sea del dominio permitido
        if (!str_ends_with($request->email, '@maritimosarboleda.com')) {
            return back()->withErrors([
                'email' => 'Solo se permiten correos del dominio @maritimosarboleda.com'
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Determinar estado según el email
            $isSuperadmin = $request->email === 'soporte@maritimosarboleda.com';
            $status = $isSuperadmin ? 'activo' : 'pendiente_aprobacion';

            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $status,
            ]);

            // Si es superadmin, crear rol Superadmin si no existe y asignarlo
            if ($isSuperadmin) {
                $superadminRole = Role::firstOrCreate(
                    ['name' => 'Superadmin'],
                    ['description' => 'Administrador del sistema con acceso total']
                );

                $user->roles()->attach($superadminRole->id);
            }

            DB::commit();

            if ($isSuperadmin) {
                // Login automático para superadmin
                auth()->login($user);
                return redirect()->route('dashboard')->with('success', '¡Bienvenido al sistema!');
            } else {
                // Usuario normal debe esperar aprobación
                return redirect()->route('login')->with('info', 'Tu cuenta ha sido creada. Espera la aprobación del administrador.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear usuario: ' . $e->getMessage()])->withInput();
        }
    }
}

