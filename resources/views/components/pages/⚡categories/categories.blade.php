<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Categorias" descricao="Organize seus produtos em categorias" />
        <button x-on:click="$dispatch('open-category-modal')"
            class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
            <span class="text-white">
                + Nova Categoria
            </span>
        </button>
    </div>
    @error('delete')
        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
    @enderror

    {{-- Cards Informativos --}}
    @if ($totalCategories > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-primary rounded-xl shadow-lg">
            <div class="rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-400/10 text-green-400">
                    <i class="bi bi-grid text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-white uppercase tracking-wide">Total de Categorias</p>
                    <p class="text-xl font-extrabold text-white">{{ $totalCategories }}</p>
                </div>
            </div>
            <div class="rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-yellow-400/10 text-yellow-400">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-white uppercase tracking-wide">Total de Produtos</p>
                    <p class="text-xl font-extrabold text-white">{{ $totalProducts }}</p>
                </div>
            </div>
            <div class="rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-400/10 text-white">
                    <i class="bi bi-tags text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-white uppercase tracking-wide">Categoria com mais
                        Produtos
                    </p>
                    <p class="text-xl font-extrabold text-white">{{ $topCategory }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Cards das categorias --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Card Fixo: Sem Categoria --}}
        @if ($uncategorizedProducts > 0 && $totalCategories > 0)
            <div class="bg-gray-50 border border-gray-200 rounded-xl shadow-sm p-5 flex items-center gap-4 w-full">
                <div class="w-12 h-12 rounded-xl bg-gray-200 flex items-center justify-center">
                    <i class="bi bi-bookmark-x text-gray-500 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-700">Sem Categoria</h3>
                    <p class="text-xs text-description flex items-center gap-1">
                        <i class="bi bi-box-seam"></i>
                        {{ $uncategorizedProducts }}
                        {{ $uncategorizedProducts === 1 ? 'produto' : 'produtos' }}
                    </p>
                </div>
            </div>
        @endif

        @forelse ($this->categories as $category)
            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 w-full">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i class="bi bi-tags text-primary text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold">{{ $category->name }}</h3>
                    <p class="text-xs text-description flex items-center gap-1">
                        <i class="bi bi-box-seam"></i>
                        {{ $category->products_count ?? 0 }}
                        {{ $category->products_count ?? 0 === 1 ? 'produto' : 'produtos' }}
                    </p>
                </div>
                <div class="flex">
                    <button x-on:click="$dispatch('open-category-modal', { id: {{ $category->id }} })"
                        class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-primary/20 hover:text-purple-950 h-10 w-10 rounded-lg hover:cursor-pointer">
                        <i class="bi bi-pen text-md"></i>
                    </button>
                    <button wire:click="delete({{ $category->id }})"
                        wire:confirm.prompt="Você tem certeza? Os produtos ficaram sem categoria\n\nDigite DELETAR para confirmar|DELETAR"
                        class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50  hover:bg-primary/20 h-10 w-10 rounded-lg text-red-500 hover:text-red-700 cursor-pointer">
                        <i class="bi bi-trash3 text-md"></i>
                    </button>
                </div>
            </div>
        @empty
            <div
                class="flex flex-col text-description justify-center items-center w-full col-span-full space-y-2 mt-10">
                <i class="bi bi-folder2-open text-5xl"></i>
                <p class="text-3xl">
                    Nenhuma Categoria Registrada
                </p>
                @if ($uncategorizedProducts > 0)
                    <span>
                        Você tem {{ $uncategorizedProducts }} produtos sem categoria
                    </span>
                @endif
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $this->categories->links() }}
    </div>
    <livewire:modals.categories />
</div>
