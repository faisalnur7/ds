<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Darus Salam CCIMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600&display=swap" rel="stylesheet" />

        <x-theme-bootstrap />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased ccims-text bg-[var(--ccims-bg)]">
        <x-flash-toast />
        @php($fullWidthPage = request()->routeIs('profile.*'))
        <div class="ccims-mobile-shell ccims-shell-bg {{ $fullWidthPage ? 'ccims-page-shell-wide' : '' }}">
            <div class="ccims-mobile-screen">
            @include('layouts.navigation', ['fullWidthPage' => $fullWidthPage])

            <!-- Page Heading -->
            @isset($header)
                <header class="hidden border-b border-white/10 bg-slate-950/60 backdrop-blur-xl sm:block">
                    <div @class([
                        'mx-auto w-full px-4 py-5 sm:px-5 sm:py-6 md:px-6 lg:px-8',
                        'max-w-none' => $fullWidthPage,
                        'max-w-[480px]' => ! $fullWidthPage,
                    ])>
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main @class([
                'mx-auto w-full px-4 py-6 pb-28 sm:px-5 sm:py-7 md:px-6 lg:px-8',
                'max-w-none' => $fullWidthPage,
                'max-w-[480px]' => ! $fullWidthPage,
            ])>
                {{ $slot }}
            </main>
            </div>
        </div>
    </body>
</html>
