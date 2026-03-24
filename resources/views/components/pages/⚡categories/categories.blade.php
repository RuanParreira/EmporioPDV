<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Categorias" descricao="Organize seus produtos em categorias" />
        <button type="button" x-on:click="$dispatch('open-category-modal')"
            class="bg-primary hover:bg-primary/90 h-10 cursor-pointer rounded-lg px-4">
            <span class="text-white">
                + Nova Categoria
            </span>
        </button>
    </div>
    @error('delete')
        <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
    @enderror

    {{-- Cards Informativos --}}
    @if ($totalCategories > 0)
        <div class="bg-primary grid grid-cols-1 gap-4 rounded-xl shadow-lg md:grid-cols-3">
            <div class="flex items-center gap-4 rounded-xl p-5">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-400/10 text-green-400">
                    <i class="bi bi-grid text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-white">Total de Categorias</p>
                    <p class="text-xl font-extrabold text-white">{{ $totalCategories }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-xl p-5">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-400/10 text-yellow-400">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-white">Total de Produtos</p>
                    <p class="text-xl font-extrabold text-white">{{ $totalProducts }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-xl p-5">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-400/10 text-white">
                    <i class="bi bi-tags text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-white">Categoria com mais
                        Produtos
                    </p>
                    <p class="text-xl font-extrabold capitalize text-white">{{ $topCategory }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Cards das categorias --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Card Fixo: Sem Categoria --}}
        @if ($uncategorizedProducts > 0 && $totalCategories > 0)
            <div class="flex w-full items-center gap-4 rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-200">
                    <i class="bi bi-bookmark-x text-2xl text-gray-500"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-700">Sem Categoria</h3>
                    <p class="text-description flex items-center gap-1 text-xs">
                        <i class="bi bi-box-seam"></i>
                        {{ $uncategorizedProducts }}
                        {{ $uncategorizedProducts === 1 ? 'produto' : 'produtos' }}
                    </p>
                </div>
            </div>
        @endif

        @forelse ($this->categories as $category)
            <div class="flex w-full items-center gap-4 rounded-xl bg-white p-5 shadow-sm">
                <div class="bg-primary/10 flex h-12 w-12 items-center justify-center rounded-xl">
                    <i class="bi bi-tags text-primary text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold capitalize">{{ $category->name }}</h3>
                    <p class="text-description flex items-center gap-1 text-xs">
                        <i class="bi bi-box-seam"></i>
                        {{ $category->products_count ?? 0 }}
                        {{ $category->products_count ?? 0 === 1 ? 'produto' : 'produtos' }}
                    </p>
                </div>
                <div class="flex">
                    <button type="button" x-on:click="$dispatch('open-category-modal', { id: {{ $category->id }} })"
                        class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium transition-colors hover:cursor-pointer hover:text-purple-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                        <i class="bi bi-pen text-md"></i>
                    </button>
                    <button type="button" wire:click="delete({{ $category->id }})"
                        wire:confirm.prompt="Você tem certeza? Os produtos ficaram sem categoria\n\nDigite DELETAR para confirmar|DELETAR"
                        class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium text-red-500 transition-colors hover:text-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                        <i class="bi bi-trash3 text-md"></i>
                    </button>
                </div>
            </div>
        @empty
            <div
                class="text-description col-span-full mt-10 flex w-full flex-col items-center justify-center space-y-2">
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
