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

        // 2. Inicia a Transação no Banco de Dados
        try {
            DB::beginTransaction();

            // 3. Cria o registro mestre da Venda (Tabela 'sales')
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'total_value' => $this->cartTotal(),
                'payment_method' => $this->paymentMethod,
                'received_value' => $receivedFloat,
            ]);

            // 4. Salva os Itens da Venda (Tabela 'sale_items')
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['observation'] ?? null,
                ]);
            }

            // 5. Confirma a transação (Salva tudo definitivamente)
            DB::commit();

            // 6. Limpa o carrinho e reseta o pagamento
            $this->reset(['cart', 'paymentMethod', 'receivedValue']);

            // 7. Dispara um evento de sucesso para mostrar um alerta na tela
            $this->dispatch('notify', title: 'Venda Concluída!', type: 'success', message: 'A venda foi salva no sistema e o carrinho limpo.');
        } catch (\Exception $e) {
            // Se algo der errado, desfaz tudo e avisa o erro
            DB::rollBack();
            Log::error('Erro ao finalizar venda: ' . $e->getMessage());
            $this->addError('checkout', 'Ocorreu um erro ao finalizar a venda. Tente novamente.');
        }
    }
};
?>

<div class="w-96 bg-white border-l border-slate-100 flex flex-col shadow-sm">
    <div class="p-4 border-b border-slate-200 flex items-center gap-2">
        <i class="bi bi-cart text-primary text-lg"></i>
        <h2 class="text-lg font-bold">
            Carrinho
        </h2>
        <span class="ml-auto bg-primary text-white text-xs font-bold px-2 py-1 rounded-full">
            {{ count($cart) }}
        </span>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        {{-- Item do Carrinho --}}
        @forelse ($cart as $id => $item)
            <div wire:key="cart-item-{{ $id }}" class="bg-input rounded-xl p-3 space-y-2">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold">{{ $item['name'] }}</h4>
                        <p class="text-xs text-description">
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
                        class="cursor-pointer text-red-600 hover:bg-red-600/10 rounded-full w-6 h-6 transition-colors items-center justify-center inline-flex">
                        <i class="bi bi-trash3 text-sm"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2">

                    {{-- Verifica se o produto é vendido por peso --}}
                    @if ($item['is_weight'] ?? false)
                        {{-- Visual para Produtos Pesados (Apenas exibe o peso em Kg) --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-description flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-weight-icon lucide-weight w-3 h-3 text-description">
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
                            class="cursor-pointer text-description w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center hover:bg-primary/20 transition-colors">
                            -
                        </button>

                        <span class="text-sm font-bold w-8 text-center">
                            {{ $item['quantity'] }}
                        </span>

                        <button type="button" wire:click="incrementQuantity({{ $id }})"
                            class="cursor-pointer text-description w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center hover:bg-primary/20 transition-colors">
                            +
                        </button>
                    @endif

                    {{-- O Valor Total do Item continua no canto direito --}}
                    <span class="ml-auto text-sm font-extrabold text-foreground">
                        R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                    </span>
                </div>

                {{-- Observação interligada diretamente ao item do array --}}
                <div class="flex items-center gap-2">
                    <i class="bi bi-chat text-description text-xs"></i>
                    <input type="text" wire:model.blur="cart.{{ $id }}.observation"
                        placeholder="Observação (ex: sem cebola)..."
                        class="w-full text-xs bg-transparent border-b border-border/50 focus:border-primary outline-none py-1">
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400 gap-2 opacity-70">
                <i class="bi bi-cart-x text-4xl"></i>
                <p class="text-sm font-semibold">O carrinho está vazio</p>
            </div>
        @endforelse
    </div>

    {{-- Rodapé do Carrinho --}}
    <div class="border-t border-slate-200 p-4 space-y-3">
        {{-- Exibe mensagens de erro caso existam --}}
        @error('cart')
            <span class="text-red-500 text-xs font-bold block">{{ $message }}</span>
        @enderror
        @error('paymentMethod')
            <span class="text-red-500 text-xs font-bold block">{{ $message }}</span>
        @enderror
        @error('checkout')
            <span class="text-red-500 text-xs font-bold block">{{ $message }}</span>
        @enderror

        <div>
            <p class="text-xs font-bold text-description uppercase tracking-wider mb-2">
                pagamento
            </p>
            <div class="grid grid-cols-3 gap-2">
                {{-- Botão Cartão --}}
                <button type="button" wire:click="$set('paymentMethod', 'cartao')"
                    class="cursor-pointer flex flex-col items-center py-2 rounded-xl text-xs font-bold transition-all border-2
                    {{ $paymentMethod === 'cartao' ? 'bg-primary text-white ' : 'bg-input text-description border-transparent hover:bg-primary/20' }}">
                    <i class="bi bi-credit-card text-lg"></i>
                    Cartão
                </button>
                {{-- Botão Pix --}}
                <button type="button" wire:click="$set('paymentMethod', 'pix')"
                    class="cursor-pointer flex flex-col items-center py-1 rounded-xl text-xs font-bold transition-all border-2
                    {{ $paymentMethod === 'pix' ? 'bg-primary text-white' : 'bg-input text-description border-transparent hover:bg-primary/20' }}">
                    <i class="bi bi-phone text-lg"></i>
                    Pix
                </button>
                {{-- Botão Dinheiro --}}
                <button type="button" wire:click="$set('paymentMethod', 'dinheiro')"
                    class="cursor-pointer flex flex-col items-center py-2 rounded-xl text-xs font-bold transition-all border-2
                    {{ $paymentMethod === 'dinheiro' ? 'bg-primary text-white' : 'bg-input text-description border-transparent hover:bg-primary/20' }}">
                    <i class="bi bi-cash text-lg"></i>
                    Dinheiro
                </button>
            </div>
            {{-- Calcular troco se for dinheiro --}}
            @if ($paymentMethod === 'dinheiro')
                <div class="mt-3 bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-2 transition-all">
                    <label class="text-xs font-bold text-description uppercase tracking-wider">
                        Valor Recebido do Cliente
                    </label>
                    <div class="relative w-full">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-sm pointer-events-none">R$</span>
                        <input type="text" wire:model.live.debounce.300ms="receivedValue" placeholder="Ex: 50,00"
                            class="w-full font-bold text-description border border-border bg-white pl-9 pr-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-lg">
                    </div>

                    @error('receivedValue')
                        <span class="text-red-500 text-xs font-bold block">{{ $message }}</span>
                    @enderror

                    {{-- Exibição do Troco em Tempo Real --}}
                    @if ($receivedValue)
                        <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-200 mt-2">
                            <span class="font-bold text-gray-500">Troco a devolver:</span>
                            <span
                                class="font-extrabold {{ $this->change > 0 ? 'text-green-600' : 'text-gray-400' }} text-md">
                                R$ {{ number_format($this->change, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="bg-primary/5 rounded-xl p-3 flex items-center justify-between">
            <span class="text-sm font-bold">
                Total
            </span>
            <span class="text-2xl font-extrabold text-primary">
                R$ {{ number_format($this->cartTotal, 2, ',', '.') }}
            </span>
        </div>
        <button type="button" wire:click="checkout" wire:loading.attr="disabled" {{ empty($cart) ? 'disabled' : '' }}
            class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 w-full h-14 rounded-xl text-white font-extrabold gap-2">
            <i class="bi bi-check-circle"></i>
            Finalizar Venda
        </button>
    </div>
</div>
