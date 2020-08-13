@props([
    'isTitle' => false,
    'label' => '',
    'url' => '#',
    'level' => 0,
])

<li class="pl-4">
    @if ($isTitle)
        <p class="font-bold nav-menu__item text-gray-500 text-xs tracking-wider uppercase mt-5">
            {{ $label }}
        </p>
    @else
        <a href="{{ $url }}"
           class="{{ 'lvl' . $level }} {{ request()->url() === $url ? 'active font-semibold text-blue-600' : '' }} nav-menu__item hover:text-blue-600"
        >
            {!! $label !!}
        </a>
    @endif

    {{ $slot }}
</li>
