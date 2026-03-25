<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sale;

new class extends Component {
    public ?Sale $sale = null;
    public bool $showModal = false;

    #[On('open-sale-modal')]
    public function loadSale($saleId)
    {
        $this->sale = Sale::with(['items', 'user'])->find($saleId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->sale = null;
    }
};
?>

<div x-data="{ open: @entangle('showModal') }" x-show="open" @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
    {{-- Overlay (Fundo Escuro) --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
        class="absolute inset-0 bg-black/80"></div>

    {{-- Cartão do Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="modal max-w-lg">

        @if ($sale)
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold">
                    Venda #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}
                </h3>
                <button type="button" @click="open = false" class="cursor-pointer text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-border rounded-xl border bg-gray-200/80 p-3">
                        <p class="text-description text-xs font-semibold">
                            Data
                        </p>
                        <p class="font-bold">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="border-border rounded-xl border bg-gray-200/80 p-3">
                        <p class="text-description text-xs font-semibold">
                            Pagamento
                        </p>
                        <p class="font-bold capitalize">
                            {{ $sale->payment_method ?? 'Dinheiro' }}
                        </p>
                    </div>
                </div>
                <div class="border-border rounded-xl border bg-gray-200/80 p-3">
                    <p class="text-description mb-2 text-xs font-semibold">
                        Produtos
                    </p>
                    <ul class="space-y-1 text-sm font-semibold">
                        @foreach ($sale->items as $item)
                            @php $qty = (float) $item->quantity; @endphp
                            <li class="flex justify-between border-b border-gray-200/60 pb-1 last:border-0 last:pb-0">
                                <span>• {{ $item->product_name }}</span>
                                <span class="text-gray-500">
                                    {{ $qty != 1 ? $qty . 'x' : '1x' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="border-border rounded-xl border bg-gray-200/80 p-4 text-center">
                    <p class="text-description text-xs font-semibold">
                        Total
                    </p>
                    <p class="text-primary text-2xl font-extrabold">
                        R$ {{ number_format($sale->total_value, 2, ',', '.') }}
                    </p>

                    @if (in_array($sale->payment_method, ['dinheiro', null]) && $sale->received_value > $sale->total_value)
                        <div class="mt-2 text-xs font-medium text-gray-500">
                            Recebido: R$ {{ number_format($sale->received_value, 2, ',', '.') }} |
                            Troco: R$ {{ number_format($sale->received_value - $sale->total_value, 2, ',', '.') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
