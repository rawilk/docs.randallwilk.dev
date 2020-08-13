<div x-data="{ open: false }"
     @click.away="open = false"
     x-cloak
     {{ $attributes->merge(['class' => 'relative inline-block text-left']) }}
>
    <div>
        {{ $trigger ?? null }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-10"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="origin-top-right absolute left-0 mt-2 w-16 rounded-md shadow-lg"
    >
        <div class="rounded-md bg-white shadow-xs">
            <div class="py-1"
                 role="menu"
                 aria-orientation="vertical"
            >
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
