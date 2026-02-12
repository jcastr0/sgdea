{{-- Header Inteligente: detecta color de fondo y muestra logo correcto --}}
@props([
    'isDark' => false,
    'withText' => true,
    'user' => null
])

<header class="app-header" style="background: {{ $isDark ? 'linear-gradient(135deg, #102544 0%, #0F3F5F 100%)' : 'white' }};">
    <div class="header-container">
        {{-- Logo --}}
        <div class="header-logo">
            @if($isDark)
                <x-logo type="white_text" size="md" alt="SGDEA" />
            @else
                <x-logo type="text" size="md" alt="SGDEA" />
            @endif
        </div>

        {{-- Navegación --}}
        <nav class="header-nav">
            <a href="{{ route('dashboard') }}" class="nav-item">Dashboard</a>
            <a href="{{ route('facturas.index') }}" class="nav-item">Facturas</a>
            <a href="{{ route('terceros.index') }}" class="nav-item">Clientes</a>
        </nav>

        {{-- User Menu --}}
        @if($user)
        <div class="header-user">
            <span class="user-name">{{ $user->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
        @endif
    </div>
</header>

<style>
.app-header {
    padding: 20px 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
    transition: all 0.3s ease;
}

.header-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
}

.header-logo {
    display: flex;
    align-items: center;
}

.header-logo img {
    height: 50px;
    width: auto;
    object-fit: contain;
}

.header-nav {
    flex: 1;
    display: flex;
    gap: 30px;
}

.nav-item {
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    color: {{ $isDark ? 'rgba(255, 255, 255, 0.8)' : '#1F2933' }};
    transition: color 0.3s ease;
    border-bottom: 2px solid transparent;
}

.nav-item:hover {
    color: {{ $isDark ? 'white' : '#2767C6' }};
    border-bottom-color: #2767C6;
}

.header-user {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-left: 30px;
    border-left: 1px solid {{ $isDark ? 'rgba(255, 255, 255, 0.1)' : '#D4D9E2' }};
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: {{ $isDark ? 'rgba(255, 255, 255, 0.9)' : '#1F2933' }};
}

.btn-logout {
    padding: 8px 16px;
    background: {{ $isDark ? 'rgba(255, 255, 255, 0.1)' : '#E4E7EB' }};
    color: {{ $isDark ? 'white' : '#1F2933' }};
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: {{ $isDark ? 'rgba(255, 255, 255, 0.2)' : '#D4D9E2' }};
}

@media (max-width: 768px) {
    .header-container {
        gap: 20px;
    }

    .header-nav {
        display: none;
    }

    .header-user {
        flex-direction: column;
        align-items: flex-end;
        padding-left: 0;
        border-left: none;
    }
}
</style>

