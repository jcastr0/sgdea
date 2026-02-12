<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PermissionButton extends Component
{
    public function __construct(
        public string $permission,
        public ?string $class = 'btn btn-primary',
        public ?string $href = null,
        public ?string $onclick = null,
    ) {}

    public function render(): View|Closure|string
    {
        // Si el usuario no tiene permiso, no renderizar nada
        if (!auth()->check() || !auth()->user()->hasPermission($this->permission)) {
            return '';
        }

        return view('components.permission-button');
    }
}

