<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EDMS') }}</title>

        <meta name="csrf_token" content="{{ csrf_token() }}" />

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;450;500;550;600;650;700;750&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/drip-theme.css') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/funaab_logo.png') }}">
    </head>
    <body class="drip-body font-sans text-gray-900 antialiased flex flex-col min-h-screen">
        <header>
                @include('partials._header_guest')
        </header>
        <main class="flex flex-1">
                {{ $slot }}
        </main>
        <footer>
             @include('partials._footer_fluid')    
        </footer>   
            
            
    </body>
</html>
