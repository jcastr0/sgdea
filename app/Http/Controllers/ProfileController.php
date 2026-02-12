<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar la página de perfil del usuario
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', [
            'user' => $user,
            'preferences' => $this->getUserPreferences($user),
        ]);
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', [
            'user' => $user,
            'preferences' => $this->getUserPreferences($user),
        ]);
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
            'language' => ['required', 'in:es,en'],
            'notifications_email' => ['boolean'],
            'notifications_browser' => ['boolean'],
            'compact_sidebar' => ['boolean'],
        ]);

        $user = Auth::user();

        // Guardar preferencias en la sesión y en la BD si existe el campo
        $preferences = [
            'theme' => $validated['theme'],
            'language' => $validated['language'],
            'notifications_email' => $request->boolean('notifications_email'),
            'notifications_browser' => $request->boolean('notifications_browser'),
            'compact_sidebar' => $request->boolean('compact_sidebar'),
        ];

        // Guardar en sesión
        session(['user_preferences' => $preferences]);

        // Si el usuario tiene el campo preferences, guardarlo
        if (in_array('preferences', $user->getFillable())) {
            $user->update(['preferences' => json_encode($preferences)]);
        }

        return redirect()->route('profile.show')->with('success', 'Preferencias actualizadas correctamente.');
    }

    /**
     * Obtener preferencias del usuario
     */
    private function getUserPreferences($user): array
    {
        // Intentar obtener de la sesión primero
        if (session()->has('user_preferences')) {
            return session('user_preferences');
        }

        // Valores por defecto
        return [
            'theme' => 'system',
            'language' => 'es',
            'notifications_email' => true,
            'notifications_browser' => true,
            'compact_sidebar' => false,
        ];
    }

    /**
     * Eliminar cuenta del usuario
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'La contraseña es incorrecta.',
        ]);

        $user = Auth::user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Tu cuenta ha sido eliminada.');
    }
}

