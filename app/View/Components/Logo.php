<?php

namespace App\View\Components;

use App\Services\LogoService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Logo extends Component
{
    public function __construct(
        public string $type = 'main',           // main, white, text, white_text
        public string $size = 'md',             // sm, md, lg, xl
        public string $class = '',
        public ?string $alt = 'SGDEA Logo',
    ) {}

    public function render(): View|Closure|string
    {
        $sizes = [
            'sm' => '40px',
            'md' => '60px',
            'lg' => '100px',
            'xl' => '150px',
        ];

        $src = match ($this->type) {
            'white' => LogoService::getWhiteLogo(),
            'text' => LogoService::getLogoWithText(),
            'white_text' => LogoService::getWhiteLogoWithText(),
            default => LogoService::getMainLogo(),
        };

        $height = $sizes[$this->size] ?? $sizes['md'];

        return view('components.logo', [
            'src' => $src,
            'height' => $height,
            'alt' => $this->alt,
            'class' => $this->class,
        ]);
    }
}

