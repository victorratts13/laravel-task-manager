<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - {{App\Models\Updater::orderByDesc('id')->first()->version}}</title>
    <link rel="shortcut icon" href="{{asset('/app.png')}}" type="image/png">
    <link rel="stylesheet" href="{{asset('/tailwind/app.css')}}">
    <wireui:scripts />
</head>

<body>
    {{ $slot }}
</body>
</html>
