@extends('layouts.app')

@section('content')
<div class="pending-container" style="background: linear-gradient(135deg, {{ $theme->color_primary ?? '#2767C6' }} 0%, {{ $theme->color_secondary ?? '#102544' }} 100%);">
    <div class="pending-box">
        {{-- Logo y nombre de empresa --}}
        <div class="company-header">
            <img src="{{ asset($logo) }}" alt="{{ $tenant->name }}" class="company-logo">
            <h1 class="company-name">{{ $tenant->name }}</h1>
        </div>

        {{-- Contenido --}}
        <div class="pending-content">
            <div class="pending-icon">⏳</div>

            <h2>Tu registro está pendiente de aprobación</h2>

            <p class="pending-message">
                Tu cuenta ha sido creada exitosamente. Sin embargo, requiere aprobación de un administrador antes de que puedas acceder al sistema.
            </p>

            <div class="info-steps">
                <h3>¿Qué sucede ahora?</h3>
                <ol>
                    <li>Se ha notificado al administrador de tu registro</li>
                    <li>El administrador revisará tu solicitud</li>
                    <li>Una vez aprobado, recibirás un correo de confirmación</li>
                    <li>Podrás iniciar sesión con tu cuenta</li>
                </ol>
            </div>

            {{-- Botón para volver a login --}}
            <a href="{{ route('login') }}" class="btn btn-primary" style="background: {{ $theme->color_primary ?? '#2767C6' }}">
                Volver al Login
            </a>

            {{-- Contacto --}}
            <p class="contact-info">
                Si tienes preguntas, contacta al administrador de tu empresa.
            </p>
        </div>
    </div>
</div>

<style>
.pending-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.pending-box {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    padding: 40px;
    width: 100%;
    max-width: 500px;
    text-align: center;
}

.company-header {
    margin-bottom: 40px;
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
    margin: 0;
}

.pending-content {
    padding: 20px 0;
}

.pending-icon {
    font-size: 64px;
    margin: 20px 0;
}

.pending-content h2 {
    color: #1F2933;
    font-size: 24px;
    font-weight: 700;
    margin: 20px 0;
}

.pending-message {
    color: #6B7280;
    font-size: 16px;
    line-height: 1.6;
    margin: 20px 0;
}

.info-steps {
    background: #F5F7FA;
    border-left: 4px solid #2767C6;
    padding: 20px;
    border-radius: 8px;
    text-align: left;
    margin: 30px 0;
}

.info-steps h3 {
    color: #1F2933;
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 15px 0;
}

.info-steps ol {
    color: #6B7280;
    font-size: 14px;
    line-height: 1.8;
    padding-left: 20px;
    margin: 0;
}

.info-steps li {
    margin-bottom: 8px;
}

.btn {
    display: inline-block;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    margin: 20px 0;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39, 103, 198, 0.3);
}

.contact-info {
    color: #6B7280;
    font-size: 13px;
    margin-top: 20px;
}
</style>
@endsection

