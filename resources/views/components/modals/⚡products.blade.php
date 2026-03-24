<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

new class extends Component {
    public bool $showModal = false;

    public ?int $productId = null;
    public ?int $category_id = null;

    public string $name = '';
    public ?string $code = null;
    public string $price = '';
    public string $measure_unit = 'UN';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3', Rule::unique('products', 'name')->ignore($this->productId)],
            'code' => ['nullable', 'integer', 'max_digits:3', Rule::unique('products', 'code')->ignore($this->productId)],
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'measure_unit' => 'required|in:UN,KG',
        ];
    }

    public function messages(): array
    {
        return [
            // Mensagens para o Nome
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.unique' => 'Já existe um produto com este nome cadastrado.',

            // Mensagens do code
            'code.integer' => 'Apenas numeros.',
            'code.max_digits' => 'Maximo 3 digitos.',
            'code.unique' => 'Já existe.',

            // Mensagens para o Preço
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico válido.',
            'price.min' => 'O preço não pode ser menor que zero.',

            // Mensagens para a Categoria
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada é inválida ou não existe.',

            // Mensagens para a Unidade de Medida
            'measure_unit.required' => 'O tipo de venda é obrigatório.',
            'measure_unit.in' => 'Selecione um tipo de venda válido (Unidade ou Peso).',
        ];
    }

    #[On('open-product-modal')]
    public function openModal(?int $id = null): void
    {
        $this->reset();
        $this->measure_unit = 'UN';
        $this->resetValidation();

        if ($id) {
            $product = Product::findOrFail($id);
            Gate::authorize('update', $product);

            $this->productId = $product->id;
            $this->name = $product->name;
            $this->code = $product->code;
            $this->category_id = $product->category_id;
            $this->price = $product->price;
            $this->measure_unit = $product->measure_unit;
        } else {
            Gate::authorize('create', Product::class);
        }

        $this->showModal = true;
    }

    public function with(): array
    {
        return [
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            Gate::authorize('update', $product);

            $product->update([
                'name' => $this->name,
                'code' => $this->code ? (int) $this->code : null,
                'price' => (float) $this->price,
                'category_id' => $this->category_id,
                'measure_unit' => $this->measure_unit,
            ]);

            $this->dispatch('notify', title: 'Sucesso!', message: 'Produto atualizado com sucesso!', type: 'success');
        } else {
            Gate::authorize('create', Product::class);

            Product::create([
                'name' => $this->name,
                'code' => $this->code,
                'price' => (float) $this->price,
                'category_id' => $this->category_id,
                'measure_unit' => $this->measure_unit,
                'active' => true,
            ]);

            $this->dispatch('notify', title: 'Sucesso!', message: 'Produto criado com sucesso!', type: 'success');
        }

        $this->showModal = false;
        $this->dispatch('product-saved');
    }
};
?>

{{-- Modal --}}
<div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;">
    {{-- Overlay (Fundo Escuro) --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
        class="absolute inset-0 bg-black/80"></div>

    {{-- Cartão do Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative z-10 w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">

        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="bi {{ $productId ? 'bi-pencil-square' : 'bi bi-basket3' }} text-primary"></i>
                {{ $productId ? 'Editar Produto' : 'Novo Produto' }}
            </h3>
            <button type="button" @click="open = false"
                class="cursor-pointer text-gray-400 transition-colors hover:text-gray-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit.prevent="save" autocomplete="off">
            <div class="space-y-5">

                {{-- Campo Nome --}}
                <div class="flex gap-4">
                    <div class="w-full">
                        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nome do
                            Produto</label>
                        {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                        <input id="name" type="text" wire:model="name"
                            class="bg-input ring-offset-background focus-visible:ring-primary border-border flex h-11 w-full rounded-lg border px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            placeholder="Digite o nome do produto">
                        @error('name')
                            <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Campo Codigo --}}
                    <div class="w-40">
                        <label for="code" class="mb-1 block text-sm font-medium text-gray-700">Code do
                            Produto</label>
                        <input id="code" type="text" wire:model.number="code"
                            class="bg-input ring-offset-background focus-visible:ring-primary border-border flex h-11 w-full rounded-lg border px-3 py-2 text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 md:text-sm"
                            placeholder="Ex: 152">
                        @error('code')
                            <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{-- Campo Preço --}}
                <div>
                    <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Preço (R$)</label>
                    {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                    <input id="price" type="text" wire:model="price"
                        class="bg-input ring-offset-background focus-visible:ring-primary border-border flex h-11 w-full rounded-lg border px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        placeholder="Digite o preço">
                    @error('price')
                        <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Categoria (Dropdown com Busca e Navegação por Teclado) --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: @entangle('category_id'),
                    categories: [
                        { id: null, name: 'Sem Categoria' },
                        ...{{ Js::from($categories) }}
                    ],
                    highlightedIndex: 0,
                
                    get filteredCategories() {
                        if (this.search === '') return this.categories;
                        return this.categories.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                    },
                
                    get selectedName() {
                        let cat = this.categories.find(c => c.id == this.selectedId);
                        return cat ? cat.name : 'Sem Categoria';
                    },
                
                    selectCategory(category) {
                        if (category) {
                            this.selectedId = category.id;
                            this.open = false;
                            this.search = '';
                            this.$nextTick(() => this.$refs.dropdownButton.focus())
                        }
                    },
                
                    init() {
                        // Zera o highlight sempre que a busca mudar
                        this.$watch('search', () => { this.highlightedIndex = 0; });
                    }
                }" class="relative w-full">

                    <label for="category_id" class="mb-1 block text-sm font-medium text-gray-700">Categoria</label>

                    {{-- Botão Principal --}}
                    <button x-ref="dropdownButton" type="button"
                        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus());"
                        @click.away="open = false"
                        @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.searchInput.focus());"
                        class="bg-input border-border focus:ring-primary flex h-11 w-full items-center justify-between rounded-lg border px-3 py-2 transition-all focus:outline-none focus:ring-2 md:text-sm"
                        :class="{ 'text-gray-900': selectedId, 'text-gray-400': !selectedId }">
                        <span x-text="selectedName" class="truncate"></span>
                        <i class="bi bi-chevron-down text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    {{-- Painel do Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
                        style="display: none;">
                        {{-- Input da Busca com Eventos de Teclado --}}
                        <div class="border-b border-gray-100 bg-gray-50/50 p-2">
                            <div class="relative">
                                <i
                                    class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                                <input x-ref="searchInput" type="text" x-model="search"
                                    placeholder="Buscar categoria..."
                                    class="focus:border-primary focus:ring-primary w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-1"
                                    @keydown.arrow-down.prevent="highlightedIndex = Math.min(highlightedIndex + 1, filteredCategories.length - 1)"
                                    @keydown.arrow-up.prevent="highlightedIndex = Math.max(highlightedIndex - 1, 0)"
                                    @keydown.enter.prevent="selectCategory(filteredCategories[highlightedIndex])"
                                    @keydown.escape.prevent="open = false; $nextTick(() => $refs.dropdownButton.focus());">
                            </div>
                        </div>

                        {{-- Lista de Categorias --}}
                        <ul class="max-h-48 space-y-0.5 overflow-y-auto p-1" id="category-list">
                            <template x-for="(category, index) in filteredCategories" :key="category.id">
                                <li @click="selectCategory(category)" @mouseenter="highlightedIndex = index"
                                    class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors"
                                    :class="{
                                        'bg-primary/10 text-primary font-semibold': selectedId == category.id,
                                        'bg-gray-100 text-gray-900': highlightedIndex === index && selectedId !=
                                            category.id,
                                        'text-gray-700': highlightedIndex !== index && selectedId != category.id
                                    }">
                                    <span x-text="category.name"></span>
                                    <i x-show="selectedId == category.id" class="bi bi-check2 text-lg"></i>
                                </li>
                            </template>

                            <li x-show="filteredCategories.length === 0"
                                class="px-3 py-4 text-center text-sm text-gray-500">
                                Nenhuma categoria encontrada.
                            </li>
                        </ul>
                    </div>

                    @error('category_id')
                        <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Selecionar o tipo --}}
                <div>
                    <label class="text-description mb-2 block text-sm font-medium">Tipo de Venda</label>
                    <div class="flex gap-3">
                        {{-- Opção Unidade --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="measure_unit" value="UN" class="peer sr-only">
                            <div
                                class="hover:bg-primary/20 peer-checked:bg-primary hover:peer-checked:bg-primary/90 peer-checked:border-primary flex items-center justify-center gap-2 rounded-xl border border-transparent bg-gray-100 py-3 text-sm font-semibold text-gray-500 transition-all peer-checked:text-white">
                                <i class="bi bi-box-seam"></i>
                                Por Unidade
                            </div>
                        </label>

                        {{-- Opção Peso --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="measure_unit" value="KG" class="peer sr-only">
                            <div
                                class="hover:bg-primary/20 peer-checked:bg-primary hover:peer-checked:bg-primary/90 peer-checked:border-primary flex items-center justify-center gap-2 rounded-xl border border-transparent bg-gray-100 py-3 text-sm font-semibold text-gray-500 transition-all peer-checked:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-weight-icon lucide-weight">
                                    <circle cx="12" cy="5" r="3" />
                                    <path
                                        d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                                </svg>
                                Por Peso
                            </div>
                        </label>
                    </div>
                    @error('measure_unit')
                        <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            {{-- Botão de Submeter --}}
            <div class="mt-8">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="bg-primary hover:bg-primary/90 flex w-full cursor-pointer items-center justify-center rounded-xl px-4 py-3.5 text-sm font-bold text-white transition-transform active:scale-[0.98]">
                    <span wire:loading.remove wire:target="save">
                        {{ $productId ? 'Atualizar Produto' : 'Criar Produto' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
