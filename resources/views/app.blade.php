<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        @if(request()->is('admin*'))
            <link rel="icon" type="image/svg+xml" href="/admin-favicon.svg">
        @else
            @php
                $siteSettings = \App\Models\SiteSettings::current();
                $faviconPath = $siteSettings->favicon_path;
            @endphp
            @if($faviconPath)
                <link rel="icon" href="{{ $faviconPath }}">
            @else
                <link rel="icon" type="image/x-icon" href="/favicon.ico">
            @endif
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
