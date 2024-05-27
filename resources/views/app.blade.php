<!DOCTYPE html>
{{-- fundatia principala a tuturor paginilor blade --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Exploreaza Romania</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{config('app.name', 'Laravel')}}</title>
        @vite([
            'resources/js/app.js',
            'public/assets/bootstrap-5.1.3-dist/css/bootstrap.min.css',
            'public/assets/css/jquery-ui.min.css',
            'public/assets/css/app.css',
        ])
        {{-- verificam daca este autentificat un user. Incarcam JS-ul in functie de ce user este autentificat. --}}
        @if (Auth::guard('tourist')->check())
            @vite(['resources/js/custom/tourist.js'])
        @elseif(Auth::guard('host')->check())
            @vite(['resources/js/custom/host.js'])
        @endif
    </head>
    <body>
        {{-- verificam daca userul este autentificat. In functie de user-ul autentificat aratam bara verde de sus cu linkuri pentru user (in backend) sau alte linkuri in frontend --}}
        @if (Auth::guard('tourist')->check() || Auth::guard('host')->check())
            @include('layout.userHeader')
        @else
            @include('layout.mainHeader')
        @endif
        {{-- aici va fi pus orice continut html --}}
        @yield('content')
        {{-- incarcam si sectiunea din partea de jos a pagini --}}
        @include('layout.footer')
    </body>
</html>