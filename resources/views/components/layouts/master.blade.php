<x-layouts.base :title="$title ?? null" :livewire="$livewire ?? false">
    <header class="header" role="banner">
        <div class="flex items-end container mx-auto">
            @if (isset($repository))
                <div class="flex flex-col justify-center">
                    <div class="flex items-center">
                        <a href="{{ $repository->homeUrl($version) }}"
                           class="text-white hover:text-white text-3xl font-bold"
                        >
                            {{ $repository->name }}
                        </a>

                        <x-dropdown class="ml-4">
                            <x-slot name="trigger">
                                <span class="rounded-md shadow-sm">
                                    <button @click="open = ! open"
                                            type="button"
                                            class="uppercase inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150"
                                            id="versions-menu"
                                            aria-haspopup="true"
                                    >
                                        {{ $version }}
                                        <svg class="-mr-1 ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            @foreach ($repository->branches as $branch => $alias)
                                <a href="{{ $repository->homeUrl($alias) }}"
                                   class="block px-4 py-2 text-lg uppercase leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                                   role="menuitem"
                                >
                                    {{ $alias }}
                                </a>
                            @endforeach
                        </x-dropdown>
                    </div>

                    <span class="hidden md:block text-white">
                        {{ $repository->description }}
                        @if (! $repository->isCurrentVersion($version))
                            - <a href="{{ $repository->homeUrl() }}" class="font-bold text-white hover:text-white">A newer version is available!</a>
                        @endif
                    </span>
                </div>
            @else
                <div class="flex flex-col justify-center">
                    <a href="{{ route('home') }}"
                       aria-label="{{ config('app.name') }} home"
                       title="{{ config('app.name') }} home"
                       class="text-white hover:text-white text-3xl font-bold"
                    >
                        {{ $title ?? config('app.name') }}
                    </a>
                    <span class="hidden md:block text-white">Documentation for my packages</span>
                </div>
            @endif

            <div class="hidden md:flex items-center justify-end flex-1 text-sm text-right sm:text-base">
                <a href="{{ $repository ? 'https://github.com/' . $repository->repository : config('site.github') }}">
                    <svg class="w-6 ml-3 text-white hover:opacity-75 fill-current sm:w-8 sm:ml-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>GitHub</title><path d="M10 0a10 10 0 0 0-3.16 19.49c.5.1.68-.22.68-.48l-.01-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.08 2.91.83.1-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.1.39-1.99 1.03-2.69a3.6 3.6 0 0 1 .1-2.64s.84-.27 2.75 1.02a9.58 9.58 0 0 1 5 0c1.91-1.3 2.75-1.02 2.75-1.02.55 1.37.2 2.4.1 2.64.64.7 1.03 1.6 1.03 2.69 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85l-.01 2.75c0 .26.18.58.69.48A10 10 0 0 0 10 0"></path></svg>
                </a>
                <a href="{{ config('site.github_profile') }}"
                   class="ml-2 py-1 px-2 bg-blue-600 text-white hover:opacity-75 hover:text-white focus:text-white rounded uppercase"
                >
                    Rawilk
                </a>
            </div>

            <div x-data="{ open: false }"
                 x-show="open"
                 @@set-nav-open.window="open = $event.detail"
                 class="fixed inset-0 z-20"
                 style="background: rgba(0, 0, 0, 0.5); display: none;"
            >
                <div x-show.transition.opacity="open"
                     class="fixed left-0 top-0 p-6"
                >
                    @include('partials.menu-toggle')
                </div>

                <div x-show.transition.translate="open"
                     @click.away="$dispatch('set-nav-open', false)"
                     class="bg-white bottom-0 fixed right-0 top-0 z-10 p-4 w-4/6 overflow-y-auto"
                >
                    <div class="flex items-center pt-4">
                        <a href="{{ $repository ? 'https://github.com/' . $repository->repository : config('site.github') }}">
                            <svg class="w-6 ml-3 text-black hover:opacity-75 fill-current sm:w-8 sm:ml-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>GitHub</title><path d="M10 0a10 10 0 0 0-3.16 19.49c.5.1.68-.22.68-.48l-.01-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.08 2.91.83.1-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.1.39-1.99 1.03-2.69a3.6 3.6 0 0 1 .1-2.64s.84-.27 2.75 1.02a9.58 9.58 0 0 1 5 0c1.91-1.3 2.75-1.02 2.75-1.02.55 1.37.2 2.4.1 2.64.64.7 1.03 1.6 1.03 2.69 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85l-.01 2.75c0 .26.18.58.69.48A10 10 0 0 0 10 0"></path></svg>
                        </a>
                        <a href="{{ config('site.github_profile') }}"
                           class="ml-2 py-1 px-2 bg-blue-600 text-white hover:opacity-75 hover:text-white focus:text-white rounded uppercase"
                        >
                            Rawilk
                        </a>
                    </div>

                    <div>
                        <div>
                            @if (isset($nav))
                                <hr>
                                @include('partials.algolia', ['elementId' => 'algolia-search-mobile'])
                                <x-menu :items="$nav" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.menu-toggle')
    </header>

    <main role="main" class="flex-auto w-full">
        {{ $slot }}
    </main>
</x-layouts.base>
