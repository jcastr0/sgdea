{{--
    Componente: Data Line (para mostrar label: value en mobile cards)

    Props:
    - label: Etiqueta
    - value: Valor (también puede usar slot)
    - icon: Icono SVG opcional
    - mono: Usar fuente monoespaciada para el valor
--}}

@props([
    'label' => '',
    'value' => null,
    'icon' => null,
    'mono' => false,
])

<div class="flex items-center justify-between py-1">
    <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
        @if($icon)
            {!! $icon !!}
        @endif
        {{ $label }}
    </span>
    <span class="text-sm {{ $mono ? 'font-mono ' : '' }}font-medium text-gray-900 dark:text-white text-right">
        {{ $value ?? $slot }}
    </span>
</div>

