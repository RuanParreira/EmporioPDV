<div class="flex h-screen overflow-hidden">
    <div class="flex flex-1 flex-col overflow-hidden p-4" x-on:keydown.window.f1.prevent="$refs.idInput.focus()">
        {{-- Titulo --}}
        <h1 class="mb-3 text-xl font-extrabold">
            Nova Venda
        </h1>
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <div class="w-18">
                <input type="text" x-ref="idInput" wire:model="searchId" wire:keydown.enter="addSearchedProduct"
                    class="input-search pl-2 text-center" placeholder="#CODE">
            </div>
            <div class="relative w-64">
                <i class="bi bi-search icon-input-search"></i>
                <input type="text" type="text" wire:model.live.debounce.200ms="search" class="input-search"
                    placeholder="Buscar produto...">
            </div>
            @if ($this->categories->isNotEmpty())
                <button type="button" wire:click="$set('filter', null)"
                    class="{{ $filter === null ? 'bg-primary text-white border-primary' : 'bg-white text-description border-transparent hover:bg-primary/20 hover:text-description' }} cursor-pointer rounded-xl border-2 px-4 py-2 text-sm font-bold shadow-sm transition-all">
                    <span class="capitalize">Todos</span>
                </button>
            @endif

            @foreach ($this->categories as $category)
                <button type="button" wire:click="$set('filter', {{ $category->id }})"
                    class="{{ $filter === $category->id ? 'bg-primary text-white border-primary' : 'bg-white text-description border-transparent hover:bg-primary/20 hover:text-description' }} cursor-pointer rounded-xl border-2 px-4 py-2 text-sm font-bold shadow-sm transition-all">
                    <span class="capitalize">{{ $category->name }}</span>
                </button>
            @endforeach
        </div>

        {{-- Produtos --}}
        <div class="grid flex-1 grid-cols-2 content-start gap-3 overflow-y-auto pb-4 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($this->products as $product)
                <button type="button"
                    @if ($product->measure_unit === 'KG') x-on:click="$dispatch('open-weight-modal', { id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $product->price }} })"
                    @else
                    x-on:click="$dispatch('add-to-cart', { productId: {{ $product->id }} })" @endif
                    class="card-shadow hover:border-primary/30 bg-primary/10 text-primary cursor-pointer rounded-xl border-2 border-transparent p-4 text-left transition-all">
                    <span class="text-[10px] font-bold uppercase tracking-wider opacity-60">
                        {{ $product->category->name ?? 'Sem Categoria' }}
                    </span>
                    <h3 class="mt-1 text-sm font-bold">
                        {{ $product->name }}
                    </h3>
                    <div class="flex items-center justify-between">
                        <p class="mt-2 text-lg font-extrabold">
                            R${{ number_format($product->price, 2, ',', '.') }}
                            @if ($product->measure_unit === 'KG')
                                <span class="text-sm font-bold">
                                    /kg
                                </span>
                            @endif
                        </p>
                        @if ($product->measure_unit === 'UN')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-shopping-basket-icon lucide-shopping-basket">
                                <path d="m15 11-1 9" />
                                <path d="m19 11-4-7" />
                                <path d="M2 11h20" />
                                <path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4" />
                                <path d="M4.5 15.5h15" />
                                <path d="m5 11 4-7" />
                                <path d="m9 11 1 9" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-scale-icon lucide-scale inline-flex">
                                <path d="M12 3v18" />
                                <path d="m19 8 3 8a5 5 0 0 1-6 0zV7" />
                                <path d="M3 7h1a17 17 0 0 0 8-2 17 17 0 0 0 8 2h1" />
                                <path d="m5 8 3 8a5 5 0 0 1-6 0zV7" />
                                <path d="M7 21h10" />
                            </svg>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Paginate --}}
        <div class="mt-4">
            {{ $this->products->links() }}
        </div>
    </div>
    {{-- Carrinho --}}
    <livewire:cart wire:key="pos-cart-sidebar" />
    <livewire:modals.cart />

</div>
