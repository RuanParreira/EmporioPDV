<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public bool $showModal = false;

    public ?int $productId = null;
    public string $productName = '';
    public float $productPrice = 0;

    public string $weight = '';

    #[On('open-weight-modal')]
    public function openModal(int $id, string $name, float $price): void
    {
        $this->productId = $id;
        $this->productName = $name;
        $this->productPrice = $price;
        $this->weight = ''; // Limpa o input sempre que abrir
        $this->resetValidation();

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(
            [
                'weight' => 'required',
            ],
            [
                'weight.required' => 'Informe o peso.',
                'weight.max' => 'Informe um peso abaixo de 100kg',
            ],
        );

        // Converte a vírgula do padrão brasileiro para o ponto do PHP
        $weightFloat = (float) str_replace(',', '.', $this->weight);

        if ($weightFloat <= 0) {
            $this->addError('weight', 'Peso deve ser maior que zero.');
            return;
        }

        if ($weightFloat > 50) {
            $this->addError('weight', 'Informe um peso de no máximo 50kg.');
            return;
        }

        // Envia o produto e o peso para o carrinho
        $this->dispatch('add-to-cart-weight', productId: $this->productId, weight: $weightFloat);

        $this->showModal = false;
    }
};
?>

<div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;">
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
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 relative z-10">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold flex gap-2 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-weight-icon lucide-weight w-5 h-5 text-primary">
                    <circle cx="12" cy="5" r="3" />
                    <path
                        d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                </svg> Informar Peso
            </h3>
            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit="save" autocomplete="off">
            @error('weight')
                <span class="text-red-500 text-xs">
                    {{ $message }}
                </span>
            @enderror
            <div class="space-y-4">
                <div class="bg-input rounded-xl p-3 text-center">
                    <p class="font-bold text-foreground">
                        {{ $productName }}
                    </p>
                    <p class="text-xs text-description">
                        R$ {{ number_format($productPrice, 2, ',', '.') }}/kg
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="peso" class="text-sm font-semibold">
                        Peso (kg)
                    </label>
                    <input type="text" id="peso" wire:model="weight" placeholder="Ex: 0,500"
                        class="flex w-full border border-border bg-background px-3 py-2 text-center ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-lg h-11">
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="open = false"
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancelar</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-transform active:scale-95 cursor-pointer">Confirmar</button>
                </div>
            </div>
        </form>
    </div>
</div>
