<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Page Title' }}</title>
    {{-- @vite('resources/css/app.css') --}}
    <link rel="stylesheet" href="{{asset('/tailwind/app.css')}}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet"> --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <wireui:scripts />
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
</head>

<body>
    {{ $slot }}
</body>
{{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}
{{-- <script src="{{asset('/alpine/alpine.js')}}"></script> --}}
{{-- @vite('resources/js/app.js') --}}
{{-- <wireui:scripts /> --}}

</html>
