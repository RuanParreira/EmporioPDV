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
    <title>EmporioCaixa</title>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
                <div class="flex flex-col items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo da Empresa" class="w-20 h-20">
                    <div class="text-center">
                        <h1 class="text-2xl font-extrabold text-primary">
                            Empório do Açaí
                            <p class="text-sm text-description font-normal">
                                Sistema de Ponto de Vendas
                            </p>
                        </h1>
                    </div>
                </div>
                @error('credentials')
                    <span class="flex items-center justify-center text-red-700 text-center">{{ $message }}</span>
                @enderror
                <form action="{{ route('auth.login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-semibold text-primary">
                            Email
                        </label>
                        <div class="relative mt-1">
                            <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-primary"></i>
                            <input type="email" id="email" name="email"
                                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#4A195C] focus:border-transparent outline-none transition"
                                placeholder="Digite seu email" value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <span class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-semibold text-primary">
                            Senha
                        </label>
                        <div class="relative mt-1">
                            <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-primary"></i>
                            <input type="password" id="password" name="password"
                                class="w-full px-4 py-3 pl-10  border border-gray-300 rounded-md focus:ring-2 focus:ring-[#4A195C] focus:border-transparent outline-none transition"
                                placeholder="Digite seu email">
                        </div>
                        @error('password')
                            <span class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit"
                        class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary hover:bg-primary/90 px-4 py-2 w-full h-12 rounded-xl text-base font-bold gap-2 text-white">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                        <span class="text-lg">
                            Entrar
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
