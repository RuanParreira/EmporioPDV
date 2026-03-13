<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Categorias" descricao="Organize seus produtos em categorias" />
        <button class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
            <span class="text-white">
                + Nova Categoria
            </span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-md p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-purple-500/10 text-purple-500">
                <i class="bi bi-grid-3x3-gap text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-description uppercase tracking-wide">Total de Categorias</p>
                <p class="text-xl font-extrabold text-foreground">{{ $totalCategories }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-500/10 text-blue-500">
                <i class="bi bi bi-clipboard-check text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-description uppercase tracking-wide">Total de Produtos</p>
                <p class="text-xl font-extrabold text-foreground">{{ $totalProducts }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-500/10 text-green-500">
                <i class="bi bi-bag-heart text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-description uppercase tracking-wide">Categoria com mais Produtos
                </p>
                <p class="text-xl font-extrabold text-foreground">{{ $topCategory }}</p>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($categories as $category)
            @php($isDefaultCategory = $category->name === 'SemCategoria')

            <div class="bg-white rounded-xl shadow-md  p-5 flex items-center gap-4 w-full">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i class="bi bi-tags text-primary text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold">{{ $category->name }}</h3>
                    <p class="text-xs text-description flex items-center gap-1">
                        <i class="bi bi-box-seam"></i>
                        {{ $category->products_count }}
                        {{ $category->products_count === 1 ? 'produto' : 'produtos' }}
                    </p>
                </div>
                @if (!$isDefaultCategory)
                    <div class="flex">
                        @can('update', $category)
                            <button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-primary/20 hover:text-purple-950 h-10 w-10 rounded-lg hover:cursor-pointer">
                                <i class="bi bi-pen text-md"></i>
                            </button>
                        @endcan

                        @can('delete', $category)
                            <button wire:click="delete({{ $category->id }})"
                                wire:confirm.prompt="Você tem certeza? Os produtos ficaram sem categoria\n\nDigite DELETAR para confirmar|DELETAR"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50  hover:bg-primary/20 h-10 w-10 rounded-lg text-red-500 hover:text-red-700 cursor-pointer">
                                <i class="bi bi-trash3 text-md"></i>
                            </button>
                        @endcan
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
