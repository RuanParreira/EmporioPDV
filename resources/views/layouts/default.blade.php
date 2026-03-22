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
    <x-toast />
    <div class="flex min-h-screen w-full">
        <livewire:menu />
        <main class="flex-1 overflow-auto">

            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('ask-to-print', (data) => {
                const eventData = Array.isArray(data) ? data[0] : data;
                const saleId = eventData.saleId;

                if (saleId) {
                    const printUrl = `${"{{ url('/imprimir-recibo') }}"}/${saleId}`;

                    window.open(printUrl, '_blank', 'width=450,height=700');
                }
            });
        });
    </script>
</body>

</html>
