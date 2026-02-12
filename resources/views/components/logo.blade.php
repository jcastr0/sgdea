<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    height="{{ $height }}"
    class="logo {{ $class }}"
    style="object-fit: contain; max-width: 100%;"
/>

<style>
    .logo {
        display: inline-block;
        height: {{ $height }};
        width: auto;
        object-fit: contain;
        max-width: 100%;
    }
</style>

