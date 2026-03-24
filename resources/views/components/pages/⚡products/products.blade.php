<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Produtos" descricao="Gerencie os produtos do sistema" />
        <div class="flex gap-4">
            <div class="relative max-w-md">
                <i class="bi bi-search text-description text-md absolute left-3 top-1/3 -translate-y-1/3"></i>
                <input type="text" type="text" wire:model.live.debounce.200ms="search"
                    class="border-border bg-input ring-offset-background focus-visible:ring-primary flex h-10 w-full rounded-lg border px-3 py-2 pl-10 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                    placeholder="Buscar produto...">
            </div>

            @can('create', \App\Models\Product::class)
                <button type="button" wire:click="$dispatch('open-product-modal')"
                    class="bg-primary hover:bg-primary/90 h-10 cursor-pointer rounded-lg px-4">
                    <span class="text-white">
                        + Novo Produto
                    </span>
                </button>
            @endcan
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-md">
        <table class="w-full">
            <thead>
                <tr class="border-border bg-primary border-b">
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        CODE
                    </th>
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Nome
                    </th>
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Categoria
                    </th>
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Tipo
                    </th>
                    <th class="p-4 text-right text-xs font-bold uppercase tracking-wider text-white">
                        Preço
                    </th>
                    @can('viewAny', \App\Models\Product::class)
                        <th class="p-4 text-right text-xs font-bold uppercase tracking-wider text-white">
                            Ações
                        </th>
                    @endcan
                </tr>
            </thead>
            <tbody>

                @forelse ($this->products as $product)
                    <tr wire:key="product-{{ $product->id }}"
                        class="border-border/50 hover:bg-description/10 {{ $product->active ? 'hover:bg-description/10' : 'bg-gray-50 opacity-60 hover:opacity-100 grayscale-[0.3]' }} border-b transition-colors">
                        <td class="text-description p-4 align-middle">
                            #{{ $product->code ? str_pad($product->code, 3, '0', STR_PAD_LEFT) : 'N/F' }}
                        </td>
                        <td class="p-4 align-middle font-semibold">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-seam text-purple-800"></i>
                                <span class="capitalize">
                                    {{ $product->name }}
                                </span>

                                @if (!$product->active)
                                    <span
                                        class="ml-2 rounded-full border border-red-200 bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-red-600 shadow-sm">
                                        Desativado
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 align-middle">
                            <span class="bg-primary/10 text-primary rounded-lg px-2 py-1 text-xs font-bold capitalize">
                                {{ $product->category?->name ?? 'Sem categoria' }}
                            </span>
                        </td>
                        <td class="text-description p-4 align-middle text-sm uppercase">
                            {{ $product->measure_unit }}
                        </td>
                        <td class="p-4 text-right align-middle font-bold">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </td>
                        @can('viewAny', $product)
                            <td class="p-4 text-right align-middle">
                                <button type="button"
                                    wire:click="$dispatch('open-product-modal', { id: {{ $product->id }} })"
                                    class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium transition-colors hover:cursor-pointer hover:text-purple-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                                <button type="button" wire:click="delete({{ $product->id }})"
                                    wire:confirm="Você tem certeza que deseja deleter esse produto?"
                                    class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium text-red-500 transition-colors hover:text-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                                    <i class="bi bi-trash3 text-md"></i>
                                </button>
                                <button type="button"
                                    title="{{ $product->active ? 'Desativar Produto' : 'Ativar Produto' }}"
                                    wire:click="toggleActive({{ $product->id }})"
                                    class="ring-offset-background focus-visible:ring-ring {{ $product->active ? 'text-blue-500 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-400 hover:bg-blue-100 hover:text-blue-600' }} inline-flex h-10 w-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                                    <i class="{{ $product->active ? 'bi bi-toggle-on' : 'bi bi-toggle-off' }} text-xl"></i>
                                </button>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr wire:key="no-products-found">
                        <td colspan="6" class="p-10 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="bi bi-search text-description/40 text-4xl"></i>
                                <p class="text-description font-medium">
                                    Nenhum produto encontrado para "{{ $search }}"
                                </p>
                                <button type="button" wire:click="$set('search', '')"
                                    class="text-primary cursor-pointer text-sm hover:underline">
                                    Limpar busca
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->products->links() }}
    </div>

    <livewire:modals.products />
</div>
