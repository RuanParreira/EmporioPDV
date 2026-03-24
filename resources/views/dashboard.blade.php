@extends('layouts.default')
@section('title', 'DashBoard')
@section('content')
    <div class="space-y-4 p-6">
        <x-titulo titulo="DashBoard" descricao="Visão geral do seu negócio" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1 --}}
            <div class="flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500">
                    <i class="bi bi-currency-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        VENDAS HOJE
                    </p>
                    <p class="text-foreground text-xl font-extrabold">
                        R$ {{ number_format($vendasHoje, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10 text-green-500">
                    <i class="bi bi-calendar2-week text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        VENDAS SEMANA
                    </p>
                    <p class="text-foreground text-xl font-extrabold">
                        R$ {{ number_format($vendasSemana, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Card 3 --}}
            <div class="flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500">
                    <i class="bi bi-calendar2-week text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        VENDAS MÊS
                    </p>
                    <p class="text-foreground text-xl font-extrabold">
                        R$ {{ number_format($vendasMes, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500/10 text-orange-500">
                    <i class="bi bi-bag-dash text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        PEDIDOS HOJE
                    </p>
                    <p class="text-foreground text-xl font-extrabold">
                        {{ $pedidosHoje }}
                    </p>
                </div>
            </div>
            {{-- Card 5 --}}
            <div class="col-span-2 flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        TICKET MÉDIO
                    </p>
                    <p class="text-foreground text-xl font-extrabold">
                        R$ {{ number_format($ticketMedio, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            {{-- Card 6 --}}
            <div class="col-span-2 flex items-center gap-4 rounded-xl bg-white p-5 shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-500/10 text-yellow-500">
                    <i class="bi bi-star text-xl"></i>
                </div>
                <div>
                    <p class="text-description text-xs font-semibold uppercase tracking-wide">
                        MAIS VENDIDO
                    </p>
                    <p class="text-foreground text-xl font-extrabold uppercase">
                        {{ $maisVendido }}
                    </p>
                </div>
            </div>
        </div>
        {{-- Possivel mudança para adicionar outra coisa --}}
        {{-- <div class="grid grid-cols-2 gap-4">
    </div> --}}
        <livewire:grafico />
    @endsection
</div>
