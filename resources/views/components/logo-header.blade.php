{{-- Logo Header - para usar en headers/navbars oscuros --}}
<div class="logo-header">
    @if($withText)
        <x-logo type="white_text" size="md" alt="{{ $alt }}" />
    @else
        <x-logo type="white" size="md" alt="{{ $alt }}" />
    @endif
</div>

<style>
    .logo-header {
        display: flex;
        align-items: center;
        padding: var(--spacing-sm) 0;
    }

    .logo-header img {
        height: 50px;
        width: auto;
        object-fit: contain;
    }
</style>

