@if($href)
    <a href="{{ $href }}" class="{{ $class }}">
        {{ $slot }}
    </a>
@else
    <button type="button" class="{{ $class }}" @if($onclick) onclick="{{ $onclick }}" @endif>
        {{ $slot }}
    </button>
@endif

