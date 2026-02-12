<!-- Inyectar CSS del tema del tenant -->
@php
    $tenantId = session('tenant_id');
    if ($tenantId) {
        $inlineCss = \App\Services\ThemeService::getInlineCss($tenantId);
    } else {
        // CSS default si no hay tenant
        $inlineCss = file_get_contents(public_path('css/theme-default.css'));
    }
@endphp

<style>
    {!! $inlineCss !!}
</style>

<!-- Google Fonts (Inter) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

