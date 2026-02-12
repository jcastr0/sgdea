{{-- Componente: Layout con fondo azul y logo blanco (para páginas críticas) --}}
@props([
    'title' => 'SGDEA',
    'subtitle' => null,
    'withLogo' => true,
    'logoType' => 'white_text'
])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <x-theme-styles />
    <style>
        html, body {
            background: linear-gradient(135deg, #102544 0%, #0F3F5F 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .branded-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 70px rgba(16, 37, 68, 0.3);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
        }

        .branded-header {
            background: linear-gradient(135deg, #102544 0%, #0F3F5F 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            border-bottom: 3px solid rgba(255, 255, 255, 0.1);
        }

        .branded-header-logo {
            height: 70px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .branded-header-logo img {
            height: 100%;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .branded-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .branded-header p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 0 0 0;
        }

        .branded-content {
            padding: 40px;
        }

        {{ $slot }}

        @media (max-width: 768px) {
            .branded-header {
                padding: 30px 20px;
            }

            .branded-content {
                padding: 30px 20px;
            }

            .branded-header-logo {
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="branded-container">
        @if($withLogo)
        <div class="branded-header">
            <div class="branded-header-logo">
                <x-logo type="{{ $logoType }}" size="lg" alt="{{ $title }}" />
            </div>
            <h1>{{ $title }}</h1>
            @if($subtitle)
            <p>{{ $subtitle }}</p>
            @endif
        </div>
        @endif

        <div class="branded-content">
            {{ $slot }}
        </div>
    </div>
</body>
</html>

