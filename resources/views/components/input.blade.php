@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md',
    'prepend' => null,
    'append' => null,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $inputId = $id ?? $name;
    $hasError = $error || ($errors->has($name) ?? false);
    $errorMessage = $error ?? ($errors->first($name) ?? null);

    // Tamaños
    $sizes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-4 py-3 text-base',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    // Clases base del input
    $inputClasses = 'block w-full rounded-lg border bg-white dark:bg-slate-800
        transition-colors duration-200
        focus:outline-none focus:ring-2 focus:ring-offset-0
        disabled:bg-gray-100 disabled:cursor-not-allowed dark:disabled:bg-slate-700
        read-only:bg-gray-50 dark:read-only:bg-slate-700';

    // Estado de error o normal
    if ($hasError) {
        $inputClasses .= ' border-red-500 text-red-900 dark:text-red-400 placeholder-red-400
            focus:border-red-500 focus:ring-red-500/20';
    } else {
        $inputClasses .= ' border-gray-300 dark:border-slate-600 text-gray-900 dark:text-gray-100
            placeholder-gray-400 dark:placeholder-gray-500
            focus:border-blue-500 focus:ring-blue-500/20 dark:focus:border-blue-400';
    }

    // Padding adicional si hay ícono
    if ($icon && $iconPosition === 'left') {
        $inputClasses .= ' pl-10';
    } elseif ($icon && $iconPosition === 'right') {
        $inputClasses .= ' pr-10';
    }
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    {{-- Label --}}
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    {{-- Input con prepend/append o ícono --}}
    <div class="relative">
        {{-- Ícono izquierdo --}}
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                {!! $icon !!}
            </div>
        @endif

        {{-- Prepend --}}
        @if($prepend)
            <div class="flex">
                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-gray-400 text-sm">
                    {{ $prepend }}
                </span>
                <input
                    type="{{ $type }}"
                    name="{{ $name }}"
                    id="{{ $inputId }}"
                    value="{{ old($name, $value) }}"
                    placeholder="{{ $placeholder }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $readonly ? 'readonly' : '' }}
                    {{ $attributes->except('class')->merge(['class' => $inputClasses . ' ' . $sizeClass . ' rounded-l-none']) }}
                >
            </div>
        {{-- Append --}}
        @elseif($append)
            <div class="flex">
                <input
                    type="{{ $type }}"
                    name="{{ $name }}"
                    id="{{ $inputId }}"
                    value="{{ old($name, $value) }}"
                    placeholder="{{ $placeholder }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $readonly ? 'readonly' : '' }}
                    {{ $attributes->except('class')->merge(['class' => $inputClasses . ' ' . $sizeClass . ' rounded-r-none']) }}
                >
                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-gray-400 text-sm">
                    {{ $append }}
                </span>
            </div>
        {{-- Input normal --}}
        @else
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes->except('class')->merge(['class' => $inputClasses . ' ' . $sizeClass]) }}
            >
        @endif

        {{-- Ícono derecho --}}
        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                {!! $icon !!}
            </div>
        @endif

        {{-- Ícono de error --}}
        @if($hasError)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Mensaje de error --}}
    @if($hasError && $errorMessage)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
            {{ $errorMessage }}
        </p>
    @endif

    {{-- Hint --}}
    @if($hint && !$hasError)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
            {{ $hint }}
        </p>
    @endif
</div>
