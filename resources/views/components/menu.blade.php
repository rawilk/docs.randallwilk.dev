@props([
    'items' => [],
    'level' => 0
])

<ul class="list-none my-0">
    @foreach ($items['items'] ?? $items as $item)
        <x-menu-item :url="$item['url']" :label="$item['title']" :level="$level" />
    @endforeach

    @foreach ($items['directories'] ?? [] as $directory)
        <x-menu-item :is-title="true" :label="$directory['title']">
            <x-menu :items="$directory['items'] ?? []" :level="++$level" />
        </x-menu-item>
    @endforeach

    {{ $slot }}
</ul>
