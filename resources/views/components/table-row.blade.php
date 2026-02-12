{{--
    Componente: Data Table Row / Card Mobile
    Renderiza como <tr> en desktop y como card en móvil

    Props:
    - item: El item de datos
    - hoverable: Efecto hover (default: true)
    - clickable: Toda la fila es clickeable (default: false)
    - href: URL al hacer click en la fila
--}}

@props([
    'item' => null,
    'hoverable' => true,
    'clickable' => false,
    'href' => null,
])

{{-- Vista Desktop (tr) --}}
<tr {{ $attributes->merge([
    'class' => 'transition-colors ' .
               ($hoverable ? 'hover:bg-gray-50 dark:hover:bg-slate-800/50 ' : '') .
               ($clickable || $href ? 'cursor-pointer ' : '')
]) }}
    @if($href)
        onclick="window.location='{{ $href }}'"
    @endif
>
    {{ $slot }}
</tr>

