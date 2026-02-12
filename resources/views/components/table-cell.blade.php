{{--
    Componente: Table Cell

    Props:
    - align: Alineación (left, center, right)
    - nowrap: No hacer wrap del texto
    - primary: Es la columna principal (más destacada)
--}}

@props([
    'align' => 'left',
    'nowrap' => false,
    'primary' => false,
])

@php
    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<td {{ $attributes->merge([
    'class' => "px-4 py-3 $alignClass " .
               ($nowrap ? 'whitespace-nowrap ' : '') .
               ($primary ? 'font-medium text-gray-900 dark:text-white ' : 'text-gray-600 dark:text-gray-300 ') .
               'text-sm'
]) }}>
    {{ $slot }}
</td>

