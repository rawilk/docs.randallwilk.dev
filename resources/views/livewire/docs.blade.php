<div>
    @foreach ($categories as $category => $repositories)
        <h2>{{ $category }}</h2>

        @foreach ($repositories as $repository)
            <h3 class="mb-1">
                <a href="{{ $repository->homeUrl() }}">
                    {{ $repository->name }}
                </a>
            </h3>
            <p class="mt-0">{{ $repository->description }}</p>
        @endforeach
    @endforeach
</div>
