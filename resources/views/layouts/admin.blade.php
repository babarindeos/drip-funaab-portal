<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GoviFlow') }}</title>

        <!-- Fonts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;450;500;550;600;650;700;750&display=swap" rel="stylesheet">
        
        
        <!-- Include Font Awesome CDN -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/drip-theme.css') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/funaab_logo.png') }}">
    </head>
    <body class="drip-body font-sans text-gray-900 antialiased flex flex-col min-h-screen">
        <header>
            @include('partials._header_admin')
        </header>
        <main class="flex flex-grow w-full border-0 border-red-900 mx-auto">
            {{ $slot }}
        </main>
        <footer>
            @include('partials._footer')   
        </footer>    
            
            
    </body>
</html>
