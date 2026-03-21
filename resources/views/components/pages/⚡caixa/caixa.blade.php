<div class="flex h-screen overflow-hidden">
    <div class="flex-1 flex flex-col p-4 overflow-hidden" x-on:keydown.window.f1.prevent="$refs.idInput.focus()">
        {{-- Titulo --}}
        <h1 class="text-xl font-extrabold  mb-3">
            Nova Venda
        </h1>
        <div class="flex gap-3 mb-4 items-center flex-wrap">
            <div class="w-15">
                <input type="text" x-ref="idInput" wire:model="searchId" wire:keydown.enter="addSearchedProduct"
                    class="flex text-center w-full border border-border bg-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-xl h-11"
                    placeholder="#ID">
            </div>
            <div class="relative w-64">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-description text-md"></i>
                <input type="text" type="text" wire:model.live.debounce.200ms="search"
                    class="flex w-full border border-border bg-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10 rounded-xl h-11"
                    placeholder="Buscar produto...">
            </div>
            @if ($this->categories->isNotEmpty())
                <button type="button" wire:click="$set('filter', null)"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm border-2
                {{ $filter === null ? 'bg-primary text-white border-primary' : 'bg-white text-description border-transparent hover:bg-primary/20 hover:text-description' }}">
                    <span class="capitalize">Todos</span>
                </button>
            @endif

            @foreach ($this->categories as $category)
                <button type="button" wire:click="$set('filter', {{ $category->id }})"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm border-2
                {{ $filter === $category->id ? 'bg-primary text-white border-primary' : 'bg-white text-description border-transparent hover:bg-primary/20 hover:text-description' }}">
                    <span class="capitalize">{{ $category->name }}</span>
                </button>
            @endforeach
        </div>

        {{-- Produtos --}}
        <div class="flex-1 overflow-y-auto grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 content-start pb-4">
            @foreach ($this->products as $product)
                <button type="button"
                    @if ($product->measure_unit === 'KG') x-on:click="$dispatch('open-weight-modal', { id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $product->price }} })"
                    @else
                    x-on:click="$dispatch('add-to-cart', { productId: {{ $product->id }} })" @endif
                    class="cursor-pointer rounded-xl card-shadow p-4 text-left transition-all border-2 hover:border-primary/30 bg-primary/10 text-primary border-transparent">
                    <span class="text-[10px] font-bold uppercase tracking-wider opacity-60">
                        {{ $product->category->name ?? 'Sem Categoria' }}
                    </span>
                    <h3 class="text-sm font-bold mt-1">
                        {{ $product->name }}
                    </h3>
                    <div class="flex justify-between items-center">
                        <p class="text-lg font-extrabold mt-2">
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
