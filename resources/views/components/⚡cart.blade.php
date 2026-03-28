<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;

new class extends Component {
    public array $cart = [];

    #[Validate('required', message: 'Selecione uma forma de pagamento.')]
    #[Validate('in:cartao,pix,dinheiro', message: 'Forma de pagamento inválida.')]
    public string $paymentMethod = '';

    public string $receivedValue = '';

    public function rules()
    {
        return [
            'receivedValue' => [
                'required_if:paymentMethod,dinheiro',
                'regex:/^[0-9]+(,[0-9]{1,2})?$/',
                function ($attribute, $value, $fail) {
                    if ($this->paymentMethod === 'dinheiro') {
                        $floatValue = (float) str_replace(',', '.', $value);
                        if ($floatValue < $this->cartTotal()) {
                            $fail('O valor recebido é menor que o total da venda.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'receivedValue.required_if' => 'Informe o valor recebido pelo cliente.',
            'receivedValue.regex' => 'O valor informado tem um formato inválido.',
        ];
    }

    public function updatedPaymentMethod($value)
    {
        if ($value !== 'dinheiro') {
            $this->resetValidation('receivedValue');
            $this->receivedValue = '';
        }
    }

    // Carrinho
    #[On('add-to-cart-weight')]
    public function addWeightToCart(int $productId, float $weight): void
    {
        // Se já tem o produto no carrinho, soma o peso novo
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] += $weight;
            return;
        }

        $product = Product::find($productId);

        if ($product) {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $weight,
                'observation' => null,
                'is_weight' => true,
            ];
        }
    }

    #[On('add-to-cart')]
    public function addToCart(int $productId): void
    {
        // Se o produto já está no carrinho, apenas aumentamos a quantidade
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            return;
        }

        // Se não está, buscamos no banco e adicionamos
        $product = Product::find($productId);

        if ($product) {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'observation' => null, // Campo para observações (ex: "Sem cebola")
            ];
        }
    }

    public function incrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        }
    }

    public function decrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                // Se chegar a zero, remove do carrinho
                $this->removeFromCart($productId);
            }
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    #[Computed]
    public function cartTotal(): float
    {
        $total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return round($total, 2);
    }
    //Carrinho

    //Calcular troco
    #[Computed]
    public function change(): float
    {
        if ($this->paymentMethod !== 'dinheiro' || empty($this->receivedValue)) {
            return 0.0;
        }

        $received = (float) str_replace(',', '.', $this->receivedValue);
        $total = $this->cartTotal();

        return $received > $total ? round($received - $total, 2) : 0.0;
    }

    // Inserir no banco
    public function checkout()
    {
        $this->validate();

        if (empty($this->cart)) {
            $this->addError('cart', 'O carrinho está vazio.');
            return;
        }

        $receivedFloat = null;
        if ($this->paymentMethod === 'dinheiro') {
            $receivedFloat = (float) str_replace(',', '.', $this->receivedValue);
        }

        try {
            $saleId = DB::transaction(function () use ($receivedFloat) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'total_value' => $this->cartTotal(),
                    'payment_method' => $this->paymentMethod,
                    'received_value' => $receivedFloat,
                ]);

                $itemsData = collect($this->cart)
                    ->map(function ($item) use ($sale) {
                        return [
                            'sale_id' => $sale->id,
                            'product_id' => $item['id'],
                            'product_name' => $item['name'],
                            'unit_price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'notes' => $item['observation'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })
                    ->toArray();

                // 2. Insere todos os itens de uma vez só em uma única query!
                SaleItem::insert($itemsData);

                return $sale->id;
            });

            $this->reset(['cart', 'paymentMethod', 'receivedValue']);
            $this->dispatch('notify', title: 'Venda Concluída!', type: 'success', message: 'A venda foi salva no sistema e o carrinho limpo.');
            $this->dispatch('ask-to-print', saleId: $saleId);
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar venda: ' . $e->getMessage());
            $this->addError('checkout', 'Erro ao processar venda.');
        }
    }
};
?>



<div class="flex w-96 flex-col border-l border-slate-100 bg-white shadow-sm">
    <div class="flex items-center gap-2 border-b border-slate-200 p-4">
        <i class="bi bi-cart text-primary text-lg"></i>
        <h2 class="text-lg font-bold">
            Carrinho
        </h2>
        <span class="bg-primary ml-auto rounded-full px-2 py-1 text-xs font-bold text-white">
            {{ count($cart) }}
        </span>
    </div>
    <div class="flex-1 space-y-3 overflow-y-auto p-4">
        {{-- Item do Carrinho --}}
        @forelse ($cart as $id => $item)
            <div wire:key="cart-item-{{ $id }}" class="bg-input space-y-2 rounded-xl p-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold">{{ $item['name'] }}</h4>
                        <p class="text-description text-xs">
                            @if ($item['is_weight'] ?? false)
                                R$ {{ number_format($item['price'], 2, ',', '.') }}/kg
                                <span class="text-purple-900">
                                    ×
                                    {{ number_format($item['quantity'], 3, ',', '.') }} kg
                                </span>
                            @else
                                R$ {{ number_format($item['price'], 2, ',', '.') }} un.
                            @endif
                        </p>
                    </div>
                    {{-- Botão de Remover --}}
                    <button type="button" wire:click="removeFromCart({{ $id }})"
                        class="inline-flex h-6 w-6 cursor-pointer items-center justify-center rounded-full text-red-600 transition-colors hover:bg-red-600/10">
                        <i class="bi bi-trash3 text-sm"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2">

                    {{-- Verifica se o produto é vendido por peso --}}
                    @if ($item['is_weight'] ?? false)
                        {{-- Visual para Produtos Pesados (Apenas exibe o peso em Kg) --}}
                        <div class="flex items-center justify-between">
                            <span class="text-description flex items-center gap-1 text-xs font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-weight-icon lucide-weight text-description h-3 w-3">
                                    <circle cx="12" cy="5" r="3" />
                                    <path
                                        d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                                </svg>
                                Peso: {{ number_format($item['quantity'], 3, ',', '.') }} kg
                            </span>
                        </div>
                    @else
                        {{-- Visual para Produtos por Unidade (Botões Normais) --}}
                        <button type="button" wire:click="decrementQuantity({{ $id }})"
                            class="text-description hover:bg-primary/20 flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg bg-white shadow-sm transition-colors">
                            -
                        </button>

                        <span class="w-8 text-center text-sm font-bold">
                            {{ $item['quantity'] }}
                        </span>

                        <button type="button" wire:click="incrementQuantity({{ $id }})"
                            class="text-description hover:bg-primary/20 flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg bg-white shadow-sm transition-colors">
                            +
                        </button>
                    @endif

                    {{-- O Valor Total do Item continua no canto direito --}}
                    <span class="text-foreground ml-auto text-sm font-extrabold">
                        R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                    </span>
                </div>

                {{-- Observação interligada diretamente ao item do array --}}
                <div class="flex items-center gap-2">
                    <i class="bi bi-chat text-description text-xs"></i>
                    <input type="text" wire:model.blur="cart.{{ $id }}.observation"
                        placeholder="Observação (ex: sem cebola)..."
                        class="border-border/50 focus:border-primary w-full border-b bg-transparent py-1 text-xs outline-none">
                </div>
            </div>
        @empty
            <div class="flex h-full flex-col items-center justify-center gap-2 text-gray-400 opacity-70">
                <i class="bi bi-cart-x text-4xl"></i>
                <p class="text-sm font-semibold">O carrinho está vazio</p>
            </div>
        @endforelse
    </div>

    {{-- Rodapé do Carrinho --}}
    <div class="space-y-3 border-t border-slate-200 p-4">
        {{-- Exibe mensagens de erro caso existam --}}
        @error('cart')
            <span class="block text-xs font-bold text-red-500">{{ $message }}</span>
        @enderror
        @error('paymentMethod')
            <span class="block text-xs font-bold text-red-500">{{ $message }}</span>
        @enderror
        @error('checkout')
            <span class="block text-xs font-bold text-red-500">{{ $message }}</span>
        @enderror

        <div>
            <p class="text-description mb-2 text-xs font-bold uppercase tracking-wider">
                pagamento
            </p>
            <div class="grid grid-cols-3 gap-2">
                {{-- Botão Cartão --}}
                <button type="button" wire:click="$set('paymentMethod', 'cartao')"
                    class="{{ $paymentMethod === 'cartao' ? 'bg-primary text-white ' : 'bg-input text-description border-transparent hover:bg-primary/20' }} flex cursor-pointer flex-col items-center rounded-xl border-2 py-2 text-xs font-bold transition-all">
                    <i class="bi bi-credit-card text-lg"></i>
                    Cartão
                </button>
                {{-- Botão Pix --}}
                <button type="button" wire:click="$set('paymentMethod', 'pix')"
                    class="{{ $paymentMethod === 'pix' ? 'bg-primary text-white' : 'bg-input text-description border-transparent hover:bg-primary/20' }} flex cursor-pointer flex-col items-center rounded-xl border-2 py-1 text-xs font-bold transition-all">
                    <i class="bi bi-phone text-lg"></i>
                    Pix
                </button>
                {{-- Botão Dinheiro --}}
                <button type="button" wire:click="$set('paymentMethod', 'dinheiro')"
                    class="{{ $paymentMethod === 'dinheiro' ? 'bg-primary text-white' : 'bg-input text-description border-transparent hover:bg-primary/20' }} flex cursor-pointer flex-col items-center rounded-xl border-2 py-2 text-xs font-bold transition-all">
                    <i class="bi bi-cash text-lg"></i>
                    Dinheiro
                </button>
            </div>
            {{-- Calcular troco se for dinheiro --}}
            @if ($paymentMethod === 'dinheiro')
                <div class="mt-3 space-y-2 rounded-xl border border-gray-200 bg-gray-50 p-3 transition-all">
                    <label class="text-description text-xs font-bold uppercase tracking-wider">
                        Valor Recebido do Cliente
                    </label>
                    <div class="relative w-full">
                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-500">R$</span>
                        <input type="text" wire:model.live.debounce.300ms="receivedValue" placeholder="Ex: 50,00"
                            class="text-description border-border ring-offset-background focus-visible:ring-primary w-full rounded-lg border bg-white py-2 pl-9 pr-3 text-sm font-bold focus-visible:outline-none focus-visible:ring-2">
                    </div>

                    @error('receivedValue')
                        <span class="block text-xs font-bold text-red-500">{{ $message }}</span>
                    @enderror

                    {{-- Exibição do Troco em Tempo Real --}}
                    @if ($receivedValue)
                        <div class="mt-2 flex items-center justify-between border-t border-gray-200 pt-2 text-sm">
                            <span class="font-bold text-gray-500">Troco a devolver:</span>
                            <span
                                class="{{ $this->change > 0 ? 'text-green-600' : 'text-gray-400' }} text-md font-extrabold">
                                R$ {{ number_format($this->change, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="bg-primary/5 flex items-center justify-between rounded-xl p-3">
            <span class="text-sm font-bold">
                Total
            </span>
            <span class="text-primary text-2xl font-extrabold">
                R$ {{ number_format($this->cartTotal, 2, ',', '.') }}
            </span>
        </div>
        <button type="button" wire:click="checkout" wire:loading.attr="disabled" {{ empty($cart) ? 'disabled' : '' }}
            class="ring-offset-background focus-visible:ring-ring bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-14 w-full cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 py-2 font-extrabold text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
            <i class="bi bi-check-circle" wire:loading.remove wire:target="checkout"></i>
            <span wire:loading.remove wire:target="checkout">Finalizar Venda</span>

            <i class="bi bi-arrow-repeat inline-block animate-spin" wire:loading wire:target="checkout"
                style="display: none;"></i>
            <span wire:loading wire:target="checkout" style="display: none;">Finalizando...</span>
        </button>
    </div>
</div>
