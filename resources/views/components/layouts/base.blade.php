@props([
    'livewire' => false,
    'title' => config('app.name'),
])

<!DOCTYPE html>
<html lang="en" class="antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{ $title }}</title>
        @stack('meta')
        <link rel="home" href="{{ config('app.url') }}">

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,300i,400,400i,700,700i,800,800i,900" rel="stylesheet">
        <link rel="stylesheet" href="{!! mix('css/app.css') !!}">
        @stack('css')

        @if ($livewire)
            <livewire:styles />
        @endif
    </head>
    <body class="flex flex-col justify-between min-h-screen font-sans leading-normal text-gray-800 bg-gray-100">
        {{ $slot }}

        <footer role="contentinfo">
            <ul class="inline-flex w-auto mx-auto flex-col justify-center list-none md:list-disc md:flex-row">
                <li class="list-none md:mr-3">
                    &copy; {{ date('Y') }}
                </li>

                <li class="md:mx-3">
                    <a href="{{ route('home') }}">
                        {{ config('app.name') }}
                    </a>
                </li>

                <li class="md:ml-3">
                    <a href="{{ config('site.github') }}">
                        Github
                    </a>
                </li>
            </ul>
        </footer>

        @if ($livewire)
            <livewire:scripts />
        @endif

        <script src="{!! mix('js/app.js') !!}"></script>
        @stack('js')
    </body>
</html>
