<x-layouts.app title="404 Page not found">
    <h1>Docs not found</h1>

    <p>
        There is nothing written about <strong>/{{ request()->path() }}</strong> yet. Help us out on
        <a href="{{ config('site.github_profile') }}">Github</a> if you know more.
    </p>

    <hr>

    <p>
        What has been written? Look at all the sections <a href="{{ route('home') }}">here</a>.
    </p>

    <hr>

    <p>
        Find even more <a href="{{ config('site.open_source_url') }}">Open Source projects</a> on my website.
    </p>
</x-layouts.app>
