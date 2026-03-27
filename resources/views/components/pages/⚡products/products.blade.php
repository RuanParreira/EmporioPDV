<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Produtos" descricao="Gerencie os produtos do sistema" />
        <div class="flex items-center gap-4">
            <div class="relative max-w-md">
                <i class="bi bi-search icon-input-search"></i>
                <input type="text" type="text" wire:model.live.debounce.200ms="search" class="input-search"
                    placeholder="Buscar produto...">
            </div>

            @can('create', \App\Models\Product::class)
                <button type="button" wire:click="$dispatch('open-product-modal')" class="button-new">
                    <span>
                        + Novo Produto
                    </span>
                </button>
            @endcan
        </div>
    </div>

    <div class="table-default">
        <table>
            <thead>
                <tr>
                    <th>
                        CODE
                    </th>
                    <th>
                        Nome
                    </th>
                    <th>
                        Categoria
                    </th>
                    <th>
                        Tipo
                    </th>
                    <th class="text-right">
                        Preço
                    </th>
                    @can('viewAny', \App\Models\Product::class)
                        <th class="text-right">
                            Ações
                        </th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse ($this->products as $product)
                    <tr wire:key="product-{{ $product->id }}"
                        class="border-border/50 hover:bg-description/10 {{ $product->is_active ? 'hover:bg-description/10' : 'bg-gray-50 opacity-60 hover:opacity-100 grayscale-[0.3]' }} border-b transition-colors">
                        <td class="text-description">
                            #{{ $product->code ? str_pad($product->code, 3, '0', STR_PAD_LEFT) : 'N/F' }}
                        </td>
                        <td class="font-semibold">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-seam text-purple-800"></i>
                                <span class="capitalize">
                                    {{ $product->name }}
                                </span>

                                @if (!$product->is_active)
                                    <span
                                        class="ml-2 rounded-full border border-red-200 bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-red-600 shadow-sm">
                                        Desativado
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="bg-primary/10 text-primary rounded-lg px-2 py-1 text-xs font-bold capitalize">
                                {{ $product->category?->name ?? 'Sem categoria' }}
                            </span>
                        </td>
                        <td class="text-description text-sm uppercase">
                            {{ $product->measure_unit }}
                        </td>
                        <td class="text-right font-bold">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </td>
                        @can('viewAny', $product)
                            <td class="text-right">
                                <button type="button"
                                    wire:click="$dispatch('open-product-modal', { id: {{ $product->id }} })"
                                    class="edit-button">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                                <button type="button" wire:click="delete({{ $product->id }})"
                                    wire:confirm="Você tem certeza que deseja deleter esse produto?" class="delete-button">
                                    <i class="bi bi-trash3 text-md"></i>
                                </button>
                                <button type="button"
                                    title="{{ $product->is_active ? 'Desativar Produto' : 'Ativar Produto' }}"
                                    wire:click="toggleActive({{ $product->id }})"
                                    class="active-button {{ $product->is_active ? 'text-blue-500 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-400 hover:bg-blue-100 hover:text-blue-600' }}">
                                    <i
                                        class="{{ $product->is_active ? 'bi bi-toggle-on' : 'bi bi-toggle-off' }} text-xl"></i>
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
