<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <!--    <link rel="icon" href="/favicon.ico">-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('themes/aura-light-blue/theme.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body>
<div id="app"></div>
</body>
</html>
