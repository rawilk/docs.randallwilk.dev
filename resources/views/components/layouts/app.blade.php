<x-layouts.master :title="$title ?? null" :livewire="$livewire ?? false" :repository="$repository ?? null" :nav="$nav ?? null" :version="$version ?? null">
    @if (isset($repository, $nav))
        @push('meta')
            <meta name="docsearch:version" content="{{ $version }}">
            <meta name="docsearch:project" content="{{ $repository->name }}">
        @endpush
    @endif

    <section class="container px-6 md:px-0 py-12 mx-auto">
        <div class="flex flex-col md:flex-row">
            @if (isset($nav))
                <nav class="nav-menu hidden md:block">
                    @include('partials.algolia', ['elementId' => 'algolia-search'])

                    <x-menu :items="$nav" />
                </nav>
            @endif

            <div class="w-full break-words content" style="overflow:auto;">
                {{ $slot }}
            </div>
        </div>
    </section>
</x-layouts.master>
