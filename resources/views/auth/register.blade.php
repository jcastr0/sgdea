@extends('layouts.app')

@section('content')
<div class="register-container" style="background: linear-gradient(135deg, {{ $theme->color_primary ?? '#2767C6' }} 0%, {{ $theme->color_secondary ?? '#102544' }} 100%);">
    <div class="register-box">
        {{-- Logo y nombre de empresa --}}
        <div class="company-header">
            <img src="{{ asset($logo) }}" alt="{{ $tenant->name }}" class="company-logo">
            <h1 class="company-name">{{ $tenant->name }}</h1>
            <p class="company-subtitle">Crea tu cuenta</p>
        </div>

        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Formulario de registro --}}
        <form method="POST" action="{{ route('auth.register') }}" class="register-form">
            @csrf

            {{-- Nombre --}}
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Tu nombre"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <div class="email-input-group">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="tu.email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                    <span class="email-domain">{{ $email_domain }}</span>
                </div>
                <small class="text-muted">
                    Tu email debe terminar con <strong>{{ $email_domain }}</strong>
                </small>
                @error('email')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Mínimo 8 caracteres"
                    required
                    autocomplete="new-password"
                >
                <small class="text-muted">Mínimo 8 caracteres</small>
                @error('password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="Repite tu contraseña"
                    required
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Términos --}}
            <div class="form-group form-check">
                <input
                    type="checkbox"
                    id="terms"
                    name="terms"
                    class="form-check-input"
                    required
                >
                <label class="form-check-label" for="terms">
                    Acepto los <a href="#" style="color: {{ $theme->color_primary ?? '#2767C6' }}">términos y condiciones</a>
                </label>
            </div>

            {{-- Botón submit --}}
            <button type="submit" class="btn btn-primary btn-block" style="background: {{ $theme->color_primary ?? '#2767C6' }}">
                Registrarse
            </button>
        </form>

        {{-- Link a login --}}
        <p class="text-center mt-3">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" style="color: {{ $theme->color_primary ?? '#2767C6' }}">
                Inicia sesión aquí
            </a>
        </p>

        {{-- Nota importante --}}
        <div class="info-box mt-4">
            <p class="info-title">⚠️ Nota importante</p>
            <p>Tu registro debe ser aprobado por un administrador antes de poder acceder al sistema.</p>
        </div>
    </div>
</div>

<style>
.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.register-box {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    padding: 40px;
    width: 100%;
    max-width: 450px;
}

.company-header {
    text-align: center;
    margin-bottom: 30px;
}

.company-logo {
    max-width: 80px;
    height: auto;
    margin-bottom: 15px;
}

.company-logo-placeholder {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2767C6 0%, #102544 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    margin: 0 auto 15px;
}

.company-name {
    font-size: 18px;
    font-weight: 700;
    color: #1F2933;
    margin: 0 0 5px 0;
}

.company-subtitle {
    color: #6B7280;
    font-size: 14px;
    margin: 0;
}

.register-form {
    margin: 20px 0;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    color: #1F2933;
    font-weight: 500;
    margin-bottom: 6px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #D4D9E2;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #2767C6;
    box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
    outline: none;
}

.email-input-group {
    display: flex;
    align-items: stretch;
    gap: 0;
}

.email-input-group .form-control {
    border-radius: 6px 0 0 6px;
    border-right: none;
}

.email-domain {
    background: #F5F7FA;
    border: 2px solid #D4D9E2;
    border-left: none;
    border-radius: 0 6px 6px 0;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    color: #6B7280;
    font-weight: 500;
    white-space: nowrap;
}

.text-muted {
    color: #6B7280;
    font-size: 12px;
    display: block;
    margin-top: 4px;
}

.invalid-feedback {
    color: #DC3545;
    font-size: 12px;
    margin-top: 4px;
}

.d-block {
    display: block;
}

.form-check-input {
    margin-right: 8px;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    font-size: 14px;
    color: #1F2933;
}

.btn-primary {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39, 103, 198, 0.3);
}

.btn-block {
    display: block;
    width: 100%;
}

.alert {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 13px;
}

.alert-danger {
    background: #FEE;
    border: 1px solid #FCC;
    color: #C33;
}

.alert p {
    margin: 0;
    line-height: 1.4;
}

.text-center {
    text-align: center;
}

.mt-3 {
    margin-top: 20px;
}

.mt-4 {
    margin-top: 24px;
}

.info-box {
    background: #E3F2FD;
    border-left: 4px solid #2767C6;
    padding: 12px;
    border-radius: 4px;
}

.info-title {
    font-weight: 600;
    color: #1565C0;
    margin: 0 0 6px 0;
    font-size: 13px;
}

.info-box p {
    color: #0D47A1;
    font-size: 12px;
    line-height: 1.5;
    margin: 0;
}
</style>
@endsection

