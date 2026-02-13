{{--
    Componente: User Avatar
    Uso: <x-admin.user-avatar :user="$user" />
         <x-admin.user-avatar :user="$user" size="lg" showStatus showName />
         <x-admin.user-avatar name="Juan Pérez" email="juan@test.com" size="sm" />

    Props:
    - user (object|null): Objeto usuario con name, email, avatar_path, status (opcional)
    - name (string): Nombre alternativo si no hay user
    - email (string): Email alternativo si no hay user
    - size (string): Tamaño (xs, sm, md, lg, xl, 2xl)
    - showStatus (bool): Mostrar indicador de estado online
    - showName (bool): Mostrar nombre al lado
    - showEmail (bool): Mostrar email debajo del nombre
    - status (string): Estado manual (active, inactive, away, busy)
    - href (string|null): Link opcional
    - ring (bool): Mostrar anillo alrededor
    - ringColor (string): Color del anillo
--}}

@props([
    'user' => null,
    'name' => null,
    'email' => null,
    'size' => 'md',
    'showStatus' => false,
    'showName' => false,
    'showEmail' => false,
    'status' => null,
    'href' => null,
    'ring' => false,
    'ringColor' => 'white',
])

@php
    // Obtener datos del usuario o de props
    $userName = $user->name ?? $name ?? 'Usuario';
    $userEmail = $user->email ?? $email ?? '';
    $userStatus = $status ?? ($user->status ?? 'active');

    // Tamaños
    $sizes = [
        'xs' => ['avatar' => 'w-6 h-6', 'text' => 'text-[10px]', 'name' => 'text-xs', 'email' => 'text-[10px]', 'status' => 'w-1.5 h-1.5'],
        'sm' => ['avatar' => 'w-8 h-8', 'text' => 'text-xs', 'name' => 'text-sm', 'email' => 'text-xs', 'status' => 'w-2 h-2'],
        'md' => ['avatar' => 'w-10 h-10', 'text' => 'text-sm', 'name' => 'text-sm', 'email' => 'text-xs', 'status' => 'w-2.5 h-2.5'],
        'lg' => ['avatar' => 'w-12 h-12', 'text' => 'text-base', 'name' => 'text-base', 'email' => 'text-sm', 'status' => 'w-3 h-3'],
        'xl' => ['avatar' => 'w-16 h-16', 'text' => 'text-lg', 'name' => 'text-lg', 'email' => 'text-sm', 'status' => 'w-3.5 h-3.5'],
        '2xl' => ['avatar' => 'w-24 h-24', 'text' => 'text-2xl', 'name' => 'text-xl', 'email' => 'text-base', 'status' => 'w-4 h-4'],
    ];

    $sizeConfig = $sizes[$size] ?? $sizes['md'];

    // Colores de estado
    $statusColors = [
        'active' => 'bg-emerald-500',
        'online' => 'bg-emerald-500',
        'inactive' => 'bg-gray-400',
        'offline' => 'bg-gray-400',
        'away' => 'bg-amber-500',
        'busy' => 'bg-red-500',
        'pending' => 'bg-amber-500',
        'blocked' => 'bg-red-500',
    ];

    $statusColorClass = $statusColors[$userStatus] ?? $statusColors['inactive'];

    // Iniciales
    $initials = collect(explode(' ', $userName))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->implode('');

    // Gradientes basados en el nombre
    $gradients = [
        'bg-gradient-to-br from-blue-500 to-blue-700',
        'bg-gradient-to-br from-emerald-500 to-teal-700',
        'bg-gradient-to-br from-purple-500 to-indigo-700',
        'bg-gradient-to-br from-amber-500 to-orange-700',
        'bg-gradient-to-br from-pink-500 to-rose-700',
        'bg-gradient-to-br from-cyan-500 to-blue-700',
        'bg-gradient-to-br from-lime-500 to-green-700',
        'bg-gradient-to-br from-red-500 to-pink-700',
    ];
    $gradientIndex = ord(mb_substr($userName, 0, 1)) % count($gradients);
    $gradient = $gradients[$gradientIndex];

    // Ring
    $ringColors = [
        'white' => 'ring-white dark:ring-slate-800',
        'gray' => 'ring-gray-200 dark:ring-slate-700',
        'primary' => 'ring-blue-500',
    ];
    $ringClass = $ring ? ('ring-2 ' . ($ringColors[$ringColor] ?? $ringColors['white'])) : '';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    {{-- Avatar --}}
    @if($href)
    <a href="{{ $href }}" class="relative flex-shrink-0">
    @else
    <div class="relative flex-shrink-0">
    @endif

        @if($user && $user->avatar_path && Storage::disk('public')->exists($user->avatar_path))
            <img
                src="{{ Storage::url($user->avatar_path) }}"
                alt="{{ $userName }}"
                class="rounded-full object-cover {{ $sizeConfig['avatar'] }} {{ $ringClass }}"
            >
        @else
            <div class="flex items-center justify-center rounded-full font-bold text-white {{ $gradient }} {{ $sizeConfig['avatar'] }} {{ $ringClass }} {{ $sizeConfig['text'] }}">
                {{ strtoupper($initials) }}
            </div>
        @endif

        {{-- Indicador de estado --}}
        @if($showStatus)
            <span class="absolute bottom-0 right-0 block rounded-full ring-2 ring-white dark:ring-slate-800 {{ $statusColorClass }} {{ $sizeConfig['status'] }}"></span>
        @endif

    @if($href)
    </a>
    @else
    </div>
    @endif

    {{-- Nombre y email --}}
    @if($showName || $showEmail)
        <div class="min-w-0 flex-1">
            @if($showName)
                <p class="font-medium text-gray-900 dark:text-white truncate {{ $sizeConfig['name'] }}">
                    {{ $userName }}
                </p>
            @endif
            @if($showEmail && $userEmail)
                <p class="text-gray-500 dark:text-gray-400 truncate {{ $sizeConfig['email'] }}">
                    {{ $userEmail }}
                </p>
            @endif
        </div>
    @endif
</div>

