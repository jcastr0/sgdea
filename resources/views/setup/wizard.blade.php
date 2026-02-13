<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Asistente de Configuración - SGDEA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Inter', 'Roboto', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #102544 0%, #0F3F5F 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .setup-wizard {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 70px rgba(16, 37, 68, 0.3);
            width: 100%;
            max-width: 1400px;
            min-height: 600px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 300px 1fr;
        }

        /* ============================================
           SIDEBAR IZQUIERDO (TIMELINE)
           ============================================ */

        .setup-sidebar {
            background: linear-gradient(135deg, #102544 0%, #0F3F5F 100%);
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            overflow-y: auto;
            max-height: 100vh;
        }

        .sidebar-header {
            text-align: center;
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }

        .sidebar-logo {
            height: 60px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-logo img {
            height: 100%;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .sidebar-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .progress-indicator {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .progress-number {
            font-size: 32px;
            font-weight: 800;
            color: #4CAF50;
            line-height: 1;
        }

        .progress-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }

        .timeline {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .timeline::-webkit-scrollbar {
            width: 6px;
        }

        .timeline::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .timeline::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .timeline-item {
            padding: 12px 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border-left: 3px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .timeline-item:hover {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .timeline-item.completed {
            background: rgba(76, 175, 80, 0.2);
            border-left-color: #4CAF50;
            color: rgba(255, 255, 255, 0.9);
        }

        .timeline-item.active {
            background: rgba(255, 255, 255, 0.25);
            border-left-color: white;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .timeline-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 14px;
        }

        /* ============================================
           CONTENIDO PRINCIPAL
           ============================================ */

        .setup-content {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow-y: auto;
            max-height: 100vh;
        }

        .setup-step {
            display: none;
        }

        .setup-step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-header {
            margin-bottom: 30px;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2767C6;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .step-title {
            font-size: 28px;
            font-weight: 700;
            color: #1F2933;
            margin-bottom: 8px;
        }

        .step-description {
            font-size: 14px;
            color: #6B7280;
            max-width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1F2933;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="color"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #D4D9E2;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="color"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2767C6;
            box-shadow: 0 0 0 3px rgba(39, 103, 198, 0.1);
        }

        .form-hint {
            font-size: 12px;
            color: #6B7280;
            margin-top: 6px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #F8D7DA;
            border-left: 4px solid #DC3545;
            color: #721C24;
        }

        .alert-success {
            background: #D4EDDA;
            border-left: 4px solid #28A745;
            color: #155724;
        }

        .alert-info {
            background: #D1ECF1;
            border-left: 4px solid #17A2B8;
            color: #0C5460;
        }

        #errorMessage {
            display: none;
        }

        #errorMessage.show {
            display: block;
        }

        /* ============================================
           LAYOUT 2 COLUMNAS EN DESKTOP
           ============================================ */

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 1024px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* Separador visual */
        .form-divider {
            grid-column: 1 / -1;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #D4D9E2;
            border-bottom: 1px solid #D4D9E2;
        }

        .form-divider h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1F2933;
            margin: 0;
        }

        /* ============================================
           BOTONES Y ACCIONES
           ============================================ */

        .setup-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            justify-content: flex-start;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #2767C6;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #0F3F5F;
            box-shadow: 0 4px 12px rgba(39, 103, 198, 0.3);
        }

        .btn-secondary {
            background: #E4E7EB;
            color: #1F2933;
        }

        .btn-secondary:hover {
            background: #D4D9E2;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn.loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            top: 50%;
            left: 50%;
            margin-left: -9px;
            margin-top: -9px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        .btn-secondary.loading::after {
            border-top-color: #1F2933;
        }

        /* Indicador de procesamiento visible */
        .processing-indicator {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .processing-indicator.show {
            display: flex;
        }

        .processing-content {
            background: white;
            padding: 30px 50px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .processing-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #E4E7EB;
            border-top: 4px solid #2767C6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .processing-text {
            font-size: 16px;
            color: #1F2933;
            font-weight: 600;
        }

        .processing-subtext {
            font-size: 13px;
            color: #6B7280;
            margin-top: 8px;
        }

        .setup-complete {
            text-align: center;
            padding: 50px 0;
        }

        .complete-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .complete-title {
            font-size: 32px;
            font-weight: 700;
            color: #28A745;
            margin-bottom: 10px;
        }

        .complete-message {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 30px;
        }

        /* ============================================
           MOBILE RESPONSIVE
           ============================================ */

        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: #2767C6;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        @media (max-width: 1024px) {
            .setup-wizard {
                grid-template-columns: 1fr;
                max-width: 100%;
            }

            .setup-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: 280px;
                height: 100vh;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .setup-sidebar.visible {
                transform: translateX(0);
            }

            .setup-sidebar::after {
                content: '';
                position: fixed;
                left: 280px;
                top: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
            }

            .setup-content {
                padding: 60px 30px 30px 30px;
                margin-top: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-group-full {
                grid-column: 1;
            }

            .step-title {
                font-size: 22px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="color"],
            select,
            textarea {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .setup-wizard {
                min-height: auto;
                border-radius: 8px;
            }

            .setup-sidebar {
                width: 100%;
                position: static;
                transform: none;
                max-height: auto;
                padding: 20px 15px;
                margin-bottom: 0;
                display: none;
            }

            .setup-sidebar.visible {
                display: flex;
                position: fixed;
                left: 0;
                top: 0;
                width: 280px;
                height: 100vh;
                z-index: 999;
            }

            .setup-sidebar.visible::after {
                content: '';
                position: fixed;
                left: 280px;
                top: 0;
                width: calc(100vw - 280px);
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
            }

            .setup-content {
                padding: 60px 20px 20px 20px;
            }

            .step-title {
                font-size: 20px;
            }

            .setup-actions {
                gap: 10px;
                flex-wrap: wrap;
            }

            .btn {
                flex: 1;
                min-width: 120px;
            }

            .complete-icon {
                font-size: 60px;
            }

            .complete-title {
                font-size: 24px;
            }
        }

        /* ============================================
           LAYOUT MEJORADO EN DESKTOP GRANDE
           ============================================ */

        @media (min-width: 1400px) {
            .setup-wizard {
                max-width: 1600px;
            }

            .setup-sidebar {
                padding: 40px 25px;
            }

            .setup-content {
                padding: 40px 50px;
            }

            .form-row {
                gap: 30px;
            }
        }

        /* ============================================
           ESTILOS PARA TEST DE CONEXIÓN
           ============================================ */

        .btn-test-connection {
            background: #17A2B8;
            color: white;
            margin-top: 10px;
            width: 100%;
        }

        .btn-test-connection:hover {
            background: #138496;
        }

        .btn-test-connection:disabled {
            background: #6C757D;
            opacity: 0.65;
        }

        .btn-test-connection.loading {
            position: relative;
            color: transparent;
        }

        .btn-test-connection.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .test-results {
            background: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }

        .test-results.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .test-result-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            background: white;
            border-left: 4px solid #DEE2E6;
        }

        .test-result-item.passed {
            border-left-color: #28A745;
            background: #F1F8F5;
        }

        .test-result-item.failed {
            border-left-color: #DC3545;
            background: #FCF1F2;
        }

        .test-result-item.pending {
            border-left-color: #FFC107;
            background: #FFFEF0;
        }

        .test-result-icon {
            font-size: 18px;
            margin-right: 12px;
            min-width: 25px;
            text-align: center;
        }

        .test-result-content {
            flex: 1;
        }

        .test-result-label {
            font-weight: 600;
            color: #2D3748;
            font-size: 14px;
        }

        .test-result-message {
            font-size: 12px;
            color: #6B7280;
            margin-top: 4px;
        }

        .test-summary {
            text-align: center;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: 600;
        }

        .test-summary.success {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }

        .test-summary.error {
            background: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }
    </style>
</head>
<body>
    <!-- INDICADOR DE PROCESAMIENTO GLOBAL -->
    <div class="processing-indicator" id="processingIndicator">
        <div class="processing-content">
            <div class="processing-spinner"></div>
            <div class="processing-text" id="processingText">Procesando...</div>
            <div class="processing-subtext" id="processingSubtext">Por favor espera mientras se completa la operación</div>
        </div>
    </div>

    <!-- BOTÓN MOBILE PARA ABRIR MENÚ -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰ Pasos</button>

    <div class="setup-wizard">
        <!-- SIDEBAR IZQUIERDO -->
        <div class="setup-sidebar" id="setupSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/logo/logo_sgdea_blanco.png') }}" alt="SGDEA Logo">
                </div>
                <div class="sidebar-title">SGDEA Setup</div>
                <div class="sidebar-subtitle">Asistente de Configuración</div>
            </div>

            <div class="progress-indicator">
                <div class="progress-number" id="progressNumber">1</div>
                <div class="progress-text">de {{ $totalSteps ?? 6 }} pasos</div>
            </div>

            <div class="timeline" id="timeline">
                @foreach($steps as $index => $step)
                <div class="timeline-item {{ $step['status'] == 'completed' ? 'completed' : ($index == 0 ? 'active' : '') }}"
                     data-step="{{ $index + 1 }}">
                    <span class="timeline-icon">
                        @if($step['status'] == 'completed')
                            ✓
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>
                    {{ $step['nombre'] }}
                </div>
                @endforeach
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="setup-content">
            <div id="errorMessage" class="alert alert-danger"></div>

            @foreach($steps as $stepIndex => $step)
            <div class="setup-step setup-step-{{ $stepIndex }} {{ $stepIndex == 0 ? 'active' : '' }}" data-step="{{ $stepIndex + 1 }}">
                <div class="step-header">
                    <div class="step-number">{{ $stepIndex + 1 }}</div>
                    <h2 class="step-title">{{ $step['nombre'] }}</h2>
                    <p class="step-description">{{ $step['descripcion'] ?? 'Complete este paso para continuar' }}</p>
                </div>

                <form id="stepForm-{{ $stepIndex + 1 }}" class="setup-form" method="POST" action="{{ route('setup.process') }}" data-step="{{ $stepIndex + 1 }}">
                    @csrf

                    <input type="hidden" name="step_key" value="{{ $step['paso_clave'] }}">

                    @if($stepIndex == 0)
                        <!-- PASO 1: ADMINISTRADOR GLOBAL DEL SISTEMA -->
                        <div class="form-divider">
                            <h4>👤 Administrador Global del Sistema</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="admin_name">Nombre Completo</label>
                                <input type="text" id="admin_name" name="admin_name" required placeholder="Tu nombre completo">
                                <div class="form-hint">Este administrador podrá crear y gestionar tenants (empresas)</div>
                            </div>

                            <div class="form-group">
                                <label for="admin_email">Email Corporativo</label>
                                <input type="email" id="admin_email" name="admin_email" required placeholder="admin@empresa.com">
                                <div class="form-hint">Tu usuario de acceso al sistema global</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="admin_password">Contraseña</label>
                                <input type="password" id="admin_password" name="admin_password" required placeholder="Mínimo 8 caracteres">
                                <div class="form-hint">Contraseña segura para el acceso global</div>
                            </div>

                            <div class="form-group">
                                <label for="admin_password_confirmation">Confirmar Contraseña</label>
                                <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required placeholder="Repite tu contraseña">
                            </div>
                        </div>

                    @elseif($stepIndex == 1)
                        <!-- PASO 2: CONFIGURACIÓN DE BASE DE DATOS -->
                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="db_host">Host de Base de Datos</label>
                                <input type="text" id="db_host" name="db_host" required value="sgdea_mysql" placeholder="localhost o IP del servidor">
                                <div class="form-hint">Nombre del servidor MySQL (en Docker: sgdea_mysql)</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="db_port">Puerto</label>
                                <input type="text" id="db_port" name="db_port" required value="3306" placeholder="3306">
                                <div class="form-hint">Puerto MySQL estándar: 3306</div>
                            </div>

                            <div class="form-group">
                                <label for="db_root_user">Usuario Root BD</label>
                                <input type="text" id="db_root_user" name="db_root_user" required value="root" placeholder="root o usuario admin">
                                <div class="form-hint">Usuario administrador de MySQL</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="db_root_password">Contraseña del Usuario Root</label>
                                <input type="password" id="db_root_password" name="db_root_password" placeholder="Contraseña del usuario root">
                                <div class="form-hint">Dejar en blanco si el usuario root no tiene contraseña</div>
                            </div>
                        </div>

                        <!-- BOTÓN DE TEST DE CONEXIÓN -->
                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <button type="button" class="btn btn-test-connection" id="testDbConnectionBtn" onclick="testDatabaseConnection()">
                                    🔌 Probar Conexión
                                </button>
                            </div>
                        </div>

                        <!-- RESULTADOS DE TEST -->
                        <div class="test-results" id="testResults">
                            <div class="test-result-item pending" id="resultConnection">
                                <div class="test-result-icon">⏳</div>
                                <div class="test-result-content">
                                    <div class="test-result-label">Conexión a MySQL</div>
                                </div>
                            </div>

                            <div class="test-result-item pending" id="resultCreateDatabase">
                                <div class="test-result-icon">⏳</div>
                                <div class="test-result-content">
                                    <div class="test-result-label">Permiso para crear base de datos</div>
                                </div>
                            </div>

                            <div class="test-result-item pending" id="resultCreateUser">
                                <div class="test-result-icon">⏳</div>
                                <div class="test-result-content">
                                    <div class="test-result-label">Permiso para crear usuario</div>
                                </div>
                            </div>

                            <div class="test-result-item pending" id="resultGrantPrivileges">
                                <div class="test-result-icon">⏳</div>
                                <div class="test-result-content">
                                    <div class="test-result-label">Permiso para otorgar privilegios</div>
                                </div>
                            </div>

                            <div class="test-summary" id="testSummary"></div>
                        </div>

                        <div class="form-divider" style="margin-top: 30px;">
                            <h4>📦 Base de Datos a Crear</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="db_name">Nombre de la Base de Datos</label>
                                <input type="text" id="db_name" name="db_name" required placeholder="sgdea_maritimos" pattern="[a-zA-Z0-9_]*">
                                <div class="form-hint">Nombre único para la BD (sin espacios, solo letras, números y guiones)</div>
                            </div>
                        </div>

                        <div class="form-divider" style="margin-top: 30px;">
                            <h4>👤 Usuario BD a Crear</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="db_user">Nombre de Usuario</label>
                                <input type="text" id="db_user" name="db_user" required placeholder="sgdea_user" pattern="[a-zA-Z0-9_]*">
                                <div class="form-hint">Usuario que accederá a la BD</div>
                            </div>

                            <div class="form-group">
                                <label for="db_user_password">Contraseña del Usuario</label>
                                <input type="password" id="db_user_password" name="db_user_password" required placeholder="Mínimo 8 caracteres">
                                <div class="form-hint">Contraseña segura para el usuario BD</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="db_user_password_confirm">Confirmar Contraseña del Usuario</label>
                                <input type="password" id="db_user_password_confirm" name="db_user_password_confirm" required>
                            </div>
                        </div>


                    @elseif($stepIndex == 2)
                        <!-- PASO 3: CREAR PRIMER TENANT Y APLICAR TEMA (COMBINADO) -->
                        <div class="form-divider">
                            <h4>🏢 Primera Empresa (Tenant) & 🎨 Tema</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="company_name">Nombre de la Empresa</label>
                                <input type="text" id="company_name" name="company_name" required placeholder="Ej: Marítimos Arboleda S.A.">
                                <div class="form-hint">Nombre oficial de tu empresa</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="company_domain">Dominio Principal</label>
                                <input type="text" id="company_domain" name="company_domain" required placeholder="Ej: maritimosarboleda.com">
                                <div class="form-hint">Dominio sin www (ejemplo: tuempresa.com)</div>
                            </div>
                        </div>

                        <div class="form-divider">
                            <h4>👤 Superadministrador del Tenant (Solo Referencia)</h4>
                        </div>

                        <p style="font-size: 13px; color: #6B7280; margin-bottom: 20px;">
                            El administrador del tenant no se crea automáticamente en este wizard. Aquí se puede registrar su email para referencia.
                        </p>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tenant_admin_name">Nombre Completo (opcional)</label>
                                <input type="text" id="tenant_admin_name" name="tenant_admin_name" placeholder="Nombre del administrador">
                                <div class="form-hint">Para referencia solamente</div>
                            </div>

                            <div class="form-group">
                                <label for="tenant_admin_email">Email (opcional)</label>
                                <input type="email" id="tenant_admin_email" name="tenant_admin_email" placeholder="admin@tuempresa.com">
                                <div class="form-hint">Para referencia solamente</div>
                            </div>
                        </div>

                        <div class="form-divider" style="margin-top: 20px;">
                            <h4>🎨 Configuración de Tema (opcional)</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="color_primary">Color Primario</label>
                                <input type="color" id="color_primary" name="color_primary" value="#2767C6">
                                <div class="form-hint">Color principal de la marca - opcional</div>
                            </div>

                            <div class="form-group">
                                <label for="color_primary_dark">Color Oscuro (Sidebar)</label>
                                <input type="color" id="color_primary_dark" name="color_primary_dark" value="#0F3F5F">
                                <div class="form-hint">Color para el sidebar y elementos oscuros</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="color_preview">Vista previa de colores</label>
                                <div style="display: flex; gap: 10px; height: 40px;">
                                    <div id="color_preview" style="flex:1; border-radius:6px; background:#2767C6; border:1px solid #D4D9E2;"></div>
                                    <div id="color_preview_dark" style="flex:1; border-radius:6px; background:#0F3F5F; border:1px solid #D4D9E2;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-divider">
                            <h4>🖼️ Logo de la Empresa (Opcional)</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="logo_file">Subir archivo de logo</label>
                                <input type="file" id="logo_file" name="logo_file" accept="image/png, image/svg+xml, image/jpeg">
                                <div class="form-hint">PNG, SVG o JPG (máx 500KB)</div>
                            </div>

                            <div class="form-group">
                                <label for="logo_svg_code">O pegar código SVG directamente</label>
                                <textarea id="logo_svg_code" name="logo_svg_code" rows="4" placeholder="<svg>...</svg>" style="font-family: monospace; font-size: 12px;"></textarea>
                                <div class="form-hint">Pega el código SVG de tu logo si lo tienes</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label>Vista previa del logo</label>
                                <div id="logo_preview" style="height: 80px; border: 2px dashed #D4D9E2; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #F8F9FA; padding: 10px;">
                                    <span style="color: #6B7280; font-size: 13px;">Vista previa del logo aparecerá aquí</span>
                                </div>
                            </div>
                        </div>

                        <script>
                            (function(){
                                // Color pickers
                                const colorInput = document.getElementById('color_primary');
                                const colorDarkInput = document.getElementById('color_primary_dark');
                                const preview = document.getElementById('color_preview');
                                const previewDark = document.getElementById('color_preview_dark');

                                if(colorInput && preview){
                                    colorInput.addEventListener('input', () => {
                                        preview.style.background = colorInput.value;
                                    });
                                }
                                if(colorDarkInput && previewDark){
                                    colorDarkInput.addEventListener('input', () => {
                                        previewDark.style.background = colorDarkInput.value;
                                    });
                                }

                                // Logo preview
                                const logoFile = document.getElementById('logo_file');
                                const logoSvgCode = document.getElementById('logo_svg_code');
                                const logoPreview = document.getElementById('logo_preview');

                                if(logoFile && logoPreview){
                                    logoFile.addEventListener('change', (e) => {
                                        const file = e.target.files[0];
                                        if(file){
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                if(file.type === 'image/svg+xml'){
                                                    logoPreview.innerHTML = e.target.result;
                                                    const svg = logoPreview.querySelector('svg');
                                                    if(svg){
                                                        svg.style.maxHeight = '70px';
                                                        svg.style.width = 'auto';
                                                    }
                                                } else {
                                                    logoPreview.innerHTML = `<img src="${e.target.result}" style="max-height: 70px; width: auto;">`;
                                                }
                                            };
                                            if(file.type === 'image/svg+xml'){
                                                reader.readAsText(file);
                                            } else {
                                                reader.readAsDataURL(file);
                                            }
                                        }
                                    });
                                }

                                if(logoSvgCode && logoPreview){
                                    logoSvgCode.addEventListener('input', () => {
                                        const svgCode = logoSvgCode.value.trim();
                                        if(svgCode.startsWith('<svg') && svgCode.includes('</svg>')){
                                            logoPreview.innerHTML = svgCode;
                                            const svg = logoPreview.querySelector('svg');
                                            if(svg){
                                                svg.style.maxHeight = '70px';
                                                svg.style.width = 'auto';
                                            }
                                        }
                                    });
                                }
                            })();
                        </script>


                    @elseif($stepIndex == 4)
                        <!-- PASO 5: CONFIGURACIÓN EMAIL (OPCIONAL) -->
                        <div class="alert alert-warning" style="margin-bottom: 20px;">
                            <strong>⚠️ PASO OPCIONAL</strong> - Puedes configurar notificaciones por email ahora o saltarlo.
                        </div>

                        <div class="form-divider">
                            <h4>📧 Configuración de Correo Electrónico</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="mail_host">Host SMTP</label>
                                <input type="text" id="mail_host" name="mail_host" placeholder="smtp.gmail.com">
                                <div class="form-hint">Servidor SMTP de tu proveedor de email</div>
                            </div>

                            <div class="form-group">
                                <label for="mail_port">Puerto SMTP</label>
                                <input type="number" id="mail_port" name="mail_port" placeholder="587" min="1" max="65535">
                                <div class="form-hint">Puerto (587 para TLS, 465 para SSL)</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="mail_username">Email/Usuario SMTP</label>
                                <input type="email" id="mail_username" name="mail_username" placeholder="tu-email@gmail.com">
                                <div class="form-hint">Usuario o email para autenticarse</div>
                            </div>

                            <div class="form-group">
                                <label for="mail_password">Contraseña/App Password</label>
                                <input type="password" id="mail_password" name="mail_password" placeholder="Contraseña o app password">
                                <div class="form-hint">Contraseña de acceso SMTP</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="mail_encryption">Cifrado</label>
                                <select id="mail_encryption" name="mail_encryption">
                                    <option value="">Sin cifrado</option>
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="mail_from_address">Email de Notificaciones</label>
                                <input type="email" id="mail_from_address" name="mail_from_address" placeholder="noreply@empresa.com">
                                <div class="form-hint">Email desde el que se enviarán las notificaciones</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="mail_from_name">Nombre del Remitente</label>
                                <input type="text" id="mail_from_name" name="mail_from_name" placeholder="SGDEA">
                                <div class="form-hint">Nombre que aparecerá como remitente</div>
                            </div>
                        </div>

                        <input type="hidden" id="skip_email_config" name="skip_email_config" value="0">

                    @elseif($stepIndex == 5)
                        <!-- PASO 6: CONFIGURACIÓN LDAP (OPCIONAL) -->
                        <div class="alert alert-warning" style="margin-bottom: 20px;">
                            <strong>⚠️ PASO OPCIONAL</strong> - Configura LDAP si tu empresa usa directorio activo o puedes saltarlo.
                        </div>

                        <div class="form-divider">
                            <h4>🔐 Configuración de Autenticación LDAP</h4>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ldap_host">Host LDAP</label>
                                <input type="text" id="ldap_host" name="ldap_host" placeholder="ldap.empresa.com">
                                <div class="form-hint">Dirección del servidor LDAP</div>
                            </div>

                            <div class="form-group">
                                <label for="ldap_port">Puerto LDAP</label>
                                <input type="number" id="ldap_port" name="ldap_port" placeholder="389" min="1" max="65535">
                                <div class="form-hint">Puerto (389 para LDAP, 636 para LDAPS)</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label for="ldap_base_dn">Base DN</label>
                                <input type="text" id="ldap_base_dn" name="ldap_base_dn" placeholder="dc=empresa,dc=com">
                                <div class="form-hint">Base DN para búsquedas (ej: dc=empresa,dc=com)</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ldap_username">Usuario Vinculación</label>
                                <input type="text" id="ldap_username" name="ldap_username" placeholder="cn=admin,dc=empresa,dc=com">
                                <div class="form-hint">DN del usuario para conectarse al LDAP</div>
                            </div>

                            <div class="form-group">
                                <label for="ldap_password">Contraseña Vinculación</label>
                                <input type="password" id="ldap_password" name="ldap_password" placeholder="Contraseña">
                                <div class="form-hint">Contraseña del usuario de vinculación</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ldap_user_filter">Filtro de Usuario</label>
                                <input type="text" id="ldap_user_filter" name="ldap_user_filter" placeholder="(&(objectClass=person)(uid=%s))">
                                <div class="form-hint">Filtro LDAP para buscar usuarios (%s será reemplazado)</div>
                            </div>

                            <div class="form-group">
                                <label for="ldap_email_attr">Atributo Email</label>
                                <input type="text" id="ldap_email_attr" name="ldap_email_attr" placeholder="mail">
                                <div class="form-hint">Atributo LDAP que contiene el email</div>
                            </div>
                        </div>

                        <input type="hidden" id="skip_ldap_config" name="skip_ldap_config" value="0">

                    @elseif($stepIndex == 6)
                        <!-- PASO 7: VERIFICACIÓN FINAL -->
                        <div class="alert alert-info" style="margin-bottom: 30px;">
                            <h4 style="margin-top: 0;">✓ Verificación de Configuración</h4>
                            <p>La configuración está completa. Revisa el resumen y haz clic en "Completar Instalación" para finalizar.</p>
                        </div>

                        <div class="form-divider">
                            <h4>📋 Resumen de Configuración</h4>
                        </div>

                        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <strong>✓ Administrador Global</strong><br>
                                    <small>Paso 1</small>
                                </div>
                                <div>
                                    <strong>✓ Base de Datos</strong><br>
                                    <small>Paso 2</small>
                                </div>
                                <div>
                                    <strong>✓ Tenant y Tema</strong><br>
                                    <small>Paso 3</small>
                                </div>
                                <div>
                                    <strong id="email-status">○ Configuración Email</strong><br>
                                    <small>Paso 4 (Opcional)</small>
                                </div>
                                <div>
                                    <strong id="ldap-status">○ Configuración LDAP</strong><br>
                                    <small>Paso 5 (Opcional)</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            ✅ El sistema está listo para ser instalado y usado.
                        </div>

                    @endif

                    <div class="setup-actions">
                        <button type="button" class="btn btn-secondary" id="btnPrev-{{ $stepIndex + 1 }}"
                                onclick="goToPreviousStep()" style="{{ $stepIndex == 0 ? 'display: none;' : '' }}">
                            ← Anterior
                        </button>

                        @if($stepIndex == 4 || $stepIndex == 5)
                            <!-- Pasos opcionales: Email y LDAP -->
                            <button type="button" class="btn btn-secondary" onclick="skipOptionalStep({{ $stepIndex + 1 }})">
                                ⊘ Saltar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Siguiente →
                            </button>
                        @else
                            <!-- Pasos obligatorios -->
                            <button type="submit" class="btn btn-primary" id="btnNext-{{ $stepIndex + 1 }}">
                                {{ $stepIndex == ($totalSteps - 1) ? '✓ Completar Instalación' : 'Siguiente →' }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
            @endforeach

            <!-- PANTALLA DE ÉXITO -->
            <div class="setup-step" data-step="complete">
                <div class="setup-complete">
                    <div class="complete-icon">🎉</div>
                    <h2 class="complete-title">¡Configuración Completada!</h2>
                    <p class="complete-message">
                        SGDEA ha sido instalado y configurado correctamente.<br>
                        Redirigiendo al login en 3 segundos...
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = {{ $totalSteps ?? 6 }};
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const setupSidebar = document.getElementById('setupSidebar');

        console.log('===== SETUP WIZARD INICIALIZANDO =====');
        console.log('totalSteps desde PHP:', totalSteps);
        console.log('currentStep inicial:', currentStep);

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded disparado');
            console.log('Pasos disponibles en DOM:', Array.from(document.querySelectorAll('[data-step]')).map(el => ({
                step: el.dataset.step,
                display: el.style.display || 'auto'
            })));

            setupFormHandlers();
            setupMobileMenu();

            // Asegurar que el paso 1 esté visible
            goToStep(1);
        });

        // ============================================
        // MANEJO DE MENÚ MOBILE
        // ============================================

        function setupMobileMenu() {
            mobileMenuToggle.addEventListener('click', () => {
                setupSidebar.classList.toggle('visible');
            });

            // Cerrar menú al hacer clic en un paso
            document.querySelectorAll('.timeline-item').forEach(item => {
                item.addEventListener('click', () => {
                    const isMobile = window.innerWidth <= 1024;
                    if (isMobile) {
                        setupSidebar.classList.remove('visible');
                    }
                });
            });

            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.setup-sidebar') && !e.target.closest('.mobile-menu-toggle')) {
                    setupSidebar.classList.remove('visible');
                }
            });
        }

        // ============================================
        // TEST DE CONEXIÓN A BD
        // ============================================

        async function testDatabaseConnection() {
            const testBtn = document.getElementById('testDbConnectionBtn');
            const resultsDiv = document.getElementById('testResults');

            if (!testBtn || !resultsDiv) {
                console.error('Elementos no encontrados en el DOM');
                return;
            }

            // Obtener valores del formulario
            const dbHost = document.getElementById('db_host')?.value?.trim();
            const dbPort = document.getElementById('db_port')?.value?.trim();
            const dbRootUser = document.getElementById('db_root_user')?.value?.trim();
            const dbRootPassword = document.getElementById('db_root_password')?.value || '';

            // Validar campos requeridos
            if (!dbHost || !dbPort || !dbRootUser) {
                showError('Por favor completa: Host, Puerto y Usuario Root');
                return;
            }

            // Desabilitar botón y mostrar loading
            testBtn.disabled = true;
            testBtn.classList.add('loading');
            resultsDiv.classList.add('show');

            try {
                console.log('Iniciando test de conexión...', {
                    host: dbHost,
                    port: dbPort,
                    user: dbRootUser
                });

                // Usar endpoint PHP directo (sin pasar por middleware de Laravel)
                const formData = new FormData();
                formData.append('db_host', dbHost);
                formData.append('db_port', dbPort);
                formData.append('db_root_user', dbRootUser);
                formData.append('db_root_password', dbRootPassword);

                const response = await fetch('/test-db-connection.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Test result:', data);

                updateTestResults(data);

            } catch (error) {
                console.error('Error en test:', error);
                showError('Error al probar conexión: ' + error.message);
            } finally {
                testBtn.disabled = false;
                testBtn.classList.remove('loading');
            }
        }

        function updateTestResults(result) {
            const checks = result || {};
            const messages = result.messages || {};

            // Actualizar cada check
            updateCheckResult('connection', checks.connection, messages.connection);
            updateCheckResult('create_database', checks.create_database, messages.create_database);
            updateCheckResult('create_user', checks.create_user, messages.create_user);
            updateCheckResult('grant_privileges', checks.grant_privileges, messages.grant_privileges);

            // Actualizar resumen
            const summary = document.getElementById('testSummary');
            const allPassed = checks.connection && checks.create_database &&
                            checks.create_user && checks.grant_privileges;

            if (allPassed) {
                summary.className = 'test-summary success';
                summary.innerHTML = '✅ Todas las validaciones pasaron correctamente';
            } else {
                summary.className = 'test-summary error';
                summary.innerHTML = '❌ Algunas validaciones fallaron. Revisa los detalles abajo.';
            }
        }

        function updateCheckResult(checkType, passed, message) {
            const checkIds = {
                'connection': 'resultConnection',
                'create_database': 'resultCreateDatabase',
                'create_user': 'resultCreateUser',
                'grant_privileges': 'resultGrantPrivileges',
            };

            const elementId = checkIds[checkType];
            const element = document.getElementById(elementId);

            if (!element) return;

            // Actualizar clase
            element.classList.remove('pending', 'passed', 'failed');
            element.classList.add(passed ? 'passed' : 'failed');

            // Actualizar icono
            const icon = element.querySelector('.test-result-icon');
            icon.textContent = passed ? '✅' : '❌';

            // Actualizar mensaje
            const messageEl = element.querySelector('.test-result-message');
            if (messageEl && message) {
                messageEl.textContent = message;
            }
        }

        // ============================================
        // MANEJO DE FORMULARIOS
        // ============================================

        function setupFormHandlers() {
            document.querySelectorAll('form.setup-form').forEach(form => {
                form.addEventListener('submit', handleFormSubmit);
            });

            document.querySelectorAll('.timeline-item').forEach(item => {
                item.addEventListener('click', () => {
                    const step = parseInt(item.dataset.step);
                    goToStep(step);
                });
            });

            // Event listener para botón de test de conexión (paso 2)
            const testBtn = document.getElementById('testDbConnectionBtn');
            if (testBtn) {
                testBtn.addEventListener('click', testDatabaseConnection);
            }
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const step = parseInt(form.dataset.step);

            console.log('handleFormSubmit: step=', step, 'form:', form.id);

            // Validar formulario
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Validaciones especiales
            if (step === 1) {
                if (!validateStep1(form)) return;
            } else if (step === 2) {
                if (!validateStep2(form)) return;
            } else if (step === 3) {
                if (!validateStep3(form)) return;
            }

            submitStep(form, step);
        }

        function validateStep1(form) {
            const adminEmail = form.querySelector('input[name="admin_email"]').value;
            const adminPassword = form.querySelector('input[name="admin_password"]').value;
            const adminPasswordConfirm = form.querySelector('input[name="admin_password_confirmation"]').value;


            if (!adminEmail.includes('@')) {
                showError('El email debe ser válido');
                return false;
            }

            if (adminPassword.length < 8) {
                showError('La contraseña debe tener mínimo 8 caracteres');
                return false;
            }

            if (adminPassword !== adminPasswordConfirm) {
                showError('Las contraseñas no coinciden');
                return false;
            }

            return true;
        }

        function validateStep2(form) {
            const dbHost = form.querySelector('input[name="db_host"]').value;
            const dbPort = form.querySelector('input[name="db_port"]').value;
            const dbRootUser = form.querySelector('input[name="db_root_user"]').value;
            const dbName = form.querySelector('input[name="db_name"]').value;
            const dbUser = form.querySelector('input[name="db_user"]').value;
            const dbUserPassword = form.querySelector('input[name="db_user_password"]').value;
            const dbUserPasswordConfirm = form.querySelector('input[name="db_user_password_confirm"]').value;

            if (!dbHost || !dbPort || !dbRootUser || !dbName || !dbUser || !dbUserPassword) {
                showError('Completa todos los campos requeridos de la BD');
                return false;
            }

            if (isNaN(dbPort) || dbPort < 1 || dbPort > 65535) {
                showError('El puerto debe ser un número válido (1-65535)');
                return false;
            }

            if (!/^[a-zA-Z0-9_]+$/.test(dbName)) {
                showError('Nombre de BD: solo letras, números y guiones bajos');
                return false;
            }

            if (!/^[a-zA-Z0-9_]+$/.test(dbUser)) {
                showError('Usuario BD: solo letras, números y guiones bajos');
                return false;
            }

            if (dbUserPassword.length < 8) {
                showError('Contraseña del usuario debe tener mínimo 8 caracteres');
                return false;
            }

            if (dbUserPassword !== dbUserPasswordConfirm) {
                showError('Las contraseñas del usuario BD no coinciden');
                return false;
            }

            return true;
        }

        function validateStep3(form) {
            const companyName = form.querySelector('input[name="company_name"]').value;
            const companyDomain = form.querySelector('input[name="company_domain"]').value;

            if (!companyName || !companyDomain) {
                showError('Nombre de empresa y dominio son requeridos');
                return false;
            }

            if (!companyDomain.includes('.')) {
                showError('El dominio debe ser válido (Ej: empresa.com)');
                return false;
            }

            return true;
        }

        function submitStep(form, step) {
            console.log('submitStep: enviando paso', step);

            // Mostrar indicador de procesamiento global
            showProcessing(getStepName(step));

            // Obtener y deshabilitar el botón de submit
            const submitBtn = form.querySelector('button[type="submit"]');
            const prevBtn = form.querySelector('button[id^="btnPrev-"]');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                submitBtn.dataset.originalText = submitBtn.innerHTML;
            }
            if (prevBtn) {
                prevBtn.disabled = true;
            }

            // Convertir FormData a JSON
            const formData = new FormData(form);
            const jsonData = {};
            for (let [key, value] of formData.entries()) {
                jsonData[key] = value;
            }

            const csrfToken = form.querySelector('input[name="_token"]')?.value;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) throw new Error('Error en respuesta: ' + response.status);
                return response.json();
            })
            .then(result => {
                console.log('submitStep resultado:', result);
                hideProcessing();
                if (result.success) {
                    console.log('Paso exitoso, actualizando progreso y navegando al siguiente');
                    updateProgress(step);
                    goToNextStep();
                } else {
                    console.log('Paso fallido:', result.message);
                    showError(result.message || 'Error procesando el paso');
                }
            })
            .catch(error => {
                console.error('submitStep error:', error);
                hideProcessing();
                showError('Error: ' + error.message);
            })
            .finally(() => {
                // Restaurar botones
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                    if (submitBtn.dataset.originalText) {
                        submitBtn.innerHTML = submitBtn.dataset.originalText;
                    }
                }
                if (prevBtn) {
                    prevBtn.disabled = false;
                }
            });
        }

        function getStepName(step) {
            const names = {
                1: 'Validando datos del administrador',
                2: 'Probando conexión a base de datos',
                3: 'Configurando tenant y tema',
                4: 'Configurando email',
                5: 'Configurando LDAP',
                6: 'Ejecutando instalación completa'
            };
            return names[step] || 'Procesando paso ' + step;
        }

        function showProcessing(message, submessage) {
            const indicator = document.getElementById('processingIndicator');
            const text = document.getElementById('processingText');
            const subtext = document.getElementById('processingSubtext');

            if (text) text.textContent = message || 'Procesando...';
            if (subtext) subtext.textContent = submessage || 'Por favor espera mientras se completa la operación';
            if (indicator) indicator.classList.add('show');
        }

        function hideProcessing() {
            const indicator = document.getElementById('processingIndicator');
            if (indicator) indicator.classList.remove('show');
        }

        function goToNextStep() {
            console.log('goToNextStep: currentStep:', currentStep, 'totalSteps:', totalSteps);
            if (currentStep < totalSteps) {
                goToStep(currentStep + 1);
            } else {
                console.log('Setup completado, mostrando pantalla de éxito');
                showComplete();
            }
        }

        async function goToPreviousStep() {
            if (currentStep > 1) {
                // Obtener el step_key del paso actual
                const currentForm = document.getElementById('stepForm-' + currentStep);
                if (!currentForm) {
                    goToStep(currentStep - 1);
                    return;
                }

                const stepKey = currentForm.querySelector('input[name="step_key"]')?.value;
                const prevBtn = currentForm.querySelector('button[id^="btnPrev-"]');

                // Mostrar loading
                if (prevBtn) {
                    prevBtn.disabled = true;
                    prevBtn.classList.add('loading');
                }

                if (!stepKey) {
                    // Si no hay step_key, simplemente vuelve
                    if (prevBtn) {
                        prevBtn.disabled = false;
                        prevBtn.classList.remove('loading');
                    }
                    goToStep(currentStep - 1);
                    return;
                }

                // Llamar al endpoint para revertir el paso
                try {
                    const csrfToken = document.querySelector('input[name="_token"]')?.value;
                    const response = await fetch('{{ route("setup.go-back") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            step_key: stepKey,
                            current_step_order: currentStep
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        console.log('Volviendo al paso anterior');
                        goToStep(currentStep - 1);
                    } else {
                        showError('Error: ' + (result.message || 'No se pudo volver atrás'));
                    }
                } catch (error) {
                    console.error('Error al volver atrás:', error);
                    showError('Error: ' + error.message);
                } finally {
                    if (prevBtn) {
                        prevBtn.disabled = false;
                        prevBtn.classList.remove('loading');
                    }
                }
            }
        }

        async function goToStep(step) {
            if (step < 1 || step > totalSteps) {
                console.warn('goToStep: paso inválido', step, 'totalSteps:', totalSteps);
                return;
            }

            // Validar que el usuario pueda acceder a este paso
            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                const response = await fetch('{{ route("setup.validate-access") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        step_order: step
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    showError('No puedes acceder a este paso. ' + (result.message || ''));
                    console.warn('Acceso denegado al paso:', step, result.message);
                    return;
                }
            } catch (error) {
                console.error('Error al validar acceso:', error);
                // Continuar de todas formas para no bloquear
            }

            console.log('goToStep:', step, 'currentStep antes:', currentStep);

            // Ocultar todos los pasos (remover clase active)
            document.querySelectorAll('.setup-step').forEach(el => {
                el.classList.remove('active');
            });

            // Mostrar nuevo paso (agregar clase active)
            const newStep = document.querySelector(`.setup-step[data-step="${step}"]`);
            if (newStep) {
                console.log('Elemento encontrado para paso:', step);
                newStep.classList.add('active');
            } else {
                console.warn('Elemento NO encontrado para paso:', step);
                console.log('Elementos disponibles:', Array.from(document.querySelectorAll('.setup-step')).map(el => el.dataset.step));
            }

            // Actualizar timeline
            document.querySelectorAll('.timeline-item').forEach((item) => {
                item.classList.remove('active');
                if (parseInt(item.dataset.step) === step) {
                    item.classList.add('active');
                }
            });

            // Actualizar número de progreso
            document.getElementById('progressNumber').textContent = step;
            currentStep = step;
            hideError();

            console.log('goToStep completado, currentStep ahora:', currentStep);

            // Scroll al inicio en mobile
            if (window.innerWidth <= 1024) {
                document.querySelector('.setup-content').scrollTop = 0;
            }
        }

        function updateProgress(step) {
            const item = document.querySelector(`.timeline-item[data-step="${step}"]`);
            if (item) {
                item.classList.add('completed');
                item.classList.remove('active');
                item.querySelector('.timeline-icon').textContent = '✓';
            }
        }

        function showComplete() {
            // Ocultar todos los pasos (remover clase active)
            document.querySelectorAll('.setup-step').forEach(el => {
                el.classList.remove('active');
            });

            const completeStep = document.querySelector('[data-step="complete"]');
            if (completeStep) {
                completeStep.classList.add('active');
            }

            // Cerrar menú mobile
            setupSidebar.classList.remove('visible');

            // Redirigir después de 3 segundos
            setTimeout(() => {
                window.location.href = '{{ route("auth.login") }}';
            }, 3000);
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideError() {
            document.getElementById('errorMessage').classList.remove('show');
        }

        function skipOptionalStep(step) {
            console.log('skipOptionalStep:', step);

            // Marcar el paso como saltado (pending)
            // y simplemente ir al siguiente paso
            updateProgress(step); // Marcar como completado visualmente

            // Obtener el nombre del paso para mostrar en el resumen
            if (step === 4) {
                const statusEl = document.getElementById('email-status');
                if (statusEl) statusEl.innerHTML = '○ Configuración Email <br><small style="color: #6B7280;">Paso 4 (Saltado)</small>';
            } else if (step === 5) {
                const statusEl = document.getElementById('ldap-status');
                if (statusEl) statusEl.innerHTML = '○ Configuración LDAP <br><small style="color: #6B7280;">Paso 5 (Saltado)</small>';
            }

            goToNextStep();
        }
    </script>
</body>
</html>

