{{--
    Componente: Action Dropdown
    Uso: <x-admin.action-dropdown :actions="[
            ['label' => 'Editar', 'href' => '/edit/1', 'icon' => 'edit'],
            ['label' => 'Eliminar', 'href' => '/delete/1', 'icon' => 'trash', 'danger' => true, 'confirm' => '¿Estás seguro?'],
            ['type' => 'divider'],
            ['label' => 'Ver logs', 'wire' => 'showLogs(1)', 'icon' => 'document'],
         ]" />

    Props:
    - actions (array): Array de acciones. Cada acción puede tener:
        - label (string): Texto del botón
        - href (string): URL de destino (opcional)
        - wire (string): Método Livewire a llamar (opcional)
        - click (string): Código JavaScript/Alpine (opcional)
        - icon (string): Nombre del icono (edit, trash, eye, download, copy, refresh, settings, user, lock, unlock, ban, check, x, link, mail, phone)
        - danger (bool): Estilo de peligro (rojo)
        - disabled (bool): Deshabilitar acción
        - confirm (string): Mensaje de confirmación antes de ejecutar
        - type (string): 'divider' para separador, 'header' para título de sección
    - label (string): Texto del botón principal
    - icon (string): Icono del botón principal
    - position (string): Posición del menú (left, right)
    - size (string): Tamaño del botón (sm, md, lg)
    - variant (string): Variante del botón (default, primary, secondary, ghost)
--}}

@props([
    'actions' => [],
    'label' => null,
    'icon' => 'dots-vertical',
    'position' => 'right',
    'size' => 'md',
    'variant' => 'ghost',
])

@php
    // Tamaños
    $sizes = [
        'sm' => 'p-1',
        'md' => 'p-1.5',
        'lg' => 'p-2',
    ];

    $buttonSize = $sizes[$size] ?? $sizes['md'];

    // Variantes del botón
    $variants = [
        'default' => 'bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700',
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600',
        'ghost' => 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-700 dark:hover:text-gray-200',
    ];

    $buttonVariant = $variants[$variant] ?? $variants['ghost'];

    // Posición del menú
    $positionClass = $position === 'left' ? 'left-0' : 'right-0';

    // ID único para Alpine
    $dropdownId = 'dropdown-' . uniqid();
@endphp

<div
    x-data="{ open: false }"
    @click.away="open = false"
    class="relative inline-block text-left"
    {{ $attributes }}
>
    {{-- Botón trigger --}}
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center justify-center rounded-lg transition-colors duration-150 cursor-pointer {{ $buttonSize }} {{ $buttonVariant }}"
    >
        @if($label)
            <span class="mr-1">{{ $label }}</span>
        @endif

        @switch($icon)
            @case('dots-vertical')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
                @break
            @case('dots-horizontal')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                @break
            @case('chevron-down')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                @break
            @default
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
        @endswitch
    </button>

    {{-- Menú dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        x-cloak
        class="absolute z-50 mt-2 w-48 rounded-lg bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-slate-700 focus:outline-none {{ $positionClass }}"
    >
        <div class="py-1">
            @foreach($actions as $action)
                @if(isset($action['type']) && $action['type'] === 'divider')
                    {{-- Separador --}}
                    <div class="my-1 border-t border-gray-200 dark:border-slate-700"></div>

                @elseif(isset($action['type']) && $action['type'] === 'header')
                    {{-- Header de sección --}}
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        {{ $action['label'] ?? '' }}
                    </div>

                @else
                    {{-- Acción normal --}}
                    @php
                        $isDanger = $action['danger'] ?? false;
                        $isDisabled = $action['disabled'] ?? false;
                        $hasConfirm = isset($action['confirm']) && $action['confirm'];

                        $itemClasses = 'group flex items-center w-full px-4 py-2 text-sm transition-colors duration-150 cursor-pointer ';

                        if ($isDisabled) {
                            $itemClasses .= 'text-gray-400 dark:text-gray-600 cursor-not-allowed';
                        } elseif ($isDanger) {
                            $itemClasses .= 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20';
                        } else {
                            $itemClasses .= 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700';
                        }

                        // Determinar el evento
                        $clickAction = '';
                        if (!$isDisabled) {
                            if ($hasConfirm) {
                                $confirmMsg = addslashes($action['confirm']);
                                if (isset($action['wire'])) {
                                    $clickAction = "if(confirm('{$confirmMsg}')) { \$wire.{$action['wire']}; open = false; }";
                                } elseif (isset($action['click'])) {
                                    $clickAction = "if(confirm('{$confirmMsg}')) { {$action['click']}; open = false; }";
                                } else {
                                    $clickAction = "if(confirm('{$confirmMsg}')) { open = false; }";
                                }
                            } else {
                                if (isset($action['wire'])) {
                                    $clickAction = "\$wire.{$action['wire']}; open = false;";
                                } elseif (isset($action['click'])) {
                                    $clickAction = "{$action['click']}; open = false;";
                                } else {
                                    $clickAction = "open = false;";
                                }
                            }
                        }
                    @endphp

                    @if(isset($action['href']) && !$isDisabled)
                        <a
                            href="{{ $action['href'] }}"
                            class="{{ $itemClasses }}"
                            @if($hasConfirm) onclick="return confirm('{{ $action['confirm'] }}')" @endif
                        >
                    @else
                        <button
                            type="button"
                            class="{{ $itemClasses }}"
                            @if(!$isDisabled) @click="{{ $clickAction }}" @endif
                            @if($isDisabled) disabled @endif
                        >
                    @endif

                        {{-- Icono --}}
                        @if(isset($action['icon']))
                            <span class="mr-3 flex-shrink-0 {{ $isDanger ? 'text-red-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}">
                                @switch($action['icon'])
                                    @case('edit')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @break
                                    @case('trash')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        @break
                                    @case('eye')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        @break
                                    @case('download')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        @break
                                    @case('copy')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        @break
                                    @case('refresh')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        @break
                                    @case('settings')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @break
                                    @case('user')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @break
                                    @case('lock')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @break
                                    @case('unlock')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                        @break
                                    @case('ban')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @break
                                    @case('check')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @break
                                    @case('x')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @break
                                    @case('link')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        @break
                                    @case('mail')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @break
                                    @case('phone')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        @break
                                    @case('document')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @break
                                    @case('switch')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        @break
                                    @default
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @endswitch
                            </span>
                        @endif

                        {{-- Label --}}
                        <span>{{ $action['label'] ?? 'Acción' }}</span>

                    @if(isset($action['href']) && !$isDisabled)
                        </a>
                    @else
                        </button>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>

