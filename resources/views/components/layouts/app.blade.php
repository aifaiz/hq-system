<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'HQ System' }}</title>
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body>
        <livewire:components.front.header enableorder="{{$enableOrder}}" />
        <div>
            {{ $slot }}
        </div>

        @livewireScriptConfig
    </body>
</html>
