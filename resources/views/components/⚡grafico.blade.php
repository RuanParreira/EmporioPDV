<?php

use Livewire\Component;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

new class extends Component {
    public const WEEKDAYS = [
        'Sun' => 'Dom',
        'Mon' => 'Seg',
        'Tue' => 'Ter',
        'Wed' => 'Qua',
        'Thu' => 'Qui',
        'Fri' => 'Sex',
        'Sat' => 'Sáb',
    ];

    public array $vendas = [];
    public float|int $maximo = 1000;

    public function mount(): void
    {
        $inicioDaSemana = now()->startOfWeek(Carbon::MONDAY);
        $fimDaSemana = now()->endOfWeek(Carbon::SUNDAY);

        $vendasDaSemana = Sale::whereBetween('created_at', [$inicioDaSemana, $fimDaSemana])
            ->select([DB::raw('DATE(created_at) as data'), DB::raw('SUM(total_value) as total')])
            ->groupBy('data')
            ->orderBy('data', 'asc')
            ->get();

        $vendasFormatadas = [];

        for ($i = 0; $i <= 6; $i++) {
            $data = $inicioDaSemana->copy()->addDays($i);
            $dataFormatada = $data->format('Y-m-d');
            $vendaDoDia = $vendasDaSemana->firstWhere('data', $dataFormatada);
            $diaEmIngles = $data->format('D');

            $vendasFormatadas[] = [
                'dia' => self::WEEKDAYS[$diaEmIngles],
                'valor' => $vendaDoDia ? (float) $vendaDoDia->total : 0,
            ];
        }

        $maiorVenda = collect($vendasFormatadas)->max('valor');
        $valorMaximo = $maiorVenda > 0 ? $maiorVenda * 1.2 : 1000;

        $this->vendas = $vendasFormatadas;
        $this->maximo = ceil($valorMaximo / 100) * 100;
    }
};
?>
<div class="w-full rounded-xl border border-slate-100 bg-white p-4 font-sans shadow-sm sm:p-6">

    <h2 class="text-description mb-6 text-base font-semibold sm:mb-8 sm:text-lg">Vendas da Semana</h2>

    <div class="md:h-100 relative flex h-60 sm:h-72">

        <div class="z-10 flex flex-col justify-between pb-6 pr-2 text-right text-xs text-slate-400 sm:pr-4 sm:text-sm">
            <span>{{ number_format($maximo, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.75, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.5, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.25, 0, ',', '.') }}</span>
            <span>0</span>
        </div>

        <div class="relative flex flex-1 items-end border-b border-l border-slate-300 pb-0">

            <div class="pointer-events-none absolute inset-0 z-0 flex flex-col justify-between pb-6">
                <div class="mt-2 w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full"></div>
            </div>

            <div class="z-10 flex h-[calc(100%-1.5rem)] w-full items-end justify-between gap-1 px-1 sm:gap-4 sm:px-4">
                @foreach ($vendas as $item)
                    <div x-data="{ show: false, x: 0, y: 0 }" @mouseenter="show = true" @mouseleave="show = false"
                        @mousemove="x = $event.clientX; y = $event.clientY" @touchstart="show = !show"
                        class="relative flex h-full flex-1 cursor-pointer flex-col items-center justify-end">

                        <div :class="show ? 'opacity-100' : 'opacity-0'"
                            class="absolute bottom-6 top-0 z-0 w-full rounded-t-md bg-slate-200/50 transition-opacity duration-300">
                        </div>

                        <div x-show="show" x-transition.opacity.duration.200ms
                            class="pointer-events-none fixed z-50 w-max rounded-xl bg-white p-3 shadow-[0_4px_20px_rgba(0,0,0,0.15)]"
                            :style="`left: ${x}px; top: ${y}px; transform: translate({{ $loop->iteration > 4 ? 'calc(-100% - 15px)' : '15px' }}, -100%);`"
                            style="display: none;">

                            <p class="text-description text-sm font-medium">{{ $item['dia'] }}</p>
                            <p class="text-primary mt-1 whitespace-nowrap text-sm font-medium">
                                Vendas : R$ {{ number_format($item['valor'], 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="bg-primary relative z-10 w-full rounded-t-md transition-all duration-300"
                            style="height: {{ $maximo > 0 ? (float) (($item['valor'] / $maximo) * 100) : 0 }}%;">
                        </div>

                        <span
                            class="z-10 mt-2 flex h-6 items-center text-[10px] capitalize text-slate-500 sm:mt-3 sm:text-sm">{{ $item['dia'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
