{{-- Logo Footer - para usar en footers oscuros --}}
<div class="logo-footer">
    <x-logo type="white_text" size="sm" alt="{{ $alt }}" class="footer-logo" />
</div>

<style>
    .logo-footer {
        display: flex;
        justify-content: center;
        margin: var(--spacing-lg) 0;
    }

    .logo-footer .footer-logo {
        height: 40px;
        opacity: 0.8;
        transition: opacity var(--transition-base);
    }

    .logo-footer:hover .footer-logo {
        opacity: 1;
    }
</style>

