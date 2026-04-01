<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($pageTitle ?? '') ? ($pageTitle . ' - ' . ($site['site_name'] ?? config('app.name'))) : ($site['site_name'] ?? config('app.name')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? ($site['tagline'] ?? '') }}">
    @if(!empty($metaKeywords ?? ''))
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <link rel="icon" href="{{ sysconfig('site', 'site_ico') ?: '/favicon.ico' }}" type="image/x-icon">
    @vite(['resources/css/website.css', 'resources/js/website.js'])
</head>
<body class="website-body">
<div class="website-app" data-website-app>
    @include('website.partials.header')
    <main class="website-main">
        @yield('content')
    </main>
    @include('website.partials.footer')
</div>
</body>
</html>
