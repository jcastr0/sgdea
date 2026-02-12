@props([
    'name' => 'modal',
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
    'closeable' => true,
    'closeOnEscape' => true,
    'closeOnClickAway' => true,
    'footer' => null,
])

@php
    // Tamaños del modal
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        '2xl' => 'max-w-6xl',
        'full' => 'max-w-full mx-4',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    @if($closeOnEscape)
    x-on:keydown.escape.window="open = false"
    @endif
    x-cloak
    {{ $attributes }}
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40"
        @if($closeOnClickAway)
        @click="open = false"
        @endif
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title-{{ $name }}"
    >
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full {{ $sizeClass }}"
                @click.stop
            >
                {{-- Header --}}
                @if($title || $closeable)
                    <div class="flex items-start justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-slate-700">
                        <div>
                            @if($title)
                                <h3 id="modal-title-{{ $name }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $title }}
                                </h3>
                            @endif
                            @if($subtitle)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $subtitle }}
                                </p>
                            @endif
                        </div>
                        @if($closeable)
                            <button
                                @click="open = false"
                                type="button"
                                class="ml-4 rounded-lg p-1 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Body --}}
                <div class="p-4 sm:p-6">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @if($footer)
                    <div class="flex items-center justify-end gap-3 p-4 sm:p-6 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
