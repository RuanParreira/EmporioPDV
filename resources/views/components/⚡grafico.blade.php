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
<div class="bg-white border border-slate-100 rounded-xl shadow-sm p-4 sm:p-6 font-sans w-full">

    <h2 class="text-base sm:text-lg font-semibold text-description mb-6 sm:mb-8">Vendas da Semana</h2>

    <div class="relative h-60 sm:h-72 md:h-100 flex">

        <div class="flex flex-col justify-between text-right text-xs sm:text-sm text-slate-400 pr-2 sm:pr-4 pb-6 z-10">
            <span>{{ number_format($maximo, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.75, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.5, 0, ',', '.') }}</span>
            <span>{{ number_format($maximo * 0.25, 0, ',', '.') }}</span>
            <span>0</span>
        </div>

        <div class="flex-1 relative flex items-end border-l border-b border-slate-300 pb-0">

            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-6 z-0">
                <div class="w-full border-t border-dashed border-slate-200 mt-2"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full border-t border-dashed border-slate-200"></div>
                <div class="w-full"></div>
            </div>

            <div class="w-full flex justify-between items-end h-[calc(100%-1.5rem)] px-1 sm:px-4 gap-1 sm:gap-4 z-10">
                @foreach ($vendas as $item)
                    <div x-data="{ show: false, x: 0, y: 0 }" @mouseenter="show = true" @mouseleave="show = false"
                        @mousemove="x = $event.clientX; y = $event.clientY" @touchstart="show = !show"
                        class="relative flex flex-col justify-end items-center flex-1 h-full cursor-pointer">

                        <div :class="show ? 'opacity-100' : 'opacity-0'"
                            class="absolute bottom-6 top-0 w-full bg-slate-200/50 rounded-t-md transition-opacity duration-300 z-0">
                        </div>

                        <div x-show="show" x-transition.opacity.duration.200ms
                            class="fixed z-50 pointer-events-none bg-white shadow-[0_4px_20px_rgba(0,0,0,0.15)] rounded-xl w-max p-3"
                            :style="`left: ${x}px; top: ${y}px; transform: translate({{ $loop->iteration > 4 ? 'calc(-100% - 15px)' : '15px' }}, -100%);`"
                            style="display: none;">

                            <p class="text-description text-sm font-medium">{{ $item['dia'] }}</p>
                            <p class="text-primary text-sm mt-1 font-medium whitespace-nowrap">
                                Vendas : R$ {{ number_format($item['valor'], 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="w-full bg-primary rounded-t-md transition-all duration-300 z-10 relative"
                            style="height: {{ $maximo > 0 ? (float) (($item['valor'] / $maximo) * 100) : 0 }}%;">
                        </div>

                        <span
                            class="text-[10px] sm:text-sm text-slate-500 mt-2 sm:mt-3 capitalize z-10 h-6 flex items-center">{{ $item['dia'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
