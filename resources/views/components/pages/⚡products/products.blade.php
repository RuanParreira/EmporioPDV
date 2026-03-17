<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Produtos" descricao="Gerencie os produtos do sistema" />
        @can('create', \App\Models\Product::class)
            <button wire:click="create" class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
                <span class="text-white">
                    + Novo Produto
                </span>
            </button>
        @endcan
    </div>

    <div class="relative max-w-md">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-description text-md"></i>
        <input type="text" type="text" wire:model.live.debounce.200ms="search"
            class="flex w-full border border-border bg-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10 rounded-xl h-11"
            placeholder="Buscar produto...">
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border bg-primary">
                    <th class="text-left p-4 text-xs font-bold text-white uppercase tracking-wider">
                        ID
                    </th>
                    <th class="text-left p-4 text-xs font-bold text-white uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="text-left p-4 text-xs font-bold text-white uppercase tracking-wider">
                        Categoria
                    </th>
                    <th class="text-left p-4 text-xs font-bold text-white uppercase tracking-wider">
                        Tipo
                    </th>
                    <th class="text-right p-4 text-xs font-bold text-white uppercase tracking-wider">
                        Preço
                    </th>
                    @can('viewAny', \App\Models\Product::class)
                        <th class="text-right p-4 text-xs font-bold text-white uppercase tracking-wider">
                            Ações
                        </th>
                    @endcan
                </tr>
            </thead>
            <tbody>

                @forelse ($this->products as $product)
                    <tr wire:key="product-{{ $product->id }}"
                        class="border-b border-border/50 hover:bg-description/10 transition-colors
                        {{ $product->active ? 'hover:bg-description/10' : 'bg-gray-50 opacity-60 hover:opacity-100 grayscale-[0.3]' }}">
                        <td class="p-4 text-description align-middle">
                            #{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="p-4 font-semibold align-middle">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-seam text-purple-800"></i>
                                <span>
                                    {{ $product->name }}
                                </span>

                                @if (!$product->active)
                                    <span
                                        class="bg-red-100 text-red-600 border border-red-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider ml-2 shadow-sm">
                                        Desativado
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 align-middle">
                            <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-lg">
                                {{ $product->category?->name ?? 'Sem categoria' }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-description capitalize align-middle">
                            {{ $product->measure_unit }}
                        </td>
                        <td class="p-4 text-right font-bold align-middle">
                            R$ {{ $product->price }}
                        </td>
                        @can('viewAny', $product)
                            <td class="p-4 text-right align-middle">
                                <button
                                    class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-primary/20 hover:text-purple-950 h-10 w-10 rounded-lg hover:cursor-pointer">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                                <button wire:click="delete({{ $product->id }})"
                                    wire:confirm="Você tem certeza que deseja deleter esse produto?"
                                    class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50  hover:bg-primary/20 h-10 w-10 rounded-lg text-red-500 hover:text-red-700 cursor-pointer">
                                    <i class="bi bi-trash3 text-md"></i>
                                </button>
                                <button title="{{ $product->active ? 'Desativar Produto' : 'Ativar Produto' }}"
                                    wire:click="toggleActive({{ $product->id }})"
                                    class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 w-10 rounded-lg cursor-pointer {{ $product->active ? 'text-blue-500 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-400 hover:bg-blue-100 hover:text-blue-600' }}">
                                    <i class="{{ $product->active ? 'bi bi-toggle-on' : 'bi bi-toggle-off' }} text-xl"></i>
                                </button>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr wire:key="no-products-found">
                        <td colspan="6" class="p-10 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="bi bi-search text-4xl text-description/40"></i>
                                <p class="text-description font-medium">
                                    Nenhum produto encontrado para "{{ $search }}"
                                </p>
                                <button wire:click="$set('search', '')"
                                    class="text-primary hover:underline text-sm cursor-pointer">
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
</div>
