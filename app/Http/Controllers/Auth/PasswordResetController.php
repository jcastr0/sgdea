<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Muestra el formulario para solicitar un enlace de restablecimiento de contraseña.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el enlace de restablecimiento de contraseña al correo del usuario.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
        ]);

        // Intentar enviar el enlace de restablecimiento
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Si el enlace fue enviado exitosamente, redirigir con mensaje de éxito
        // También manejamos el caso de que el email no exista de forma segura
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        // Por seguridad, siempre mostramos el mismo mensaje aunque el email no exista
        // Esto previene enumeración de usuarios
        return back()->with(['status' => 'Si el correo existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.']);
    }

    /**
     * Muestra el formulario de restablecimiento de contraseña.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Procesa el restablecimiento de contraseña.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'El token de restablecimiento es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Intentar restablecer la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Si se restableció exitosamente, redirigir al login
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        // Si hubo un error, mostrar el mensaje correspondiente
        return back()->withErrors(['email' => [__($status)]]);
    }
}
