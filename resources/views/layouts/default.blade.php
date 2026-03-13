<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <title>{{ $title ?? trim($__env->yieldContent('title')) }}</title>
</head>

<body class="bg-background">
    <main
        class="mt-18 w-full overflow-y-auto transition-all duration-300 lg:mt-0 lg:ml-64 lg:h-screen lg:w-[calc(100%-16rem)] p-6 space-y-6">
        <livewire:menu />
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>
    @livewireScripts
</body>

</html>
