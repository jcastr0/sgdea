<?php

namespace App\Services;

class UserValidationService
{
    /**
     * Dominio permitido para el registro
     */
    const ALLOWED_DOMAIN = '@maritimosarboleda.com';

    /**
     * Valida que el email pertenezca al dominio permitido
     */
    public static function isValidDomain(string $email): bool
    {
        return str_ends_with(strtolower($email), strtolower(self::ALLOWED_DOMAIN));
    }

    /**
     * Valida la fortaleza de la contraseña
     * Requiere:
     * - Mínimo 8 caracteres
     * - Al menos una mayúscula
     * - Al menos una minúscula
     * - Al menos un número
     * - Al menos un carácter especial
     */
    public static function isValidPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene el mensaje de error para validación de contraseña
     */
    public static function getPasswordValidationErrors(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra mayúscula.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número.';
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un carácter especial (!@#$%^&*() etc).';
        }

        return $errors;
    }
}

