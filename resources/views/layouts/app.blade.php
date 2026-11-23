<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#171717">
    <title>@yield('title', config('app.name', 'RentaGo')) · {{ config('app.name', 'RentaGo') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F5F5F5] text-[#171717] antialiased selection:bg-blue-100 selection:text-blue-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:shadow-lg">Skip to content</a>
    @include('partials.navbar')
    @include('partials.alerts')
    <main id="main-content" class="min-h-[calc(100vh-9rem)]">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
